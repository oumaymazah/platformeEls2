// (function() {
//     // Fonction pour initialiser immédiatement le badge du panier
//     function initBadgeImmediately() {
//         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
        
//         // Mettre à jour tous les badges fixes existants
//         const fixedBadges = document.querySelectorAll('#fixed-cart-badge, .custom-violet-badge, .cart-badge');
//         fixedBadges.forEach(badge => {
//             badge.textContent = cartCount.toString();
//             badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
//             badge.style.opacity = cartCount > 0 ? '1' : '0';
//         });
        
//         // Créer des badges pour toutes les icônes de panier si nécessaire
//         if (cartCount > 0) {
//             const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
//             cartIcons.forEach(icon => {
//                 const container = icon.closest('a, div, button, .cart-container');
//                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                     createBadgeForContainer(container, cartCount);
//                 }
//             });
//         }
//     }
//     // Exécuter immédiatement pour l'affichage le plus rapide possible
//     initBadgeImmediately();
//     // Observer le DOM pour les icônes ajoutées dynamiquement
//     if (typeof MutationObserver !== 'undefined') {
//         const observer = new MutationObserver(mutations => {
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             if (cartCount <= 0) return;
            
//             mutations.forEach(mutation => {
//                 if (mutation.addedNodes.length) {
//                     mutation.addedNodes.forEach(node => {
//                         if (node.nodeType === 1) {
//                             const icons = node.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
//                             icons.forEach(icon => {
//                                 const container = icon.closest('a, div, button, .cart-container');
//                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                                     createBadgeForContainer(container, cartCount);
//                                 }
//                             });
                            
//                             if (node.matches && node.matches('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg')) {
//                                 const container = node.closest('a, div, button, .cart-container');
//                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                                     createBadgeForContainer(container, cartCount);
//                                 }
//                             }
//                         }
//                     });
//                 }
//             });
//         });
        
//         observer.observe(document.documentElement, {
//             childList: true,
//             subtree: true
//         });
//     }
//     // Injecter les styles nécessaires immédiatement
//     const badgeElement = document.createElement('style');
//     badgeElement.textContent = `
//         .cart-badge, .custom-violet-badge {
//             position: absolute;
//             top: -8px;
//             right: -8px;
//             background-color: #2563EB;
//             color: white;
//             border-radius: 50%;
//             width: 18px;
//             height: 18px;
//             font-size: 12px;
//             display: flex;
//             align-items: center;
//             justify-content: center;
//             font-weight: bold;
//             z-index: 10;
//             visibility: hidden;
//             opacity: 0;
//         }
//     `;
//     document.head.appendChild(badgeElement);
// })();
// function createBadgeForContainer(container, count) {
//     if (!container) return;
    
//     if (container.querySelector('.cart-badge, .custom-violet-badge')) {
//         const existingBadge = container.querySelector('.cart-badge, .custom-violet-badge');
//         existingBadge.textContent = count.toString();
//         existingBadge.style.visibility = count > 0 ? 'visible' : 'hidden';
//         existingBadge.style.opacity = count > 0 ? '1' : '0';
//         return;
//     }
    
//     let badge = document.createElement('span');
//     badge.className = 'cart-badge custom-violet-badge';
//     badge.textContent = count.toString();
//     badge.style.visibility = count > 0 ? 'visible' : 'hidden';
//     badge.style.opacity = count > 0 ? '1' : '0';
    
//     if (getComputedStyle(container).position === 'static') {
//         container.style.position = 'relative';
//     }
    
//     container.appendChild(badge);
// }
// (function() {
//     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
//     window.hasExistingReservation = hasExistingReservation;
    
//     const storedCount = parseInt(localStorage.getItem('cartCount') || '0');
    
//     if (storedCount > 0) {
//         updateAllBadges(storedCount);
//     } else {
//         showEmptyCartMessage();
//     }
    
//     if (hasExistingReservation) {
//         const reservationId = localStorage.getItem('reservationId');
//         if (reservationId && typeof transformReserverButton === 'function') {
//             transformReserverButton(parseInt(reservationId));
//         }
//     }
    
//     synchronizeWithServer();
    
//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', initializeListeners);
//     } else {
//         initializeListeners();
//     }
// })();

// function synchronizeWithServer() {
//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) return;
    
//     fetch('/panier/count', {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'X-Requested-With': 'XMLHttpRequest'
//         }
//     })
//     .then(response => response.json())
//     .then(data => {
//         const count = data.count || 0;
//         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
        
//         if (oldCount !== count) {
//             localStorage.setItem('cartCount', count.toString());
//             updateAllBadges(count);
//         }
//     })
//     .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
// }
// function updateAllBadges(count) {
//     const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
//     badges.forEach(badge => {
//         badge.textContent = count.toString();
//         badge.style.visibility = count > 0 ? 'visible' : 'hidden';
//         badge.style.opacity = count > 0 ? '1' : '0';
//     });
    
//     if (count > 0) {
//         const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
//         cartIcons.forEach(icon => {
//             const container = icon.closest('a, div, button, .cart-container');
//             if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                 createBadgeForContainer(container, count);
//             }
//         });
//     }
// }
// function initializeListeners() {
//     setupRemoveFromCartListeners();
    
//     // CORRECTION : Vérifier l'état du panier au chargement initial
//     const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//     if (cartCount === 0 && window.location.pathname.includes('/panier')) {
//         showEmptyCartMessage();
//     }
    
//     setInterval(refreshCartBadgeWithoutReload, 5000);
//     setInterval(verifyCartItemsExistence, 120000);
    
//     document.addEventListener('visibilitychange', function() {
//         if (!document.hidden) {
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             updateAllBadges(cartCount);
//             refreshCartBadgeWithoutReload();
//             verifyCartItemsExistence();
            
//             // CORRECTION : Vérifier l'état du panier quand l'utilisateur revient sur la page
//             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
//                 showEmptyCartMessage();
//             }
//         }
//     });
    
//     const oldXHROpen = window.XMLHttpRequest.prototype.open;
//     window.XMLHttpRequest.prototype.open = function() {
//         this.addEventListener('load', function() {
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             updateAllBadges(cartCount);
            
//             // CORRECTION : Vérifier l'état du panier après chaque requête XHR
//             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
//                 showEmptyCartMessage();
//             }
//         });
//         return oldXHROpen.apply(this, arguments);
//     };
// }
// function setupRemoveFromCartListeners() {
//     document.addEventListener('click', function(e) {
//         const removeLink = e.target.closest('.remove-link');
//         if (removeLink) {
//             e.preventDefault();
//             const formationId = removeLink.getAttribute('data-formation-id');
//             if (formationId) {
//                 removeFromCart(formationId);
//             }
//         }
//     });
// }
// function refreshCartBadgeWithoutReload() {
//     fetch('/panier/items-count', {
//         method: 'GET',
//         headers: {
//             'X-Requested-With': 'XMLHttpRequest',
//             'Accept': 'application/json'
//         }
//     })
//     .then(handleResponse)
//     .then(data => {
//         const count = parseInt(data.count) || 0;
//         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
//         if (oldCount !== count) {
//             localStorage.setItem('cartCount', count.toString());
//             updateAllBadges(count);
//         }
        
//         const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
//         if (count > 0 && !hasExistingReservation) {
//             /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
//                 createReserverButtonEarly();
//             } */
//         } else if (count === 0 && !hasExistingReservation) {
//             document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
//                 btn.remove();
//             });
//         } else if (hasExistingReservation) {
//             const reservationId = localStorage.getItem('reservationId');
//             if (reservationId && typeof transformReserverButton === 'function') {
//                 transformReserverButton(parseInt(reservationId));
//             }
//         }
//     })
//     .catch(error => console.error('Erreur:', error));
// }
// function updateCartCount(count) {
//     count = parseInt(count) || 0;
//     window.globalCartCount = count;
//     localStorage.setItem('cartCount', count.toString());
//     updateAllBadges(count);
    
//     var panierCountElements = document.querySelectorAll('.panier-count');
//     panierCountElements.forEach(function(el) {
//         el.textContent = count + ' formation(s)';
//         el.style.opacity = '1';
//     });
    
//     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
//     if (count > 0 && !hasExistingReservation) {
//         /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
//             createReserverButtonEarly();
//         } */
//     } else if (count === 0 && !hasExistingReservation) {
//         document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
//             btn.remove();
//         });
//     } else if (hasExistingReservation) {
//         const reservationId = localStorage.getItem('reservationId');
//         if (reservationId && typeof transformReserverButton === 'function') {
//             transformReserverButton(parseInt(reservationId));
//         }
//     }
// }

// function removeFromCart(formationId) {
//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) {
//         console.error('CSRF token non trouvé');
//         return;
//     }
    
//     const baseUrl = window.location.origin;
    
//     fetch(`${baseUrl}/panier/supprimer`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'Accept': 'application/json'
//         },
//         body: JSON.stringify({
//             formation_id: formationId
//         })
//     })
//     .then(handleResponse)
//     .then(response => {
//         // Vérifier si la réponse est valide avant de continuer
//         if (!response || !response.success) {
//             console.error(response?.message || 'Erreur lors de la suppression de la formation');
//             return;
//         }
        
//         const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
//         const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
//         localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
//         localStorage.setItem('cartCount', response.cartCount.toString());
//         updateCartCount(response.cartCount);
        
//         const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
//         if (formationItem) {
//             // Vérifier si la formation supprimée était complète
//             const wasCompleteFormation = formationItem.classList.contains('formation-full');
            
//             formationItem.remove();
//             updateUIAfterRemoval(response);
            
//             // Si la formation supprimée était complète, vérifier immédiatement s'il reste des formations complètes
//             if (wasCompleteFormation) {
//                 checkRemainingCompleteFormations();
//             }
//         }
        
//         if (response.cartCount === 0) {
//             showEmptyCartMessage();
//             removeCompleteFormationsWarning(); // Supprimer l'alerte immédiatement si le panier est vide
//         }
//     })
//     .catch(error => {
//         console.error('Erreur lors de la suppression:', error);
//     });
// }
// function updateUIAfterRemoval(response) {
//     // Ajout de logs pour debugging
//     console.log("updateUIAfterRemoval appelé avec:", response);
//     console.log("Nouveau cartCount:", response.cartCount);
    
//     // Mettre à jour le compteur du panier
//     updateCartCount(response.cartCount);
    
