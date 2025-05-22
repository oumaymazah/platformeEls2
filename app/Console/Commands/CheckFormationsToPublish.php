<?php

namespace App\Console\Commands;

use App\Models\Formation;
use App\Models\Training;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckFormationsToPublish extends Command
{
    protected $signature = 'formations:check';
    protected $description = 'Affiche toutes les formations avec leur statut de publication';

    public function handle()
    {
        $nowUTC = Carbon::now('UTC');
        $nowTunis = $nowUTC->copy()->setTimezone('Africa/Tunis');

        $formations = Training::orderBy('publish_date', 'asc')->get();

        $formations->transform(function ($formation) use ($nowUTC) {
            $pubDateUTC = $formation->publish_date ? Carbon::parse($formation->publish_date, 'UTC') : null;
            
            // Déterminer le statut
            if ($formation->status == 1) {
                if (!$formation->publish_date) {
                    $status = 'PUBLIÉE IMMÉDIATEMENT';
                } elseif ($pubDateUTC->lte($nowUTC)) {
                    $status = 'PROGRAMMÉE PUIS PUBLIÉE';
                } else {
                    $status = 'PUBLIÉE AVANT LA DATE';
                }
            } else {
                if (!$formation->publish_date) {
                    $status = 'NON PUBLIÉE (SANS DATE)';
                } elseif ($pubDateUTC->lte($nowUTC)) {
                    $status = 'EN RETARD';
                } else {
                    $status = 'PROGRAMMÉE';
                }
            }

            return [
                'id' => $formation->id,
                'title' => $formation->title,
                'publish_date_local' => $formation->publish_date ? $pubDateUTC->copy()->setTimezone('Africa/Tunis')->format('Y-m-d H:i:s') : 'N/A',
                'status' => $status,
                'current_status' => $formation->status,
                'created_at' => $formation->created_at->format('Y-m-d H:i:s'),
                'time_diff' => $formation->publish_date ? $pubDateUTC->diffForHumans($nowUTC) : 'N/A'
            ];
        });

        // Compter les formations par statut
        $stats = [
            'pub_immediate' => $formations->where('status', 'PUBLIÉE IMMÉDIATEMENT')->count(),
            'pub_programmee' => $formations->where('status', 'PROGRAMMÉE PUIS PUBLIÉE')->count(),
            'pub_avant_date' => $formations->where('status', 'PUBLIÉE AVANT LA DATE')->count(),
            'programmees' => $formations->where('status', 'PROGRAMMÉE')->count(),
            'en_retard' => $formations->where('status', 'EN RETARD')->count(),
            'sans_date' => $formations->where('status', 'NON PUBLIÉE (SANS DATE)')->count()
        ];

        // Affichage du tableau détaillé
        $this->table(
            ['ID', 'Titre', 'Date Tunis', 'Statut', 'Créée le', 'Délai'],
            $formations->map(function ($f) {
                $colors = [
                    'PUBLIÉE IMMÉDIATEMENT' => 'blue',
                    'PROGRAMMÉE PUIS PUBLIÉE' => 'green',
                    'PUBLIÉE AVANT LA DATE' => 'yellow',
                    'PROGRAMMÉE' => 'cyan',
                    'EN RETARD' => 'red',
                    'NON PUBLIÉE (SANS DATE)' => 'gray'
                ];

                return [
                    $f['id'],
                    $f['title'],
                    $f['publish_date_local'],
                    "<fg={$colors[$f['status']]}>{$f['status']}</>",
                    $f['created_at'],
                    $f['time_diff']
                ];
            })->toArray()
        );

        // Affichage des statistiques
        $this->newLine(2);
        $this->line("<fg=yellow>STATISTIQUES DES PUBLICATIONS</>");
        $this->line("<fg=blue>➤ {$stats['pub_immediate']} publiées immédiatement</>");
        $this->line("<fg=green>➤ {$stats['pub_programmee']} programmées puis publiées</>");
        $this->line("<fg=yellow>➤ {$stats['pub_avant_date']} publiées avant la date</>");
        $this->line("<fg=cyan>➤ {$stats['programmees']} programmées (non publiées)</>");
        $this->line("<fg=red>➤ {$stats['en_retard']} en retard</>");
        $this->line("<fg=gray>➤ {$stats['sans_date']} non publiées (sans date)</>");
    }
}