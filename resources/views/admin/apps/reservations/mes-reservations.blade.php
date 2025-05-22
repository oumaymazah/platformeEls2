@extends('layouts.admin.master')
<script>
    // Définir les fonctions dans l'espace global pour y accéder depuis n'importe où
window.synchronizeWithServer = function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;
    
    fetch('/panier/count', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        const count = data.count || 0;
        localStorage.setItem('cartCount', count.toString());
        
        // Mettre à jour tous les badges existants
        const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
        badges.forEach(badge => {
            badge.textContent = count.toString();
            badge.style.display = count > 0 ? 'flex' : 'none';
        });
        
        // Mettre à jour le badge fixe si présent
        const fixedBadge = document.getElementById('fixed-cart-badge');
        if (fixedBadge) {
            fixedBadge.textContent = count.toString();
            fixedBadge.style.display = count > 0 ? 'flex' : 'none';
        }
        
        // Créer des badges s'il n'y en a pas et si le compteur > 0
        if (count > 0 && badges.length === 0) {
            window.initializeCartBadges();
        }
    })
    .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
};

window.initializeCartBadges = function() {
    // Récupérer la valeur du panier depuis localStorage
    const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    
    // Ne rien faire si le compteur est 0
    if (cartCount <= 0) return;
    
    // Injecter le style du badge si nécessaire
    if (!document.getElementById('cart-badge-styles')) {
        const style = document.createElement('style');
        style.id = 'cart-badge-styles';
        style.innerHTML = `
            .cart-badge, .custom-violet-badge {
                position: absolute;
                top: -8px;
                right: -8px;
                background-color: #2B6ED4;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                z-index: 10;
                opacity: 1 !important;
                animation: none !important;
                transition: none !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Sélecteurs pour trouver les icônes de panier
    const cartSelectors = [
        '.shopping-cart-icon', 
        'svg[data-icon="shopping-cart"]', 
        '.cart-icon', 
        'a[href*="panier"] svg', 
        '.cart-container svg',
        '.cart-link',
        '.panier-icon'
    ];
    
    const iconSelector = cartSelectors.join(', ');
    const cartIcons = document.querySelectorAll(iconSelector);
    
    cartIcons.forEach(icon => {
        const container = icon.closest('a, div, button, .cart-container');
        if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
            // Créer le badge
            const badge = document.createElement('span');
            badge.className = 'cart-badge custom-violet-badge';
            badge.textContent = cartCount.toString();
            
            // S'assurer que le conteneur est en position relative
            if (getComputedStyle(container).position === 'static') {
                container.style.position = 'relative';
            }
            
            container.appendChild(badge);
        }
    });
};

window.updateCartBadgeCount = function(count) {
    localStorage.setItem('cartCount', count.toString());
    
    // Mettre à jour tous les badges existants
    const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
    badges.forEach(badge => {
        badge.textContent = count.toString();
        badge.style.display = count > 0 ? 'flex' : 'none';
    });
    
    // Mettre à jour le badge fixe si présent
    const fixedBadge = document.getElementById('fixed-cart-badge');
    if (fixedBadge) {
        fixedBadge.textContent = count.toString();
        fixedBadge.style.display = count > 0 ? 'flex' : 'none';
    }
    
    // Créer des badges s'il n'y en a pas et si le compteur > 0
    if (count > 0 && badges.length === 0) {
        window.initializeCartBadges();
    }
};

// Fonction d'initialisation principale
function initCartSystem() {
    // Initialiser les badges immédiatement avec la valeur en cache
    window.initializeCartBadges();
    
    // Puis synchroniser avec le serveur
    setTimeout(window.synchronizeWithServer, 100);
    
    // Observer le DOM pour les nouvelles icônes de panier
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length) {
                // Vérifier si de nouvelles icônes ont été ajoutées
                window.initializeCartBadges();
            }
        });
    });
    
    // Observer le document entier
    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });
}

// Exécuter au chargement de la page
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCartSystem);
} else {
    initCartSystem();
}

// Recharger les badges quand on revient sur la page
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        window.initializeCartBadges();
        window.synchronizeWithServer();
    }
});
</script>

{{-- <script>
    // Définir les fonctions dans l'espace global pour y accéder depuis n'importe où
window.synchronizeWithServer = function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;
    
    // Vérifier si l'utilisateur a une réservation en attente
    fetch('/check-pending-reservation', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => response.json())
    .then(data => {
        const hasPendingReservation = data.hasPendingReservation;
        localStorage.setItem('hasPendingReservation', hasPendingReservation ? 'true' : 'false');
        
        // Si l'utilisateur a une réservation en attente, afficher le badge
        // Sinon, masquer le badge
        if (hasPendingReservation) {
            // Récupérer le compte d'articles pour l'afficher dans le badge
            fetch('/panier/count', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(response => response.json())
            .then(cartData => {
                const count = cartData.count || 0;
                localStorage.setItem('cartCount', count.toString());
                
                // Mettre à jour tous les badges existants
                const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
                badges.forEach(badge => {
                    badge.textContent = count.toString();
                    badge.style.display = count > 0 ? 'flex' : 'none';
                });
                
                // Mettre à jour le badge fixe si présent
                const fixedBadge = document.getElementById('fixed-cart-badge');
                if (fixedBadge) {
                    fixedBadge.textContent = count.toString();
                    fixedBadge.style.display = count > 0 ? 'flex' : 'none';
                }
                
                // Créer des badges s'il n'y en a pas et si le compteur > 0
                if (count > 0 && badges.length === 0) {
                    window.initializeCartBadges();
                }
            })
            .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
        } else {
            // Masquer tous les badges car pas de réservation en attente
            const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
            badges.forEach(badge => {
                badge.style.display = 'none';
            });
            
            // Masquer le badge fixe si présent
            const fixedBadge = document.getElementById('fixed-cart-badge');
            if (fixedBadge) {
                fixedBadge.style.display = 'none';
            }
        }
    })
    .catch(error => console.error('Erreur lors de la vérification de réservation:', error));
};

window.initializeCartBadges = function() {
    // Vérifier si l'utilisateur a une réservation en attente
    const hasPendingReservation = localStorage.getItem('hasPendingReservation') === 'true';
    
    // Ne rien faire si l'utilisateur n'a pas de réservation en attente
    if (!hasPendingReservation) return;
    
    // Récupérer la valeur du panier depuis localStorage
    const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    
    // Ne rien faire si le compteur est 0
    if (cartCount <= 0) return;
    
    // Injecter le style du badge si nécessaire
    if (!document.getElementById('cart-badge-styles')) {
        const style = document.createElement('style');
        style.id = 'cart-badge-styles';
        style.innerHTML = `
            .cart-badge, .custom-violet-badge {
                position: absolute;
                top: -8px;
                right: -8px;
                background-color: #2B6ED4;
                color: white;
                border-radius: 50%;
                width: 18px;
                height: 18px;
                font-size: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                z-index: 10;
                opacity: 1 !important;
                animation: none !important;
                transition: none !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Sélecteurs pour trouver les icônes de panier
    const cartSelectors = [
        '.shopping-cart-icon', 
        'svg[data-icon="shopping-cart"]', 
        '.cart-icon', 
        'a[href*="panier"] svg', 
        '.cart-container svg',
        '.cart-link',
        '.panier-icon'
    ];
    
    const iconSelector = cartSelectors.join(', ');
    const cartIcons = document.querySelectorAll(iconSelector);
    
    cartIcons.forEach(icon => {
        const container = icon.closest('a, div, button, .cart-container');
        if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
            // Créer le badge
            const badge = document.createElement('span');
            badge.className = 'cart-badge custom-violet-badge';
            badge.textContent = cartCount.toString();
            
            // S'assurer que le conteneur est en position relative
            if (getComputedStyle(container).position === 'static') {
                container.style.position = 'relative';
            }
            
            container.appendChild(badge);
        }
    });
};

window.updateCartBadgeCount = function(count) {
    // Vérifier si l'utilisateur a une réservation en attente
    const hasPendingReservation = localStorage.getItem('hasPendingReservation') === 'true';
    
    // Ne rien faire si l'utilisateur n'a pas de réservation en attente
    if (!hasPendingReservation) {
        // Masquer tous les badges
        const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
        badges.forEach(badge => {
            badge.style.display = 'none';
        });
        
        // Masquer le badge fixe si présent
        const fixedBadge = document.getElementById('fixed-cart-badge');
        if (fixedBadge) {
            fixedBadge.style.display = 'none';
        }
        
        return;
    }
    
    localStorage.setItem('cartCount', count.toString());
    
    // Mettre à jour tous les badges existants
    const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
    badges.forEach(badge => {
        badge.textContent = count.toString();
        badge.style.display = count > 0 ? 'flex' : 'none';
    });
    
    // Mettre à jour le badge fixe si présent
    const fixedBadge = document.getElementById('fixed-cart-badge');
    if (fixedBadge) {
        fixedBadge.textContent = count.toString();
        fixedBadge.style.display = count > 0 ? 'flex' : 'none';
    }
    
    // Créer des badges s'il n'y en a pas et si le compteur > 0
    if (count > 0 && badges.length === 0) {
        window.initializeCartBadges();
    }
};

// Fonction d'initialisation principale
function initCartSystem() {
    // Synchroniser avec le serveur d'abord
    window.synchronizeWithServer();
    
    // Puis initialiser les badges avec la valeur en cache
    setTimeout(window.initializeCartBadges, 100);
    
    // Observer le DOM pour les nouvelles icônes de panier
    const observer = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.addedNodes.length) {
                // Vérifier si de nouvelles icônes ont été ajoutées
                window.initializeCartBadges();
            }
        });
    });
    
    // Observer le document entier
    observer.observe(document.documentElement, {
        childList: true,
        subtree: true
    });
}