//     // Vérifier si le panier est maintenant vide
//     if (response.cartCount === 0) {
//         console.log("Panier détecté comme vide, affichage du message");
        
//         // Nettoyer les éléments existants du panier
//         const panierContent = document.querySelector('.panier-content');
//         if (panierContent) {
//             panierContent.remove();
//             console.log("Contenu du panier supprimé");
//         }
        
//         // Afficher le message de panier vide
//         showEmptyCartMessage();
        
//         // Cacher tous les badges du panier
//         document.querySelectorAll('.cart-badge, .custom-violet-badge').forEach(badge => {
//             badge.style.visibility = 'hidden';
//             badge.style.opacity = '0';
//             console.log("Badge caché");
//         });
        
//         // Supprimer le bouton de réservation s'il existe
//         const reserveButton = document.querySelector('.reserver-button');
//         if (reserveButton) {
//             reserveButton.remove();
//             console.log("Bouton de réservation supprimé");
//         }
        
//         // Supprimer l'alerte de formations complètes
//         removeCompleteFormationsWarning();
//     } else {
//         // Si le panier n'est pas vide, mettre à jour le résumé
//         console.log("Panier non vide, mise à jour du résumé");
//         updateCartSummary(response);
        
//         // Vérifier s'il reste des formations complètes
//         checkRemainingCompleteFormations();
//     }
// }

// function showEmptyCartMessage() {
//     // Ajout de logs pour déboguer
//     console.log("Exécution de showEmptyCartMessage()");
    
//     // Récupérer les éléments existants
//     const panierContent = document.querySelector('.panier-content');
//     const container = document.querySelector('.container');
    
//     // Log pour vérifier si les éléments sont trouvés
//     console.log("panierContent trouvé:", !!panierContent);
//     console.log("container trouvé:", !!container);
    
//     // Supprimer le contenu du panier s'il existe
//     if (panierContent) {
//         panierContent.remove();
//         console.log("panierContent supprimé");
//     }
    
//     // Vérifier si le message de panier vide existe déjà
//     const existingEmptyCart = document.querySelector('.empty-cart');
//     if (existingEmptyCart) {
//         console.log("Message 'panier vide' déjà présent");
//         return; // Ne pas créer de doublon
//     }
    
//     // Créer le HTML pour le message de panier vide
//     const emptyCartHTML = `
//         <div class="empty-cart">
//             <i class="fas fa-shopping-cart"></i>
//             <p>Votre panier est vide</p>
//             <a href="formation/formations">Découvrir des formations</a>
//         </div>
//     `;
    
//     // Ajouter le message au container
//     if (container) {
//         container.innerHTML += emptyCartHTML;
//         console.log("Message 'panier vide' ajouté au container");
//     } else {
//         console.error("Container non trouvé pour ajouter le message 'panier vide'");
//     }
    
//     // Supprimer l'alerte de formations complètes
//     removeCompleteFormationsWarning();
// }

// function updateCartSummary(response) {
//     if (response.cartCount === 0) {
//         const existingButton = document.querySelector('.reserver-button');
//         if (existingButton) {
//             existingButton.remove();
//         }
//         removeCompleteFormationsWarning();
//         return;
//     }
    
//     const totalPriceElement = document.querySelector('.total-price');
//     if (totalPriceElement) {
//         totalPriceElement.textContent = response.totalPrice + ' DT';
//     }
    
//     if (response.hasDiscount && parseFloat(response.discountedItemsOriginalPrice) > 0) {
//         let originalPriceElement = document.querySelector('.original-price');
//         let discountElement = document.querySelector('.discount-percentage');
        
//         if (originalPriceElement) {
//             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
//         } else if (totalPriceElement) {
//             originalPriceElement = document.createElement('div');
//             originalPriceElement.className = 'original-price';
//             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
//             totalPriceElement.insertAdjacentElement('afterend', originalPriceElement);
//         }
        
//         if (discountElement) {
//             discountElement.textContent = response.discountPercentage + '% ';
//         } else if (originalPriceElement) {
//             discountElement = document.createElement('div');
//             discountElement.className = 'discount-percentage';
//             discountElement.textContent = response.discountPercentage + '% ';
//             originalPriceElement.insertAdjacentElement('afterend', discountElement);
//         }
//     } else {
//         const originalPrice = document.querySelector('.original-price');
//         const discountPercentage = document.querySelector('.discount-percentage');
//         if (originalPrice) originalPrice.remove();
//         if (discountPercentage) discountPercentage.remove();
//     }
// }
// function verifyCartItemsExistence() {
//     const cartItems = JSON.parse(localStorage.getItem('cartFormations') || '[]');
//     if (cartItems.length === 0) return;
    
//     fetch('/panier/details', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//             'Accept': 'application/json',
//             'X-Requested-With': 'XMLHttpRequest'
//         },
//         body: JSON.stringify({
//             items: cartItems
//         })
//     })
//     .then(handleResponse)
//     .then(response => {
//         if (response.removed_items && response.removed_items.length > 0) {
//             const validItems = cartItems.filter(itemId => 
//                 !response.removed_items.includes(itemId.toString()) && 
//                 !response.removed_items.includes(parseInt(itemId))
//             );
//             localStorage.setItem('cartFormations', JSON.stringify(validItems));
//             updateCartCount(response.cartCount);
            
//             if (window.location.pathname.includes('/panier')) {
//                 response.removed_items.forEach(itemId => {
//                     const formationItem = document.querySelector(`.formation-item[data-formation-id="${itemId}"]`);
//                     if (formationItem) {
//                         formationItem.remove();
//                     }
//                 });
                
//                 if (response.cartCount === 0) {
//                     showEmptyCartMessage();
//                 } else {
//                     updateCartSummary(response);
//                 }
//             }
            
//             // Vérifier s'il reste des formations complètes après suppression
//             checkRemainingCompleteFormations();
//         }
//     })
//     .catch(error => console.error('Erreur lors de la vérification des articles du panier:', error));
// }
// function checkRemainingCompleteFormations() {
//     const remainingCompleteFormations = document.querySelectorAll('.formation-full');
    
//     if (remainingCompleteFormations.length === 0) {
//         // Plus aucune formation complète, supprimer l'avertissement immédiatement
//         removeCompleteFormationsWarning();
        
//         // Réactiver le bouton de réservation s'il existe
//         const reserverButton = document.querySelector('.reserver-button');
//         if (reserverButton) {
//             reserverButton.disabled = false;
//             reserverButton.classList.remove('disabled');
//             reserverButton.removeAttribute('title');
//         }
        
//         // Si le système de réservation est initialisé, on met à jour le statut global
//         if (window.hasCompleteFormationsInCart !== undefined) {
//             window.hasCompleteFormationsInCart = false;
//         }
//     } else {
//         // S'il reste des formations complètes, s'assurer que l'avertissement est affiché
//         showCompleteFormationsWarning();
        
//         // Désactiver le bouton de réservation
//         const reserverButton = document.querySelector('.reserver-button');
//         if (reserverButton) {
//             reserverButton.disabled = true;
//             reserverButton.classList.add('disabled');
//             reserverButton.title = 'Une ou plusieurs formations sont complètes';
//         }
        
//         // Si le système de réservation est initialisé, on met à jour le statut global
//         if (window.hasCompleteFormationsInCart !== undefined) {
//             window.hasCompleteFormationsInCart = true;
//         }
//     }
// }
// function checkFormationsAvailability() {
//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) {
//         console.error('CSRF token non trouvé');
//         return;
//     }
    
//     console.log('Vérification des disponibilités en cours...');
    
//     const baseUrl = window.location.origin;
//     const url = `${baseUrl}/panier/check-availability`;
    
//     fetch(url, {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'X-Requested-With': 'XMLHttpRequest'
//         },
//         credentials: 'same-origin'
//     })
//     .then(async response => {
//         if (!response.ok) {
//             const errorText = await response.text();
//             console.error('Erreur de réponse:', response.status, errorText);
//             throw new Error(`Erreur de réponse du serveur: ${response.status}`);
//         }
//         return response.json();
//     })
//     .then(data => {
//         console.log('Réponse reçue de check-availability:', data);
        
//         let hasCompleteFormations = false;
        
//         // Réinitialiser d'abord toutes les formations
//         document.querySelectorAll('.formation-full').forEach(item => {
//             item.classList.remove('formation-full');
//         });
//         document.querySelectorAll('.formation-status-badge').forEach(badge => {
//             badge.remove();
//         });
        
//         if (data.success && data.formations && data.formations.length > 0) {
//             data.formations.forEach(formation => {
//                 const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);
                
//                 if (formationElement) {
//                     // Trouver l'élément pour placer le badge
//                     const formationTitle = formationElement.querySelector('.formation-title') || 
//                                          formationElement.querySelector('h4') || 
//                                          formationElement.querySelector('h3');
                    
//                     if (formation.is_full || formation.has_pending_reservation) {
//                         const statusBadge = document.createElement('span');
//                         statusBadge.className = 'formation-status-badge ml-2';
                        
//                         if (formation.is_full) {
//                             console.log(`Formation ${formation.id} est COMPLÈTE`);
//                             hasCompleteFormations = true;
//                             statusBadge.classList.add('badge', 'badge-danger');
//                             statusBadge.textContent = 'Complète';
//                             formationElement.classList.add('formation-full');
                            
//                             // Style amélioré pour le badge
//                             statusBadge.style.fontWeight = 'bold';
//                             statusBadge.style.fontSize = '0.9rem';
//                             statusBadge.style.padding = '0.3rem 0.6rem';
                            
//                             if (formationTitle) {
//                                 formationTitle.appendChild(statusBadge);
//                             } else {
//                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
//                             }
//                         } else if (formation.has_pending_reservation) {
//                             statusBadge.classList.add('badge', 'badge-warning');
//                             statusBadge.textContent = 'Réservation en attente';
                            
//                             if (formationTitle) {
//                                 formationTitle.appendChild(statusBadge);
//                             } else {
//                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
//                             }
//                         }
//                     }
//                 }
//             });

//             // Ajouter ou supprimer l'avertissement selon le statut
//             if (hasCompleteFormations) {
//                 showCompleteFormationsWarning();
                
