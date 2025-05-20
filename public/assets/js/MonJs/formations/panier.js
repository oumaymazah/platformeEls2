// // (function() {
// //     // Fonction pour initialiser immédiatement le badge du panier
// //     function initBadgeImmediately() {
// //         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

// //         // Mettre à jour tous les badges fixes existants
// //         const fixedBadges = document.querySelectorAll('#fixed-cart-badge, .custom-violet-badge, .cart-badge');
// //         fixedBadges.forEach(badge => {
// //             badge.textContent = cartCount.toString();
// //             badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
// //             badge.style.opacity = cartCount > 0 ? '1' : '0';
// //         });

// //         // Créer des badges pour toutes les icônes de panier si nécessaire
// //         if (cartCount > 0) {
// //             const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
// //             cartIcons.forEach(icon => {
// //                 const container = icon.closest('a, div, button, .cart-container');
// //                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                     createBadgeForContainer(container, cartCount);
// //                 }
// //             });
// //         }
// //     }
// //     // Exécuter immédiatement pour l'affichage le plus rapide possible
// //     initBadgeImmediately();
// //     // Observer le DOM pour les icônes ajoutées dynamiquement
// //     if (typeof MutationObserver !== 'undefined') {
// //         const observer = new MutationObserver(mutations => {
// //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //             if (cartCount <= 0) return;

// //             mutations.forEach(mutation => {
// //                 if (mutation.addedNodes.length) {
// //                     mutation.addedNodes.forEach(node => {
// //                         if (node.nodeType === 1) {
// //                             const icons = node.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
// //                             icons.forEach(icon => {
// //                                 const container = icon.closest('a, div, button, .cart-container');
// //                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                                     createBadgeForContainer(container, cartCount);
// //                                 }
// //                             });

// //                             if (node.matches && node.matches('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg')) {
// //                                 const container = node.closest('a, div, button, .cart-container');
// //                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                                     createBadgeForContainer(container, cartCount);
// //                                 }
// //                             }
// //                         }
// //                     });
// //                 }
// //             });
// //         });

// //         observer.observe(document.documentElement, {
// //             childList: true,
// //             subtree: true
// //         });
// //     }
// //     // Injecter les styles nécessaires immédiatement
// //     const badgeElement = document.createElement('style');
// //     badgeElement.textContent = `
// //         .cart-badge, .custom-violet-badge {
// //             position: absolute;
// //             top: -8px;
// //             right: -8px;
// //             background-color: #2563EB;
// //             color: white;
// //             border-radius: 50%;
// //             width: 18px;
// //             height: 18px;
// //             font-size: 12px;
// //             display: flex;
// //             align-items: center;
// //             justify-content: center;
// //             font-weight: bold;
// //             z-index: 10;
// //             visibility: hidden;
// //             opacity: 0;
// //         }
// //     `;
// //     document.head.appendChild(badgeElement);
// // })();
// // function createBadgeForContainer(container, count) {
// //     if (!container) return;

// //     if (container.querySelector('.cart-badge, .custom-violet-badge')) {
// //         const existingBadge = container.querySelector('.cart-badge, .custom-violet-badge');
// //         existingBadge.textContent = count.toString();
// //         existingBadge.style.visibility = count > 0 ? 'visible' : 'hidden';
// //         existingBadge.style.opacity = count > 0 ? '1' : '0';
// //         return;
// //     }

// //     let badge = document.createElement('span');
// //     badge.className = 'cart-badge custom-violet-badge';
// //     badge.textContent = count.toString();
// //     badge.style.visibility = count > 0 ? 'visible' : 'hidden';
// //     badge.style.opacity = count > 0 ? '1' : '0';

// //     if (getComputedStyle(container).position === 'static') {
// //         container.style.position = 'relative';
// //     }

// //     container.appendChild(badge);
// // }
// // (function() {
// //     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
// //     window.hasExistingReservation = hasExistingReservation;

// //     const storedCount = parseInt(localStorage.getItem('cartCount') || '0');

// //     if (storedCount > 0) {
// //         updateAllBadges(storedCount);
// //     } else {
// //         showEmptyCartMessage();
// //     }

// //     if (hasExistingReservation) {
// //         const reservationId = localStorage.getItem('reservationId');
// //         if (reservationId && typeof transformReserverButton === 'function') {
// //             transformReserverButton(parseInt(reservationId));
// //         }
// //     }

// //     synchronizeWithServer();

// //     if (document.readyState === 'loading') {
// //         document.addEventListener('DOMContentLoaded', initializeListeners);
// //     } else {
// //         initializeListeners();
// //     }
// // })();

// // function synchronizeWithServer() {
// //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
// //     if (!csrfToken) return;

// //     fetch('/panier/count', {
// //         method: 'GET',
// //         headers: {
// //             'Accept': 'application/json',
// //             'X-CSRF-TOKEN': csrfToken,
// //             'X-Requested-With': 'XMLHttpRequest'
// //         }
// //     })
// //     .then(response => response.json())
// //     .then(data => {
// //         const count = data.count || 0;
// //         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');

// //         if (oldCount !== count) {
// //             localStorage.setItem('cartCount', count.toString());
// //             updateAllBadges(count);
// //         }
// //     })
// //     .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
// // }
// // function updateAllBadges(count) {
// //     const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
// //     badges.forEach(badge => {
// //         badge.textContent = count.toString();
// //         badge.style.visibility = count > 0 ? 'visible' : 'hidden';
// //         badge.style.opacity = count > 0 ? '1' : '0';
// //     });

// //     if (count > 0) {
// //         const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
// //         cartIcons.forEach(icon => {
// //             const container = icon.closest('a, div, button, .cart-container');
// //             if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                 createBadgeForContainer(container, count);
// //             }
// //         });
// //     }
// // }
// // function initializeListeners() {
// //     setupRemoveFromCartListeners();

// //     // CORRECTION : Vérifier l'état du panier au chargement initial
// //     const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //     if (cartCount === 0 && window.location.pathname.includes('/panier')) {
// //         showEmptyCartMessage();
// //     }

// //     setInterval(refreshCartBadgeWithoutReload, 5000);
// //     setInterval(verifyCartItemsExistence, 120000);

// //     document.addEventListener('visibilitychange', function() {
// //         if (!document.hidden) {
// //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //             updateAllBadges(cartCount);
// //             refreshCartBadgeWithoutReload();
// //             verifyCartItemsExistence();

// //             // CORRECTION : Vérifier l'état du panier quand l'utilisateur revient sur la page
// //             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
// //                 showEmptyCartMessage();
// //             }
// //         }
// //     });

// //     const oldXHROpen = window.XMLHttpRequest.prototype.open;
// //     window.XMLHttpRequest.prototype.open = function() {
// //         this.addEventListener('load', function() {
// //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //             updateAllBadges(cartCount);

// //             // CORRECTION : Vérifier l'état du panier après chaque requête XHR
// //             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
// //                 showEmptyCartMessage();
// //             }
// //         });
// //         return oldXHROpen.apply(this, arguments);
// //     };
// // }
// // function setupRemoveFromCartListeners() {
// //     document.addEventListener('click', function(e) {
// //         const removeLink = e.target.closest('.remove-link');
// //         if (removeLink) {
// //             e.preventDefault();
// //             const formationId = removeLink.getAttribute('data-formation-id');
// //             if (formationId) {
// //                 removeFromCart(formationId);
// //             }
// //         }
// //     });
// // }
// // function refreshCartBadgeWithoutReload() {
// //     fetch('/panier/items-count', {
// //         method: 'GET',
// //         headers: {
// //             'X-Requested-With': 'XMLHttpRequest',
// //             'Accept': 'application/json'
// //         }
// //     })
// //     .then(handleResponse)
// //     .then(data => {
// //         const count = parseInt(data.count) || 0;
// //         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
// //         if (oldCount !== count) {
// //             localStorage.setItem('cartCount', count.toString());
// //             updateAllBadges(count);
// //         }

// //         const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
// //         if (count > 0 && !hasExistingReservation) {
// //             /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
// //                 createReserverButtonEarly();
// //             } */
// //         } else if (count === 0 && !hasExistingReservation) {
// //             document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
// //                 btn.remove();
// //             });
// //         } else if (hasExistingReservation) {
// //             const reservationId = localStorage.getItem('reservationId');
// //             if (reservationId && typeof transformReserverButton === 'function') {
// //                 transformReserverButton(parseInt(reservationId));
// //             }
// //         }
// //     })
// //     .catch(error => console.error('Erreur:', error));
// // }
// // function updateCartCount(count) {
// //     count = parseInt(count) || 0;
// //     window.globalCartCount = count;
// //     localStorage.setItem('cartCount', count.toString());
// //     updateAllBadges(count);

// //     var panierCountElements = document.querySelectorAll('.panier-count');
// //     panierCountElements.forEach(function(el) {
// //         el.textContent = count + ' formation(s)';
// //         el.style.opacity = '1';
// //     });

// //     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
// //     if (count > 0 && !hasExistingReservation) {
// //         /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
// //             createReserverButtonEarly();
// //         } */
// //     } else if (count === 0 && !hasExistingReservation) {
// //         document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
// //             btn.remove();
// //         });
// //     } else if (hasExistingReservation) {
// //         const reservationId = localStorage.getItem('reservationId');
// //         if (reservationId && typeof transformReserverButton === 'function') {
// //             transformReserverButton(parseInt(reservationId));
// //         }
// //     }
// // }

// // function removeFromCart(formationId) {
// //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
// //     if (!csrfToken) {
// //         console.error('CSRF token non trouvé');
// //         return;
// //     }

// //     const baseUrl = window.location.origin;

// //     fetch(`${baseUrl}/panier/supprimer`, {
// //         method: 'POST',
// //         headers: {
// //             'Content-Type': 'application/json',
// //             'X-CSRF-TOKEN': csrfToken,
// //             'Accept': 'application/json'
// //         },
// //         body: JSON.stringify({
// //             formation_id: formationId
// //         })
// //     })
// //     .then(handleResponse)
// //     .then(response => {
// //         // Vérifier si la réponse est valide avant de continuer
// //         if (!response || !response.success) {
// //             console.error(response?.message || 'Erreur lors de la suppression de la formation');
// //             return;
// //         }

// //         const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
// //         const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
// //         localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
// //         localStorage.setItem('cartCount', response.cartCount.toString());
// //         updateCartCount(response.cartCount);

// //         const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
// //         if (formationItem) {
// //             // Vérifier si la formation supprimée était complète
// //             const wasCompleteFormation = formationItem.classList.contains('formation-full');

// //             formationItem.remove();
// //             updateUIAfterRemoval(response);

// //             // Si la formation supprimée était complète, vérifier immédiatement s'il reste des formations complètes
// //             if (wasCompleteFormation) {
// //                 checkRemainingCompleteFormations();
// //             }
// //         }

// //         if (response.cartCount === 0) {
// //             showEmptyCartMessage();
// //             removeCompleteFormationsWarning(); // Supprimer l'alerte immédiatement si le panier est vide
// //         }
// //     })
// //     .catch(error => {
// //         console.error('Erreur lors de la suppression:', error);
// //     });
// // }
// // function updateUIAfterRemoval(response) {
// //     // Ajout de logs pour debugging
// //     console.log("updateUIAfterRemoval appelé avec:", response);
// //     console.log("Nouveau cartCount:", response.cartCount);

// //     // Mettre à jour le compteur du panier
// //     updateCartCount(response.cartCount);

// //     // Vérifier si le panier est maintenant vide
// //     if (response.cartCount === 0) {
// //         console.log("Panier détecté comme vide, affichage du message");

// //         // Nettoyer les éléments existants du panier
// //         const panierContent = document.querySelector('.panier-content');
// //         if (panierContent) {
// //             panierContent.remove();
// //             console.log("Contenu du panier supprimé");
// //         }

// //         // Afficher le message de panier vide
// //         showEmptyCartMessage();

// //         // Cacher tous les badges du panier
// //         document.querySelectorAll('.cart-badge, .custom-violet-badge').forEach(badge => {
// //             badge.style.visibility = 'hidden';
// //             badge.style.opacity = '0';
// //             console.log("Badge caché");
// //         });

// //         // Supprimer le bouton de réservation s'il existe
// //         const reserveButton = document.querySelector('.reserver-button');
// //         if (reserveButton) {
// //             reserveButton.remove();
// //             console.log("Bouton de réservation supprimé");
// //         }

// //         // Supprimer l'alerte de formations complètes
// //         removeCompleteFormationsWarning();
// //     } else {
// //         // Si le panier n'est pas vide, mettre à jour le résumé
// //         console.log("Panier non vide, mise à jour du résumé");
// //         updateCartSummary(response);

// //         // Vérifier s'il reste des formations complètes
// //         checkRemainingCompleteFormations();
// //     }
// // }

// // function showEmptyCartMessage() {
// //     // Ajout de logs pour déboguer
// //     console.log("Exécution de showEmptyCartMessage()");

// //     // Récupérer les éléments existants
// //     const panierContent = document.querySelector('.panier-content');
// //     const container = document.querySelector('.container');

