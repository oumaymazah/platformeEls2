<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\Reservation;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingDetailController extends Controller
{
    /**
     * Affiche la page de détail d'une formation
     */
    public function show($id)
    {
        // Récupérer la formation avec ses relations
        $training = Training::with([
            'user',
            'category',
            'feedbacks',
            'quizzes' => function($query) {
                $query->where('is_published', true);
            },
            'courses.chapters.lessons'
        ])->findOrFail($id);

        // Calculer la note moyenne
        $averageRating = 0;
        $totalFeedbacks = count($training->feedbacks);

        if ($totalFeedbacks > 0) {
            $sumRatings = $training->feedbacks->sum('rating');
            $averageRating = $sumRatings / $totalFeedbacks;
        }

        // Vérifier si l'utilisateur a réservé cette formation
        $hasReserved = false;
        if (Auth::check()) {
            $userId = Auth::id();
            $hasReserved = Reservation::where('user_id', $userId)
                ->where('status', 1) // Réservation confirmée/payée
                ->where(function($query) use ($training) {
                    $query->whereJsonContains('training_data->id', $training->id);
                })
                ->exists();
        }

        return view('admin.apps.formation.detail', compact('training', 'averageRating', 'totalFeedbacks', 'hasReserved'));
    }

    /**
     * Récupère le contenu d'une leçon via AJAX
     */
    // public function getLessonContent(Request $request)
    // {
    //     $lessonId = $request->input('lesson_id');
    //     $lesson = Lesson::with('files')->findOrFail($lessonId);

    //     // Vérifier si l'utilisateur est connecté
    //     if (!Auth::check()) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'not_authenticated',
    //             'content' => view('admin.apps.formation.lesson_unauthorized', ['type' => 'not_authenticated'])->render()
    //         ]);
    //     }

    //     // Récupérer le chapitre et le cours associés à cette leçon
    //     $chapter = $lesson->chapter;
    //     $course = $chapter->course;
    //     $training = $course->training;

    //     // Récupérer l'utilisateur connecté
    //     $user = Auth::user();

    //     // Si l'utilisateur est un étudiant, vérifier s'il a payé la formation
    //     if ($user->hasRole('etudiant')) {
    //         $hasPaid = Reservation::where('user_id', $user->id)
    //             ->where('status', 1) // Réservation confirmée/payée
    //             ->where(function($query) use ($training) {
    //                 $query->whereJsonContains('training_data->id', $training->id);
    //             })
    //             ->exists();

    //         if (!$hasPaid) {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => 'not_purchased',
    //                 'content' => view('admin.apps.formation.lesson_unauthorized', ['type' => 'not_purchased', 'trainingId' => $training->id])->render()
    //             ]);
    //         }
    //     }

    //     // Si l'utilisateur est un admin/prof ou s'il est étudiant et a payé, afficher le contenu
    //     return response()->json([
    //         'status' => 'success',
    //         'title' => $lesson->title,
    //         'content' => view('admin.apps.formation.lesson_content', compact('lesson'))->render()
    //     ]);
    // }

    public function getLessonContent(Request $request)
{
    // Debug de base
    \Log::debug('Requête reçue pour leçon ID: '.$request->lesson_id);

    try {
        $lesson = Lesson::with(['files', 'chapter.course.training'])
            ->findOrFail($request->lesson_id);

        if (!Auth::check()) {

            return response()->json([
                'status' => 'error',
                'content' => view('admin.apps.formation.lesson_unauthorized', [
                    'type' => 'not_authenticated'
                ])->render()
            ]);
        }

        $user = Auth::user();
        $training = $lesson->chapter->course->training;

        if ($user->hasRole('etudiant')) {
            $hasPaid = Reservation::where('user_id', $user->id)
                ->where('status', 1)
                // ->whereJsonContains('training_data->id', $training->id)
                ->whereRaw("JSON_CONTAINS(training_data, JSON_OBJECT('id', ".$training->id."), '$')")
                ->exists();

            if (!$hasPaid) {

                return response()->json([
                    'status' => 'error',
                    'content' => view('admin.apps.formation.lesson_unauthorized', [
                        'type' => 'not_purchased',
                        'trainingId' => $training->id
                    ])->render()
                ]);
            }
        }


        return response()->json([
            'status' => 'success',
            'content' => view('admin.apps.formation.lesson_content', compact('lesson'))->render()
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'status' => 'error',
            'content' => 'Une erreur est survenue'
        ], 500);
    }
}
    /**
     * Récupère le contenu d'un quiz via AJAX
     */
    public function getQuizContent(Request $request)
    {
        $quizId = $request->input('quiz_id');
        $quiz = Quiz::with('questions.answers')->findOrFail($quizId);

        // Vérifier si l'utilisateur est connecté
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'not_authenticated',
                'content' => view('admin.apps.formation.lesson_unauthorized', ['type' => 'not_authenticated'])->render()
            ]);
        }

        // Récupérer la formation associée à ce quiz
        $training = $quiz->training;

        // Récupérer l'utilisateur connecté
        $user = Auth::user();

        // Si l'utilisateur est un étudiant, vérifier s'il a payé la formation
        if ($user->hasRole('etudiant')) {
            $hasPaid = Reservation::where('user_id', $user->id)
                ->where('status', 1) // Réservation confirmée/payée
                ->where(function($query) use ($training) {
                    $query->whereJsonContains('training_data->id', $training->id);
                })
                ->exists();

            if (!$hasPaid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'not_purchased',
                    'content' => view('admin.apps.formation.lesson_unauthorized', ['type' => 'not_purchased', 'trainingId' => $training->id])->render()
                ]);
            }
        }

        // Si l'utilisateur est un admin/prof ou s'il est étudiant et a payé, afficher le contenu
        return response()->json([
            'status' => 'success',
            'title' => $quiz->title,
            'content' => view('admin.apps.formation.quiz_content', compact('quiz'))->render()
        ]);
    }
}
