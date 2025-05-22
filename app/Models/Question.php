<?php

namespace App\Models;

use App\Models\Answer;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'quiz_id',
        'question_text',
        'points'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function correctAnswers()
    {
        return $this->answers()->where('is_correct', true);
    }
     /**
     * Détermine si cette question est à choix unique
     * (basé sur le nombre de réponses correctes)
     */
    public function isSingleChoice()
    {
        return $this->answers()->where('is_correct', true)->count() <= 1;
    }

    /**
     * Détermine si cette question est à choix multiple
     * (basé sur le nombre de réponses correctes)
     */
    public function isMultipleChoice()
    {
        return $this->answers()->where('is_correct', true)->count() > 1;
    }
    public function getQuestionType()
    {
        return $this->isMultipleChoice() ? 'multiple' : 'single';
    }
}
