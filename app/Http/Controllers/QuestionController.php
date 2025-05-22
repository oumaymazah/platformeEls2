<?php

namespace App\Http\Controllers;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function store(Request $request)
    {
        // Règles communes pour tous les types de questions
        $rules = [
            'quiz_id' => 'required|exists:quizzes,id',
            'question_text' => 'required|string|max:1000',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2',
            'answers.*' => 'required|string|max:255',
        ];

        // Adaptation des règles selon le type de question (sans stockage du type)
        if ($request->question_type === 'single') {
            $rules['correct_answer'] = 'required|integer|min:0';
        } else {
            $rules['correct_answers'] = 'required|array|min:1';
            $rules['correct_answers.*'] = 'required|integer|min:0';
        }

        $messages = [
            // Messages personnalisés
            'quiz_id.required' => 'Veuillez sélectionner un quiz.',
            'quiz_id.exists' => 'Le quiz sélectionné n\'existe pas.',

            'question_text.required' => 'Le texte de la question est obligatoire.',
            'question_text.string' => 'Le texte de la question doit être une chaîne de caractères.',
            'question_text.max' => 'Le texte de la question ne doit pas dépasser 1000 caractères.',

            'points.required' => 'Veuillez indiquer le nombre de points pour cette question.',
            'points.integer' => 'Le nombre de points doit être un nombre entier.',
            'points.min' => 'Le nombre de points doit être au moins 1.',

            'answers.required' => 'Veuillez fournir des réponses.',
            'answers.array' => 'Les réponses doivent être présentées sous forme de liste.',
            'answers.min' => 'Vous devez fournir au moins 2 réponses.',
            'answers.*.required' => 'Chaque réponse est obligatoire.',
            'answers.*.string' => 'Chaque réponse doit être une chaîne de caractères.',
            'answers.*.max' => 'Chaque réponse ne doit pas dépasser 255 caractères.',

            'correct_answer.required' => 'Veuillez sélectionner une réponse correcte.',
            'correct_answer.integer' => 'La réponse correcte doit être un nombre entier.',
            'correct_answer.min' => 'L\'indice de la réponse correcte doit être valide.',

            'correct_answers.required' => 'Veuillez indiquer au moins une réponse correcte.',
            'correct_answers.array' => 'Les réponses correctes doivent être présentées sous forme de liste.',
            'correct_answers.min' => 'Vous devez indiquer au moins une réponse correcte.',
            'correct_answers.*.required' => 'Chaque indice de réponse correcte est obligatoire.',
            'correct_answers.*.integer' => 'Les indices des réponses correctes doivent être des nombres entiers.',
            'correct_answers.*.min' => 'Les indices des réponses correctes doivent être valides.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('show_modal', 'addQuestionModal');
        }

        // Stockage de la question (sans le type)
        $question = Question::create([
            'quiz_id' => $request->quiz_id,
            'question_text' => $request->question_text,
            'points' => $request->points
        ]);

        // Préparation des réponses correctes
        $correctAnswers = [];
        if ($request->question_type === 'single') {
            $correctAnswers = [$request->correct_answer];
        } else {
            $correctAnswers = $request->correct_answers ?? [];
        }

        // Création des réponses
        foreach ($request->answers as $index => $answerText) {
            Answer::create([
                'question_id' => $question->id,
                'answer_text' => $answerText,
                'is_correct' => in_array($index, $correctAnswers)
            ]);
        }

        return redirect()->back()->with('success', 'Question ajoutée avec succès!');
    }

    public function edit(Question $question)
    {
        $question->load('answers');

        // Déterminer dynamiquement si c'est une question à choix unique ou multiple
        $correctAnswersCount = $question->answers->where('is_correct', true)->count();
        $questionType = $correctAnswersCount > 1 ? 'multiple' : 'single';

        return response()->json([
            'question' => $question,
            'answers' => $question->answers,
            'question_type' => $questionType
        ]);
    }

    public function update(Request $request, Question $question)
    {
        // Règles communes
        $rules = [
            'question_text' => 'required|string|max:1000',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string|max:255',
            'answers.*.id' => 'required|exists:answers,id',
        ];

        // Règles spécifiques selon le type de question
        if ($request->question_type === 'single') {
            $rules['correct_answer'] = 'required|exists:answers,id';
        } else {
            $rules['correct_answers'] = 'required|array|min:1';
            $rules['correct_answers.*'] = 'required|exists:answers,id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $question->update([
            'question_text' => $request->question_text,
            'points' => $request->points
        ]);

        // Préparation des réponses correctes
        $correctAnswers = [];
        if ($request->question_type === 'single') {
            $correctAnswers = [$request->correct_answer];
        } else {
            $correctAnswers = $request->correct_answers ?? [];
        }

        // Mise à jour de toutes les réponses
        foreach ($request->answers as $answerData) {
            Answer::where('id', $answerData['id'])->update([
                'answer_text' => $answerData['text'],
                'is_correct' => in_array($answerData['id'], $correctAnswers)
            ]);
        }

        return response()->json(['success' => 'Question mise à jour avec succès!']);
    }

    public function destroy(Question $question)
    {
        $question->delete();
        return redirect()->back()->with('success', 'Question supprimée avec succès!');
    }
}
