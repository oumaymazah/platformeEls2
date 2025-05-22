<!-- Modal de facture pour la réservation -->
<div class="modal fade" id="invoiceModal{{ $reservation->id }}" tabindex="-1" aria-labelledby="invoiceModalLabel{{ $reservation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel{{ $reservation->id }}">
                     Reçu - Réservation 
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="invoiceContent{{ $reservation->id }}">
                <div class="invoice-container">
                    <!-- En-tête de la facture -->
                    <div class="invoice-header">
                        <div class="row px-4 pt-4">
                            <div class="col-7">
                                <div class="logo-container">
                                    <div class="logo-wrapper">
                                        <img src="{{ asset('img/logo.png') }}" alt="Logo CENTRE ELS" class="invoice-logo" onerror="this.src='https://via.placeholder.com/120x60?text=CENTRE+ELS'">
                                    </div>
                                    <div class="company-info">
                                        <h3 class="company-name"><span style="color: #FF4B59;">E</span>MPOWERMENT<br><span style="color:  #2B6ED4;">L</span>EARNING<br><span style="color: #FFF435;">S</span>UCCESS</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-5 text-end">
                                <div class="invoice-info mt-3">
                                    
                                    <p><strong>Reçu No:</strong> <span class="fw-bold">{{  $reservation->id }}</span></p>
                                    <p><strong>Date d'émission:</strong> {{ \Carbon\Carbon::parse($reservation->created_at)->format('d.m.Y') }}</p>
                                    <div class="status-badge mb-3">
                                        @if($reservation->status)
                                        <span class="badge bg-royal-blue">Payée</span>
                                        @else
                                        <span class="badge bg-danger text-white">En attente de paiement</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Information client -->
                    <div class="row px-4 mt-4">
                        <div class="col-6">
                            <div class="invoice-info-box">
                                <div class="invoice-pay-to">
                                    <h5></i>Payé à:</h5>
                                    <p>
                                        <strong>EMPOWERMENT LEARNING SUCCESS</strong><br>
                                        Rue farabi trocadéro, immeuble kraiem 1 étage<br>
                                        <i class="fas fa-phone-alt me-1"></i> 52450193 / 21272129<br>
                                        <i class="fas fa-envelope me-1"></i> els.center2022@gmail.com
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="invoice-info-box">
                                <div class="invoice-to">
                                    <h5>Facturé à:</h5>
                                    <p>
                                        <strong>{{ auth()->user()->name }} {{ auth()->user()->lastname }}</strong><br>
                                        @if(auth()->user()->address)
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ auth()->user()->address }}<br>
                                        @endif
                                        @if(auth()->user()->phone)
                                            <i class="fas fa-phone-alt me-1"></i> {{ auth()->user()->phone }}<br>
                                        @endif
                                        <i class="fas fa-envelope me-1"></i> {{ auth()->user()->email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Détails de la facture -->
                    <div class="px-4 mt-4">
                        <div class="table-responsive">
                            <table class="table invoice-table">
                                <thead>
                                    <tr class="table-header-row">
                                        <th>Formations</th>
                                        <th>Professeur</th>
                                        <th class="text-end nowrap">Prix original</th>
                                        <th class="text-end">Remise</th>
                                        <th class="text-end nowrap">Prix final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservation->trainings as $index => $training)
                                        <tr>
                                            <td>{{ $index + 1 }}. {{ $training->title }}</td>
                                            <td>Par {{ $training->user ? $training->user->lastname . ' ' . $training->user->name : 'Non assigné' }}</td>
                                            <td class="text-end">{{ number_format($training->price, 2, ',', ' ') }} Dt</td>
                                            <td class="text-end">
                                                @if($training->discount > 0)
                                                    {{ $training->discount }}% 
                                                    ({{ number_format($training->discount_amount, 2, ',', ' ') }} Dt)
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format($training->price_after_discount, 2, ',', ' ') }} Dt</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-7">

                            </div>
                            <div class="col-5">
                                <div class="invoice-totals-wrapper">
                                    <div class="invoice-totals-container">
                                        <table class="table table-clear invoice-totals mb-0">
                                            <tbody>
                                                <tr class="subtotal-row">
                                                    <td class="text-end"><strong>Sous-total:</strong></td>
                                                    <td class="price-column">{{ number_format($reservation->original_total, 2, ',', ' ') }} Dt</td>
                                                </tr>
                                                <tr class="discount-row">
                                                    <td class="text-end"><strong>Remise: </strong></td>
                                                    <td class="price-column">{{ number_format($reservation->total_discount, 2, ',', ' ') }} Dt</td>
                                                </tr>
                                                <tr class="grand-total">
                                                    <td colspan="2" class="total-row">
                                                        <div class="total-content">
                                                            <span class="total-label">Total:</span>
                                                            <span class="total-amount">{{ number_format($reservation->total_price, 2, ',', ' ') }} Dt</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Signature - section modifiée pour être complètement indépendante -->
                    <div class="signature-section px-4 mt-4 mb-4">
                        <div class="row">
                            <div class="col-12 text-end">
                                <div class="signature-container">
                                    <div class="signature-image">
                                        <span class="cursive-signature">Signature</span>
                                    </div>
                                    <div class="signature-name">Rahma Amri</div>
                                    <div class="signature-title">Gérante</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Les conditions générales ont été supprimées -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary print-invoice" data-invoice-id="{{ $reservation->id }}">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <button type="button" class="btn btn-danger download-pdf" data-reservation-id="{{ $reservation->id }}">
                    <i class="fas fa-file-pdf"></i> Télécharger PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
