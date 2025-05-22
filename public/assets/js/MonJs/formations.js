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
    
//     function initStatusFilter() {
//         // Vérifier si un statut est déjà dans l'URL
//         const urlParams = new URLSearchParams(window.location.search);
//         if (!urlParams.has('status')) {
//             // Si aucun statut n'est spécifié dans l'URL, définir sur "Publiée" par défaut
//             statusFilter.val('1');
//         }
        
//         // Si le select utilise select2, mettre à jour l'interface
//         if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
//             statusFilter.trigger('change.select2');
//         }
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
        
//         if (status !== '') url.searchParams.set('status', status);
//         else url.searchParams.delete('status');
        
//         if (searchTerm) url.searchParams.set('search', searchTerm);
//         else url.searchParams.delete('search');
        
//         window.history.pushState({}, '', url);
        
//         // Préparation des paramètres AJAX
//         const ajaxParams = {};
        
//         if (categoryId) ajaxParams.category_id = categoryId;
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
    
//     function applyUrlFilters() {
//         const urlParams = new URLSearchParams(window.location.search);
        
//         // Si l'URL contient des paramètres de filtre, les appliquer
//         if (urlParams.has('category_id')) {
//             const categoryId = urlParams.get('category_id');
//             // Sélectionner la catégorie correspondante
//             $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
//         } else {
//             // Sinon, sélectionner "Tous"
//             $('#category-all').prop('checked', true);
//         }
        
//         if (urlParams.has('status')) {
//             const status = urlParams.get('status');
//             statusFilter.val(status);
//         } else {
//             // Sinon, sélectionner "Publiée" par défaut
//             statusFilter.val('1');
//         }
        
//         // Si le select utilise select2, mettre à jour l'interface
//         if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
//             statusFilter.trigger('change.select2');
//         }
        
//         if (urlParams.has('search')) {
//             searchInput.val(urlParams.get('search'));
//         }
//     }
    
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














// Filtrage dynamique des formations :

// Par catégorie (via des boutons radio)
// Par statut (via un menu déroulant)
// Par terme de recherche (via un champ de texte)


// Suppression de formations :

// Bouton de suppression qui déclenche un modal de confirmation
// Requête AJAX pour supprimer la formation côté serveur
// Notification de succès ou d'échec


// Gestion de l'affichage :

// Mise à jour dynamique des formations affichées sans rechargement de page
// Gestion d'état de chargement avec spinner
// Message lorsqu'aucune formation n'est disponible


// Gestion de l'URL :

// Mise à jour des paramètres d'URL pour refléter les filtres sélectionnés
// Restauration des filtres à partir de l'URL lors du chargement de la page




