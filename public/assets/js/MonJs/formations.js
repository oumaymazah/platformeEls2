// // // Filtrage dynamique des formations :

// // // Par catégorie (via des boutons radio)
// // // Par statut (via un menu déroulant)
// // // Par terme de recherche (via un champ de texte)


// // // Suppression de formations :

// // // Bouton de suppression qui déclenche un modal de confirmation
// // // Requête AJAX pour supprimer la formation côté serveur
// // // Notification de succès ou d'échec


// // // Gestion de l'affichage :

// // // Mise à jour dynamique des formations affichées sans rechargement de page
// // // Gestion d'état de chargement avec spinner
// // // Message lorsqu'aucune formation n'est disponible


// // // Gestion de l'URL :

// // // Mise à jour des paramètres d'URL pour refléter les filtres sélectionnés
// // // Restauration des filtres à partir de l'URL lors du chargement de la page




// // $(document).ready(function() {
// //     // Sélecteurs
// //     const formationsContainer = $('.formations-container');
// //     const searchInput = $('#search-formations');
// //     const categoryFilter = $('input[name="category_filter"]');
// //     const statusFilter = $('.status-filter');
// //  // Handler pour la suppression de formation
// // $(document).on('click', '.delete-formation', function() {
// //     // Récupérer l'ID directement depuis l'élément
// //     const formationId = $(this).data('id');

// //     console.log("ID de formation à supprimer:", formationId);

// //     // Vérification que l'ID existe
// //     if (!formationId) {
// //         console.error("Erreur: data-id manquant sur le bouton de suppression");
// //         return;
// //     }

// //     // Stocker l'ID dans une variable globale temporaire
// //     window.formationIdToDelete = formationId;

// //     // Afficher le modal de confirmation
// //     $('#deleteConfirmationModal').modal('show');
// // });

// // // Intercepter la soumission du formulaire de suppression
// // $('#deleteFormationForm').on('submit', function(e) {
// //     e.preventDefault();

// //     // Récupérer l'ID depuis la variable globale
// //     const formationId = window.formationIdToDelete;
// //     const token = $('input[name="_token"]', this).val();

// //     console.log("Tentative de suppression de la formation avec ID:", formationId);

// //     // Vérification finale
// //     if (!formationId) {
// //         console.error("ID de formation non disponible pour la suppression");
// //         return;
// //     }

// //     // Construire l'URL explicitement
// //     const url = `/formation/${formationId}`;

// //     console.log("URL de suppression:", url);

// //     // Fermer le modal
// //     $('#deleteConfirmationModal').modal('hide');

// //     // Effectuer la requête AJAX pour la suppression
// //     $.ajax({
// //         url: url,
// //         type: 'DELETE',
// //         data: {
// //             _token: token
// //         },
// //         beforeSend: function(xhr) {
// //             // Ajouter explicitement le token CSRF dans les en-têtes
// //             xhr.setRequestHeader('X-CSRF-TOKEN', token);
// //         },
// //         success: function(response) {
// //             console.log("Suppression réussie:", response);
// //             showNotification('success', 'Formation supprimée avec succès');
// //             loadFilteredFormations();
// //         },
// //         error: function(xhr, status, error) {
// //             console.error("Détails de l'erreur:", xhr.status, xhr.responseText);
// //             showNotification('error', 'Erreur lors de la suppression de la formation');
// //         }
// //     });
// // });
// //     // Fonction pour afficher une notification
// //     function showNotification(type, message) {
// //         const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
// //         const notification = $(`
// //             <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
// //                 ${message}
// //                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
// //             </div>
// //         `);

// //         // Ajouter la notification à la page
// //         $('.notifications-container').html(notification);

// //         // Supprimer automatiquement après 3 secondes
// //         setTimeout(function() {
// //             notification.alert('close');
// //         }, 3000);
// //     }

// //     // Filtrage par catégorie et statut avec AJAX
// //     categoryFilter.on('change', loadFilteredFormations);
// //     statusFilter.on('change', loadFilteredFormations);
// //     searchInput.on('keyup', debounce(loadFilteredFormations, 30)); // Ajouter un délai pour éviter trop de requêtes

// //     // Fonction pour débouncer les événements
// //     function debounce(func, wait) {
// //         let timeout;
// //         return function() {
// //             const context = this, args = arguments;
// //             clearTimeout(timeout);
// //             timeout = setTimeout(function() {
// //                 func.apply(context, args);
// //             }, wait);
// //         };
// //     }

// //     function initStatusFilter() {
// //         // Vérifier si un statut est déjà dans l'URL
// //         const urlParams = new URLSearchParams(window.location.search);
// //         if (!urlParams.has('status')) {
// //             // Si aucun statut n'est spécifié dans l'URL, définir sur "Publiée" par défaut
// //             statusFilter.val('1');
// //         }

// //         // Si le select utilise select2, mettre à jour l'interface
// //         if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
// //             statusFilter.trigger('change.select2');
// //         }
// //     }

// //     function loadFilteredFormations() {
// //         const categoryId = $('input[name="category_filter"]:checked').val();
// //         const status = statusFilter.val();
// //         const searchTerm = searchInput.val();

// //         console.log("Filtrage:", { categoryId, status, searchTerm });

// //         // Mise à jour de l'URL
// //         const url = new URL(window.location);

// //         if (categoryId) url.searchParams.set('category_id', categoryId);
// //         else url.searchParams.delete('category_id');

// //         if (status !== '') url.searchParams.set('status', status);
// //         else url.searchParams.delete('status');

// //         if (searchTerm) url.searchParams.set('search', searchTerm);
// //         else url.searchParams.delete('search');

// //         window.history.pushState({}, '', url);

// //         // Préparation des paramètres AJAX
// //         const ajaxParams = {};

// //         if (categoryId) ajaxParams.category_id = categoryId;
// //         if (status !== '') ajaxParams.status = status;
// //         if (searchTerm) ajaxParams.search = searchTerm;

// //         // Requête AJAX
// //         $.ajax({
// //             url: window.location.pathname,
// //             type: 'GET',
// //             data: ajaxParams,
// //             dataType: 'json',
// //             beforeSend: function() {
// //                 formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
// //             },
// //             success: function(response) {
// //                 console.log("Réponse:", response);
// //                 updateFormationsDisplay(response);
// //             },
// //             error: function(xhr, status, error) {
// //                 console.error("Erreur:", error);
// //                 formationsContainer.html(`<div class="col-12"><div class="alert alert-danger">Une erreur s'est produite.</div></div>`);
// //             }
// //         });
// //     }
// //     function updateFormationsDisplay(data) {
// //         formationsContainer.empty();

// //         if (!data.formations || data.formations.length === 0) {
// //             formationsContainer.html(`
// //                 <div class="col-12">
// //                     <div class="alert alert-info">
// //                         Aucune formation disponible.
// //                     </div>
// //                 </div>
// //             `);
// //             return;
// //         }

// //         // Mettre à jour le titre si nécessaire
// //         if (data.title) {
// //             $('.breadcrumb_title h3').text('Formations: ' + data.title);
// //         }

// //         // Nombre de formations par ligne
// //         const coursesPerRow = 3;

// //         // Ajouter chaque formation en utilisant la fonction du fichier formations-details.js
// //         data.formations.forEach((formation, index) => {
// //             const formationHtml = createFormationCard(formation);
// //             formationsContainer.append(formationHtml);