// Impression exacte du reçu comme il apparaît dans le modal
// document.querySelectorAll('.print-invoice').forEach(button => {
//     button.addEventListener('click', function() {
//         const invoiceId = this.getAttribute('data-invoice-id');
        
//         // Ajout du feedback visuel
//         this.classList.add('btn-loading');
        
//         // Cloner le contenu de la facture pour l'impression
//         const modalContent = document.getElementById(`invoiceContent${invoiceId}`);
//         const contentToPrint = modalContent.cloneNode(true);
        
//         // Obtenir les dimensions actuelles du modal pour les conserver
//         const modalWidth = modalContent.offsetWidth;
        
//         // Assurer que les images sont chargées avant l'impression
//         const images = contentToPrint.querySelectorAll('img');
//         const imagePromises = Array.from(images).map(img => {
//             if (img.complete) return Promise.resolve();
//             return new Promise(resolve => {
//                 img.onload = resolve;
//                 img.onerror = resolve;
//                 // S'assurer que l'URL est absolue
//                 if (img.src.startsWith('/')) {
//                     img.src = window.location.origin + img.src;
//                 }
//             });
//         });
        
//         // Créer un style spécifique pour l'impression
//         const style = document.createElement('style');
//         style.innerHTML = `
//             @media print {
//                 /* Cacher tout le reste de la page */
//                 body * {
//                     visibility: hidden;
//                 }
                
//                 /* Rendre visible uniquement notre contenu d'impression */
//                 #printSection, #printSection * {
//                     visibility: visible;
//                 }
                
//                 /* Positionner notre contenu en haut de la page */
//                 #printSection {
//                     position: absolute;
//                     left: 0;
//                     top: 0;
//                     width: 100%;
//                     height: auto;
//                 }
                
//                 /* Force l'impression des couleurs exactement comme affichées */
//                 * {
//                     -webkit-print-color-adjust: exact !important;
//                     print-color-adjust: exact !important;
//                     color-adjust: exact !important;
//                 }
                
//                 /* Définition des marges de page */
//                 @page {
//                     size: auto;
//                     margin: 10mm;
//                 }
                
//                 /* Préserver le style du modal original */
//                 .invoice-container {
//                     width: 100% !important;
//                     max-width: 210mm !important; /* Largeur A4 moins les marges */
//                     margin: 0 auto !important;
//                     box-shadow: none !important;
//                     font-size: 14px !important; /* Taille de police agrandie */
//                     scale: 1 !important; /* Empêcher le redimensionnement */
//                     transform: scale(1) !important; /* Assurer une échelle 1:1 */
//                 }
                
