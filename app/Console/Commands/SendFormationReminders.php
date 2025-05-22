<?php

namespace App\Console\Commands;

use App\Mail\FormationReminderMail;
use App\Models\Reservation;
use App\Models\Training;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendFormationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'training:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie des emails de rappel aux étudiants 2 jours avant le début de leurs formations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Date cible: date de début = date actuelle + 2 jours
        $targetDate = Carbon::now()->addDays(2)->toDateString();
        
        $this->info("Recherche des formations commençant le: " . $targetDate);
        
        // Récupérer toutes les formations qui commencent dans 2 jours
        $upcomingTrainings = Training::whereDate('start_date', $targetDate)->get();
        
        if ($upcomingTrainings->isEmpty()) {
            $this->info("Aucune formation ne commence dans 2 jours.");
            return 0;
        }
        
        $this->info("Nombre de formations trouvées: " . $upcomingTrainings->count());
        
        // Tableau pour stocker les formations par utilisateur
        $userTrainings = [];
        
        // Pour chaque formation à venir
        foreach ($upcomingTrainings as $training) {
            $this->info("Traitement de la formation: " . $training->title . " (ID: " . $training->id . ")");
            
            // Trouver toutes les réservations confirmées
            $confirmedReservations = Reservation::where('status', 1)->get();
            
            foreach ($confirmedReservations as $reservation) {
                $trainingFound = false;
                
                // Vérifier dans training_data pour les réservations confirmées
                if (!empty($reservation->training_data) && is_array($reservation->training_data)) {
                    foreach ($reservation->training_data as $trainingData) {
                        if (isset($trainingData['id']) && $trainingData['id'] == $training->id) {
                            $trainingFound = true;
                            break;
                        }
                    }
                }
                
                if ($trainingFound) {
                    // Ajouter la formation à la liste des formations de l'utilisateur
                    if (!isset($userTrainings[$reservation->user_id])) {
                        $userTrainings[$reservation->user_id] = [];
                    }
                    $userTrainings[$reservation->user_id][] = $training;
                }
            }
        }
        
        // Envoyer un e-mail à chaque utilisateur avec toutes ses formations
        foreach ($userTrainings as $userId => $trainings) {
            $user = User::find($userId);
            
            if ($user && $user->email) {
                try {
                    $this->info("Envoi d'email à {$user->email} pour " . count($trainings) . " formations");
                    
                    Mail::to($user->email)->send(new FormationReminderMail($user, $trainings));
                    $this->info("Email envoyé avec succès");
                } catch (\Exception $e) {
                    $this->error("Erreur lors de l'envoi de l'email à {$user->email}: " . $e->getMessage());
                    Log::error("Erreur d'envoi d'email de rappel: " . $e->getMessage());
                }
            } else {
                $this->warn("Utilisateur introuvable ou sans email pour l'ID: " . $userId);
            }
        }
        
        $this->info("Traitement terminé");
        return 0;
    }
}