//                 // Désactiver le bouton de réservation si présent
//                 const reserverButton = document.querySelector('.reserver-button');
//                 if (reserverButton) {
//                     reserverButton.disabled = true;
//                     reserverButton.classList.att('disabled');
//                     reserverButton.title = 'Une ou plusieurs formations sont complètes';
//                 }
//             } else {
//                 removeCompleteFormationsWarning();
                
//                 // Réactiver le bouton de réservation si présent
//                 const reserverButton = document.querySelector('.reserver-button');
//                 if (reserverButton) {
//                     reserverButton.disabled = false;
//                     reserverButton.classList.remove('disabled');
//                     reserverButton.removeAttribute('title');
//                 }
//             }
//         } else {
//             removeCompleteFormationsWarning();
//         }
//     })
//     .catch(error => {
//         console.error('Erreur lors de la vérification de la disponibilité:', error);
//     });
// }

// function handleResponse(response) {
//     if (!response.ok) {
//         throw new Error('Erreur réseau: ' + response.status);
//     }
//     return response.json();
// }

// function showCompleteFormationsWarning() {
//     // Vérifier si l'avertissement existe déjà
//     let existingWarning = document.querySelector('.complete-formations-warning');
//     if (existingWarning) return;

//     // Créer le conteneur d'avertissement avec une animation
//     const warningContainer = document.createElement('div');
//     warningContainer.className = 'complete-formations-warning';
//     warningContainer.style.animation = 'fadeIn 0.5s';
//     warningContainer.innerHTML = `
//         <i class="fas fa-exclamation-triangle mr-2"></i>
//         <strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.
//     `;

//     // Insérer l'avertissement au bon endroit
//     const greenHeader = document.querySelector('.panier-header');
//     if (greenHeader) {
//         greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
//     } else {
//         const panierContent = document.querySelector('.panier-content');
//         const container = document.querySelector('.container');

//         if (panierContent) {
//             panierContent.insertBefore(warningContainer, panierContent.firstChild);
//         } else if (container) {
//             container.insertBefore(warningContainer, container.firstChild);
//         }
//     }
    
//     // Faire défiler vers l'avertissement
//     warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
// }

// function removeCompleteFormationsWarning() {
//     const warning = document.querySelector('.complete-formations-warning');
//     if (warning) {
//         warning.remove();
//     }
// }

// // Ajouter des styles pour les badges et l'avertissement
// const styleElement = document.createElement('style');
// styleElement.textContent = `
//     @keyframes fadeIn {
//         from { opacity: 0; }
//         to { opacity: 1; }
//     }

//     .complete-formations-warning {
//         width: 100%;
//         margin-bottom: 1rem;
//         display: flex;
//         align-items: center;
//         text-align: center;
//         justify-content: center;
//         padding: 1rem;
//         background-color: #f8d7da;
//         color: #721c24;
//         border: 1px solid #f5c6cb;
//         border-radius: 4px;
//         animation: fadeIn 0.5s;
//     }

//     .formation-status-badge {
//         display: inline-block;
//         padding: 0.25rem 0.5rem;
//         font-size: 0.75rem;
//         font-weight: 600;
//         border-radius: 0.25rem;
//         margin-left: 0.5rem;
//     }

//     .badge-danger {
//         background-color: #dc3545;
//         color: white;
//     }

//     .badge-warning {
//         background-color: #ffc107;
//         color: #212529;
//     }
    
//     .formation-full {
//         background-color: #fff8f8;
//     }
// `;
// document.head.appendChild(styleElement);

// // Exécuter la vérification au chargement de la page
// document.addEventListener('DOMContentLoaded', function() {
//     // Attendez un peu pour être sûr que tout est chargé
//     setTimeout(checkFormationsAvailability, 500);
// });

// // Vérifier périodiquement (toutes les 2 minutes)
// setInterval(checkFormationsAvailability, 2 * 60 * 1000);

// // Fonctions globales pour d'autres interactions du panier
// window.removeFromCart = removeFromCart;
// window.updateCartSummary = updateCartSummary;
// window.forceUpdateCartBadge = updateCartCount;
// window.refreshCartBadge = refreshCartBadgeWithoutReload;
// window.fetchCartItemsCount = refreshCartBadgeWithoutReload;
// window.updateCartCount = updateCartCount;
// window.verifyCartItemsExistence = verifyCartItemsExistence;
// window.checkRemainingCompleteFormations = checkRemainingCompleteFormations;
// window.checkFormationsAvailability = checkFormationsAvailability;

// // Ajouter à l'initialisation des listeners
// function enhanceInitializeListeners() {
//     const originalInitializeListeners = window.initializeListeners || function() {};
    
//     window.initializeListeners = function() {
//         originalInitializeListeners();
        
//         // Vérifier les dates des formations au chargement
//         if (typeof window.checkFormationsDates === 'function') {
//             window.checkFormationsDates();
//         }
        
//         // Ajouter la vérification des dates après chaque action sur le panier
//         document.addEventListener('visibilitychange', function() {
//             if (!document.hidden) {
//                 if (typeof window.checkFormationsDates === 'function') {
//                     window.checkFormationsDates();
//                 }
//             }
//         });
//     };
    
//     // Si la page est déjà chargée, exécuter immédiatement
//     if (document.readyState !== 'loading') {
//         if (typeof window.checkFormationsDates === 'function') {
//             window.checkFormationsDates();
//         }
//     }
// }

// // Améliorer la fonction removeFromCart pour vérifier les dates après suppression
// function enhanceRemoveFromCart() {
//     const originalUpdateUIAfterRemoval = window.updateUIAfterRemoval || function() {};
    
//     window.updateUIAfterRemoval = function(response) {
//         originalUpdateUIAfterRemoval(response);
        
//         // Vérifier s'il reste des formations expirées après suppression
//         if (response.cartCount > 0) {
//             if (typeof window.checkFormationsDates === 'function') {
//                 window.checkFormationsDates();
//             }
//         } else {
//             // Si le panier est vide, supprimer tous les avertissements
//             if (typeof window.removeExpiredFormationsWarning === 'function') {
//                 window.removeExpiredFormationsWarning();
//             }
//         }
//     };
// }

// // Améliorer les fonctions existantes
// enhanceInitializeListeners();
// enhanceRemoveFromCart();

// (function() {
//     // Fonction pour initialiser immédiatement le badge du panier
//     function initBadgeImmediately() {
//         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
        
//         // Mettre à jour tous les badges fixes existants
//         const fixedBadges = document.querySelectorAll('#fixed-cart-badge, .custom-violet-badge, .cart-badge');
//         fixedBadges.forEach(badge => {
//             badge.textContent = cartCount.toString();
//             badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
//             badge.style.opacity = cartCount > 0 ? '1' : '0';
//         });
        
//         // Créer des badges pour toutes les icônes de panier si nécessaire
//         if (cartCount > 0) {
//             const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
//             cartIcons.forEach(icon => {
//                 const container = icon.closest('a, div, button, .cart-container');
//                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                     createBadgeForContainer(container, cartCount);
//                 }
//             });
//         }
//     }
//     // Exécuter immédiatement pour l'affichage le plus rapide possible
//     initBadgeImmediately();
//     // Observer le DOM pour les icônes ajoutées dynamiquement
//     if (typeof MutationObserver !== 'undefined') {
//         const observer = new MutationObserver(mutations => {
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             if (cartCount <= 0) return;
            
//             mutations.forEach(mutation => {
//                 if (mutation.addedNodes.length) {
//                     mutation.addedNodes.forEach(node => {
//                         if (node.nodeType === 1) {
//                             const icons = node.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
//                             icons.forEach(icon => {
//                                 const container = icon.closest('a, div, button, .cart-container');
//                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                                     createBadgeForContainer(container, cartCount);
//                                 }
//                             });
                            
//                             if (node.matches && node.matches('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg')) {
//                                 const container = node.closest('a, div, button, .cart-container');
//                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                                     createBadgeForContainer(container, cartCount);
//                                 }
//                             }
//                         }
//                     });
//                 }
//             });
//         });
        
//         observer.observe(document.documentElement, {
//             childList: true,
//             subtree: true
//         });
//     }
//     // Injecter les styles nécessaires immédiatement
//     const badgeElement  = document.createElement('style');
//     badgeElement .textContent = `
//         .cart-badge, .custom-violet-badge {
//             position: absolute;
//             top: -8px;
//             right: -8px;
//             background-color: #2563EB;
//             color: white;
//             border-radius: 50%;
//             width: 18px;
//             height: 18px;
//             font-size: 12px;
//             display: flex;
//             align-items: center;
//             justify-content: center;
//             font-weight: bold;
//             z-index: 10;
//             visibility: hidden;
//             opacity: 0;
//         }
//     `;
//     document.head.appendChild(badgeElement );
// })();
// function createBadgeForContainer(container, count) {
//     if (!container) return;
    
//     if (container.querySelector('.cart-badge, .custom-violet-badge')) {
//         const existingBadge = container.querySelector('.cart-badge, .custom-violet-badge');
//         existingBadge.textContent = count.toString();
//         existingBadge.style.visibility = count > 0 ? 'visible' : 'hidden';
//         existingBadge.style.opacity = count > 0 ? '1' : '0';
//         return;
//     }
    
//     let badge = document.createElement('span');
//     badge.className = 'cart-badge custom-violet-badge';
//     badge.textContent = count.toString();
//     badge.style.visibility = count > 0 ? 'visible' : 'hidden';
//     badge.style.opacity = count > 0 ? '1' : '0';
    
//     if (getComputedStyle(container).position === 'static') {
//         container.style.position = 'relative';
//     }
    
//     container.appendChild(badge);
// }
// (function() {
//     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
//     window.hasExistingReservation = hasExistingReservation;
    
//     const storedCount = parseInt(localStorage.getItem('cartCount') || '0');
    
//     if (storedCount > 0) {
//         updateAllBadges(storedCount);
//     } else {
//         showEmptyCartMessage();
//     }
    
//     if (hasExistingReservation) {
//         const reservationId = localStorage.getItem('reservationId');
//         if (reservationId && typeof transformReserverButton === 'function') {
//             transformReserverButton(parseInt(reservationId));
//         }
//     }
    
//     synchronizeWithServer();
    
//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', initializeListeners);
//     } else {
//         initializeListeners();
//     }
// })();

// function synchronizeWithServer() {
//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) return;
    
