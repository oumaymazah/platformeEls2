<?php

namespace App\Models;


use App\Models\Training;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'training_id',
        'obtained_date',
        'status',
        'certificate_number',
    ];
    protected $dates = [
        'obtained_date',
        'created_at',
        'updated_at'
    ];
    // Relation avec l'étudiant
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation avec la formation
    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function generateCertificate()
    {
        $pdf = Pdf::loadView('certificates.certificate', [
            'user' => $this->user,
            'training' => $this->training,
            'certification' => $this
        ]);

        return $pdf; // Retourne l'instance sans streamer
    }

}
