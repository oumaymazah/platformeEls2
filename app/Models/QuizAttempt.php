<?php

namespace App\Models;

use App\Models\Quiz;
use App\Models\User;
use App\Models\UserAnswer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'passed',
        'level',
        'tab_switches',
        'completed',
        'started_at',
        'finished_at'
    ];

    protected $dates = [
        'started_at',
        'finished_at'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'attempt_id');
    }

    public function isCheated()
    {
        return $this->tab_switches >= 2;
    }
	//cet methode calculer le temps reste pour finir le quiz il calculer en seconds puis il
	//envoyer danns le blade celui qui convert le temps en minutes ou en une autre forme
    public function calculateTimeLeft()
    {
        if (!$this->started_at) {
            return $this->quiz->duration * 60;
        }

        $timeElapsed = now()->diffInSeconds($this->started_at);
        $totalTime = $this->quiz->duration * 60;
        return max(0, $totalTime - $timeElapsed);
    }
}