// //             // Ajouter un espace après chaque ligne complète (tous les 3 éléments)
// //             // L'opération modulo (%) retourne 0 lorsque index+1 est un multiple de coursesPerRow
// //             if ((index + 1) % coursesPerRow === 0 && index < data.formations.length - 1) {
// //                 formationsContainer.append('<div class="w-100 mb-4"></div>'); // Ajoute un espacement vertical entre les lignes
// //             }
// //         });

// //         // Réinitialiser les tooltips et autres plugins
// //         if (typeof feather !== 'undefined') {
// //             feather.replace();
// //         }

// //         if ($.fn.tooltip) {
// //             $('[data-toggle="tooltip"]').tooltip();
// //         }
// //     }

// //     // Initialisation des plugins
// //     if ($.fn.select2) {
// //         $('.select2').select2();
// //     }

// //     function applyUrlFilters() {
// //         const urlParams = new URLSearchParams(window.location.search);

// //         // Si l'URL contient des paramètres de filtre, les appliquer
// //         if (urlParams.has('category_id')) {
// //             const categoryId = urlParams.get('category_id');
// //             // Sélectionner la catégorie correspondante
// //             $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
// //         } else {
// //             // Sinon, sélectionner "Tous"
// //             $('#category-all').prop('checked', true);
// //         }

// //         if (urlParams.has('status')) {
// //             const status = urlParams.get('status');
// //             statusFilter.val(status);
// //         } else {
// //             // Sinon, sélectionner "Publiée" par défaut
// //             statusFilter.val('1');
// //         }

// //         // Si le select utilise select2, mettre à jour l'interface
// //         if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
// //             statusFilter.trigger('change.select2');
// //         }

// //         if (urlParams.has('search')) {
// //             searchInput.val(urlParams.get('search'));
// //         }
// //     }

// //     // Lorsque l'utilisateur rafraîchit la page, réinitialiser les filtres
// //     $(window).on('beforeunload', function() {
// //         // Supprimer les paramètres de l'URL
// //         const url = new URL(window.location);
// //         url.searchParams.delete('category_id');
// //         url.searchParams.delete('status');
// //         url.searchParams.delete('search');
// //         window.history.replaceState({}, '', url);
// //     });

// //     // Assurer que les filtres par défaut soient appliqués au chargement initial
// //     applyUrlFilters();
// //     initStatusFilter();


// //     // Chargement initial pour afficher toutes les formations
// //     loadFilteredFormations();
// // });














// // Filtrage dynamique des formations :

// // Par catégorie (via des boutons radio)
// // Par statut (via un menu déroulant)
// // Par terme de recherche (via un champ de texte)


// // Suppression de formations :

// // Bouton de suppression qui déclenche un modal de confirmation
// // Requête AJAX pour supprimer la formation côté serveur
// // Notification de succès ou d'échec


// // Gestion de l'affichage :

// // Mise à jour dynamique des formations affichées sans rechargement de page
// // Gestion d'état de chargement avec spinner
// // Message lorsqu'aucune formation n'est disponible


// // Gestion de l'URL :

// // Mise à jour des paramètres d'URL pour refléter les filtres sélectionnés
// // Restauration des filtres à partir de l'URL lors du chargement de la page




// $(document).ready(function() {
//     // Sélecteurs
//     // Vérifier si l'utilisateur est admin en récupérant l'info du backend
//     // Cette information doit être ajoutée par le controller dans la vue
//     const userIsAdmin = $('body').data('user-is-admin');
//     console.log("L'utilisateur est admin:", userIsAdmin);
//     const formationsContainer = $('.formations-container');
//     const searchInput = $('#search-formations');
//     const categoryFilter = $('input[name="category_filter"]');
//     const statusFilter = $('.status-filter');
//  // Handler pour la suppression de formation
// $(document).on('click', '.delete-formation', function() {
//     // Récupérer l'ID directement depuis l'élément
//     const formationId = $(this).data('id');

//     console.log("ID de formation à supprimer:", formationId);

//     // Vérification que l'ID existe
//     if (!formationId) {
//         console.error("Erreur: data-id manquant sur le bouton de suppression");
//         return;
//     }

//     // Stocker l'ID dans une variable globale temporaire
//     window.formationIdToDelete = formationId;

//     // Afficher le modal de confirmation
//     $('#deleteConfirmationModal').modal('show');
// });

// // Intercepter la soumission du formulaire de suppression
// $('#deleteFormationForm').on('submit', function(e) {
//     e.preventDefault();

//     // Récupérer l'ID depuis la variable globale
//     const formationId = window.formationIdToDelete;
//     const token = $('input[name="_token"]', this).val();

//     console.log("Tentative de suppression de la formation avec ID:", formationId);

//     // Vérification finale
//     if (!formationId) {
//         console.error("ID de formation non disponible pour la suppression");
//         return;
//     }

//     // Construire l'URL explicitement
//     const url = `/formation/${formationId}`;

//     console.log("URL de suppression:", url);

//     // Fermer le modal
//     $('#deleteConfirmationModal').modal('hide');

//     // Effectuer la requête AJAX pour la suppression
//     $.ajax({
//         url: url,
//         type: 'DELETE',
//         data: {
//             _token: token
//         },
//         beforeSend: function(xhr) {
//             // Ajouter explicitement le token CSRF dans les en-têtes
//             xhr.setRequestHeader('X-CSRF-TOKEN', token);
//         },
//         success: function(response) {
//             console.log("Suppression réussie:", response);
//             showNotification('success', 'Formation supprimée avec succès');
//             loadFilteredFormations();
//         },
//         error: function(xhr, status, error) {
//             console.error("Détails de l'erreur:", xhr.status, xhr.responseText);
//             showNotification('error', 'Erreur lors de la suppression de la formation');
//         }
//     });
// });
//     // Fonction pour afficher une notification
//     function showNotification(type, message) {
//         const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
//         const notification = $(`
//             <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
//                 ${message}
//                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//             </div>
//         `);

//         // Ajouter la notification à la page
//         $('.notifications-container').html(notification);

//         // Supprimer automatiquement après 3 secondes
//         setTimeout(function() {
//             notification.alert('close');
//         }, 3000);
//     }

//     // Filtrage par catégorie et statut avec AJAX
//     categoryFilter.on('change', loadFilteredFormations);
//     statusFilter.on('change', loadFilteredFormations);
//     searchInput.on('keyup', debounce(loadFilteredFormations, 30)); // Ajouter un délai pour éviter trop de requêtes

//     // Fonction pour débouncer les événements
//     function debounce(func, wait) {
//         let timeout;
//         return function() {
//             const context = this, args = arguments;
//             clearTimeout(timeout);
//             timeout = setTimeout(function() {
//                 func.apply(context, args);
//             }, wait);
//         };
//     }
//     function loadFilteredFormations() {
//         const categoryId = $('input[name="category_filter"]:checked').val();
//         const status = statusFilter.val();
//         const searchTerm = searchInput.val();

//         console.log("Filtrage:", { categoryId, status, searchTerm });

//         // Mise à jour de l'URL
//         const url = new URL(window.location);

//         if (categoryId) url.searchParams.set('category_id', categoryId);
//         else url.searchParams.delete('category_id');

//         // Ne pas ajouter le paramètre status à l'URL si on est étudiant
//         // On peut déterminer si l'utilisateur est admin via un data attribute
//         const userIsAdmin = $('body').data('user-is-admin');