// Exécuter au chargement de la page
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCartSystem);
} else {
    initCartSystem();
}

// Recharger les badges quand on revient sur la page
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        window.synchronizeWithServer();
        setTimeout(window.initializeCartBadges, 100);
    }
});
</script> --}}
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
@endpush

@section('content')
<div class="container my-5">

    @if($reservations->isEmpty())
        <div class="alert alert-info">
            Vous n'avez pas encore de réservations.
        </div>
    @else
    <meta name="csrf-token" content="{{ csrf_token() }}">


        @foreach($reservations as $reservation)
            <div class="card mb-4">
    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #CFE2FF;">
    <div>
        <h4 style="font-weight: bold; color:#2C2C3A;">Mes Réservations</h4>
        
        <div class="alert alert-info mb-4" style="border-left: -1px solid #2C2C3A;">
            <i class="fas fa-info-circle me-2"></i>
            Pour valider votre réservation, veuillez effectuer le paiement au centre au moins deux jours avant votre première séance de formation, en présentant le reçu ci-dessous.
        </div>
        
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0" style="color: #1b1c1d;">Réservation #{{ $reservation->id }}</h5>
                <small class="text-muted">Effectuée le {{ \Carbon\Carbon::parse($reservation->created_at)->format('d/m/Y à H:i') }}</small>
            </div>
            <div class="status-badge">
                @if($reservation->status)
                    <span class="badge" style="background-color: #2B6ED4; color: white;">Payée</span>
                @else
                    <span class="badge bg-danger text-white small">En attente de paiement</span>
                @endif
            </div>
        </div>
    </div>
