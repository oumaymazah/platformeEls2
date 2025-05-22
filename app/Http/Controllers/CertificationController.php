<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\Training;
use App\Models\User;
use Illuminate\Support\Facades\Str;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    //afficher la certification dans le navvigateur
    public function show(User $user, Training $training)
    {
        abort_unless(auth()->id() == $user->id, 403);
        $certification = Certification::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->firstOrFail();

        return $certification->generateCertificate()
            ->stream('certificat_' . $user->name . '.pdf');
    }

    //télécharger la certification
    public function download(User $user, Training $training)
    {
        $certification = Certification::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->firstOrFail();

        return $certification->generateCertificate()
            ->download('certificat_' . $user->name . '.pdf');
    }
}
