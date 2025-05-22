$(document).ready(function() {
    console.log("Script AJAX pour formation chargé");

    // Définir les URLs directement (plus simple et fiable)
    const lessonContentUrl = '/training/lesson/content';
    const quizContentUrl = '/training/quiz/content';

    // Configuration globale pour AJAX - ajouter automatiquement le CSRF token
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Gestionnaire pour les liens de leçon
    $('.lesson-link').on('click', function(e) {
        e.preventDefault();
        var lessonId = $(this).closest('.lesson-item').data('lesson-id');
        console.log('Chargement de la leçon ID:', lessonId);
        loadLessonContent(lessonId);
    });

    // Gestionnaire pour les liens de quiz
    $('.quiz-link').on('click', function(e) {
        e.preventDefault();
        var quizId = $(this).closest('.lesson-item').data('quiz-id');
        console.log('Chargement du quiz ID:', quizId);
        loadQuizContent(quizId);
    });

    // Fonction pour charger le contenu d'une leçon
    function loadLessonContent(lessonId) {
        // Afficher l'animation de chargement
        $('#blog-container').html(`
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement de la leçon...</p>
            </div>
        `);

        // Requête AJAX
        $.ajax({
            url: lessonContentUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                lesson_id: lessonId
            },
            success: function(response) {
                console.log('Réponse reçue:', response);

                if (response.status === 'success') {
                    // Mise à jour du contenu
                    $('#blog-container').html(response.content);

                    // Mettre à jour l'état actif des éléments
                    $('.lesson-item').removeClass('active');
                    $('.lesson-item[data-lesson-id="' + lessonId + '"]').addClass('active');

                    // Mettre à jour l'URL sans recharger la page
                    var newUrl = updateUrlParameter(window.location.href, 'lesson', lessonId);
                    window.history.pushState({}, '', newUrl);
                } else {
                    // Erreur côté serveur
                    $('#blog-container').html(response.content ||
                        '<div class="alert alert-warning">Impossible de charger le contenu de la leçon</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', error);
                $('#blog-container').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Une erreur s'est produite lors du chargement de la leçon.
                        <button class="btn btn-sm btn-outline-danger ms-2" onclick="location.reload();">Réessayer</button>
                    </div>
                `);
            }
        });
    }

    // Fonction pour charger le contenu d'un quiz
    function loadQuizContent(quizId) {
        // Afficher l'animation de chargement
        $('#blog-container').html(`
            <div class="card-body text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
                <p class="mt-2">Chargement du quiz...</p>
            </div>
        `);

        // Requête AJAX
        $.ajax({
            url: quizContentUrl,
            type: 'POST',
            dataType: 'json',
            data: {
                quiz_id: quizId
            },
            success: function(response) {
                console.log('Réponse reçue:', response);

                if (response.status === 'success') {
                    // Mise à jour du contenu
                    $('#blog-container').html(response.content);

                    // Mettre à jour l'état actif des éléments
                    $('.lesson-item').removeClass('active');
                    $('.lesson-item[data-quiz-id="' + quizId + '"]').addClass('active');

                    // Mettre à jour l'URL sans recharger la page
                    var newUrl = updateUrlParameter(window.location.href, 'quiz', quizId);
                    window.history.pushState({}, '', newUrl);
                } else {
                    // Erreur côté serveur
                    $('#blog-container').html(response.content ||
                        '<div class="alert alert-warning">Impossible de charger le contenu du quiz</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', error);
                $('#blog-container').html(`
                    <div class="alert alert-danger">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Une erreur s'est produite lors du chargement du quiz.
                        <button class="btn btn-sm btn-outline-danger ms-2" onclick="location.reload();">Réessayer</button>
                    </div>
                `);
            }
        });
    }

    // Fonction utilitaire pour mettre à jour les paramètres d'URL
    function updateUrlParameter(url, param, value) {
        var regex = new RegExp('([?&])' + param + '=.*?(&|$)', 'i');
        var separator = url.indexOf('?') !== -1 ? '&' : '?';

        if (url.match(regex)) {
            return url.replace(regex, '$1' + param + '=' + value + '$2');
        } else {
            return url + separator + param + '=' + value;
        }
    }

    // Vérifier si un paramètre d'URL est présent au chargement
    function checkUrlParameters() {
        var urlParams = new URLSearchParams(window.location.search);
        var lessonId = urlParams.get('lesson');
        var quizId = urlParams.get('quiz');

        if (lessonId) {
            loadLessonContent(lessonId);
        } else if (quizId) {
            loadQuizContent(quizId);
        }
    }

    // Vérifier les paramètres d'URL au chargement initial
    checkUrlParameters();

    // Gérer la navigation avec le bouton retour
    window.addEventListener('popstate', function() {
        checkUrlParameters();
    });
});