//     fetch('/panier/count', {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'X-Requested-With': 'XMLHttpRequest'
//         }
//     })
//     .then(response => response.json())
//     .then(data => {
//         const count = data.count || 0;
//         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
        
//         if (oldCount !== count) {
//             localStorage.setItem('cartCount', count.toString());
//             updateAllBadges(count);
//         }
//     })
//     .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
// }
// function updateAllBadges(count) {
//     const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
//     badges.forEach(badge => {
//         badge.textContent = count.toString();
//         badge.style.visibility = count > 0 ? 'visible' : 'hidden';
//         badge.style.opacity = count > 0 ? '1' : '0';
//     });
    
//     if (count > 0) {
//         const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
//         cartIcons.forEach(icon => {
//             const container = icon.closest('a, div, button, .cart-container');
//             if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
//                 createBadgeForContainer(container, count);
//             }
//         });
//     }
// }
// function initializeListeners() {
//     setupRemoveFromCartListeners();
    
//     // CORRECTION : Vérifier l'état du panier au chargement initial
//     const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//     if (cartCount === 0 && window.location.pathname.includes('/panier')) {
//         showEmptyCartMessage();
//     }
    
//     setInterval(refreshCartBadgeWithoutReload, 5000);
//     setInterval(verifyCartItemsExistence, 120000);
    
//     document.addEventListener('visibilitychange', function() {
//         if (!document.hidden) {
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             updateAllBadges(cartCount);
//             refreshCartBadgeWithoutReload();
//             verifyCartItemsExistence();
            
//             // CORRECTION : Vérifier l'état du panier quand l'utilisateur revient sur la page
//             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
//                 showEmptyCartMessage();
//             }
//         }
//     });
    
//     const oldXHROpen = window.XMLHttpRequest.prototype.open;
//     window.XMLHttpRequest.prototype.open = function() {
//         this.addEventListener('load', function() {
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             updateAllBadges(cartCount);
            
//             // CORRECTION : Vérifier l'état du panier après chaque requête XHR
//             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
//                 showEmptyCartMessage();
//             }
//         });
//         return oldXHROpen.apply(this, arguments);
//     };
// }
// function setupRemoveFromCartListeners() {
//     document.addEventListener('click', function(e) {
//         const removeLink = e.target.closest('.remove-link');
//         if (removeLink) {
//             e.preventDefault();
//             const formationId = removeLink.getAttribute('data-formation-id');
//             if (formationId) {
//                 removeFromCart(formationId);
//             }
//         }
//     });
// }
// function refreshCartBadgeWithoutReload() {
//     fetch('/panier/items-count', {
//         method: 'GET',
//         headers: {
//             'X-Requested-With': 'XMLHttpRequest',
//             'Accept': 'application/json'
//         }
//     })
//     .then(handleResponse)
//     .then(data => {
//         const count = parseInt(data.count) || 0;
//         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
//         if (oldCount !== count) {
//             localStorage.setItem('cartCount', count.toString());
//             updateAllBadges(count);
//         }
        
//         const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
//         if (count > 0 && !hasExistingReservation) {
//             /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
//                 createReserverButtonEarly();
//             } */
//         } else if (count === 0 && !hasExistingReservation) {
//             document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
//                 btn.remove();
//             });
//         } else if (hasExistingReservation) {
//             const reservationId = localStorage.getItem('reservationId');
//             if (reservationId && typeof transformReserverButton === 'function') {
//                 transformReserverButton(parseInt(reservationId));
//             }
//         }
//     })
//     .catch(error => console.error('Erreur:', error));
// }
// function updateCartCount(count) {
//     count = parseInt(count) || 0;
//     window.globalCartCount = count;
//     localStorage.setItem('cartCount', count.toString());
//     updateAllBadges(count);
    
//     var panierCountElements = document.querySelectorAll('.panier-count');
//     panierCountElements.forEach(function(el) {
//         el.textContent = count + ' formation(s)';
//         el.style.opacity = '1';
//     });
    
//     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
//     if (count > 0 && !hasExistingReservation) {
//         /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
//             createReserverButtonEarly();
//         } */
//     } else if (count === 0 && !hasExistingReservation) {
//         document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
//             btn.remove();
//         });
//     } else if (hasExistingReservation) {
//         const reservationId = localStorage.getItem('reservationId');
//         if (reservationId && typeof transformReserverButton === 'function') {
//             transformReserverButton(parseInt(reservationId));
//         }
//     }
// }

// function removeFromCart(formationId) {
//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) {
//         console.error('CSRF token non trouvé');
//         return;
//     }
    
//     const baseUrl = window.location.origin;
    
//     fetch(`${baseUrl}/panier/supprimer`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'Accept': 'application/json'
//         },
//         body: JSON.stringify({
//             formation_id: formationId
//         })
//     })
//     .then(handleResponse)
//     .then(response => {
//         // Vérifier si la réponse est valide avant de continuer
//         if (!response || !response.success) {
//             console.error(response?.message || 'Erreur lors de la suppression de la formation');
//             return;
//         }
        
//         const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
//         const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
//         localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
//         localStorage.setItem('cartCount', response.cartCount.toString());
//         updateCartCount(response.cartCount);
        
//         const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
//         if (formationItem) {
//             // Vérifier si la formation supprimée était complète
//             const wasCompleteFormation = formationItem.classList.contains('formation-full');
            
//             formationItem.remove();
//             updateUIAfterRemoval(response);
            
//             // Si la formation supprimée était complète, vérifier immédiatement s'il reste des formations complètes
//             if (wasCompleteFormation) {
//                 checkRemainingCompleteFormations();
//             }
//         }
        
//         if (response.cartCount === 0) {
//             showEmptyCartMessage();
//             removeCompleteFormationsWarning(); // Supprimer l'alerte immédiatement si le panier est vide
//         }
//     })
//     .catch(error => {
//         console.error('Erreur lors de la suppression:', error);
//     });
// }
// function updateUIAfterRemoval(response) {
//     // Ajout de logs pour debugging
//     console.log("updateUIAfterRemoval appelé avec:", response);
//     console.log("Nouveau cartCount:", response.cartCount);
    
//     // Mettre à jour le compteur du panier
//     updateCartCount(response.cartCount);
    
//     // Vérifier si le panier est maintenant vide
//     if (response.cartCount === 0) {
//         console.log("Panier détecté comme vide, affichage du message");
        
//         // Nettoyer les éléments existants du panier
//         const panierContent = document.querySelector('.panier-content');
//         if (panierContent) {
//             panierContent.remove();
//             console.log("Contenu du panier supprimé");
//         }
        
//         // Afficher le message de panier vide
//         showEmptyCartMessage();
        
//         // Cacher tous les badges du panier
//         document.querySelectorAll('.cart-badge, .custom-violet-badge').forEach(badge => {
//             badge.style.visibility = 'hidden';
//             badge.style.opacity = '0';
//             console.log("Badge caché");
//         });
        
//         // Supprimer le bouton de réservation s'il existe
//         const reserveButton = document.querySelector('.reserver-button');
//         if (reserveButton) {
//             reserveButton.remove();
//             console.log("Bouton de réservation supprimé");
//         }
        
//         // Supprimer l'alerte de formations complètes
//         removeCompleteFormationsWarning();
//     } else {
//         // Si le panier n'est pas vide, mettre à jour le résumé
//         console.log("Panier non vide, mise à jour du résumé");
//         updateCartSummary(response);
        
//         // Vérifier s'il reste des formations complètes
//         checkRemainingCompleteFormations();
//     }
// }

// function showEmptyCartMessage() {
//     // Ajout de logs pour déboguer
//     console.log("Exécution de showEmptyCartMessage()");
    
//     // Récupérer les éléments existants
//     const panierContent = document.querySelector('.panier-content');
//     const container = document.querySelector('.container');
    
//     // Log pour vérifier si les éléments sont trouvés
//     console.log("panierContent trouvé:", !!panierContent);
//     console.log("container trouvé:", !!container);
    
//     // Supprimer le contenu du panier s'il existe
//     if (panierContent) {
//         panierContent.remove();
//         console.log("panierContent supprimé");
//     }
    
//     // Vérifier si le message de panier vide existe déjà
//     const existingEmptyCart = document.querySelector('.empty-cart');
//     if (existingEmptyCart) {
//         console.log("Message 'panier vide' déjà présent");
//         return; // Ne pas créer de doublon
//     }
    
//     // Créer le HTML pour le message de panier vide
//     const emptyCartHTML = `
//         <div class="empty-cart">
//             <i class="fas fa-shopping-cart"></i>
//             <p>Votre panier est vide</p>
//             <a href="formation/formations">Découvrir des formations</a>
//         </div>
//     `;
    
//     // Ajouter le message au container
//     if (container) {
//         container.innerHTML += emptyCartHTML;
//         console.log("Message 'panier vide' ajouté au container");
//     } else {
//         console.error("Container non trouvé pour ajouter le message 'panier vide'");
//     }
    
//     // Supprimer l'alerte de formations complètes si présente
//     removeCompleteFormationsWarning();
// }

// function updateCartSummary(response) {
//     if (response.cartCount === 0) {
//         const existingButton = document.querySelector('.reserver-button');
//         if (existingButton) {
//             existingButton.remove();
//         }
//         removeCompleteFormationsWarning();
//         return;
//     }
    
//     const totalPriceElement = document.querySelector('.total-price');
//     if (totalPriceElement) {
//         totalPriceElement.textContent = response.totalPrice + ' DT';
//     }
    
//     if (response.hasDiscount && parseFloat(response.discountedItemsOriginalPrice) > 0) {
//         let originalPriceElement = document.querySelector('.original-price');
//         let discountElement = document.querySelector('.discount-percentage');
        
//         if (originalPriceElement) {
//             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
//         } else if (totalPriceElement) {
//             originalPriceElement = document.createElement('div');
//             originalPriceElement.className = 'original-price';
//             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
//             totalPriceElement.insertAdjacentElement('afterend', originalPriceElement);
//         }
        
//         if (discountElement) {
//             discountElement.textContent = response.discountPercentage + '% ';
//         } else if (originalPriceElement) {
//             discountElement = document.createElement('div');
//             discountElement.className = 'discount-percentage';
//             discountElement.textContent = response.discountPercentage + '% ';
//             originalPriceElement.insertAdjacentElement('afterend', discountElement);
//         }
//     } else {
//         const originalPrice = document.querySelector('.original-price');
//         const discountPercentage = document.querySelector('.discount-percentage');
//         if (originalPrice) originalPrice.remove();
//         if (discountPercentage) discountPercentage.remove();
//     }
// }
// function verifyCartItemsExistence() {
//     const cartItems = JSON.parse(localStorage.getItem('cartFormations') || '[]');
//     if (cartItems.length === 0) return;
    