//         if (status !== '' && userIsAdmin) url.searchParams.set('status', status);
//         else url.searchParams.delete('status');

//         if (searchTerm) url.searchParams.set('search', searchTerm);
//         else url.searchParams.delete('search');

//         window.history.pushState({}, '', url);

//         // Préparation des paramètres AJAX
//         const ajaxParams = {};

//         if (categoryId) ajaxParams.category_id = categoryId;

//         // Toujours envoyer le status dans la requête AJAX, même s'il n'est pas affiché dans l'URL
//         if (status !== '') ajaxParams.status = status;

//         if (searchTerm) ajaxParams.search = searchTerm;

//         // Requête AJAX
//         $.ajax({
//             url: window.location.pathname,
//             type: 'GET',
//             data: ajaxParams,
//             dataType: 'json',
//             beforeSend: function() {
//                 formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
//             },
//             success: function(response) {
//                 console.log("Réponse:", response);
//                 updateFormationsDisplay(response);
//             },
//             error: function(xhr, status, error) {
//                 console.error("Erreur:", error);
//                 formationsContainer.html(`<div class="col-12"><div class="alert alert-danger">Une erreur s'est produite.</div></div>`);
//             }
//         });
//     }

//     function applyUrlFilters() {
//     const urlParams = new URLSearchParams(window.location.search);
//     const userIsAdmin = $('body').data('user-is-admin');
//     const userIsProf = $('body').data('user-is-prof');

//     // Si l'URL contient des paramètres de filtre, les appliquer
//     if (urlParams.has('category_id')) {
//         const categoryId = urlParams.get('category_id');
//         // Sélectionner la catégorie correspondante
//         $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
//     } else {
//         // Sinon, sélectionner "Tous"
//         $('#category-all').prop('checked', true);
//     }

//     // Pour le status, vérifier d'abord l'URL
//     if (urlParams.has('status')) {
//         const status = urlParams.get('status');
//         statusFilter.val(status);
//     } else {
//         // Si rien dans l'URL, appliquer la valeur par défaut selon le rôle
//         if (userIsAdmin || userIsProf) {
//             // Pour admin et prof, ne pas appliquer de filtre par défaut (tous)
//             statusFilter.val('');
//         } else {
//             // Pour les étudiants, filtrer sur les formations publiées
//             statusFilter.val('1');
//         }
//     }

//     // Si le select utilise select2, mettre à jour l'interface
//     if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
//         statusFilter.trigger('change.select2');
//     }

//     if (urlParams.has('search')) {
//         searchInput.val(urlParams.get('search'));
//     }
// }

//     // Modifiez la fonction initStatusFilter pour qu'elle ne fasse rien si l'initialisation
//     // a déjà été prise en charge par applyUrlFilters
//     function initStatusFilter() {
//         // Cette fonction est maintenant gérée par applyUrlFilters
//         // Nous la laissons vide pour compatibilité avec le code existant
//     }
//     // function initStatusFilter() {
//     //     // Vérifier si un statut est déjà dans l'URL
//     //     const urlParams = new URLSearchParams(window.location.search);
//     //     if (!urlParams.has('status')) {
//     //         // Si aucun statut n'est spécifié dans l'URL, définir sur "Publiée" par défaut
//     //         statusFilter.val('1');
//     //     }

//     //     // Si le select utilise select2, mettre à jour l'interface
//     //     if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
//     //         statusFilter.trigger('change.select2');
//     //     }
//     // }

//     // function loadFilteredFormations() {
//     //     const categoryId = $('input[name="category_filter"]:checked').val();
//     //     const status = statusFilter.val();
//     //     const searchTerm = searchInput.val();

//     //     console.log("Filtrage:", { categoryId, status, searchTerm });

//     //     // Mise à jour de l'URL
//     //     const url = new URL(window.location);

//     //     if (categoryId) url.searchParams.set('category_id', categoryId);
//     //     else url.searchParams.delete('category_id');

//     //     if (status !== '') url.searchParams.set('status', status);
//     //     else url.searchParams.delete('status');

//     //     if (searchTerm) url.searchParams.set('search', searchTerm);
//     //     else url.searchParams.delete('search');

//     //     window.history.pushState({}, '', url);

//     //     // Préparation des paramètres AJAX
//     //     const ajaxParams = {};

//     //     if (categoryId) ajaxParams.category_id = categoryId;
//     //     if (status !== '') ajaxParams.status = status;
//     //     if (searchTerm) ajaxParams.search = searchTerm;

//     //     // Requête AJAX
//     //     $.ajax({
//     //         url: window.location.pathname,
//     //         type: 'GET',
//     //         data: ajaxParams,
//     //         dataType: 'json',
//     //         beforeSend: function() {
//     //             formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
//     //         },
//     //         success: function(response) {
//     //             console.log("Réponse:", response);
//     //             updateFormationsDisplay(response);
//     //         },
//     //         error: function(xhr, status, error) {
//     //             console.error("Erreur:", error);
//     //             formationsContainer.html(`<div class="col-12"><div class="alert alert-danger">Une erreur s'est produite.</div></div>`);
//     //         }
//     //     });
//     // }
//     function updateFormationsDisplay(data) {
//         formationsContainer.empty();

//         if (!data.formations || data.formations.length === 0) {
//             formationsContainer.html(`
//                 <div class="col-12">
//                     <div class="alert alert-info">
//                         Aucune formation disponible.
//                     </div>
//                 </div>
//             `);
//             return;
//         }

//         // Mettre à jour le titre si nécessaire
//         if (data.title) {
//             $('.breadcrumb_title h3').text('Formations: ' + data.title);
//         }

//         // Nombre de formations par ligne
//         const coursesPerRow = 3;

//         // Ajouter chaque formation en utilisant la fonction du fichier formations-details.js
//         data.formations.forEach((formation, index) => {
//             const formationHtml = createFormationCard(formation);
//             formationsContainer.append(formationHtml);

//             // Ajouter un espace après chaque ligne complète (tous les 3 éléments)
//             // L'opération modulo (%) retourne 0 lorsque index+1 est un multiple de coursesPerRow
//             if ((index + 1) % coursesPerRow === 0 && index < data.formations.length - 1) {
//                 formationsContainer.append('<div class="w-100 mb-4"></div>'); // Ajoute un espacement vertical entre les lignes
//             }
//         });

//         // Réinitialiser les tooltips et autres plugins
//         if (typeof feather !== 'undefined') {
//             feather.replace();
//         }

//         if ($.fn.tooltip) {
//             $('[data-toggle="tooltip"]').tooltip();
//         }
//     }

//     // Initialisation des plugins
//     if ($.fn.select2) {
//         $('.select2').select2();
//     }

//     // function applyUrlFilters() {
//     //     const urlParams = new URLSearchParams(window.location.search);

//     //     // Si l'URL contient des paramètres de filtre, les appliquer
//     //     if (urlParams.has('category_id')) {
//     //         const categoryId = urlParams.get('category_id');
//     //         // Sélectionner la catégorie correspondante
//     //         $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
//     //     } else {
//     //         // Sinon, sélectionner "Tous"
//     //         $('#category-all').prop('checked', true);
//     //     }