// //     // Log pour vérifier si les éléments sont trouvés
// //     console.log("panierContent trouvé:", !!panierContent);
// //     console.log("container trouvé:", !!container);

// //     // Supprimer le contenu du panier s'il existe
// //     if (panierContent) {
// //         panierContent.remove();
// //         console.log("panierContent supprimé");
// //     }

// //     // Vérifier si le message de panier vide existe déjà
// //     const existingEmptyCart = document.querySelector('.empty-cart');
// //     if (existingEmptyCart) {
// //         console.log("Message 'panier vide' déjà présent");
// //         return; // Ne pas créer de doublon
// //     }

// //     // Créer le HTML pour le message de panier vide
// //     const emptyCartHTML = `
// //         <div class="empty-cart">
// //             <i class="fas fa-shopping-cart"></i>
// //             <p>Votre panier est vide</p>
// //             <a href="formation/formations">Découvrir des formations</a>
// //         </div>
// //     `;

// //     // Ajouter le message au container
// //     if (container) {
// //         container.innerHTML += emptyCartHTML;
// //         console.log("Message 'panier vide' ajouté au container");
// //     } else {
// //         console.error("Container non trouvé pour ajouter le message 'panier vide'");
// //     }

// //     // Supprimer l'alerte de formations complètes
// //     removeCompleteFormationsWarning();
// // }

// // function updateCartSummary(response) {
// //     if (response.cartCount === 0) {
// //         const existingButton = document.querySelector('.reserver-button');
// //         if (existingButton) {
// //             existingButton.remove();
// //         }
// //         removeCompleteFormationsWarning();
// //         return;
// //     }

// //     const totalPriceElement = document.querySelector('.total-price');
// //     if (totalPriceElement) {
// //         totalPriceElement.textContent = response.totalPrice + ' DT';
// //     }

// //     if (response.hasDiscount && parseFloat(response.discountedItemsOriginalPrice) > 0) {
// //         let originalPriceElement = document.querySelector('.original-price');
// //         let discountElement = document.querySelector('.discount-percentage');

// //         if (originalPriceElement) {
// //             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
// //         } else if (totalPriceElement) {
// //             originalPriceElement = document.createElement('div');
// //             originalPriceElement.className = 'original-price';
// //             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
// //             totalPriceElement.insertAdjacentElement('afterend', originalPriceElement);
// //         }

// //         if (discountElement) {
// //             discountElement.textContent = response.discountPercentage + '% ';
// //         } else if (originalPriceElement) {
// //             discountElement = document.createElement('div');
// //             discountElement.className = 'discount-percentage';
// //             discountElement.textContent = response.discountPercentage + '% ';
// //             originalPriceElement.insertAdjacentElement('afterend', discountElement);
// //         }
// //     } else {
// //         const originalPrice = document.querySelector('.original-price');
// //         const discountPercentage = document.querySelector('.discount-percentage');
// //         if (originalPrice) originalPrice.remove();
// //         if (discountPercentage) discountPercentage.remove();
// //     }
// // }
// // function verifyCartItemsExistence() {
// //     const cartItems = JSON.parse(localStorage.getItem('cartFormations') || '[]');
// //     if (cartItems.length === 0) return;

// //     fetch('/panier/details', {
// //         method: 'POST',
// //         headers: {
// //             'Content-Type': 'application/json',
// //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
// //             'Accept': 'application/json',
// //             'X-Requested-With': 'XMLHttpRequest'
// //         },
// //         body: JSON.stringify({
// //             items: cartItems
// //         })
// //     })
// //     .then(handleResponse)
// //     .then(response => {
// //         if (response.removed_items && response.removed_items.length > 0) {
// //             const validItems = cartItems.filter(itemId =>
// //                 !response.removed_items.includes(itemId.toString()) &&
// //                 !response.removed_items.includes(parseInt(itemId))
// //             );
// //             localStorage.setItem('cartFormations', JSON.stringify(validItems));
// //             updateCartCount(response.cartCount);

// //             if (window.location.pathname.includes('/panier')) {
// //                 response.removed_items.forEach(itemId => {
// //                     const formationItem = document.querySelector(`.formation-item[data-formation-id="${itemId}"]`);
// //                     if (formationItem) {
// //                         formationItem.remove();
// //                     }
// //                 });

// //                 if (response.cartCount === 0) {
// //                     showEmptyCartMessage();
// //                 } else {
// //                     updateCartSummary(response);
// //                 }
// //             }

// //             // Vérifier s'il reste des formations complètes après suppression
// //             checkRemainingCompleteFormations();
// //         }
// //     })
// //     .catch(error => console.error('Erreur lors de la vérification des articles du panier:', error));
// // }
// // function checkRemainingCompleteFormations() {
// //     const remainingCompleteFormations = document.querySelectorAll('.formation-full');

// //     if (remainingCompleteFormations.length === 0) {
// //         // Plus aucune formation complète, supprimer l'avertissement immédiatement
// //         removeCompleteFormationsWarning();

// //         // Réactiver le bouton de réservation s'il existe
// //         const reserverButton = document.querySelector('.reserver-button');
// //         if (reserverButton) {
// //             reserverButton.disabled = false;
// //             reserverButton.classList.remove('disabled');
// //             reserverButton.removeAttribute('title');
// //         }

// //         // Si le système de réservation est initialisé, on met à jour le statut global
// //         if (window.hasCompleteFormationsInCart !== undefined) {
// //             window.hasCompleteFormationsInCart = false;
// //         }
// //     } else {
// //         // S'il reste des formations complètes, s'assurer que l'avertissement est affiché
// //         showCompleteFormationsWarning();

// //         // Désactiver le bouton de réservation
// //         const reserverButton = document.querySelector('.reserver-button');
// //         if (reserverButton) {
// //             reserverButton.disabled = true;
// //             reserverButton.classList.add('disabled');
// //             reserverButton.title = 'Une ou plusieurs formations sont complètes';
// //         }

// //         // Si le système de réservation est initialisé, on met à jour le statut global
// //         if (window.hasCompleteFormationsInCart !== undefined) {
// //             window.hasCompleteFormationsInCart = true;
// //         }
// //     }
// // }
// // function checkFormationsAvailability() {
// //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
// //     if (!csrfToken) {
// //         console.error('CSRF token non trouvé');
// //         return;
// //     }

// //     console.log('Vérification des disponibilités en cours...');

// //     const baseUrl = window.location.origin;
// //     const url = `${baseUrl}/panier/check-availability`;

// //     fetch(url, {
// //         method: 'GET',
// //         headers: {
// //             'Accept': 'application/json',
// //             'Content-Type': 'application/json',
// //             'X-CSRF-TOKEN': csrfToken,
// //             'X-Requested-With': 'XMLHttpRequest'
// //         },
// //         credentials: 'same-origin'
// //     })
// //     .then(async response => {
// //         if (!response.ok) {
// //             const errorText = await response.text();
// //             console.error('Erreur de réponse:', response.status, errorText);
// //             throw new Error(`Erreur de réponse du serveur: ${response.status}`);
// //         }
// //         return response.json();
// //     })
// //     .then(data => {
// //         console.log('Réponse reçue de check-availability:', data);

// //         let hasCompleteFormations = false;

// //         // Réinitialiser d'abord toutes les formations
// //         document.querySelectorAll('.formation-full').forEach(item => {
// //             item.classList.remove('formation-full');
// //         });
// //         document.querySelectorAll('.formation-status-badge').forEach(badge => {
// //             badge.remove();
// //         });

// //         if (data.success && data.formations && data.formations.length > 0) {
// //             data.formations.forEach(formation => {
// //                 const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);

// //                 if (formationElement) {
// //                     // Trouver l'élément pour placer le badge
// //                     const formationTitle = formationElement.querySelector('.formation-title') ||
// //                                          formationElement.querySelector('h4') ||
// //                                          formationElement.querySelector('h3');

// //                     if (formation.is_full || formation.has_pending_reservation) {
// //                         const statusBadge = document.createElement('span');
// //                         statusBadge.className = 'formation-status-badge ml-2';

// //                         if (formation.is_full) {
// //                             console.log(`Formation ${formation.id} est COMPLÈTE`);
// //                             hasCompleteFormations = true;
// //                             statusBadge.classList.add('badge', 'badge-danger');
// //                             statusBadge.textContent = 'Complète';
// //                             formationElement.classList.add('formation-full');

// //                             // Style amélioré pour le badge
// //                             statusBadge.style.fontWeight = 'bold';
// //                             statusBadge.style.fontSize = '0.9rem';
// //                             statusBadge.style.padding = '0.3rem 0.6rem';

// //                             if (formationTitle) {
// //                                 formationTitle.appendChild(statusBadge);
// //                             } else {
// //                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
// //                             }
// //                         } else if (formation.has_pending_reservation) {
// //                             statusBadge.classList.add('badge', 'badge-warning');
// //                             statusBadge.textContent = 'Réservation en attente';

// //                             if (formationTitle) {
// //                                 formationTitle.appendChild(statusBadge);
// //                             } else {
// //                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
// //                             }
// //                         }
// //                     }
// //                 }
// //             });

// //             // Ajouter ou supprimer l'avertissement selon le statut
// //             if (hasCompleteFormations) {
// //                 showCompleteFormationsWarning();

// //                 // Désactiver le bouton de réservation si présent
// //                 const reserverButton = document.querySelector('.reserver-button');
// //                 if (reserverButton) {
// //                     reserverButton.disabled = true;
// //                     reserverButton.classList.att('disabled');
// //                     reserverButton.title = 'Une ou plusieurs formations sont complètes';
// //                 }
// //             } else {
// //                 removeCompleteFormationsWarning();

// //                 // Réactiver le bouton de réservation si présent
// //                 const reserverButton = document.querySelector('.reserver-button');
// //                 if (reserverButton) {
// //                     reserverButton.disabled = false;
// //                     reserverButton.classList.remove('disabled');
// //                     reserverButton.removeAttribute('title');
// //                 }
// //             }
// //         } else {
// //             removeCompleteFormationsWarning();
// //         }
// //     })
// //     .catch(error => {
// //         console.error('Erreur lors de la vérification de la disponibilité:', error);
// //     });
// // }

// // function handleResponse(response) {
// //     if (!response.ok) {
// //         throw new Error('Erreur réseau: ' + response.status);
// //     }
// //     return response.json();
// // }

// // function showCompleteFormationsWarning() {
// //     // Vérifier si l'avertissement existe déjà
// //     let existingWarning = document.querySelector('.complete-formations-warning');
// //     if (existingWarning) return;

// //     // Créer le conteneur d'avertissement avec une animation
// //     const warningContainer = document.createElement('div');
// //     warningContainer.className = 'complete-formations-warning';
// //     warningContainer.style.animation = 'fadeIn 0.5s';
// //     warningContainer.innerHTML = `
// //         <i class="fas fa-exclamation-triangle mr-2"></i>
// //         <strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.
// //     `;

// //     // Insérer l'avertissement au bon endroit
// //     const greenHeader = document.querySelector('.panier-header');
// //     if (greenHeader) {
// //         greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
// //     } else {
// //         const panierContent = document.querySelector('.panier-content');
// //         const container = document.querySelector('.container');

// //         if (panierContent) {
// //             panierContent.insertBefore(warningContainer, panierContent.firstChild);
// //         } else if (container) {
// //             container.insertBefore(warningContainer, container.firstChild);
// //         }
// //     }

// //     // Faire défiler vers l'avertissement
// //     warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
// // }

// // function removeCompleteFormationsWarning() {
// //     const warning = document.querySelector('.complete-formations-warning');
// //     if (warning) {
// //         warning.remove();
// //     }
// // }

// // // Ajouter des styles pour les badges et l'avertissement
// // const styleElement = document.createElement('style');
// // styleElement.textContent = `
// //     @keyframes fadeIn {
// //         from { opacity: 0; }
// //         to { opacity: 1; }
// //     }

// //     .complete-formations-warning {
// //         width: 100%;
// //         margin-bottom: 1rem;
// //         display: flex;
// //         align-items: center;
// //         text-align: center;
// //         justify-content: center;
// //         padding: 1rem;
// //         background-color: #f8d7da;
// //         color: #721c24;
// //         border: 1px solid #f5c6cb;
// //         border-radius: 4px;
// //         animation: fadeIn 0.5s;
// //     }

// //     .formation-status-badge {
// //         display: inline-block;
// //         padding: 0.25rem 0.5rem;
// //         font-size: 0.75rem;
// //         font-weight: 600;
// //         border-radius: 0.25rem;
// //         margin-left: 0.5rem;
// //     }

// //     .badge-danger {
// //         background-color: #dc3545;
// //         color: white;
// //     }

// //     .badge-warning {
// //         background-color: #ffc107;
// //         color: #212529;
// //     }

// //     .formation-full {
// //         background-color: #fff8f8;
// //     }
// // `;
// // document.head.appendChild(styleElement);