//     fetch('/panier/details', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//             'Accept': 'application/json',
//             'X-Requested-With': 'XMLHttpRequest'
//         },
//         body: JSON.stringify({
//             items: cartItems
//         })
//     })
//     .then(handleResponse)
//     .then(response => {
//         if (response.removed_items && response.removed_items.length > 0) {
//             const validItems = cartItems.filter(itemId => 
//                 !response.removed_items.includes(itemId.toString()) && 
//                 !response.removed_items.includes(parseInt(itemId))
//             );
//             localStorage.setItem('cartFormations', JSON.stringify(validItems));
//             updateCartCount(response.cartCount);
            
//             if (window.location.pathname.includes('/panier')) {
//                 response.removed_items.forEach(itemId => {
//                     const formationItem = document.querySelector(`.formation-item[data-formation-id="${itemId}"]`);
//                     if (formationItem) {
//                         formationItem.remove();
//                     }
//                 });
                
//                 if (response.cartCount === 0) {
//                     showEmptyCartMessage();
//                 } else {
//                     updateCartSummary(response);
//                 }
//             }
            
//             // Vérifier s'il reste des formations complètes après suppression
//             checkRemainingCompleteFormations();
//         }
//     })
//     .catch(error => console.error('Erreur lors de la vérification des articles du panier:', error));
// }
// function checkRemainingCompleteFormations() {
//     const remainingCompleteFormations = document.querySelectorAll('.formation-full');
    
//     if (remainingCompleteFormations.length === 0) {
//         // Plus aucune formation complète, supprimer l'avertissement immédiatement
//         removeCompleteFormationsWarning();
        
//         // Réactiver le bouton de réservation s'il existe
//         const reserverButton = document.querySelector('.reserver-button');
//         if (reserverButton) {
//             reserverButton.disabled = false;
//             reserverButton.classList.remove('disabled');
//             reserverButton.removeAttribute('title');
//         }
        
//         // Si le système de réservation est initialisé, on met à jour le statut global
//         if (window.hasCompleteFormationsInCart !== undefined) {
//             window.hasCompleteFormationsInCart = false;
//         }
//     } else {
//         // S'il reste des formations complètes, s'assurer que l'avertissement est affiché
//         showCompleteFormationsWarning();
        
//         // Désactiver le bouton de réservation
//         const reserverButton = document.querySelector('.reserver-button');
//         if (reserverButton) {
//             reserverButton.disabled = true;
//             reserverButton.classList.add('disabled');
//             reserverButton.title = 'Une ou plusieurs formations sont complètes';
//         }
        
//         // Si le système de réservation est initialisé, on met à jour le statut global
//         if (window.hasCompleteFormationsInCart !== undefined) {
//             window.hasCompleteFormationsInCart = true;
//         }
//     }
// }
// function checkFormationsAvailability() {
//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) {
//         console.error('CSRF token non trouvé');
//         return;
//     }
    
//     console.log('Vérification des disponibilités en cours...');
    
//     const baseUrl = window.location.origin;
//     const url = `${baseUrl}/panier/check-availability`;
    
//     fetch(url, {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'X-Requested-With': 'XMLHttpRequest'
//         },
//         credentials: 'same-origin'
//     })
//     .then(async response => {
//         if (!response.ok) {
//             const errorText = await response.text();
//             console.error('Erreur de réponse:', response.status, errorText);
//             throw new Error(`Erreur de réponse du serveur: ${response.status}`);
//         }
//         return response.json();
//     })
//     .then(data => {
//         console.log('Réponse reçue de check-availability:', data);
        
//         let hasCompleteFormations = false;
        
//         // Réinitialiser d'abord toutes les formations
//         document.querySelectorAll('.formation-full').forEach(item => {
//             item.classList.remove('formation-full');
//         });
//         document.querySelectorAll('.formation-status-badge').forEach(badge => {
//             badge.remove();
//         });
        
//         if (data.success && data.formations && data.formations.length > 0) {
//             data.formations.forEach(formation => {
//                 const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);
                
//                 if (formationElement) {
//                     // Trouver l'élément pour placer le badge
//                     const formationTitle = formationElement.querySelector('.formation-title') || 
//                                          formationElement.querySelector('h4') || 
//                                          formationElement.querySelector('h3');
                    
//                     if (formation.is_full || formation.has_pending_reservation) {
//                         const statusBadge = document.createElement('span');
//                         statusBadge.className = 'formation-status-badge ml-2';
                        
//                         if (formation.is_full) {
//                             console.log(`Formation ${formation.id} est COMPLÈTE`);
//                             hasCompleteFormations = true;
//                             statusBadge.classList.add('badge', 'badge-danger');
//                             statusBadge.textContent = 'Complète';
//                             formationElement.classList.add('formation-full');
                            
//                             // Style amélioré pour le badge
//                             statusBadge.style.fontWeight = 'bold';
//                             statusBadge.style.fontSize = '0.9rem';
//                             statusBadge.style.padding = '0.3rem 0.6rem';
                            
//                             if (formationTitle) {
//                                 formationTitle.appendChild(statusBadge);
//                             } else {
//                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
//                             }
//                         } else if (formation.has_pending_reservation) {
//                             statusBadge.classList.add('badge', 'badge-warning');
//                             statusBadge.textContent = 'Réservation en attente';
                            
//                             if (formationTitle) {
//                                 formationTitle.appendChild(statusBadge);
//                             } else {
//                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
//                             }
//                         }
//                     }
//                 }
//             });

//             // Ajouter ou supprimer l'avertissement selon le statut
//             if (hasCompleteFormations) {
//                 showCompleteFormationsWarning();
                
//                 // Désactiver le bouton de réservation si présent
//                 const reserverButton = document.querySelector('.reserver-button');
//                 if (reserverButton) {
//                     reserverButton.disabled = true;
//                     reserverButton.classList.add('disabled');
//                     reserverButton.title = 'Une ou plusieurs formations sont complètes';
//                 }
//             } else {
//                 removeCompleteFormationsWarning();
                
//                 // Réactiver le bouton de réservation si présent
//                 const reserverButton = document.querySelector('.reserver-button');
//                 if (reserverButton) {
//                     reserverButton.disabled = false;
//                     reserverButton.classList.remove('disabled');
//                     reserverButton.removeAttribute('title');
//                 }
//             }
//         } else {
//             removeCompleteFormationsWarning();
//         }
//     })
//     .catch(error => {
//         console.error('Erreur lors de la vérification de la disponibilité:', error);
//     });
// }

// function handleResponse(response) {
//     if (!response.ok) {
//         throw new Error('Erreur réseau: ' + response.status);
//     }
//     return response.json();
// }

// function showCompleteFormationsWarning() {
//     // Vérifier si l'avertissement existe déjà
//     let existingWarning = document.querySelector('.complete-formations-warning');
//     if (existingWarning) return;

//     // Créer le conteneur d'avertissement avec une animation
//     const warningContainer = document.createElement('div');
//     warningContainer.className = 'complete-formations-warning';
//     warningContainer.style.animation = 'fadeIn 0.5s';
//     warningContainer.innerHTML = `
//         <i class="fas fa-exclamation-triangle mr-2"></i>
//         <strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.
//     `;

//     // Insérer l'avertissement au bon endroit
//     const greenHeader = document.querySelector('.panier-header');
//     if (greenHeader) {
//         greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
//     } else {
//         const panierContent = document.querySelector('.panier-content');
//         const container = document.querySelector('.container');

//         if (panierContent) {
//             panierContent.insertBefore(warningContainer, panierContent.firstChild);
//         } else if (container) {
//             container.insertBefore(warningContainer, container.firstChild);
//         }
//     }
    
//     // Faire défiler vers l'avertissement
//     warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
// }

// function removeCompleteFormationsWarning() {
//     const warning = document.querySelector('.complete-formations-warning');
//     if (warning) {
//         warning.remove();
//     }
// }

// // Ajouter des styles pour les badges et l'avertissement
// const styleElement = document.createElement('style');
// styleElement.textContent = `
//     @keyframes fadeIn {
//         from { opacity: 0; }
//         to { opacity: 1; }
//     }

//     .complete-formations-warning {
//         width: 100%;
//         margin-bottom: 1rem;
//         display: flex;
//         align-items: center;
//         text-align: center;
//         justify-content: center;
//         padding: 1rem;
//         background-color: #f8d7da;
//         color: #721c24;
//         border: 1px solid #f5c6cb;
//         border-radius: 4px;
//         animation: fadeIn 0.5s;
//     }

//     .formation-status-badge {
//         display: inline-block;
//         padding: 0.25rem 0.5rem;
//         font-size: 0.75rem;
//         font-weight: 600;
//         border-radius: 0.25rem;
//         margin-left: 0.5rem;
//     }

//     .badge-danger {
//         background-color: #dc3545;
//         color: white;
//     }

//     .badge-warning {
//         background-color: #ffc107;
//         color: #212529;
//     }
    
//     .formation-full {
//         background-color: #fff8f8;
//     }
// `;
// document.head.appendChild(styleElement);

// // Exécuter la vérification au chargement de la page
// document.addEventListener('DOMContentLoaded', function() {
//     // Attendez un peu pour être sûr que tout est chargé
//     setTimeout(checkFormationsAvailability, 500);
// });

// // Vérifier périodiquement (toutes les 2 minutes)
// setInterval(checkFormationsAvailability, 2 * 60 * 1000);

// // Fonctions globales pour d'autres interactions du panier
// window.removeFromCart = removeFromCart;
// window.updateCartSummary = updateCartSummary;
// window.forceUpdateCartBadge = updateCartCount;
// window.refreshCartBadge = refreshCartBadgeWithoutReload;
// window.fetchCartItemsCount = refreshCartBadgeWithoutReload;
// window.updateCartCount = updateCartCount;
// window.verifyCartItemsExistence = verifyCartItemsExistence;
// window.checkRemainingCompleteFormations = checkRemainingCompleteFormations;
// window.checkFormationsAvailability = checkFormationsAvailability;

