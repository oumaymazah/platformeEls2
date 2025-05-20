
// (function() {
//     const currentPage = window.location.pathname; // Exemple: '/ma-page-specifique'

//     // Liste des pages où le spinner doit apparaître
//     const allowedPages = [
//         '/panier',
//     ];

//     // Si nous ne sommes pas sur une page autorisée, ne rien faire
//     if (!allowedPages.includes(currentPage)) {
//         return;
//     }

//     // Variables globales pour suivre l'état des réservations
//     window.hasExistingReservation = false;
//     window.checkingReservations = true; // Commencer avec l'état de vérification activé
//     window.buttonsInitialized = false;  // Nouvelle variable pour suivre si les boutons ont été initialisés
//     window.reservationStatus = 0;      // Nouvelle variable pour suivre le statut de la réservation
//     window.hasCompleteFormationsInCart = false; // Nouvelle variable pour suivre si des formations complètes sont dans le panier

//     // Fonction pour créer et afficher le spinner de chargement
//     function showLoadingSpinner() {
//         // Créer le conteneur du spinner
//         const spinnerContainer = document.createElement('div');
//         spinnerContainer.id = 'reservation-spinner';
//         spinnerContainer.style.cssText = `
//             position: fixed;
//             top: 0;
//             left: 0;
//             width: 100%;
//             height: 100%;
//             display: flex;
//             justify-content: center;
//             align-items: center;
//             background-color: rgba(255, 255, 255, 0.8);
//             z-index: 9999;
//         `;

//         // Créer la structure du spinner selon votre modèle
//         const spinnerCard = document.createElement('div');
//         spinnerCard.className = 'container-fluid';
//         spinnerCard.innerHTML = `
//         <div class="card-body row justify-content-center">
//             <div class="col-sm-6 col-md-3">
//                 <div class="loader-box">
//                     <div class="loader-19"></div>
//                 </div>
//             </div>
//         </div>
//     `;

//         // Ajouter le spinner au conteneur
//         spinnerContainer.appendChild(spinnerCard);

//         // Ajouter le spinner au body
//         document.body.appendChild(spinnerContainer);
//     }

//     // Fonction pour masquer le spinner
//     function hideLoadingSpinner() {
//         const spinner = document.getElementById('reservation-spinner');
//         if (spinner) {
//             spinner.style.transition = 'opacity 0.3s ease';
//             spinner.style.opacity = '0';

//             // Retirer le spinner après la transition
//             setTimeout(() => {
//                 spinner.remove();
//             }, 300);
//         }
//     }

//     // Afficher le spinner immédiatement
//     showLoadingSpinner();

//     // Masquer tout contenu du panier jusqu'à la vérification complète
//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', function() {
//             hideCartContent();
//             initReservationSystem();
//         });
//     } else {
//         hideCartContent();
//         initReservationSystem();
//     }

//     function hideCartContent() {
//         // Masquer temporairement le contenu du panier pendant la vérification
//         const cartContent = document.querySelector('.panier-content') ||
//                           document.querySelector('.total-container');
//         if (cartContent) {
//             cartContent.style.opacity = '0';
//             cartContent.style.transition = 'opacity 0.2s';
//         }

//         // Masquer également l'en-tête du panier
//         const panierHeader = document.querySelector('.panier-header');
//         if (panierHeader) {
//             panierHeader.style.opacity = '0';
//             panierHeader.style.transition = 'opacity 0.2s';
//         }

//         // Masquer le conteneur d'application Vue
//         const appContainer = document.querySelector('#app[data-formations-url]');
//         if (appContainer) {
//             appContainer.style.opacity = '0';
//             appContainer.style.transition = 'opacity 0.2s';
//         }
//     }

//     function showCartContent() {
//         // Afficher le contenu du panier une fois la vérification terminée
//         const cartContent = document.querySelector('.panier-content') ||
//                           document.querySelector('.total-container');
//         if (cartContent) {
//             cartContent.style.opacity = '1';
//         }

//         // Afficher également l'en-tête du panier
//         const panierHeader = document.querySelector('.panier-header');
//         if (panierHeader) {
//             panierHeader.style.opacity = '1';
//         }

//         // Afficher le conteneur d'application Vue
//         const appContainer = document.querySelector('#app[data-formations-url]');
//         if (appContainer) {
//             appContainer.style.opacity = '1';
//         }

//         // Masquer le spinner maintenant que le contenu est affiché
//         hideLoadingSpinner();
//     }

//     // function initReservationSystem() {
//     //     // Vérifier d'abord avec le serveur avant toute initialisation d'interface
//     //     checkExistingReservations()
//     //         .then(result => {
//     //             const hasReservation = result.hasReservation;
//     //             const reservationStatus = result.status || 0;
//     //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

//     //             // Stocker le statut de la réservation
//     //             window.reservationStatus = reservationStatus;
//     //             localStorage.setItem('reservationStatus', reservationStatus.toString());

//     //             if (hasReservation) {
//     //                 // Une réservation existe côté serveur
//     //                 const currentReservationId = localStorage.getItem('reservationId');
//     //                 transformReserverButton(parseInt(currentReservationId), reservationStatus, true);
//     //             } else if (cartCount > 0) {
//     //                 // Pas de réservation active mais panier non vide
//     //                 createReserverButton(true);
//     //             } else {
//     //                 // Pas de réservation et panier vide
//     //                 removeAllButtons();
//     //             }

//     //             // Maintenant que tout est prêt, afficher le contenu
//     //             window.buttonsInitialized = true;
//     //             showCartContent();
//     //         })
//     //         .catch(error => {
//     //             console.error("Erreur de vérification avec le serveur:", error);

//     //             // En cas d'erreur, se baser sur les données du localStorage
//     //             const storedReservationStatus = localStorage.getItem('hasExistingReservation') === 'true';
//     //             const reservationId = localStorage.getItem('reservationId');
//     //             const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
//     //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

//     //             // Stocker le statut de la réservation
//     //             window.reservationStatus = reservationStatus;

//     //             if (storedReservationStatus && reservationId) {
//     //                 transformReserverButton(parseInt(reservationId), reservationStatus, true);
//     //             } else if (cartCount > 0) {
//     //                 createReserverButton(true);
//     //             } else {
//     //                 removeAllButtons();
//     //             }

//     //             // Afficher le contenu même en cas d'erreur
//     //             window.buttonsInitialized = true;
//     //             showCartContent();
//     //         })
//     //         .finally(() => {
//     //             window.checkingReservations = false;
//     //         });
//     // }
//     function initReservationSystem() {
//     // Vérifier d'abord avec le serveur avant toute initialisation d'interface
//     checkExistingReservations()
//         .then(result => {
//             const hasReservation = result.hasReservation;
//             const reservationStatus = result.status || 0;
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             // Stocker le statut de la réservation
//             window.reservationStatus = reservationStatus;
//             localStorage.setItem('reservationStatus', reservationStatus.toString());

//             // Vérifier si des formations complètes sont présentes dans le panier
//             checkForCompleteFormations().then(hasCompleteFormations => {
//                 window.hasCompleteFormationsInCart = hasCompleteFormations;

//                 if (hasReservation) {
//                     // Une réservation existe côté serveur
//                     const currentReservationId = localStorage.getItem('reservationId');
//                     transformReserverButton(parseInt(currentReservationId), reservationStatus, true);
//                 } else if (cartCount > 0) {
//                     // Pas de réservation active mais panier non vide
//                     createReserverButton(true);
//                 } else {
//                     // Pas de réservation et panier vide
//                     removeAllButtons();
//                 }
//                 // Maintenant que tout est prêt, afficher le contenu
//                 window.buttonsInitialized = true;
//                 showCartContent();
//             });
//         })
//         .catch(error => {
//             console.error("Erreur de vérification avec le serveur:", error);