//     //     if (urlParams.has('status')) {
//     //         const status = urlParams.get('status');
//     //         statusFilter.val(status);
//     //     } else {
//     //         // Sinon, sélectionner "Publiée" par défaut
//     //         statusFilter.val('1');
//     //     }

//     //     // Si le select utilise select2, mettre à jour l'interface
//     //     if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
//     //         statusFilter.trigger('change.select2');
//     //     }

//     //     if (urlParams.has('search')) {
//     //         searchInput.val(urlParams.get('search'));
//     //     }
//     // }

//     // Lorsque l'utilisateur rafraîchit la page, réinitialiser les filtres
//     $(window).on('beforeunload', function() {
//         // Supprimer les paramètres de l'URL
//         const url = new URL(window.location);
//         url.searchParams.delete('category_id');
//         url.searchParams.delete('status');
//         url.searchParams.delete('search');
//         window.history.replaceState({}, '', url);
//     });

//     // Assurer que les filtres par défaut soient appliqués au chargement initial
//     applyUrlFilters();
//     initStatusFilter();


//     // Chargement initial pour afficher toutes les formations
//     loadFilteredFormations();
// });





// // $(document).ready(function() {
// //     // Sélecteurs
// //     // Vérifier si l'utilisateur est admin en récupérant l'info du backend
// //     // Cette information doit être ajoutée par le controller dans la vue
// //     const userIsAdmin = $('body').data('user-is-admin');
// //     console.log("L'utilisateur est admin:", userIsAdmin);
// //     const formationsContainer = $('.formations-container');
// //     const searchInput = $('#search-formations');
// //     const categoryFilter = $('input[name="category_filter"]');
// //     const statusFilter = $('.status-filter');
// //  // Handler pour la suppression de formation
// // $(document).on('click', '.delete-formation', function() {
// //     // Récupérer l'ID directement depuis l'élément
// //     const formationId = $(this).data('id');

// //     console.log("ID de formation à supprimer:", formationId);

// //     // Vérification que l'ID existe
// //     if (!formationId) {
// //         console.error("Erreur: data-id manquant sur le bouton de suppression");
// //         return;
// //     }

// //     // Stocker l'ID dans une variable globale temporaire
// //     window.formationIdToDelete = formationId;

// //     // Afficher le modal de confirmation
// //     $('#deleteConfirmationModal').modal('show');
// // });

// // // Intercepter la soumission du formulaire de suppression
// // $('#deleteFormationForm').on('submit', function(e) {
// //     e.preventDefault();

// //     // Récupérer l'ID depuis la variable globale
// //     const formationId = window.formationIdToDelete;
// //     const token = $('input[name="_token"]', this).val();

// //     console.log("Tentative de suppression de la formation avec ID:", formationId);

// //     // Vérification finale
// //     if (!formationId) {
// //         console.error("ID de formation non disponible pour la suppression");
// //         return;
// //     }

// //     // Construire l'URL explicitement
// //     const url = `/formation/${formationId}`;

// //     console.log("URL de suppression:", url);

// //     // Fermer le modal
// //     $('#deleteConfirmationModal').modal('hide');

// //     // Effectuer la requête AJAX pour la suppression
// //     $.ajax({
// //         url: url,
// //         type: 'DELETE',
// //         data: {
// //             _token: token
// //         },
// //         beforeSend: function(xhr) {
// //             // Ajouter explicitement le token CSRF dans les en-têtes
// //             xhr.setRequestHeader('X-CSRF-TOKEN', token);
// //         },
// //         success: function(response) {
// //             console.log("Suppression réussie:", response);
// //             showNotification('success', 'Formation supprimée avec succès');
// //             loadFilteredFormations();
// //         },
// //         error: function(xhr, status, error) {
// //             console.error("Détails de l'erreur:", xhr.status, xhr.responseText);
// //             showNotification('error', 'Erreur lors de la suppression de la formation');
// //         }
// //     });
// // });
// //     // Fonction pour afficher une notification
// //     function showNotification(type, message) {
// //         const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
// //         const notification = $(`
// //             <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
// //                 ${message}
// //                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
// //             </div>
// //         `);

// //         // Ajouter la notification à la page
// //         $('.notifications-container').html(notification);

// //         // Supprimer automatiquement après 3 secondes
// //         setTimeout(function() {
// //             notification.alert('close');
// //         }, 3000);
// //     }

// //     // Filtrage par catégorie et statut avec AJAX
// //     categoryFilter.on('change', loadFilteredFormations);
// //     statusFilter.on('change', loadFilteredFormations);
// //     searchInput.on('keyup', debounce(loadFilteredFormations, 30)); // Ajouter un délai pour éviter trop de requêtes

// //     // Fonction pour débouncer les événements
// //     function debounce(func, wait) {
// //         let timeout;
// //         return function() {
// //             const context = this, args = arguments;
// //             clearTimeout(timeout);
// //             timeout = setTimeout(function() {
// //                 func.apply(context, args);
// //             }, wait);
// //         };
// //     }
// // // Modifier la gestion de l'icône de suppression :
// // $(document).on('click', '.product-search i.fa-times', function() {
// //     const searchInput = $('#search-formations');

// //     // Vider le champ de recherche
// //     searchInput.val('');

// //     // Remettre l'icône de recherche
// //     $(this).removeClass('fa-times').addClass('fa-search');

// //     // Recharger les formations immédiatement avec un appel explicite
// //     loadFilteredFormations();
// // });

// // // Modification de la fonction updateFormationsDisplay pour corriger le problème
// // function updateFormationsDisplay(data) {
// //     formationsContainer.empty();

// //     // Mettre à jour l'icône de recherche si nécessaire
// //     const searchIcon = $('.product-search i');
// //     if (searchIcon.length > 0) {
// //         if ($('#search-formations').val().trim() !== '') {
// //             searchIcon.removeClass('fa-search').addClass('fa-times');
// //         } else {
// //             searchIcon.removeClass('fa-times').addClass('fa-search');
// //         }
// //     }

// //     // Vérifier si aucune formation n'est disponible
// //     if (!data.formations || data.formations.length === 0) {
// //         formationsContainer.html(`
// //             <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
// //                 <div class="alert alert-info text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
// //                     <i class="fa fa-info-circle fa-2x mb-3"></i>
// //                     <h5>Aucune formation disponible</h5>
// //                     <p class="mb-0">Essayez de modifier vos critères de recherche ou de filtrage.</p>
// //                 </div>
// //             </div>
// //         `);
// //         return;
// //     }

// //     // Mettre à jour le titre si nécessaire
// //     if (data.title) {
// //         $('.breadcrumb_title h3').text('Formations: ' + data.title);
// //     }

// //     // Nombre de formations par ligne
// //     const coursesPerRow = 3;

// //     // Ajouter chaque formation en utilisant la fonction du fichier formations-details.js
// //     data.formations.forEach((formation, index) => {
// //         const formationHtml = createFormationCard(formation);
// //         formationsContainer.append(formationHtml);

// //         // Ajouter un espace après chaque ligne complète (tous les 3 éléments)
// //         if ((index + 1) % coursesPerRow === 0 && index < data.formations.length - 1) {
// //             formationsContainer.append('<div class="w-100 mb-4"></div>');
// //         }
// //     });

// //     // Réinitialiser les tooltips et autres plugins
// //     if (typeof feather !== 'undefined') {
// //         feather.replace();
// //     }

