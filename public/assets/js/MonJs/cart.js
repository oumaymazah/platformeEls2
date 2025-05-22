// // //Gestion du panier d'achat - Permet aux utilisateurs d'ajouter et de supprimer des formations
// // Synchronisation client-serveur - Maintient la cohérence entre le stockage local et la base de données côté serveur
// // Prévention des actions multiples - Empêche les utilisateurs de cliquer plusieurs fois rapidement sur les boutons d'action
// // Mise à jour visuelle de l'interface - Actualise les boutons et les compteurs en fonction de l'état du panier
// // Notifications utilisateur - Informe l'utilisateur des actions réussies ou des erreurs via des notifications toast



// //code2222
// document.addEventListener('DOMContentLoaded', function() {
//     // Récupération du token CSRF pour les requêtes AJAX
//     const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
//     // Variable pour suivre les requêtes en cours
//     let pendingRequests = {};
//     // Création d'une instance de toast personnalisée
//     window.toast = new ToastNotification({
//         duration: 2000,  // Durée de 3 secondes
//         position: 'top-end',  // Position en haut à droite
//     });
//     // Fonction pour stocker les IDs des formations dans le panier
//     function storeCartFormationsInLocalStorage(formationIds) {
//         localStorage.setItem('cartFormations', JSON.stringify(formationIds));
//     }
//     // Initialisation du compteur de panier
//     initializeCartCounter();
//     // Ajout des écouteurs d'événements pour les boutons d'action
//     setupEventListeners();
    
//     /**
//      * Initialise le compteur de panier depuis localStorage ou via API
//      */
//     function initializeCartCounter() {
//         // Vérifier s'il y a une valeur en localStorage
//         let cartCount = localStorage.getItem('cartCount');
        
//         if (cartCount !== null) {
//             updateCartBadge(parseInt(cartCount, 10));
//         } else {
//             // Si pas de valeur en localStorage, récupérer depuis l'API
//             fetchCartCount();
//         }
//     }
//     /**
//      * Récupère le nombre d'articles dans le panier via API
//      */
//     function fetchCartCount() {
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
//             updateCartBadge(count);
//         })
//         .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
//     }
    
//     /**
//      * Met à jour le badge du compteur de panier dans l'interface
//      * @param {number} count - Le nombre d'articles dans le panier
//      */
//     function updateCartBadge(count) {
//         const cartBadge = document.querySelector('.cart-badge');
//         if (cartBadge) {
//             cartBadge.textContent = count;
//             cartBadge.style.display = count > 0 ? 'block' : 'none';
//         }
//     } 
   

//     function setupEventListeners() {
//         document.addEventListener('click', function(event) {
//             const addToCartBtn = event.target.closest('.addcart-btn .btn[href="/panier"]');
//             if (addToCartBtn) {
//                 event.preventDefault();
                
//                 // Empêcher les clics multiples rapides
//                 if (addToCartBtn.classList.contains('processing')) {
//                     return;
//                 }
                
//                 // Si le bouton est désactivé (formation complète), ne rien faire
//                 if (addToCartBtn.disabled) {
//                     return;
//                 }
                
//                 // Vérifier si le bouton a déjà l'attribut data-in-cart
//                 if (addToCartBtn.getAttribute('data-in-cart') === 'true') {
//                     // Si le produit est déjà dans le panier, rediriger vers la page panier
//                     window.location.href = '/panier';
//                     return;
//                 }
//                 // Marquer le bouton comme étant en cours de traitement
//                 addToCartBtn.classList.add('processing');
                
//                 // Rechercher l'ID de formation
//                 let formationId;
//                 const modalContent = addToCartBtn.closest('.modal-content');
//                 if (modalContent) {
//                     // Si le bouton est dans un modal
//                     formationId = modalContent.closest('.modal').id.split('-').pop();
//                 } else {
//                     // Si le bouton est sur une carte de formation
//                     const formationCard = addToCartBtn.closest('.formation-item, .product-box');
//                     if (formationCard) {
//                         formationId = formationCard.getAttribute('data-category-id');
                        
//                         // Alternative si data-category-id n'est pas trouvé
//                         if (!formationId && formationCard.hasAttribute('data-formation-id')) {
//                             formationId = formationCard.getAttribute('data-formation-id');
//                         }
//                     }
//                 }
                
