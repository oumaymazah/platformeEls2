<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Imports\QuizImport;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Maatwebsite\Excel\Facades\Excel;

class QuizController extends Controller
{
    //page de l'affichaege des quiz
    public function index()
    {
        $query = Quiz::with(['training', 'questions'])->withCount('questions');

        if (request('status') === 'active') {
            $query->where('is_published', true);
        } elseif (request('status') === 'inactive') {
            $query->where('is_published', false);
        }

        $quizzes = $query->paginate(10);
        return view('admin.quizzes.index', compact('quizzes'));
    }
    //page pour la creation d'un quiz
    public function create()
    {
        $trainings = Training::all();
        return view('admin.quizzes.create', compact('trainings'));
    }



    //     public function store(Request $request)
    // {
    //     // Validation de la requête (inchangé)
    //     $validator = Validator::make($request->all(), [
    //         'training_id' => 'required|exists:trainings,id',
    //         'title' => 'required|string|max:255',
    //         'type' => 'required|in:final,placement',
    //         'duration' => 'required|integer|min:1',
    //         'passing_score' => 'nullable|integer|min:1',
    //         'quiz_file' => 'required|file|mimes:csv,xlsx,json'
    //     ]);

    //     if ($validator->fails()) {
    //         return redirect()->back()->withErrors($validator)->withInput();
    //     }

    //     // Vérification du quiz existant (inchangé)
    //     $existingQuiz = Quiz::where('training_id', $request->training_id)
    //                         ->where('type', $request->type)
    //                         ->first();

    //     if ($existingQuiz) {
    //         $quizTypeText = $request->type === 'placement' ? 'test de niveau' : 'quiz final';
    //         return redirect()->back()
    //             ->with('error', "Un $quizTypeText existe déjà pour cette formation.")
    //             ->withInput();
    //     }

    //     $quiz = Quiz::create([
    //         'training_id' => $request->training_id,
    //         'title' => $request->title,
    //         'type' => $request->type,
    //         'duration' => $request->duration,
    //         'passing_score' => $request->passing_score,
    //         'is_published' => false
    //     ]);

    //     try {
    //         $file = $request->file('quiz_file');
    //         $extension = $file->getClientOriginalExtension();
    //         $isJson = $extension === 'json';

    //         if ($isJson) {
    //             $jsonContent = json_decode(file_get_contents($file->getPathname()), true);
    //             $import = new QuizImport($quiz, true);
    //             $import->collection(collect($jsonContent));
    //         } else {
    //             $import = new QuizImport($quiz);
    //             Excel::import($import, $file);
    //         }

    //         return redirect()->route('admin.quizzes.show', $quiz->id)
    //             ->with('success', 'Quiz créé avec succès. Le quiz n\'est pas encore publié et donc invisible pour les étudiants.');
    //     } catch (ValidationException $e) {
    //         // Gérer spécifiquement les exceptions de validation
    //         $quiz->delete();

    //         // Récupérer les erreurs de validation
    //         $errors = $e->validator->errors()->all();
    //         $errorMessage = !empty($errors) ? implode(' ', $errors) : $e->getMessage();

    //         // Message additionnel si nécessaire
    //         if (isset($e->customMessage)) {
    //             $errorMessage = $e->customMessage . ': ' . $errorMessage;
    //         }

    //         return redirect()->back()->with('error', 'Erreur lors de l\'importation du fichier: ' . $errorMessage)->withInput();
    //     } catch (\Exception $e) {
    //         // Gérer les autres exceptions
    //         $quiz->delete();

    //         // Message d'erreur personnalisé
    //         $errorMessage = $e->getMessage();



    //         return redirect()->back()->with('error', 'Erreur lors de l\'importation du fichier: ' . $errorMessage)->withInput();
    //     }
    // }