// //     if ($.fn.tooltip) {
// //         $('[data-toggle="tooltip"]').tooltip();
// //     }
// // }

// // // Modification de la fonction keyup pour le champ de recherche
// // searchInput.on('keyup', function(e) {
// //     // Si la touche Escape est pressée ou si le champ est vide
// //     if (e.key === 'Escape' || $(this).val() === '') {
// //         // Si le champ est vide, mettre à jour l'icône et recharger immédiatement
// //         if ($(this).val() === '') {
// //             $('.product-search i').removeClass('fa-times').addClass('fa-search');
// //             loadFilteredFormations();
// //         }
// //     } else {
// //         // Pour les autres touches, utiliser le debounce normal
// //         debounce(loadFilteredFormations, 30)();
// //     }
// // });

// //     function applyUrlFilters() {
// //     const urlParams = new URLSearchParams(window.location.search);
// //     const userIsAdmin = $('body').data('user-is-admin');
// //     const userIsProf = $('body').data('user-is-prof');

// //     // Si l'URL contient des paramètres de filtre, les appliquer
// //     if (urlParams.has('category_id')) {
// //         const categoryId = urlParams.get('category_id');
// //         // Sélectionner la catégorie correspondante
// //         $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
// //     } else {
// //         // Sinon, sélectionner "Tous"
// //         $('#category-all').prop('checked', true);
// //     }

// //     // Pour le status, vérifier d'abord l'URL
// //     if (urlParams.has('status')) {
// //         const status = urlParams.get('status');
// //         statusFilter.val(status);
// //     } else {
// //         // Si rien dans l'URL, appliquer la valeur par défaut selon le rôle
// //         if (userIsAdmin || userIsProf) {
// //             // Pour admin et prof, ne pas appliquer de filtre par défaut (tous)
// //             statusFilter.val('');
// //         } else {
// //             // Pour les étudiants, filtrer sur les formations publiées
// //             statusFilter.val('1');
// //         }
// //     }

// //     // Si le select utilise select2, mettre à jour l'interface
// //     if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
// //         statusFilter.trigger('change.select2');
// //     }

// //     if (urlParams.has('search')) {
// //         searchInput.val(urlParams.get('search'));
// //     }
// // }


// //     function initStatusFilter() {
// //     }


// //     // Initialisation des plugins
// //     if ($.fn.select2) {
// //         $('.select2').select2();
// //     }
// //     // 1. Ajout d'un gestionnaire pour l'icône de recherche
// // $('.product-search i.fa-search').on('click', function() {
// //     const searchInput = $('#search-formations');

// //     // Si le champ n'est pas vide, on ajoute une classe et on change l'icône
// //     if (searchInput.val().trim() !== '') {
// //         $(this).removeClass('fa-search').addClass('fa-times');
// //     }
// // });

// // // 2. Ajout d'un gestionnaire pour l'icône de suppression
// // // $(document).on('click', '.product-search i.fa-times', function() {
// // //     const searchInput = $('#search-formations');

// // //     // Vider le champ de recherche
// // //     searchInput.val('');

// // //     // Remettre l'icône de recherche
// // //     $(this).removeClass('fa-times').addClass('fa-search');

// // //     // Recharger les formations immédiatement
// // //     loadFilteredFormations();
// // // });

// // // 3. Modification de la fonction updateFormationsDisplay
// // // function updateFormationsDisplay(data) {
// // //     formationsContainer.empty();

// // //     if (!data.formations || data.formations.length === 0) {
// // //         formationsContainer.html(`
// // //             <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
// // //                 <div class="alert alert-info text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
// // //                     <i class="fa fa-info-circle fa-2x mb-3"></i>
// // //                     <h5>Aucune formation disponible</h5>
// // //                     <p class="mb-0">Essayez de modifier vos critères de recherche ou de filtrage.</p>
// // //                 </div>
// // //             </div>
// // //         `);

// // //         // Si nous avons une icône de recherche, la mettre à jour
// // //         const searchIcon = $('.product-search i');
// // //         if (searchIcon.length > 0 && $('#search-formations').val().trim() !== '') {
// // //             searchIcon.removeClass('fa-search').addClass('fa-times');
// // //         }

// // //         return;
// // //     }

// // //     // Mettre à jour l'icône de recherche si nécessaire
// // //     const searchIcon = $('.product-search i');
// // //     if (searchIcon.length > 0) {
// // //         if ($('#search-formations').val().trim() !== '') {
// // //             searchIcon.removeClass('fa-search').addClass('fa-times');
// // //         } else {
// // //             searchIcon.removeClass('fa-times').addClass('fa-search');
// // //         }
// // //     }

// // //     // Mettre à jour le titre si nécessaire
// // //     if (data.title) {
// // //         $('.breadcrumb_title h3').text('Formations: ' + data.title);
// // //     }

// // //     // Nombre de formations par ligne
// // //     const coursesPerRow = 3;

// // //     // Ajouter chaque formation en utilisant la fonction du fichier formations-details.js
// // //     data.formations.forEach((formation, index) => {
// // //         const formationHtml = createFormationCard(formation);
// // //         formationsContainer.append(formationHtml);

// // //         // Ajouter un espace après chaque ligne complète (tous les 3 éléments)
// // //         // L'opération modulo (%) retourne 0 lorsque index+1 est un multiple de coursesPerRow
// // //         if ((index + 1) % coursesPerRow === 0 && index < data.formations.length - 1) {
// // //             formationsContainer.append('<div class="w-100 mb-4"></div>'); // Ajoute un espacement vertical entre les lignes
// // //         }
// // //     });

// // //     // Réinitialiser les tooltips et autres plugins
// // //     if (typeof feather !== 'undefined') {
// // //         feather.replace();
// // //     }

// // //     if ($.fn.tooltip) {
// // //         $('[data-toggle="tooltip"]').tooltip();
// // //     }
// // // }

// // // 4. Modification de la fonction loadFilteredFormations pour gérer correctement le changement d'icône
// // function loadFilteredFormations() {
// //     const categoryId = $('input[name="category_filter"]:checked').val();
// //     const status = statusFilter.val();
// //     const searchTerm = searchInput.val();

// //     console.log("Filtrage:", { categoryId, status, searchTerm });

// //     // Mise à jour de l'icône de recherche selon le contenu
// //     const searchIcon = $('.product-search i');
// //     if (searchTerm && searchTerm.trim() !== '') {
// //         searchIcon.removeClass('fa-search').addClass('fa-times');
// //     } else {
// //         searchIcon.removeClass('fa-times').addClass('fa-search');
// //     }

// //     // Mise à jour de l'URL (sans le paramètre de recherche)
// //     const url = new URL(window.location);

// //     if (categoryId) url.searchParams.set('category_id', categoryId);
// //     else url.searchParams.delete('category_id');

// //     // Ne pas ajouter le paramètre status à l'URL si on est étudiant
// //     const userIsAdmin = $('body').data('user-is-admin');

// //     if (status !== '' && userIsAdmin) url.searchParams.set('status', status);
// //     else url.searchParams.delete('status');

// //     // Supprimer le terme de recherche de l'URL pour qu'il n'apparaisse pas
// //     url.searchParams.delete('search');

// //     window.history.pushState({}, '', url);

// //     // Préparation des paramètres AJAX
// //     const ajaxParams = {};

// //     if (categoryId) ajaxParams.category_id = categoryId;