//             // En cas d'erreur, se baser sur les données du localStorage
//             const storedReservationStatus = localStorage.getItem('hasExistingReservation') === 'true';
//             const reservationId = localStorage.getItem('reservationId');
//             const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

//             // Stocker le statut de la réservation
//             window.reservationStatus = reservationStatus;

//             // Vérifier si des formations complètes sont présentes dans le panier
//             checkForCompleteFormations().then(hasCompleteFormations => {
//                 window.hasCompleteFormationsInCart = hasCompleteFormations;

//                 if (storedReservationStatus && reservationId) {
//                     transformReserverButton(parseInt(reservationId), reservationStatus, true);
//                 } else if (cartCount > 0) {
//                     createReserverButton(true);
//                 } else {
//                     removeAllButtons();
//                 }

//                 // Afficher le contenu même en cas d'erreur
//                 window.buttonsInitialized = true;
//                 showCartContent();
//             });
//         })
//         .finally(() => {
//             window.checkingReservations = false;
//         });
// }

// // Nouvelle fonction pour vérifier les formations complètes
// function checkForCompleteFormations() {
//     return new Promise((resolve) => {
//         // Vérifier si la fonction checkFormationsAvailability existe déjà
//         if (typeof window.checkFormationsAvailability === 'function') {
//             // Attendre un court instant pour que la vérification s'effectue
//             setTimeout(() => {
//                 const completeFormations = document.querySelectorAll('.formation-full');
//                 resolve(completeFormations.length > 0);
//             }, 300);
//         } else {
//             // Vérifier directement le DOM si la fonction n'est pas disponible
//             const completeFormations = document.querySelectorAll('.formation-full');
//             resolve(completeFormations.length > 0);
//         }
//     });
// }

//     function removeAllButtons() {
//         const reserverButton = document.querySelector('.reserver-button');
//         if (reserverButton) {
//             reserverButton.remove();
//         }

//         const cancelButton = document.querySelector('.annuler-button');
//         if (cancelButton) {
//             cancelButton.remove();
//         }
//     }

//     function findOrCreateTotalContainer() {
//         // D'abord essayer de trouver le conteneur existant
//         const totalPriceElement = document.querySelector('.total-price');
//         if (totalPriceElement) {
//             return totalPriceElement.closest('.total-container') || totalPriceElement.parentElement;
//         }

//         // Si on ne trouve pas le conteneur, essayer de localiser où le créer
//         const containers = [
//             document.querySelector('.panier-content'),
//             document.querySelector('.container'),
//             document.querySelector('main'),
//             document.body
//         ];

//         const container = containers.find(c => c !== null);
//         if (!container) return null;

//         // Créer une structure minimale pour héberger les boutons
//         const totalWrapper = document.createElement('div');
//         totalWrapper.className = 'total-container temp-container';
//         container.appendChild(totalWrapper);

//         return totalWrapper;
//     }

//     function checkExistingReservations() {
//         return new Promise((resolve, reject) => {
//             window.checkingReservations = true;

//             checkUserAuthentication()
//                 .then(authenticated => {
//                     if (authenticated) {
//                         fetch('/api/reservations/check', {
//                             method: 'GET',
//                             headers: {
//                                 'X-Requested-With': 'XMLHttpRequest',
//                                 'Accept': 'application/json'
//                             }
//                         })
//                         .then(handleResponse)
//                         .then(data => {
//                             if (data.hasReservation) {
//                                 // Mettre à jour le localStorage pour les chargements futurs
//                                 window.hasExistingReservation = true;
//                                 localStorage.setItem('hasExistingReservation', 'true');
//                                 localStorage.setItem('reservationId', data.reservation_id.toString());

//                                 // Stocker le statut de la réservation
//                                 const status = data.status || 0;
//                                 window.reservationStatus = status;
//                                 localStorage.setItem('reservationStatus', status.toString());

//                                 resolve({
//                                     hasReservation: true,
//                                     status: status
//                                 });
//                             } else {
//                                 window.hasExistingReservation = false;
//                                 localStorage.removeItem('hasExistingReservation');
//                                 localStorage.removeItem('reservationId');
//                                 localStorage.removeItem('reservationStatus');

//                                 resolve({
//                                     hasReservation: false,
//                                     status: 0
//                                 });
//                             }
//                         })
//                         .catch(error => {
//                             console.error('Erreur lors de la vérification des réservations:', error);
//                             reject(error);
//                         });
//                     } else {
//                         resolve({
//                             hasReservation: false,
//                             status: 0
//                         });
//                     }
//                 })
//                 .catch(reject);
//         });
//     }

//     function transformReserverButton(reservationId, reservationStatus = 0, isInitialLoad = false) {
//         // Supprimer d'abord les boutons existants pour éviter les doublons
//         removeAllButtons();

//         // Si le conteneur n'est pas encore chargé, essayer de le créer nous-mêmes
//         let totalContainer = findOrCreateTotalContainer();
//         if (!totalContainer) {
//             // Réessayer plus tard seulement si on n'a pas pu créer le conteneur
//             setTimeout(() => transformReserverButton(reservationId, reservationStatus, isInitialLoad), 30);
//             return;
//         }

//         // Créer et ajouter le bouton "Voir mes réservations" en PREMIER
//         const reserverButton = document.createElement('button');
//         reserverButton.className = 'reserver-button';
//         reserverButton.innerHTML = 'Voir mes réservations <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

//         reserverButton.addEventListener('click', function(e) {
//             e.preventDefault();
//             window.location.href = '/mes-reservations';
//         });

//         totalContainer.appendChild(reserverButton);

//         // N'ajouter le bouton d'annulation QUE si le statut de réservation n'est pas 1 (payé)
//         if (reservationStatus !== 1) {
//             // Créer et ajouter le bouton d'annulation en SECOND
//             const cancelButton = document.createElement('button');
//             cancelButton.className = 'annuler-button';
//             cancelButton.innerHTML = 'Annuler la réservation';

//             cancelButton.addEventListener('click', function(e) {
//                 e.preventDefault();
//                 cancelReservation(reservationId);
//             });

//             totalContainer.appendChild(cancelButton);
//         }
//     }

//     function cancelReservation(reservationId) {
//         fetch('/api/reservations/cancel', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//                 'X-Requested-With': 'XMLHttpRequest',
//                 'Accept': 'application/json'
//             },
//             body: JSON.stringify({
//                 reservation_id: reservationId
//             })
//         })
//         .then(handleResponse)
//         .then(data => {
//             if (data.success) {
//                 console.log(data.message || 'Réservation annulée avec succès');

//                 // Mise à jour des variables
//                 window.hasExistingReservation = false;
//                 localStorage.removeItem('hasExistingReservation');
//                 localStorage.removeItem('reservationId');
//                 localStorage.removeItem('reservationStatus');

//                 // Restaurer l'état du bouton si le panier n'est pas vide
//                 const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//                 if (cartCount > 0) {
//                     createReserverButton(false);
//                 } else {
//                     // Si le panier est vide, supprimer tous les boutons
//                     removeAllButtons();
//                 }
//             } else {
//                 console.error(data.message || 'Erreur lors de l\'annulation de la réservation');
//             }
//         })
//         .catch(error => {
//             console.error('Erreur lors de l\'annulation de la réservation:', error);
//         });
//     }


