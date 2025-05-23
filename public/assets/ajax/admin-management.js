class AdminManager {
    constructor() {
      this.init();
    }

    init() {
      this.bindEvents();
      this.loadUsers(); // Load users by default

    }

    bindEvents() {
      // Navigation
      $(document)
        .on('click', '#load-users', () => this.loadUsers())
        .on('click', '#load-roles', () => this.loadRoles())
        .on('click', '#load-evaluation', () => this.loadEvaluations())
        .on('click', '#loadCreateUserForm', () => this.loadCreateUserForm())

        //zedtou tw
        .on('click', '#load-reservations', () => this.loadReservations()) // Nouveau bouton pour les réservations

        .on('click', '.back-btn', (e) => this.handleBackButton(e));

      // Forms
      $(document)
        .on('submit', '#create-user-form', (e) => this.handleUserForm(e))



      // Actions
      $(document)
        .on('click', '.delete-user', (e) => this.deleteItem(e, 'users'))


        .on('change', '.toggle-status-switch', (e) => this.toggleUserStatus(e))
        .on('click', '.toggle-status-menu', (e) => this.toggleUserStatusMenu(e))
        .on('click', '.view-user-roles', (e) => this.viewUserRoles(e))
        .on('click', '.view-quiz-detail', (e) => this.viewQuizDetail(e))

       // Handler pour révoquer une permission
        .on('change', '#role-filter, #status-filter', () => this.applyFilters())

        //zedtou tw
        .on('change', '.toggle-reservation-status', (e) => this.toggleReservationStatus(e)) // Nouveau pour le statut de réservation
        .on('change', '#reservation-status-filter', () => this.applyReservationFilters())  // Nouveau pour filtrer les réservations


        .on('click', '#reset-filters', () => this.resetFilters())
        .on('change', '#role-filter-permission', () => this.applyFiltersPermission()) // Ajout du bouton de réinitialisation
        .on('click', '#reset-filters-permissions', () => this.resetFiltersPermission())
        // .on('change', '#reservation-status-filter', () => this.applyReservationFilters())
        // .on('keyup', '#reservation-search-input', (e) => {
        //     if (e.key === 'Enter') {
        //         this.applyReservationFilters();
        //     }
        // })
        // .on('click', '#reset-reservation-filters', () => this.resetReservationFilters());
        $(document).on('change', '#reservation-status-filter', () => this.applyReservationFilters());
        $(document).on('keyup', '#reservation-search-input', (e) => {
            if (e.key === 'Enter') {
                this.applyReservationFilters();
            }
        });
        $(document).on('click', '#reset-reservation-filters', () => this.resetReservationFilters());



      // Gestionnaire pour le bouton d'annulation
      $(document).on('click', '.cancel-user-creation', () => {

        $('#exampleModal').modal('hide');
      });

      // NOUVELLE SOLUTION: Gestionnaire global pour la fermeture de modal
      $('#exampleModal').on('hidden.bs.modal', (e) => {
        console.log("Modal fermée, redirection en cours...");

        // Utiliser l'attribut data pour savoir où rediriger
        const returnView = $('#exampleModal').data('return-view');
        console.log("Retour à la vue:", returnView);

        if (returnView === 'users') {
          setTimeout(() => this.loadUsers(), 100);
        } else if (returnView === 'roles') {
          setTimeout(() => this.loadRoles(), 100);
        } else if (returnView === 'permissions') {
          setTimeout(() => this.loadPermissions(), 100);
        }
        else if (returnView === 'reservations') {
          setTimeout(() => this.loadReservations(), 100);
        } else if (returnView === 'evaluations') {
            setTimeout(() => this.loadEvaluations(), 100);
        }else {
          // Fallback: détecter en fonction du titre ou du contenu
          const modalTitle = $('#exampleModalLabel').text().toLowerCase();

          if (modalTitle.includes('utilisateur') || modalTitle.includes('user')) {
            setTimeout(() => this.loadUsers(), 100);
          } else if (modalTitle.includes('reservations') || modalTitle.includes('reservations')) {
            setTimeout(() => this.loadReservations(), 100);
          }else if (modalTitle.includes('evaluation')) {
            setTimeout(() => this.loadEvaluations(), 100);
          }
        }

        // Nettoyer pour éviter des redirections multiples
        $('#exampleModal').removeData('return-view');
      });

      // Ajouter du débogage pour vérifier que les événements sont liés
      console.log('AdminManager: Events bound successfully');
    }

    // Méthode pour gérer le bouton retour
    handleBackButton(e) {
        e.preventDefault();
        const targetTab = $(e.currentTarget).data('back-tab');
        console.log("Handling back button with target:", targetTab);

        if (targetTab === 'users') {
            this.loadUsers();
        } else if (targetTab === 'roles') {
            this.loadRoles();
        } else if (targetTab === 'permissions') {
            this.loadPermissions();

        } else if (targetTab === 'listes de reservations ') {
            this.loadReservations();
        }
        else if (targetTab === 'evaluation') {
            this.loadEvaluations();
        }
  }


    // View loading methods
    loadUsers() {

      console.log("Loading users view");

      $.ajax({
        url: $('#load-users').data('user-url'),
        type: 'GET',
        success: (response) => {
          $('#blog-container').html(response);
          this.initDataTable('#users-table');
          this.initSelect2();
          // Initialiser la validation du formulaire après le chargement
          if (window.initFormValidation) {
            console.log("Initializing form validation after loading users");
            window.initFormValidation();
          }
        },
        error: (xhr) => this.handleError(xhr)
      });
    }

     loadEvaluations() {
        console.log("Loading evaluation view");

        $.ajax({
            url: $('#load-evaluation').data('evaluation-url'),
            type: 'GET',
            success: (response) => {
                $('#blog-container').html(response);
                // Ne pas initialiser DataTable ici car cela interfère avec la pagination
                // this.initDataTable('#evaluations-table');
                this.initSelect2();

                // Important: Initialiser les gestionnaires pour la pagination et le filtrage
                this.initEvaluationHandlers();
            },
            error: (xhr) => this.handleError(xhr)
        });
    }
     // Nouvelle méthode améliorée pour initialiser les gestionnaires d'événements pour les évaluations
    initEvaluationHandlers() {
        console.log("Initializing evaluation handlers");


        $(document).off('click', '.pagination a');
        $(document).off('submit', '.filter-form');
        $(document).off('click', '.reset-filters');



        // Gestion des clics sur les liens de pagination
        $(document).on('click', '.pagination a', (e) => {
            e.preventDefault();
            const url = $(e.currentTarget).attr('href');
            console.log("Pagination link clicked, URL:", url);
            this.loadEvaluationWithUrl(url);
        });

        // Gestion de la soumission du formulaire de filtrage
        $(document).on('submit', '.filter-form', (e) => {
            e.preventDefault();
            const form = $(e.currentTarget);
            const url = form.attr('action') || window.location.href;
            const data = form.serialize();
            console.log("Filter form submitted, URL:", url, "Data:", data);
            this.loadEvaluationWithUrl(url + '?' + data);
        });

        // Gestion du clic sur le bouton réinitialiser des filtres
        $(document).on('click', '.reset-filters', (e) => {
            e.preventDefault();
            const url = $(e.currentTarget).attr('href');
            console.log("Reset filters clicked, URL:", url);
            this.loadEvaluationWithUrl(url);
        });
    }



    // Méthode améliorée pour charger l'évaluation avec URL
    loadEvaluationWithUrl(url) {
        console.log("Loading evaluation with URL:", url);

        // Ajouter un indicateur de chargement
        $('#blog-container').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div></div>');

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                $('#blog-container').html(response);

                this.initSelect2();

                // Réinitialiser les gestionnaires d'événements
                this.initEvaluationHandlers();
            },
            error: (xhr) => {
                // Supprimer l'overlay en cas d'erreur
                $('.loading-overlay').remove();
                this.handleError(xhr);
            }
        });
    }
    loadRoles() {

      $.ajax({
        url: $('#load-roles').data('roles-url'),
        type: 'GET',
        success: (response) => {
          $('#blog-container').html(response);
          this.initDataTable('#roles-table');
          // Initialiser la validation du formulaire après le chargement
          if (window.initFormValidation) {
            console.log("Initializing form validation after loading roles");
            window.initFormValidation();
          }
        },
        error: (xhr) => this.handleError(xhr)
      });
    }
   loadReservations() {
        console.log("Loading reservations view");

        $.ajax({
            url: $('#load-reservations').data('reservations-url'),
            type: 'GET',
            success: (response) => {
                $('#blog-container').html(response);

                // Si vous utilisez la pagination Laravel, ne réinitialisez pas DataTable
                // Sinon, décommentez la ligne suivante
                // this.initDataTable('#reservations-table');

                this.initSelect2();

                // Important : initialiser les gestionnaires d'événements pour la pagination
                this.initReservationHandlers();

                // Initialiser la validation du formulaire après le chargement
                if (window.initFormValidation) {
                    console.log("Initializing form validation after loading reservations");
                    window.initFormValidation();
                }
            },
            error: (xhr) => this.handleError(xhr)
        });
    }

    // Méthode améliorée pour charger les réservations avec URL spécifique
    loadReservationsWithUrl(url) {
        console.log("Loading reservations with URL:", url);

        // Ajouter un indicateur de chargement
        // $('#blog-container').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div></div>');

        $.ajax({
            url: url,
            type: 'GET',
            success: (response) => {
                $('#blog-container').html(response);

                // NE PAS réinitialiser DataTable si la vue contient déjà une pagination
                // Si la vue n'utilise pas la pagination Laravel, décommentez la ligne suivante
                // this.initDataTable('#reservations-table');

                this.initSelect2();

                // Important : réinitialiser les gestionnaires d'événements pour la pagination
                this.initReservationHandlers();
            },
            error: (xhr) => {
                // Supprimer l'overlay en cas d'erreur
                $('.loading-overlay').remove();
                this.handleError(xhr);
            }
        });
    }

    // Méthode dédiée pour initialiser les gestionnaires d'événements pour les réservations
   initReservationHandlers() {
        console.log("Initializing reservation handlers");

        // Important : détacher d'abord les gestionnaires existants pour éviter les doublons
        $(document).off('click', '.pagination a');
        $(document).off('change', '#reservation-status-filter');
        $(document).off('keyup', '#reservation-search-input');
        $(document).off('click', '#reset-reservation-filters');
	 // Réattacher les autres gestionnaires d'événements
  	$(document).on('change', '#reservation-status-filter', () => this.applyReservationFilters());
  	$(document).on('keyup', '#reservation-search-input', (e) => {
    	if (e.key === 'Enter') {
     	 this.applyReservationFilters();
    	}
  });
  	$(document).on('click', '#reset-reservation-filters', () => this.resetReservationFilters());

        // Gestion des clics sur les liens de pagination - CORRECTION ICI
        $(document).on('click', '.pagination a', (e) => {
            e.preventDefault();
            const url = $(e.currentTarget).attr('href');
            console.log("Pagination link clicked, URL:", url);

            // Ajouter un indicateur de chargement
            // $('#blog-container').append('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div></div>');

            // Chargement AJAX avec préservation du contexte
            $.ajax({
                url: url,
                type: 'GET',
                success: (response) => {
                    // Remplacer uniquement le contenu principal
                    $('#blog-container').html(response);

                    // Réattacher les gestionnaires après le chargement
                    this.loadReservationsWithUrl(url);

                    // Défiler vers le haut de la liste
                    $('html, body').animate({
                        scrollTop: $('#blog-container').offset().top - 100
                    }, 200);
                },
                error: (xhr) => {
                    // Supprimer l'overlay en cas d'erreur
                    $('.loading-overlay').remove();
                    this.handleError(xhr);
                }
            });
        });
    }
    applyReservationFilters() {
        const status = $('#reservation-status-filter').val();
        const search = $('#reservation-search-input').val();

        console.log('Applying reservation filters:', { status, search });

        // Construire l'URL avec les paramètres
        let url = $('#load-reservations').data('reservations-url');
        let params = [];

        if (status) params.push(`status=${status}`);
        if (search) params.push(`search=${encodeURIComponent(search)}`);

        if (params.length > 0) {
            url += (url.includes('?') ? '&' : '?') + params.join('&');
        }

        this.loadReservationsWithUrl(url);
    }

    resetReservationFilters() {
        $('#reservation-status-filter').val('');
        $('#reservation-search-input').val('');

        // Utiliser l'URL de base sans paramètres
        const url = $('#load-reservations').data('reservations-url');
        this.loadReservationsWithUrl(url);
    }


    // Form loading methods with data-return-view attribute
    loadCreateUserForm() {
        $('#exampleModal .modal-body').empty();
        if (!$('#exampleModal').hasClass('show')) {
            $('#exampleModal').modal('show');
        }
      $.ajax({
        url: $('#loadCreateUserForm').data('create-url'),
        type: 'GET',
        success: (response) => {
          $('#exampleModal .modal-body').html(response);
          $('#exampleModalLabel').text('Créer un utilisateur');
          // AJOUT: attribut data pour savoir où rediriger après fermeture
          $('#exampleModal').data('return-view', 'users');
          this.initSelect2();
          // Initialiser la validation du formulaire après le chargement
          if (window.initFormValidation) {
            console.log("Initializing form validation for create user form");
            window.initFormValidation();
          }
        },
        error: (xhr) => this.handleError(xhr)
      });
    }










    // Form handlers
    handleUserForm(e) {
        e.preventDefault();
        const form = $(e.target);

        // Initialiser la validation du formulaire avant traitement
        if (window.initFormValidation) {
          console.log("Initializing form validation during user form submission");
          window.initFormValidation();
        }

        // NE PAS fermer le modal immédiatement
        // Soumettre le formulaire en arrière-plan
        this.submitForm(form, () => {

          // Cette callback sera exécutée en cas de succès
          $('#exampleModal').modal('hide');
          setTimeout(() => {
            this.loadUsers();
          }, 300);
        });
    }





    // Désactiver le bouton pendant la soumission
    disableSubmitButton(form, loadingText = 'Traitement en cours...') {
        const submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.data('original-text', submitBtn.html());
        submitBtn.html(`<span class="spinner-border spinner-border-sm me-2" role="status"></span>${loadingText}`);
    }

    // Réactiver le bouton en cas d'erreur
    enableSubmitButton(form) {
        const submitBtn = form.find('button[type="submit"]');
        if (submitBtn.data('original-text')) {
            submitBtn.html(submitBtn.data('original-text'));
        }
        submitBtn.prop('disabled', false);
    }

    submitForm(form, successCallback) {
        // Supprimer les anciennes erreurs
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        // Désactiver le bouton et afficher l'indicateur de chargement
        this.disableSubmitButton(form);

        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serialize(),
            success: (response) => {
                if (response.success) {
                    // En cas de succès, exécuter immédiatement le callback
                    // qui fermera le modal
                    if (typeof successCallback === 'function') {
                        successCallback();
                    }
                    // Afficher le message de succès APRÈS la fermeture du modal
                    setTimeout(() => {
                        this.showAlert('success', response.message);
                    }, 300);
                } else if (response.errors) {
                    this.enableSubmitButton(form);
                    this.displayFormErrors(form, response.errors);
                    // Réinitialiser la validation après affichage des erreurs
                    if (window.initFormValidation) window.initFormValidation();
                }
            },
            error: (xhr) => {
                this.enableSubmitButton(form);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    this.displayFormErrors(form, xhr.responseJSON.errors);
                    // Réinitialiser la validation après affichage des erreurs
                    if (window.initFormValidation) window.initFormValidation();
                } else {
                    this.handleError(xhr);
                    if (xhr.status !== 422) {
                        $('#exampleModal').modal('hide');
                        setTimeout(() => {
                            this.loadUsers();
                        }, 300);
                    }
                }
            }
        });
    }



    deleteItem(e, type) {
        e.preventDefault();
        const url = $(e.currentTarget).data('url');
        const row = $(e.currentTarget).closest('tr');
        const tableId = type === 'users' ? '#users-table' :
                        type === 'roles' ? '#roles-table' : '#permissions-table';

        this.confirmDelete(() => {
          // Ajouter un indicateur de chargement à la ligne
          row.find('td').css('opacity', '0.5');
          row.append('<td class="delete-loading position-absolute w-100 h-100 bg-light bg-opacity-50 d-flex justify-content-center align-items-center" style="left:0; top:0;"><div class="spinner-border spinner-border-sm text-primary" role="status"></div></td>');

          const that = this; // Stocker une référence à this

          $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
              // Stocker le message
              const message = response.success || response.message || 'Élément supprimé avec succès';

              // Supprimer la ligne du tableau avec une animation
              row.fadeOut(300, function() {
                // Manipuler DataTable ou supprimer la ligne
                if ($.fn.DataTable.isDataTable(tableId)) {
                  const dataTable = $(tableId).DataTable();
                  dataTable.row(row).remove().draw(false);
                } else {
                  row.remove();
                }

                // Afficher l'alerte en utilisant une approche directe
                const alertHtml = `
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                  </div>
                `;
                $('#alert-container').html(alertHtml);

                // Supprimer automatiquement l'alerte après 5 secondes
                setTimeout(function() {
                  $('.alert').alert('close');
                }, 5000);
              });
            },
            error: function(xhr) {
              // Supprimer l'indicateur de chargement et restaurer l'opacité
              row.find('.delete-loading').remove();
              row.find('td').css('opacity', '1');
              that.handleError(xhr);
            }
          });
        });
      }
    // Méthode modifiée pour éviter le rechargement complet
    toggleUserStatus(e) {
      const switchElement = $(e.currentTarget);
      const url = switchElement.data('url');
      const isChecked = switchElement.is(':checked');
      const status = isChecked ? 'active' : 'inactive';
      const userId = switchElement.attr('id').replace('status-', '');

      // Petit loader local pour indiquer que l'action est en cours
      const parentTd = switchElement.closest('td');
      const originalContent = parentTd.html();
      parentTd.append('<span class="ms-2 status-loading"><i class="fas fa-spinner fa-spin"></i></span>');

      $.ajax({
        url: url,
        type: 'PATCH',
        data: { status: status },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: (response) => {
          // Retirer le loader local
          parentTd.find('.status-loading').remove();

          // Mettre à jour le statut dans l'interface
          switchElement.prop('checked', response.status === 'active');

          // Afficher le message de succès
          this.showAlert('success', response.message);

          // Mettre à jour d'autres éléments liés au statut si nécessaire
          const statusCell = switchElement.closest('tr').find('td:contains("Statut")');
          if (statusCell.length) {
            statusCell.text(response.status === 'active' ? 'Actif' : 'Inactif');
          }

          // Si vous avez un menu dropdown pour changer le statut, mettez-le à jour aussi
          const dropdownItem = $(`#dropdownMenuButton-${userId}`).closest('.dropdown').find('.toggle-status-menu');
          if (dropdownItem.length) {
            dropdownItem.data('status', response.status);
            dropdownItem.html(`<i class="fas fa-toggle-${response.status === 'active' ? 'on' : 'off'} me-2"></i> ${response.status === 'active' ? 'Désactiver' : 'Activer'}`);
          }
        },
        error: (xhr) => {
          // En cas d'erreur, restaurer l'état initial du switch
          parentTd.find('.status-loading').remove();
          switchElement.prop('checked', !isChecked);
          this.handleError(xhr);
        }
      });
    }

    viewUserRoles(e) {
      e.preventDefault();
      const url = $(e.currentTarget).data('url');
      console.log("Loading user roles view with URL:", url);

      this.showLoader();
      $.ajax({
        url: url,
        type: 'GET',
        success: (response) => {
          $('#blog-container').html(response);
          // Initialiser la validation du formulaire après le chargement
          if (window.initFormValidation) {
            console.log("Initializing form validation after loading user roles");
            window.initFormValidation();
          }
        },
        error: (xhr) => this.handleError(xhr)
      });
    }
     viewQuizDetail(e) {
        e.preventDefault();
        const url = $(e.currentTarget).data('url');
        console.log("Loading user roles view with URL:", url);

        this.showLoader();
        $.ajax({
          url: url,
          type: 'GET',
          success: (response) => {
            $('#blog-container').html(response);
            // Initialiser la validation du formulaire après le chargement
            if (window.initFormValidation) {
              console.log("Initializing form validation after loading user roles");
              window.initFormValidation();
            }
          },
          error: (xhr) => this.handleError(xhr)
        });
      }

    applyFilters() {
      const role = $('#role-filter').val();
      const status = $('#status-filter').val();

      console.log('Applying filters:', { role, status });

    //   this.showLoader();
      $.ajax({
        url: $('#load-users').data('user-url'),
        type: 'GET',
        data: {
          role: role,
          status: status
        },
        success: (response) => {
          $('#blog-container').html(response);
          this.initDataTable('#users-table');
          this.initSelect2();
          // Initialiser la validation du formulaire après le chargement
          if (window.initFormValidation) {
            console.log("Initializing form validation after applying filters");
            window.initFormValidation();
          }

          // Restaurer les valeurs des filtres après le rechargement
          $('#role-filter').val(role);
          $('#status-filter').val(status);
        },
        error: (xhr) => this.handleError(xhr)
      });
    }




    // Méthode pour réinitialiser les filtres
    resetFilters() {
      $('#role-filter').val('');
      $('#status-filter').val('');
      this.loadUsers();
    }



    confirmDelete(callback) {
        Swal.fire({
          title: 'Êtes-vous sûr?',
          text: "Cette action est irréversible!",
          icon: 'warning',
          iconColor: '#f8bb86',      // Couleur de l'icône d'avertissement
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#9e9e9e',  // Couleur grise pour le bouton d'annulation
          confirmButtonText: 'Oui, supprimer!',
          cancelButtonText: 'Non, annuler',
          buttonsStyling: true,
          customClass: {
            confirmButton: 'swal-confirm-button-no-border',
            cancelButton: 'swal-cancel-button-no-border',
            popup: 'swal-custom-popup'
          },
          background: '#ffffff',
          backdrop: 'rgba(0,0,0,0.4)'
        }).then((result) => {
          if (result.isConfirmed) {
            callback();
          }
        });
      }

    showLoader() {
      $('#blog-container').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div></div>');
    }

    hideLoader() {
      // Retirer le loader global si nécessaire
      $('.spinner-border').parent().remove();
    }




    showAlert(type, message) {
        // Configuration des couleurs
        const colors = {
          success: {
            background: '#FFFFFF',
            text: ' #2B6ED4',
            timeline: ' #2B6ED4'
          },
          danger: {
            background: '#FFFFFF',
            text: '#F44336',
            timeline: '#F44336'
          },
          warning: {
            background: '#FFFFFF',
            text: '#FF9800',
            timeline: '#FF9800'
          },
          info: {
            background: '#FFFFFF',
            text: '#2196F3',
            timeline: '#2196F3'
          }
        };

        // Ajustement du type pour l'icône
        const iconType = type === 'danger' ? 'error' : type;

        // Fonction de fermeture séparée
        function closeToast(toastElement) {
          toastElement.style.opacity = '0';
          toastElement.style.transform = 'translateX(20px)';
          setTimeout(() => {
            if (toastElement.parentNode) {
              toastElement.parentNode.removeChild(toastElement);
            }
          }, 300);
        }

        // Créer le conteneur du toast s'il n'existe pas
        let container = document.querySelector('.toast-container');
        if (!container) {
          container = document.createElement('div');
          container.className = 'toast-container';
          container.style.position = 'fixed';
          container.style.zIndex = '99999'; // Z-index très élevé
          container.style.top = '20px';
          container.style.right = '20px';
          container.style.pointerEvents = 'none'; // Le conteneur ne capte pas les événements
          document.body.appendChild(container);
        }

        // Créer le toast
        const toast = document.createElement('div');
        toast.className = `custom-toast toast-${iconType}`;
        toast.style.opacity = '0';
        toast.style.transition = 'all 0.3s ease-in-out';
        toast.style.marginBottom = '10px';
        toast.style.borderRadius = '4px';
        toast.style.padding = '15px 20px';
        toast.style.display = 'flex';
        toast.style.flexDirection = 'column';
        toast.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        toast.style.width = '300px';
        toast.style.boxSizing = 'border-box';
        toast.style.position = 'relative';
        toast.style.overflow = 'hidden';
        toast.style.pointerEvents = 'auto'; // Le toast capte les événements

        // Appliquer les couleurs selon le type
        if (colors[type]) {
          toast.style.backgroundColor = colors[type].background;
          toast.style.color = colors[type].text;
        }

        // Conteneur principal pour le contenu du toast
        const contentContainer = document.createElement('div');
        contentContainer.style.display = 'flex';
        contentContainer.style.alignItems = 'center';
        contentContainer.style.width = '100%';

        // Icône selon le type
        const icon = document.createElement('span');
        icon.style.marginRight = '10px';
        icon.style.fontSize = '18px';
        icon.style.color = colors[type] ? colors[type].text : '';

        // Définir l'icône selon le type
        if (iconType === 'success') icon.innerHTML = '<i class="fa fa-check-circle"></i>';
        else if (iconType === 'error') icon.innerHTML = '<i class="fa fa-times-circle"></i>';
        else if (iconType === 'warning') icon.innerHTML = '<i class="fa fa-exclamation-triangle"></i>';
        else if (iconType === 'info') icon.innerHTML = '<i class="fa fa-info-circle"></i>';

        // Contenu du message
        const content = document.createElement('div');
        content.style.flex = '1';
        content.style.wordBreak = 'break-word';
        content.innerHTML = message;

        // Bouton de fermeture
        const closeBtn = document.createElement('span');
        closeBtn.innerHTML = '×';
        closeBtn.style.marginLeft = '10px';
        closeBtn.style.cursor = 'pointer';
        closeBtn.style.fontSize = '22px';
        closeBtn.style.fontWeight = 'bold';
        closeBtn.style.opacity = '0.7';
        closeBtn.style.color = colors[type] ? colors[type].text : '';
        closeBtn.style.position = 'relative'; // Position relative pour l'isoler
        closeBtn.style.zIndex = '999'; // Z-index plus élevé pour s'assurer qu'il est au-dessus
        closeBtn.style.width = '30px'; // Largeur définie pour une meilleure zone de clic
        closeBtn.style.height = '30px'; // Hauteur définie pour une meilleure zone de clic
        closeBtn.style.textAlign = 'center'; // Centrer le X
        closeBtn.style.lineHeight = '25px'; // Alignement vertical du X

        closeBtn.addEventListener('mouseover', function() {
          this.style.opacity = '1';
        });

        closeBtn.addEventListener('mouseout', function() {
          this.style.opacity = '0.7';
        });

        // Gestionnaire d'événement isolé avec capture d'événements
        const clickHandler = function(event) {
          event.stopPropagation();
          event.preventDefault();
          event.stopImmediatePropagation(); // Arrête également les autres gestionnaires
          closeToast(toast);
          return false; // Empêche la propagation
        };

        // Ajouter plusieurs écouteurs pour s'assurer que l'événement est capturé
        closeBtn.addEventListener('click', clickHandler, true);
        closeBtn.addEventListener('mousedown', clickHandler, true);
        closeBtn.addEventListener('mouseup', clickHandler, true);

        // Assembler les éléments
        contentContainer.appendChild(icon);
        contentContainer.appendChild(content);
        contentContainer.appendChild(closeBtn);
        toast.appendChild(contentContainer);

        // Ajouter la timeline
        const timeline = document.createElement('div');
        timeline.className = 'toast-timeline';
        timeline.style.position = 'absolute';
        timeline.style.bottom = '0';
        timeline.style.left = '0';
        timeline.style.height = '3px';
        timeline.style.width = '100%';
        timeline.style.backgroundColor = 'rgba(0, 0, 0, 0.1)';

        const progress = document.createElement('div');
        progress.style.height = '100%';
        progress.style.width = '0%';
        progress.style.backgroundColor = colors[type] ? colors[type].timeline : '';
        progress.style.transition = 'width 5000ms linear'; // 5000ms = 5 secondes

        timeline.appendChild(progress);
        toast.appendChild(timeline);

        // Ajouter le toast au container
        container.appendChild(toast);

        // Animation d'entrée
        setTimeout(() => {
          toast.style.opacity = '1';
          progress.style.width = '100%';
        }, 50);

        // Fermeture automatique après 5 secondes
        setTimeout(() => {
          closeToast(toast);
        }, 5000);

        return toast;
      }

    handleError(xhr) {
      let message = 'Une erreur est survenue';
      if (xhr.responseJSON && xhr.responseJSON.message) {
        message = xhr.responseJSON.message;
      }
      this.showAlert('danger', message);
      console.error("AJAX Error:", xhr); // Ajout de débogage détaillé
    }

    displayFormErrors(form, errors) {
        // Supprimer les anciennes erreurs
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();

        // Supprimer les anciens écouteurs d'événements pour éviter les doublons
        form.find('input, select, textarea').off('input.formError change.formError');

        // Afficher les erreurs pour chaque champ
        $.each(errors, (field, messages) => {
            const input = form.find(`[name="${field}"]`);
            if (input.length) {
                input.addClass('is-invalid');
                const feedbackDiv = $(`<div class="invalid-feedback">${Array.isArray(messages) ? messages.join('<br>') : messages}</div>`);
                input.after(feedbackDiv);

                // Ajouter des écouteurs d'événements pour les champs avec erreurs
                const eventType = input.is('select') ? 'change.formError' : 'input.formError';
                input.on(eventType, function() {
                    // Vérifier si le champ n'est plus vide
                    if ($(this).val().trim() !== '') {
                        // Supprimer l'état d'erreur
                        $(this).removeClass('is-invalid');
                        feedbackDiv.hide();
                    } else {
                        // Réappliquer l'état d'erreur si le champ est toujours vide
                        $(this).addClass('is-invalid');
                        feedbackDiv.show();
                    }
                });
            } else {
                // Si le champ n'est pas trouvé, ajouter une alerte générale
                this.showAlert('danger', `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`);
            }
        });

        // Faire défiler jusqu'à la première erreur
        const firstError = form.find('.is-invalid').first();
        if (firstError.length) {
            firstError.focus();
        }
    }

    initDataTable(selector) {
      if ($.fn.DataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
      }
      $(selector).DataTable({
        responsive: true,
        language: {
          url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        pageLength: 10
      });
    }

    initSelect2() {
      $('.select2').select2({
        dropdownParent: $('#exampleModal'),
        width: '100%'
      });
       // Ajoutez ce code pour cibler spécifiquement les multi-select
        $('.js-example-basic-multiple').select2({
            dropdownParent: $('#exampleModal'),
            width: '100%',
            multiple: true,
            placeholder: "Sélectionnez des permissions"
        });
    }
}

// Initialize when document is ready
$(document).ready(function() {
    console.log("Document ready - initializing AdminManager");
    // Vérifier si la fonction de validation est disponible globalement
    console.log("Form validation function available:", typeof window.initFormValidation !== 'undefined');
    new AdminManager();
});