// //     // Toujours envoyer le status dans la requête AJAX, même s'il n'est pas affiché dans l'URL
// //     if (status !== '') ajaxParams.status = status;

// //     // Toujours inclure le terme de recherche dans la requête AJAX, mais pas dans l'URL
// //     if (searchTerm) ajaxParams.search = searchTerm;

// //     // Requête AJAX
// //     $.ajax({
// //         url: window.location.pathname,
// //         type: 'GET',
// //         data: ajaxParams,
// //         dataType: 'json',
// //         beforeSend: function() {
// //             formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
// //         },
// //         success: function(response) {
// //             console.log("Réponse:", response);
// //             updateFormationsDisplay(response);
// //         },
// //         error: function(xhr, status, error) {
// //             console.error("Erreur:", error);
// //             formationsContainer.html(`
// //                 <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
// //                     <div class="alert alert-danger text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
// //                         <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
// //                         <h5>Erreur</h5>
// //                         <p class="mb-0">Une erreur s'est produite lors du chargement des formations.</p>
// //                     </div>
// //                 </div>
// //             `);
// //         }
// //     });
// // }

// //     // Lorsque l'utilisateur rafraîchit la page, réinitialiser les filtres
// //     $(window).on('beforeunload', function() {
// //         // Supprimer les paramètres de l'URL
// //         const url = new URL(window.location);
// //         url.searchParams.delete('category_id');
// //         url.searchParams.delete('status');
// //         url.searchParams.delete('search');
// //         window.history.replaceState({}, '', url);
// //     });

// //     // Assurer que les filtres par défaut soient appliqués au chargement initial
// //     applyUrlFilters();
// //     initStatusFilter();


// //     // Chargement initial pour afficher toutes les formations
// //     loadFilteredFormations();
// // });


// $(document).ready(function() {
//     // Sélecteurs
//     const formationsContainer = $('.formations-container');
//     const searchInput = $('#search-formations');
//     const categoryFilter = $('input[name="category_filter"]');
//     const statusFilter = $('.status-filter');

//     // Vérifier si l'utilisateur est admin en récupérant l'info du backend
//     const userIsAdmin = $('body').data('user-is-admin');
//     const userIsProf = $('body').data('user-is-prof');

//     console.log("L'utilisateur est admin:", userIsAdmin);
//     console.log("L'utilisateur est professeur:", userIsProf);

//     // Fonction pour afficher une notification
//     function showNotification(type, message) {
//         const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
//         const notification = $(`
//             <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
//                 ${message}
//                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//             </div>
//         `);

//         // Ajouter la notification à la page
//         $('.notifications-container').html(notification);

//         // Supprimer automatiquement après 3 secondes
//         setTimeout(function() {
//             notification.alert('close');
//         }, 3000);
//     }

//     // Fonction pour débouncer les événements
//     function debounce(func, wait) {
//         let timeout;
//         return function() {
//             const context = this, args = arguments;
//             clearTimeout(timeout);
//             timeout = setTimeout(function() {
//                 func.apply(context, args);
//             }, wait);
//         };
//     }

//     // Gestion du bouton de recherche/suppression
//     $('.product-search i').on('click', function() {
//         const searchInput = $('#search-formations');

//         if ($(this).hasClass('fa-times')) {
//             // Si c'est l'icône de suppression, on vide le champ
//             searchInput.val('');
//             $(this).removeClass('fa-times').addClass('fa-search');
//             // Recharger immédiatement les formations
//             loadFilteredFormations();
//         } else if (searchInput.val().trim() !== '') {
//             // Si c'est l'icône de recherche et que le champ n'est pas vide, lancer la recherche
//             loadFilteredFormations();
//         }
//     });

//     // Filtrage par catégorie et statut avec AJAX
//     categoryFilter.on('change', loadFilteredFormations);
//     statusFilter.on('change', loadFilteredFormations);

//     // Recherche par terme avec délai (debounce)
//     searchInput.on('keyup', function(e) {
//         // Si touche Escape pressée, vider le champ
//         if (e.key === 'Escape') {
//             $(this).val('');
//             $('.product-search i').removeClass('fa-times').addClass('fa-search');
//             loadFilteredFormations();
//             return;
//         }

//         // Si la touche Entrée est pressée, exécuter immédiatement
//         if (e.key === 'Enter') {
//             loadFilteredFormations();
//             return;
//         }

//         // Sinon, utiliser le debounce
//         debounce(loadFilteredFormations, 500)();
//     });

//     // Fonction principale pour charger les formations filtrées
//     function loadFilteredFormations() {
//         const categoryId = $('input[name="category_filter"]:checked').val();
//         const status = statusFilter.val();
//         const searchTerm = searchInput.val().trim();

//         console.log("Filtrage:", { categoryId, status, searchTerm });

//         // Mise à jour de l'icône de recherche selon le contenu
//         const searchIcon = $('.product-search i');
//         if (searchTerm !== '') {
//             searchIcon.removeClass('fa-search').addClass('fa-times');
//         } else {
//             searchIcon.removeClass('fa-times').addClass('fa-search');
//         }

//         // Mise à jour de l'URL
//         const url = new URL(window.location);

//         if (categoryId) url.searchParams.set('category_id', categoryId);
//         else url.searchParams.delete('category_id');

//         // Ne pas ajouter le paramètre status à l'URL si on est étudiant
//         if (status !== '' && (userIsAdmin || userIsProf)) {
//             url.searchParams.set('status', status);
//         } else {
//             url.searchParams.delete('status');
//         }

//         // Gestion du terme de recherche dans l'URL
//         if (searchTerm) {
//             url.searchParams.set('search', searchTerm);
//         } else {
//             url.searchParams.delete('search');
//         }

//         // Mettre à jour l'URL sans recharger la page
//         window.history.pushState({}, '', url);

//         // Préparation des paramètres AJAX
//         const ajaxParams = {
//             format: 'json' // Forcer le format JSON pour la réponse
//         };

//         if (categoryId) ajaxParams.category_id = categoryId;
//         if (status !== '') ajaxParams.status = status;
//         if (searchTerm) ajaxParams.search = searchTerm;

//         // Afficher un indicateur de chargement
//         formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');

//         // Requête AJAX
//         $.ajax({
//             url: window.location.pathname,
//             type: 'GET',
//             data: ajaxParams,
//             dataType: 'json',
//             success: function(response) {
//                 console.log("Réponse:", response);
//                 updateFormationsDisplay(response);
//             },
//             error: function(xhr, status, error) {
//                 console.error("Erreur:", error);
//                 formationsContainer.html(`
//                     <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
//                         <div class="alert alert-danger text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
//                             <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
//                             <h5>Erreur</h5>
//                             <p class="mb-0">Une erreur s'est produite lors du chargement des formations.</p>
//                         </div>
//                     </div>
//                 `);
//             }
//         });
//     }

//     // Fonction pour mettre à jour l'affichage des formations
//     function updateFormationsDisplay(data) {
//         formationsContainer.empty();

//         // Mettre à jour l'icône de recherche si nécessaire
//         const searchIcon = $('.product-search i');
//         if (searchInput.val().trim() !== '') {
//             searchIcon.removeClass('fa-search').addClass('fa-times');
//         } else {
//             searchIcon.removeClass('fa-times').addClass('fa-search');
//         }