// // // Exécuter la vérification au chargement de la page
// // document.addEventListener('DOMContentLoaded', function() {
// //     // Attendez un peu pour être sûr que tout est chargé
// //     setTimeout(checkFormationsAvailability, 500);
// // });

// // // Vérifier périodiquement (toutes les 2 minutes)
// // setInterval(checkFormationsAvailability, 2 * 60 * 1000);

// // // Fonctions globales pour d'autres interactions du panier
// // window.removeFromCart = removeFromCart;
// // window.updateCartSummary = updateCartSummary;
// // window.forceUpdateCartBadge = updateCartCount;
// // window.refreshCartBadge = refreshCartBadgeWithoutReload;
// // window.fetchCartItemsCount = refreshCartBadgeWithoutReload;
// // window.updateCartCount = updateCartCount;
// // window.verifyCartItemsExistence = verifyCartItemsExistence;
// // window.checkRemainingCompleteFormations = checkRemainingCompleteFormations;
// // window.checkFormationsAvailability = checkFormationsAvailability;

// // // Ajouter à l'initialisation des listeners
// // function enhanceInitializeListeners() {
// //     const originalInitializeListeners = window.initializeListeners || function() {};

// //     window.initializeListeners = function() {
// //         originalInitializeListeners();

// //         // Vérifier les dates des formations au chargement
// //         if (typeof window.checkFormationsDates === 'function') {
// //             window.checkFormationsDates();
// //         }

// //         // Ajouter la vérification des dates après chaque action sur le panier
// //         document.addEventListener('visibilitychange', function() {
// //             if (!document.hidden) {
// //                 if (typeof window.checkFormationsDates === 'function') {
// //                     window.checkFormationsDates();
// //                 }
// //             }
// //         });
// //     };

// //     // Si la page est déjà chargée, exécuter immédiatement
// //     if (document.readyState !== 'loading') {
// //         if (typeof window.checkFormationsDates === 'function') {
// //             window.checkFormationsDates();
// //         }
// //     }
// // }

// // // Améliorer la fonction removeFromCart pour vérifier les dates après suppression
// // function enhanceRemoveFromCart() {
// //     const originalUpdateUIAfterRemoval = window.updateUIAfterRemoval || function() {};

// //     window.updateUIAfterRemoval = function(response) {
// //         originalUpdateUIAfterRemoval(response);

// //         // Vérifier s'il reste des formations expirées après suppression
// //         if (response.cartCount > 0) {
// //             if (typeof window.checkFormationsDates === 'function') {
// //                 window.checkFormationsDates();
// //             }
// //         } else {
// //             // Si le panier est vide, supprimer tous les avertissements
// //             if (typeof window.removeExpiredFormationsWarning === 'function') {
// //                 window.removeExpiredFormationsWarning();
// //             }
// //         }
// //     };
// // }

// // // Améliorer les fonctions existantes
// // enhanceInitializeListeners();
// // enhanceRemoveFromCart();

// // (function() {
// //     // Fonction pour initialiser immédiatement le badge du panier
// //     function initBadgeImmediately() {
// //         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

// //         // Mettre à jour tous les badges fixes existants
// //         const fixedBadges = document.querySelectorAll('#fixed-cart-badge, .custom-violet-badge, .cart-badge');
// //         fixedBadges.forEach(badge => {
// //             badge.textContent = cartCount.toString();
// //             badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
// //             badge.style.opacity = cartCount > 0 ? '1' : '0';
// //         });

// //         // Créer des badges pour toutes les icônes de panier si nécessaire
// //         if (cartCount > 0) {
// //             const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
// //             cartIcons.forEach(icon => {
// //                 const container = icon.closest('a, div, button, .cart-container');
// //                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                     createBadgeForContainer(container, cartCount);
// //                 }
// //             });
// //         }
// //     }
// //     // Exécuter immédiatement pour l'affichage le plus rapide possible
// //     initBadgeImmediately();
// //     // Observer le DOM pour les icônes ajoutées dynamiquement
// //     if (typeof MutationObserver !== 'undefined') {
// //         const observer = new MutationObserver(mutations => {
// //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //             if (cartCount <= 0) return;

// //             mutations.forEach(mutation => {
// //                 if (mutation.addedNodes.length) {
// //                     mutation.addedNodes.forEach(node => {
// //                         if (node.nodeType === 1) {
// //                             const icons = node.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
// //                             icons.forEach(icon => {
// //                                 const container = icon.closest('a, div, button, .cart-container');
// //                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                                     createBadgeForContainer(container, cartCount);
// //                                 }
// //                             });

// //                             if (node.matches && node.matches('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg')) {
// //                                 const container = node.closest('a, div, button, .cart-container');
// //                                 if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                                     createBadgeForContainer(container, cartCount);
// //                                 }
// //                             }
// //                         }
// //                     });
// //                 }
// //             });
// //         });

// //         observer.observe(document.documentElement, {
// //             childList: true,
// //             subtree: true
// //         });
// //     }
// //     // Injecter les styles nécessaires immédiatement
// //     const badgeElement  = document.createElement('style');
// //     badgeElement .textContent = `
// //         .cart-badge, .custom-violet-badge {
// //             position: absolute;
// //             top: -8px;
// //             right: -8px;
// //             background-color: #2563EB;
// //             color: white;
// //             border-radius: 50%;
// //             width: 18px;
// //             height: 18px;
// //             font-size: 12px;
// //             display: flex;
// //             align-items: center;
// //             justify-content: center;
// //             font-weight: bold;
// //             z-index: 10;
// //             visibility: hidden;
// //             opacity: 0;
// //         }
// //     `;
// //     document.head.appendChild(badgeElement );
// // })();
// // function createBadgeForContainer(container, count) {
// //     if (!container) return;

// //     if (container.querySelector('.cart-badge, .custom-violet-badge')) {
// //         const existingBadge = container.querySelector('.cart-badge, .custom-violet-badge');
// //         existingBadge.textContent = count.toString();
// //         existingBadge.style.visibility = count > 0 ? 'visible' : 'hidden';
// //         existingBadge.style.opacity = count > 0 ? '1' : '0';
// //         return;
// //     }

// //     let badge = document.createElement('span');
// //     badge.className = 'cart-badge custom-violet-badge';
// //     badge.textContent = count.toString();
// //     badge.style.visibility = count > 0 ? 'visible' : 'hidden';
// //     badge.style.opacity = count > 0 ? '1' : '0';

// //     if (getComputedStyle(container).position === 'static') {
// //         container.style.position = 'relative';
// //     }

// //     container.appendChild(badge);
// // }
// // (function() {
// //     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
// //     window.hasExistingReservation = hasExistingReservation;

// //     const storedCount = parseInt(localStorage.getItem('cartCount') || '0');

// //     if (storedCount > 0) {
// //         updateAllBadges(storedCount);
// //     } else {
// //         showEmptyCartMessage();
// //     }

// //     if (hasExistingReservation) {
// //         const reservationId = localStorage.getItem('reservationId');
// //         if (reservationId && typeof transformReserverButton === 'function') {
// //             transformReserverButton(parseInt(reservationId));
// //         }
// //     }

// //     synchronizeWithServer();

// //     if (document.readyState === 'loading') {
// //         document.addEventListener('DOMContentLoaded', initializeListeners);
// //     } else {
// //         initializeListeners();
// //     }
// // })();

// // function synchronizeWithServer() {
// //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
// //     if (!csrfToken) return;

// //     fetch('/panier/count', {
// //         method: 'GET',
// //         headers: {
// //             'Accept': 'application/json',
// //             'X-CSRF-TOKEN': csrfToken,
// //             'X-Requested-With': 'XMLHttpRequest'
// //         }
// //     })
// //     .then(response => response.json())
// //     .then(data => {
// //         const count = data.count || 0;
// //         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');

// //         if (oldCount !== count) {
// //             localStorage.setItem('cartCount', count.toString());
// //             updateAllBadges(count);
// //         }
// //     })
// //     .catch(error => console.error('Erreur lors de la récupération du compteur:', error));
// // }
// // function updateAllBadges(count) {
// //     const badges = document.querySelectorAll('.cart-badge, .custom-violet-badge, #fixed-cart-badge');
// //     badges.forEach(badge => {
// //         badge.textContent = count.toString();
// //         badge.style.visibility = count > 0 ? 'visible' : 'hidden';
// //         badge.style.opacity = count > 0 ? '1' : '0';
// //     });

// //     if (count > 0) {
// //         const cartIcons = document.querySelectorAll('.shopping-cart-icon, svg[data-icon="shopping-cart"], .cart-icon, a[href*="panier"] svg, .cart-container svg');
// //         cartIcons.forEach(icon => {
// //             const container = icon.closest('a, div, button, .cart-container');
// //             if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
// //                 createBadgeForContainer(container, count);
// //             }
// //         });
// //     }
// // }
// // function initializeListeners() {
// //     setupRemoveFromCartListeners();

// //     // CORRECTION : Vérifier l'état du panier au chargement initial
// //     const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //     if (cartCount === 0 && window.location.pathname.includes('/panier')) {
// //         showEmptyCartMessage();
// //     }

// //     setInterval(refreshCartBadgeWithoutReload, 5000);
// //     setInterval(verifyCartItemsExistence, 120000);

// //     document.addEventListener('visibilitychange', function() {
// //         if (!document.hidden) {
// //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //             updateAllBadges(cartCount);
// //             refreshCartBadgeWithoutReload();
// //             verifyCartItemsExistence();

// //             // CORRECTION : Vérifier l'état du panier quand l'utilisateur revient sur la page
// //             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
// //                 showEmptyCartMessage();
// //             }
// //         }
// //     });

// //     const oldXHROpen = window.XMLHttpRequest.prototype.open;
// //     window.XMLHttpRequest.prototype.open = function() {
// //         this.addEventListener('load', function() {
// //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //             updateAllBadges(cartCount);

// //             // CORRECTION : Vérifier l'état du panier après chaque requête XHR
// //             if (cartCount === 0 && window.location.pathname.includes('/panier')) {
// //                 showEmptyCartMessage();
// //             }
// //         });
// //         return oldXHROpen.apply(this, arguments);
// //     };
// // }
// // function setupRemoveFromCartListeners() {
// //     document.addEventListener('click', function(e) {
// //         const removeLink = e.target.closest('.remove-link');
// //         if (removeLink) {
// //             e.preventDefault();
// //             const formationId = removeLink.getAttribute('data-formation-id');
// //             if (formationId) {
// //                 removeFromCart(formationId);
// //             }
// //         }
// //     });
// // }
// // function refreshCartBadgeWithoutReload() {
// //     fetch('/panier/items-count', {
// //         method: 'GET',
// //         headers: {
// //             'X-Requested-With': 'XMLHttpRequest',
// //             'Accept': 'application/json'
// //         }
// //     })
// //     .then(handleResponse)
// //     .then(data => {
// //         const count = parseInt(data.count) || 0;
// //         const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
// //         if (oldCount !== count) {
// //             localStorage.setItem('cartCount', count.toString());
// //             updateAllBadges(count);
// //         }

// //         const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
// //         if (count > 0 && !hasExistingReservation) {
// //             /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
// //                 createReserverButtonEarly();
// //             } */
// //         } else if (count === 0 && !hasExistingReservation) {
// //             document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
// //                 btn.remove();
// //             });
// //         } else if (hasExistingReservation) {
// //             const reservationId = localStorage.getItem('reservationId');
// //             if (reservationId && typeof transformReserverButton === 'function') {
// //                 transformReserverButton(parseInt(reservationId));
// //             }
// //         }
// //     })
// //     .catch(error => console.error('Erreur:', error));
// // }
// // function updateCartCount(count) {
// //     count = parseInt(count) || 0;
// //     window.globalCartCount = count;
// //     localStorage.setItem('cartCount', count.toString());
// //     updateAllBadges(count);

// //     var panierCountElements = document.querySelectorAll('.panier-count');
// //     panierCountElements.forEach(function(el) {
// //         el.textContent = count + ' formation(s)';
// //         el.style.opacity = '1';
// //     });

// //     const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
// //     if (count > 0 && !hasExistingReservation) {
// //         /* if (!document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
// //             createReserverButtonEarly();
// //         } */
// //     } else if (count === 0 && !hasExistingReservation) {
// //         document.querySelectorAll('.reserver-button, .reserver-button-preloaded').forEach(btn => {
// //             btn.remove();
// //         });
// //     } else if (hasExistingReservation) {
// //         const reservationId = localStorage.getItem('reservationId');
// //         if (reservationId && typeof transformReserverButton === 'function') {
// //             transformReserverButton(parseInt(reservationId));
// //         }
// //     }
// // }

// // function removeFromCart(formationId) {
// //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
// //     if (!csrfToken) {
// //         console.error('CSRF token non trouvé');
// //         return;
// //     }

// //     const baseUrl = window.location.origin;