//                 if (formationId) {
//                     // Éviter les appels multiples pour le même ID
//                     if (pendingRequests[formationId]) {
//                         return;
//                     }
                    
//                     pendingRequests[formationId] = true;
//                     addToCart(formationId, false, function() {
//                         // Callback pour réinitialiser l'état après le traitement
//                         addToCartBtn.classList.remove('processing');
//                         delete pendingRequests[formationId];
//                     });
//                 } else {
//                     addToCartBtn.classList.remove('processing');
//                 }
//             }
//         });
        
//         // Pour les boutons "Supprimer" dans le panier
//         document.querySelectorAll('.remove-link').forEach(button => {
//             button.addEventListener('click', function(e) {
//                 e.preventDefault();
                
//                 // Empêcher les clics multiples rapides
//                 if (this.classList.contains('processing')) {
//                     return;
//                 }
                
//                 this.classList.add('processing');
//                 const formationId = this.getAttribute('data-formation-id');
                
//                 if (pendingRequests[formationId]) {
//                     this.classList.remove('processing');
//                     return;
//                 }
                
//                 pendingRequests[formationId] = true;
//                 removeFromCart(formationId, () => {
//                     this.classList.remove('processing');
//                     delete pendingRequests[formationId];
//                 });
//             });
//         });
        
//         // Vérifier si les formations sont dans le panier au chargement de la page, mais une seule fois
//         if (!window.formationsChecked) {
//             window.formationsChecked = true;
//             checkFormationsInCart();
//         }
//     }
//     /**
//      * Ajoute une formation au panier
//      * @param {string|number} formationId - L'ID de la formation à ajouter
//      * @param {boolean} redirectToCart - Si true, redirige vers la page panier après l'ajout
//      * @param {function} callback - Fonction appelée une fois l'opération terminée
//      */
//     function addToCart(formationId, redirectToCart = false, callback = null) {
//         // Vérifier d'abord si cette formation est déjà dans le localStorage
//         const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
//         if (cartFormations.includes(formationId.toString())) {
//             showNotification('Cette formation est déjà dans votre panier', 'info');
//             if (callback) callback();
//             return;
//         }
        
//         localStorage.setItem('lastAddedFormation', formationId);
//         updateAddToCartButton(formationId, true); // Mettre à jour le bouton immédiatement pour le feedback visuel
        