//         // Vérifier si aucune formation n'est disponible
//         if (!data.formations || data.formations.length === 0) {
//             formationsContainer.html(`
//                 <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
//                     <div class="alert alert-info text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
//                         <i class="fa fa-info-circle fa-2x mb-3"></i>
//                         <h5>Aucune formation disponible</h5>
//                         <p class="mb-0">Essayez de modifier vos critères de recherche ou de filtrage.</p>
//                     </div>
//                 </div>
//             `);

//             // Si un terme de recherche est actif, afficher un message spécifique
//             if (data.searchPerformed) {
//                 formationsContainer.html(`
//                     <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
//                         <div class="alert alert-info text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
//                             <i class="fa fa-search fa-2x mb-3"></i>
//                             <h5>Aucun résultat pour "${data.searchTerm}"</h5>
//                             <p class="mb-0">Aucune formation ne correspond à votre recherche. Essayez d'autres termes ou modifiez vos filtres.</p>
//                         </div>
//                     </div>
//                 `);
//             }

//             return;
//         }

//         // Mettre à jour le titre si nécessaire
//         if (data.title) {
//             $('.breadcrumb_title h3').text('Formations: ' + data.title);

//             // Si une recherche est active, ajouter le terme au titre
//             if (data.searchPerformed && data.searchTerm) {
//                 $('.breadcrumb_title h3').text('Recherche: ' + data.searchTerm);
//             }
//         }

//         // Nombre de formations par ligne
//         const coursesPerRow = 3;

//         // Ajouter chaque formation en utilisant la fonction du fichier formations-details.js
//         data.formations.forEach((formation, index) => {
//             const formationHtml = createFormationCard(formation);
//             formationsContainer.append(formationHtml);

//             // Ajouter un espace après chaque ligne complète (tous les 3 éléments)
//             if ((index + 1) % coursesPerRow === 0 && index < data.formations.length - 1) {
//                 formationsContainer.append('<div class="w-100 mb-4"></div>');
//             }
//         });

//         // Réinitialiser les tooltips et autres plugins
//         if (typeof feather !== 'undefined') {
//             feather.replace();
//         }

//         if ($.fn.tooltip) {
//             $('[data-toggle="tooltip"]').tooltip();
//         }
//     }

//     // Handler pour la suppression de formation
//     $(document).on('click', '.delete-formation', function() {
//         const formationId = $(this).data('id');

//         console.log("ID de formation à supprimer:", formationId);

//         if (!formationId) {
//             console.error("Erreur: data-id manquant sur le bouton de suppression");
//             return;
//         }

//         window.formationIdToDelete = formationId;
//         $('#deleteConfirmationModal').modal('show');
//     });

//     // Intercepter la soumission du formulaire de suppression
//     $('#deleteFormationForm').on('submit', function(e) {
//         e.preventDefault();

//         const formationId = window.formationIdToDelete;
//         const token = $('input[name="_token"]', this).val();

//         console.log("Tentative de suppression de la formation avec ID:", formationId);

//         if (!formationId) {
//             console.error("ID de formation non disponible pour la suppression");
//             return;
//         }

//         const url = `/formation/${formationId}`;

//         $('#deleteConfirmationModal').modal('hide');

//         $.ajax({
//             url: url,
//             type: 'DELETE',
//             data: {
//                 _token: token
//             },
//             beforeSend: function(xhr) {
//                 xhr.setRequestHeader('X-CSRF-TOKEN', token);
//             },
//             success: function(response) {
//                 console.log("Suppression réussie:", response);
//                 showNotification('success', 'Formation supprimée avec succès');
//                 loadFilteredFormations();
//             },
//             error: function(xhr, status, error) {
//                 console.error("Détails de l'erreur:", xhr.status, xhr.responseText);
//                 showNotification('error', 'Erreur lors de la suppression de la formation');
//             }
//         });
//     });

//     // Fonction pour appliquer les filtres depuis l'URL
//     function applyUrlFilters() {
//         const urlParams = new URLSearchParams(window.location.search);

//         // Catégorie
//         if (urlParams.has('category_id')) {
//             const categoryId = urlParams.get('category_id');
//             $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
//         } else {
//             $('#category-all').prop('checked', true);
//         }

//         // Status
//         if (urlParams.has('status')) {
//             const status = urlParams.get('status');
//             statusFilter.val(status);
//         } else {
//             if (!(userIsAdmin || userIsProf)) {
//                 statusFilter.val('1'); // Pour les étudiants, filtrer sur les formations publiées
//             } else {
//                 statusFilter.val(''); // Pour admin et prof, pas de filtre par défaut
//             }
//         }

//         // Recherche
//         if (urlParams.has('search')) {
//             searchInput.val(urlParams.get('search'));
//             // Mettre à jour l'icône
//             if (searchInput.val().trim() !== '') {
//                 $('.product-search i').removeClass('fa-search').addClass('fa-times');
//             }
//         }

//         // Si le select utilise select2, mettre à jour l'interface
//         if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
//             statusFilter.trigger('change.select2');
//         }
//     }

//     // Initialisation des plugins
//     if ($.fn.select2) {
//         $('.select2').select2();
//     }

//     // Appliquer les filtres depuis l'URL
//     applyUrlFilters();