$(document).ready(function() {
    // Sélecteurs
    // Vérifier si l'utilisateur est admin en récupérant l'info du backend
    // Cette information doit être ajoutée par le controller dans la vue
    const userIsAdmin = $('body').data('user-is-admin');
    console.log("L'utilisateur est admin:", userIsAdmin);
    const formationsContainer = $('.formations-container');
    const searchInput = $('#search-formations');
    const categoryFilter = $('input[name="category_filter"]');
    const statusFilter = $('.status-filter');
 // Handler pour la suppression de formation
$(document).on('click', '.delete-formation', function() {
    // Récupérer l'ID directement depuis l'élément
    const formationId = $(this).data('id');
    
    console.log("ID de formation à supprimer:", formationId);
    
    // Vérification que l'ID existe
    if (!formationId) {
        console.error("Erreur: data-id manquant sur le bouton de suppression");
        return;
    }
    
    // Stocker l'ID dans une variable globale temporaire
    window.formationIdToDelete = formationId;
    
    // Afficher le modal de confirmation
    $('#deleteConfirmationModal').modal('show');
});

// Intercepter la soumission du formulaire de suppression
$('#deleteFormationForm').on('submit', function(e) {
    e.preventDefault();
    
    // Récupérer l'ID depuis la variable globale
    const formationId = window.formationIdToDelete;
    const token = $('input[name="_token"]', this).val();
    
    console.log("Tentative de suppression de la formation avec ID:", formationId);
    
    // Vérification finale
    if (!formationId) {
        console.error("ID de formation non disponible pour la suppression");
        return;
    }
    
    // Construire l'URL explicitement
    const url = `/formation/${formationId}`;
    
    console.log("URL de suppression:", url);
    
    // Fermer le modal
    $('#deleteConfirmationModal').modal('hide');
    
    // Effectuer la requête AJAX pour la suppression
    $.ajax({
        url: url,
        type: 'DELETE',
        data: {
            _token: token
        },
        beforeSend: function(xhr) {
            // Ajouter explicitement le token CSRF dans les en-têtes
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
    
    // Filtrage par catégorie et statut avec AJAX
    categoryFilter.on('change', loadFilteredFormations);
    statusFilter.on('change', loadFilteredFormations);
    searchInput.on('keyup', debounce(loadFilteredFormations, 30)); // Ajouter un délai pour éviter trop de requêtes
    
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
    function loadFilteredFormations() {
        const categoryId = $('input[name="category_filter"]:checked').val();
        const status = statusFilter.val();
        const searchTerm = searchInput.val();
        
        console.log("Filtrage:", { categoryId, status, searchTerm });
        
        // Mise à jour de l'URL
        const url = new URL(window.location);
        
        if (categoryId) url.searchParams.set('category_id', categoryId);
        else url.searchParams.delete('category_id');
        
        // Ne pas ajouter le paramètre status à l'URL si on est étudiant
        // On peut déterminer si l'utilisateur est admin via un data attribute
        const userIsAdmin = $('body').data('user-is-admin');
        
        if (status !== '' && userIsAdmin) url.searchParams.set('status', status);
        else url.searchParams.delete('status');
        
        if (searchTerm) url.searchParams.set('search', searchTerm);
        else url.searchParams.delete('search');
        
        window.history.pushState({}, '', url);
        
        // Préparation des paramètres AJAX
        const ajaxParams = {};
        
        if (categoryId) ajaxParams.category_id = categoryId;
        
        // Toujours envoyer le status dans la requête AJAX, même s'il n'est pas affiché dans l'URL
        if (status !== '') ajaxParams.status = status;
        
        if (searchTerm) ajaxParams.search = searchTerm;
        
        // Requête AJAX
        $.ajax({
            url: window.location.pathname,
            type: 'GET',
            data: ajaxParams,
            dataType: 'json',
            beforeSend: function() {
                formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
            },
            success: function(response) {
                console.log("Réponse:", response);
                updateFormationsDisplay(response);
            },
            error: function(xhr, status, error) {
                console.error("Erreur:", error);
                formationsContainer.html(`<div class="col-12"><div class="alert alert-danger">Une erreur s'est produite.</div></div>`);
            }
        });
    }
    
    function applyUrlFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    const userIsAdmin = $('body').data('user-is-admin');
    const userIsProf = $('body').data('user-is-prof');
    
    // Si l'URL contient des paramètres de filtre, les appliquer
    if (urlParams.has('category_id')) {
        const categoryId = urlParams.get('category_id');
        // Sélectionner la catégorie correspondante
        $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
    } else {
        // Sinon, sélectionner "Tous"
        $('#category-all').prop('checked', true);
    }
    
    // Pour le status, vérifier d'abord l'URL
    if (urlParams.has('status')) {
        const status = urlParams.get('status');
        statusFilter.val(status);
    } else {
        // Si rien dans l'URL, appliquer la valeur par défaut selon le rôle
        if (userIsAdmin || userIsProf) {
            // Pour admin et prof, ne pas appliquer de filtre par défaut (tous)
            statusFilter.val('');
        } else {
            // Pour les étudiants, filtrer sur les formations publiées
            statusFilter.val('1');
        }
    }
    
    // Si le select utilise select2, mettre à jour l'interface
    if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
        statusFilter.trigger('change.select2');
    }
    
    if (urlParams.has('search')) {
        searchInput.val(urlParams.get('search'));
    }
}
    
    // Modifiez la fonction initStatusFilter pour qu'elle ne fasse rien si l'initialisation
    // a déjà été prise en charge par applyUrlFilters
    function initStatusFilter() {
        // Cette fonction est maintenant gérée par applyUrlFilters
        // Nous la laissons vide pour compatibilité avec le code existant
    }
    // function initStatusFilter() {
    //     // Vérifier si un statut est déjà dans l'URL
    //     const urlParams = new URLSearchParams(window.location.search);
    //     if (!urlParams.has('status')) {
    //         // Si aucun statut n'est spécifié dans l'URL, définir sur "Publiée" par défaut
    //         statusFilter.val('1');
    //     }
        
    //     // Si le select utilise select2, mettre à jour l'interface
    //     if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
    //         statusFilter.trigger('change.select2');
    //     }
    // }
    
    // function loadFilteredFormations() {
    //     const categoryId = $('input[name="category_filter"]:checked').val();
    //     const status = statusFilter.val();
    //     const searchTerm = searchInput.val();
        
    //     console.log("Filtrage:", { categoryId, status, searchTerm });
        
    //     // Mise à jour de l'URL
    //     const url = new URL(window.location);
        
    //     if (categoryId) url.searchParams.set('category_id', categoryId);
    //     else url.searchParams.delete('category_id');
        
    //     if (status !== '') url.searchParams.set('status', status);
    //     else url.searchParams.delete('status');
        
    //     if (searchTerm) url.searchParams.set('search', searchTerm);
    //     else url.searchParams.delete('search');
        
    //     window.history.pushState({}, '', url);
        
    //     // Préparation des paramètres AJAX
    //     const ajaxParams = {};
        
    //     if (categoryId) ajaxParams.category_id = categoryId;
    //     if (status !== '') ajaxParams.status = status;
    //     if (searchTerm) ajaxParams.search = searchTerm;
        
    //     // Requête AJAX
    //     $.ajax({
    //         url: window.location.pathname,
    //         type: 'GET',
    //         data: ajaxParams,
    //         dataType: 'json',
    //         beforeSend: function() {
    //             formationsContainer.html('<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div></div>');
    //         },
    //         success: function(response) {
    //             console.log("Réponse:", response);
    //             updateFormationsDisplay(response);
    //         },
    //         error: function(xhr, status, error) {
    //             console.error("Erreur:", error);
    //             formationsContainer.html(`<div class="col-12"><div class="alert alert-danger">Une erreur s'est produite.</div></div>`);
    //         }
    //     });
    // }
    function updateFormationsDisplay(data) {
        formationsContainer.empty();
        
        if (!data.formations || data.formations.length === 0) {
            formationsContainer.html(`
                <div class="col-12">
                    <div class="alert alert-info">
                        Aucune formation disponible.
                    </div>
                </div>
            `);
            return;
        }
        
        // Mettre à jour le titre si nécessaire
        if (data.title) {
            $('.breadcrumb_title h3').text('Formations: ' + data.title);
        }
        
        // Nombre de formations par ligne
        const coursesPerRow = 3;
        
        // Ajouter chaque formation en utilisant la fonction du fichier formations-details.js
        data.formations.forEach((formation, index) => {
            const formationHtml = createFormationCard(formation);
            formationsContainer.append(formationHtml);
            
            // Ajouter un espace après chaque ligne complète (tous les 3 éléments)
            // L'opération modulo (%) retourne 0 lorsque index+1 est un multiple de coursesPerRow
            if ((index + 1) % coursesPerRow === 0 && index < data.formations.length - 1) {
                formationsContainer.append('<div class="w-100 mb-4"></div>'); // Ajoute un espacement vertical entre les lignes
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
    
    // Initialisation des plugins
    if ($.fn.select2) {
        $('.select2').select2();
    }
    
    // function applyUrlFilters() {
    //     const urlParams = new URLSearchParams(window.location.search);
        
    //     // Si l'URL contient des paramètres de filtre, les appliquer
    //     if (urlParams.has('category_id')) {
    //         const categoryId = urlParams.get('category_id');
    //         // Sélectionner la catégorie correspondante
    //         $(`input[name="category_filter"][value="${categoryId}"]`).prop('checked', true);
    //     } else {
    //         // Sinon, sélectionner "Tous"
    //         $('#category-all').prop('checked', true);
    //     }
        
    //     if (urlParams.has('status')) {
    //         const status = urlParams.get('status');
    //         statusFilter.val(status);
    //     } else {
    //         // Sinon, sélectionner "Publiée" par défaut
    //         statusFilter.val('1');
    //     }
        
    //     // Si le select utilise select2, mettre à jour l'interface
    //     if ($.fn.select2 && statusFilter.hasClass('select2-hidden-accessible')) {
    //         statusFilter.trigger('change.select2');
    //     }
        
    //     if (urlParams.has('search')) {
    //         searchInput.val(urlParams.get('search'));
    //     }
    // }
    
    // Lorsque l'utilisateur rafraîchit la page, réinitialiser les filtres
    $(window).on('beforeunload', function() {
        // Supprimer les paramètres de l'URL
        const url = new URL(window.location);
        url.searchParams.delete('category_id');
        url.searchParams.delete('status');
        url.searchParams.delete('search');
        window.history.replaceState({}, '', url);
    });
    
    // Assurer que les filtres par défaut soient appliqués au chargement initial
    applyUrlFilters();
    initStatusFilter();
    
    
    // Chargement initial pour afficher toutes les formations
    loadFilteredFormations();
});