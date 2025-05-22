// pour partager l'état des formations expirées avec le système de réservation
function checkFormationsDates() {
    // Ajouter un flag pour éviter les vérifications simultanées
    if (window.checkingFormationDatesInProgress) {
        return Promise.resolve(false);
    }
    
    window.checkingFormationDatesInProgress = true;
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token non trouvé');
        window.checkingFormationDatesInProgress = false;
        return Promise.resolve(false);
    }
    
    console.log('Vérification des dates des formations en cours...');
    
    // Récupérer les détails du panier
    const baseUrl = window.location.origin;
    const url = `${baseUrl}/panier/details`;
    
    return fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Erreur de réponse:', response.status, errorText);
            throw new Error(`Erreur de réponse du serveur: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Réponse reçue pour les détails du panier:', data);
        
        if (!data.success || !data.trainings || data.trainings.length === 0) {
            console.log('Aucune formation dans le panier');
            removeExpiredFormationsWarning();
            
            // AJOUT: Indiquer qu'il n'y a pas de formations expirées
            window.hasExpiredFormationsInCart = false;
            updateReserveButtonState();
            return false;
        }
        
        let hasExpiredFormations = false;
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Comparer seulement les dates sans l'heure
        
        // Réinitialiser d'abord toutes les formations
        document.querySelectorAll('.formation-expired').forEach(item => {
            item.classList.remove('formation-expired');
        });
        document.querySelectorAll('.formation-expired-badge').forEach(badge => {
            badge.remove();
        });
        
        // Parcourir toutes les formations dans le panier
        data.trainings.forEach(formation => {
            const startDate = new Date(formation.start_date);
            const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);
            
            if (formationElement) {
                // Trouver l'élément pour placer le badge
                const formationTitle = formationElement.querySelector('.formation-title') || 
                                     formationElement.querySelector('h4') || 
                                     formationElement.querySelector('h3');
                
                if (startDate < today) {
                    // La date de formation est dépassée
                    console.log(`Formation ${formation.id} a une date dépassée: ${formation.start_date}`);
                    hasExpiredFormations = true;
                    
                    const statusBadge = document.createElement('span');
                    statusBadge.className = 'formation-status-badge formation-expired-badge ml-2';
                    statusBadge.classList.add('badge', 'badge-secondary');
                    statusBadge.textContent = 'Date dépassée';
                    
                    // Style amélioré pour le badge
                    statusBadge.style.fontWeight = 'bold';
                    statusBadge.style.fontSize = '0.9rem';
                    statusBadge.style.padding = '0.3rem 0.6rem';
                    
                    formationElement.classList.add('formation-expired');
                    
                    if (formationTitle) {
                        formationTitle.appendChild(statusBadge);
                    } else {
                        formationElement.insertAdjacentElement('afterbegin', statusBadge);
                    }
                }
            }
        });

        // AJOUT: Stocker l'état global des formations expirées
        window.hasExpiredFormationsInCart = hasExpiredFormations;

        // Ajouter ou supprimer l'avertissement selon le statut
        if (hasExpiredFormations) {
            showExpiredFormationsWarning();
            
            // MODIFICATION: Appeler la fonction updateReserveButtonState
            updateReserveButtonState();
        } else {
            removeExpiredFormationsWarning();
            
            // MODIFICATION: Appeler la fonction updateReserveButtonState
            updateReserveButtonState();
        }
        
        return hasExpiredFormations;
    })
    .catch(error => {
        console.error('Erreur lors de la vérification des dates des formations:', error);
        return false;
    })
    .finally(() => {
        window.checkingFormationDatesInProgress = false;
    });
}

function updateReserveButtonState() {
    // Récupérer l'état des formations directement depuis le DOM pour être sûr d'avoir l'état actuel
    const expiredFormationElements = document.querySelectorAll('.formation-expired');
    const completeFormationElements = document.querySelectorAll('.formation-full');
    
    // Utiliser le DOM direct plutôt que les variables globales qui peuvent être en retard
    const hasExpiredFormations = expiredFormationElements.length > 0;
    const hasCompleteFormations = completeFormationElements.length > 0;
    
    // Mettre à jour les variables globales pour la cohérence
    window.hasExpiredFormationsInCart = hasExpiredFormations;
    window.hasCompleteFormationsInCart = hasCompleteFormations;
    
    // Récupérer le bouton de réservation (uniquement s'il n'y a pas de réservation existante)
    const reserverButton = document.querySelector('.reserver-button');
    if (reserverButton && !window.hasExistingReservation) {
        if (hasExpiredFormations) {
            // Désactiver le bouton si des formations sont expirées
            reserverButton.disabled = true;
            reserverButton.classList.add('disabled');
            reserverButton.title = 'Votre panier contient des formations dont la date est dépassée';
        } else if (hasCompleteFormations) {
            // Désactiver le bouton si des formations sont complètes
            reserverButton.disabled = true;
            reserverButton.classList.add('disabled');
            reserverButton.title = 'Une ou plusieurs formations sont complètes';
        } else {
            // Activer le bouton si tout est OK
            reserverButton.disabled = false;
            reserverButton.classList.remove('disabled');
            reserverButton.removeAttribute('title');
        }
    }
}

// AJOUT: Fonction pour observer les changements du panier et mettre à jour le bouton en temps réel
function setupCartChangeObserver() {
    // Observer les changements dans le contenu du panier
    const panierContent = document.querySelector('.panier-content');
    if (!panierContent) return;
    
    const cartObserver = new MutationObserver(function(mutations) {
        // Attendre un court délai pour que toutes les modifications du DOM soient terminées
        setTimeout(() => {
            // Vérifier l'état actuel directement depuis le DOM
            const expiredFormationElements = document.querySelectorAll('.formation-expired');
            const completeFormationElements = document.querySelectorAll('.formation-full');
            
            const hasExpiredFormations = expiredFormationElements.length > 0;
            const hasCompleteFormations = completeFormationElements.length > 0;
            
            // Mettre à jour les variables globales
            window.hasExpiredFormationsInCart = hasExpiredFormations;
            window.hasCompleteFormationsInCart = hasCompleteFormations;
            
            // Mettre à jour l'état du bouton
            updateReserveButtonState();
        }, 50); // Un délai court pour éviter l'effet de clignotement
    });
    
    // Observer les suppressions/ajouts d'éléments dans le panier
    cartObserver.observe(panierContent, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['class']
    });
}

// Initialiser l'observateur au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(setupCartChangeObserver, 1000);
});
// AJOUT: Nouvelle fonction pour mettre à jour l'état du bouton de réservation
// function updateReserveButtonState() {
//     // Récupérer l'état des formations expirées
//     const hasExpiredFormations = window.hasExpiredFormationsInCart || false;
    
//     // Récupérer l'état des formations complètes
//     const hasCompleteFormations = window.hasCompleteFormationsInCart || document.querySelectorAll('.formation-full').length > 0;
    
//     // Récupérer le bouton de réservation (uniquement s'il n'y a pas de réservation existante)
//     const reserverButton = document.querySelector('.reserver-button');
//     if (reserverButton && !window.hasExistingReservation) {
//         if (hasExpiredFormations) {
//             // Désactiver le bouton si des formations sont expirées
//             reserverButton.disabled = true;
//             reserverButton.classList.add('disabled');
//             reserverButton.title = 'Votre panier contient des formations dont la date est dépassée';
//         } else if (hasCompleteFormations) {
//             // Désactiver le bouton si des formations sont complètes
//             reserverButton.disabled = true;
//             reserverButton.classList.add('disabled');
//             reserverButton.title = 'Une ou plusieurs formations sont complètes';
//         } else {
//             // Activer le bouton si tout est OK
//             reserverButton.disabled = false;
//             reserverButton.classList.remove('disabled');
//             reserverButton.removeAttribute('title');
//         }
//     }
// }
// AJOUT: Exposer la nouvelle fonction globalement
window.updateReserveButtonState = updateReserveButtonState;
window.hasExpiredFormationsInCart = false; // Initialiser la variable globale

function showExpiredFormationsWarning() {
    // Vérifier si l'avertissement existe déjà
    let existingWarning = document.querySelector('.expired-formations-warning');
    if (existingWarning) return;

    // Créer le conteneur d'avertissement
    const warningContainer = document.createElement('div');
    warningContainer.className = 'expired-formations-warning';
    warningContainer.style.animation = 'fadeIn 0.5s';
    warningContainer.innerHTML = `
        <i class="fas fa-calendar-times mr-2"></i>
        <strong>Attention:</strong> Votre panier contient des formations dont la date est dépassée. Veuillez les supprimer pour poursuivre votre réservation.
    `;

    // Insérer l'avertissement au bon endroit
    const completeWarning = document.querySelector('.complete-formations-warning');
    const greenHeader = document.querySelector('.panier-header');
    
    if (completeWarning) {
        completeWarning.parentNode.insertBefore(warningContainer, completeWarning.nextSibling);
    } else if (greenHeader) {
        greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
    } else {
        const panierContent = document.querySelector('.panier-content');
        const container = document.querySelector('.container');

        if (panierContent) {
            panierContent.insertBefore(warningContainer, panierContent.firstChild);
        } else if (container) {
            container.insertBefore(warningContainer, container.firstChild);
        }
    }
    
    // Faire défiler vers l'avertissement
    warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeExpiredFormationsWarning() {
    const warning = document.querySelector('.expired-formations-warning');
    if (warning) {
        warning.remove();
    }
}

// Ajouter des styles pour les badges et l'avertissement
const expiredStyleElement = document.createElement('style');
expiredStyleElement.textContent = `
    .expired-formations-warning {
        width: 100%;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        text-align: center;
        justify-content: center;
        padding: 1rem;
        background-color: #e9ecef;
        color: #495057;
        border: 1px solid #ced4da;
        border-radius: 4px;
        animation: fadeIn 0.5s;
    }

    .badge-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .formation-expired {
        background-color: #f8f9fa;
        opacity: 0.8;
    }
`;
document.head.appendChild(expiredStyleElement);

// Exécuter la vérification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Attendre un peu pour être sûr que tout est chargé
    setTimeout(checkFormationsDates, 800);
});

// Vérifier périodiquement (toutes les 2 minutes)
setInterval(checkFormationsDates, 2 * 60 * 1000);

// Exposer les fonctions globalement
window.checkFormationsDates = checkFormationsDates;
window.showExpiredFormationsWarning = showExpiredFormationsWarning;
window.removeExpiredFormationsWarning = removeExpiredFormationsWarning;