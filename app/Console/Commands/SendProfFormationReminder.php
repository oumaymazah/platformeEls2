<?php 
namespace App\Console\Commands;

use App\Mail\ProfessorFormationReminderMail;
use App\Models\Training;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendProfFormationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'formations:send-reminders-prof';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie des emails de rappel aux professeurs 2 jours avant le début de leurs formations';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Trouver toutes les formations qui commencent dans 2 jours
        $twoDaysFromNow = Carbon::now()->addDays(2)->startOfDay();
        $endOfDay = Carbon::now()->addDays(2)->endOfDay();
        
        $this->info('Recherche des formations débutant le ' . $twoDaysFromNow->format('d/m/Y'));
        
        $formations = Training::whereBetween('start_date', [$twoDaysFromNow, $endOfDay])
                     ->where('status', 1) // Seulement les formations publiées
                     ->get();
        
        $this->info('Formations trouvées: ' . $formations->count());
        
        // Regrouper les formations par professeur
        $profFormations = [];
        
        foreach ($formations as $formation) {
            if (!$formation->user_id) {
                $this->warn("Formation sans professeur assigné: ID {$formation->id}");
                continue;
            }
            
            // Ajouter la formation au tableau du professeur
            if (!isset($profFormations[$formation->user_id])) {
                $profFormations[$formation->user_id] = [];
            }
            
            $profFormations[$formation->user_id][] = $formation;
        }
        
        // Envoyer un email à chaque professeur avec toutes ses formations
        foreach ($profFormations as $professorId => $professorFormations) {
            try {
                // Récupérer le professeur
                $professor = User::find($professorId);
                
                if (!$professor) {
                    $this->error("Aucun professeur trouvé pour l'ID: {$professorId}");
                    continue;
                }
                
                $this->info("Envoi d'un email au professeur {$professor->name} {$professor->lastname} pour " . count($professorFormations) . " formations");
                
                // Envoyer l'email
                Mail::to($professor->email)->send(new ProfessorFormationReminderMail($professorFormations, $professor));
                
                $this->info("Email envoyé avec succès!");
                
                // Enregistrer l'envoi dans les logs
                Log::info("Email de rappel envoyé au professeur ID:{$professorId} pour " . count($professorFormations) . " formations");
            } catch (\Exception $e) {
                $this->error("Erreur lors de l'envoi de l'email au professeur ID: {$professorId}: " . $e->getMessage());
                Log::error("Erreur lors de l'envoi du rappel au professeur ID:{$professorId}: " . $e->getMessage());
            }
        }
        
        return 0;
    }
}