//     // Chargement initial des formations
//     loadFilteredFormations();
// });
$(document).ready(function() {
    // Sélecteurs
    const formationsContainer = $('.formations-container');
    const searchInput = $('#search-formations');
    const categoryFilter = $('input[name="category_filter"]');
    const statusFilter = $('.status-filter');

    // Vérifier si l'utilisateur est admin en récupérant l'info du backend
    const userIsAdmin = $('body').data('user-is-admin');
    const userIsProf = $('body').data('user-is-prof');

    console.log("L'utilisateur est admin:", userIsAdmin);
    console.log("L'utilisateur est professeur:", userIsProf);

    // Fonction pour afficher une notification
    function showNotification(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const notification = $(`
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `);

        // Ajouter la notification à la page
        $('.notifications-container').html(notification);

        // Supprimer automatiquement après 3 secondes
        setTimeout(function() {
            notification.alert('close');
        }, 3000);
    }

    // Fonction pour débouncer les événements
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    }

    // Gestion du bouton de recherche/suppression
    $('.product-search i').on('click', function() {
        const searchInput = $('#search-formations');

        if ($(this).hasClass('fa-times')) {
            // Si c'est l'icône de suppression, on vide le champ
            searchInput.val('');
            $(this).removeClass('fa-times').addClass('fa-search');
            // Recharger immédiatement les formations
            loadFilteredFormations();
        } else if (searchInput.val().trim() !== '') {
            // Si c'est l'icône de recherche et que le champ n'est pas vide, lancer la recherche
            loadFilteredFormations();
        }
    });

    // Filtrage par catégorie et statut avec AJAX
    categoryFilter.on('change', loadFilteredFormations);
    statusFilter.on('change', loadFilteredFormations);

    // Recherche par terme avec délai (debounce)
    searchInput.on('keyup', function(e) {
        // Si touche Escape pressée, vider le champ
        if (e.key === 'Escape') {
            $(this).val('');
            $('.product-search i').removeClass('fa-times').addClass('fa-search');
            loadFilteredFormations();
            return;
        }

        // Si la touche Entrée est pressée, exécuter immédiatement
        if (e.key === 'Enter') {
            loadFilteredFormations();
            return;
        }

        // Sinon, utiliser le debounce
        debounce(loadFilteredFormations, 500)();
    });

    // Fonction principale pour charger les formations filtrées
    function loadFilteredFormations() {
        const categoryId = $('input[name="category_filter"]:checked').val();
        const status = statusFilter.val();
        const searchTerm = searchInput.val().trim();

        console.log("Filtrage:", { categoryId, status, searchTerm });

        // Mise à jour de l'icône de recherche selon le contenu
        const searchIcon = $('.product-search i');
        if (searchTerm !== '') {
            searchIcon.removeClass('fa-search').addClass('fa-times');
        } else {
            searchIcon.removeClass('fa-times').addClass('fa-search');
        }

        // Mise à jour de l'URL
        const url = new URL(window.location);

        if (categoryId) url.searchParams.set('category_id', categoryId);
        else url.searchParams.delete('category_id');

        // Ne pas ajouter le paramètre status à l'URL si on est étudiant
        if (status !== '' && (userIsAdmin || userIsProf)) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }

        // Ne pas ajouter le terme de recherche dans l'URL
        url.searchParams.delete('search');

        // Mettre à jour l'URL sans recharger la page
        window.history.pushState({}, '', url);

        // Préparation des paramètres AJAX
        const ajaxParams = {
            format: 'json' // Forcer le format JSON pour la réponse
        };

        if (categoryId) ajaxParams.category_id = categoryId;
        if (status !== '') ajaxParams.status = status;
        if (searchTerm) ajaxParams.search = searchTerm;

        // Afficher un indicateur de chargement
        formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');

        // Requête AJAX
        $.ajax({
            url: window.location.pathname,
            type: 'GET',
            data: ajaxParams,
            dataType: 'json',
            success: function(response) {
                console.log("Réponse:", response);
                updateFormationsDisplay(response);
            },
            error: function(xhr, status, error) {
                console.error("Erreur:", error);
                formationsContainer.html(`
                    <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        <div class="alert alert-danger text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <i class="fa fa-exclamation-triangle fa-2x mb-3"></i>
                            <h5>Erreur</h5>
                            <p class="mb-0">Une erreur s'est produite lors du chargement des formations.</p>
                        </div>
                    </div>
                `);
            }
        });
    }

    // Fonction pour mettre à jour l'affichage des formations
    function updateFormationsDisplay(data) {
        formationsContainer.empty();

        // Mettre à jour l'icône de recherche si nécessaire
        const searchIcon = $('.product-search i');
        if (searchInput.val().trim() !== '') {
            searchIcon.removeClass('fa-search').addClass('fa-times');
        } else {
            searchIcon.removeClass('fa-times').addClass('fa-search');
        }

        // Vérifier si aucune formation n'est disponible
        if (!data.formations || data.formations.length === 0) {
            formationsContainer.html(`
                <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="alert alert-info text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fa fa-info-circle fa-2x mb-3"></i>
                        <h5>Aucune formation disponible</h5>
                        <p class="mb-0">Essayez de modifier vos critères de recherche ou de filtrage.</p>
                    </div>
                </div>
            `);

            // Si un terme de recherche est actif, afficher un message spécifique
            if (data.searchPerformed) {
                formationsContainer.html(`
                    <div class="col-12 d-flex justify-content-center align-items-center" style="min-height: 200px;">
                        <div class="alert alert-info text-center p-4" style="width: 80%; max-width: 500px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <i class="fa fa-search fa-2x mb-3"></i>
                            <h5>Aucun résultat pour "${data.searchTerm}"</h5>
                            <p class="mb-0">Aucune formation ne correspond à votre recherche. Essayez d'autres termes ou modifiez vos filtres.</p>
                        </div>
                    </div>
                `);
            }

            return;
        }

        // Mettre à jour le titre si nécessaire
        if (data.title) {
            $('.breadcrumb_title h3').text('Formations: ' + data.title);

            // Si une recherche est active, ajouter le terme au titre
            if (data.searchPerformed && data.searchTerm) {
                $('.breadcrumb_title h3').text('Recherche: ' + data.searchTerm);
            }
        }

        // Nombre de formations par ligne
        const coursesPerRow = 3;

        // Ajouter chaque formation en utilisant la fonction du fichier formations-details.js
        data.formations.forEach((formation, index) => {
            const formationHtml = createFormationCard(formation);
            formationsContainer.append(formationHtml);

            // Ajouter un espace après chaque ligne complète (tous les 3 éléments)
            if ((index + 1) % coursesPerRow === 0 && index < data.formations.length - 1) {
                formationsContainer.append('<div class="w-100 mb-4"></div>');
            }
        });

        // Réinitialiser les tooltips et autres plugins
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        if ($.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    }

    // Handler pour la suppression de formation
    $(document).on('click', '.delete-formation', function() {
        const formationId = $(this).data('id');

        console.log("ID de formation à supprimer:", formationId);

        if (!formationId) {
            console.error("Erreur: data-id manquant sur le bouton de suppression");
            return;
        }

        window.formationIdToDelete = formationId;
        $('#deleteConfirmationModal').modal('show');
    });

    // Intercepter la soumission du formulaire de suppression
    $('#deleteFormationForm').on('submit', function(e) {
        e.preventDefault();

        const formationId = window.formationIdToDelete;
        const token = $('input[name="_token"]', this).val();

        console.log("Tentative de suppression de la formation avec ID:", formationId);

        if (!formationId) {
            console.error("ID de formation non disponible pour la suppression");
            return;
        }

        const url = `/formation/${formationId}`;

        $('#deleteConfirmationModal').modal('hide');

        $.ajax({
            url: url,
            type: 'DELETE',
            data: {
                _token: token
            },
            beforeSend: function(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', token);
            },
            success: function(response) {
                console.log("Suppression réussie:", response);
                showNotification('success', 'Formation supprimée avec succès');
                loadFilteredFormations();
            },
            error: function(xhr, status, error) {
                console.error("Détails de l'erreur:", xhr.status, xhr.responseText);
                showNotification('error', 'Erreur lors de la suppression de la formation');
            }
        });
    });

    // Fonction pour appliquer les filtres depuis l'URL
    function applyUrlFilters() {
        const urlParams = new URLSearchParams(window.location.search);

        // Catégorie
        if (urlParams.has('category_id')) {
            const categoryId = urlParams.get('category_id');
            $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
        } else {
            $('#category-all').prop('checked', true);
        }

        // Status
        if (urlParams.has('status')) {
            const status = urlParams.get('status');
            statusFilter.val(status);
        } else {
            if (!(userIsAdmin || userIsProf)) {
                statusFilter.val('1'); // Pour les étudiants, filtrer sur les formations publiées
            } else {
                statusFilter.val(''); // Pour admin et prof, pas de filtre par défaut
            }
        }

        // Recherche
        if (urlParams.has('search')) {
            searchInput.val(urlParams.get('search'));
            // Mettre à jour l'icône
            if (searchInput.val().trim() !== '') {
                $('.product-search i').removeClass('fa-search').addClass('fa-times');
            }
        }

        // Si le select utilise select2, mettre à jour l'interface
        if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
            statusFilter.trigger('change.select2');
        }
    }

    // Initialisation des plugins
    if ($.fn.select2) {
        $('.select2').select2();
    }

    // Appliquer les filtres depuis l'URL
    applyUrlFilters();

    // Chargement initial des formations
    loadFilteredFormations();
});