// // Fonction pour vérifier les dates des formations dans le panier
// function checkFormationsDates() {
//     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
//     if (!csrfToken) {
//         console.error('CSRF token non trouvé');
//         return;
//     }
    
//     console.log('Vérification des dates des formations en cours...');
    
//     // Récupérer les détails du panier
//     const baseUrl = window.location.origin;
//     const url = `${baseUrl}/panier/details`;
    
//     fetch(url, {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//             'Content-Type': 'application/json',
//             'X-CSRF-TOKEN': csrfToken,
//             'X-Requested-With': 'XMLHttpRequest'
//         },
//         credentials: 'same-origin'
//     })
//     .then(async response => {
//         if (!response.ok) {
//             const errorText = await response.text();
//             console.error('Erreur de réponse:', response.status, errorText);
//             throw new Error(`Erreur de réponse du serveur: ${response.status}`);
//         }
//         return response.json();
//     })
//     .then(data => {
//         console.log('Réponse reçue pour les détails du panier:', data);
        
//         if (!data.success || !data.trainings || data.trainings.length === 0) {
//             console.log('Aucune formation dans le panier');
//             removeExpiredFormationsWarning();
//             return;
//         }
        
//         let hasExpiredFormations = false;
//         const today = new Date();
//         today.setHours(0, 0, 0, 0); // Comparer seulement les dates sans l'heure
        
//         // Réinitialiser d'abord toutes les formations
//         document.querySelectorAll('.formation-expired').forEach(item => {
//             item.classList.remove('formation-expired');
//         });
//         document.querySelectorAll('.formation-expired-badge').forEach(badge => {
//             badge.remove();
//         });
        
//         // Parcourir toutes les formations dans le panier
//         data.trainings.forEach(formation => {
//             const startDate = new Date(formation.start_date);
//             const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);
            
//             if (formationElement) {
//                 // Trouver l'élément pour placer le badge
//                 const formationTitle = formationElement.querySelector('.formation-title') || 
//                                      formationElement.querySelector('h4') || 
//                                      formationElement.querySelector('h3');
                
//                 if (startDate < today) {
//                     // La date de formation est dépassée
//                     console.log(`Formation ${formation.id} a une date dépassée: ${formation.start_date}`);
//                     hasExpiredFormations = true;
                    
//                     const statusBadge = document.createElement('span');
//                     statusBadge.className = 'formation-status-badge formation-expired-badge ml-2';
//                     statusBadge.classList.add('badge', 'badge-secondary');
//                     statusBadge.textContent = 'Date dépassée';
                    
//                     // Style amélioré pour le badge
//                     statusBadge.style.fontWeight = 'bold';
//                     statusBadge.style.fontSize = '0.9rem';
//                     statusBadge.style.padding = '0.3rem 0.6rem';
                    
//                     formationElement.classList.add('formation-expired');
                    
//                     if (formationTitle) {
//                         formationTitle.appendChild(statusBadge);
//                     } else {
//                         formationElement.insertAdjacentElement('afterbegin', statusBadge);
//                     }
//                 }
//             }
//         });

//         // Ajouter ou supprimer l'avertissement selon le statut
//         if (hasExpiredFormations) {
//             showExpiredFormationsWarning();
            
//             // Désactiver le bouton de réservation si présent
//             const reserverButton = document.querySelector('.reserver-button');
//             if (reserverButton) {
//                 reserverButton.disabled = true;
//                 reserverButton.classList.add('disabled');
//                 reserverButton.title = 'Votre panier contient des formations dont la date est dépassée';
//             }
//         } else {
//             removeExpiredFormationsWarning();
            
//             // Vérifier si le bouton doit être activé (si pas de formations complètes)
//             const hasCompleteFormations = document.querySelectorAll('.formation-full').length > 0;
//             if (!hasCompleteFormations) {
//                 const reserverButton = document.querySelector('.reserver-button');
//                 if (reserverButton) {
//                     reserverButton.disabled = false;
//                     reserverButton.classList.remove('disabled');
//                     reserverButton.removeAttribute('title');
//                 }
//             }
//         }
//     })
//     .catch(error => {
//         console.error('Erreur lors de la vérification des dates des formations:', error);
//     });
// }

// // Fonction pour afficher l'avertissement de formations expirées
// function showExpiredFormationsWarning() {
//     // Vérifier si l'avertissement existe déjà
//     let existingWarning = document.querySelector('.expired-formations-warning');
//     if (existingWarning) return;

//     // Créer le conteneur d'avertissement
//     const warningContainer = document.createElement('div');
//     warningContainer.className = 'expired-formations-warning';
//     warningContainer.style.animation = 'fadeIn 0.5s';
//     warningContainer.innerHTML = `
//         <i class="fas fa-calendar-times mr-2"></i>
//         <strong>Attention:</strong> Votre panier contient des formations dont la date est dépassée. Veuillez les supprimer pour poursuivre votre réservation.
//     `;

//     // Insérer l'avertissement au bon endroit
//     const completeWarning = document.querySelector('.complete-formations-warning');
//     const greenHeader = document.querySelector('.panier-header');
    
//     if (completeWarning) {
//         completeWarning.parentNode.insertBefore(warningContainer, completeWarning.nextSibling);
//     } else if (greenHeader) {
//         greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
//     } else {
//         const panierContent = document.querySelector('.panier-content');
//         const container = document.querySelector('.container');

//         if (panierContent) {
//             panierContent.insertBefore(warningContainer, panierContent.firstChild);
//         } else if (container) {
//             container.insertBefore(warningContainer, container.firstChild);
//         }
//     }
    
//     // Faire défiler vers l'avertissement
//     warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
// }

// // Fonction pour supprimer l'avertissement de formations expirées
// function removeExpiredFormationsWarning() {
//     const warning = document.querySelector('.expired-formations-warning');
//     if (warning) {
//         warning.remove();
//     }
// }

// // Ajouter des styles pour les badges et l'avertissement
// const expiredStyleElement = document.createElement('style');
// expiredStyleElement.textContent = `
//     .expired-formations-warning {
//         width: 100%;
//         margin-bottom: 1rem;
//         display: flex;
//         align-items: center;
//         text-align: center;
//         justify-content: center;
//         padding: 1rem;
//         background-color: #e9ecef;
//         color: #495057;
//         border: 1px solid #ced4da;
//         border-radius: 4px;
//         animation: fadeIn 0.5s;
//     }

//     .badge-secondary {
//         background-color: #6c757d;
//         color: white;
//     }
    
//     .formation-expired {
//         background-color: #f8f9fa;
//         opacity: 0.8;
//     }
// `;
// document.head.appendChild(expiredStyleElement);

// // Exécuter la vérification au chargement de la page
// document.addEventListener('DOMContentLoaded', function() {
//     // Attendre un peu pour être sûr que tout est chargé
//     setTimeout(checkFormationsDates, 800);
// });

// // Vérifier périodiquement (toutes les 2 minutes)
// setInterval(checkFormationsDates, 2 * 60 * 1000);

// // Ajouter à l'initialisation des listeners
// function enhanceInitializeListeners() {
//     const originalInitializeListeners = window.initializeListeners || function() {};
    
//     window.initializeListeners = function() {
//         originalInitializeListeners();
        
//         // Vérifier les dates des formations au chargement
//         checkFormationsDates();
        
//         // Ajouter la vérification des dates après chaque action sur le panier
//         document.addEventListener('visibilitychange', function() {
//             if (!document.hidden) {
//                 checkFormationsDates();
//             }
//         });
//     };
    
//     // Si la page est déjà chargée, exécuter immédiatement
//     if (document.readyState !== 'loading') {
//         checkFormationsDates();
//     }
// }

// // Améliorer la fonction removeFromCart pour vérifier les dates après suppression
// function enhanceRemoveFromCart() {
//     const originalUpdateUIAfterRemoval = window.updateUIAfterRemoval || function() {};
    
//     window.updateUIAfterRemoval = function(response) {
//         originalUpdateUIAfterRemoval(response);
        
//         // Vérifier s'il reste des formations expirées après suppression
//         if (response.cartCount > 0) {
//             checkFormationsDates();
//         } else {
//             // Si le panier est vide, supprimer tous les avertissements
//             removeExpiredFormationsWarning();
//         }
//     };
// }

// // Exposer les fonctions globalement
// window.checkFormationsDates = checkFormationsDates;
// window.showExpiredFormationsWarning = showExpiredFormationsWarning;
// window.removeExpiredFormationsWarning = removeExpiredFormationsWarning;

// // Améliorer les fonctions existantes
// enhanceInitializeListeners();
// enhanceRemoveFromCart();


(function() {
    // Fonction pour initialiser immédiatement le badge du panier
    function initBadgeImmediately() {
        const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
        
        // Mettre à jour tous les badges fixes existants
        const fixedBadges = document.querySelectorAll('#fixed-cart-badge, .custom-violet-badge, .cart-badge');
        fixedBadges.forEach(badge => {
            badge.textContent = cartCount.toString();
            badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
            badge.style.opacity = cartCount > 0 ? '1' : '0';
        });
        
        // Créer des badges pour toutes les icônes de panier si nécessaire
        if (cartCount > 0) {
            const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
            cartIcons.forEach(icon => {
                const container = icon.closest('a, div, button, .cart-container');
                if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                    createBadgeForContainer(container, cartCount);
                }
            });
        }
    }
    // Exécuter immédiatement pour l'affichage le plus rapide possible
    initBadgeImmediately();
    // Observer le DOM pour les icônes ajoutées dynamiquement
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(mutations => {
            const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
            if (cartCount <= 0) return;
            
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) {
                            const icons = node.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
                            icons.forEach(icon => {
                                const container = icon.closest('a, div, button, .cart-container');
                                if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                                    createBadgeForContainer(container, cartCount);
                                }
                            });
                            
                            if (node.matches && node.matches('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg')) {
                                const container = node.closest('a, div, button, .cart-container');
                                if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                                    createBadgeForContainer(container, cartCount);
                                }
                            }
                        }
                    });
                }
            });
        });
        
        observer.observe(document.documentElement, {
            childList: true,
            subtree: true
        });
    }
    // Injecter les styles nécessaires immédiatement
    const badgeElement = document.createElement('style');
    badgeElement.textContent = `
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
            visibility: hidden;
            opacity: 0;
        }
    `;
    document.head.appendChild(badgeElement);
})();
function createBadgeForContainer(container, count) {
    if (!container) return;
    
    if (container.querySelector('.cart-badge, .custom-violet-badge')) {
        const existingBadge = container.querySelector('.cart-badge, .custom-violet-badge');
        existingBadge.textContent = count.toString();
        existingBadge.style.visibility = count > 0 ? 'visible' : 'hidden';
        existingBadge.style.opacity = count > 0 ? '1' : '0';
        return;
    }
    
    let badge = document.createElement('span');
    badge.className = 'cart-badge custom-violet-badge';
    badge.textContent = count.toString();
    badge.style.visibility = count > 0 ? 'visible' : 'hidden';
    badge.style.opacity = count > 0 ? '1' : '0';
    
    if (getComputedStyle(container).position === 'static') {
        container.style.position = 'relative';
    }
    
    container.appendChild(badge);
}
(function() {
    const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
    window.hasExistingReservation = hasExistingReservation;
    
    const storedCount = parseInt(localStorage.getItem('cartCount') || '0');
    
    if (storedCount > 0) {
        updateAllBadges(storedCount);
    } else {
        showEmptyCartMessage();
    }
    
    if (hasExistingReservation) {
        const reservationId = localStorage.getItem('reservationId');
        if (reservationId && typeof transformReserverButton === 'function') {
            transformReserverButton(parseInt(reservationId));
        }
    }
    
    synchronizeWithServer();
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeListeners);
    } else {
        initializeListeners();
    }
})();

