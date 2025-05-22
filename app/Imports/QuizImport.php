<?php

namespace App\Imports;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Nécessaire pour l'importation réelle
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel; // Ajouté pour la validation
use Maatwebsite\Excel\Validators\ValidationException as ExcelValidationException; // Pour attraper les erreurs spécifiques d'Excel


class QuizImport implements ToCollection, WithHeadingRow // WithHeadingRow est utilisé par Excel::import
{
    protected $quiz;
    protected $isJson = false;

    public function __construct(Quiz $quiz, $isJson = false)
    {
        $this->quiz = $quiz;
        $this->isJson = $isJson;
    }

    /**
     * Méthode statique pour valider les données d'importation AVANT la création du quiz.
     *
     * @param \Illuminate\Http\UploadedFile $file Le fichier uploadé à valider
     * @param string|null $quizType Le type de quiz ('placement' ou 'final') pour les validations spécifiques
     * @return array Liste des erreurs de validation (vide si aucune erreur)
     */
    public static function validateImport($file, $quizType = null): array
    {
        $errors = [];

        try {
            $extension = strtolower($file->getClientOriginalExtension()); // Mettre en minuscule pour la comparaison
            $isJson = $extension === 'json';

            if ($isJson) {
                // --- Traitement Validation JSON ---
                $jsonContent = json_decode(file_get_contents($file->getPathname()), true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return ['Le fichier JSON est invalide ou mal formé: ' . json_last_error_msg()];
                }
                if (!is_array($jsonContent)) {
                     return ['Le contenu JSON principal doit être un tableau (Array) de questions.'];
                }


                // Vérifier si c'est un test de niveau et qu'il a 90 questions
                if ($quizType === 'placement' && count($jsonContent) !== 90) {
                    $errors[] = "Validation: Un test de niveau JSON doit contenir exactement 90 questions. Actuellement: " . count($jsonContent) . " questions.";
                }

                // Valider chaque question et réponse dans le JSON
                foreach ($jsonContent as $index => $item) {
                     if (!is_array($item)) {
                        $errors[] = "Validation: L'élément JSON à l'index " . $index . " n'est pas un objet/tableau valide.";
                        continue; // Passe à l'élément suivant
                    }

                    $validator = Validator::make($item, [
                        'question' => 'required|string',
                        'points' => 'required|numeric|min:1',
                        'answers' => 'required|array|min:2',
                        'answers.*.text' => 'required|string',
                        'answers.*.correct' => 'required|boolean',
                    ], self::getValidationMessages('json')); // Utiliser une méthode pour les messages

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error) {
                            $errors[] = "Validation JSON Question " . ($index + 1) . ": " . $error;
                        }
                        // On continue la validation des autres questions même si celle-ci a des erreurs
                        // Mais on ne vérifie pas la présence de réponse correcte si la structure de base est fausse
                        continue;
                    }

                    // Vérifier qu'au moins une réponse est correcte (après validation de base)
                    $hasCorrectAnswer = false;
                    if (isset($item['answers']) && is_array($item['answers'])) {
                        foreach ($item['answers'] as $answer) {
                            if (is_array($answer) && isset($answer['correct']) && $answer['correct'] === true) {
                                $hasCorrectAnswer = true;
                                break;
                            }
                        }
                    }

                    if (!$hasCorrectAnswer) {
                        $errors[] = "Validation JSON Question " . ($index + 1) . ": Aucune réponse correcte définie (la clé 'correct' doit être 'true' pour au moins une réponse).";
                    }
                }

            } else if (in_array($extension, ['csv', 'xlsx'])) {
                // --- Traitement Validation Excel/CSV (CORRIGÉ) ---

                // Utiliser une classe anonyme implémentant WithHeadingRow
                // pour lire les en-têtes correctement avec toCollection.
                 $collections = Excel::toCollection(new class implements WithHeadingRow {}, $file);


                // Vérifier si le fichier est vide ou si la première feuille est vide
                if ($collections->isEmpty() || $collections->first()->isEmpty()) {
                    return ['Validation: Le fichier CSV/XLSX est vide ou ne contient pas de données après la ligne d\'en-tête.'];
                }

                // Prendre la première feuille
                $rows = $collections->first(); // $rows est une Collection d'objets/tableaux associatifs

                // Vérifier si c'est un test de niveau et qu'il a 90 questions (lignes de données)
                if ($quizType === 'placement' && $rows->count() !== 90) {
                    $errors[] = "Validation: Un test de niveau CSV/XLSX doit contenir exactement 90 lignes de questions (hors en-tête). Actuellement: " . $rows->count() . " questions.";
                }

                // Valider chaque ligne (qui correspond à une question)
                foreach ($rows as $rowIndex => $row) {
                    $rowArray = $row->toArray(); // Convertit la ligne en tableau associatif
                    $currentLineNumber = $rowIndex + 2; // +1 pour index 0, +1 pour l'en-tête

                    // Validation des champs obligatoires
                    $validator = Validator::make($rowArray, [
                        'question' => 'required|string',
                        'points' => 'required|numeric|min:1',
                        'correct_answer' => 'required', // La validation du format se fait ensuite
                    ], self::getValidationMessages('csv')); // Utiliser une méthode pour les messages

                    if ($validator->fails()) {
                        foreach ($validator->errors()->all() as $error) {
                            $errors[] = "Validation Ligne " . $currentLineNumber . ": " . $error;
                        }
                        continue; // Passer à la ligne suivante si la validation de base échoue
                    }

                    // Compter les réponses non vides (answer_1, answer_2, ...)
                    $answerCount = 0;
                    $i = 1;
                    while (isset($rowArray["answer_" . $i]) && !empty(trim($rowArray["answer_" . $i]))) {
                       $answerCount++;
                       $i++;
                    }
                    // Si answer_N existe mais est vide, on arrête de compter.

                     // Vérifier qu'il y a au moins 2 réponses non vides
                    if ($answerCount < 2) {
                        $errors[] = "Validation Ligne " . $currentLineNumber . ": Doit avoir au moins 2 réponses non vides (colonnes answer_1, answer_2, ...). Actuellement: " . $answerCount . " réponses non vides trouvées.";
                        continue; // On ne peut pas valider correct_answer sans assez de réponses
                    }

                    // Détecter si answer_0 est utilisé (erreur courante)
                    if (isset($rowArray["answer_0"])) {
                        $errors[] = "Validation Ligne " . $currentLineNumber . ": Les indexes des réponses doivent commencer à 1 (utilisez answer_1, answer_2, ...). La colonne 'answer_0' ne doit pas être utilisée.";
                    }

                    try {
                        // Parser et valider les réponses correctes
                        $correctAnswers = self::flexibleParseCorrectAnswers($rowArray['correct_answer'], $answerCount);

                        // Vérifier si la fonction a retourné un tableau vide (ne devrait pas si format ok)
                        // Ou si l'exception n'a pas été levée mais aucune réponse valide n'est retournée.
                        if (empty($correctAnswers)) {
                            $errors[] = "Validation Ligne " . $currentLineNumber . ": Aucune réponse correcte valide n'a pu être déterminée pour 'correct_answer' = '" . $rowArray['correct_answer'] . "'. Vérifiez le format et les numéros par rapport aux réponses fournies (1 à $answerCount).";
                        }
                    } catch (\Exception $e) {
                        // Attraper l'exception de flexibleParseCorrectAnswers
                        $errors[] = "Validation Ligne " . $currentLineNumber . ": Erreur dans 'correct_answer' -> " . $e->getMessage();
                    }
                }
            } else {
                 // Si l'extension n'est ni json, ni csv, ni xlsx (devrait être attrapé par la validation du form aussi)
                 return ['Validation: Format de fichier non supporté. Utilisez CSV, XLSX ou JSON.'];
            }
           // Vérification de la somme des points pour les quiz de type "final"
            if ($quizType === 'final') {
                $totalPoints = 0;

                if ($isJson) {
                    // Pour les fichiers JSON, calculer la somme des points
                    foreach ($jsonContent as $item) {
                        if (isset($item['points']) && is_numeric($item['points'])) {
                            $totalPoints += (float)$item['points'];
                        }
                    }
                } else if (in_array($extension, ['csv', 'xlsx']) && isset($rows)) {
                    // Pour les fichiers CSV/XLSX, calculer la somme des points
                    foreach ($rows as $row) {
                        if (isset($row['points']) && is_numeric($row['points'])) {
                            $totalPoints += (float)$row['points'];
                        }
                    }
                }

                // Vérifier si la somme est égale à 20
                if ($totalPoints != 20) {
                    $errors[] = "La somme des points pour un quiz final doit être égale à 20. Somme actuelle: $totalPoints points.";
                }
            }

