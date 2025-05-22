<!-- FICHIER 1: resources/views/admin/quizzes/attempt.blade.php -->
@extends('layouts.admin.master')
@section('css')
{{-- @vite(['resources/css/MonCss/quizzes.css']) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link href="{{ mix('css/quizzes.css') }}" rel="stylesheet">
@endsection
@section('content')
<div class="container quiz-container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $quiz->title }}</h5>
                    <div id="timer" class="badge badge-light"></div>
                </div>

                <div class="card-body">
                    <div class="alert alert-danger" id="tab-warning" style="display: none;">
                        <strong>Attention!</strong> Vous avez changé d'onglet <span id="tab-count">0</span> fois.
                        Après 2 changements, le quiz sera automatiquement soumis.
                    </div>

                    <form id="quiz-form" action="{{ route('quizzes.answer', $attempt->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">

                        <!-- Numéro de la question courante -->
                        <div class="text-muted mb-2">
                            Question {{ $currentQuestionIndex + 1 }} sur {{ count($questions) }}
                        </div>



                        <!-- Message pour indiquer si c'est une question à choix multiple -->
                        @php
                            $correctAnswersCount = $currentQuestion->answers->where('is_correct', true)->count();
                            $isMultipleChoice = $correctAnswersCount > 1;
                        @endphp

                        @if($isMultipleChoice)
                            <div class="alert alert-info mb-3">
                                <i class="fa fa-info-circle"></i>
                                Cette question peut avoir plusieurs bonnes réponses. Cochez toutes les réponses que vous pensez être correctes.
                            </div>
                        @endif
                        <h4 class="mb-4">{{ $currentQuestion->question_text }}</h4>
                        <div class="list-group mb-4">
                            @foreach($currentQuestion->answers as $answer)
                            <label class="list-group-item d-flex align-items-center">
                                @if($isMultipleChoice)
                                    <input type="checkbox" name="answer_ids[]" value="{{ $answer->id }}" class="mr-2"
                                    {{ in_array($answer->id, $userAnswers[$currentQuestion->id] ?? []) ? 'checked' : '' }}>
                                @else
                                    <input type="radio" name="answer_ids[]" value="{{ $answer->id }}" class="mr-2"
                                    {{ in_array($answer->id, $userAnswers[$currentQuestion->id] ?? []) ? 'checked' : '' }}>
                                @endif
                                {{ $answer->answer_text }}
                            </label>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary" id="prev-btn"
                                {{ $currentQuestionIndex > 0 ? '' : 'disabled' }}>
                                <i class="fa fa-backward"></i> Précédent
                            </button>
                            <button type="submit" class="btn btn-primary" id="next-btn">
                                {{ $currentQuestionIndex < count($questions) - 1 ? 'Suivant' : 'Terminer' }}
                                <i class="fa fa-forward"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Assurez-vous qu'Axios est chargé -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- Script intégré pour le moniteur de quiz -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Configuration
    const attemptId = {{ $attempt->id }};
    const csrfToken = "{{ csrf_token() }}";
    const routes = {
        tabSwitch: "{{ route('quizzes.tab-switch', $attempt->id) }}",
        result: "{{ route('quizzes.result', $attempt->id) }}"
    };
    const config = {
        maxSwitches: 2,
        warningThreshold: 1, // nombre de changements avant d'afficher un avertissement
        switchCount: 0
    };

    // Éléments du DOM
    const elements = {
        warning: document.getElementById('tab-warning'),
        counter: document.getElementById('tab-count'),
        timer: document.getElementById('timer')
    };

    // Fonctions principales
    const handleTabSwitch = async () => {
        config.switchCount++;
        updateWarningDisplay();

        try {
            const response = await axios.post(routes.tabSwitch, {
                _token: csrfToken
            });

            if (response.data.force_submit) {
                handleForceSubmit();
            }
        } catch (error) {
            console.error('Error reporting tab switch:', error);
        }
    };

    // Sert à avertir l'étudiant visuellement qu'il est surveillé
    // après qu'il ait changé d'onglet plus d'une fois
    const updateWarningDisplay = () => {
        if (config.switchCount >= config.warningThreshold) {
            elements.warning.style.display = 'block';
            elements.counter.textContent = config.switchCount;
        }
    };

    const handleForceSubmit = () => {
        alert('Tentative de triche détectée. Le quiz sera soumis automatiquement.');
        window.location.href = routes.result;
    };

    // Sécurité: Bloquer clic droit, copier, couper
    const preventUserActions = (e) => {
        e.preventDefault();
    };

    // Forcer le mode plein écran
    const manageFullscreen = () => {
        const methods = [
            'requestFullscreen',
            'webkitRequestFullscreen',
            'msRequestFullscreen'
        ];

        const elem = document.documentElement;
        methods.some(method => elem[method]?.call(elem));
    };

    // Surveillance des onglets
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) handleTabSwitch();
    });

    // Protection contre les actions utilisateur
    ['contextmenu', 'copy', 'cut'].forEach(event => {
        document.addEventListener(event, preventUserActions);
    });

    // Gestion du plein écran
    manageFullscreen();
    document.addEventListener('fullscreenchange', () => {
        if (!document.fullscreenElement) manageFullscreen();
    });

    // Gestion du timer
    let timeLeft = {{ $timeLeft }};

    const timerInterval = setInterval(() => {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        elements.timer.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            window.location.href = "{{ route('quizzes.finish', $attempt->id) }}";
        }

        timeLeft--;
    }, 1000);

    // Gestion des boutons de navigation
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const form = document.getElementById('quiz-form');

    // Configuration de la navigation
    const currentIndex = {{ $currentQuestionIndex }};
    const totalQuestions = {{ count($questions) }};

    // Bouton précédent
    prevBtn.addEventListener('click', function() {
        if (currentIndex > 0) {
            // Ajouter un champ caché pour indiquer la question suivante
            const nextQuestionInput = document.createElement('input');
            nextQuestionInput.type = 'hidden';
            nextQuestionInput.name = 'next_question';
            nextQuestionInput.value = currentIndex - 1;
            form.appendChild(nextQuestionInput);

            // Soumettre le formulaire
            form.submit();
        }
    });

    // Modification du bouton suivant pour le dernier élément
    if (currentIndex === totalQuestions - 1) {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Ajouter un champ caché pour indiquer de terminer le quiz
            const submitQuizInput = document.createElement('input');
            submitQuizInput.type = 'hidden';
            submitQuizInput.name = 'submit_quiz';
            submitQuizInput.value = '1';
            form.appendChild(submitQuizInput);

            // Soumettre le formulaire
            form.submit();
        });
    } else {
        nextBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Ajouter un champ caché pour indiquer la question suivante
            const nextQuestionInput = document.createElement('input');
            nextQuestionInput.type = 'hidden';
            nextQuestionInput.name = 'next_question';
            nextQuestionInput.value = currentIndex + 1;
            form.appendChild(nextQuestionInput);

            // Soumettre le formulaire
            form.submit();
        });
    }

    // Validation pour les questions à choix multiple n'est plus nécessaire car on permet la navigation sans réponse
    // Mais nous pouvons garder une vérification facultative pour le bouton "Terminer"
    if (currentIndex === totalQuestions - 1) {
        const checkAllAnswered = () => {
            // Vérifier que l'utilisateur a répondu à toutes les questions avant de soumettre
            // Cette fonction est optionnelle, à implémenter si besoin
        };
    }

    // Message de débogage pour confirmer que le script est chargé
    console.log('Quiz monitor initialized successfully');
});
</script>