function synchronizeWithServer() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) return;
    
    fetch('/panier/count', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const count = data.count || 0;
        const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
        
        if (oldCount !== count) {
            localStorage.setItem('cartCount', count.toString());
            updateAllBadges(count);
        }
    })
    .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
}
function updateAllBadges(count) {
    const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
    badges.forEach(badge => {
        badge.textContent = count.toString();
        badge.style.visibility = count > 0 ? 'visible' : 'hidden';
        badge.style.opacity = count > 0 ? '1' : '0';
    });
    
    if (count > 0) {
        const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
        cartIcons.forEach(icon => {
            const container = icon.closest('a, div, button, .cart-container');
            if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                createBadgeForContainer(container, count);
            }
        });
    }
}
function initializeListeners() {
    setupRemoveFromCartListeners();
    
    // CORRECTION : Vérifier l'état du panier au chargement initial
    const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    if (cartCount === 0 && window.location.pathname.includes('/panier')) {
        showEmptyCartMessage();
    }
    
    setInterval(refreshCartBadgeWithoutReload, 5000);
    setInterval(verifyCartItemsExistence, 120000);
    
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
            updateAllBadges(cartCount);
            refreshCartBadgeWithoutReload();
            verifyCartItemsExistence();
            
            // CORRECTION : Vérifier l'état du panier quand l'utilisateur revient sur la page
            if (cartCount === 0 && window.location.pathname.includes('/panier')) {
                showEmptyCartMessage();
            }
        }
    });
    
    const oldXHROpen = window.XMLHttpRequest.prototype.open;
    window.XMLHttpRequest.prototype.open = function() {
        this.addEventListener('load', function() {
            const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
            updateAllBadges(cartCount);
            
            // CORRECTION : Vérifier l'état du panier après chaque requête XHR
            if (cartCount === 0 && window.location.pathname.includes('/panier')) {
                showEmptyCartMessage();
            }
        });
        return oldXHROpen.apply(this, arguments);
    };
}
function setupRemoveFromCartListeners() {
    document.addEventListener('click', function(e) {
        const removeLink = e.target.closest('.remove-link');
        if (removeLink) {
            e.preventDefault();
            const formationId = removeLink.getAttribute('data-formation-id');
            if (formationId) {
                removeFromCart(formationId);
            }
        }
    });
}
function refreshCartBadgeWithoutReload() {
    fetch('/panier/items-count', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(handleResponse)
    .then(data => {
        const count = parseInt(data.count) || 0;
        const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
        if (oldCount !== count) {
            localStorage.setItem('cartCount', count.toString());
            updateAllBadges(count);
        }
        
        const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
        if (count > 0 && !hasExistingReservation) {
            /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
                createReserverButtonEarly();
            } */
        } else if (count === 0 && !hasExistingReservation) {
            document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
                btn.remove();
            });
        } else if (hasExistingReservation) {
            const reservationId = localStorage.getItem('reservationId');
            if (reservationId && typeof transformReserverButton === 'function') {
                transformReserverButton(parseInt(reservationId));
            }
        }
    })
    .catch(error => console.error('Erreur:', error));
}
function updateCartCount(count) {
    count = parseInt(count) || 0;
    window.globalCartCount = count;
    localStorage.setItem('cartCount', count.toString());
    updateAllBadges(count);
    
    var panierCountElements = document.querySelectorAll('.panier-count');
    panierCountElements.forEach(function(el) {
        el.textContent = count + ' formation(s)';
        el.style.opacity = '1';
    });
    
    const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
    if (count > 0 && !hasExistingReservation) {
        /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
            createReserverButtonEarly();
        } */
    } else if (count === 0 && !hasExistingReservation) {
        document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
            btn.remove();
        });
    } else if (hasExistingReservation) {
        const reservationId = localStorage.getItem('reservationId');
        if (reservationId && typeof transformReserverButton === 'function') {
            transformReserverButton(parseInt(reservationId));
        }
    }
}