//         fetch('/panier/ajouter', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'Accept': 'application/json',
//                 'X-CSRF-TOKEN': csrfToken
//             },
//             body: JSON.stringify({
//                 training_id: formationId
//             })
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.success) {
//                 // Mise à jour du compteur
//                 localStorage.setItem('cartCount', data.cartCount.toString());
//                 updateCartBadge(data.cartCount);
                
//                 // Ajouter la formation au localStorage seulement si elle n'y est pas déjà
//                 if (!cartFormations.includes(formationId.toString())) {
//                     cartFormations.push(formationId.toString());
//                     storeCartFormationsInLocalStorage(cartFormations);
//                     showNotification(data.message, 'success');
//                 }
//                 updateAddToCartButton(formationId, true);
                
//                 if (redirectToCart) {
//                     window.location.href = '/panier';
//                 }
//             } else {
//                 localStorage.removeItem('lastAddedFormation');
//                 updateAddToCartButton(formationId, false); // Réinitialiser le bouton
//                 showNotification(data.message, 'error');
//             }
            
//             if (callback) callback();
//         })
//         .catch(error => {
//             console.error('Erreur lors de l\'ajout au panier:', error);
//             localStorage.removeItem('lastAddedFormation');
//             updateAddToCartButton(formationId, false); // Réinitialiser le bouton
//             showNotification('Une erreur est survenue lors de l\'ajout au panier.', 'error');
            
//             if (callback) callback();
//         });
//     }
//     function removeFromCart(formationId, callback = null) {
//         // Log de débogage
//         console.log("Tentative de suppression de la formation:", formationId);
        
//         fetch('/panier/supprimer', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//                 'Accept': 'application/json'
//             },
//             body: JSON.stringify({
//                 formation_id: formationId
//             })
//         })
//         .then(response => {
//             if (!response.ok) {
//                 console.error("Erreur de réponse:", response.status);
//                 throw new Error('Erreur réseau: ' + response.status);
//             }
//             return response.json();
//         })
//         .then(data => {
//             console.log("Réponse de suppression:", data);
            
//             if (data.success) {
//                 // Mise à jour du localStorage
//                 const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
//                 const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
//                 localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
                
//                 // Mise à jour du compteur global
//                 localStorage.setItem('cartCount', data.cartCount.toString());
//                 updateCartCount(data.cartCount);
                
//                 // Suppression visuelle de l'élément
//                 const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
//                 if (formationItem) {
//                     formationItem.remove();
//                     updateUIAfterRemoval(data);
//                 }
                
//                 showNotification(data.message || 'Formation supprimée du panier', 'success');
//             } else {
//                 showNotification(data.message || 'Erreur lors de la suppression de la formation', 'error');
//             }
            
//             if (callback) callback();
//         })
//         .catch(error => {
//             console.error('Erreur complète:', error);
//             showNotification('Une erreur est survenue lors de la suppression de la formation', 'error');
            
//             if (callback) callback();
//         });
//     }
//     function checkFormationsInCart() {
//         // Récupérer les formations déjà connues du localStorage
//         const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
        
//         // Vérifier d'abord la dernière formation ajoutée (pour une mise à jour rapide lors du retour arrière)
//         const lastAddedFormation = localStorage.getItem('lastAddedFormation');
//         if (lastAddedFormation) {
//             console.log("Formation précédemment ajoutée:", lastAddedFormation);
//             // Désactiver immédiatement le bouton pour la dernière formation ajoutée
//             updateAddToCartButton(lastAddedFormation, true, false);
            
//             // Ajouter cette formation aux formations du panier si elle n'y est pas déjà
//             if (!cartFormations.includes(lastAddedFormation)) {
//                 cartFormations.push(lastAddedFormation);
//                 storeCartFormationsInLocalStorage(cartFormations);
//             }
            
//             // Supprimer cette information maintenant qu'elle a été traitée
//             localStorage.removeItem('lastAddedFormation');
//         }
        
//         // Mettre à jour tous les boutons pour les formations connues du localStorage
//         cartFormations.forEach(formationId => {
//             // Vérifiez si la formation est complète (à faire selon votre logique)
//             const isComplete = false; // Par défaut, supposons qu'elle n'est pas complète
//             updateAddToCartButton(formationId, true, isComplete);
//         });
        
//         // Et plus bas dans la même fonction:
//         $('.formation-item, .product-box').each(function() {
//             const formationItem = $(this);
//             const formationId = formationItem.attr('data-category-id') || formationItem.attr('data-formation-id');
//             const inCart = formationId && cartFormations.includes(formationId.toString());
            
//             // Vérification si formation complète
//             let isComplete = false;
//             const seatsInfo = formationItem.find('.badge-light-success, .badge-light-danger');
//             if (seatsInfo.length) {
//                 const seatsText = seatsInfo.text().trim();
//                 const matches = seatsText.match(/(\d+)\/(\d+)/);
//                 if (matches && matches.length === 3) {
//                     const occupiedSeats = parseInt(matches[1], 10);
//                     const totalSeats = parseInt(matches[2], 10);
//                     isComplete = occupiedSeats >= totalSeats;
//                 }
//             }
            
//             // Mettre à jour le bouton en passant le paramètre isComplete
//             if (formationId) {
//                 updateAddToCartButton(formationId, inCart, isComplete);
//             }
//         });
        
//         // Ensuite, synchroniser avec le serveur
//         fetch('/panier/items', {
//             method: 'GET',
//             headers: {
//                 'Accept': 'application/json',
//                 'X-CSRF-TOKEN': csrfToken
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             if (data.items && Array.isArray(data.items)) {
//                 // Créer un nouvel array des IDs de formations
//                 const serverFormationIds = data.items.map(item => item.toString());
                
//                 // Mettre à jour le localStorage avec les données du serveur (plus fiables)
//                 storeCartFormationsInLocalStorage(serverFormationIds);
                
//                 // Mettre à jour tous les boutons
//                 // Pour chaque formation, on vérifie si elle est complète
//                 $('.formation-item, .product-box').each(function() {
//                     const formationItem = $(this);
//                     const formationId = formationItem.attr('data-category-id') || formationItem.attr('data-formation-id');
                    
//                     if (formationId) {
//                         const inCart = serverFormationIds.includes(formationId.toString());
                        
//                         // Vérifier si la formation est complète
//                         let isComplete = false;
//                         const seatsInfo = formationItem.find('.badge-light-success, .badge-light-danger');
//                         if (seatsInfo.length) {
//                             const seatsText = seatsInfo.text().trim();
//                             const matches = seatsText.match(/(\d+)\/(\d+)/);
//                             if (matches && matches.length === 3) {
//                                 const occupiedSeats = parseInt(matches[1], 10);
//                                 const totalSeats = parseInt(matches[2], 10);
//                                 isComplete = occupiedSeats >= totalSeats;
//                             }
//                         }
                        
//                         updateAddToCartButton(formationId, inCart, isComplete);
//                     }
//                 });
                
//                 // Mettre à jour le compteur du panier
//                 if (serverFormationIds.length !== parseInt(localStorage.getItem('cartCount') || '0', 10)) {
//                     localStorage.setItem('cartCount', serverFormationIds.length.toString());
//                     updateCartBadge(serverFormationIds.length);
//                 }
//             }
//         })
//         .catch(error => console.error('Erreur lors de la vérification du panier:', error));
//     }
    
//     /**
//      * Affiche une notification à l'utilisateur
//      * @param {string} message - Le message à afficher
//      * @param {string} type - Le type de notification (success, error, info, warning)
//      */
//     function showNotification(message, type = 'info') {
//         // Utiliser la classe ToastNotification
//         if (typeof toast !== 'undefined' && toast.show) {
//             toast.show(message, type);
//             return;
//         }
        
//         // Fallback au cas où toast n'est pas chargé
//         if (type === 'error') {
//             alert('Erreur: ' + message);
//         } else {
//             alert(message);
//         }
//     }

// });
// // window.removeFromCart = removeFromCart;


// //Gestion du panier d'achat - Permet aux utilisateurs d'ajouter et de supprimer des formations
// Synchronisation client-serveur - Maintient la cohérence entre le stockage local et la base de données côté serveur
// Prévention des actions multiples - Empêche les utilisateurs de cliquer plusieurs fois rapidement sur les boutons d'action
// Mise à jour visuelle de l'interface - Actualise les boutons et les compteurs en fonction de l'état du panier
// Notifications utilisateur - Informe l'utilisateur des actions réussies ou des erreurs via des notifications toast



//code2222
document.addEventListener('DOMContentLoaded', function() {
    // Récupération du token CSRF pour les requêtes AJAX
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // Variable pour suivre les requêtes en cours
    let pendingRequests = {};
    // Création d'une instance de toast personnalisée
    window.toast = new ToastNotification({
        duration: 2000,  // Durée de 3 secondes
        position: 'top-end',  // Position en haut à droite
    });
    // Fonction pour stocker les IDs des formations dans le panier
    function storeCartFormationsInLocalStorage(formationIds) {
        localStorage.setItem('cartFormations', JSON.stringify(formationIds));
    }
    // Initialisation du compteur de panier
    initializeCartCounter();
    // Ajout des écouteurs d'événements pour les boutons d'action
    setupEventListeners();
    
    /**
     * Initialise le compteur de panier depuis localStorage ou via API
     */
    // function initializeCartCounter() {
    //     // Vérifier s'il y a une valeur en localStorage
    //     let cartCount = localStorage.getItem('cartCount');
        
    //     if (cartCount !== null) {
    //         updateCartBadge(parseInt(cartCount, 10));
    //     } else {
    //         // Si pas de valeur en localStorage, récupérer depuis l'API
    //         fetchCartCount();
    //     }
    // }
    /**
     * Récupère le nombre d'articles dans le panier via API
     */
    function fetchCartCount() {
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
            updateCartBadge(count);
        })
        .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
    }
    
    
/**
 * @param {number} count - Le nombre d'articles dans le panier
 */
function updateCartBadge(count) {
    // 1. Sélecteurs étendus pour trouver tous les badges possibles
    const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge');
    
    if (count <= 0) {
        // Masquer tous les badges si le panier est vide
        badges.forEach(badge => {
            badge.style.display = 'none';
        });
        return;
    }
    
    // 2. Mettre à jour tous les badges existants
    if (badges.length > 0) {
        badges.forEach(badge => {
            badge.textContent = count.toString();
            badge.style.display = 'flex';
            badge.style.opacity = '1';
        });
    } 
    
    // 3. Créer de nouveaux badges pour les icônes qui n'en ont pas encore
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
        if (!container) return;
        
        // Vérifier si ce conteneur a déjà un badge
        if (container.querySelector('.cart-badge, .custom-violet-badge')) return;
        
        // Créer un nouveau badge
        const badge = document.createElement('span');
        badge.className = 'cart-badge custom-violet-badge';
        badge.textContent = count.toString();
        
        // S'assurer que le conteneur est positionné correctement
        if (getComputedStyle(container).position === 'static') {
            container.style.position = 'relative';
        }
        
        container.appendChild(badge);
    });
}