//                 /* S'assurer que les couleurs des éléments spécifiques sont conservées */
//                 .table-header-row, .grand-total, .total-content {
//                     background-color: #007bff !important;
//                     color: white !important;
//                 }
                
//                 /* Éliminer toute pagination inutile */
//                 .modal-body {
//                     overflow: visible !important;
//                     page-break-inside: avoid !important;
//                 }
                
//                 /* Garantir que tous les tableaux s'affichent correctement */
//                 .table-responsive {
//                     overflow: visible !important;
//                 }
                
//                 /* Éviter les sauts de page à l'intérieur du contenu */
//                 table, tr, td, th, tbody, thead, tfoot {
//                     page-break-inside: avoid !important;
//                 }
                
//                 /* Permettre au contenu de s'adapter à une seule page */
//                 html, body {
//                     height: 99%;
//                     page-break-after: avoid;
//                 }
                
//                 /* Conserver les espacements du modal */
//                 .px-4 {
//                     padding-left: 1.5rem !important;
//                     padding-right: 1.5rem !important;
//                 }
                
//                 /* Assurer que les tailles des éléments sont préservées */
//                 .invoice-logo {
//                     max-height: 80px !important;
//                 }
                
//                 /* Préserver la taille des textes */
//                 .company-name {
//                     font-size: 16px !important;
//                 }
                
//                 /* Maintenir la taille des tableaux */
//                 .invoice-table td, .invoice-table th {
//                     padding: 8px 6px !important;
//                     font-size: 14px !important;
//                 }
                
//                 /* Assurer que les signatures sont bien visibles */
//                 .signature-container {
//                     margin-top: 20px !important;
//                 }
                
//                 /* Préserver l'apparence des badges */
//                 .badge {
//                     padding: 5px 10px !important;
//                     font-size: 14px !important;
//                 }
//             }
//         `;
        
//         // Attendre que toutes les images soient chargées
//         Promise.all(imagePromises)
//             .then(() => {
//                 // Créer un conteneur d'impression
//                 let printSection = document.getElementById('printSection');
//                 if (!printSection) {
//                     printSection = document.createElement('div');
//                     printSection.id = 'printSection';
//                     document.body.appendChild(printSection);
//                 }
                
//                 // Vider le conteneur d'impression et y ajouter le contenu
//                 printSection.innerHTML = '';
//                 printSection.appendChild(style);
//                 printSection.appendChild(contentToPrint);
                
//                 // Ajouter un style inline pour s'assurer que la largeur est préservée
//                 contentToPrint.style.width = `${modalWidth}px`;
//                 contentToPrint.style.maxWidth = '100%';
                