// //     fetch(`${baseUrl}/panier/supprimer`, {
// //         method: 'POST',
// //         headers: {
// //             'Content-Type': 'application/json',
// //             'X-CSRF-TOKEN': csrfToken,
// //             'Accept': 'application/json'
// //         },
// //         body: JSON.stringify({
// //             formation_id: formationId
// //         })
// //     })
// //     .then(handleResponse)
// //     .then(response => {
// //         // Vérifier si la réponse est valide avant de continuer
// //         if (!response || !response.success) {
// //             console.error(response?.message || 'Erreur lors de la suppression de la formation');
// //             return;
// //         }

// //         const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
// //         const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
// //         localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
// //         localStorage.setItem('cartCount', response.cartCount.toString());
// //         updateCartCount(response.cartCount);

// //         const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
// //         if (formationItem) {
// //             // Vérifier si la formation supprimée était complète
// //             const wasCompleteFormation = formationItem.classList.contains('formation-full');

// //             formationItem.remove();
// //             updateUIAfterRemoval(response);

// //             // Si la formation supprimée était complète, vérifier immédiatement s'il reste des formations complètes
// //             if (wasCompleteFormation) {
// //                 checkRemainingCompleteFormations();
// //             }
// //         }

// //         if (response.cartCount === 0) {
// //             showEmptyCartMessage();
// //             removeCompleteFormationsWarning(); // Supprimer l'alerte immédiatement si le panier est vide
// //         }
// //     })
// //     .catch(error => {
// //         console.error('Erreur lors de la suppression:', error);
// //     });
// // }
// // function updateUIAfterRemoval(response) {
// //     // Ajout de logs pour debugging
// //     console.log("updateUIAfterRemoval appelé avec:", response);
// //     console.log("Nouveau cartCount:", response.cartCount);

// //     // Mettre à jour le compteur du panier
// //     updateCartCount(response.cartCount);

// //     // Vérifier si le panier est maintenant vide
// //     if (response.cartCount === 0) {
// //         console.log("Panier détecté comme vide, affichage du message");

// //         // Nettoyer les éléments existants du panier
// //         const panierContent = document.querySelector('.panier-content');
// //         if (panierContent) {
// //             panierContent.remove();
// //             console.log("Contenu du panier supprimé");
// //         }

// //         // Afficher le message de panier vide
// //         showEmptyCartMessage();

// //         // Cacher tous les badges du panier
// //         document.querySelectorAll('.cart-badge, .custom-violet-badge').forEach(badge => {
// //             badge.style.visibility = 'hidden';
// //             badge.style.opacity = '0';
// //             console.log("Badge caché");
// //         });

// //         // Supprimer le bouton de réservation s'il existe
// //         const reserveButton = document.querySelector('.reserver-button');
// //         if (reserveButton) {
// //             reserveButton.remove();
// //             console.log("Bouton de réservation supprimé");
// //         }

// //         // Supprimer l'alerte de formations complètes
// //         removeCompleteFormationsWarning();
// //     } else {
// //         // Si le panier n'est pas vide, mettre à jour le résumé
// //         console.log("Panier non vide, mise à jour du résumé");
// //         updateCartSummary(response);

// //         // Vérifier s'il reste des formations complètes
// //         checkRemainingCompleteFormations();
// //     }
// // }

// // function showEmptyCartMessage() {
// //     // Ajout de logs pour déboguer
// //     console.log("Exécution de showEmptyCartMessage()");

// //     // Récupérer les éléments existants
// //     const panierContent = document.querySelector('.panier-content');
// //     const container = document.querySelector('.container');

// //     // Log pour vérifier si les éléments sont trouvés
// //     console.log("panierContent trouvé:", !!panierContent);
// //     console.log("container trouvé:", !!container);

// //     // Supprimer le contenu du panier s'il existe
// //     if (panierContent) {
// //         panierContent.remove();
// //         console.log("panierContent supprimé");
// //     }

// //     // Vérifier si le message de panier vide existe déjà
// //     const existingEmptyCart = document.querySelector('.empty-cart');
// //     if (existingEmptyCart) {
// //         console.log("Message 'panier vide' déjà présent");
// //         return; // Ne pas créer de doublon
// //     }

// //     // Créer le HTML pour le message de panier vide
// //     const emptyCartHTML = `
// //         <div class="empty-cart">
// //             <i class="fas fa-shopping-cart"></i>
// //             <p>Votre panier est vide</p>
// //             <a href="formation/formations">Découvrir des formations</a>
// //         </div>
// //     `;

// //     // Ajouter le message au container
// //     if (container) {
// //         container.innerHTML += emptyCartHTML;
// //         console.log("Message 'panier vide' ajouté au container");
// //     } else {
// //         console.error("Container non trouvé pour ajouter le message 'panier vide'");
// //     }

// //     // Supprimer l'alerte de formations complètes si présente
// //     removeCompleteFormationsWarning();
// // }

// // function updateCartSummary(response) {
// //     if (response.cartCount === 0) {
// //         const existingButton = document.querySelector('.reserver-button');
// //         if (existingButton) {
// //             existingButton.remove();
// //         }
// //         removeCompleteFormationsWarning();
// //         return;
// //     }

// //     const totalPriceElement = document.querySelector('.total-price');
// //     if (totalPriceElement) {
// //         totalPriceElement.textContent = response.totalPrice + ' DT';
// //     }

// //     if (response.hasDiscount && parseFloat(response.discountedItemsOriginalPrice) > 0) {
// //         let originalPriceElement = document.querySelector('.original-price');
// //         let discountElement = document.querySelector('.discount-percentage');

// //         if (originalPriceElement) {
// //             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
// //         } else if (totalPriceElement) {
// //             originalPriceElement = document.createElement('div');
// //             originalPriceElement.className = 'original-price';
// //             originalPriceElement.textContent = response.discountedItemsOriginalPrice + ' DT';
// //             totalPriceElement.insertAdjacentElement('afterend', originalPriceElement);
// //         }

// //         if (discountElement) {
// //             discountElement.textContent = response.discountPercentage + '% ';
// //         } else if (originalPriceElement) {
// //             discountElement = document.createElement('div');
// //             discountElement.className = 'discount-percentage';
// //             discountElement.textContent = response.discountPercentage + '% ';
// //             originalPriceElement.insertAdjacentElement('afterend', discountElement);
// //         }
// //     } else {
// //         const originalPrice = document.querySelector('.original-price');
// //         const discountPercentage = document.querySelector('.discount-percentage');
// //         if (originalPrice) originalPrice.remove();
// //         if (discountPercentage) discountPercentage.remove();
// //     }
// // }
// // function verifyCartItemsExistence() {
// //     const cartItems = JSON.parse(localStorage.getItem('cartFormations') || '[]');
// //     if (cartItems.length === 0) return;

// //     fetch('/panier/details', {
// //         method: 'POST',
// //         headers: {
// //             'Content-Type': 'application/json',
// //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
// //             'Accept': 'application/json',
// //             'X-Requested-With': 'XMLHttpRequest'
// //         },
// //         body: JSON.stringify({
// //             items: cartItems
// //         })
// //     })
// //     .then(handleResponse)
// //     .then(response => {
// //         if (response.removed_items && response.removed_items.length > 0) {
// //             const validItems = cartItems.filter(itemId =>
// //                 !response.removed_items.includes(itemId.toString()) &&
// //                 !response.removed_items.includes(parseInt(itemId))
// //             );
// //             localStorage.setItem('cartFormations', JSON.stringify(validItems));
// //             updateCartCount(response.cartCount);

// //             if (window.location.pathname.includes('/panier')) {
// //                 response.removed_items.forEach(itemId => {
// //                     const formationItem = document.querySelector(`.formation-item[data-formation-id="${itemId}"]`);
// //                     if (formationItem) {
// //                         formationItem.remove();
// //                     }
// //                 });

// //                 if (response.cartCount === 0) {
// //                     showEmptyCartMessage();
// //                 } else {
// //                     updateCartSummary(response);
// //                 }
// //             }

// //             // Vérifier s'il reste des formations complètes après suppression
// //             checkRemainingCompleteFormations();
// //         }
// //     })
// //     .catch(error => console.error('Erreur lors de la vérification des articles du panier:', error));
// // }
// // function checkRemainingCompleteFormations() {
// //     const remainingCompleteFormations = document.querySelectorAll('.formation-full');

// //     if (remainingCompleteFormations.length === 0) {
// //         // Plus aucune formation complète, supprimer l'avertissement immédiatement
// //         removeCompleteFormationsWarning();

// //         // Réactiver le bouton de réservation s'il existe
// //         const reserverButton = document.querySelector('.reserver-button');
// //         if (reserverButton) {
// //             reserverButton.disabled = false;
// //             reserverButton.classList.remove('disabled');
// //             reserverButton.removeAttribute('title');
// //         }

// //         // Si le système de réservation est initialisé, on met à jour le statut global
// //         if (window.hasCompleteFormationsInCart !== undefined) {
// //             window.hasCompleteFormationsInCart = false;
// //         }
// //     } else {
// //         // S'il reste des formations complètes, s'assurer que l'avertissement est affiché
// //         showCompleteFormationsWarning();

// //         // Désactiver le bouton de réservation
// //         const reserverButton = document.querySelector('.reserver-button');
// //         if (reserverButton) {
// //             reserverButton.disabled = true;
// //             reserverButton.classList.add('disabled');
// //             reserverButton.title = 'Une ou plusieurs formations sont complètes';
// //         }

// //         // Si le système de réservation est initialisé, on met à jour le statut global
// //         if (window.hasCompleteFormationsInCart !== undefined) {
// //             window.hasCompleteFormationsInCart = true;
// //         }
// //     }
// // }
// // function checkFormationsAvailability() {
// //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
// //     if (!csrfToken) {
// //         console.error('CSRF token non trouvé');
// //         return;
// //     }

// //     console.log('Vérification des disponibilités en cours...');

// //     const baseUrl = window.location.origin;
// //     const url = `${baseUrl}/panier/check-availability`;

// //     fetch(url, {
// //         method: 'GET',
// //         headers: {
// //             'Accept': 'application/json',
// //             'Content-Type': 'application/json',
// //             'X-CSRF-TOKEN': csrfToken,
// //             'X-Requested-With': 'XMLHttpRequest'
// //         },
// //         credentials: 'same-origin'
// //     })
// //     .then(async response => {
// //         if (!response.ok) {
// //             const errorText = await response.text();
// //             console.error('Erreur de réponse:', response.status, errorText);
// //             throw new Error(`Erreur de réponse du serveur: ${response.status}`);
// //         }
// //         return response.json();
// //     })
// //     .then(data => {
// //         console.log('Réponse reçue de check-availability:', data);

// //         let hasCompleteFormations = false;

// //         // Réinitialiser d'abord toutes les formations
// //         document.querySelectorAll('.formation-full').forEach(item => {
// //             item.classList.remove('formation-full');
// //         });
// //         document.querySelectorAll('.formation-status-badge').forEach(badge => {
// //             badge.remove();
// //         });

// //         if (data.success && data.formations && data.formations.length > 0) {
// //             data.formations.forEach(formation => {
// //                 const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);

// //                 if (formationElement) {
// //                     // Trouver l'élément pour placer le badge
// //                     const formationTitle = formationElement.querySelector('.formation-title') ||
// //                                          formationElement.querySelector('h4') ||
// //                                          formationElement.querySelector('h3');

// //                     if (formation.is_full || formation.has_pending_reservation) {
// //                         const statusBadge = document.createElement('span');
// //                         statusBadge.className = 'formation-status-badge ml-2';

// //                         if (formation.is_full) {
// //                             console.log(`Formation ${formation.id} est COMPLÈTE`);
// //                             hasCompleteFormations = true;
// //                             statusBadge.classList.add('badge', 'badge-danger');
// //                             statusBadge.textContent = 'Complète';
// //                             formationElement.classList.add('formation-full');

// //                             // Style amélioré pour le badge
// //                             statusBadge.style.fontWeight = 'bold';
// //                             statusBadge.style.fontSize = '0.9rem';
// //                             statusBadge.style.padding = '0.3rem 0.6rem';

// //                             if (formationTitle) {
// //                                 formationTitle.appendChild(statusBadge);
// //                             } else {
// //                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
// //                             }
// //                         } else if (formation.has_pending_reservation) {
// //                             statusBadge.classList.add('badge', 'badge-warning');
// //                             statusBadge.textContent = 'Réservation en attente';

// //                             if (formationTitle) {
// //                                 formationTitle.appendChild(statusBadge);
// //                             } else {
// //                                 formationElement.insertAdjacentElement('afterbegin', statusBadge);
// //                             }
// //                         }
// //                     }
// //                 }
// //             });

// //             // Ajouter ou supprimer l'avertissement selon le statut
// //             if (hasCompleteFormations) {
// //                 showCompleteFormationsWarning();

// //                 // Désactiver le bouton de réservation si présent
// //                 const reserverButton = document.querySelector('.reserver-button');
// //                 if (reserverButton) {
// //                     reserverButton.disabled = true;
// //                     reserverButton.classList.add('disabled');
// //                     reserverButton.title = 'Une ou plusieurs formations sont complètes';
// //                 }
// //             } else {
// //                 removeCompleteFormationsWarning();

