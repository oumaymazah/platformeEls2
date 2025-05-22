<?php

namespace App\Models;
use App\Models\Cart;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'cart_id', 'reservation_date', 'reservation_time','status',
        'payment_date','training_data'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
 protected $casts = [
        'training_data' => 'array', // Cast le JSON en array PHP
    ];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    protected $dates = [
        'created_at',
        'updated_at',
        'reservation_date',
        'payment_date'
    ];
    // Observer qui s'exécute avant la sauvegarde du modèle
    protected static function boot()
    {
        parent::boot();
        
        // Lorsqu'une réservation est mise à jour
        static::saving(function ($reservation) {
            // Détecter si le statut a changé
            if ($reservation->isDirty('status')) {
                $oldStatus = $reservation->getOriginal('status');
                $newStatus = $reservation->status;
                
                // Si le statut passe de 0 à 1 (payée)
                if ($oldStatus == 0 && $newStatus == 1) {
                    $reservation->payment_date = Carbon::now();
                }
                // Si le statut passe de 1 à 0 (annulée) 
                else if ($oldStatus == 1 && $newStatus == 0) {
                    $reservation->payment_date = null;
                }
            }
        });
    }

 /**
 * Génère un PDF de facture pour cette réservation
 *   
 * @return \Barryvdh\DomPDF\PDF  
 */
        
    // public function generateInvoicePdf() 
    // {
    //     // Chargement du contenu HTML de la facture dans DomPDF
    //     $pdf = PDF::loadView('invoices.invoice-pdf', [
    //         'reservation' => $this->load(['trainings.user', 'user']),
    //     ]);
            
    //     // Configuration supplémentaire du PDF
    //     $pdf->setPaper('a4');
    //     $pdf->setOptions([
    //         'defaultFont' => 'dejavu sans',
    //         'isRemoteEnabled' => true,
    //         'isHtml5ParserEnabled' => true,
    //         'debugCss' => false,        // Désactiver le débogage CSS
    //         'dpi' => 150,               // Résolution plus élevée pour meilleure qualité
    //         'defaultMediaType' => 'screen', // Mode d'affichage "screen" pour un meilleur rendu
    //         'enableCssFloat' => true,   // Activer les flottants CSS
    //         'fontHeightRatio' => 1.1,   // Ajuster la hauteur du texte
    //         'isFontSubsettingEnabled' => true, // Activer le sous-ensemble de polices
    //         'tempDir' => storage_path('app/pdf-temp'), // Dossier temporaire
    //         'chroot' => public_path(),  // Racine pour les chemins de fichiers
    //         'logOutputFile' => storage_path('logs/pdf-errors.log'), // Journal d'erreurs
    //     ]);
            
    //     return $pdf;
    // }
/**
 * Génère un PDF de facture pour cette réservation
 *   
 * @return \Barryvdh\DomPDF\PDF  
 */
        
 public function generateInvoicePdf() 
 {
     // Chargement du contenu HTML de la facture dans DomPDF
     $pdf = PDF::loadView('invoices.invoice-pdf', [
         'reservation' => $this->load(['trainings.user', 'user']),
     ]);
         
     // Configuration supplémentaire du PDF
     $pdf->setPaper('a4');
     $pdf->setOptions([
         'defaultFont' => 'dejavu sans',
         'isRemoteEnabled' => true,
         'isHtml5ParserEnabled' => true,
         'debugCss' => false,        // Désactiver le débogage CSS
         'dpi' => 150,               // Résolution plus élevée pour meilleure qualité
         'defaultMediaType' => 'screen', // Mode d'affichage "screen" pour un meilleur rendu
         'enableCssFloat' => true,   // Activer les flottants CSS
         'fontHeightRatio' => 1.1,   // Ajuster la hauteur du texte
         'isFontSubsettingEnabled' => true, // Activer le sous-ensemble de polices
         'tempDir' => storage_path('app/pdf-temp'), // Dossier temporaire
         'chroot' => public_path(),  // Racine pour les chemins de fichiers
         'logOutputFile' => storage_path('logs/pdf-errors.log'), // Journal d'erreurs
     ]);
         
     return $pdf;
 }

}