// function createReserverButton(isInitialLoad = false) {
//     // Ne pas créer le bouton si l'utilisateur a déjà une réservation
//     if (window.hasExistingReservation === true) {
//         // Au lieu de retourner simplement, on affiche le bouton "Voir mes réservations"
//         const reservationId = localStorage.getItem('reservationId');
//         const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
//         if (reservationId) {
//             transformReserverButton(parseInt(reservationId), reservationStatus, isInitialLoad);
//         }
//         return false;
//     }

//     // Supprimer d'abord les boutons existants pour éviter les doublons
//     removeAllButtons();

//     // Utiliser notre fonction pour trouver ou créer le conteneur
//     const totalContainer = findOrCreateTotalContainer();
//     if (!totalContainer) {
//         setTimeout(() => createReserverButton(isInitialLoad), 30);
//         return false;
//     }

//     const reservButton = document.createElement('button');
//     reservButton.className = 'reserver-button';
//     reservButton.innerHTML = 'Réserver <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

//     // On ajoute l'événement ici
//     reservButton.addEventListener('click', handleReservationClick);

//     // MODIFICATION: Vérifier directement depuis le DOM pour un état plus précis
//     const expiredFormationElements = document.querySelectorAll('.formation-expired');
//     const completeFormationElements = document.querySelectorAll('.formation-full');

//     const hasExpiredFormations = expiredFormationElements.length > 0 || window.hasExpiredFormationsInCart;
//     const hasCompleteFormations = completeFormationElements.length > 0 || window.hasCompleteFormationsInCart;

//     // Mise à jour des variables globales pour cohérence
//     window.hasExpiredFormationsInCart = hasExpiredFormations;
//     window.hasCompleteFormationsInCart = hasCompleteFormations;

//     if (hasExpiredFormations) {
//         reservButton.disabled = true;
//         reservButton.classList.add('disabled');
//         reservButton.title = 'Votre panier contient des formations dont la date est dépassée';
//     } else if (hasCompleteFormations) {
//         reservButton.disabled = true;
//         reservButton.classList.add('disabled');
//         reservButton.title = 'Une ou plusieurs formations sont complètes';
//     }

//     totalContainer.appendChild(reservButton);

//     return true;
// }
// function startDOMObserver() {
//     if (!window.domChangeObserver) {
//         window.domChangeObserver = new MutationObserver(function(mutations) {
//             // Ne rien faire si les boutons sont déjà initialisés ou si la vérification est en cours
//             if (window.checkingReservations) {
//                 return;
//             }

//             // Si des éléments sont ajoutés au DOM et que la page est déjà chargée
//             if (document.readyState === 'complete') {
//                 const totalPrice = document.querySelector('.total-price');
//                 if (totalPrice && !document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
//                     // Détecter si on doit afficher les boutons de réservation ou d'annulation
//                     if (window.hasExistingReservation) {
//                         const reservationId = localStorage.getItem('reservationId');
//                         const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
//                         if (reservationId) {
//                             transformReserverButton(parseInt(reservationId), reservationStatus, false);
//                         }
//                     } else {
//                         // Seulement après avoir vérifié qu'il n'y a pas de réservation
//                         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//                         if (cartCount > 0) {
//                             createReserverButton(false);
//                         }
//                     }
//                 }

//                 // MODIFICATION: Vérifier directement depuis le DOM plutôt que d'utiliser des Promises
//                 const expiredFormationElements = document.querySelectorAll('.formation-expired');
//                 const completeFormationElements = document.querySelectorAll('.formation-full');

//                 const hasExpiredFormations = expiredFormationElements.length > 0;
//                 const hasCompleteFormations = completeFormationElements.length > 0;

//                 // Mettre à jour les variables globales
//                 window.hasExpiredFormationsInCart = hasExpiredFormations;
//                 window.hasCompleteFormationsInCart = hasCompleteFormations;

//                 // Si on a le bouton "Réserver" (et non pas "Voir mes réservations")
//                 const reserveButton = document.querySelector('.reserver-button');
//                 if (reserveButton && !window.hasExistingReservation) {
//                     if (hasExpiredFormations) {
//                         // Priorité aux formations expirées
//                         reserveButton.disabled = true;
//                         reserveButton.classList.add('disabled');
//                         reserveButton.title = 'Votre panier contient des formations dont la date est dépassée';
//                     } else if (hasCompleteFormations) {
//                         reserveButton.disabled = true;
//                         reserveButton.classList.add('disabled');
//                         reserveButton.title = 'Une ou plusieurs formations sont complètes';
//                     } else {
//                         reserveButton.disabled = false;
//                         reserveButton.classList.remove('disabled');
//                         reserveButton.removeAttribute('title');
//                     }
//                 }

//                 // Lancer une vérification des dates si la fonction est disponible
//                 if (typeof window.checkFormationsDates === 'function' &&
//                     !window.checkingFormationDates) {
//                     window.checkingFormationDates = true;
//                     window.checkFormationsDates().finally(() => {
//                         window.checkingFormationDates = false;
//                     });
//                 }
//             }
//         });

//         window.domChangeObserver.observe(document.body, {
//             childList: true,
//             subtree: true
//         });
//     }
// }
//   function handleReservationClick(e) {
//     e.preventDefault();
//     e.stopPropagation(); // Empêcher la propagation de l'événement

//     const reservButton = e.currentTarget;
//     const originalText = reservButton.innerHTML;

//     // Vérifier si on a une fonction de validation
//     if (typeof window.validatedReservationClick === 'function') {
//         // Ne pas modifier le bouton ici, car cela sera fait dans validatedReservationClick
//         window.validatedReservationClick(e, () => {
//             // La modification du bouton est déjà faite dans validatedReservationClick
//             processReservation()
//                 .finally(() => {
//                     // Réactiver le bouton après traitement (au cas où la réservation échoue)
//                     reservButton.disabled = false;
//                     reservButton.innerHTML = originalText;
//                 });
//         });
//     } else {
//         // Si la fonction n'existe pas, traiter directement
//         // Changer le texte du bouton immédiatement
//         reservButton.innerHTML = 'Réservation en cours...';
//         reservButton.disabled = true;

//         processReservation()
//             .finally(() => {
//                 // Réactiver le bouton après traitement (au cas où la réservation échoue)
//                 reservButton.disabled = false;
//                 reservButton.innerHTML = originalText;
//             });
//     }
// }


//     function processReservation() {
//         return checkUserAuthentication()
//             .then(authenticated => {
//                 if (authenticated) {
//                     return checkCartNotEmpty()
//                         .then(notEmpty => {
//                             if (notEmpty) {
//                                 return createReservation()
//                                     .then(response => {
//                                         handleReservationResponse(response);
//                                         return response; // Retourner la réponse pour le chaînage
//                                     });
//                             } else {
//                                 console.log('Votre panier est vide');
//                                 throw new Error('Panier vide');
//                             }
//                         });
//                 } else {
//                     console.log('Veuillez vous connecter pour réserver');
//                     redirectToLogin();
//                     throw new Error('Non authentifié');
//                 }
//             });
//     }

//     function checkUserAuthentication() {
//         return fetch('/api/user/check-auth', {
//             method: 'GET',
//             headers: {
//                 'X-Requested-With': 'XMLHttpRequest',
//                 'Accept': 'application/json'
//             }
//         })
//         .then(response => response.json())
//         .then(data => {
//             return data.authenticated;
//         })
//         .catch(error => {
//             console.error('Erreur lors de la vérification de l\'authentification:', error);
//             return false;
//         });
//     }