// //                 // Réactiver le bouton de réservation si présent
// //                 const reserverButton = document.querySelector('.reserver-button');
// //                 if (reserverButton) {
// //                     reserverButton.disabled = false;
// //                     reserverButton.classList.remove('disabled');
// //                     reserverButton.removeAttribute('title');
// //                 }
// //             }
// //         } else {
// //             removeCompleteFormationsWarning();
// //         }
// //     })
// //     .catch(error => {
// //         console.error('Erreur lors de la vérification de la disponibilité:', error);
// //     });
// // }

// // function handleResponse(response) {
// //     if (!response.ok) {
// //         throw new Error('Erreur réseau: ' + response.status);
// //     }
// //     return response.json();
// // }

// // function showCompleteFormationsWarning() {
// //     // Vérifier si l'avertissement existe déjà
// //     let existingWarning = document.querySelector('.complete-formations-warning');
// //     if (existingWarning) return;

// //     // Créer le conteneur d'avertissement avec une animation
// //     const warningContainer = document.createElement('div');
// //     warningContainer.className = 'complete-formations-warning';
// //     warningContainer.style.animation = 'fadeIn 0.5s';
// //     warningContainer.innerHTML = `
// //         <i class="fas fa-exclamation-triangle mr-2"></i>
// //         <strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.
// //     `;

// //     // Insérer l'avertissement au bon endroit
// //     const greenHeader = document.querySelector('.panier-header');
// //     if (greenHeader) {
// //         greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
// //     } else {
// //         const panierContent = document.querySelector('.panier-content');
// //         const container = document.querySelector('.container');

// //         if (panierContent) {
// //             panierContent.insertBefore(warningContainer, panierContent.firstChild);
// //         } else if (container) {
// //             container.insertBefore(warningContainer, container.firstChild);
// //         }
// //     }

// //     // Faire défiler vers l'avertissement
// //     warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
// // }

// // function removeCompleteFormationsWarning() {
// //     const warning = document.querySelector('.complete-formations-warning');
// //     if (warning) {
// //         warning.remove();
// //     }
// // }

// // // Ajouter des styles pour les badges et l'avertissement
// // const styleElement = document.createElement('style');
// // styleElement.textContent = `
// //     @keyframes fadeIn {
// //         from { opacity: 0; }
// //         to { opacity: 1; }
// //     }

// //     .complete-formations-warning {
// //         width: 100%;
// //         margin-bottom: 1rem;
// //         display: flex;
// //         align-items: center;
// //         text-align: center;
// //         justify-content: center;
// //         padding: 1rem;
// //         background-color: #f8d7da;
// //         color: #721c24;
// //         border: 1px solid #f5c6cb;
// //         border-radius: 4px;
// //         animation: fadeIn 0.5s;
// //     }

// //     .formation-status-badge {
// //         display: inline-block;
// //         padding: 0.25rem 0.5rem;
// //         font-size: 0.75rem;
// //         font-weight: 600;
// //         border-radius: 0.25rem;
// //         margin-left: 0.5rem;
// //     }

// //     .badge-danger {
// //         background-color: #dc3545;
// //         color: white;
// //     }

// //     .badge-warning {
// //         background-color: #ffc107;
// //         color: #212529;
// //     }

// //     .formation-full {
// //         background-color: #fff8f8;
// //     }
// // `;
// // document.head.appendChild(styleElement);

// // // Exécuter la vérification au chargement de la page
// // document.addEventListener('DOMContentLoaded', function() {
// //     // Attendez un peu pour être sûr que tout est chargé
// //     setTimeout(checkFormationsAvailability, 500);
// // });

// // // Vérifier périodiquement (toutes les 2 minutes)
// // setInterval(checkFormationsAvailability, 2 * 60 * 1000);

// // // Fonctions globales pour d'autres interactions du panier
// // window.removeFromCart = removeFromCart;
// // window.updateCartSummary = updateCartSummary;
// // window.forceUpdateCartBadge = updateCartCount;
// // window.refreshCartBadge = refreshCartBadgeWithoutReload;
// // window.fetchCartItemsCount = refreshCartBadgeWithoutReload;
// // window.updateCartCount = updateCartCount;
// // window.verifyCartItemsExistence = verifyCartItemsExistence;
// // window.checkRemainingCompleteFormations = checkRemainingCompleteFormations;
// // window.checkFormationsAvailability = checkFormationsAvailability;

// // // Fonction pour vérifier les dates des formations dans le panier
// // function checkFormationsDates() {
// //     const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
// //     if (!csrfToken) {
// //         console.error('CSRF token non trouvé');
// //         return;
// //     }

// //     console.log('Vérification des dates des formations en cours...');

// //     // Récupérer les détails du panier
// //     const baseUrl = window.location.origin;
// //     const url = `${baseUrl}/panier/details`;

// //     fetch(url, {
// //         method: 'GET',
// //         headers: {
// //             'Accept': 'application/json',
// //             'Content-Type': 'application/json',
// //             'X-CSRF-TOKEN': csrfToken,
// //             'X-Requested-With': 'XMLHttpRequest'
// //         },
// //         credentials: 'same-origin'
// //     })
// //     .then(async response => {
// //         if (!response.ok) {
// //             const errorText = await response.text();
// //             console.error('Erreur de réponse:', response.status, errorText);
// //             throw new Error(`Erreur de réponse du serveur: ${response.status}`);
// //         }
// //         return response.json();
// //     })
// //     .then(data => {
// //         console.log('Réponse reçue pour les détails du panier:', data);

// //         if (!data.success || !data.trainings || data.trainings.length === 0) {
// //             console.log('Aucune formation dans le panier');
// //             removeExpiredFormationsWarning();
// //             return;
// //         }

// //         let hasExpiredFormations = false;
// //         const today = new Date();
// //         today.setHours(0, 0, 0, 0); // Comparer seulement les dates sans l'heure

// //         // Réinitialiser d'abord toutes les formations
// //         document.querySelectorAll('.formation-expired').forEach(item => {
// //             item.classList.remove('formation-expired');
// //         });
// //         document.querySelectorAll('.formation-expired-badge').forEach(badge => {
// //             badge.remove();
// //         });

// //         // Parcourir toutes les formations dans le panier
// //         data.trainings.forEach(formation => {
// //             const startDate = new Date(formation.start_date);
// //             const formationElement = document.querySelector(`.formation-item[data-formation-id="${formation.id}"]`);

// //             if (formationElement) {
// //                 // Trouver l'élément pour placer le badge
// //                 const formationTitle = formationElement.querySelector('.formation-title') ||
// //                                      formationElement.querySelector('h4') ||
// //                                      formationElement.querySelector('h3');

// //                 if (startDate < today) {
// //                     // La date de formation est dépassée
// //                     console.log(`Formation ${formation.id} a une date dépassée: ${formation.start_date}`);
// //                     hasExpiredFormations = true;

// //                     const statusBadge = document.createElement('span');
// //                     statusBadge.className = 'formation-status-badge formation-expired-badge ml-2';
// //                     statusBadge.classList.add('badge', 'badge-secondary');
// //                     statusBadge.textContent = 'Date dépassée';

// //                     // Style amélioré pour le badge
// //                     statusBadge.style.fontWeight = 'bold';
// //                     statusBadge.style.fontSize = '0.9rem';
// //                     statusBadge.style.padding = '0.3rem 0.6rem';

// //                     formationElement.classList.add('formation-expired');

// //                     if (formationTitle) {
// //                         formationTitle.appendChild(statusBadge);
// //                     } else {
// //                         formationElement.insertAdjacentElement('afterbegin', statusBadge);
// //                     }
// //                 }
// //             }
// //         });

// //         // Ajouter ou supprimer l'avertissement selon le statut
// //         if (hasExpiredFormations) {
// //             showExpiredFormationsWarning();

// //             // Désactiver le bouton de réservation si présent
// //             const reserverButton = document.querySelector('.reserver-button');
// //             if (reserverButton) {
// //                 reserverButton.disabled = true;
// //                 reserverButton.classList.add('disabled');
// //                 reserverButton.title = 'Votre panier contient des formations dont la date est dépassée';
// //             }
// //         } else {
// //             removeExpiredFormationsWarning();

// //             // Vérifier si le bouton doit être activé (si pas de formations complètes)
// //             const hasCompleteFormations = document.querySelectorAll('.formation-full').length > 0;
// //             if (!hasCompleteFormations) {
// //                 const reserverButton = document.querySelector('.reserver-button');
// //                 if (reserverButton) {
// //                     reserverButton.disabled = false;
// //                     reserverButton.classList.remove('disabled');
// //                     reserverButton.removeAttribute('title');
// //                 }
// //             }
// //         }
// //     })
// //     .catch(error => {
// //         console.error('Erreur lors de la vérification des dates des formations:', error);
// //     });
// // }

// // // Fonction pour afficher l'avertissement de formations expirées
// // function showExpiredFormationsWarning() {
// //     // Vérifier si l'avertissement existe déjà
// //     let existingWarning = document.querySelector('.expired-formations-warning');
// //     if (existingWarning) return;

// //     // Créer le conteneur d'avertissement
// //     const warningContainer = document.createElement('div');
// //     warningContainer.className = 'expired-formations-warning';
// //     warningContainer.style.animation = 'fadeIn 0.5s';
// //     warningContainer.innerHTML = `
// //         <i class="fas fa-calendar-times mr-2"></i>
// //         <strong>Attention:</strong> Votre panier contient des formations dont la date est dépassée. Veuillez les supprimer pour poursuivre votre réservation.
// //     `;

// //     // Insérer l'avertissement au bon endroit
// //     const completeWarning = document.querySelector('.complete-formations-warning');
// //     const greenHeader = document.querySelector('.panier-header');

// //     if (completeWarning) {
// //         completeWarning.parentNode.insertBefore(warningContainer, completeWarning.nextSibling);
// //     } else if (greenHeader) {
// //         greenHeader.parentNode.insertBefore(warningContainer, greenHeader);
// //     } else {
// //         const panierContent = document.querySelector('.panier-content');
// //         const container = document.querySelector('.container');

// //         if (panierContent) {
// //             panierContent.insertBefore(warningContainer, panierContent.firstChild);
// //         } else if (container) {
// //             container.insertBefore(warningContainer, container.firstChild);
// //         }
// //     }

// //     // Faire défiler vers l'avertissement
// //     warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
// // }

// // // Fonction pour supprimer l'avertissement de formations expirées
// // function removeExpiredFormationsWarning() {
// //     const warning = document.querySelector('.expired-formations-warning');
// //     if (warning) {
// //         warning.remove();
// //     }
// // }

// // // Ajouter des styles pour les badges et l'avertissement
// // const expiredStyleElement = document.createElement('style');
// // expiredStyleElement.textContent = `
// //     .expired-formations-warning {
// //         width: 100%;
// //         margin-bottom: 1rem;
// //         display: flex;
// //         align-items: center;
// //         text-align: center;
// //         justify-content: center;
// //         padding: 1rem;
// //         background-color: #e9ecef;
// //         color: #495057;
// //         border: 1px solid #ced4da;
// //         border-radius: 4px;
// //         animation: fadeIn 0.5s;
// //     }

// //     .badge-secondary {
// //         background-color: #6c757d;
// //         color: white;
// //     }

// //     .formation-expired {
// //         background-color: #f8f9fa;
// //         opacity: 0.8;
// //     }
// // `;
// // document.head.appendChild(expiredStyleElement);

// // // Exécuter la vérification au chargement de la page
// // document.addEventListener('DOMContentLoaded', function() {
// //     // Attendre un peu pour être sûr que tout est chargé
// //     setTimeout(checkFormationsDates, 800);
// // });

// // // Vérifier périodiquement (toutes les 2 minutes)
// // setInterval(checkFormationsDates, 2 * 60 * 1000);

// // // Ajouter à l'initialisation des listeners
// // function enhanceInitializeListeners() {
// //     const originalInitializeListeners = window.initializeListeners || function() {};

// //     window.initializeListeners = function() {
// //         originalInitializeListeners();

// //         // Vérifier les dates des formations au chargement
// //         checkFormationsDates();

// //         // Ajouter la vérification des dates après chaque action sur le panier
// //         document.addEventListener('visibilitychange', function() {
// //             if (!document.hidden) {
// //                 checkFormationsDates();
// //             }
// //         });
// //     };

// //     // Si la page est déjà chargée, exécuter immédiatement
// //     if (document.readyState !== 'loading') {
// //         checkFormationsDates();
// //     }
// // }

// // // Améliorer la fonction removeFromCart pour vérifier les dates après suppression
// // function enhanceRemoveFromCart() {
// //     const originalUpdateUIAfterRemoval = window.updateUIAfterRemoval || function() {};

// //     window.updateUIAfterRemoval = function(response) {
// //         originalUpdateUIAfterRemoval(response);

// //         // Vérifier s'il reste des formations expirées après suppression
// //         if (response.cartCount > 0) {
// //             checkFormationsDates();
// //         } else {
// //             // Si le panier est vide, supprimer tous les avertissements
// //             removeExpiredFormationsWarning();
// //         }
// //     };
// // }

