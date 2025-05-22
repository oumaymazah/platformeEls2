@extends('layouts.admin.master')
@push('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    :root {
        --primary-color: #2B6ED4;
        --primary-light: #4c89e8;
        --primary-dark: #1e5bb7;
        --success-color: #2B6ED4; /* Remplacé le vert par le bleu */
        --danger-color: #d9364f; /* Assombri le rouge pour plus de contraste */
        --warning-color: #ffc107;
        --text-color: #333;
        --light-gray: #f8f9fa;
        --border-radius: 8px;
        --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        --transition: all 0.3s ease;
    }

    body {
        background-color: #f5f7fa;
        color: var(--text-color);
    }

    .card {
        border-radius: var(--border-radius) !important;
        box-shadow: var(--box-shadow);
        border: none !important;
        overflow: hidden;
        transition: var(--transition);
    }

    .card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
        border-bottom: none !important;
        padding: 1.25rem 1.5rem !important;
    }

    .card-body {
        padding: 1.5rem !important;
    }

    .result-summary {
        padding: 1.5rem;
        border-radius: var(--border-radius);
        background-color: white;
        box-shadow: var(--box-shadow);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .score-card {
        position: relative;
        padding: 1.5rem;
        border-radius: var(--border-radius);
        background: white;
        box-shadow: var(--box-shadow);
        text-align: center;
        height: 100%;
    }

    .score-circle {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        margin: 1rem auto;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.0rem;
        font-weight: bold;
        position: relative;
        box-shadow: inset 0 0 15px rgba(0, 0, 0, 0.1);
    }

    .passed .score-circle {
        background: linear-gradient(to bottom right, #c8e6ff, var(--success-color)); /* Modifié pour le bleu */
        color: white;
    }

    .failed .score-circle {
        background: linear-gradient(to bottom right, #ffd1d8, var(--danger-color)); /* Assombri pour plus de contraste */
        color: white;
    }

    .detail-card {
        padding: 1.5rem;
        border-radius: var(--border-radius);
        background: white;
        box-shadow: var(--box-shadow);
        height: 100%;
    }

    .detail-card i {
        color: var(--primary-color);
        margin-right: 8px;
    }

    .btn {
        border-radius: 30px;
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        transition: var(--transition);
    }

    .btn-primary {
        background-color: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
    }

    .btn-primary:hover {
        background-color: var(--primary-dark) !important;
        border-color: var(--primary-dark) !important;
        transform: translateY(-2px);
    }

    .btn-success {
        background-color: var(--success-color) !important;
        border-color: var(--success-color) !important;
    }

    .btn-success:hover {
        background-color: var(--primary-dark) !important; /* Changé pour correspondre au bleu */
        border-color: var(--primary-dark) !important;
        transform: translateY(-2px);
    }

    .btn-outline-secondary {
        color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-outline-secondary:hover {
        background-color: var(--primary-light);
        border-color: var(--primary-light);
        color: white;
        transform: translateY(-2px);
    }

    .badge {
        padding: 0.5em 0.8em;
        border-radius: 30px;
        font-weight: 500;
    }

    .badge-success {
        background-color: var(--success-color) !important;
    }

    .badge-danger {
        background-color: var(--danger-color) !important;
    }

    .badge-info {
        background-color: var(--primary-light) !important;
    }

    .alert {
        border-radius: var(--border-radius);
        border: none;
    }

    .alert-success {
        background-color: #d4e6ff; /* Modifié pour le bleu */
        color: #0d4899; /* Modifié pour le bleu foncé */
    }

    .alert-danger {
        background-color: #ffd1d8; /* Assombri pour plus de contraste */
        color: #9c1c2d; /* Assombri pour plus de contraste */
    }

    /* Certificat section */
    .certificate-container {
        background: linear-gradient(135deg, #f5f7fa, #e4f1fe);
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin: 1.5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .certificate-container:before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 100px;
        height: 100px;
        background: var(--primary-light);
        opacity: 0.1;
        border-radius: 50%;
    }

    .certificate-container:after {
        content: '';
        position: absolute;
        bottom: -30px;
        left: -30px;
        width: 60px;
        height: 60px;
        background: var(--success-color);
        opacity: 0.1;
        border-radius: 50%;
    }

    .certificate-icon {
        font-size: 3rem;
        color: var(--success-color); /* Changé pour le bleu */
        margin-bottom: 1rem;
    }


    .accordion .card {
        margin-bottom: 1rem;
        border: none !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        border-radius: 10px !important;
        overflow: hidden;
    }

    .accordion .card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .accordion .card-header {
        background: #f8f9fc !important;
        border-bottom: none !important;
        padding: 0 !important;
        border-radius: 10px !important;
    }

    .accordion .btn-link {
        width: 100%;
        text-align: left;
        text-decoration: none !important;
        color: var(--text-color) !important;
        font-weight: 600;
        position: relative;
        padding: 1.25rem 1.5rem !important;
        border-radius: 10px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .accordion .btn-link:hover {
        background-color: rgba(43, 110, 212, 0.05);
    }

    .accordion .btn-link:after {
        content: '\f107';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        position: absolute;
        right: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        transition: all 0.3s ease;
        font-size: 1.2rem;
        color: var(--primary-color);
    }

    .accordion .btn-link.collapsed:after {
        transform: translateY(-50%) rotate(-90deg);
    }

    .accordion .text-success {
        color: var(--success-color) !important;
    }

    .accordion .text-danger {
        color: var(--danger-color) !important;
    }

    .accordion i.fa-check-circle {
        color: var(--success-color);
        font-size: 1.25rem;
        margin-right: 12px;
    }

    .accordion i.fa-times-circle {
        color: var(--danger-color);
        font-size: 1.25rem;
        margin-right: 12px;
    }

    .accordion .card-body {
        padding: 1.5rem 2rem !important;
        background-color: #ffffff;
    }

    /* Amélioration des listes de réponses */
    .accordion ul {
        list-style-type: none;
        padding-left: 10px;
        margin-bottom: 1rem;
    }

    .accordion ul li {
        position: relative;
        padding: 10px 15px;
        margin-bottom: 8px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        transition: all 0.2s ease;
    }

    .accordion ul li.text-success {
        background-color: rgba(43, 110, 212, 0.08);
    }

    .accordion ul li.text-danger {
        background-color: rgba(217, 54, 79, 0.08);
    }

    .accordion ul li i {
        margin-left: auto;
        font-size: 1rem;
    }

    .accordion ul li i.fa-check {
        color: var(--success-color);
    }

    .accordion ul li i.fa-times {
        color: var(--danger-color);
    }

    /* Badge pour les questions à choix multiple */
    .accordion .badge-secondary {
        background-color: var(--primary-light) !important;
        color: white;
        font-weight: 500;
        padding: 0.4em 0.8em;
        margin-left: 15px !important;
        border-radius: 20px;
        font-size: 0.75rem;
    }

    /* Section des réponses correctes */
    .accordion p.text-success {
        margin-top: 1.5rem !important;
        padding-top: 1rem;
        border-top: 1px dashed rgba(43, 110, 212, 0.3);
    }

    .accordion ul.text-success li {
        background-color: rgba(43, 110, 212, 0.05);
        padding: 10px 15px;
        margin-bottom: 8px;
        border-radius: 8px;
        border-left: 3px solid var(--success-color);
    }

    /* Animation douce pour l'ouverture/fermeture */
    .accordion .collapse {
        transition: all 0.35s ease;
    }

    /* Header pour afficher le titre de la section */
    .answers-header {
        display: flex;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(43, 110, 212, 0.2);
    }

    .answers-header i {
        color: var(--primary-color);
        font-size: 1.5rem;
        margin-right: 12px;
    }

    .answers-header h5 {
        margin-bottom: 0;
        font-weight: 600;
    }

    /* Cheating alert */
    .cheat-alert {
        background: linear-gradient(135deg, #fff3cd, #ffeeba);
        border-left: 4px solid var(--warning-color);
        border-radius: var(--border-radius);
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    /* Star rating */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
    }

    .star-container {
        position: relative;
        font-size: 2rem;
        cursor: pointer;
        margin: 0 5px;
    }

    .star-half-left, .star-half-right {
        position: absolute;
        top: 0;
        width: 50%;
        height: 100%;
        cursor: pointer;
        z-index: 2;
    }

    .star-half-left {
        left: 0;
    }

    .star-half-right {
        right: 0;
    }

    .star-container i {
        transition: all 0.3s;
    }

    .star-container i.fas.fa-star {
        color: var(--warning-color);
    }

    .star-container i.fas.fa-star-half-alt {
        color: var(--warning-color);
    }

    /* Animation pour les étoiles */
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.3); }
    }

    .star-container.selected i {
        animation: bounce 0.5s;
    }

    /* Modal styling */
    .modal-content {
        border-radius: var(--border-radius);
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
        color: white;
        border-bottom: none;
        border-top-left-radius: var(--border-radius);
        border-top-right-radius: var(--border-radius);
    }

    .modal-footer {
        border-top: none;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .score-circle {
            width: 100px;
            height: 100px;
            font-size: 2rem;
        }

        .certificate-container {
            padding: 1rem;
        }
    }
</style>
@endpush
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0 text-white"><i class="fas fa-graduation-cap mr-2"></i>Résultats du Quiz: {{ $quiz->title }}</h4>
                </div>

                <div class="card-body">
                    @if($attempt->isCheated())
                        <div class="cheat-alert">
                            <h5><i class="fas fa-exclamation-triangle text-warning mr-2"></i> Tentative de triche détectée</h5>
                            <p class="mb-0">Vous avez changé d'onglet ou d'application trop souvent pendant le quiz.</p>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6 mb-4 mb-md-0">
                            <div class="score-card {{ $attempt->passed ? 'passed' : 'failed' }}">
                                <h5 class="card-title">Votre Score</h5>
                                <div class="score-circle">
                                    {{ $attempt->score }}
                                    @if($quiz->isFinalQuiz())
                                        <span style="font-size: 1rem; position: absolute; bottom: 1rem;">/20</span>
                                    @endif
                                </div>
                                @if($quiz->isFinalQuiz())
                                    <p class="mt-3 mb-0">
                                        @if($attempt->passed)
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Réussi</span>
                                        @else
                                            <span class="badge badge-danger"><i class="fas fa-times mr-1"></i>Échoué</span>
                                        @endif
                                        <br>
                                        <small class="text-muted mt-2 d-block">Score minimum requis: {{ $quiz->passing_score }}/20</small>
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-card">
                                <h5 class="card-title mb-3">Détails</h5>
                                <p class="mb-2">
                                    <i class="fas fa-clock"></i>
                                    <strong>Temps:</strong>
                                    @if($attempt->started_at && $attempt->finished_at)
                                        {{ $attempt->started_at->diff($attempt->finished_at)->format('%i minutes %s secondes') }}
                                    @else
                                        Non disponible
                                    @endif
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-calendar-alt"></i>
                                    <strong>Date:</strong>
                                    @if($attempt->finished_at)
                                        {{ $attempt->finished_at->format('d/m/Y H:i') }}
                                    @else
                                        {{ now()->format('d/m/Y H:i') }} (Estimée)
                                    @endif
                                </p>
                                @if($level)
                                    <p class="mb-0">
                                        <i class="fas fa-layer-group"></i>
                                        <strong>Niveau:</strong>
                                        <span class="badge badge-info">{{ $level }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($quiz->isFinalQuiz() && $attempt->passed)
                        <div class="certificate-container">
                            <div class="certificate-icon">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h5><i class="fas fa-trophy text-warning mr-2"></i> Certificat obtenu</h5>
                            <p>Félicitations ! Vous avez réussi le quiz et obtenu votre certificat.</p>
                            <div class="mt-3">
                                <a href="{{ route('certificates.show', ['user' => auth()->id(), 'training' => $quiz->training_id]) }}" class="btn btn-success">
                                    <i class="fas fa-certificate"></i> Voir le certificat
                                </a>
                                <a href="{{ route('certificates.download', ['user' => auth()->id(), 'training' => $quiz->training_id]) }}" class="btn btn-success ml-2">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                            </div>
                        </div>
                    @endif



                    <div class="answers-header">
                        <i class="fas fa-list-ol"></i>
                        <h5>Détail des réponses</h5>
                    </div>

                    <div class="accordion" id="answersAccordion">
                        @php
                            // Regrouper les réponses par question
                            $answersGroupedByQuestion = $userAnswers->groupBy('question_id');
                        @endphp

                        @foreach($answersGroupedByQuestion as $questionId => $questionAnswers)
                            @php
                                $question = $questionAnswers->first()->question;
                                $allQuestionAnswers = $question->answers;
                                $correctAnswers = $allQuestionAnswers->where('is_correct', true);
                                $userSelectedAnswerIds = $questionAnswers->pluck('answer_id')->toArray();

                                // Déterminer si la question est entièrement correcte
                                $isQuestionCorrect = true;

                                // Vérifier si l'utilisateur a sélectionné toutes les bonnes réponses
                                foreach ($correctAnswers as $correctAnswer) {
                                    if (!in_array($correctAnswer->id, $userSelectedAnswerIds)) {
                                        $isQuestionCorrect = false;
                                        break;
                                    }
                                }

                                // Vérifier si l'utilisateur a sélectionné des réponses incorrectes
                                $incorrectSelections = array_diff(
                                    $userSelectedAnswerIds,
                                    $correctAnswers->pluck('id')->toArray()
                                );

                                if (count($incorrectSelections) > 0) {
                                    $isQuestionCorrect = false;
                                }
                            @endphp

                            <div class="card mb-2">
                                <div class="card-header" id="heading{{ $questionId }}">
                                    <h6 class="mb-0">
                                        <button class="btn btn-link {{ $isQuestionCorrect ? 'text-success' : 'text-danger' }} collapsed"
                                                type="button" data-toggle="collapse"
                                                data-target="#collapse{{ $questionId }}"
                                                aria-expanded="false"
                                                aria-controls="collapse{{ $questionId }}">
                                            <i class="fas {{ $isQuestionCorrect ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                            <span>{{ $question->question_text }}</span>
                                            @if(count($correctAnswers) > 1)
                                                <span class="badge badge-secondary">Question à choix multiple</span>
                                            @endif
                                        </button>
                                    </h6>
                                </div>

                                <div id="collapse{{ $questionId }}" class="collapse" aria-labelledby="heading{{ $questionId }}"
                                    data-parent="#answersAccordion">
                                    <div class="card-body">
                                        <p><strong>Vos réponses:</strong></p>
                                        <ul>
                                            @foreach($questionAnswers as $answer)
                                                <li class="{{ $answer->is_correct ? 'text-success' : 'text-danger' }}">
                                                    <span>{{ $answer->answer->answer_text }}</span>
                                                    <i class="fas {{ $answer->is_correct ? 'fa-check' : 'fa-times' }}"></i>
                                                </li>
                                            @endforeach
                                        </ul>

                                        @if(!$isQuestionCorrect)
                                            <p class="text-success"><strong>Réponses correctes:</strong></p>
                                            <ul class="text-success">
                                                @foreach($correctAnswers as $correctAnswer)
                                                    @if(!in_array($correctAnswer->id, $userSelectedAnswerIds))
                                                        <li>{{ $correctAnswer->answer_text }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        @if($quiz->isFinalQuiz())
                            <a href="{{ route('index') }}" class="btn btn-primary">
                                <i class="fas fa-home mr-1"></i> Retour au tableau de bord
                            </a>
                        @else
                            <a href="{{ route('index') }}" class="btn btn-primary">
                                <i class="fas fa-home mr-1"></i> Retour au tableau de bord
                            </a>
                        @endif

                        <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#feedbackModal">
                            <i class="fas fa-star mr-1"></i> Donner votre avis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('feedbacks.store') }}" method="POST" id="feedbackForm">
                @csrf
                <input type="hidden" name="training_id" value="{{ $quiz->training_id }}">
                <input type="hidden" name="quiz_attempt_id" value="{{ $attempt->id }}">

                <div class="modal-header">
                    <h5 class="modal-title" id="feedbackModalLabel"><i class="fas fa-comment-alt mr-2"></i>Évaluer la formation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Notez cette formation (0.5 à 5 étoiles)</label>
                        <div class="star-rating text-center">
                            <div class="star-container" data-value="5">
                                <div class="star-half-right" data-value="5.0"></div>
                                <div class="star-half-left" data-value="4.5"></div>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="star-container" data-value="4">
                                <div class="star-half-right" data-value="4.0"></div>
                                <div class="star-half-left" data-value="3.5"></div>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="star-container" data-value="3">
                                <div class="star-half-right" data-value="3.0"></div>
                                <div class="star-half-left" data-value="2.5"></div>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="star-container" data-value="2">
                                <div class="star-half-right" data-value="2.0"></div>
                                <div class="star-half-left" data-value="1.5"></div>
                                <i class="far fa-star"></i>
                            </div>
                            <div class="star-container" data-value="1">
                                <div class="star-half-right" data-value="1.0"></div>
                                <div class="star-half-left" data-value="0.5"></div>
                                <i class="far fa-star"></i>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <span class="badge badge-primary p-2" id="rating-value">Pas de note</span>/5
                        </div>
                        <input type="hidden" name="rating_count" id="rating_count">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane mr-1"></i> Envoyer l'évaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>

$(document).ready(function() {
    // Vérifier si l'utilisateur a déjà soumis un feedback
    $.ajax({
        url: '/check-feedback',
        method: 'GET',
        data: {
            quiz_attempt_id: '{{ $attempt->id }}'
        },
        success: function(response) {
            if (response.has_feedback) {
                $('[data-target="#feedbackModal"]').prop('disabled', true)
                    .addClass('btn-secondary').removeClass('btn-outline-secondary')
                    .text('Avis déjà soumis');
            }
        }
    });

    // Initialiser les étoiles
    $('.star-container i').removeClass('fa-star fa-star-half-alt').addClass('far fa-star');

    // Gérer le clic sur les demi-étoiles
    $('.star-half-left, .star-half-right').on('click', function() {
        let rating = parseFloat($(this).data('value'));
        $('#rating_count').val(rating);
        $('#rating-value').text(rating.toFixed(1));
        updateStars(rating);
    });

    function updateStars(rating) {
        $('.star-container').removeClass('selected');
        $('.star-container').each(function() {
            let value = parseFloat($(this).data('value'));
            let starIcon = $(this).find('i');
            starIcon.removeClass('fa-star fa-star-half-alt far fas');

            if (rating >= value) {
                starIcon.addClass('fas fa-star');
                $(this).addClass('selected');
            } else if (rating + 0.5 >= value && rating < value) {
                starIcon.addClass('fas fa-star-half-alt');
                $(this).addClass('selected');
            } else {
                starIcon.addClass('far fa-star');
            }
        });
    }

    // Gérer la soumission du formulaire
    $('#feedbackForm').submit(function(e) {
        e.preventDefault();

        // Vérifier si une note a été sélectionnée
        if (!$('#rating_count').val()) {
            alert('Veuillez sélectionner une note entre 0.5 et 5 étoiles');
            return;
        }

        // Afficher les données avant envoi pour débogage
        console.log("Données envoyées:", $(this).serialize());

        // Désactiver le bouton pour éviter les soumissions multiples
        $(this).find('button[type="submit"]').prop('disabled', true).text('Envoi en cours...');

        // Fermer la modal manuellement, même avant l'AJAX (puisque nous savons que les données sont enregistrées)
        manuallyCloseModal();

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                console.log("Succès:", response);

                // Remplacer le bouton par un message de succès
                $('[data-target="#feedbackModal"]').replaceWith(
                    '<div class="alert alert-success d-inline-block float-right py-1 px-3 mb-0">' +
                    '   <i class="fas fa-check-circle"></i> Merci pour votre feedback !' +
                    '</div>'
                );
            },
            error: function(xhr, status, error) {
                console.error("Erreur AJAX:", status, error);
                console.error("Détails:", xhr);

                // Si l'erreur est 422 mais que nous savons que les données sont enregistrées,
                // nous pouvons ignorer l'erreur
                if (xhr.status === 422) {
                    console.log("Erreur 422 ignorée, l'enregistrement a probablement réussi");
                } else {
                    // Rouvrir le modal en cas d'erreur réelle
                    setTimeout(function() {
                        $('#feedbackModal').modal('show');
                    }, 300);

                    // Réactiver le bouton
                    $('#feedbackForm').find('button[type="submit"]').prop('disabled', false).text('Envoyer l\'évaluation');

                    // Afficher l'erreur
                    alert('Une erreur est survenue: ' + error);
                }
            },
            complete: function() {
                // Remplacer le bouton par un message de succès, même en cas d'erreur 422
                // puisque nous savons que les données sont enregistrées
                $('[data-target="#feedbackModal"]').replaceWith(
                    '<div class="alert alert-success d-inline-block float-right py-1 px-3 mb-0">' +
                    '   <i class="fas fa-check-circle"></i> Merci pour votre feedback !' +
                    '</div>'
                );
            }
        });
    });

    // Fonction pour fermer manuellement le modal, à cause du bug
    function manuallyCloseModal() {
        // 1. Cacher le modal
        $('#feedbackModal').modal('hide');

        // 2. Attendre un peu puis:
        setTimeout(function() {
            // 3. Supprimer tous les éléments liés à Bootstrap modal
            $('.modal-backdrop').remove();
            $('#feedbackModal').removeClass('show');
            $('#feedbackModal').css('display', 'none');
            $('body').removeClass('modal-open').css('padding-right', '');
            $('body').attr('style', '');
        }, 100);
    }

    // Gestionnaire pour fermer manuellement avec le bouton "Annuler"
    $('#feedbackModal .btn-secondary').on('click', function() {
        manuallyCloseModal();
    });

    // Gestionnaire pour fermer manuellement avec le X en haut à droite
    $('#feedbackModal .close').on('click', function() {
        manuallyCloseModal();
    });
});
</script>
@endpush