function removeFromCart(formationId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token non trouvé');
        return;
    }
    
    const baseUrl = window.location.origin;
    
    fetch(`${baseUrl}/panier/supprimer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            formation_id: formationId
        })
    })
    .then(handleResponse)
    .then(response => {
        // Vérifier si la réponse est valide avant de continuer
        if (!response || !response.success) {
            console.error(response?.message || 'Erreur lors de la suppression de la formation');
            return;
        }
        
        const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
        const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
        localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
        localStorage.setItem('cartCount', response.cartCount.toString());
        updateCartCount(response.cartCount);
        
        const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
        if (formationItem) {
            // Vérifier si la formation supprimée était complète
            const wasCompleteFormation = formationItem.classList.contains('formation-full');
            
            formationItem.remove();
            updateUIAfterRemoval(response);
            
            // Si la formation supprimée était complète, vérifier immédiatement s'il reste des formations complètes
            if (wasCompleteFormation) {
                checkRemainingCompleteFormations();
            }
        }
        
        if (response.cartCount === 0) {
            showEmptyCartMessage();
            removeCompleteFormationsWarning(); // Supprimer l'alerte immédiatement si le panier est vide
        }
    })
    .catch(error => {
        console.error('Erreur lors de la suppression:', error);
    });
}
function updateUIAfterRemoval(response) {
    // Ajout de logs pour debugging
    console.log("updateUIAfterRemoval appelé avec:", response);
    console.log("Nouveau cartCount:", response.cartCount);
    
    // Mettre à jour le compteur du panier
    updateCartCount(response.cartCount);
    
    // Vérifier si le panier est maintenant vide
    if (response.cartCount === 0) {
        console.log("Panier détecté comme vide, affichage du message");
        
        // Nettoyer les éléments existants du panier
        const panierContent = document.querySelector('.panier-content');
        if (panierContent) {
            panierContent.remove();
            console.log("Contenu du panier supprimé");
        }
        
        // Afficher le message de panier vide
        showEmptyCartMessage();
        
        // Cacher tous les badges du panier
        document.querySelectorAll('.cart-badge, .custom-violet-badge').forEach(badge => {
            badge.style.visibility = 'hidden';
            badge.style.opacity = '0';
            console.log("Badge caché");
        });
        
        // Supprimer le bouton de réservation s'il existe
        const reserveButton = document.querySelector('.reserver-button');
        if (reserveButton) {
            reserveButton.remove();
            console.log("Bouton de réservation supprimé");
        }
        
        // Supprimer l'alerte de formations complètes
        removeCompleteFormationsWarning();
    } else {
        // Si le panier n'est pas vide, mettre à jour le résumé
        console.log("Panier non vide, mise à jour du résumé");
        updateCartSummary(response);
        
        // Vérifier s'il reste des formations complètes
        checkRemainingCompleteFormations();
    }
}

function showEmptyCartMessage() {
    // Ajout de logs pour déboguer
    console.log("Exécution de showEmptyCartMessage()");
    
    // Récupérer les éléments existants
    const panierContent = document.querySelector('.panier-content');
    const container = document.querySelector('.container');
    
    // Log pour vérifier si les éléments sont trouvés
    console.log("panierContent trouvé:", !!panierContent);
    console.log("container trouvé:", !!container);
    
    // Supprimer le contenu du panier s'il existe
    if (panierContent) {
        panierContent.remove();
        console.log("panierContent supprimé");
    }
    
    // Vérifier si le message de panier vide existe déjà
    const existingEmptyCart = document.querySelector('.empty-cart');
    if (existingEmptyCart) {
        console.log("Message 'panier vide' déjà présent");
        return; // Ne pas créer de doublon
    }
    
    // Créer le HTML pour le message de panier vide
    const emptyCartHTML = `
        <div class="empty-cart">
            <i class="fas fa-shopping-cart"></i>
            <p>Votre panier est vide</p>
            <a href="formation/formations">Découvrir des formations</a>
        </div>
    `;
    
    // Ajouter le message au container
    if (container) {
        container.innerHTML += emptyCartHTML;
        console.log("Message 'panier vide' ajouté au container");
    } else {
        console.error("Container non trouvé pour ajouter le message 'panier vide'");
    }
    
    // Supprimer l'alerte de formations complètes
    removeCompleteFormationsWarning();
}

function updateCartSummary(response) {
    if (response.cartCount === 0) {
        const existingButton = document.querySelector('.reserver-button');
        if (existingButton) {
            existingButton.remove();
        }
        removeCompleteFormationsWarning();
        return;
    }
    
    const totalPriceElement = document.querySelector('.total-price');
    if (totalPriceElement) {
        totalPriceElement.textContent = response.totalPrice + ' DT';
    }
    
    if (response.hasDiscount && parseFloat(response.discountedItemsOriginalPrice) > 0) {
        let originalPriceElement = document.querySelector('.original-price');
        let discountElement = document.querySelector('.discount-percentage');
        
        if (originalPriceElement) {
            originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
        } else if (totalPriceElement) {
            originalPriceElement = document.createElement('div');
            originalPriceElement.className = 'original-price';
            originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
            totalPriceElement.insertAdjacentElement('afterend', originalPriceElement);
        }
        
        if (discountElement) {
            discountElement.textContent = response.discountPercentage + '% ';
        } else if (originalPriceElement) {
            discountElement = document.createElement('div');
            discountElement.className = 'discount-percentage';
            discountElement.textContent = response.discountPercentage + '% ';
            originalPriceElement.insertAdjacentElement('afterend', discountElement);
        }
    } else {
        const originalPrice = document.querySelector('.original-price');
        const discountPercentage = document.querySelector('.discount-percentage');
        if (originalPrice) originalPrice.remove();
        if (discountPercentage) discountPercentage.remove();
    }
}
function verifyCartItemsExistence() {
    const cartItems = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    if (cartItems.length === 0) return;
    
    fetch('/panier/details', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            items: cartItems
        })
    })
    .then(handleResponse)
    .then(response => {
        if (response.removed_items && response.removed_items.length > 0) {
            const validItems = cartItems.filter(itemId => 
                !response.removed_items.includes(itemId.toString()) && 
                !response.removed_items.includes(parseInt(itemId))
            );
            localStorage.setItem('cartFormations', JSON.stringify(validItems));
            updateCartCount(response.cartCount);
            
            if (window.location.pathname.includes('/panier')) {
                response.removed_items.forEach(itemId => {
                    const formationItem = document.querySelector(`.formation-item[data-formation-id="${itemId}"]`);
                    if (formationItem) {
                        formationItem.remove();
                    }
                });
                
                if (response.cartCount === 0) {
                    showEmptyCartMessage();
                } else {
                    updateCartSummary(response);
                }
            }
            
            // Vérifier s'il reste des formations complètes après suppression
            checkRemainingCompleteFormations();
        }
    })
    .catch(error => console.error('Erreur lors de la vérification des articles du panier:', error));
}
function checkRemainingCompleteFormations() {
    const remainingCompleteFormations = document.querySelectorAll('.formation-full');
    
    if (remainingCompleteFormations.length === 0) {
        // Plus aucune formation complète, supprimer l'avertissement immédiatement
        removeCompleteFormationsWarning();
        
        // Réactiver le bouton de réservation s'il existe
        const reserverButton = document.querySelector('.reserver-button');
        if (reserverButton) {
            reserverButton.disabled = false;
            reserverButton.classList.remove('disabled');
            reserverButton.removeAttribute('title');
        }
        
        // Si le système de réservation est initialisé, on met à jour le statut global
        if (window.hasCompleteFormationsInCart !== undefined) {
            window.hasCompleteFormationsInCart = false;
        }
    } else {
        // S'il reste des formations complètes, s'assurer que l'avertissement est affiché
        showCompleteFormationsWarning();
        
        // Désactiver le bouton de réservation
        const reserverButton = document.querySelector('.reserver-button');
        if (reserverButton) {
            reserverButton.disabled = true;
            reserverButton.classList.add('disabled');
            reserverButton.title = 'Une ou plusieurs formations sont complètes';
        }
        
        // Si le système de réservation est initialisé, on met à jour le statut global
        if (window.hasCompleteFormationsInCart !== undefined) {
            window.hasCompleteFormationsInCart = true;
        }
    }
}
function checkFormationsAvailability() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token non trouvé');
        return;
    }
    
    console.log('Vérification des disponibilités en cours...');
    
    const baseUrl = window.location.origin;
    const url = `${baseUrl}/panier/check-availability`;
    
    fetch(url, {
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
        console.log('Réponse reçue de check-availability:', data);
        
        let hasCompleteFormations = false;
        
        // Réinitialiser d'abord toutes les formations
        document.querySelectorAll('.formation-full').forEach(item => {
            item.classList.remove('formation-full');
        });
        document.querySelectorAll('.formation-status-badge').forEach(badge => {
            badge.remove();
        });
        
        if (data.success && data.formations && data.formations.length > 0) {
            data.formations.forEach(formation => {
                const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);
                
                if (formationElement) {
                    // Trouver l'élément pour placer le badge
                    const formationTitle = formationElement.querySelector('.formation-title') || 
                                         formationElement.querySelector('h4') || 
                                         formationElement.querySelector('h3');
                    
                    if (formation.is_full || formation.has_pending_reservation) {
                        const statusBadge = document.createElement('span');
                        statusBadge.className = 'formation-status-badge ml-2';
                        
                        if (formation.is_full) {
                            console.log(`Formation ${formation.id} est COMPLÈTE`);
                            hasCompleteFormations = true;
                            statusBadge.classList.add('badge', 'badge-danger');
                            statusBadge.textContent = 'Complète';
                            formationElement.classList.add('formation-full');
                            
                            // Style amélioré pour le badge
                            statusBadge.style.fontWeight = 'bold';
                            statusBadge.style.fontSize = '0.9rem';
                            statusBadge.style.padding = '0.3rem 0.6rem';
                            
                            if (formationTitle) {
                                formationTitle.appendChild(statusBadge);
                            } else {
                                formationElement.insertAdjacentElement('afterbegin', statusBadge);
                            }
                        } else if (formation.has_pending_reservation) {
                            statusBadge.classList.add('badge', 'badge-warning');
                            statusBadge.textContent = 'Réservation en attente';
                            
                            if (formationTitle) {
                                formationTitle.appendChild(statusBadge);
                            } else {
                                formationElement.insertAdjacentElement('afterbegin', statusBadge);
                            }
                        }
                    }
                }
            });

            // Ajouter ou supprimer l'avertissement selon le statut
            if (hasCompleteFormations) {
                showCompleteFormationsWarning();
                
                // Désactiver le bouton de réservation si présent
                const reserverButton = document.querySelector('.reserver-button');
                if (reserverButton) {
                    reserverButton.disabled = true;
                    reserverButton.classList.att('disabled');
                    reserverButton.title = 'Une ou plusieurs formations sont complètes';
                }
            } else {
                removeCompleteFormationsWarning();
                
                // Réactiver le bouton de réservation si présent
                const reserverButton = document.querySelector('.reserver-button');
                if (reserverButton) {
                    reserverButton.disabled = false;
                    reserverButton.classList.remove('disabled');
                    reserverButton.removeAttribute('title');
                }
            }
        } else {
            removeCompleteFormationsWarning();
        }
    })
    .catch(error => {
        console.error('Erreur lors de la vérification de la disponibilité:', error);
    });
}

function handleResponse(response) {
    if (!response.ok) {
        throw new Error('Erreur réseau: ' + response.status);
    }
    return response.json();
}

function showCompleteFormationsWarning() {
    // Vérifier si l'avertissement existe déjà
    let existingWarning = document.querySelector('.complete-formations-warning');
    if (existingWarning) return;

    // Créer le conteneur d'avertissement avec une animation
    const warningContainer = document.createElement('div');
    warningContainer.className = 'complete-formations-warning';
    warningContainer.style.animation = 'fadeIn 0.5s';
    warningContainer.innerHTML = `
        <i class="fas fa-exclamation-triangle mr-2"></i>
        <strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.
    `;

    // Insérer l'avertissement au bon endroit
    const greenHeader = document.querySelector('.panier-header');
    if (greenHeader) {
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

function removeCompleteFormationsWarning() {
    const warning = document.querySelector('.complete-formations-warning');
    if (warning) {
        warning.remove();
    }
}

// Ajouter des styles pour les badges et l'avertissement
const styleElement = document.createElement('style');
styleElement.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .complete-formations-warning {
        width: 100%;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        text-align: center;
        justify-content: center;
        padding: 1rem;
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        animation: fadeIn 0.5s;
    }

    .formation-status-badge {
        display: inline-block;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 0.25rem;
        margin-left: 0.5rem;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
    
    .formation-full {
        background-color: #fff8f8;
    }
`;
document.head.appendChild(styleElement);

// Exécuter la vérification au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Attendez un peu pour être sûr que tout est chargé
    setTimeout(checkFormationsAvailability, 500);
});

// Vérifier périodiquement (toutes les 2 minutes)
setInterval(checkFormationsAvailability, 2 * 60 * 1000);

// Fonctions globales pour d'autres interactions du panier
window.removeFromCart = removeFromCart;
window.updateCartSummary = updateCartSummary;
window.forceUpdateCartBadge = updateCartCount;
window.refreshCartBadge = refreshCartBadgeWithoutReload;
window.fetchCartItemsCount = refreshCartBadgeWithoutReload;
window.updateCartCount = updateCartCount;
window.verifyCartItemsExistence = verifyCartItemsExistence;
window.checkRemainingCompleteFormations = checkRemainingCompleteFormations;
window.checkFormationsAvailability = checkFormationsAvailability;

// Ajouter à l'initialisation des listeners
function enhanceInitializeListeners() {
    const originalInitializeListeners = window.initializeListeners || function() {};
    
    window.initializeListeners = function() {
        originalInitializeListeners();
        
        // Vérifier les dates des formations au chargement
        if (typeof window.checkFormationsDates === 'function') {
            window.checkFormationsDates();
        }
        
        // Ajouter la vérification des dates après chaque action sur le panier
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                if (typeof window.checkFormationsDates === 'function') {
                    window.checkFormationsDates();
                }
            }
        });
    };
    
    // Si la page est déjà chargée, exécuter immédiatement
    if (document.readyState !== 'loading') {
        if (typeof window.checkFormationsDates === 'function') {
            window.checkFormationsDates();
        }
    }
}

// Améliorer la fonction removeFromCart pour vérifier les dates après suppression
function enhanceRemoveFromCart() {
    const originalUpdateUIAfterRemoval = window.updateUIAfterRemoval || function() {};
    
    window.updateUIAfterRemoval = function(response) {
        originalUpdateUIAfterRemoval(response);
        
        // Vérifier s'il reste des formations expirées après suppression
        if (response.cartCount > 0) {
            if (typeof window.checkFormationsDates === 'function') {
                window.checkFormationsDates();
            }
        } else {
            // Si le panier est vide, supprimer tous les avertissements
            if (typeof window.removeExpiredFormationsWarning === 'function') {
                window.removeExpiredFormationsWarning();
            }
        }
    };
}

// Améliorer les fonctions existantes
enhanceInitializeListeners();
enhanceRemoveFromCart();