// // // Exposer les fonctions globalement
// // window.checkFormationsDates = checkFormationsDates;
// // window.showExpiredFormationsWarning = showExpiredFormationsWarning;
// // window.removeExpiredFormationsWarning = removeExpiredFormationsWarning;

// // // Améliorer les fonctions existantes
// // enhanceInitializeListeners();
// // enhanceRemoveFromCart();


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



const CART_ICON_SELECTORS = [
  '.shopping-cart-icon',
  'svg[data-icon="shopping-cart"]',
  '.cart-icon',
  'a[href*="panier"] svg',
  '.cart-container svg',
  '.cart-link',
  '.panier-icon'
].join(', ');
const BADGE_SELECTORS = '.cart-badge, .custom-violet-badge, #fixed-cart-badge';
const pendingRequests = {};
const processedCompleteFormationIds = new Set(); // Suivre les formations complètes déjà marquées
const styleElement = document.createElement('style');
styleElement.textContent = `
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
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
    transition: opacity 0.2s, visibility 0.2s;
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
  .formation-full {
    background-color: #fff8f8;
    border-left: 4px solid #f8d7da;
  }
  .removing {
    transition: opacity 0.3s ease-out;
  }
`;
document.head.appendChild(styleElement);

// Gestion des réponses HTTP
function handleResponse(response) {
  if (!response.ok) {
    throw new Error(`Erreur réseau: ${response.status}`);
  }
  return response.json();
}

// Mettre à jour les badges du panier
function updateCartBadges(count) {
  count = parseInt(count) || 0;
  // Mettre à jour les badges existants
  const badges = document.querySelectorAll(BADGE_SELECTORS);
  badges.forEach(badge => {
    badge.textContent = count.toString();
    badge.style.display = count > 0 ? 'flex' : 'none';
    badge.style.visibility = count > 0 ? 'visible' : 'hidden';
    badge.style.opacity = count > 0 ? '1' : '0';
  });
  // Créer des badges pour les icônes sans badge si count > 0
  if (count > 0) {
    const cartIcons = document.querySelectorAll(CART_ICON_SELECTORS);
    cartIcons.forEach(icon => {
      const container = icon.closest('a, div, button, .cart-container');
      if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
        const badge = document.createElement('span');
        badge.className = 'cart-badge custom-violet-badge';
        badge.textContent = count.toString();
        badge.style.position = 'absolute';
        badge.style.top = '-8px';
        badge.style.right = '-8px';
        badge.style.display = 'flex';
        badge.style.visibility = 'visible';
        badge.style.opacity = '1';
        if (getComputedStyle(container).position === 'static') {
          container.style.position = 'relative';
        }
        container.appendChild(badge);
      }
    });
  }
}

// Synchroniser le compteur du panier avec le serveur
function syncCartCount() {
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
    .then(handleResponse)
    .then(data => {
      const count = parseInt(data.count) || 0;
      const oldCount = parseInt(localStorage.getItem('cartCount') || '0');
      if (oldCount !== count) {
        localStorage.setItem('cartCount', count.toString());
        updateCartBadges(count);
      }
      if (count === 0 && window.location.pathname.includes('/panier')) {
        showEmptyCartMessage();
      }
    })
    .catch(error => console.error('Erreur lors de la synchronisation du compteur:', error));
}

function updateCartUI(data) {
  updateCartBadges(data.cartCount);

  // Mettre à jour les compteurs
  const cartCountElements = document.querySelectorAll('.cart-count, .panier-count');
  cartCountElements.forEach(element => {
    if (data.cartCount > 0) {
      element.textContent = `${data.cartCount} formation(s)`;
      element.style.display = '';
      element.style.opacity = '1';
      element.classList.remove('empty');
    } else {
      element.style.display = 'none';
      element.classList.add('empty');
    }
  });

  // Mettre à jour les totaux
  const totalPriceElement = document.querySelector('.cart-total-price, .total-price');
  if (totalPriceElement) {
    totalPriceElement.textContent = data.totalPrice ? `${data.totalPrice} DT` : '0 DT';
  }

  const originalPriceElement = document.querySelector('.original-price');
  const discountElement = document.querySelector('.discount-percentage');

  if (data.hasDiscount && data.discountedItemsOriginalPrice) {
    if (originalPriceElement) {
      originalPriceElement.textContent = `${data.discountedItemsOriginalPrice} DT`;
    } else if (totalPriceElement) {
      const newOriginalPrice = document.createElement('div');
      newOriginalPrice.className = 'original-price';
      newOriginalPrice.textContent = `${data.discountedItemsOriginalPrice} DT`;
      totalPriceElement.insertAdjacentElement('afterend', newOriginalPrice);
    }

    if (discountElement) {
      discountElement.textContent = `${data.discountPercentage}%`;
    } else if (originalPriceElement) {
      const newDiscount = document.createElement('div');
      newDiscount.className = 'discount-percentage';
      newDiscount.textContent = `${data.discountPercentage}%`;
      originalPriceElement.insertAdjacentElement('afterend', newDiscount);
    }
  } else {
    if (originalPriceElement) originalPriceElement.remove();
    if (discountElement) discountElement.remove();
  }

  // Afficher le message de panier vide si nécessaire
  if (data.cartCount === 0) {
    showEmptyCartMessage();
  }
}