//                 // Petit délai pour permettre au DOM d'être mis à jour
//                 setTimeout(() => {
//                     try {
//                         window.print();
//                     } catch (e) {
//                         console.error("Erreur d'impression :", e);
//                         alert("Une erreur est survenue lors de l'impression");
//                     } finally {
//                         // Retirer le spinner
//                         button.classList.remove('btn-loading');
//                         // Nettoyer la section d'impression après impression
//                         setTimeout(() => {
//                             printSection.innerHTML = '';
//                         }, 500);
//                     }
//                 }, 500); // Délai augmenté pour s'assurer que le DOM est correctement mis à jour
//             })
//             .catch(err => {
//                 console.error("Erreur lors du chargement des images :", err);
//                 button.classList.remove('btn-loading');
//             });
//     });
// });
// Impression exacte du reçu comme il apparaît dans le modal
document.querySelectorAll('.print-invoice').forEach(button => {
    button.addEventListener('click', function() {
        const invoiceId = this.getAttribute('data-invoice-id');
        
        // Ajout du feedback visuel
        this.classList.add('btn-loading');
        
        // Cloner le contenu de la facture pour l'impression
        const modalContent = document.getElementById(`invoiceContent${invoiceId}`);
        const contentToPrint = modalContent.cloneNode(true);
        
        // Obtenir les dimensions actuelles du modal pour les conserver
        const modalWidth = modalContent.offsetWidth;
        
        // Assurer que les images sont chargées avant l'impression
        const images = contentToPrint.querySelectorAll('img');
        const imagePromises = Array.from(images).map(img => {
            if (img.complete) return Promise.resolve();
            return new Promise(resolve => {
                img.onload = resolve;
                img.onerror = resolve;
                // S'assurer que l'URL est absolue
                if (img.src.startsWith('/')) {
                    img.src = window.location.origin + img.src;
                }
            });
        });
        
        // Créer un style spécifique pour l'impression
        const style = document.createElement('style');
        style.innerHTML = `
            @media print {
                /* Cacher tout le reste de la page */
                body * {
                    visibility: hidden;
                }
                
                /* Rendre visible uniquement notre contenu d'impression */
                #printSection, #printSection * {
                    visibility: visible;
                }
                
                /* Positionner notre contenu en haut de la page */
                #printSection {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: auto;
                }
                
                /* Force l'impression des couleurs exactement comme affichées */
                * {
                    -webkit-print-color-adjust: exact !important;
                    print-color-adjust: exact !important;
                    color-adjust: exact !important;
                }
                
                /* Définition des marges de page */
                @page {
                    size: auto;
                    margin: 10mm;
                }
                
                /* Préserver le style du modal original */
                .invoice-container {
                    width: 100% !important;
                    max-width: 210mm !important; /* Largeur A4 moins les marges */
                    margin: 0 auto !important;
                    box-shadow: none !important;
                    font-size: 14px !important; /* Taille de police agrandie */
                    scale: 1 !important; /* Empêcher le redimensionnement */
                    transform: scale(1) !important; /* Assurer une échelle 1:1 */
                }
                
                /* S'assurer que les couleurs des éléments spécifiques sont conservées */
                .table-header-row, .grand-total, .total-content {
                    background-color: #007bff !important;
                    color: white !important;
                }
                
                /* Éliminer toute pagination inutile */
                .modal-body {
                    overflow: visible !important;
                    page-break-inside: avoid !important;
                }
                
                /* Garantir que tous les tableaux s'affichent correctement */
                .table-responsive {
                    overflow: visible !important;
                }
                
                /* Éviter les sauts de page à l'intérieur du contenu */
                table, tr, td, th, tbody, thead, tfoot {
                    page-break-inside: avoid !important;
                }
                
                /* Permettre au contenu de s'adapter à une seule page */
                html, body {
                    height: 99%;
                    page-break-after: avoid;
                }
                
                /* Conserver les espacements du modal */
                .px-4 {
                    padding-left: 1.5rem !important;
                    padding-right: 1.5rem !important;
                }
                
                /* Assurer que les tailles des éléments sont préservées */
                .invoice-logo {
                    max-height: 80px !important;
                    margin-left:-85px !important;

                }
                
                /* Préserver la taille des textes */
                .company-name {
                    font-size: 16px !important;
                }
                
                /* Maintenir la taille des tableaux */
                .invoice-table td, .invoice-table th {
                    padding: 8px 6px !important;
                    font-size: 14px !important;
                }
                
                /* Assurer que les signatures sont bien visibles */
                .signature-container {
                    margin-top: 20px !important;
                }
                
                /* Préserver l'apparence des badges */
                .badge {
                    padding: 5px 10px !important;
                    font-size: 14px !important;
                }
            }
        `;
        
        // Attendre que toutes les images soient chargées
        Promise.all(imagePromises)
            .then(() => {
                // Créer un conteneur d'impression
                let printSection = document.getElementById('printSection');
                if (!printSection) {
                    printSection = document.createElement('div');
                    printSection.id = 'printSection';
                    document.body.appendChild(printSection);
                }
                
                // Vider le conteneur d'impression et y ajouter le contenu
                printSection.innerHTML = '';
                printSection.appendChild(style);
                printSection.appendChild(contentToPrint);
                
                // Ajouter un style inline pour s'assurer que la largeur est préservée
                contentToPrint.style.width = `${modalWidth}px`;
                contentToPrint.style.maxWidth = '100%';
                
                // Petit délai pour permettre au DOM d'être mis à jour
                setTimeout(() => {
                    try {
                        window.print();
                    } catch (e) {
                        console.error("Erreur d'impression :", e);
                        alert("Une erreur est survenue lors de l'impression");
                    } finally {
                        // Retirer le spinner
                        button.classList.remove('btn-loading');
                        // Nettoyer la section d'impression après impression
                        setTimeout(() => {
                            printSection.innerHTML = '';
                        }, 500);
                    }
                }, 500); // Délai augmenté pour s'assurer que le DOM est correctement mis à jour
            })
            .catch(err => {
                console.error("Erreur lors du chargement des images :", err);
                button.classList.remove('btn-loading');
            });
    });
});