// Modification de la fonction initializeCartCounter pour un chargement immédiat
function initializeCartCounter() {
    // Récupérer directement depuis localStorage pour un affichage instantané
    const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    
    // Mettre à jour l'interface immédiatement
    if (cartCount > 0) {
        updateCartBadge(cartCount);
    }
    
    // Ensuite vérifier avec le serveur pour synchroniser
    fetchCartCount();
}

// S'assurer que ce code s'exécute le plus tôt possible
function setupEarlyCartBadges() {
    // Injecter le style immédiatement
    const style = document.createElement('style');
    style.innerHTML = `
        .cart-badge, .custom-violet-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #2563EB;
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
    
    // Initialiser immédiatement le compteur du panier
    initializeCartCounter();
}

// Exécuter immédiatement
setupEarlyCartBadges();

// Continuer à exécuter au chargement du DOM pour les éléments qui peuvent être ajoutés plus tard
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCartCounter);
}

// Exécuter également au chargement complet de la page
window.addEventListener('load', initializeCartCounter);

    function setupEventListeners() {
        document.addEventListener('click', function(event) {
            const addToCartBtn = event.target.closest('.addcart-btn .btn[href="/panier"]');
            if (addToCartBtn) {
                event.preventDefault();
                
                // Empêcher les clics multiples rapides
                if (addToCartBtn.classList.contains('processing')) {
                    return;
                }
                
                // Si le bouton est désactivé (formation complète), ne rien faire
                if (addToCartBtn.disabled) {
                    return;
                }
                
                // Vérifier si le bouton a déjà l'attribut data-in-cart
                if (addToCartBtn.getAttribute('data-in-cart') === 'true') {
                    // Si le produit est déjà dans le panier, rediriger vers la page panier
                    window.location.href = '/panier';
                    return;
                }
                // Marquer le bouton comme étant en cours de traitement
                addToCartBtn.classList.add('processing');
                
                // Rechercher l'ID de formation
                let formationId;
                const modalContent = addToCartBtn.closest('.modal-content');
                if (modalContent) {
                    // Si le bouton est dans un modal
                    formationId = modalContent.closest('.modal').id.split('-').pop();
                } else {
                    // Si le bouton est sur une carte de formation
                    const formationCard = addToCartBtn.closest('.formation-item, .product-box');
                    if (formationCard) {
                        formationId = formationCard.getAttribute('data-category-id');
                        
                        // Alternative si data-category-id n'est pas trouvé
                        if (!formationId && formationCard.hasAttribute('data-formation-id')) {
                            formationId = formationCard.getAttribute('data-formation-id');
                        }
                    }
                }
                
                if (formationId) {
                    // Éviter les appels multiples pour le même ID
                    if (pendingRequests[formationId]) {
                        return;
                    }
                    
                    pendingRequests[formationId] = true;
                    addToCart(formationId, false, function() {
                        // Callback pour réinitialiser l'état après le traitement
                        addToCartBtn.classList.remove('processing');
                        delete pendingRequests[formationId];
                    });
                } else {
                    addToCartBtn.classList.remove('processing');
                }
            }
        });
        
        // Pour les boutons "Supprimer" dans le panier
        document.querySelectorAll('.remove-link').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Empêcher les clics multiples rapides
                if (this.classList.contains('processing')) {
                    return;
                }
                
                this.classList.add('processing');
                const formationId = this.getAttribute('data-formation-id');
                
                if (pendingRequests[formationId]) {
                    this.classList.remove('processing');
                    return;
                }
                
                pendingRequests[formationId] = true;
                removeFromCart(formationId, () => {
                    this.classList.remove('processing');
                    delete pendingRequests[formationId];
                });
            });
        });
        
        // Vérifier si les formations sont dans le panier au chargement de la page, mais une seule fois
        if (!window.formationsChecked) {
            window.formationsChecked = true;
            checkFormationsInCart();
        }
    }
    /**
     * Ajoute une formation au panier
     * @param {string|number} formationId - L'ID de la formation à ajouter
     * @param {boolean} redirectToCart - Si true, redirige vers la page panier après l'ajout
     * @param {function} callback - Fonction appelée une fois l'opération terminée
     */
    // function addToCart(formationId, redirectToCart = false, callback = null) {
    //     // Vérifier d'abord si cette formation est déjà dans le localStorage
    //     const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    //     if (cartFormations.includes(formationId.toString())) {
    //         showNotification('Cette formation est déjà dans votre panier', 'info');
    //         if (callback) callback();
    //         return;
    //     }
        
    //     localStorage.setItem('lastAddedFormation', formationId);
    //     updateAddToCartButton(formationId, true); // Mettre à jour le bouton immédiatement pour le feedback visuel
        
    //     fetch('/panier/ajouter', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'Accept': 'application/json',
    //             'X-CSRF-TOKEN': csrfToken
    //         },
    //         body: JSON.stringify({
    //             training_id: formationId
    //         })
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.success) {
    //             // Mise à jour du compteur
    //             localStorage.setItem('cartCount', data.cartCount.toString());
    //             updateCartBadge(data.cartCount);
                
    //             // Ajouter la formation au localStorage seulement si elle n'y est pas déjà
    //             if (!cartFormations.includes(formationId.toString())) {
    //                 cartFormations.push(formationId.toString());
    //                 storeCartFormationsInLocalStorage(cartFormations);
    //                 showNotification(data.message, 'success');
    //             }
    //             updateAddToCartButton(formationId, true);
                
    //             if (redirectToCart) {
    //                 window.location.href = '/panier';
    //             }
    //         } else {
    //             localStorage.removeItem('lastAddedFormation');
    //             updateAddToCartButton(formationId, false); // Réinitialiser le bouton
    //             showNotification(data.message, 'error');
    //         }
            
    //         if (callback) callback();
    //     })
    //     .catch(error => {
    //         console.error('Erreur lors de l\'ajout au panier:', error);
    //         localStorage.removeItem('lastAddedFormation');
    //         updateAddToCartButton(formationId, false); // Réinitialiser le bouton
    //         showNotification('Une erreur est survenue lors de l\'ajout au panier.', 'error');
            
    //         if (callback) callback();
    //     });
    // }
    function addToCart(formationId, redirectToCart = false, callback = null) {
    // Vérifier d'abord si cette formation est déjà dans le localStorage
    const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    if (cartFormations.includes(formationId.toString())) {
        showNotification('Cette formation est déjà dans votre panier', 'info');
        if (callback) callback();
        return;
    }
    
    // AMÉLIORATION : Mise à jour immédiate du badge et du compteur
    const currentCartCount = parseInt(localStorage.getItem('cartCount') || '0');
    const newCartCount = currentCartCount + 1;
    
    // Mise à jour immédiate du localStorage
    localStorage.setItem('cartCount', newCartCount.toString());
    
    // Mise à jour immédiate du badge
    const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
    badges.forEach(badge => {
        badge.textContent = newCartCount.toString();
        badge.style.visibility = 'visible';
        badge.style.opacity = '1';
    });
    
    // Créer immédiatement des badges pour les icônes sans badge
    const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
    cartIcons.forEach(icon => {
        const container = icon.closest('a, div, button, .cart-container');
        if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
            const badge = document.createElement('span');
            badge.className = 'cart-badge custom-violet-badge';
            badge.textContent = newCartCount.toString();
            badge.style.position = 'absolute';
            badge.style.top = '-8px';
            badge.style.right = '-8px';
            badge.style.visibility = 'visible';
            badge.style.opacity = '1';
            
            if (getComputedStyle(container).position === 'static') {
                container.style.position = 'relative';
            }
            
            container.appendChild(badge);
        }
    });
    
    // Mise à jour visuelle du bouton immédiatement
    updateAddToCartButton(formationId, true);
    
    localStorage.setItem('lastAddedFormation', formationId);
    
    fetch('/panier/ajouter', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            training_id: formationId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Synchronisation avec la réponse du serveur
            localStorage.setItem('cartCount', data.cartCount.toString());
            
            // Mise à jour de tous les badges avec le compteur du serveur
            const serverBadges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
            serverBadges.forEach(badge => {
                badge.textContent = data.cartCount.toString();
                badge.style.visibility = data.cartCount > 0 ? 'visible' : 'hidden';
                badge.style.opacity = data.cartCount > 0 ? '1' : '0';
            });
            
            // Ajouter la formation au localStorage seulement si elle n'y est pas déjà
            if (!cartFormations.includes(formationId.toString())) {
                cartFormations.push(formationId.toString());
                localStorage.setItem('cartFormations', JSON.stringify(cartFormations));
                showNotification(data.message, 'success');
            }
            
            updateAddToCartButton(formationId, true);
            
            if (redirectToCart) {
                window.location.href = '/panier';
            }
        } else {
            // En cas d'échec, réinitialiser
            localStorage.setItem('cartCount', currentCartCount.toString());
            
            // Réinitialiser les badges
            const resetBadges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
            resetBadges.forEach(badge => {
                badge.textContent = currentCartCount.toString();
                badge.style.visibility = currentCartCount > 0 ? 'visible' : 'hidden';
                badge.style.opacity = currentCartCount > 0 ? '1' : '0';
            });
            
            updateAddToCartButton(formationId, false);
            showNotification(data.message, 'error');
        }
        
        if (callback) callback();
    })
    .catch(error => {
        console.error('Erreur lors de l\'ajout au panier:', error);
        
        // Réinitialiser en cas d'erreur
        localStorage.setItem('cartCount', currentCartCount.toString());
        
        // Réinitialiser les badges
        const resetBadges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
        resetBadges.forEach(badge => {
            badge.textContent = currentCartCount.toString();
            badge.style.visibility = currentCartCount > 0 ? 'visible' : 'hidden';
            badge.style.opacity = currentCartCount > 0 ? '1' : '0';
        });
        
        updateAddToCartButton(formationId, false);
        showNotification('Une erreur est survenue lors de l\'ajout au panier.', 'error');
        
        if (callback) callback();
    });
}
    // function removeFromCart(formationId, callback = null) {
    //     // Log de débogage
    //     console.log("Tentative de suppression de la formation:", formationId);
        
    //     fetch('/panier/supprimer', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    //             'Accept': 'application/json'
    //         },
    //         body: JSON.stringify({
    //             formation_id: formationId
    //         })
    //     })
    //     .then(response => {
    //         if (!response.ok) {
    //             console.error("Erreur de réponse:", response.status);
    //             throw new Error('Erreur réseau: ' + response.status);
    //         }
    //         return response.json();
    //     })
    //     .then(data => {
    //         console.log("Réponse de suppression:", data);
            
    //         if (data.success) {
    //             // Mise à jour du localStorage
    //             const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    //             const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
    //             localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
                
    //             // Mise à jour du compteur global
    //             localStorage.setItem('cartCount', data.cartCount.toString());
    //             updateCartCount(data.cartCount);
                
    //             // Suppression visuelle de l'élément
    //             const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
    //             if (formationItem) {
    //                 formationItem.remove();
    //                 updateUIAfterRemoval(data);
    //             }
                
    //             // showNotification(data.message || 'Formation supprimée du panier', 'success');
    //         } else {
    //             showNotification(data.message || 'Erreur lors de la suppression de la formation', 'error');
    //         }
            
    //         if (callback) callback();
    //     })
    //     .catch(error => {
    //         console.error('Erreur complète:', error);
    //         showNotification('Une erreur est survenue lors de la suppression de la formation', 'error');
            
    //         if (callback) callback();
    //     });
    // }
    function checkFormationsInCart() {
        // Récupérer les formations déjà connues du localStorage
        const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
        
        // Vérifier d'abord la dernière formation ajoutée (pour une mise à jour rapide lors du retour arrière)
        const lastAddedFormation = localStorage.getItem('lastAddedFormation');
        if (lastAddedFormation) {
            console.log("Formation précédemment ajoutée:", lastAddedFormation);
            // Désactiver immédiatement le bouton pour la dernière formation ajoutée
            updateAddToCartButton(lastAddedFormation, true, false);
            
            // Ajouter cette formation aux formations du panier si elle n'y est pas déjà
            if (!cartFormations.includes(lastAddedFormation)) {
                cartFormations.push(lastAddedFormation);
                storeCartFormationsInLocalStorage(cartFormations);
            }
            
            // Supprimer cette information maintenant qu'elle a été traitée
            localStorage.removeItem('lastAddedFormation');
        }
        
        // Mettre à jour tous les boutons pour les formations connues du localStorage
        cartFormations.forEach(formationId => {
            // Vérifiez si la formation est complète (à faire selon votre logique)
            const isComplete = false; // Par défaut, supposons qu'elle n'est pas complète
            updateAddToCartButton(formationId, true, isComplete);
        });
        
        // Et plus bas dans la même fonction:
        $('.formation-item, .product-box').each(function() {
            const formationItem = $(this);
            const formationId = formationItem.attr('data-category-id') || formationItem.attr('data-formation-id');
            const inCart = formationId && cartFormations.includes(formationId.toString());
            
            // Vérification si formation complète
            let isComplete = false;
            const seatsInfo = formationItem.find('.badge-light-success, .badge-light-danger');
            if (seatsInfo.length) {
                const seatsText = seatsInfo.text().trim();
                const matches = seatsText.match(/(\d+)\/(\d+)/);
                if (matches && matches.length === 3) {
                    const occupiedSeats = parseInt(matches[1], 10);
                    const totalSeats = parseInt(matches[2], 10);
                    isComplete = occupiedSeats >= totalSeats;
                }
            }
            
            // Mettre à jour le bouton en passant le paramètre isComplete
            if (formationId) {
                updateAddToCartButton(formationId, inCart, isComplete);
            }
        });
        
        // Ensuite, synchroniser avec le serveur
        fetch('/panier/items', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.items && Array.isArray(data.items)) {
                // Créer un nouvel array des IDs de formations
                const serverFormationIds = data.items.map(item => item.toString());
                
                // Mettre à jour le localStorage avec les données du serveur (plus fiables)
                storeCartFormationsInLocalStorage(serverFormationIds);
                
                // Mettre à jour tous les boutons
                // Pour chaque formation, on vérifie si elle est complète
                $('.formation-item, .product-box').each(function() {
                    const formationItem = $(this);
                    const formationId = formationItem.attr('data-category-id') || formationItem.attr('data-formation-id');
                    
                    if (formationId) {
                        const inCart = serverFormationIds.includes(formationId.toString());
                        
                        // Vérifier si la formation est complète
                        let isComplete = false;
                        const seatsInfo = formationItem.find('.badge-light-success, .badge-light-danger');
                        if (seatsInfo.length) {
                            const seatsText = seatsInfo.text().trim();
                            const matches = seatsText.match(/(\d+)\/(\d+)/);
                            if (matches && matches.length === 3) {
                                const occupiedSeats = parseInt(matches[1], 10);
                                const totalSeats = parseInt(matches[2], 10);
                                isComplete = occupiedSeats >= totalSeats;
                            }
                        }
                        
                        updateAddToCartButton(formationId, inCart, isComplete);
                    }
                });
                
                // Mettre à jour le compteur du panier
                if (serverFormationIds.length !== parseInt(localStorage.getItem('cartCount') || '0', 10)) {
                    localStorage.setItem('cartCount', serverFormationIds.length.toString());
                    updateCartBadge(serverFormationIds.length);
                }
            }
        })
        .catch(error => console.error('Erreur lors de la vérification du panier:', error));
    }
    
    /**
     * Affiche une notification à l'utilisateur
     * @param {string} message - Le message à afficher
     * @param {string} type - Le type de notification (success, error, info, warning)
     */
    function showNotification(message, type = 'info') {
        // Utiliser la classe ToastNotification
        if (typeof toast !== 'undefined' && toast.show) {
            toast.show(message, type);
            return;
        }
        
        // Fallback au cas où toast n'est pas chargé
        if (type === 'error') {
            alert('Erreur: ' + message);
        } else {
            alert(message);
        }
    }

});