//     function checkCartNotEmpty() {
//         return fetch('/panier/items-count', {
//             method: 'GET',
//             headers: {
//                 'X-Requested-With': 'XMLHttpRequest',
//                 'Accept': 'application/json'
//             }
//         })
//         .then(handleResponse)
//         .then(data => {
//             return data.count > 0;
//         });
//     }

//     function createReservation() {
//         return fetch('/api/reservations/create', {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//                 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
//                 'X-Requested-With': 'XMLHttpRequest',
//                 'Accept': 'application/json'
//             },
//             body: JSON.stringify({
//                 reservation_date: new Date().toISOString().split('T')[0], // Date actuelle
//                 reservation_time: new Date().toTimeString().split(' ')[0] // Heure actuelle
//             })
//         })
//         .then(response => {
//             if (!response.ok) {
//                 // Si le statut n'est pas OK (2xx), on lance une erreur
//                 return response.json().then(errorData => {
//                     throw new Error(errorData.message || 'Erreur lors de la réservation');
//                 });
//             }
//             return response.json();
//         });
//     }

//     function handleReservationResponse(response) {
//         if (response.success) {
//             console.log(response.message || 'Réservation effectuée avec succès');

//             // Mettre à jour la variable globale et localStorage
//             window.hasExistingReservation = true;
//             localStorage.setItem('hasExistingReservation', 'true');

//             // Récupérer le statut de la réservation (par défaut 0 si non fourni)
//             const status = response.status || 0;
//             window.reservationStatus = status;
//             localStorage.setItem('reservationStatus', status.toString());

//             // Transformer le bouton immédiatement
//             if (response.reservation_id) {
//                 localStorage.setItem('reservationId', response.reservation_id.toString());
//                 transformReserverButton(response.reservation_id, status, false);
//             }

//             // Si la réservation est réussie et qu'il faut vider le panier
//             if (response.clearCart) {
//                 // Mise à jour du compteur sans supprimer visuellement le bouton
//                 localStorage.setItem('cartCount', '0');
//             }

//             // Redirection éventuelle vers la page de confirmation
//             if (response.redirectUrl) {
//                 window.location.href = response.redirectUrl;
//             }
//         } else {
//             console.error(response.message || 'Erreur lors de la réservation');
//             // Restaurer le texte original du bouton
//             const reservButton = document.querySelector('.reserver-button');
//             if (reservButton) {
//                 reservButton.innerHTML = 'Réserver <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
//             }
//             throw new Error(response.message || 'Erreur lors de la réservation');
//         }
//     }

//     function handleReservationError(error) {
//         console.error('Erreur lors de la réservation:', error);
//     }

//     function redirectToLogin() {
//         // Sauvegarder l'URL actuelle pour rediriger après la connexion
//         localStorage.setItem('redirectAfterLogin', window.location.href);
//         window.location.href = '/login';
//     }

//     function handleResponse(response) {
//         if (!response.ok) {
//             throw new Error('Erreur réseau');
//         }
//         return response.json();
//     }

//     // Ajouter un observateur de mutations pour détecter les changements dans le DOM
//     // function startDOMObserver() {
//     //     if (!window.domChangeObserver) {
//     //         window.domChangeObserver = new MutationObserver(function(mutations) {
//     //             // Ne rien faire si les boutons sont déjà initialisés ou si la vérification est en cours
//     //             if (window.buttonsInitialized || window.checkingReservations) {
//     //                 return;
//     //             }

//     //             // Si des éléments sont ajoutés au DOM et que la page est déjà chargée
//     //             if (document.readyState === 'complete') {
//     //                 const totalPrice = document.querySelector('.total-price');
//     //                 if (totalPrice && !document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
//     //                     // Détecter si on doit afficher les boutons de réservation ou d'annulation
//     //                     if (window.hasExistingReservation) {
//     //                         const reservationId = localStorage.getItem('reservationId');
//     //                         const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
//     //                         if (reservationId) {
//     //                             transformReserverButton(parseInt(reservationId), reservationStatus, false);
//     //                         }
//     //                     } else {
//     //                         // Seulement après avoir vérifié qu'il n'y a pas de réservation
//     //                         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//     //                         if (cartCount > 0) {
//     //                             createReserverButton(false);
//     //                         }
//     //                     }
//     //                 }
//     //             }
//     //         });

//     //         window.domChangeObserver.observe(document.body, {
//     //             childList: true,
//     //             subtree: true
//     //         });
//     //     }
//     // }
//     // Voici la fonction transformReserverButton modifiée
// function transformReserverButton(reservationId, reservationStatus = 0, isInitialLoad = false) {
//     // Supprimer d'abord les boutons existants pour éviter les doublons
//     removeAllButtons();

//     // Si le conteneur n'est pas encore chargé, essayer de le créer nous-mêmes
//     let totalContainer = findOrCreateTotalContainer();
//     if (!totalContainer) {
//         // Réessayer plus tard seulement si on n'a pas pu créer le conteneur
//         setTimeout(() => transformReserverButton(reservationId, reservationStatus, isInitialLoad), 30);
//         return;
//     }

//     // Créer et ajouter le bouton "Voir mes réservations" en PREMIER
//     const reserverButton = document.createElement('button');
//     reserverButton.className = 'reserver-button';
//     reserverButton.innerHTML = 'Voir mes réservations <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

//     // Le bouton "Voir mes réservations" doit toujours rester actif
//     // Pas de vérification de formations complètes ici
//     reserverButton.addEventListener('click', function(e) {
//         e.preventDefault();
//         window.location.href = '/mes-reservations';
//     });

//     totalContainer.appendChild(reserverButton);

//     // N'ajouter le bouton d'annulation QUE si le statut de réservation n'est pas 1 (payé)
//     if (reservationStatus !== 1) {
//         // Créer et ajouter le bouton d'annulation en SECOND
//         const cancelButton = document.createElement('button');
//         cancelButton.className = 'annuler-button';
//         cancelButton.innerHTML = 'Annuler la réservation';

//         cancelButton.addEventListener('click', function(e) {
//             e.preventDefault();
//             cancelReservation(reservationId);
//         });

//         totalContainer.appendChild(cancelButton);
//     }
// }

// // Modifier également la fonction startDOMObserver pour garantir que les boutons conservent leur état
// // function startDOMObserver() {
// //     if (!window.domChangeObserver) {
// //         window.domChangeObserver = new MutationObserver(function(mutations) {
// //             // Ne rien faire si les boutons sont déjà initialisés ou si la vérification est en cours
// //             if (window.checkingReservations) {
// //                 return;
// //             }

// //             // Si des éléments sont ajoutés au DOM et que la page est déjà chargée
// //             if (document.readyState === 'complete') {
// //                 const totalPrice = document.querySelector('.total-price');
// //                 if (totalPrice && !document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
// //                     // Détecter si on doit afficher les boutons de réservation ou d'annulation
// //                     if (window.hasExistingReservation) {
// //                         const reservationId = localStorage.getItem('reservationId');
// //                         const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
// //                         if (reservationId) {
// //                             transformReserverButton(parseInt(reservationId), reservationStatus, false);
// //                         }
// //                     } else {
// //                         // Seulement après avoir vérifié qu'il n'y a pas de réservation
// //                         const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
// //                         if (cartCount > 0) {
// //                             createReserverButton(false);
// //                         }
// //                     }
// //                 }