// Fonction pour vérifier le statut des formations dans le panier
function checkCartItemsStatus() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrfToken) {
    console.error('CSRF token non trouvé');
    return;
  }

  console.log('Vérification des formations dans le panier...');

  // Récupérer d'abord les formations dans le panier
  fetch('/panier/items', {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
    .then(handleResponse)
    .then(data => {
      if (data.items && Array.isArray(data.items)) {
        console.log('Formations dans le panier:', data.items);

        let hasCompleteFormations = false;
        let completedChecks = 0;
        const totalFormations = data.items.length;

        // Nettoyer les formations qui ne sont plus dans le panier
        const currentFormationIds = new Set(data.items.map(id => id.toString()));
        processedCompleteFormationIds.forEach(id => {
          if (!currentFormationIds.has(id)) {
            processedCompleteFormationIds.delete(id);
            console.log(`Formation ${id} supprimée de processedCompleteFormationIds`);
          }
        });

        if (totalFormations === 0) {
          // Aucune formation dans le panier
          removeCompleteFormationsWarning();
          const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
          if (reserverButton) {
            reserverButton.disabled = false;
            reserverButton.classList.remove('disabled');
            reserverButton.removeAttribute('title');
          }
          processedCompleteFormationIds.clear();
          return;
        }

        // Vérifier chaque formation individuellement
        data.items.forEach(formationId => {
          fetch(`/get-remaining-seats/${formationId}`, {
            method: 'GET',
            headers: {
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
            .then(handleResponse)
            .then(formationData => {
              console.log(`Données pour formation ${formationId}:`, formationData);

              completedChecks++;

              const formationElement = document.querySelector(
                `.formation-item[data-formation-id="${formationId}"], ` +
                `.cart-item[data-formation-id="${formationId}"], ` +
                `tr[data-formation-id="${formationId}"]`
              );

              if (formationData.success && formationData.is_complete) {
                console.log('Formation complète détectée:', formationId);
                hasCompleteFormations = true;

                if (formationElement) {
                  // Vérifier si le badge existe déjà ou si la formation est déjà marquée
                  const existingBadge = formationElement.querySelector('.formation-status-badge.badge-danger');
                  if (existingBadge || processedCompleteFormationIds.has(formationId.toString())) {
                    console.log(`Badge "Complète" déjà présent ou formation ${formationId} déjà traitée`);
                    processedCompleteFormationIds.add(formationId.toString());
                  } else {
                    // Sélectionner le titre de la formation
                    const formationTitle = formationElement.querySelector(
                      '.formation-title, h4, h3, .cart-item-title, td:first-child'
                    );

                    // Créer le badge "Complète"
                    const statusBadge = document.createElement('span');
                    statusBadge.className = 'formation-status-badge badge badge-danger';
                    statusBadge.textContent = 'Complète';
                    statusBadge.style.backgroundColor = '#dc3545';
                    statusBadge.style.color = 'white';
                    statusBadge.style.display = 'inline-flex';
                    statusBadge.style.alignItems = 'center';
                    statusBadge.style.justifyContent = 'center';
                    statusBadge.style.fontWeight = 'bold';
                    statusBadge.style.fontSize = '0.8rem';
                    statusBadge.style.padding = '0.3rem 0.6rem';
                    statusBadge.style.borderRadius = '0.25rem';
                    statusBadge.style.marginLeft = '0.5rem';

                    // Ajouter une classe à l'élément de formation
                    formationElement.classList.add('formation-full');

                    // Ajouter le style visuel
                    formationElement.style.backgroundColor = '#fff8f8';
                    formationElement.style.borderLeft = '4px solid #f8d7da';

                    // Ajouter le badge au titre
                    if (formationTitle) {
                      formationTitle.appendChild(statusBadge);
                    } else {
                      formationElement.insertAdjacentElement('afterbegin', statusBadge);
                    }

                    processedCompleteFormationIds.add(formationId.toString());
                    console.log(`Badge "Complète" ajouté pour formation ${formationId}`);
                  }
                } else {
                  console.log('Élément formation non trouvé pour ID:', formationId);
                }
              } else if (formationElement) {
                // Supprimer la classe et le style si la formation n'est plus complète
                formationElement.classList.remove('formation-full');
                formationElement.style.backgroundColor = '';
                formationElement.style.borderLeft = '';
                const existingBadge = formationElement.querySelector('.formation-status-badge.badge-danger');
                if (existingBadge) {
                  existingBadge.remove();
                }
                processedCompleteFormationIds.delete(formationId.toString());
              }
              if (completedChecks === totalFormations) {
  const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
  if (hasCompleteFormations) {
  console.log('Des formations complètes détectées');

  // N'afficher l'avertissement que sur la page panier
  if (window.location.pathname.includes('/panier')) {
    showCompleteFormationsWarning();
  }

  // Vérifier s'il y a une réservation avec status=0 avant de désactiver le bouton
  const hasReservationStatus0 = window.hasExistingReservation && localStorage.getItem('reservationStatus') === '0';

  if (reserverButton && !hasReservationStatus0) {
    reserverButton.disabled = true;
    reserverButton.classList.add('disabled');
    reserverButton.title = 'Une ou plusieurs formations sont complètes';
  }
  window.hasCompleteFormationsInCart = true;
}


  // if (hasCompleteFormations) {
  //   console.log('Des formations complètes détectées');

  //   // N'afficher l'avertissement que sur la page panier
  //   if (window.location.pathname.includes('/panier')) {
  //     showCompleteFormationsWarning();
  //   }

  //   if (reserverButton) {
  //     reserverButton.disabled = true;
  //     reserverButton.classList.add('disabled');
  //     reserverButton.title = 'Une ou plusieurs formations sont complètes';
  //   }
  //   window.hasCompleteFormationsInCart = true;
  // }
  else {
    console.log('Aucune formation complète');

    // Supprimer l'avertissement seulement si on est sur la page panier
    if (window.location.pathname.includes('/panier')) {
      removeCompleteFormationsWarning();
    }

    if (reserverButton) {
      reserverButton.disabled = false;
      reserverButton.classList.remove('disabled');
      reserverButton.removeAttribute('title');
    }
    window.hasCompleteFormationsInCart = false;
  }
}

              // Vérifier si toutes les formations ont été traitées
              // if (completedChecks === totalFormations) {
              //   // Gérer l'avertissement et le bouton de réservation
              //   const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');

              //   if (hasCompleteFormations) {
              //     console.log('Des formations complètes détectées, affichage de l\'avertissement');
              //     showCompleteFormationsWarning();
              //     if (reserverButton) {
              //       reserverButton.disabled = true;
              //       reserverButton.classList.add('disabled');
              //       reserverButton.title = 'Une ou plusieurs formations sont complètes';
              //     }
              //     window.hasCompleteFormationsInCart = true;
              //   } else {
              //     console.log('Aucune formation complète, suppression de l\'avertissement');
              //     removeCompleteFormationsWarning();
              //     if (reserverButton) {
              //       reserverButton.disabled = false;
              //       reserverButton.classList.remove('disabled');
              //       reserverButton.removeAttribute('title');
              //     }
              //     window.hasCompleteFormationsInCart = false;
              //   }
              // }
            })
            .catch(error => {
              console.error(`Erreur lors de la vérification de la formation ${formationId}:`, error);
              completedChecks++;

              // Si toutes les vérifications sont terminées (même avec erreurs)
              if (completedChecks === totalFormations) {
                const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');

                if (hasCompleteFormations) {
                  showCompleteFormationsWarning();
                  if (reserverButton) {
                    reserverButton.disabled = true;
                    reserverButton.classList.add('disabled');
                    reserverButton.title = 'Une ou plusieurs formations sont complètes';
                  }
                } else {
                  removeCompleteFormationsWarning();
                  if (reserverButton) {
                    reserverButton.disabled = false;
                    reserverButton.classList.remove('disabled');
                    reserverButton.removeAttribute('title');
                  }
                }
              }
            });
        });
      }
    })
    .catch(error => {
      console.error('Erreur lors de la récupération des formations du panier:', error);
    });
}

// Nouvelle fonction pour vérifier une formation individuelle (si nécessaire)
function checkSingleFormationStatus(formationId, callback = null) {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrfToken) {
    console.error('CSRF token non trouvé');
    if (callback) callback(null);
    return;
  }

  fetch(`/get-remaining-seats/${formationId}`, {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'X-Requested-With': 'XMLHttpRequest'
    }
  })
    .then(handleResponse)
    .then(data => {
      if (callback) {
        callback({
          success: data.success,
          isComplete: data.is_complete,
          remainingSeats: data.remaining_seats,
          totalSeats: data.total_seats
        });
      }
    })
    .catch(error => {
      console.error(`Erreur lors de la vérification de la formation ${formationId}:`, error);
      if (callback) callback(null);
    });
}

// Fonction utilitaire pour afficher les informations de places disponibles
function displaySeatsInfo(formationId, containerId = null) {
  checkSingleFormationStatus(formationId, (result) => {
    if (result && result.success) {
      const container = containerId ?
        document.getElementById(containerId) :
        document.querySelector(`.formation-item[data-formation-id="${formationId}"] .seats-info`);

      if (container) {
        const seatsText = result.isComplete ?
          `Complet (${result.totalSeats}/${result.totalSeats})` :
          `${result.totalSeats - result.remainingSeats}/${result.totalSeats} places occupées`;

        container.textContent = seatsText;
        container.className = result.isComplete ? 'badge badge-danger' : 'badge badge-success';
      }
    }
  });
}
function showCompleteFormationsWarning() {
  // Vérifier si on est sur la page panier
  if (!window.location.pathname.includes('/panier')) {
    console.log('Pas sur la page panier, alerte non affichée');
    return;
  }

  let existingWarning = document.querySelector('.complete-formations-warning');
  if (existingWarning) return;

  console.log('Création de l\'avertissement pour les formations complètes dans la page panier');

  const warningContainer = document.createElement('div');
  warningContainer.className = 'complete-formations-warning';
  warningContainer.style.animation = 'fadeIn 0.5s';
  warningContainer.style.width = '100%';
  warningContainer.style.marginBottom = '1rem';
  warningContainer.style.display = 'flex';
  warningContainer.style.alignItems = 'center';
  warningContainer.style.justifyContent = 'center';
  warningContainer.style.padding = '1rem';
  warningContainer.style.backgroundColor = '#f8d7da';
  warningContainer.style.color = '#721c24';
  warningContainer.style.border = '1px solid #f5c6cb';
  warningContainer.style.borderRadius = '4px';

  warningContainer.innerHTML = `
    <i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>
    <strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.
  `;

  // Insertion spécifique pour la page panier - après le titre et le compteur
  const panierTitle = document.querySelector('h1, .panier-title, h2');
  const panierCount = document.querySelector('.panier-count, .cart-count');

  let insertAfter = null;

  // Chercher l'élément de comptage s'il existe
  if (panierCount) {
    insertAfter = panierCount;
  } else if (panierTitle) {
    insertAfter = panierTitle;
  }

  if (insertAfter) {
    insertAfter.parentNode.insertBefore(warningContainer, insertAfter.nextSibling);
    console.log('Avertissement inséré après:', insertAfter);
  } else {
    // Fallback: insérer avant le contenu principal du panier
    const panierContent = document.querySelector('.panier-content, .cart-content, .container');
    if (panierContent) {
      panierContent.insertBefore(warningContainer, panierContent.firstChild);
      console.log('Avertissement inséré au début du contenu panier');
    }
  }

  // Scroll vers l'avertissement pour s'assurer qu'il est visible
  warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// Amélioration de la fonction showCompleteFormationsWarning
// function showCompleteFormationsWarning() {
//   let existingWarning = document.querySelector('.complete-formations-warning');
//   if (existingWarning) return;

//   console.log('Création de l\'avertissement pour les formations complètes');

//   const warningContainer = document.createElement('div');
//   warningContainer.className = 'complete-formations-warning';
//   warningContainer.style.animation = 'fadeIn 0.5s';
//   warningContainer.style.width = '100%';
//   warningContainer.style.marginBottom = '1rem';
//   warningContainer.style.display = 'flex';
//   warningContainer.style.alignItems = 'center';
//   warningContainer.style.justifyContent = 'center';
//   warningContainer.style.padding = '1rem';
//   warningContainer.style.backgroundColor = '#f8d7da';
//   warningContainer.style.color = '#721c24';
//   warningContainer.style.border = '1px solid #f5c6cb';
//   warningContainer.style.borderRadius = '4px';

//   warningContainer.innerHTML = `
//     <i class="fas fa-exclamation-triangle" style="margin-right: 0.5rem;"></i>
//     <strong>Attention:</strong> Vous devez supprimer les formations complètes pour poursuivre votre réservation.
//   `;

//   // Insertion de l'avertissement à un endroit visible
//   const possibleContainers = [
//     '.panier-header',
//     '.panier-content',
//     '.cart-container',
//     '.container',
//     'main',
//     'body'
//   ];

//   let inserted = false;
//   for (const selector of possibleContainers) {
//     const container = document.querySelector(selector);
//     if (container) {
//       container.parentNode.insertBefore(warningContainer, container);
//       inserted = true;
//       console.log('Avertissement inséré avant:', selector);
//       break;
//     }
//   }

//   if (!inserted) {
//     document.body.insertBefore(warningContainer, document.body.firstChild);
//     console.log('Avertissement inséré au début du body');
//   }

//   // Scroll vers l'avertissement pour s'assurer qu'il est visible
//   warningContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
// }

function showEmptyCartMessage() {
  console.log('Affichage du message de panier vide amélioré');

  // Supprimer le contenu existant du panier mais PAS l'en-tête
  const panierContent = document.querySelector('.panier-content');
  if (panierContent) {
    panierContent.remove();
    console.log('Contenu du panier supprimé');
  }

  // Vérifier si le message existe déjà
  const existingEmptyCart = document.querySelector('.empty-cart-container');
  if (existingEmptyCart) {
    console.log('Message "panier vide" déjà présent');
    return;
  }

  // Supprimer l'ancien message s'il existe (version Blade ou ancienne version JS)
  const oldEmptyCart = document.querySelector('.empty-cart');
  const emptyCartPlaceholder = document.querySelector('.empty-cart-placeholder');

  if (oldEmptyCart) {
    oldEmptyCart.remove();
  }
  if (emptyCartPlaceholder) {
    emptyCartPlaceholder.remove();
  }

  // Créer le nouveau message de panier vide avec le design de l'image
  const emptyCartHTML = `
    <div class="empty-cart-container">
      <div class="empty-cart-icon">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <h2 class="empty-cart-title">Votre panier est vide</h2>
      <p class="empty-cart-subtitle">
        Découvrez nos formations exceptionnelles et commencez votre parcours d'apprentissage dès aujourd'hui
      </p>
      <a href="/formation/formations" class="discover-btn">
        Découvrir nos formations
        <i class="fas fa-arrow-right"></i>
      </a>
    </div>
  `;

  // Trouver le container approprié - S'assurer de chercher après l'en-tête
  const panierHeader = document.querySelector('.panier-header');
  if (panierHeader) {
    panierHeader.insertAdjacentHTML('afterend', emptyCartHTML);
    console.log('Message "panier vide" ajouté après l\'en-tête');
  } else {
    // Fallback si l'en-tête n'existe pas
    let targetContainer = null;

    // Rechercher dans l'ordre de priorité
    const containerSelectors = [
      '.container',
      '#app',
      'main',
      'body'
    ];

    for (const selector of containerSelectors) {
      const element = document.querySelector(selector);
      if (element) {
        targetContainer = element;
        break;
      }
    }

    if (targetContainer) {
      targetContainer.insertAdjacentHTML('beforeend', emptyCartHTML);
      console.log('Message "panier vide" ajouté au container');
    } else {
      console.error('Container non trouvé pour ajouter le message "panier vide"');
    }
  }

  // Masquer le compteur de panier
  const panierCount = document.querySelector('.panier-count');
  if (panierCount) {
    panierCount.style.display = 'none';
  }

  // Supprimer l'avertissement des formations complètes
  removeCompleteFormationsWarning();

  // Animation d'entrée
  const emptyCartElement = document.querySelector('.empty-cart-container');
  if (emptyCartElement) {
    emptyCartElement.style.opacity = '0';
    emptyCartElement.style.transform = 'translateY(30px)';

    requestAnimationFrame(() => {
      emptyCartElement.style.transition = 'all 0.6s ease-out';
      emptyCartElement.style.opacity = '1';
      emptyCartElement.style.transform = 'translateY(0)';
    });
  }

  // Ajouter les styles CSS dynamiquement si ils n'existent pas
  addEmptyCartStyles();
}

// Fonction pour ajouter les styles CSS qui reproduisent l'apparence de l'image
function addEmptyCartStyles() {
  // Vérifier si les styles existent déjà
  if (document.querySelector('#empty-cart-styles')) {
    return;
  }

  const style = document.createElement('style');
  style.id = 'empty-cart-styles';
  style.textContent = `
    .empty-cart-container {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      padding: 2rem 0;
      background-color: transparent;
      margin: 0;
      text-align: left;
    }

    .empty-cart-icon {
      margin-bottom: 1.5rem;
    }

    .empty-cart-icon i {
      font-size: 2.5rem;
      color: #374151;
    }

    .empty-cart-title {
      font-size: 2rem;
      font-weight: 700;
      color: #374151;
      margin-bottom: 1rem;
      line-height: 1.2;
    }

    .empty-cart-subtitle {
      font-size: 1rem;
      color: #6b7280;
      margin-bottom: 2rem;
      line-height: 1.6;
      max-width: 600px;
    }

    .discover-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.75rem 1.5rem;
      background-color: transparent;
      color: #3b82f6;
      text-decoration: none;
      border: none;
      font-weight: 500;
      font-size: 1rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .discover-btn:hover {
      color: #2563eb;
      text-decoration: none;
    }

    .discover-btn i {
      font-size: 0.875rem;
      margin-left: 0.25rem;
    }

    /* Animation keyframes */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .empty-cart-container {
      animation: fadeInUp 0.6s ease-out;
    }
  `;

  document.head.appendChild(style);
}

// Fonction pour initialiser le message de panier vide si nécessaire
function initializeEmptyCartDisplay() {
  // Vérifier si on est sur la page panier
  if (window.location.pathname.includes('/panier')) {
    const panierContent = document.querySelector('.panier-content');
    const emptyCartPlaceholder = document.querySelector('.empty-cart-placeholder');
    const oldEmptyCart = document.querySelector('.empty-cart');

    // Si aucun contenu de panier n'est trouvé OU si le placeholder existe
    // OU si l'ancien message existe, afficher le nouveau message
    if (!panierContent || emptyCartPlaceholder || oldEmptyCart) {
      showEmptyCartMessage();
    }
  }
}

// Supprimer l'avertissement pour les formations complètes
function removeCompleteFormationsWarning() {
  const warning = document.querySelector('.complete-formations-warning');
  if (warning) warning.remove();
}

// Afficher une notification
function showNotification(message, type = 'info') {
  if (typeof toast !== 'undefined' && toast.show) {
    toast.show(message, type);
  } else if (type === 'error') {
    alert('Erreur: ' + message);
  } else {
    alert(message);
  }
}

// Mettre à jour le bouton d'ajout au panier
function updateAddToCartButton(formationId, inCart, isComplete = false) {
  const buttons = document.querySelectorAll(`.addcart-btn .btn[href="/panier"][data-formation-id="${formationId}"], .addcart-btn .btn[href="/panier"][data-category-id="${formationId}"]`);
  buttons.forEach(button => {
    if (inCart) {
      button.setAttribute('data-in-cart', 'true');
      button.textContent = 'Voir le panier';
      button.disabled = false;
    } else {
      button.removeAttribute('data-in-cart');
      button.textContent = 'Ajouter au panier';
      button.disabled = isComplete;
    }
  });
}

// Vérifier les formations dans le panier
function checkFormationsInCart() {
  const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
  const lastAddedFormation = localStorage.getItem('lastAddedFormation');
  if (lastAddedFormation) {
    console.log('Formation précédemment ajoutée:', lastAddedFormation);
    updateAddToCartButton(lastAddedFormation, true, false);
    if (!cartFormations.includes(lastAddedFormation)) {
      cartFormations.push(lastAddedFormation);
      localStorage.setItem('cartFormations', JSON.stringify(cartFormations));
    }
    localStorage.removeItem('lastAddedFormation');
  }
  cartFormations.forEach(formationId => {
    updateAddToCartButton(formationId, true, false);
  });
  document.querySelectorAll('.formation-item, .product-box').forEach(item => {
    const formationId = item.getAttribute('data-category-id') || item.getAttribute('data-formation-id');
    if (formationId) {
      const inCart = cartFormations.includes(formationId.toString());
      let isComplete = false;
      const seatsInfo = item.querySelector('.badge-light-success, .badge-light-danger');
      if (seatsInfo) {
        const seatsText = seatsInfo.textContent.trim();
        const matches = seatsText.match(/(\d+)\/(\d+)/);
        if (matches && matches.length === 3) {
          isComplete = parseInt(matches[1], 10) >= parseInt(matches[2], 10);
        }
      }
      updateAddToCartButton(formationId, inCart, isComplete);
    }
  });
  fetch('/panier/items', {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    }
  })
    .then(handleResponse)
    .then(data => {
      if (data.items && Array.isArray(data.items)) {
        const serverFormationIds = data.items.map(item => item.toString());
        localStorage.setItem('cartFormations', JSON.stringify(serverFormationIds));
        document.querySelectorAll('.formation-item, .product-box').forEach(item => {
          const formationId = item.getAttribute('data-category-id') || item.getAttribute('data-formation-id');
          if (formationId) {
            const inCart = serverFormationIds.includes(formationId.toString());
            let isComplete = false;
            const seatsInfo = item.querySelector('.badge-light-success, .badge-light-danger');
            if (seatsInfo) {
              const seatsText = seatsInfo.textContent.trim();
              const matches = seatsText.match(/(\d+)\/(\d+)/);
              if (matches && matches.length === 3) {
                isComplete = parseInt(matches[1], 10) >= parseInt(matches[2], 10);
              }
            }
            updateAddToCartButton(formationId, inCart, isComplete);
          }
        });
        if (serverFormationIds.length !== parseInt(localStorage.getItem('cartCount') || '0')) {
          localStorage.setItem('cartCount', serverFormationIds.length.toString());
          updateCartBadges(serverFormationIds.length);
        }
      }
    })
    .catch(error => console.error('Erreur lors de la vérification du panier:', error));
}

// Ajouter une formation au panier
function addToCart(formationId, redirectToCart = false, callback = null) {
  const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
  if (cartFormations.includes(formationId.toString())) {
    showNotification('Cette formation est déjà dans votre panier', 'info');
    if (callback) callback();
    return;
  }
  const currentCartCount = parseInt(localStorage.getItem('cartCount') || '0');
  const newCartCount = currentCartCount + 1;
  localStorage.setItem('cartCount', newCartCount.toString());
  updateCartBadges(newCartCount);
  updateAddToCartButton(formationId, true);

  fetch('/panier/ajouter', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    },
    body: JSON.stringify({ training_id: formationId })
  })
    .then(handleResponse)
    .then(data => {
      if (data.success) {
        localStorage.setItem('cartCount', data.cartCount.toString());
        if (!cartFormations.includes(formationId.toString())) {
          cartFormations.push(formationId.toString());
          localStorage.setItem('cartFormations', JSON.stringify(cartFormations));
        }
        updateCartBadges(data.cartCount);
        updateAddToCartButton(formationId, true);
        showNotification(data.message, 'success');
        if (redirectToCart) {
          // window.location.href = '/panier';
        }
      } else {
        localStorage.setItem('cartCount', currentCartCount.toString());
        updateCartBadges(currentCartCount);
        updateAddToCartButton(formationId, false);
        showNotification(data.message, 'error');
      }
      if (callback) callback();
    })
    .catch(error => {
      console.error('Erreur lors de l\'ajout au panier:', error);
      localStorage.setItem('cartCount', currentCartCount.toString());
      updateCartBadges(currentCartCount);
      updateAddToCartButton(formationId, false);
      showNotification('Erreur lors de l\'ajout au panier', 'error');
      if (callback) callback();
    });
}

