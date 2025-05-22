<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Models\Training;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class FeedbackController extends Controller
{


    


    public function checkFeedback(Request $request)
    {
        $validatedData = $request->validate([
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id',
        ]);

        $hasFeedback = Feedback::where('quiz_attempt_id', $validatedData['quiz_attempt_id'])
            ->where('user_id', Auth::id())
            ->exists();

        return response()->json([
            'has_feedback' => $hasFeedback
        ]);
    }

    /**
     * Enregistre un nouveau feedback
     */
    public function store(Request $request)
    {
        // 1. Validation des données avec support pour les valeurs décimales
        $validatedData = $this->validate($request, [
            'training_id' => 'required|exists:trainings,id',
            'quiz_attempt_id' => 'required|exists:quiz_attempts,id',
            'rating_count' => 'required|numeric|min:0.5|max:5', // Accepte les demi-étoiles
        ]);

        // 2. Vérification que la tentative de quiz appartient bien à l'utilisateur
        $attempt = QuizAttempt::findOrFail($validatedData['quiz_attempt_id']);

        if ($attempt->user_id !== Auth::id()) {
            return response()->json([
                'error' => 'Action non autorisée'
            ], 403);
        }

        // 3. Vérification qu'il n'y a pas déjà un feedback pour cette tentative
        if (Feedback::where('quiz_attempt_id', $validatedData['quiz_attempt_id'])->exists()) {
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Vous avez déjà soumis un feedback pour ce quiz.'
                ], 422);
            }
            return back()->with('error', 'Vous avez déjà soumis un feedback pour ce quiz.');
        }

        // 4. Création du feedback
        $feedback = Feedback::create([
            'user_id' => Auth::id(),
            'training_id' => $validatedData['training_id'],
            'quiz_attempt_id' => $validatedData['quiz_attempt_id'],
            'rating_count' => $validatedData['rating_count'],
        ]);

        // 5. Retourner une réponse selon le type de requête
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Merci pour votre feedback ! Votre évaluation a bien été enregistrée.',
                'feedback' => $feedback
            ]);
        }

        // Redirection normale avec message de succès si ce n'est pas une requête AJAX
        return back()->with('success', 'Merci pour votre feedback ! Votre évaluation a bien été enregistrée.');
    }

}
