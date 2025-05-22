<?php
namespace App\Console\Commands;
use App\Models\Formation;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
class PublishFormationsCommand extends Command
{
    protected $signature = 'formations:publish';
    protected $description = 'Publie les formations programmées (date seulement)';
    public function handle()
    {
        // Obtenir la date actuelle (sans l'heure) au fuseau horaire de Tunis
        $today = Carbon::now('Africa/Tunis')->startOfDay();
        $this->info("Exécution pour la date : ".$today->format('Y-m-d'));
   
        // Récupérer les formations non publiées dont la date de publication est aujourd'hui ou avant
        $formations = Training::where('status', 0)
            ->where(function($query) use ($today) {
                $query->whereNull('publish_date')
                     ->orWhereDate('publish_date', '<=', $today);
            })
            ->get();
   
        $this->table(
            ['ID', 'Titre', 'Date publication', 'Statut'],
            $formations->map(function($f) {
                return [
                    $f->id,
                    $f->title,
                    $f->publish_date ?? 'NULL',
                    $f->status
                ];
            })->toArray()
        );
        
        $publishedCount = 0;
        foreach ($formations as $formation) {
            try {
                $formation->update(['status' => 1]);
                $publishedCount++;
                Log::channel('formations')->info('Formation publiée', [
                    'id' => $formation->id,
                    'title' => $formation->title,
                    'publish_date' => $formation->publish_date,
                    'published_at' => now()->format('Y-m-d')
                ]);
            } catch (\Exception $e) {
                Log::channel('formations')->error('Erreur publication formation', [
                    'id' => $formation->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
        $this->info("{$publishedCount} formations publiées avec succès");
        return 0;
    }
}