    //methode 2
//     public function store(Request $request)
// {
//     $messages = [
//         'training_id.required' => 'La formation est obligatoire.',
//         'training_id.exists' => 'La formation sélectionnée n\'existe pas.',
//         'title.required' => 'Le titre est obligatoire.',
//         'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
//         'type.required' => 'Le type de quiz est obligatoire.',
//         'type.in' => 'Le type de quiz doit être final ou placement.',
//         'duration.required' => 'La durée est obligatoire.',
//         'duration.integer' => 'La durée doit être un nombre entier.',
//         'duration.min' => 'La durée doit être d\'au moins 1 minute.',
//         'passing_score.integer' => 'Le score de réussite doit être un nombre entier.',
//         'passing_score.min' => 'Le score de réussite doit être d\'au moins 1.',
//         'passing_score.max' => 'Le score de réussite ne peut pas dépasser 20.',
//         'quiz_file.required' => 'Le fichier de quiz est obligatoire.',
//         'quiz_file.file' => 'Le quiz doit être un fichier valide.',
//         'quiz_file.mimes' => 'Le fichier doit être au format CSV, XLSX ou JSON.'
//     ];
//     // 1. Validation du formulaire
//     $validator = Validator::make($request->all(), [
//         'training_id' => 'required|exists:trainings,id',
//         'title' => 'required|string|max:255',
//         'type' => 'required|in:final,placement',
//         'duration' => 'required|integer|min:1',
//         'passing_score' => 'nullable|integer|min:1|max:20',
//         'quiz_file' => 'required|file|mimes:csv,xlsx,json'
//     ], $messages);

//     $formErrors = $validator->fails() ? $validator->errors() : null;

//     // 2. Vérification du quiz existant
//     $existingQuizError = null;
//     if ($request->has('training_id') && $request->has('type')) {
//         $existingQuiz = Quiz::where('training_id', $request->training_id)
//                             ->where('type', $request->type)
//                             ->first();

//         if ($existingQuiz) {
//             $quizTypeText = $request->type === 'placement' ? 'test de niveau' : 'quiz final';
//             $existingQuizError = "Un $quizTypeText existe déjà pour cette formation.";
//         }
//     }

//     // 3. Validation du fichier d'importation
//     $fileErrors = [];
//     if ($request->hasFile('quiz_file')) {
//         $fileErrors = QuizImport::validateImport($request->file('quiz_file'), $request->type);
//     }

//     // 4. Si des erreurs existent, rediriger avec toutes les erreurs
//     if ($formErrors || $existingQuizError || !empty($fileErrors)) {
//         return redirect()->back()
//             ->withErrors($validator)
//             ->with('error', $existingQuizError)
//             ->with('validationErrors', $fileErrors)
//             ->withInput();
//     }

//     // 5. Si tout est valide, créer le quiz et importer les données
//     DB::beginTransaction();

//     try {
//         $quiz = Quiz::create([
//             'training_id' => $request->training_id,
//             'title' => $request->title,
//             'type' => $request->type,
//             'duration' => $request->duration,
//             'passing_score' => $request->passing_score,
//             'is_published' => false
//         ]);

//         $file = $request->file('quiz_file');
//         $extension = $file->getClientOriginalExtension();
//         $isJson = $extension === 'json';

//         if ($isJson) {
//             $jsonContent = json_decode(file_get_contents($file->getPathname()), true);
//             $import = new QuizImport($quiz, true);
//             $import->collection(collect($jsonContent));
//         } else {
//             $import = new QuizImport($quiz);
//             Excel::import($import, $file);
//         }

//         DB::commit();

//         return redirect()->route('admin.quizzes.show', $quiz->id)
//             ->with('success', 'Quiz créé avec succès. Le quiz n\'est pas encore publié et donc invisible pour les étudiants.');
//     } catch (\Exception $e) {
//         DB::rollBack();
//         return redirect()->back()
//             ->withInput()
//             ->with('error', 'Erreur inattendue: ' . $e->getMessage());
//     }
// }
    public function store(Request $request)
    {
        // --- 1. Validation de base du Formulaire ---
        $formRules = [
            'training_id' => 'required|exists:trainings,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:final,placement',
            'duration' => 'required|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:20', // Permettre 0, max 20? Ajustez si besoin.
            'quiz_file' => 'required|file|mimes:csv,xlsx,json|max:10240', // Ajout limite taille (ex: 10MB)
        ];

        $formMessages = [
            'training_id.required' => 'La formation est obligatoire.',
            'training_id.exists' => 'La formation sélectionnée n\'existe pas.',
            'title.required' => 'Le titre est obligatoire.',
            'title.max' => 'Le titre ne doit pas dépasser 255 caractères.',
            'type.required' => 'Le type de quiz est obligatoire.',
            'type.in' => 'Le type de quiz doit être "Final" ou "Test de niveau".',
            'duration.required' => 'La durée est obligatoire.',
            'duration.integer' => 'La durée doit être un nombre entier (minutes).',
            'duration.min' => 'La durée doit être d\'au moins 1 minute.',
            'passing_score.integer' => 'Le score de réussite doit être un nombre entier.',
            'passing_score.min' => 'Le score de réussite doit être au moins 0.',
             'passing_score.max' => 'Le score de réussite ne peut pas dépasser 20.',
            'quiz_file.required' => 'Le fichier de quiz est obligatoire.',
            'quiz_file.file' => 'Le quiz doit être un fichier valide.',
            'quiz_file.mimes' => 'Le fichier doit être au format CSV, XLSX ou JSON.',
            'quiz_file.max' => 'Le fichier ne doit pas dépasser 10MB.'
        ];

        $formValidator = Validator::make($request->all(), $formRules, $formMessages);
        $formErrors = $formValidator->fails() ? $formValidator->errors() : new MessageBag(); // Initialiser comme MessageBag vide si pas d'erreur

        // --- 2. Vérification du Quiz Existant (si les champs nécessaires sont valides) ---
        $existingQuizError = null;
        if (!$formErrors->has('training_id') && !$formErrors->has('type')) {
            $existingQuiz = Quiz::where('training_id', $request->training_id)
                                ->where('type', $request->type)
                                ->first();

            if ($existingQuiz) {
                $quizTypeText = $request->type === 'placement' ? 'test de niveau' : 'quiz final';
                // Ajouter cette erreur spécifique au MessageBag
                $formErrors->add('quiz_duplication', "Un $quizTypeText existe déjà pour cette formation.");
            }
        }

        // --- 3. Validation du contenu du Fichier d'Importation (si le fichier est présent et valide) ---
        $fileValidationErrors = [];
        if ($request->hasFile('quiz_file') && !$formErrors->has('quiz_file')) {
            // Valider le contenu du fichier en utilisant la méthode statique
            $fileValidationErrors = QuizImport::validateImport($request->file('quiz_file'), $request->type);
        } else if (!$request->hasFile('quiz_file') && !$formErrors->has('quiz_file')) {
            // Si le champ fichier n'a pas d'erreur de format/présence mais qu'il n'y a pas de fichier (ne devrait pas arriver avec 'required')
             $formErrors->add('quiz_file', 'Aucun fichier n\'a été reçu.');
        }


        // --- 4. Redirection si des erreurs existent (Formulaire OU Fichier) ---
        // Vérifier s'il y a des erreurs dans le MessageBag du formulaire OU dans le tableau des erreurs de fichier
        if ($formErrors->isNotEmpty() || !empty($fileValidationErrors)) {

           
            $errorMessage = $formErrors->has('quiz_duplication')? $formErrors->first('quiz_duplication'): null;

            return redirect()->back()
                ->withErrors($formErrors) // Pour les erreurs de champ
                ->with('error', $errorMessage) // Uniquement pour la duplication de quiz
                ->with('fileValidationErrors', $fileValidationErrors) // Erreurs de fichier
                ->withInput();
        }

        // --- 5. Si TOUT est valide : Création du Quiz et Importation des Données ---
        DB::beginTransaction();

        try {
            // Créer le quiz en base de données
            $quiz = Quiz::create([
                'training_id' => $request->training_id,
                'title' => $request->title,
                'type' => $request->type,
                'duration' => $request->duration,
                'passing_score' => $request->passing_score ?? 0, // Mettre 0 si null par défaut?
                'is_published' => false // Toujours non publié à la création
            ]);

            // Importer les données du fichier
            $file = $request->file('quiz_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $isJson = $extension === 'json';

            if ($isJson) {
                // Décoder le JSON
                $jsonContent = json_decode(file_get_contents($file->getPathname()), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    // Ne devrait pas arriver car validé avant, mais sécurité
                    throw new \Exception("Erreur lors du décodage final du fichier JSON: " . json_last_error_msg());
                }
                // Créer l'instance d'import et appeler collection manuellement
                $import = new QuizImport($quiz, true); // true pour indiquer JSON
                $import->collection(collect($jsonContent)); // Passer la collection décodée

            } else { // CSV ou XLSX
                // Utiliser Excel::import qui utilisera la méthode collection de QuizImport
                 $import = new QuizImport($quiz, false); // false pour indiquer non-JSON
                 Excel::import($import, $file);
            }

            // Si tout s'est bien passé
            DB::commit();

            return redirect()->route('admin.quizzes.show', $quiz->id) // Adapter le nom de la route si nécessaire
                ->with('success', 'Quiz importé et créé avec succès. Le quiz n\'est pas encore publié.');

        } catch (ValidationException $e) { // Attrape les ValidationException levées par l'import
             DB::rollBack();
             // Formatter les erreurs pour l'utilisateur
              $importErrors = $e->validator->errors()->all();
             return redirect()->back()
                 ->withInput()
                 ->with('error', 'Erreur de validation lors de l\'importation des données du fichier.')
                 ->with('fileValidationErrors', $importErrors); // Réutiliser la même variable flash pour les erreurs de fichier

        } catch (\Exception $e) { // Attrape les autres exceptions (DB, logique d'importation, etc.)
            DB::rollBack();
             \Log::error("Erreur lors de la création/importation du quiz: " . $e->getMessage(), [
                'request' => $request->except('quiz_file'), // Ne pas logger le contenu du fichier
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur inattendue est survenue lors de la création du quiz: ' . $e->getMessage() . '. Veuillez vérifier le format du fichier ou contacter l\'administrateur.');
                 // Ne pas renvoyer $e->getMessage() directement à l'utilisateur en production si elle contient des infos sensibles.
        }
    }

    //l page eli taffichou fiha les details du quiz ba3id l'mportation
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions.answers', 'training', 'attempts.user']);//cahngement de la relation de quiz
        return view('admin.quizzes.show', compact('quiz'));
    }

    //methode pour publier le quiz
    public function publish(Quiz $quiz)
    {
        if ($quiz->questions()->count() < 1) {
            return redirect()->back()->with('error', 'Le quiz doit avoir au moins une question pour être publié.');
        }

        $quiz->update(['is_published' => true]);
        return redirect()->back()->with('success', 'Quiz publié avec succès.');
    }
    //methode tit7akim fi l'activation w l'disactivation ta3 l'quiz
    public function toggle(Quiz $quiz)
    {
        $quiz->update(['is_published' => !$quiz->is_published]);

        $status = $quiz->is_published ? 'activé' : 'désactivé';
        return redirect()->back()->with('success', "Le quiz a été $status avec succès.");
    }
    //methode pour supprimer un quiz
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('admin.quizzes.index')->with('success', 'Quiz supprimé avec succès.');
    }


    public function downloadTemplate($type)
    {
        $file = null;
        switch ($type) {
            case 'csv':
                $file = storage_path('\app\public\templates\quiz_template.csv');
                break;
            case 'excel':
                $file = storage_path('\app\public\templates\quiz_template.xlsx');
                break;
            case 'json':
                $file = storage_path('\app\public\templates\quiz_template.json');
                break;
            default:
                return redirect()->back()->with('error', 'Type de template non supporté.');
        }

        // Vérification de l'existence du fichier pour déboguer
        if (!file_exists($file)) {
            return redirect()->back()->with('error', 'Le fichier demandé n\'existe pas: ' . $file);
        }

        return response()->download($file);
    }

}
