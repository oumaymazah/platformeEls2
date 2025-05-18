
$(document).ready(function() {
    // Fonction pour afficher les alertes
    function showAlert(message, type) {
        const alertDiv = $('<div></div>')
            .addClass('alert')
            .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
            .html(message);

        $('#alert-container').empty().append(alertDiv);

        // Faire disparaître l'alerte après 5 secondes
        setTimeout(function() {
            alertDiv.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }

    // Fonction pour afficher des alertes dans le modal
    function showModalAlert(message, type) {
        const alertDiv = $('<div></div>')
            .addClass('alert')
            .addClass(type === 'success' ? 'alert-success' : 'alert-danger')
            .html(message);

        $('#modal-alert-container').empty().append(alertDiv);
        setTimeout(function() {
            alertDiv.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    }




    // Fonction pour charger le contenu d'un onglet
    function loadTabContent(tabName, callback = null) {
        let url = '';
        let title = '';

        switch(tabName) {
            case 'profile':
                url = PROFILE_URLS.profile;

                break;
            case 'account':
                url = PROFILE_URLS.account;

                break;
            case 'certification':
                url = PROFILE_URLS.certification;
                
                break;
            default:
                url = PROFILE_URLS.profile;
                title = 'Modifier le Profil';
        }

        // $('#tab-title').html('<i class="fas fa-pen me-2"></i>' + title);
        $('#content-loader').show();

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#tab-content').html(response);
                $('#content-loader').hide();
                if (callback) callback();

            },
            error: function(xhr) {
                $('#content-loader').hide();
                showAlert('Une erreur est survenue lors du chargement de la page.', 'danger');
            }
        });
    }
    // $(document).on('click', '#certification-tab', function(e) {
    //     e.preventDefault();

    //     // Mise à jour de l'état actif des onglets
    //     $('.list-group-item').removeClass('active');
    //     $(this).addClass('active');

    //     loadTabContent('certification');
    // });
    // Gérer les clics sur les onglets
    $('.list-group-item').on('click', function(e) {
        e.preventDefault();

        // Mise à jour de l'état actif des onglets
        $('.list-group-item').removeClass('active');
        $(this).addClass('active');

        const tabName = $(this).data('tab');
        loadTabContent(tabName);
    });

    // Charger l'onglet "Modifier le Profil" par défaut
    loadTabContent('profile');

    // Gérer les événements délégués pour les liens qui seront chargés dynamiquement
    $(document).on('click', '.account-link', function(e) {
        e.preventDefault();
        const linkType = $(this).data('link');

        let url = '';
        let title = '';

        switch(linkType) {
            case 'email':
                url = PROFILE_URLS.email;
                title = 'Modifier l\'Email';
                break;
            case 'password':
                url = PROFILE_URLS.password;
                title = 'Modifier le Mot de Passe';
                break;
            default:
                return;
        }

        // $('#tab-title').html('<i class="fas fa-pen me-2"></i>' + title);
        $('#content-loader').show();

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                $('#tab-content').html(response);
                $('#content-loader').hide();

            },
            error: function(xhr) {
                $('#content-loader').hide();
                showAlert('Une erreur est survenue lors du chargement de la page.', 'danger');
            }
        });
    });

    // Gestionnaire pour le bouton "Envoyer le code de validation"
    $(document).on('click', '#send-code-btn', function(e) {
        e.preventDefault();
        // Vérifier si le bouton est désactivé
        if ($(this).prop('disabled')) {
            return;
        }



        // Valider l'email côté client
        const emailInput = $('#new-email');
        const email = emailInput.val();



        // Vérifier la disponibilité de l'email côté serveur avant d'afficher le modal
        $('#content-loader').show();

        $.ajax({
            url: PROFILE_URLS.checkEmail,
            type: 'POST',
            data: {
                email: email,
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                $('#content-loader').hide();

                // Si l'email est valide et disponible, afficher le modal de mot de passe
                // Réinitialiser le champ de mot de passe avant d'afficher le modal
                $('#current-password').val('');

                // Effacer les messages d'alerte précédents dans le modal
                $('#modal-alert-container').empty();


                // Afficher le modal de vérification du mot de passe
                $('#passwordModal').modal('show');
            },
            error: function(xhr) {
                $('#content-loader').hide();

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    showAlert(xhr.responseJSON.error, 'danger');
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMsg = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        errorMsg += value + '<br>';
                    });
                    showAlert(errorMsg, 'danger');
                } else {
                    showAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
                }
            }
        });
    });



    // Réinitialiser le champ de mot de passe lorsque le modal se ferme
    $(document).on('hidden.bs.modal', '#passwordModal', function () {
        $('#current-password').val('');
        $('#modal-alert-container').empty();

    });



    // Gestionnaire pour le bouton "Vérifier" du modal
    $(document).on('click', '#verify-password-btn', function() {
        const password = $('#current-password').val();
        const email = $('#new-email').val();

        if (!password) {
            showModalAlert('Veuillez entrer votre mot de passe.', 'danger');
            return;
        }

        // Envoyer la requête de vérification de mot de passe
        $.ajax({
            url: PROFILE_URLS.verifyPassword,
            type: 'POST',
            data: {
                password: password,
                email: email,
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                // Fermer le modal
                $('#passwordModal').modal('hide');

                // Réinitialiser le champ de mot de passe
                $('#current-password').val('');

                // Envoyer le code de vérification
                sendVerificationCode();
            },
            error: function(xhr) {
                if (xhr.status === 403 && xhr.responseJSON.redirect) {
                    // L'utilisateur est bloqué, rediriger vers la page de compte bloqué
                    window.location.href = xhr.responseJSON.redirect;
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    // Afficher l'erreur dans le modal
                    showModalAlert(xhr.responseJSON.error, 'danger');
                } else {
                    // Pour les autres erreurs
                    showModalAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
                }
            }
        });
    });



    // Fonction pour envoyer le code de vérification
    function sendVerificationCode() {
        $('#content-loader').show();

        $.ajax({
            url: PROFILE_URLS.sendCode,
            type: 'POST',
            data: {
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                $('#content-loader').hide();

                if (response.message) {
                    showAlert(response.message, 'success');
                }

                // Charger la page de validation du code
                $('#tab-title').html('<i class="fas fa-check-circle me-2"></i>Validation du Code');

                $.ajax({
                    url: PROFILE_URLS.validateCode,
                    type: 'GET',
                    success: function(response) {
                        $('#tab-content').html(response);

                    },
                    error: function(xhr) {
                        showAlert('Une erreur est survenue lors du chargement de la page de validation.', 'danger');
                    }
                });
            },
            error: function(xhr) {
                $('#content-loader').hide();

                if (xhr.responseJSON && xhr.responseJSON.error) {
                    showAlert(xhr.responseJSON.error, 'danger');
                } else {
                    showAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
                }
            }
        });
    }


    // Gestion des formulaires par AJAX
    $(document).on('submit', '.ajax-form', function(e) {
        e.preventDefault();

        const form = $(this);
        const url = form.attr('action');
        const method = form.attr('method');
        const formData = form.serialize();

        $('#content-loader').show();

        $.ajax({
            url: url,
            type: method,
            data: formData,
            success: function(response) {
                $('#content-loader').hide();

                if (response.message) {
                    showAlert(response.message, 'success');
                }

                // Gestion spécifique pour le formulaire de mot de passe
                if (form.attr('id') === 'edit-password-form' || form.data('reload-tab') === 'account') {
                    loadTabContent('account');
                    return;
                }

                // Gestion normale pour les autres formulaires
                if (form.data('reload-tab')) {
                    loadTabContent(form.data('reload-tab'));
                }
            },
            error: function(xhr) {
                $('#content-loader').hide();

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    let errorMsg = '';
                    $.each(xhr.responseJSON.errors, function(key, value) {
                        // Vérifier si la valeur est un tableau ou une chaîne
                        if (Array.isArray(value)) {
                            // Si c'est un tableau, ajouter chaque message d'erreur
                            value.forEach(function(message) {
                                errorMsg += message + '<br>';
                            });
                        } else {
                            // Si c'est une chaîne, l'ajouter directement
                            errorMsg += value + '<br>';
                        }
                    });
                    showAlert(errorMsg, 'danger');
                } else if (xhr.responseJSON && xhr.responseJSON.error) {
                    showAlert(xhr.responseJSON.error, 'danger');
                } else {
                    showAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
                }
            }
        });
    });

    // Gestion du retour arrière
    $(document).on('click', '.back-btn', function(e) {
        e.preventDefault();
        const backTab = $(this).data('back-tab');
        loadTabContent(backTab);
    });
});