</div>
                <div class="card-body">
                    
                    <h6 class="card-subtitle mb-3" style=" font-weight: 600;">Formations réservées</h6>
                    <br>
                    
                    @if($reservation->trainings->isEmpty())
                        <p>Aucune formation trouvée pour cette réservation.</p>
                    @else

                        <div class="table-responsive">
                            <table class="table">
                                <thead style="background-color: #CFE2FF;">
                                    <tr>
                                        <th style="width: 100px"></th>
                                        <th>Formation</th>
                                        <th>Professeur</th>
                                        <th class="text-end">Prix original</th>
                                        <th class="text-end">Remise</th>
                                        <th class="text-end">Prix final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservation->trainings as $training)
                                        <tr>
                                            <td>
                                                @if($training->image)
                                                    <img src="{{ asset('storage/' . $training->image) }}" alt="{{ $training->title }}" style="width: 70px; height: 50px; object-fit: cover; border-radius: 5px; border: 1px solid #CFE2FF;">
                                                @else
                                                    <div class="no-image-placeholder" style="width: 70px; height: 50px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; border-radius: 5px; border: 1px solid #CFE2FF;">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2B6ED4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $training->title }}</td>
                                            <td>{{ $training->user ? $training->user->lastname . ' ' . $training->user->name : 'Non assigné' }}</td>
                                            <td class="text-end">{{ number_format($training->price, 2, ',', ' ') }} Dt</td>
                                            <td class="text-end">
                                                @if($training->discount > 0)
                                                    <span class="badge" style="background-color: #FF5252;">{{ $training->discount }}%</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="text-end" style="font-weight: 600;">{{ number_format($training->price_after_discount, 2, ',', ' ') }} Dt</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot style="background-color: #f8f9fa; border-top: 2px solid #CFE2FF;">
                                    <tr>
                                        <th colspan="3" class="text-end">Total</th>
                                        <th class="text-end">{{ number_format($reservation->original_total, 2, ',', ' ') }} Dt</th>
                                        <th class="text-end">{{ number_format($reservation->total_discount, 2, ',', ' ') }} Dt</th>
                                        <th class="text-end" style="color: #2B6ED4; font-weight: bold;">{{ number_format($reservation->total_price, 2, ',', ' ') }} Dt</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-between" style="background-color: #f8f9fa; border-top: 1px solid #CFE2FF;">
                   <button 
                        class="btn view-invoice-btn" 
                        style="
                            color: white;
                             background-color: #8A8A8A;
                            transition: all 0.3s ease;
                            border-radius: 5px;

                            padding: 8px 15px;
                        "
                        {{-- onmouseover="this.style.backgroundColor='#1c5cb8'; this.style.transform='translateY(-2px)';" --}}
                        {{-- onmouseout="this.style.backgroundColor='#2B6ED4'; this.style.transform='translateY(0)';" --}}
                        data-bs-toggle="modal" 
                        data-bs-target="#invoiceModal{{ $reservation->id }}"
                    >
                        <i class="fas fa-file-invoice mr-2 " ></i> Voir le reçu 
                    </button>
                </div>
            </div>

            <!-- Inclusion du modal de facture -->
            @include('admin.apps.reservations.modal-facture', ['reservation' => $reservation])
        @endforeach
    @endif