function removeFromCart(formationId, callback = null) {
  const formationItem = document.querySelector(`.formation-item[data-formation-id="${formationId}"]`);
  if (formationItem) {
    formationItem.classList.add('removing');
    formationItem.style.opacity = '0.5';
  }
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  if (!csrfToken) {
    console.error('CSRF token non trouvé');
    if (formationItem) {
      formationItem.classList.remove('removing');
      formationItem.style.opacity = '1';
    }
    if (callback) callback();
    return;
  }
  fetch('/panier/supprimer', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': csrfToken,
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({ formation_id: formationId })
  })
    .then(handleResponse)
    .then(data => {
      if (data.success) {
        const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
        const updatedFormations = cartFormations.filter(id => id !== formationId.toString());
        localStorage.setItem('cartFormations', JSON.stringify(updatedFormations));
        localStorage.setItem('cartCount', data.cartCount.toString());
        if (formationItem) {
          formationItem.style.transition = 'all 0.05s ease-out';
          formationItem.remove();
        }
        updateCartUI(data);
        processedCompleteFormationIds.delete(formationId.toString()); // Supprimer de processedCompleteFormationIds

        // Vérifier si la formation supprimée était complète
        const wasComplete = processedCompleteFormationIds.has(formationId.toString()) ||
                            (formationItem && formationItem.querySelector('.formation-status-badge.badge-danger'));

        // Vérifier s'il reste des formations complètes dans le panier
        let hasRemainingCompleteFormations = false;
        const remainingFormations = document.querySelectorAll('.formation-item');
        remainingFormations.forEach(item => {
          const id = item.getAttribute('data-formation-id');
          if (id && (processedCompleteFormationIds.has(id) || item.querySelector('.formation-status-badge.badge-danger'))) {
            hasRemainingCompleteFormations = true;
          }
        });

        // Si la formation supprimée était complète et qu'il n'y a plus de formations complètes, supprimer l'alerte immédiatement
        // if (wasComplete && !hasRemainingCompleteFormations) {
        //   console.log('Aucune formation complète restante, suppression immédiate de l\'avertissement');
        //   removeCompleteFormationsWarning();
        //   const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
        //   if (reserverButton) {
        //     reserverButton.disabled = false;
        //     reserverButton.classList.remove('disabled');
        //     reserverButton.removeAttribute('title');
        //   }
        //   window.hasCompleteFormationsInCart = false;
        // }
        if (wasComplete && !hasRemainingCompleteFormations && window.location.pathname.includes('/panier')) {
  console.log('Aucune formation complète restante, suppression immédiate de l\'avertissement');
  removeCompleteFormationsWarning();
  const reserverButton = document.querySelector('.reserver-button, #reserver-btn, .btn-reserver');
  if (reserverButton) {
    reserverButton.disabled = false;
    reserverButton.classList.remove('disabled');
    reserverButton.removeAttribute('title');
  }
  window.hasCompleteFormationsInCart = false;
}

        // Continuer la vérification asynchrone pour les autres mises à jour
        checkCartItemsStatus();

        // Cacher l'en-tête du panier si le panier est vide
        if (data.cartCount === 0) {
          const panierHeader = document.querySelector('.panier-header');
          if (panierHeader) {
            panierHeader.style.display = 'none';
          }
        }
      } else {
        if (formationItem) {
          formationItem.classList.remove('removing');
          formationItem.style.opacity = '1';
        }
      }
      if (callback) callback();
    })
    .catch(error => {
      console.error('Erreur lors de la suppression:', error);
      if (formationItem) {
        formationItem.classList.remove('removing');
        formationItem.style.opacity = '1';
      }
      showNotification('Erreur lors de la suppression', 'error');
      if (callback) callback();
    });
}

// Initialisation du panier
function initializeCart() {
   initializeEmptyCartDisplay();
  const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
  updateCartBadges(cartCount);
  syncCartCount();
  checkCartItemsStatus();
  if (cartCount === 0 && window.location.pathname.includes('/panier')) {
    showEmptyCartMessage();
  }
  // Gestion des réservations existantes
  const hasExistingReservation = localStorage.getItem('hasExistingReservation') === 'true';
  window.hasExistingReservation = hasExistingReservation;
  if (hasExistingReservation) {
    const reservationId = localStorage.getItem('reservationId');
    if (reservationId && typeof transformReserverButton === 'function') {
      transformReserverButton(parseInt(reservationId));
    }
  }

  // Délégation d'événements
document.addEventListener('click', event => {
  const addToCartBtn = event.target.closest('.addcart-btn .btn[href="/panier"]');
  const removeLink = event.target.closest('.remove-link');

  if (removeLink) {
    event.preventDefault(); // Appel immédiat de preventDefault

    if (!removeLink.classList.contains('processing')) {
      const formationId = removeLink.getAttribute('data-formation-id');
      if (formationId && !pendingRequests[formationId]) {
        removeLink.classList.add('processing');
        pendingRequests[formationId] = true;
        removeFromCart(formationId, () => {
          removeLink.classList.remove('processing');
          delete pendingRequests[formationId];
        });
      }
    }
  }

  if (addToCartBtn && !addToCartBtn.classList.contains('processing') && !addToCartBtn.disabled) {
    event.preventDefault(); // Empêche la redirection par défaut

    if (addToCartBtn.getAttribute('data-in-cart') === 'true') {
      window.location.href = '/panier';
      return;
    }

    addToCartBtn.classList.add('processing');
    let formationId = addToCartBtn.closest('.modal-content')?.closest('.modal').id.split('-').pop() ||
      addToCartBtn.closest('.formation-item, .product-box')?.getAttribute('data-formation-id') ||
      addToCartBtn.closest('.formation-item, .product-box')?.getAttribute('data-category-id');

    if (formationId && !pendingRequests[formationId]) {
      pendingRequests[formationId] = true;
      addToCart(formationId, false, () => {  // Notez le "false" ici pour ne pas rediriger
        addToCartBtn.classList.remove('processing');
        delete pendingRequests[formationId];
      });
    } else {
      addToCartBtn.classList.remove('processing');
    }
  }
    if (removeLink && !removeLink.classList.contains('processing')) {
      event.preventDefault();
      const formationId = removeLink.getAttribute('data-formation-id');
      if (formationId && !pendingRequests[formationId]) {
        removeLink.classList.add('processing');
        pendingRequests[formationId] = true;
        removeFromCart(formationId, () => {
          removeLink.classList.remove('processing');
          delete pendingRequests[formationId];
        });
      }
    }
  });
  // Observer les modifications du DOM pour les icônes ajoutées dynamiquement
  if (typeof MutationObserver !== 'undefined') {
    const observer = new MutationObserver(mutations => {
      const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
      if (cartCount <= 0) return;
      mutations.forEach(mutation => {
        if (mutation.addedNodes.length) {
          mutation.addedNodes.forEach(node => {
            if (node.nodeType === 1) {
              const icons = node.querySelectorAll(CART_ICON_SELECTORS);
              icons.forEach(icon => {
                const container = icon.closest('a, div, button, .cart-container');
                if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                  const badge = document.createElement('span');
                  badge.className = 'cart-badge custom-violet-badge';
                  badge.textContent = cartCount.toString();
                  badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
                  badge.style.opacity = cartCount > 0 ? '1' : '0';
                  if (getComputedStyle(container).position === 'static') {
                    container.style.position = 'relative';
                  }
                  container.appendChild(badge);
                }
              });
              if (node.matches && node.matches(CART_ICON_SELECTORS)) {
                const container = node.closest('a, div, button, .cart-container');
                if (container && !container.querySelector('.cart-badge, .custom-violet-badge')) {
                  const badge = document.createElement('span');
                  badge.className = 'cart-badge custom-violet-badge';
                  badge.textContent = cartCount.toString();
                  badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
                  badge.style.opacity = cartCount > 0 ? '1' : '0';
                  if (getComputedStyle(container).position === 'static') {
                    container.style.position = 'relative';
                  }
                  container.appendChild(badge);
                }
              }
            }
          });
        }
      });
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });
  }
  // Vérifications périodiques
  setInterval(syncCartCount, 5000);
  setInterval(checkCartItemsStatus, 120000);
  // Gestion des changements de visibilité
  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
      syncCartCount();
      checkCartItemsStatus();
      if (parseInt(localStorage.getItem('cartCount') || '0') === 0 && window.location.pathname.includes('/panier')) {
        showEmptyCartMessage();
      }
    }
  });
  // Interception des requêtes XHR
  const oldXHROpen = window.XMLHttpRequest.prototype.open;
  window.XMLHttpRequest.prototype.open = function () {
    this.addEventListener('load', () => {
      syncCartCount();
      if (parseInt(localStorage.getItem('cartCount') || '0') === 0 && window.location.pathname.includes('/panier')) {
        showEmptyCartMessage();
      }
    });
    return oldXHROpen.apply(this, arguments);
  };
  // Vérification initiale des formations
  checkFormationsInCart();
}

// Exécuter l'initialisation
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeCart);
} else {
  initializeCart();
}

// Exposer les fonctions globales
window.removeFromCart = removeFromCart;
window.updateCartCount = (count) => {
  localStorage.setItem('cartCount', count.toString());
  updateCartBadges(count);
};
window.checkCartItemsStatus = checkCartItemsStatus;
window.addToCart = addToCart;
window.showNotification = showNotification;
window.checkFormationsInCart = checkFormationsInCart;
