<?php

namespace App\Models;

use App\Models\Question;
use App\Models\QuizAttempt;
use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;
    protected $fillable = [
        'training_id',
        'title',
        'type',
        'duration',
        'passing_score',
        'is_published'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function isPlacementTest()
    {
        return $this->type === 'placement';
    }

    public function isFinalQuiz()
    {
        return $this->type === 'final';
    }
}
