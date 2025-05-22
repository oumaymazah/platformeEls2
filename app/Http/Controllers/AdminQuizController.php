<?php

namespace App\Http\Controllers;
use App\Models\QuizAttempt;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class AdminQuizController extends Controller
{
    

    public function index(Request $request)
{
    $query = QuizAttempt::with([
        'user',
        'quiz.training',
        'userAnswers.answer'
    ])->latest();

    // Filtres
    if ($request->filled('training_id')) {
        $query->whereHas('quiz', fn($q) => $q->where('training_id', $request->training_id));
    }

    if ($request->filled('quiz_type')) {
        $query->whereHas('quiz', fn($q) => $q->where('type', $request->quiz_type));
    }

    if ($request->filled('date')) {
        $query->whereDate('created_at', $request->date);
    }

    if ($request->filled('status')) {
        $query->where('passed', $request->status === 'passed');
    }

    if ($request->filled('cheat_detected')) {
        $query->where('tab_switches', '>', 3);
    }

    // Augmenter le nombre d'éléments par page pour les tests
    $attempts = $query->paginate(10); // Changez la valeur selon vos besoins

    // Préparer les données communes
    $viewData = [
        'attempts' => $attempts,
        'trainings' => Training::all(),
        'quizTypes' => [
            'final' => 'Quiz Final',
            'placement' => 'Test de Niveau'
        ]
    ];

    // Si la requête est en Ajax, retourner seulement la vue partielle
    if ($request->ajax()) {
        return view('admin.quizzes.attempts-details', $viewData);
    }

    // Sinon, retourner la vue complète
    return view('admin.quizzes.attempts-details', $viewData);
}

    /**
     * Affiche le détail d'une tentative spécifique
     */
    public function show(QuizAttempt $attempt)
    {
        $attempt->load([
            'user',
            'quiz.training',
            'userAnswers.answer',
            'userAnswers.question.correctAnswers'
        ]);

        return view('admin.quizzes.attempt-single', compact('attempt'));
    }

    public function destroy(QuizAttempt $attempt)
    {
        $attempt->delete();
        return response()->json(['success' => 'Tentative supprimée avec succès.']);

        // return redirect()->route('admin.quizzes.index')->with('success', 'Tentative supprimée avec succès.');
    }
}
