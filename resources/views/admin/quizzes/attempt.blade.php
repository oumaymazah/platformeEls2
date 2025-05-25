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


    // Surveillance des onglets
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) handleTabSwitch();
    });

    // Protection contre les actions utilisateur
    ['contextmenu', 'copy', 'cut'].forEach(event => {
        document.addEventListener(event, preventUserActions);
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

@endpush
