<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use App\Models\QuizAttempt;
use App\Models\UserAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Str;

use App\Models\Certification;

class QuizAttemptController extends Controller
{
    public function start(Quiz $quiz)
    {
        if (!auth()->user()->hasRole('etudiant')) {
            return redirect()->back()->with('error', 'Seuls les étudiants peuvent passer ce quiz.');
        }
        if (!$quiz->is_published) {
            return redirect()->back()->with('error', 'Ce quiz n\'est pas disponible pour le moment.');
        }
        // Vérifier si l'utilisateur a déjà tenté ce quiz
        $previousAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('completed', true)
            ->first();

        if ($previousAttempt) {
            return redirect()->route('trainings.show', $quiz->training_id)
                ->with('error', 'Vous avez déjà passé ce quiz. Une seule tentative est autorisée.');
        }

        // Vérifier s'il y a une tentative en cours
        $currentAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->where('completed', false)
            ->first();

        if ($currentAttempt) {
            // Reprendre la tentative en cours
            return redirect()->route('quizzes.attempt', $currentAttempt->id);
        }

        // Créer une nouvelle tentative
        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'quiz_id' => $quiz->id,
            'started_at' => now(),
            'score' => 0,
            'passed' => false,
            'completed' => false
        ]);

        return redirect()->route('quizzes.attempt', $attempt->id);
    }

        public function attempt(QuizAttempt $attempt, Request $request)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        if ($attempt->completed) {
            return redirect()->route('quizzes.result', $attempt->id);
        }

        $quiz = $attempt->quiz;

        // Vérifier si la session contient déjà l'ordre des questions pour cette tentative
        $sessionKey = 'quiz_' . $attempt->id . '_questions';

        if (session()->has($sessionKey)) {
            // Utiliser l'ordre des questions sauvegardé en session
            $questionIds = session($sessionKey);
            $questions = $quiz->questions()
                ->whereIn('id', $questionIds)
                ->get()
                ->sortBy(function($question) use ($questionIds) {
                    return array_search($question->id, $questionIds);
                })
                ->values();

            // Charger les réponses dans un ordre aléatoire pour chaque question
            foreach ($questions as $question) {
                $question->setRelation('answers', $question->answers()->inRandomOrder()->get());
            }
        } else {
            // Premier chargement: générer un ordre aléatoire et le sauvegarder
            $questions = $quiz->questions()->inRandomOrder()->get();
            $questionIds = $questions->pluck('id')->toArray();
            session([$sessionKey => $questionIds]);

            // Charger les réponses dans un ordre aléatoire pour chaque question
            foreach ($questions as $question) {
                $question->setRelation('answers', $question->answers()->inRandomOrder()->get());
            }
        }

        // Récupérer les réponses déjà données par l'utilisateur
        $userAnswers = [];
        $userAnswersData = UserAnswer::where('attempt_id', $attempt->id)
            ->get();

        foreach ($userAnswersData as $userAnswer) {
            if (!isset($userAnswers[$userAnswer->question_id])) {
                $userAnswers[$userAnswer->question_id] = [];
            }
            $userAnswers[$userAnswer->question_id][] = $userAnswer->answer_id;
        }

        // Déterminer la question actuelle
        $currentQuestionIndex = $request->query('question', 0);
        // S'assurer que l'index est dans les limites valides
        $currentQuestionIndex = max(0, min(count($questions) - 1, intval($currentQuestionIndex)));
        $currentQuestion = $questions[$currentQuestionIndex];

        $timeLeft = $attempt->calculateTimeLeft();

        return view('admin.quizzes.attempt', compact('attempt', 'quiz', 'questions', 'currentQuestion', 'currentQuestionIndex', 'userAnswers', 'timeLeft'));
    }

    public function answer(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id() || $attempt->completed) {
            abort(403);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'answer_ids' => 'array',
            'answer_ids.*' => 'exists:answers,id'
        ]);

        $question = Question::find($validated['question_id']);
        $quiz = $attempt->quiz;
        $questions = $quiz->questions;

        $nextQuestionIndex = $request->input('next_question');
        $submitQuiz = $request->has('submit_quiz');

        // Supprimer toutes les anciennes réponses pour cette question (si l'utilisateur modifie sa réponse)
        UserAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->delete();

        // Enregistrer les réponses de l'utilisateur s'il y en a
        if (isset($validated['answer_ids']) && is_array($validated['answer_ids'])) {
            $this->saveUserAnswers($attempt, $question, $validated['answer_ids']);
        }

        // Mettre à jour le score
        $this->updateAttemptScore($attempt);

        // Si l'utilisateur souhaite terminer le quiz
        if ($submitQuiz) {
            return $this->finishAttempt($attempt);
        }

        // Si l'utilisateur veut naviguer vers une question spécifique
        if ($nextQuestionIndex !== null) {
            return redirect()->route('quizzes.attempt', [
                'attempt' => $attempt->id,
                'question' => $nextQuestionIndex
            ]);
        }

        // Par défaut, passer à la question suivante s'il n'y a pas d'instruction spécifique
        $currentQuestionIndex = 0;
        foreach ($questions as $index => $q) {
            if ($q->id === $question->id) {
                $currentQuestionIndex = $index;
                break;
            }
        }

        $nextIndex = $currentQuestionIndex + 1;
        if ($nextIndex >= count($questions)) {
            // Si c'est la dernière question, terminer le quiz
            return $this->finishAttempt($attempt);
        }

        return redirect()->route('quizzes.attempt', [
            'attempt' => $attempt->id,
            'question' => $nextIndex
        ]);
    }

    /**
     * Enregistre les réponses de l'utilisateur
     *
     * @param QuizAttempt $attempt
     * @param Question $question
     * @param array $answerIds
     * @return void
     */
    protected function saveUserAnswers(QuizAttempt $attempt, Question $question, array $answerIds)
    {
        // Enregistrer chaque réponse de l'utilisateur
        foreach ($answerIds as $answerId) {
            $answer = Answer::find($answerId);
            $isCorrect = $answer->is_correct;

            UserAnswer::create([
                'attempt_id' => $attempt->id,
                'question_id' => $question->id,
                'answer_id' => $answerId,
                'is_correct' => $isCorrect
            ]);
        }
    }

    /**
     * Met à jour le score de la tentative
     *
     * @param QuizAttempt $attempt
     * @return float Score total (%)
     */
    protected function updateAttemptScore(QuizAttempt $attempt)
    {
        $quiz = $attempt->quiz;
        $questions = $quiz->questions;

        // Si c'est un test de niveau, on compte simplement le nombre de questions entièrement correctes
        if ($quiz->isPlacementTest()) {
            return $this->updatePlacementTestScore($attempt, $questions);
        } else {
            // Pour les quiz finaux, on utilise la pondération des points par question
            return $this->updateFinalQuizScore($attempt, $questions);
        }
    }

    /**
     * Calcule le score pour un test de niveau
     *
     * @param QuizAttempt $attempt
     * @param Collection $questions
     * @return float
     */
    protected function updatePlacementTestScore(QuizAttempt $attempt, $questions)
    {
        $totalQuestions = $questions->count();
        $correctQuestions = 0;

        foreach ($questions as $question) {
            // Récupérer les réponses correctes pour cette question
            $correctAnswerIds = $question->answers()
                ->where('is_correct', true)
                ->pluck('id')
                ->toArray();

            // Récupérer les réponses de l'utilisateur pour cette question
            $userAnswerIds = UserAnswer::where('attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->pluck('answer_id')
                ->toArray();

            // Une question est considérée comme correcte si:
            // 1. L'utilisateur a sélectionné toutes les réponses correctes
            // 2. L'utilisateur n'a pas sélectionné de réponses incorrectes
            $hasAllCorrectAnswers = count(array_intersect($userAnswerIds, $correctAnswerIds)) === count($correctAnswerIds);
            $hasNoIncorrectAnswers = count(array_diff($userAnswerIds, $correctAnswerIds)) === 0;

            if ($hasAllCorrectAnswers && $hasNoIncorrectAnswers && !empty($userAnswerIds)) {
                $correctQuestions++;
            }
        }

        // Pour un test de niveau, on stocke le score en nombre de bonnes réponses
        $attempt->update(['score' => $correctQuestions]);

        // On retourne également le pourcentage pour les calculs internes
        return ($totalQuestions > 0) ? ($correctQuestions / $totalQuestions) * 100 : 0;
    }

    /**
     * Calcule le score pour un quiz final avec pondération des points
     *
     * @param QuizAttempt $attempt
     * @param Collection $questions
     * @return float
     */
    protected function updateFinalQuizScore(QuizAttempt $attempt, $questions)
    {
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($questions as $question) {
            $questionPoints = $question->points ?? 1; // Par défaut 1 point si non spécifié
            $totalPoints += $questionPoints;

            // Récupérer les réponses correctes pour cette question
            $correctAnswerIds = $question->answers()
                ->where('is_correct', true)
                ->pluck('id')
                ->toArray();

            // Récupérer les réponses de l'utilisateur pour cette question
            $userAnswerIds = UserAnswer::where('attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->pluck('answer_id')
                ->toArray();

            // Vérifier si l'utilisateur a sélectionné des réponses incorrectes
            $incorrectSelected = UserAnswer::where('attempt_id', $attempt->id)
                ->where('question_id', $question->id)
                ->where('is_correct', false)
                ->exists();

            if (!$incorrectSelected && !empty($userAnswerIds)) {
                // Si aucune réponse incorrecte et au moins une réponse donnée
                $correctSelected = count(array_intersect($userAnswerIds, $correctAnswerIds));
                $totalCorrect = count($correctAnswerIds);

                if ($totalCorrect > 0) {
                    // Nouvelle logique de notation
                    if ($correctSelected == $totalCorrect) {
                        // Toutes les bonnes réponses sélectionnées = 100% des points
                        $earnedPoints += $questionPoints;
                    } elseif ($correctSelected >= ceil($totalCorrect / 2)) {
                        // Au moins la moitié des bonnes réponses = 50% des points
                        $earnedPoints += $questionPoints / 2;
                    }
                    // Moins de la moitié des bonnes réponses = 0 point (implicite)
                }
            }
            // Si au moins une réponse incorrecte, 0 point pour cette question (déjà géré)
        }

        // Convertir en score sur 20
        // $score = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 20 : 0;
        $score = ($totalPoints > 0) ? $earnedPoints : 0;

        // Mettre à jour le score
        $attempt->update(['score' => $score]);

        // Retourner le pourcentage pour les calculs internes
        return ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;
    }

    /**
     * Calcule le score final à afficher à l'utilisateur
     *
     * @param QuizAttempt $attempt
     * @return mixed
     */
    protected function calculateScore(QuizAttempt $attempt)
    {
        if ($attempt->quiz->isPlacementTest()) {
            // Pour un test de niveau, on retourne le nombre de questions correctes
            return $attempt->score; // Déjà stocké comme nombre de bonnes réponses
        } else {
            // Pour un quiz final, on retourne la note sur 20 (déjà stockée ainsi)
            return $attempt->score;
        }
    }

    protected function createCertificate(QuizAttempt $attempt)
    {
        $certificateNumber = 'CERT-' . now()->format('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 5));

        Certification::create([
            'user_id' => $attempt->user_id,
            'training_id' => $attempt->quiz->training_id,
            'obtained_date' => now(),
            'status' => 'Délivrée',
            'certificate_number' => $certificateNumber
        ]);
    }

    public function finishAttempt(QuizAttempt $attempt)
    {
        // Le score est déjà calculé et stocké dans updateAttemptScore
        $score = $attempt->score;
        $passed = $this->determinePassStatus($attempt, $score);

        // Si c'est un test de placement, déterminer le niveau de langue ici
        if ($attempt->quiz->isPlacementTest()) {
            $level = $this->determineLanguageLevel($attempt);
            $attempt->update([
                'completed' => true,
                'finished_at' => now(),
                'passed' => $passed,
                'level' => $level
            ]);
        } else {
            $attempt->update([
                'completed' => true,
                'finished_at' => now(),
                'passed' => $passed
            ]);
        }

        // Création conditionnelle du certificat
        if ($attempt->quiz->isFinalQuiz() && $passed) {
            $this->createCertificate($attempt);
        }

        return redirect()->route('quizzes.result', $attempt->id);
    }

    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }

        $quiz = $attempt->quiz;
        $userAnswers = $attempt->userAnswers()->with(['question', 'answer'])->get();

        // Récupérer le niveau directement depuis la base de données si c'est un test de placement
        $level = $attempt->level;

        // Si pour une raison quelconque le niveau n'est pas enregistré mais qu'il s'agit d'un test de placement,
        // le déterminer à nouveau et le sauvegarder
        if ($quiz->isPlacementTest() && !$level) {
            $level = $this->determineLanguageLevel($attempt);
            // Sauvegarder le niveau s'il n'était pas déjà en base
            $attempt->update(['level' => $level]);
        }

        return view('admin.quizzes.result', compact('attempt', 'quiz', 'userAnswers', 'level'));
    }

    public function tabSwitch(QuizAttempt $attempt)
    {
        $newTabSwitches = $attempt->tab_switches + 1;
        $attempt->update(['tab_switches' => $newTabSwitches]);

        if ($newTabSwitches >= 2) {
            // Calculer le score et déterminer si l'utilisateur a réussi
            // Même en cas de triche, on calcule normalement le score
            $this->updateAttemptScore($attempt);
            $passed = $this->determinePassStatus($attempt, $attempt->score);

            // Si c'est un test de placement, déterminer le niveau de langue
            if ($attempt->quiz->isPlacementTest()) {
                $level = $this->determineLanguageLevel($attempt);
                $attempt->update([
                    'completed' => true,
                    'finished_at' => now(),
                    'passed' => $passed,
                    'level' => $level
                ]);
            } else {
                $attempt->update([
                    'completed' => true,
                    'finished_at' => now(),
                    'passed' => $passed
                ]);
            }

            return response()->json(['force_submit' => true]);
        }

        return response()->json(['tab_switches' => $newTabSwitches]);
    }

    protected function determinePassStatus(QuizAttempt $attempt, $score = null)
    {
        if ($attempt->quiz->isPlacementTest()) {
            return true; // Toujours "passé" pour un test de niveau
        }

        $score = $score ?? $attempt->score;
        return $score >= $attempt->quiz->passing_score;
    }

    protected function determineLanguageLevel(QuizAttempt $attempt)
    {
        if (!$attempt->quiz->isPlacementTest()) {
            return null;
        }

        // Utiliser directement le nombre de réponses correctes (score)
        $score = $attempt->score;
        $level = null;

        // Détermination du niveau selon le nombre de réponses correctes
        if ($score <= 20) {
            $level = 'A1 – débutant';
        } elseif ($score <= 35) {
            $level = 'A2 – faux débutant';
        } elseif ($score <= 60) {
            $level = 'B1 – intermédiaire';
        } elseif ($score <= 80) {
            $level = 'B2 – avancé';
        } elseif ($score <= 90) {
            $level = 'C1 – courant';
        } else {
            $level = 'C2 – maîtrise';
        }

        return $level;
    }
}