// //                 // Vérifier si des formations complètes sont présentes et mettre à jour les états des boutons
// //                 // Cette vérification ne concernera que le bouton "Réserver", pas le bouton "Voir mes réservations"
// //                 checkForCompleteFormations().then(hasCompleteFormations => {
// //                     window.hasCompleteFormationsInCart = hasCompleteFormations;

// //                     // Si on a le bouton "Réserver" (et non pas "Voir mes réservations")
// //                     const reserveButton = document.querySelector('.reserver-button');
// //                     if (reserveButton && !window.hasExistingReservation) {
// //                         if (hasCompleteFormations) {
// //                             reserveButton.disabled = true;
// //                             reserveButton.classList.add('disabled');
// //                             reserveButton.title = 'Une ou plusieurs formations sont complètes';
// //                         } else {
// //                             reserveButton.disabled = false;
// //                             reserveButton.classList.remove('disabled');
// //                             reserveButton.removeAttribute('title');
// //                         }
// //                     }
// //                 });
// //             }
// //         });

// //         window.domChangeObserver.observe(document.body, {
// //             childList: true,
// //             subtree: true
// //         });
// //     }
// // }

//     // Démarrer l'observateur DOM après l'initialisation
//     window.addEventListener('load', startDOMObserver);

//     // Exposer les fonctions nécessaires globalement
//     window.processReservation = processReservation;
//     window.createReservation = createReservation;
//     window.checkUserAuthentication = checkUserAuthentication;
//     window.transformReserverButton = transformReserverButton;
//     window.cancelReservation = cancelReservation;
//     window.checkExistingReservations = checkExistingReservations;
//     window.createReserverButton = createReserverButton;
// })();