// Téléchargement PDF amélioré avec support des images
document.querySelectorAll('.download-pdf').forEach(button => {
    button.addEventListener('click', function() {
        const reservationId = this.getAttribute('data-reservation-id');
        const element = document.getElementById(`invoiceContent${reservationId}`);
        
        // Ajout du feedback visuel
        this.classList.add('btn-loading');
        
        // S'assurer que les images sont chargées avec URLs absolues
        const images = element.querySelectorAll('img');
        images.forEach(img => {
            // Convertir les URLs relatives en URLs absolues
            if (img.src.startsWith('/')) {
                img.src = window.location.origin + img.src;
            }
            // Ajouter un attribut crossorigin pour les images externes
            img.setAttribute('crossorigin', 'anonymous');
        });
        
        // Configuration pour html2pdf
        // const opt = {
        //     margin: [0.5, 0.5, 0.5, 0.5],
        //     filename: `Reçu_ELS_${reservationId}.pdf`,
        //     image: { type: 'jpeg', quality: 0.98 },
        //     html2canvas: { 
        //         scale: 2,
        //         useCORS: true,
        //         logging: true,
        //         letterRendering: true,
        //         allowTaint: true
        //     },
        //     jsPDF: { 
        //         unit: 'in', 
        //         format: 'a4', 
        //         orientation: 'portrait',
        //         compress: true
        //     },
        //     pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
        // };
        // Dans la partie download-pdf, modifiez les options comme suit :
const opt = {
    margin: [0.3, 0.3, 0.3, 0.3], // Marges réduites au minimum
    filename: `Reçu_ELS_${reservationId}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { 
        scale: 2,
        useCORS: true,
        logging: true,
        letterRendering: true,
        allowTaint: true,
        scrollY: 0 // Important pour éviter les problèmes de positionnement
    },
    jsPDF: { 
        unit: 'in', 
        format: 'a4', 
        orientation: 'portrait',
        compress: true
    },
    pagebreak: { 
        mode: ['avoid-all', 'css', 'legacy'],
        before: '#force-page-break' // Utilisez cette option si nécessaire
    }
};

// Ajoutez ce style temporaire avant la génération du PDF :
const styleNode = document.createElement('style');
styleNode.innerHTML = `
    @media print {
        /* Réduire les espacements pour le PDF */
        .invoice-header {
            padding-bottom: 5px !important;
        }
        .invoice-info-box {
            padding: 10px !important;
        }
        .mt-4 {
            margin-top: 0.5rem !important;
        }
        .px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .invoice-table td, .invoice-table th {
            padding: 4px 3px !important;
            font-size: 12px !important;
        }
        /* Forcer la signature à rester sur la première page */
        .signature-section {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: auto;
            margin-top: 0 !important;
            page-break-inside: avoid !important;
        }
        /* Réduire la taille de certains éléments si nécessaire */
        .company-name {
            font-size: 14px !important;
        }
        .invoice-title {
            font-size: 24px !important;
        }
    }
`;
element.appendChild(styleNode);
        
        // Création du PDF avec gestion des erreurs
        html2pdf()
            .set(opt)
            .from(element)
            .save()
            .then(() => {
                console.log('PDF généré avec succès');
                // Supprimer le style temporaire
                element.removeChild(styleNode);
            })
            .catch(err => {
                console.error('Erreur génération PDF:', err);
                alert("Erreur lors de la génération du PDF");
                // Supprimer le style en cas d'erreur également
                element.removeChild(styleNode);
            })
            .finally(() => {
                // Retirer le spinner
                this.classList.remove('btn-loading');
            });
    });
});
</script>

<style>
/* styles-invoice.css */
.invoice-container {
    background-color: #fff;
    position: relative;
    font-family: 'Arial', sans-serif;
    color: #333;
    border-radius: 6px;
    overflow: hidden;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

/* En-tête de la facture */
.invoice-header {
    border-bottom: 1px solid #eee;
    padding-bottom: 15px; /* Réduit légèrement */
}

.invoice-logo {
    max-height: 80px;
    max-width: 120px;
    margin-left: -20px;
}

.company-info {
    display: flex;
    flex-direction: column;
}

.company-name {
    font-size: 16px;
    font-weight: bold;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1.3;
}

/* Style du titre INVOICE */
.invoice-title {
    font-size: 48px;
    font-weight: 700;
    color: #000;
    text-transform: uppercase;
    margin: 0;
    letter-spacing: 2px;
}

.invoice-info {
    text-align: right;
}

.invoice-info p {
    margin-bottom: 5px;
    font-size: 14px;
}

.status-badge {
    margin-bottom: 15px;
}

/* Info boxes pour les sections client */
.invoice-info-box {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    height: 100%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.invoice-to h5, .invoice-pay-to h5 {
    color: #333;
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 10px;
    border-bottom: 1px solid #ddd;
    padding-bottom: 8px;
}

.invoice-to p, .invoice-pay-to p {
    font-size: 14px;
    color: #666;
    line-height: 1.5;
    margin-bottom: 0;
}

/* Table styles */
.invoice-table {
    margin-bottom: 0;
    border-collapse: collapse;
    width: 100%;
    border: 1px solid #dee2e6;
}

/* Couleur d'en-tête de tableau */
.invoice-table thead th {
    background-color: #007bff;
    border-bottom: 2px solid #dee2e6;
    color: #ffffff;
    font-weight: 600;
    padding: 10px 6px; /* Réduit pour économiser de l'espace */
}

/* Classe pour la ligne d'en-tête */
.table-header-row {
    background-color: #007bff;
    color: white;
}

.invoice-table tbody td {
    padding: 8px 6px; /* Réduit pour économiser de l'espace */
    border-bottom: 1px solid #eee;
    vertical-align: middle;
}

.invoice-table tbody tr:nth-child(even) {
    background-color: rgba(0,0,0,0.02);
}

.invoice-table tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Payment info box */
.payment-info-box {
    background-color: #f8f9fa;
    border-radius: 6px;
    padding: 15px;
    height: 100%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.payment-info-box h5 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
    border-bottom: 1px solid #ddd;
    padding-bottom: 8px;
}
.bg-royal-blue {
    background-color: #2B6ED4; /* Couleur royal blue */
    color: white;
}

.payment-info-box p {
    margin-bottom: 0.25rem;
    font-size: 14px;
}

/* Style du logo */
.logo-container {
    display: flex;
    align-items: center;
    margin-left: -60px;
}

.logo-wrapper {
    margin-right: 15px;
}

/* Styles pour la signature - MODIFIÉ pour être indépendant */
.signature-section {
    margin-top: 10px;
    margin-bottom: 50px; /* Augmenté pour plus d'espace en bas */
}

.signature-container {
    display: inline-block;
    text-align: center;
    border-top: 1px solid #ddd;
    padding-top: 8px;
    min-width: 200px;
    position: relative;
    bottom: 0;
    right: 0;
    transform: none !important;
}

.signature-image {
    margin-bottom: 3px;
}

.cursive-signature {
    font-family: 'Brush Script MT', cursive;
    font-size: 26px;
    color: #000;
    font-weight: bold;
}

.signature-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 2px;
}

.signature-title {
    font-size: 12px;
    color: #777;
}

/* Styles pour la page de réservation */
.reservation-card {
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.reservation-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
}

.training-thumbnail {
    transition: transform 0.2s ease;
    border-radius: 4px;
}

.training-thumbnail:hover {
    transform: scale(1.8);
    z-index: 10;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.no-image-placeholder {
    border: 1px solid #dee2e6;
    color: #adb5bd;
}

.nowrap {
    white-space: nowrap;
}

/* Classe pour le bouton de téléchargement en cours */
.btn-loading {
    opacity: 0.8;
    position: relative;
}

.btn-loading:after {
    content: '';
    display: inline-block;
    width: 1em;
    height: 1em;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 1s ease-in-out infinite;
    position: absolute;
    right: 8px;
    top: calc(50% - 0.5em);
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Modal customization */
.modal-header {
    background-color: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

/* Augmenter la largeur du modal */
.modal-dialog.modal-lg {
    max-width: 900px;
}

.modal-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.modal-footer {
    border-top: 1px solid #eee;
    padding: 10px 10px;
    background-color: #f8f9fa;
}

/* Print button styling */
.print-invoice, .download-pdf {
    transition: all 0.3s ease;
}

.print-invoice:hover, .download-pdf:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Badge styling */
.badge {
    padding: 5px 10px;
    font-weight: normal;
    border-radius: 4px;
}

/* ------------------------------
   SECTION TOTAUX FACTURE
   ------------------------------ */
.invoice-totals-wrapper {
    display: flex;
    justify-content: flex-end;
    width: 100%;
}

.invoice-totals-container {
    background-color: #f8f9fa;
    border-radius: 4px;
    width: auto;
    min-width: 250px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.invoice-totals {
    width: 100%;
    margin-bottom: 0;
    padding: 0;
}

.invoice-totals td {
    border: none;
    padding: 8px 15px;
    font-size: 14px;
}

.invoice-totals .price-column {
    text-align: right;
    width: 120px;
    white-space: nowrap;
}

/* Lignes de sous-total et remise */
.subtotal-row, .discount-row {
    background-color: #f8f9fa;
}

/* Ligne du total avec fond bleu */
.grand-total {
    background-color: #007bff !important;
    color: white !important;
    padding: 0;
}

.grand-total td {
    padding: 0;
}

.total-row {
    padding: 0;
    border: none;
}

.total-content {
    display: flex;
    justify-content: space-between;
    padding: 8px 15px; /* Légèrement réduit */
    width: 100%;
    background-color: #007bff !important;
    color: white !important;
}

.total-label {
    font-weight: bold;
    font-size: 16px;
}

.total-amount {
    font-weight: bold;
    white-space: nowrap;
    font-size: 16px;
}

/* Media Query for printing */
@media print {
    /* Réinitialiser les dimensions et les échelles */
    html, body {
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 0;
    }
    
    /* S'assurer que le contenu d'impression occupe toute la page */
    #printSection {
        width: 100%;
        max-width: 100%;
    }
    
    /* Style pour la facture imprimée */
    .invoice-container {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 15px !important;
        font-size: 14px !important;
        transform-origin: top left;
    }
    
    /* Préserver les espacements */
    .mt-4 {
        margin-top: 1.5rem !important;
    }
    
    .px-4 {
        padding-left: 1.5rem !important;
        padding-right: 1.5rem !important;
    }
    
    /* Assurer la bonne taille des éléments spécifiques */
    .logo-container {
        margin-left: 0 !important;
    }
    
    .invoice-logo {
        max-height: 80px !important;
        max-width: 120px !important;
        margin-left: 0 !important;
    }
    
    .company-name {
        font-size: 16px !important;
    }
    
    /* S'assurer que les couleurs des éléments sont préservées */
    .table-header-row th, .total-content {
        background-color: #007bff !important;
        color: white !important;
    }
}
</style>