</div>

<style>
    /* Styles pour les cartes de réservation */
    .reservation-card {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid #b7bac0;
        overflow: hidden;
    }
    /* .card-header h4 {
    margin-bottom: 10px;
    padding-bottom: 10px;
    border-bottom: 2px solid #CFE2FF;
      width: 100%;
              border-radius: 10px;

} */
 .container h4 {
    margin-bottom: 10px;
    padding-bottom: 10px;
    position: relative;
}

.container h4:after {
    content: "";
    position: absolute;
    bottom: 0;
    left: -15px;
    right: -15px;
    height: 1px;
    border:1px;
    background-color: #E0E0E0;
    width:450%;
}
  
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #CFE2FF;
    }
    
    .status-badge .badge {
        font-size: 0.9rem;
        padding: 0.5rem 0.75rem;
        border-radius: 5px;
    }
    
    .training-thumbnail {
        transition: transform 0.2s ease;
        border-radius: 4px;
    }
    

    .no-image-placeholder {
        border: 1px solid #dee2e6;
        color: #adb5bd;
    }
    
    .table th {
        font-weight: 600;
        color: #2B6ED4;
    }

    .view-invoice-btn:focus {
        box-shadow: 0 0 0 0.25rem rgba(43, 110, 212, 0.25);
    }
</style>

@endsection

@section('scripts')
<script>
    // Gestion du téléchargement de facture via l'API
    document.querySelectorAll('.download-button').forEach(button => {
        button.addEventListener('click', function() {
            const reservationId = this.getAttribute('data-reservation-id');
            // Redirection vers l'endpoint de téléchargement de facture
            window.location.href = `/api/reservations/${reservationId}/invoice`;
        });
    });
    
    // // Script du panier (conservé de l'original)
    // (function() {
    //     function initCartBadge() {
    //         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
            
    //         const fixedBadge = document.getElementById('fixed-cart-badge');
    //         if (fixedBadge) {
    //             fixedBadge.textContent = cartCount.toString();
    //             if (cartCount > 0) {
    //                 fixedBadge.style.display = 'flex';
    //             } else {
    //                 fixedBadge.style.display = 'none';
    //             }
    //         }
            
    //         synchronizeWithServer();
    //     }
        
    //     function synchronizeWithServer() {
    //         const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    //         if (!csrfToken) return;
            
    //         fetch('/panier/count', {
    //             method: 'GET',
    //             headers: {
    //                 'Accept': 'application/json',
    //                 'X-CSRF-TOKEN': csrfToken
    //             }
    //         })
    //         .then(response => response.json())
    //         .then(data => {
    //             const count = data.count || 0;
    //             localStorage.setItem('cartCount', count.toString());
                
    //             const fixedBadge = document.getElementById('fixed-cart-badge');
    //             if (fixedBadge) {
    //                 fixedBadge.textContent = count.toString();
    //                 fixedBadge.style.display = count > 0 ? 'flex' : 'none';
    //             }
    //         })
    //         .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
    //     }
        
    //     initCartBadge();
        
    //     if (document.readyState === 'loading') {
    //         document.addEventListener('DOMContentLoaded', initCartBadge);
    //     }
        
    //     window.updateCartBadgeCount = function(count) {
    //         localStorage.setItem('cartCount', count.toString());
    //         const fixedBadge = document.getElementById('fixed-cart-badge');
    //         if (fixedBadge) {
    //             fixedBadge.textContent = count.toString();
    //             fixedBadge.style.display = count > 0 ? 'flex' : 'none';
    //         }
    //     };
    // })();
</script>

<script>
    <script src="{{ asset('assets/js/MonJs/formations/panier.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/cart.js') }}"></script>
  



</script>
@endsection