(function() {
    const currentPage = window.location.pathname; // Exemple: '/ma-page-specifique'

    // Liste des pages où le spinner doit apparaître
    const allowedPages = [
        '/panier',
    ];

    // Si nous ne sommes pas sur une page autorisée, ne rien faire
    if (!allowedPages.includes(currentPage)) {
        return;
    }
    // Variables globales pour suivre l'état des réservations
    window.hasExistingReservation = false;
    window.checkingReservations = true; // Commencer avec l'état de vérification activé
    window.buttonsInitialized = false;  // Nouvelle variable pour suivre si les boutons ont été initialisés
    window.reservationStatus = 0;      // Nouvelle variable pour suivre le statut de la réservation
    window.hasCompleteFormationsInCart = false; // Nouvelle variable pour suivre si des formations complètes sont dans le panier

    // Fonction pour créer et afficher le spinner de chargement
    function showLoadingSpinner() {
        // Créer le conteneur du spinner
        const spinnerContainer = document.createElement('div');
        spinnerContainer.id = 'reservation-spinner';
        spinnerContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.8);
            z-index: 9999;
        `;

        // Créer la structure du spinner selon votre modèle
        const spinnerCard = document.createElement('div');
        spinnerCard.className = 'container-fluid';
        spinnerCard.innerHTML = `
        <div class="card-body row justify-content-center">
            <div class="col-sm-6 col-md-3">
                <div class="loader-box">
                    <div class="loader-19"></div>
                </div>
            </div>
        </div>
    `;

        // Ajouter le spinner au conteneur
        spinnerContainer.appendChild(spinnerCard);

        // Ajouter le spinner au body
        document.body.appendChild(spinnerContainer);
    }

    // Fonction pour masquer le spinner
    function hideLoadingSpinner() {
        const spinner = document.getElementById('reservation-spinner');
        if (spinner) {
            spinner.style.transition = 'opacity 0.3s ease';
            spinner.style.opacity = '0';

            // Retirer le spinner après la transition
            setTimeout(() => {
                spinner.remove();
            }, 300);
        }
    }

    // Afficher le spinner immédiatement
    showLoadingSpinner();

    // Masquer tout contenu du panier jusqu'à la vérification complète
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            hideCartContent();
            initReservationSystem();
        });
    } else {
        hideCartContent();
        initReservationSystem();
    }

    function hideCartContent() {
        // Masquer temporairement le contenu du panier pendant la vérification
        const cartContent = document.querySelector('.panier-content') ||
                          document.querySelector('.total-container');
        if (cartContent) {
            cartContent.style.opacity = '0';
            cartContent.style.transition = 'opacity 0.2s';
        }

        // Masquer également l'en-tête du panier
        const panierHeader = document.querySelector('.panier-header');
        if (panierHeader) {
            panierHeader.style.opacity = '0';
            panierHeader.style.transition = 'opacity 0.2s';
        }

        // Masquer le conteneur d'application Vue
        const appContainer = document.querySelector('#app[data-formations-url]');
        if (appContainer) {
            appContainer.style.opacity = '0';
            appContainer.style.transition = 'opacity 0.2s';
        }
    }

    function showCartContent() {
        // Afficher le contenu du panier une fois la vérification terminée
        const cartContent = document.querySelector('.panier-content') ||
                          document.querySelector('.total-container');
        if (cartContent) {
            cartContent.style.opacity = '1';
        }

        // Afficher également l'en-tête du panier
        const panierHeader = document.querySelector('.panier-header');
        if (panierHeader) {
            panierHeader.style.opacity = '1';
        }

        // Afficher le conteneur d'application Vue
        const appContainer = document.querySelector('#app[data-formations-url]');
        if (appContainer) {
            appContainer.style.opacity = '1';
        }

        // Masquer le spinner maintenant que le contenu est affiché
        hideLoadingSpinner();
    }
        // Fonction pour initialiser le système de réservation
function initReservationSystem() {
    // Vérifier d'abord le contenu du panier avant de décider d'afficher le spinner
    checkCartNotEmpty()
        .then(notEmpty => {
            if (!notEmpty) {
                // Si le panier est vide, cacher immédiatement le spinner et afficher le contenu
                hideLoadingSpinner();
                showCartContent();
                return { hasReservation: false, status: 0 };
            }

            // Si le panier contient des éléments, continuer avec la vérification des réservations
            return checkExistingReservations();
        })
        .then(result => {
            // Ne traiter la suite que si le panier n'est pas vide ou s'il y a une réservation existante
            const hasReservation = result.hasReservation;
            const reservationStatus = result.status || 0;
            const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

            // Stocker le statut de la réservation
            window.reservationStatus = reservationStatus;
            localStorage.setItem('reservationStatus', reservationStatus.toString());

            // Vérifier si des formations complètes sont présentes dans le panier
            return checkForCompleteFormations().then(hasCompleteFormations => {
                window.hasCompleteFormationsInCart = hasCompleteFormations;

                if (hasReservation) {
                    // Une réservation existe côté serveur
                    const currentReservationId = localStorage.getItem('reservationId');
                    transformReserverButton(parseInt(currentReservationId), reservationStatus, true);
                } else if (cartCount > 0) {
                    // Pas de réservation active mais panier non vide
                    createReserverButton(true);
                } else {
                    // Pas de réservation et panier vide
                    removeAllButtons();
                }

                // Maintenant que tout est prêt, afficher le contenu
                window.buttonsInitialized = true;
                showCartContent();
            });
        })
        .catch(error => {
            console.error("Erreur de vérification avec le serveur:", error);

            // En cas d'erreur, afficher quand même le contenu
            window.buttonsInitialized = true;
            showCartContent();
            hideLoadingSpinner();
        })
        .finally(() => {
            window.checkingReservations = false;
        });
}
    // function initReservationSystem() {
    //     // Vérifier d'abord avec le serveur avant toute initialisation d'interface
    //     checkExistingReservations()
    //         .then(result => {
    //             const hasReservation = result.hasReservation;
    //             const reservationStatus = result.status || 0;
    //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

    //             // Stocker le statut de la réservation
    //             window.reservationStatus = reservationStatus;
    //             localStorage.setItem('reservationStatus', reservationStatus.toString());

    //             if (hasReservation) {
    //                 // Une réservation existe côté serveur
    //                 const currentReservationId = localStorage.getItem('reservationId');
    //                 transformReserverButton(parseInt(currentReservationId), reservationStatus, true);
    //             } else if (cartCount > 0) {
    //                 // Pas de réservation active mais panier non vide
    //                 createReserverButton(true);
    //             } else {
    //                 // Pas de réservation et panier vide
    //                 removeAllButtons();
    //             }

    //             // Maintenant que tout est prêt, afficher le contenu
    //             window.buttonsInitialized = true;
    //             showCartContent();
    //         })
    //         .catch(error => {
    //             console.error("Erreur de vérification avec le serveur:", error);

    //             // En cas d'erreur, se baser sur les données du localStorage
    //             const storedReservationStatus = localStorage.getItem('hasExistingReservation') === 'true';
    //             const reservationId = localStorage.getItem('reservationId');
    //             const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
    //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

    //             // Stocker le statut de la réservation
    //             window.reservationStatus = reservationStatus;

    //             if (storedReservationStatus && reservationId) {
    //                 transformReserverButton(parseInt(reservationId), reservationStatus, true);
    //             } else if (cartCount > 0) {
    //                 createReserverButton(true);
    //             } else {
    //                 removeAllButtons();
    //             }

    //             // Afficher le contenu même en cas d'erreur
    //             window.buttonsInitialized = true;
    //             showCartContent();
    //         })
    //         .finally(() => {
    //             window.checkingReservations = false;
    //         });
    // }
//     function initReservationSystem() {
//     // Vérifier d'abord avec le serveur avant toute initialisation d'interface
//     checkExistingReservations()
//         .then(result => {
//             const hasReservation = result.hasReservation;
//             const reservationStatus = result.status || 0;
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
//             // Stocker le statut de la réservation
//             window.reservationStatus = reservationStatus;
//             localStorage.setItem('reservationStatus', reservationStatus.toString());

//             // Vérifier si des formations complètes sont présentes dans le panier
//             checkForCompleteFormations().then(hasCompleteFormations => {
//                 window.hasCompleteFormationsInCart = hasCompleteFormations;

//                 if (hasReservation) {
//                     // Une réservation existe côté serveur
//                     const currentReservationId = localStorage.getItem('reservationId');
//                     transformReserverButton(parseInt(currentReservationId), reservationStatus, true);
//                 } else if (cartCount > 0) {
//                     // Pas de réservation active mais panier non vide
//                     createReserverButton(true);
//                 } else {
//                     // Pas de réservation et panier vide
//                     removeAllButtons();
//                 }
//                 // Maintenant que tout est prêt, afficher le contenu
//                 window.buttonsInitialized = true;
//                 showCartContent();
//             });
//         })
//         .catch(error => {
//             console.error("Erreur de vérification avec le serveur:", error);

//             // En cas d'erreur, se baser sur les données du localStorage
//             const storedReservationStatus = localStorage.getItem('hasExistingReservation') === 'true';
//             const reservationId = localStorage.getItem('reservationId');
//             const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
//             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');

//             // Stocker le statut de la réservation
//             window.reservationStatus = reservationStatus;

//             // Vérifier si des formations complètes sont présentes dans le panier
//             checkForCompleteFormations().then(hasCompleteFormations => {
//                 window.hasCompleteFormationsInCart = hasCompleteFormations;

//                 if (storedReservationStatus && reservationId) {
//                     transformReserverButton(parseInt(reservationId), reservationStatus, true);
//                 } else if (cartCount > 0) {
//                     createReserverButton(true);
//                 } else {
//                     removeAllButtons();
//                 }

//                 // Afficher le contenu même en cas d'erreur
//                 window.buttonsInitialized = true;
//                 showCartContent();
//             });
//         })
//         .finally(() => {
//             window.checkingReservations = false;
//         });
// }

// Nouvelle fonction pour vérifier les formations complètes
function checkForCompleteFormations() {
    return new Promise((resolve) => {
        // Vérifier si la fonction checkFormationsAvailability existe déjà
        if (typeof window.checkFormationsAvailability === 'function') {
            // Attendre un court instant pour que la vérification s'effectue
            setTimeout(() => {
                const completeFormations = document.querySelectorAll('.formation-full');
                resolve(completeFormations.length > 0);
            }, 300);
        } else {
            // Vérifier directement le DOM si la fonction n'est pas disponible
            const completeFormations = document.querySelectorAll('.formation-full');
            resolve(completeFormations.length > 0);
        }
    });
}

    function removeAllButtons() {
        const reserverButton = document.querySelector('.reserver-button');
        if (reserverButton) {
            reserverButton.remove();
        }

        const cancelButton = document.querySelector('.annuler-button');
        if (cancelButton) {
            cancelButton.remove();
        }
    }

    function findOrCreateTotalContainer() {
        // D'abord essayer de trouver le conteneur existant
        const totalPriceElement = document.querySelector('.total-price');
        if (totalPriceElement) {
            return totalPriceElement.closest('.total-container') || totalPriceElement.parentElement;
        }

        // Si on ne trouve pas le conteneur, essayer de localiser où le créer
        const containers = [
            document.querySelector('.panier-content'),
            document.querySelector('.container'),
            document.querySelector('main'),
            document.body
        ];

        const container = containers.find(c => c !== null);
        if (!container) return null;

        // Créer une structure minimale pour héberger les boutons
        const totalWrapper = document.createElement('div');
        totalWrapper.className = 'total-container temp-container';
        container.appendChild(totalWrapper);

        return totalWrapper;
    }

    function checkExistingReservations() {
        return new Promise((resolve, reject) => {
            window.checkingReservations = true;

            checkUserAuthentication()
                .then(authenticated => {
                    if (authenticated) {
                        fetch('/api/reservations/check', {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        })
                        .then(handleResponse)
                        .then(data => {
                            if (data.hasReservation) {
                                // Mettre à jour le localStorage pour les chargements futurs
                                window.hasExistingReservation = true;
                                localStorage.setItem('hasExistingReservation', 'true');
                                localStorage.setItem('reservationId', data.reservation_id.toString());

                                // Stocker le statut de la réservation
                                const status = data.status || 0;
                                window.reservationStatus = status;
                                localStorage.setItem('reservationStatus', status.toString());

                                resolve({
                                    hasReservation: true,
                                    status: status
                                });
                            } else {
                                window.hasExistingReservation = false;
                                localStorage.removeItem('hasExistingReservation');
                                localStorage.removeItem('reservationId');
                                localStorage.removeItem('reservationStatus');

                                resolve({
                                    hasReservation: false,
                                    status: 0
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la vérification des réservations:', error);
                            reject(error);
                        });
                    } else {
                        resolve({
                            hasReservation: false,
                            status: 0
                        });
                    }
                })
                .catch(reject);
        });
    }

    function transformReserverButton(reservationId, reservationStatus = 0, isInitialLoad = false) {
        // Supprimer d'abord les boutons existants pour éviter les doublons
        removeAllButtons();

        // Si le conteneur n'est pas encore chargé, essayer de le créer nous-mêmes
        let totalContainer = findOrCreateTotalContainer();
        if (!totalContainer) {
            // Réessayer plus tard seulement si on n'a pas pu créer le conteneur
            setTimeout(() => transformReserverButton(reservationId, reservationStatus, isInitialLoad), 30);
            return;
        }

        // Créer et ajouter le bouton "Voir mes réservations" en PREMIER
        const reserverButton = document.createElement('button');
        reserverButton.className = 'reserver-button';
        reserverButton.innerHTML = 'Voir mes réservations <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

        reserverButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '/mes-reservations';
        });

        totalContainer.appendChild(reserverButton);

        // N'ajouter le bouton d'annulation QUE si le statut de réservation n'est pas 1 (payé)
        if (reservationStatus !== 1) {
            // Créer et ajouter le bouton d'annulation en SECOND
            const cancelButton = document.createElement('button');
            cancelButton.className = 'annuler-button';
            cancelButton.innerHTML = 'Annuler la réservation';

            cancelButton.addEventListener('click', function(e) {
                e.preventDefault();
                cancelReservation(reservationId);
            });

            totalContainer.appendChild(cancelButton);
        }
    }

    // function cancelReservation(reservationId) {
    //     fetch('/api/reservations/cancel', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    //             'X-Requested-With': 'XMLHttpRequest',
    //             'Accept': 'application/json'
    //         },
    //         body: JSON.stringify({
    //             reservation_id: reservationId
    //         })
    //     })
    //     .then(handleResponse)
    //     .then(data => {
    //         if (data.success) {
    //             console.log(data.message || 'Réservation annulée avec succès');

    //             // Mise à jour des variables
    //             window.hasExistingReservation = false;
    //             localStorage.removeItem('hasExistingReservation');
    //             localStorage.removeItem('reservationId');
    //             localStorage.removeItem('reservationStatus');

    //             // Restaurer l'état du bouton si le panier n'est pas vide
    //             const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
    //             if (cartCount > 0) {
    //                 createReserverButton(false);
    //             } else {
    //                 // Si le panier est vide, supprimer tous les boutons
    //                 removeAllButtons();
    //             }
    //         } else {
    //             console.error(data.message || 'Erreur lors de l\'annulation de la réservation');
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Erreur lors de l\'annulation de la réservation:', error);
    //     });
    // }

    function cancelReservation(reservationId) {
    // Récupérer le bouton et stocker son texte original
    const cancelButton = document.querySelector('.annuler-button');
    const originalText = cancelButton.innerHTML;

    // Changer le texte et désactiver le bouton pendant l'annulation
    cancelButton.innerHTML = 'Annulation en cours...';
    cancelButton.disabled = true;

    fetch('/api/reservations/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            reservation_id: reservationId
        })
    })
    .then(handleResponse)
    .then(data => {
        if (data.success) {
            console.log(data.message || 'Réservation annulée avec succès');

            // Mise à jour des variables
            window.hasExistingReservation = false;
            localStorage.removeItem('hasExistingReservation');
            localStorage.removeItem('reservationId');
            localStorage.removeItem('reservationStatus');

            // Restaurer l'état du bouton si le panier n'est pas vide
            const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
            if (cartCount > 0) {
                createReserverButton(false);
            } else {
                // Si le panier est vide, supprimer tous les boutons
                removeAllButtons();
            }
        } else {
            console.error(data.message || 'Erreur lors de l\'annulation de la réservation');
            // Restaurer le texte original et réactiver le bouton
            cancelButton.innerHTML = originalText;
            cancelButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Erreur lors de l\'annulation de la réservation:', error);
        // Restaurer le texte original et réactiver le bouton
        cancelButton.innerHTML = originalText;
        cancelButton.disabled = false;
    });
}


function createReserverButton(isInitialLoad = false) {
    // Ne pas créer le bouton si l'utilisateur a déjà une réservation
    if (window.hasExistingReservation === true) {
        // Au lieu de retourner simplement, on affiche le bouton "Voir mes réservations"
        const reservationId = localStorage.getItem('reservationId');
        const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
        if (reservationId) {
            transformReserverButton(parseInt(reservationId), reservationStatus, isInitialLoad);
        }
        return false;
    }

    // Supprimer d'abord les boutons existants pour éviter les doublons
    removeAllButtons();

    // Utiliser notre fonction pour trouver ou créer le conteneur
    const totalContainer = findOrCreateTotalContainer();
    if (!totalContainer) {
        setTimeout(() => createReserverButton(isInitialLoad), 30);
        return false;
    }

    const reservButton = document.createElement('button');
    reservButton.className = 'reserver-button';
    reservButton.innerHTML = 'Réserver <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    // On ajoute l'événement ici
    reservButton.addEventListener('click', handleReservationClick);

    // MODIFICATION: Vérifier directement depuis le DOM pour un état plus précis
    const expiredFormationElements = document.querySelectorAll('.formation-expired');
    const completeFormationElements = document.querySelectorAll('.formation-full');

    const hasExpiredFormations = expiredFormationElements.length > 0 || window.hasExpiredFormationsInCart;
    const hasCompleteFormations = completeFormationElements.length > 0 || window.hasCompleteFormationsInCart;

    // Mise à jour des variables globales pour cohérence
    window.hasExpiredFormationsInCart = hasExpiredFormations;
    window.hasCompleteFormationsInCart = hasCompleteFormations;

    if (hasExpiredFormations) {
        reservButton.disabled = true;
        reservButton.classList.add('disabled');
        reservButton.title = 'Votre panier contient des formations dont la date est dépassée';
    } else if (hasCompleteFormations) {
        reservButton.disabled = true;
        reservButton.classList.add('disabled');
        reservButton.title = 'Une ou plusieurs formations sont complètes';
    }

    totalContainer.appendChild(reservButton);

    return true;
}
function startDOMObserver() {
    if (!window.domChangeObserver) {
        window.domChangeObserver = new MutationObserver(function(mutations) {
            // Ne rien faire si les boutons sont déjà initialisés ou si la vérification est en cours
            if (window.checkingReservations) {
                return;
            }

            // Si des éléments sont ajoutés au DOM et que la page est déjà chargée
            if (document.readyState === 'complete') {
                const totalPrice = document.querySelector('.total-price');
                if (totalPrice && !document.querySelector('.reserver-button') && !document.querySelector('.annuler-button')) {
                    // Détecter si on doit afficher les boutons de réservation ou d'annulation
                    if (window.hasExistingReservation) {
                        const reservationId = localStorage.getItem('reservationId');
                        const reservationStatus = parseInt(localStorage.getItem('reservationStatus') || '0');
                        if (reservationId) {
                            transformReserverButton(parseInt(reservationId), reservationStatus, false);
                        }
                    } else {
                        // Seulement après avoir vérifié qu'il n'y a pas de réservation
                        const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
                        if (cartCount > 0) {
                            createReserverButton(false);
                        }
                    }
                }

                // MODIFICATION: Vérifier directement depuis le DOM plutôt que d'utiliser des Promises
                const expiredFormationElements = document.querySelectorAll('.formation-expired');
                const completeFormationElements = document.querySelectorAll('.formation-full');

                const hasExpiredFormations = expiredFormationElements.length > 0;
                const hasCompleteFormations = completeFormationElements.length > 0;

                // Mettre à jour les variables globales
                window.hasExpiredFormationsInCart = hasExpiredFormations;
                window.hasCompleteFormationsInCart = hasCompleteFormations;

                // Si on a le bouton "Réserver" (et non pas "Voir mes réservations")
                const reserveButton = document.querySelector('.reserver-button');
                if (reserveButton && !window.hasExistingReservation) {
                    if (hasExpiredFormations) {
                        // Priorité aux formations expirées
                        reserveButton.disabled = true;
                        reserveButton.classList.add('disabled');
                        reserveButton.title = 'Votre panier contient des formations dont la date est dépassée';
                    } else if (hasCompleteFormations) {
                        reserveButton.disabled = true;
                        reserveButton.classList.add('disabled');
                        reserveButton.title = 'Une ou plusieurs formations sont complètes';
                    } else {
                        reserveButton.disabled = false;
                        reserveButton.classList.remove('disabled');
                        reserveButton.removeAttribute('title');
                    }
                }

                // Lancer une vérification des dates si la fonction est disponible
                if (typeof window.checkFormationsDates === 'function' &&
                    !window.checkingFormationDates) {
                    window.checkingFormationDates = true;
                    window.checkFormationsDates().finally(() => {
                        window.checkingFormationDates = false;
                    });
                }
            }
        });

        window.domChangeObserver.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
}
  function handleReservationClick(e) {
    e.preventDefault();
    e.stopPropagation(); // Empêcher la propagation de l'événement

    const reservButton = e.currentTarget;
    const originalText = reservButton.innerHTML;

    // Vérifier si on a une fonction de validation
    if (typeof window.validatedReservationClick === 'function') {
        // Ne pas modifier le bouton ici, car cela sera fait dans validatedReservationClick
        window.validatedReservationClick(e, () => {
            // La modification du bouton est déjà faite dans validatedReservationClick
            processReservation()
                .finally(() => {
                    // Réactiver le bouton après traitement (au cas où la réservation échoue)
                    reservButton.disabled = false;
                    reservButton.innerHTML = originalText;
                });
        });
    } else {
        // Si la fonction n'existe pas, traiter directement
        // Changer le texte du bouton immédiatement
        reservButton.innerHTML = 'Réservation en cours...';
        reservButton.disabled = true;

        processReservation()
            .finally(() => {
                // Réactiver le bouton après traitement (au cas où la réservation échoue)
                reservButton.disabled = false;
                reservButton.innerHTML = originalText;
            });
    }
}


    function processReservation() {
        return checkUserAuthentication()
            .then(authenticated => {
                if (authenticated) {
                    return checkCartNotEmpty()
                        .then(notEmpty => {
                            if (notEmpty) {
                                return createReservation()
                                    .then(response => {
                                        handleReservationResponse(response);
                                        return response; // Retourner la réponse pour le chaînage
                                    });
                            } else {
                                console.log('Votre panier est vide');
                                throw new Error('Panier vide');
                            }
                        });
                } else {
                    console.log('Veuillez vous connecter pour réserver');
                    redirectToLogin();
                    throw new Error('Non authentifié');
                }
            });
    }

    function checkUserAuthentication() {
        return fetch('/api/user/check-auth', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            return data.authenticated;
        })
        .catch(error => {
            console.error('Erreur lors de la vérification de l\'authentification:', error);
            return false;
        });
    }

    function checkCartNotEmpty() {
        return fetch('/panier/items-count', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(handleResponse)
        .then(data => {
            return data.count > 0;
        });
    }

    function createReservation() {
        return fetch('/api/reservations/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                reservation_date: new Date().toISOString().split('T')[0], // Date actuelle
                reservation_time: new Date().toTimeString().split(' ')[0] // Heure actuelle
            })
        })
        .then(response => {
            if (!response.ok) {
                // Si le statut n'est pas OK (2xx), on lance une erreur
                return response.json().then(errorData => {
                    throw new Error(errorData.message || 'Erreur lors de la réservation');
                });
            }
            return response.json();
        });
    }

    function handleReservationResponse(response) {
        if (response.success) {
            console.log(response.message || 'Réservation effectuée avec succès');

            // Mettre à jour la variable globale et localStorage
            window.hasExistingReservation = true;
            localStorage.setItem('hasExistingReservation', 'true');

            // Récupérer le statut de la réservation (par défaut 0 si non fourni)
            const status = response.status || 0;
            window.reservationStatus = status;
            localStorage.setItem('reservationStatus', status.toString());

            // Transformer le bouton immédiatement
            if (response.reservation_id) {
                localStorage.setItem('reservationId', response.reservation_id.toString());
                transformReserverButton(response.reservation_id, status, false);
            }

            // Si la réservation est réussie et qu'il faut vider le panier
            if (response.clearCart) {
                // Mise à jour du compteur sans supprimer visuellement le bouton
                localStorage.setItem('cartCount', '0');
            }

            // Redirection éventuelle vers la page de confirmation
            if (response.redirectUrl) {
                window.location.href = response.redirectUrl;
            }
        } else {
            console.error(response.message || 'Erreur lors de la réservation');
            // Restaurer le texte original du bouton
            const reservButton = document.querySelector('.reserver-button');
            if (reservButton) {
                reservButton.innerHTML = 'Réserver <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }
            throw new Error(response.message || 'Erreur lors de la réservation');
        }
    }

    function handleReservationError(error) {
        console.error('Erreur lors de la réservation:', error);
    }

    function redirectToLogin() {
        // Sauvegarder l'URL actuelle pour rediriger après la connexion
        localStorage.setItem('redirectAfterLogin', window.location.href);
        window.location.href = '/login';
    }

    function handleResponse(response) {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    }

    // Voici la fonction transformReserverButton modifiée
function transformReserverButton(reservationId, reservationStatus = 0, isInitialLoad = false) {
    // Supprimer d'abord les boutons existants pour éviter les doublons
    removeAllButtons();

    // Si le conteneur n'est pas encore chargé, essayer de le créer nous-mêmes
    let totalContainer = findOrCreateTotalContainer();
    if (!totalContainer) {
        // Réessayer plus tard seulement si on n'a pas pu créer le conteneur
        setTimeout(() => transformReserverButton(reservationId, reservationStatus, isInitialLoad), 30);
        return;
    }

    // Créer et ajouter le bouton "Voir mes réservations" en PREMIER
    const reserverButton = document.createElement('button');
    reserverButton.className = 'reserver-button';
    reserverButton.innerHTML = 'Voir mes réservations <svg class="arrow-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12H19M19 12L12 5M19 12L12 19" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    // Le bouton "Voir mes réservations" doit toujours rester actif
    // Pas de vérification de formations complètes ici
    reserverButton.addEventListener('click', function(e) {
        e.preventDefault();
        window.location.href = '/mes-reservations';
    });

    totalContainer.appendChild(reserverButton);

    // N'ajouter le bouton d'annulation QUE si le statut de réservation n'est pas 1 (payé)
    if (reservationStatus !== 1) {
        // Créer et ajouter le bouton d'annulation en SECOND
        const cancelButton = document.createElement('button');
        cancelButton.className = 'annuler-button';
        cancelButton.innerHTML = 'Annuler la réservation';

        cancelButton.addEventListener('click', function(e) {
            e.preventDefault();
            cancelReservation(reservationId);
        });

        totalContainer.appendChild(cancelButton);
    }
}

// Démarrer l'observateur DOM après l'initialisation
    window.addEventListener('load', startDOMObserver);

    // Exposer les fonctions nécessaires globalement
    window.processReservation = processReservation;
    window.createReservation = createReservation;
    window.checkUserAuthentication = checkUserAuthentication;
    window.transformReserverButton = transformReserverButton;
    window.cancelReservation = cancelReservation;
    window.checkExistingReservations = checkExistingReservations;
    window.createReserverButton = createReserverButton;
})();

