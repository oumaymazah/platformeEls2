@extends('layouts.admin.master')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('assets/css/MonCss/quizzes/quizShowPage.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Titre du quiz en card header -->
    <div class="card quiz-header-card">
        <div class="card-body d-flex justify-content-between align-items-center">

            <a href="{{ route('admin.quizzes.index') }}" class="back-icon">
                <i class="fas fa-arrow-left"></i>
            </a>

            <div style="width: 165px;"><!-- Espace vide pour l'équilibre --></div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Quiz Information Card -->
        <div class="col-md-6">
            <div class="card quiz-info-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Informations générales</h5>
                    <div class="action-icons-container">
                        @if(!$quiz->is_published)
                        <form action="{{ route('admin.quizzes.publish', $quiz->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="action-icon action-icon-publish" data-bs-toggle="tooltip" data-bs-placement="top" title="Publier">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        @else
                        <form action="{{ route('admin.quizzes.toggle', $quiz->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="action-icon action-icon-unpublish" data-bs-toggle="tooltip" data-bs-placement="top" title="Dépublier">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('admin.quizzes.destroy', $quiz->id) }}" method="POST" class="d-inline delete-quiz-form">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="action-icon action-icon-delete delete-quiz-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body quiz-info-card-body">
                    <table class="table quiz-info-table">
                        <tr>
                            <th>Statut</th>
                            <td>
                                @if($quiz->is_published)
                                    <span class="badge status-published"><i class="fas fa-check-circle me-1"></i> Publié</span>
                                @else
                                    <span class="badge status-draft"><i class="fas fa-clock me-1"></i> Non publié</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Titre de Quiz</th>
                            <td>{{ $quiz->title }}</td>
                        </tr>
                        <tr>
                            <th>Formation associée</th>
                            <td>{{ $quiz->training->title }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>
                                @if($quiz->isPlacementTest())
                                    <span class="badge badge-placement">Test de niveau</span>
                                @else
                                    <span class="badge badge-final">Quiz final</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Durée</th>
                            <td>{{ $quiz->duration }} minutes</td>
                        </tr>
                        @if($quiz->isFinalQuiz())
                        <tr>
                            <th>Score de passage</th>
                            <td>{{ $quiz->passing_score }}/20</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Nombre de questions</th>
                            <td>{{ $quiz->questions->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Card pour les statistiques -->
        <div class="col-md-6">
            <div class="card quiz-actions-card">
                <div class="card-header">
                    <h5 class="mb-0">Statistiques</h5>
                </div>
                <div class="card-body">
                    @if($quiz->is_published && $quiz->attempts->count() > 0)
                    <div class="row">
                        <div class="col-md-4">
                            <div class="quiz-stats-card" style="background-color: rgba(43, 110, 212, 0.1);">
                                <i class="fas fa-users text-primary"></i>
                                <h3>{{ $quiz->attempts->count() }}</h3>
                                <p>Tentatives</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="quiz-stats-card" style="background-color: rgba(52, 195, 143, 0.1);">
                                <i class="fas fa-chart-pie text-success"></i>
                                <h3>{{ number_format(($quiz->attempts->where('passed', true)->count() / $quiz->attempts->count()) * 100, 1) }}%</h3>
                                <p>Taux de réussite</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="quiz-stats-card" style="background-color: rgba(100, 116, 139, 0.1);">
                                <i class="fas fa-star text-warning"></i>
                                <h3>{{ number_format($quiz->attempts->avg('score'), 1) }}</h3>
                                <p>Score moyen / 20</p>
                            </div>
                        </div>
                    </div>
                    @elseif($quiz->is_published)
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h4>Aucune statistique disponible</h4>
                        <p>Ce quiz n'a pas encore été tenté par des apprenants.</p>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle text-warning"></i>
                        <h4>Quiz non publié</h4>
                        <p>Publiez ce quiz pour permettre aux apprenants de le passer et voir les statistiques.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Section -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Gestion des questions</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                <i class="fas fa-plus me-2"></i> Ajouter une question
            </button>
        </div>
        <div class="card-body">
            @if($quiz->questions->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-clipboard-question"></i>
                    <h4>Aucune question ajoutée</h4>
                    <p>Commencez par ajouter des questions à ce quiz pour que les étudiants puissent le passer.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="fas fa-plus me-2"></i> Ajouter une première question
                    </button>
                </div>
            @else
                <div class="two-panel-layout">
                    <!-- Questions Sidebar (maintenant à gauche) -->
                    <div class="questions-sidebar">
                        <div class="card mb-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Questions ({{ $quiz->questions->count() }})</h5>
                            </div>
                            <div class="card-body">
                                @foreach($quiz->questions as $index => $question)
                                <div class="question-card" id="question-card-{{ $index }}">
                                    <div class="question-header" onclick="showQuestionDetails({{ $index }})">
                                        <div>
                                            <h6>Question {{ $index + 1 }}</h6>
                                            <small class="text-muted">{{ Str::limit($question->question_text, 40) }}</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge-points me-3">{{ $question->points }} pts</span>
                                            <a href="#" class="delete-question-btn"
                                               data-delete-url="{{ route('admin.questions.destroy', $question->id) }}"
                                               data-csrf="{{ csrf_token() }}" onclick="event.stopPropagation();">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Question Detail Panel (maintenant à droite) -->
                    <div class="question-detail-panel">
                        <div id="questionDetailsContainer">
                            <div class="no-question-selected">
                                <i class="fas fa-hand-point-left"></i>
                                <h5>Sélectionnez une question</h5>
                                <p>Cliquez sur une question à gauche pour afficher ses détails</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Templates cachés pour les détails de questions -->
@foreach($quiz->questions as $index => $question)
<template id="question-details-{{ $index }}">
    <div class="question-details">
        <h5>Question {{ $index + 1 }}</h5>
        <div class="mb-4">
            <h6 class="text-secondary"><strong>Énoncé</strong></h6>
            <p>{{ $question->question_text }}</p>
        </div>

        <div>
            <h6 class="text-secondary"><strong>Réponses possibles</strong></h6>
            <div class="answer-list">
                @foreach($question->answers as $answer)
                <div class="answer-item {{ $answer->is_correct ? 'correct' : '' }}">
                    <div>{{ $answer->answer_text }}</div>
                    @if($answer->is_correct)
                    <span class="correct-badge">Correcte</span>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</template>
@endforeach

<!-- Modal pour ajouter une question (amélioré) -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.questions.store') }}" method="POST" class="needs-validation" novalidate id="question-form">
                @csrf
                <input type="hidden" name="quiz_id" value="{{ $quiz->id }}" id="hidden_quiz_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="addQuestionModalLabel">
                        <i class="fas fa-question-circle me-2"></i>
                        Ajouter une nouvelle question
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <label for="question_text" class="form-label">Énoncé de la question</label>
                        <textarea name="question_text" id="statement" class="form-control" rows="3" placeholder="Entrez votre question ici..." required></textarea>
                        <div class="invalid-feedback">Veuillez entrer un énoncé pour cette question.</div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label for="points" class="form-label">Points</label>
                            <div class="input-group">
                                <input type="number" name="points" id="points" class="form-control" value="1" min="1" required>
                                <span class="input-group-text">pts</span>
                                <div class="invalid-feedback">Veuillez entrer une valeur valide.</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="question_type" class="form-label">Type de question</label>
                            <select class="form-select" id="question_type" name="question_type">
                                <option value="single">Réponse unique</option>
                                <option value="multiple">Réponses multiples</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="response_count" class="form-label">Nombre de réponses</label>
                            <select class="form-select" id="response_count" name="response_count" data-initial="4">
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4" selected>4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="border-bottom pb-2 mb-4">Réponses</h5>
                    <div class="alert alert-danger d-none" id="error-message"></div>

                    <div id="reponses-container" class="mb-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('assets/js/MonJs/questions/question-create.js') }}"></script>
<script src="{{ asset('assets/js/MonJs/questions/showpage.js') }}"></script>

@endpush

@endsection
