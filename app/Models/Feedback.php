<?php

namespace App\Models;


use App\Models\QuizAttempt;
use App\Models\Training;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;
    protected $table = 'feedbacks';
    protected $fillable = [
        'user_id',
        'rating_count',
        'quiz_attempt_id',
        'training_id',
    ];


    //(Chaque Feedback appartient à une Formation)

    public function training()
    {
        return $this->belongsTo(Training::class);
    }
    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    // (Chaque Feedback appartient à un Utilisateur)

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