            return $errors; // Retourne le tableau des erreurs accumulées

        } catch (ExcelValidationException $e) {
            // Erreurs de validation internes levées par Maatwebsite/Excel pendant toCollection
            $failures = $e->failures();
            $errorMessages = ['Erreur de validation interne du fichier Excel/CSV:'];
            foreach ($failures as $failure) {
                 $errorMessages[] = "Ligne " . $failure->row() . ": " . implode(', ', $failure->errors()) . " (Attribut: " . $failure->attribute() . ")";
            }
             return $errorMessages;
        } catch (\Exception $e) {
            // Autres erreurs (lecture fichier, etc.)
            \Log::error("Erreur lors de la validation du fichier d'import quiz: " . $e->getMessage(), [
                'file' => $file->getClientOriginalName(),
                'trace' => $e->getTraceAsString() // Pour debug si besoin
            ]);
            return ['Erreur inattendue lors de la validation du fichier: ' . $e->getMessage() . '. Vérifiez les logs pour plus de détails.'];
        }
    }

     /**
     * Retourne les messages de validation personnalisés.
     * @param string $format 'csv' ou 'json'
     * @return array
     */
    protected static function getValidationMessages(string $format = 'csv'): array
    {
        if ($format === 'json') {
            return [
                'question.required' => 'Le champ question est obligatoire.',
                'question.string' => 'Le champ question doit être une chaîne.',
                'points.required' => 'Le champ points est obligatoire.',
                'points.numeric' => 'Le champ points doit être un nombre.',
                'points.min' => 'Le champ points doit être au moins 1.',
                'answers.required' => 'Les réponses sont obligatoires.',
                'answers.array' => 'Les réponses doivent être un tableau.',
                'answers.min' => 'Il doit y avoir au moins 2 réponses.',
                'answers.*.text.required' => 'Le texte de la réponse est obligatoire.',
                'answers.*.text.string' => 'Le texte de la réponse doit être une chaîne.',
                'answers.*.correct.required' => 'Le champ correct (true/false) est obligatoire.',
                'answers.*.correct.boolean' => 'Le champ correct doit être un booléen (true ou false).',
            ];
        } else { // CSV/XLSX
            return [
                'question.required' => 'La colonne question est obligatoire.',
                'question.string' => 'La colonne question doit être une chaîne.',
                'points.required' => 'La colonne points est obligatoire.',
                'points.numeric' => 'La colonne points doit être un nombre.',
                'points.min' => 'La colonne points doit être au moins 1.',
                'correct_answer.required' => 'La colonne correct_answer est obligatoire.',
                // La validation du format de correct_answer est faite par flexibleParseCorrectAnswers
            ];
        }
    }


    /**
     * Méthode appelée par Excel::import pour traiter les données après validation.
     */
    public function collection(Collection $rows)
    {
        // Cette vérification est redondante si validateImport a été appelée avant,
        // mais elle sert de sécurité si l'import est appelé directement ailleurs.
        if ($this->quiz->type === 'placement' && !$this->isJson && $rows->count() !== 90) {
             throw new \Exception("Importation: Un test de niveau CSV/XLSX doit contenir exactement 90 questions. Actuellement: " . $rows->count() . " questions.");
        }
         // Pour JSON, la collection est passée manuellement après décodage, la vérification est là aussi.

        if ($this->isJson) {
            // Pour JSON, $rows est la collection que nous avons créée manuellement dans `store`
            $this->processJsonFormat($rows);
        } else {
             // Pour CSV/XLSX, $rows est fourni par Maatwebsite/Excel grâce à WithHeadingRow
            $this->processExcelCsvFormat($rows);
        }
    }

    /**
     * Traite les lignes pour le format Excel/CSV. Appelée par collection().
     */
    protected function processExcelCsvFormat(Collection $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            $currentLineNumber = $rowIndex + 2; // Pour les messages d'erreur éventuels

            // Validation de base (redondante si validateImport est bien faite, mais sécurité)
            $validator = Validator::make($row->toArray(), [
                'question' => 'required|string',
                'points' => 'required|numeric|min:1',
                'correct_answer' => 'required',
            ], self::getValidationMessages('csv'));

            if ($validator->fails()) {
                // Lever une exception qui sera attrapée dans le contrôleur
                throw ValidationException::withMessages([
                    'import_error' => "Erreur d'importation Ligne " . $currentLineNumber . ": " . implode(', ', $validator->errors()->all())
                ]);
            }

            // Compter les réponses non vides
            $answerCount = 0;
            $i = 1;
             while (isset($row["answer_" . $i]) && !empty(trim($row["answer_" . $i]))) {
                $answerCount++;
                $i++;
             }


            if ($answerCount < 2) {
                throw new \Exception("Importation Ligne " . $currentLineNumber . ": Doit avoir au moins 2 réponses non vides. Trouvé: " . $answerCount);
            }
             if (isset($row["answer_0"])) {
                 throw new \Exception("Importation Ligne " . $currentLineNumber . ": Les indexes des réponses doivent commencer à 1 (answer_1, answer_2,...). Colonne 'answer_0' trouvée.");
            }


            try {
                $correctAnswers = self::flexibleParseCorrectAnswers($row['correct_answer'], $answerCount);
                if (empty($correctAnswers)) {
                     throw new \Exception("Aucune réponse correcte valide trouvée pour 'correct_answer'."); // Message complété par le catch
                }
            } catch (\Exception $e) {
                throw new \Exception("Importation Ligne " . $currentLineNumber . " (correct_answer): " . $e->getMessage());
            }

            // Créer la question
            $question = Question::create([
                'quiz_id' => $this->quiz->id,
                'question_text' => $row['question'],
                'points' => $row['points']
            ]);

            // Créer les réponses (index démarrant à 1)
            for ($i = 1; $i <= $answerCount; $i++) {
                // On vérifie à nouveau que la réponse n'est pas vide, même si answerCount est basé sur ça
                 if (!empty(trim($row["answer_$i"]))) {
                    Answer::create([
                        'question_id' => $question->id,
                        'answer_text' => trim($row["answer_$i"]),
                        'is_correct' => in_array($i, $correctAnswers) // Vérifie si l'index est dans les réponses correctes parsées
                    ]);
                }
            }
        }
    }

    /**
     * Traite les données pour le format JSON. Appelée par collection().
     * $jsonData est une Collection créée à partir du tableau JSON décodé.
     */
    protected function processJsonFormat(Collection $jsonData)
    {
         // Vérification spécifique au JSON pour le type placement (sécurité)
         if ($this->quiz->type === 'placement' && $jsonData->count() !== 90) {
             throw new \Exception("Importation: Un test de niveau JSON doit contenir exactement 90 questions. Actuellement: " . $jsonData->count() . " questions.");
         }

        foreach ($jsonData as $index => $item) {
            // Assurer que $item est un tableau associatif
             $itemArray = is_object($item) ? (array)$item : $item;
             if(!is_array($itemArray)) {
                 throw new \Exception("Importation JSON Élément " . ($index + 1) . ": Les données de la question ne sont pas dans un format de tableau/objet valide.");
             }


            // Validation de base (sécurité)
            $validator = Validator::make($itemArray, [
                'question' => 'required|string',
                'points' => 'required|numeric|min:1',
                'answers' => 'required|array|min:2',
                'answers.*.text' => 'required|string',
                'answers.*.correct' => 'required|boolean',
            ], self::getValidationMessages('json'));

            if ($validator->fails()) {
                 throw ValidationException::withMessages([
                    'import_error' => "Erreur d'importation JSON Question " . ($index + 1) . ": " . implode(', ', $validator->errors()->all())
                ]);
            }

            // Vérification réponse correcte (sécurité)
             $hasCorrectAnswer = collect($itemArray['answers'])->contains(function ($answer) {
                return is_array($answer) && isset($answer['correct']) && $answer['correct'] === true;
             });
            if (!$hasCorrectAnswer) {
                throw new \Exception("Importation JSON Question " . ($index + 1) . ": Aucune réponse correcte définie ('correct': true requis pour au moins une réponse).");
            }

            // Créer la question
            $question = Question::create([
                'quiz_id' => $this->quiz->id,
                'question_text' => $itemArray['question'],
                'points' => $itemArray['points']
            ]);

            // Créer les réponses
            foreach ($itemArray['answers'] as $answerData) {
                 if(!is_array($answerData)) {
                     throw new \Exception("Importation JSON Question " . ($index + 1) . ": Une des réponses n'est pas un objet/tableau valide.");
                 }
                Answer::create([
                    'question_id' => $question->id,
                    'answer_text' => $answerData['text'],
                    'is_correct' => (bool)$answerData['correct'] // Assurer que c'est un booléen
                ]);
            }
        }
    }


    /**
     * Méthode statique (ou protégée si appelée seulement en interne) pour parser
     * la colonne 'correct_answer' des formats CSV/XLSX.
     * Gère les formats: '1', 1, '1,3', '1-3'. Indexes basés sur 1.
     *
     * @param mixed $correctAnswerInput La valeur de la cellule 'correct_answer'
     * @param int $maxAnswerIndex Le nombre maximum d'index de réponse possible (basé sur answer_1, answer_2...)
     * @return array Tableau d'entiers représentant les index (base 1) des réponses correctes.
     * @throws \Exception Si le format est invalide ou si les numéros sont hors limites.
     */
    protected static function flexibleParseCorrectAnswers($correctAnswerInput, int $maxAnswerIndex): array
    {
        if ($maxAnswerIndex < 1) {
            // Cas théorique où il n'y aurait pas de colonnes answer_N valides trouvées
            throw new \Exception("Impossible de valider 'correct_answer' car aucune colonne de réponse valide (answer_1, ...) n'a été trouvée.");
        }

        // 1. Gérer le cas où c'est déjà un entier (peut arriver avec certains parseurs Excel)
        if (is_int($correctAnswerInput)) {
            if ($correctAnswerInput >= 1 && $correctAnswerInput <= $maxAnswerIndex) {
                return [$correctAnswerInput];
            }
            throw new \Exception("Numéro de réponse correcte '$correctAnswerInput' hors limites. Doit être entre 1 et $maxAnswerIndex.");
        }

        // 2. Travailler avec une chaîne nettoyée
        $correctAnswerString = trim((string)$correctAnswerInput);

        if ($correctAnswerString === '') {
             throw new \Exception("La valeur de 'correct_answer' ne peut pas être vide.");
        }


        // 3. Essayer de parser comme une liste (virgules)
        if (strpos($correctAnswerString, ',') !== false) {
            $parts = explode(',', $correctAnswerString);
            $correctIndexes = [];
            foreach ($parts as $part) {
                $trimmedPart = trim($part);
                if (!is_numeric($trimmedPart)) {
                    throw new \Exception("Format liste invalide pour 'correct_answer' ('$correctAnswerString'). La partie '$trimmedPart' n'est pas un nombre.");
                }
                $index = (int)$trimmedPart;
                if ($index >= 1 && $index <= $maxAnswerIndex) {
                    $correctIndexes[] = $index;
                } else {
                     throw new \Exception("Numéro de réponse correcte '$index' (dans la liste '$correctAnswerString') hors limites. Doit être entre 1 et $maxAnswerIndex.");
                }
            }
            if (!empty($correctIndexes)) {
                 // Retourner les index uniques triés
                return array_values(array_unique($correctIndexes));
            }
            // Si la liste était vide après traitement (ex: ",") - ne devrait pas arriver avec la validation is_numeric
             throw new \Exception("Format liste invalide ou vide pour 'correct_answer' ('$correctAnswerString').");
        }

        // 4. Essayer de parser comme une plage (tiret)
        if (strpos($correctAnswerString, '-') !== false) {
            $parts = explode('-', $correctAnswerString);
            if (count($parts) === 2) {
                $startStr = trim($parts[0]);
                $endStr = trim($parts[1]);
                if (is_numeric($startStr) && is_numeric($endStr)) {
                    $start = (int)$startStr;
                    $end = (int)$endStr;

                    if ($start < 1 || $end < 1 || $start > $maxAnswerIndex || $end > $maxAnswerIndex) {
                         throw new \Exception("Numéros de plage ('$correctAnswerString') hors limites. Doivent être entre 1 et $maxAnswerIndex.");
                    }
                    if ($start > $end) {
                        throw new \Exception("Plage invalide ('$correctAnswerString'). Le début ($start) ne peut pas être supérieur à la fin ($end).");
                    }
                    // Générer la séquence d'index
                    return range($start, $end);
                }
            }
            // Si le format n'est pas "nombre-nombre"
             throw new \Exception("Format de plage invalide pour 'correct_answer' ('$correctAnswerString'). Doit être 'nombre-nombre' (ex: '1-3').");
        }

        // 5. Essayer de parser comme un nombre unique (chaîne)
        if (is_numeric($correctAnswerString)) {
            $index = (int)$correctAnswerString;
            if ($index >= 1 && $index <= $maxAnswerIndex) {
                return [$index];
            }
             throw new \Exception("Numéro de réponse correcte '$index' hors limites. Doit être entre 1 et $maxAnswerIndex.");
        }

        // 6. Si aucun format n'a fonctionné
        throw new \Exception("Format invalide pour 'correct_answer' ('$correctAnswerString'). Utilisez un numéro (ex: 1 ou '1'), une liste (ex: '1,3') ou une plage (ex: '1-3'). Les indexes commencent à 1.");
    }

}