{{-- <script>
   document.addEventListener('DOMContentLoaded', () => {
    // Configuration
    const attemptId = {{ $attempt->id }};
    const csrfToken = "{{ csrf_token() }}";
    const routes = {
        tabSwitch: "{{ route('quizzes.tab-switch', $attempt->id) }}",
        result: "{{ route('quizzes.result', $attempt->id) }}"
    };
    const config = {
        maxSwitches: 2,
        warningThreshold: 1,
        switchCount: 0
    };

    // Utiliser localStorage au lieu de sessionStorage pour une meilleure persistance
    // même en cas de rechargement forcé ou de fermeture d'onglet
    const storageKey = `quiz_fullscreen_${attemptId}`;
    const questionSubmitKey = `quiz_question_${attemptId}_submit`;
    const fullscreenInitializedKey = `quiz_fullscreen_initialized_${attemptId}`;

    // Marquer l'état du plein écran dans localStorage (plus persistant que sessionStorage)
    const markFullScreenAsInitialized = () => {
        localStorage.setItem(fullscreenInitializedKey, 'true');
        localStorage.setItem(storageKey, 'active');
    };

    // Vérifier si le plein écran a déjà été activé dans cette tentative
    const isFullScreenInitialized = () => {
        return localStorage.getItem(fullscreenInitializedKey) === 'true';
    };

    // Éléments du DOM
    const elements = {
        warning: document.getElementById('tab-warning'),
        counter: document.getElementById('tab-count'),
        timer: document.getElementById('timer')
    };

    // Créer un overlay de blocage pour quand l'utilisateur quitte le plein écran
    const createBlockingOverlay = () => {
        // Vérifier si l'overlay existe déjà
        let overlay = document.getElementById('fullscreen-overlay');
        if (overlay) return overlay;

        overlay = document.createElement('div');
        overlay.id = 'fullscreen-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 1.5rem;
            text-align: center;
            padding: 20px;
        `;

        const message = document.createElement('p');
        message.textContent = 'Le mode plein écran est obligatoire pour ce quiz.';

        const button = document.createElement('button');
        button.textContent = 'Réactiver le plein écran pour continuer';
        button.className = 'btn btn-warning mt-3';
        button.onclick = requestFullScreen;

        overlay.appendChild(message);
        overlay.appendChild(button);
        document.body.appendChild(overlay);

        return overlay;
    };

    // Fonctions principales
    const handleTabSwitch = async () => {
        config.switchCount++;
        updateWarningDisplay();

        try {
            const response = await axios.post(routes.tabSwitch, {
                _token: csrfToken
            });

            if (response.data.force_submit) {
                handleForceSubmit();
            }
        } catch (error) {
            console.error('Error reporting tab switch:', error);
        }
    };

    const updateWarningDisplay = () => {
        if (elements.warning && config.switchCount >= config.warningThreshold) {
            elements.warning.style.display = 'block';
            if (elements.counter) {
                elements.counter.textContent = config.switchCount;
            }
        }
    };

    const handleForceSubmit = () => {
        alert('Tentative de triche détectée. Le quiz sera soumis automatiquement.');
        window.location.href = routes.result;
    };

    // Sécurité: Bloquer clic droit, copier, couper
    const preventUserActions = (e) => {
        e.preventDefault();
    };

    // Gestion améliorée du plein écran - compatibilité multi-navigateurs
    const fullScreenAPI = {
        supportsFullScreen: false,
        isFullScreen: function() { return false; },
        requestFullScreen: function() {},
        cancelFullScreen: function() {},
        fullScreenEventName: '',
        prefix: ''
    };

    // Détecter les différentes implémentations selon les navigateurs
    const browserPrefixes = ['webkit', 'moz', 'ms', 'o'];

    // Vérifier si le navigateur standard est supporté
    if (document.documentElement.requestFullscreen) {
        fullScreenAPI.supportsFullScreen = true;
        fullScreenAPI.prefix = '';
        fullScreenAPI.requestFullScreen = function() { document.documentElement.requestFullscreen(); };
        fullScreenAPI.cancelFullScreen = function() { document.exitFullscreen(); };
        fullScreenAPI.fullScreenEventName = 'fullscreenchange';
        fullScreenAPI.isFullScreen = function() { return document.fullscreenElement !== null; };
    } else {
        // Tester les préfixes spécifiques des navigateurs
        for (let i = 0; i < browserPrefixes.length; i++) {
            const prefix = browserPrefixes[i];
            const requestFullscreenMethod = `${prefix}RequestFullscreen`;
            const exitFullscreenMethod = `${prefix}ExitFullscreen`;
            const fullscreenElementProperty = `${prefix}FullscreenElement`;
            const lowercasePrefix = prefix.toLowerCase();

            // Cas spéciaux pour certains navigateurs
            const capitalizedPrefix = prefix.charAt(0).toUpperCase() + prefix.slice(1);
            const alternateExitMethod = `exit${capitalizedPrefix}Fullscreen`;
            const alternateElementProperty = `${lowercasePrefix}FullScreenElement`;

            // Tester différentes combinaisons selon les navigateurs
            if (document.documentElement[requestFullscreenMethod]) {
                fullScreenAPI.supportsFullScreen = true;
                fullScreenAPI.prefix = lowercasePrefix;
                fullScreenAPI.requestFullScreen = function() { document.documentElement[requestFullscreenMethod](); };

                // Déterminer la bonne méthode pour quitter le plein écran
                if (document[exitFullscreenMethod]) {
                    fullScreenAPI.cancelFullScreen = function() { document[exitFullscreenMethod](); };
                } else if (document[alternateExitMethod]) {
                    fullScreenAPI.cancelFullScreen = function() { document[alternateExitMethod](); };
                }

                // Déterminer le bon événement à surveiller
                fullScreenAPI.fullScreenEventName = `${lowercasePrefix}fullscreenchange`;

                // Déterminer la bonne propriété pour vérifier l'état
                if (document[fullscreenElementProperty] !== undefined) {
                    fullScreenAPI.isFullScreen = function() { return document[fullscreenElementProperty] !== null; };
                } else if (document[alternateElementProperty] !== undefined) {
                    fullScreenAPI.isFullScreen = function() { return document[alternateElementProperty] !== null; };
                }

                break;
            }
        }
    }

    // Vérifier si le plein écran est disponible
    if (!fullScreenAPI.supportsFullScreen) {
        // Si le plein écran n'est pas supporté, créer un message adapté dans l'interface
        console.warn("Le plein écran n'est pas supporté par ce navigateur");

        const quizContainer = document.querySelector('.card-body') || document.body;
        const warningElement = document.createElement('div');
        warningElement.className = 'alert alert-danger';
        warningElement.innerHTML = '<strong>Attention!</strong> Votre navigateur ne prend pas en charge le mode plein écran requis pour ce quiz. ' +
            'Nous vous recommandons d\'utiliser Chrome, Firefox, Safari ou Edge dans leur version récente.';

        quizContainer.insertBefore(warningElement, quizContainer.firstChild);

        // Ajouter un bouton pour essayer quand même
        const tryAnywayBtn = document.createElement('button');
        tryAnywayBtn.textContent = 'Continuer quand même';
        tryAnywayBtn.className = 'btn btn-warning ms-3';
        tryAnywayBtn.onclick = function() {
            warningElement.style.display = 'none';
        };

        warningElement.appendChild(tryAnywayBtn);
    }

    // Fonction pour activer le plein écran avec gestion d'erreurs
    const requestFullScreen = () => {
        try {
            if (!fullScreenAPI.isFullScreen()) {
                fullScreenAPI.requestFullScreen();
                console.log("Demande de plein écran envoyée");
            }

            // Marquer comme initialisé quand on demande le plein écran
            markFullScreenAsInitialized();

            // Supprimer l'overlay s'il existe
            const overlay = document.getElementById('fullscreen-overlay');
            if (overlay) {
                overlay.remove();
            }

        } catch (error) {
            console.error("Erreur lors de la demande de plein écran:", error);

            // Créer un message d'erreur à l'intérieur de l'interface
            const errorMessage = document.createElement('div');
            errorMessage.className = 'alert alert-danger mt-3';
            errorMessage.innerHTML = 'Impossible d\'activer le mode plein écran. Veuillez vérifier les paramètres de votre navigateur ou essayer avec un autre navigateur.';

            // Ajouter le message au début de l'interface
            const quizContainer = document.querySelector('.card-body') || document.body;
            quizContainer.insertBefore(errorMessage, quizContainer.firstChild);

            // Retirer le message après 5 secondes
            setTimeout(() => {
                errorMessage.remove();
            }, 5000);
        }
    };

    // Détecter quand l'utilisateur quitte le plein écran
    const handleFullscreenChange = () => {
        if (fullScreenAPI.isFullScreen()) {
            // Si nous entrons en plein écran, mettre à jour le statut
            markFullScreenAsInitialized();

            // S'assurer que l'overlay est retiré s'il existe
            const overlay = document.getElementById('fullscreen-overlay');
            if (overlay) overlay.remove();

            console.log("Entrée en mode plein écran détectée et enregistrée");
        } else if (isFullScreenInitialized()) {
            // Si nous quittons le plein écran et qu'il était initialisé auparavant
            console.log("Sortie du mode plein écran détectée");

            // Vérifier si c'est une navigation délibérée entre questions
            if (!localStorage.getItem(questionSubmitKey)) {
                console.log("Tentative de réactivation du plein écran...");

                // Ne pas montrer immédiatement l'overlay, essayons d'abord de réactiver automatiquement
                setTimeout(() => {
                    if (!fullScreenAPI.isFullScreen()) {
                        requestFullScreen();

                        // Si toujours pas en plein écran après tentative, afficher l'overlay
                        setTimeout(() => {
                            if (!fullScreenAPI.isFullScreen()) {
                                createBlockingOverlay();
                                handleTabSwitch();
                            }
                        }, 500);
                    }
                }, 300);
            }
        }
    };

    // Configurer les écouteurs pour le changement de plein écran
    const setupFullscreenListeners = () => {
        // Ajouter tous les gestionnaires possibles pour une meilleure compatibilité
        document.addEventListener('fullscreenchange', handleFullscreenChange);
        document.addEventListener('webkitfullscreenchange', handleFullscreenChange);
        document.addEventListener('mozfullscreenchange', handleFullscreenChange);
        document.addEventListener('MSFullscreenChange', handleFullscreenChange);

        if (fullScreenAPI.fullScreenEventName) {
            document.addEventListener(fullScreenAPI.fullScreenEventName, handleFullscreenChange);
        }
    };

    // Solution au problème du rechargement: Utiliser AJAX pour soumettre le formulaire
    // et naviguer sans quitter le plein écran
    const setupAjaxNavigation = () => {
        const form = document.getElementById('quiz-form');
        const prevBtn = document.getElementById('prev-btn');
        const nextBtn = document.getElementById('next-btn');

        if (!form) return;

        // Configuration de la navigation
        const currentIndex = {{ $currentQuestionIndex }};
        const totalQuestions = {{ count($questions) }};

        // Intercepter la soumission du formulaire
        form.addEventListener('submit', function(e) {
            // Marquer que nous sommes en train de soumettre le formulaire
            localStorage.setItem(questionSubmitKey, 'true');
        });

        // Configuration du bouton précédent
        if (prevBtn && currentIndex > 0) {
            prevBtn.addEventListener('click', function(e) {
                // Ajouter un champ caché pour indiquer la question précédente
                const nextQuestionInput = document.createElement('input');
                nextQuestionInput.type = 'hidden';
                nextQuestionInput.name = 'next_question';
                nextQuestionInput.value = currentIndex - 1;
                form.appendChild(nextQuestionInput);

                // Signaler que nous naviguons volontairement
                localStorage.setItem(questionSubmitKey, 'true');

                // Soumettre le formulaire
                form.submit();
            });
        }

        // Configuration du bouton suivant
        if (nextBtn) {
            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();

                if (currentIndex === totalQuestions - 1) {
                    // Dernier élément - soumettre le quiz
                    const submitQuizInput = document.createElement('input');
                    submitQuizInput.type = 'hidden';
                    submitQuizInput.name = 'submit_quiz';
                    submitQuizInput.value = '1';
                    form.appendChild(submitQuizInput);
                } else {
                    // Question suivante
                    const nextQuestionInput = document.createElement('input');
                    nextQuestionInput.type = 'hidden';
                    nextQuestionInput.name = 'next_question';
                    nextQuestionInput.value = currentIndex + 1;
                    form.appendChild(nextQuestionInput);
                }

                // Signaler que nous naviguons volontairement
                localStorage.setItem(questionSubmitKey, 'true');

                // Soumettre le formulaire
                form.submit();
            });
        }
    };

    // Forcer le plein écran à la première interaction utilisateur
    const forceFullScreenOnInteraction = function() {
        if (!fullScreenAPI.isFullScreen()) {
            console.log("Interaction utilisateur détectée - activation du plein écran");
            requestFullScreen();
        }

        // Retirer ces gestionnaires pour éviter les activations multiples
        document.removeEventListener('click', forceFullScreenOnInteraction);
        document.removeEventListener('keydown', forceFullScreenOnInteraction);
        document.removeEventListener('touchstart', forceFullScreenOnInteraction);
    };

    // Gérer l'état initial du plein écran
    const handleInitialFullScreen = () => {
        // Si nous sommes déjà en plein écran
        if (fullScreenAPI.isFullScreen()) {
            markFullScreenAsInitialized();
            return;
        }

        // Si le quiz a déjà été initialisé en plein écran dans une session précédente
        if (isFullScreenInitialized()) {
            console.log("Le quiz était initialisé en plein écran - tentative de réactivation");

            // Essayer d'activer le plein écran automatiquement (peut échouer selon les restrictions du navigateur)
            setTimeout(() => {
                requestFullScreen();

                // Si l'activation échoue, afficher l'overlay
                setTimeout(() => {
                    if (!fullScreenAPI.isFullScreen()) {
                        createBlockingOverlay();
                    }
                }, 500);
            }, 300);
        } else {
            // Première initialisation - nécessite une interaction utilisateur
            console.log("Première initialisation - en attente d'interaction utilisateur");

            // Afficher un message pour informer l'utilisateur
            const quizContainer = document.querySelector('.card-body') || document.body;
            const infoMessage = document.createElement('div');
            infoMessage.className = 'alert alert-info mb-3 text-center';
            infoMessage.id = 'fullscreen-prompt';
            infoMessage.innerHTML = '<strong>Attention:</strong> Cliquez n\'importe où sur la page pour activer le mode plein écran requis pour ce quiz.';

            if (quizContainer.firstChild) {
                quizContainer.insertBefore(infoMessage, quizContainer.firstChild);
            } else {
                quizContainer.appendChild(infoMessage);
            }

            // Configurer les écouteurs pour activer le plein écran à la première interaction
            document.addEventListener('click', forceFullScreenOnInteraction);
            document.addEventListener('keydown', forceFullScreenOnInteraction);
            document.addEventListener('touchstart', forceFullScreenOnInteraction);

            // Supprimer le message après activation du plein écran
            const removePrompt = function() {
                const prompt = document.getElementById('fullscreen-prompt');
                if (prompt && fullScreenAPI.isFullScreen()) {
                    prompt.remove();
                    document.removeEventListener('fullscreenchange', removePrompt);
                    document.removeEventListener('webkitfullscreenchange', removePrompt);
                    document.removeEventListener('mozfullscreenchange', removePrompt);
                    document.removeEventListener('MSFullscreenChange', removePrompt);
                }
            };

            document.addEventListener('fullscreenchange', removePrompt);
            document.addEventListener('webkitfullscreenchange', removePrompt);
            document.addEventListener('mozfullscreenchange', removePrompt);
            document.addEventListener('MSFullscreenChange', removePrompt);
        }
    };

    // Vérifier l'état de navigation
    const checkNavigationState = () => {
        // Nettoyer le marqueur de navigation volontaire après un rechargement
        if (localStorage.getItem(questionSubmitKey)) {
            localStorage.removeItem(questionSubmitKey);
        }
    };

    // Initialiser tout
    const init = () => {
        console.log("Initialisation du script de surveillance du quiz");

        // Configurer les écouteurs pour le plein écran
        setupFullscreenListeners();

        // Vérifier l'état de navigation
        checkNavigationState();

        // Configurer la navigation AJAX
        setupAjaxNavigation();

        // Gérer l'état initial du plein écran
        handleInitialFullScreen();

        // Surveillance des onglets
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) handleTabSwitch();
        });

        // Protection contre les actions utilisateur
        ['contextmenu', 'copy', 'cut'].forEach(event => {
            document.addEventListener(event, preventUserActions);
        });
    };

    // Gestion du timer
    let timeLeft = {{ $timeLeft }};

    if (elements.timer) {
        const timerInterval = setInterval(() => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            elements.timer.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                window.location.href = "{{ route('quizzes.finish', $attempt->id) }}";
            }

            timeLeft--;
        }, 1000);
    }

    // Vérifier périodiquement l'état du plein écran
    setInterval(() => {
        // Si le plein écran était initialisé mais n'est plus actif,
        // et qu'il n'y a pas de navigation en cours,
        // essayer de le réactiver
        if (isFullScreenInitialized() && !fullScreenAPI.isFullScreen() && !localStorage.getItem(questionSubmitKey)) {
            console.log("Vérification périodique: plein écran désactivé - tentative de réactivation");
            requestFullScreen();

            // Si toujours pas en plein écran après tentative, afficher l'overlay
            setTimeout(() => {
                if (!fullScreenAPI.isFullScreen() && !document.getElementById('fullscreen-overlay')) {
                    createBlockingOverlay();
                }
            }, 500);
        }
    }, 2000);

    // Démarrer tout le processus
    init();
});
    </script> --}}
@endpush
