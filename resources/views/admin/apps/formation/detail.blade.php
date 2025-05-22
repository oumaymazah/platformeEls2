@extends('layouts.admin.master')
@section('title', $training->title . ' - Détails')
@section('content')
<div class="container-fluid">

</div>
<div class="container-fluid">
    <div class="row">
        <!-- Information de la formation (côté gauche) -->
        <div class="col-lg-8 content-area mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Informations de la formation</h6>

                    </div>
                </div>
                <div class="card-body" id="lesson-content-area">
                    <!-- Contenu par défaut qui sera remplacé par le contenu de la leçon -->
                    <div class="row">
                        <div class="col-md-5">
                            <img src="{{ asset('storage/' . $training->image) }}"  alt="{{ $training->title }}" class="img-fluid rounded mb-3">
                        </div>
                        <div class="col-md-7">
                            <h4>{{ $training->title }}</h4>
                            <div class="d-flex align-items-center mb-2">
                                <div class="rating-stars me-2">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= round($averageRating))
                                            <i class="fa fa-star text-warning"></i>
                                        @else
                                            <i class="fa fa-star-o text-muted"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span>{{ number_format($averageRating, 1) }} ({{ $totalFeedbacks }} avis)</span>
                            </div>
                            <p><strong>Enseignant:</strong> {{ $training->user->name }} {{ $training->user->lastname }}</p>
                            <p><strong>Catégorie:</strong> {{ $training->category->name }}</p>
                            <p><strong>Type:</strong> {{ $training->type == 'payante' ? 'Payante' : 'Gratuite' }}</p>
                            <p><strong>Prix:</strong>
                                @if($training->type == 'payante')
                                    @if($training->discount > 0)
                                        <span class="text-primary">{{ number_format($training->final_price, 2) }} Dt</span>
                                        <del class="text-muted">{{ number_format($training->price, 2) }} Dt</del>
                                    @else
                                        <span class="text-primary">{{ number_format($training->price, 2) }} Dt</span>
                                    @endif
                                @else
                                    <span class="badge bg-success">Gratuite</span>
                                @endif
                            </p>
                            <p><strong>Durée:</strong> {{ $training->formatted_duration }}</p>
                            <p><strong>Places disponibles:</strong> {{ $training->remaining_seats }} / {{ $training->total_seats }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <h5>Description</h5>
                        <p>{{ $training->description }}</p>
                    </div>
                </div>
            </div>
        </div>
    <!-- Structure du cours (côté droit) -->
    <div class="col-lg-4 sidebar-area">
        <div class="card">
            <div class="card-header">
                <h6><i class="fa fa-list me-2"></i>Contenu du formations</h6>
            </div>
            <div class="card-body p-0">
                <div class="course-structure accordion accordion-flush" id="courseStructure">
                    <!-- Quiz de placement si présent -->
                    @foreach($training->quizzes as $quiz)
                        @if($quiz->type == 'placement')
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-quiz-{{ $quiz->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-quiz-{{ $quiz->id }}" aria-expanded="false">
                                        <i class="icon-help-circle me-2"></i> Test de niveau
                                    </button>
                                </h2>
                                <div id="collapse-quiz-{{ $quiz->id }}" class="accordion-collapse collapse" aria-labelledby="heading-quiz-{{ $quiz->id }}">
                                    <div class="accordion-body">
                                        <div class="lesson-item" data-quiz-id="{{ $quiz->id }}">
                                            <a href="javascript:void(0)" class="quiz-link d-flex align-items-center">
                                                <i class="icon-help me-2"></i>
                                                <span>{{ $quiz->title }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    <!-- Cours -->
                    @foreach($training->courses as $course)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-course-{{ $course->id }}">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-course-{{ $course->id }}" aria-expanded="false">
                                    <i class="icon-book me-2"></i> {{ $course->title }}
                                </button>
                            </h2>
                            <div id="collapse-course-{{ $course->id }}" class="accordion-collapse collapse" aria-labelledby="heading-course-{{ $course->id }}">
                                <div class="accordion-body p-0">
                                    <!-- Chapitres -->
                                    @foreach($course->chapters as $chapter)
                                        <div class="accordion-item nested-accordion">
                                            <h2 class="accordion-header" id="heading-chapter-{{ $chapter->id }}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-chapter-{{ $chapter->id }}" aria-expanded="false">
                                                    <i class="icon-folder me-2"></i> {{ $chapter->title }}
                                                </button>
                                            </h2>
                                            <div id="collapse-chapter-{{ $chapter->id }}" class="accordion-collapse collapse" aria-labelledby="heading-chapter-{{ $chapter->id }}">
                                                <div class="accordion-body">
                                                    <!-- Leçons -->
                                                    @foreach($chapter->lessons as $lesson)
                                                        <div class="lesson-item" data-lesson-id="{{ $lesson->id }}">
                                                            <a href="javascript:void(0)" class="lesson-link d-flex align-items-center">
                                                                <i class="icon-file-text me-2"></i>
                                                                <span>{{ $lesson->title }}</span>
                                                                <span class="ms-auto text-muted small">{{ $lesson->duration }}</span>
                                                            </a>
                                                        </div>
                                                        {{-- <div class="lesson-item" data-lesson-id="{{ $lesson->id }}">
                                                            <a href="javascript:void(0)" class="lesson-link d-flex align-items-center">
                                                                <i class="icon-file-text me-2"></i>
                                                                <span>{{ $lesson->title }}</span>
                                                                <span class="ms-auto text-muted small">{{ $lesson->duration }}</span>
                                                            </a>
                                                        </div> --}}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Quiz final si présent -->
                    @foreach($training->quizzes as $quiz)
                        @if($quiz->type == 'final')
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-quiz-{{ $quiz->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-quiz-{{ $quiz->id }}" aria-expanded="false">
                                        <i class="icon-award me-2"></i> Quiz final
                                    </button>
                                </h2>
                                <div id="collapse-quiz-{{ $quiz->id }}" class="accordion-collapse collapse" aria-labelledby="heading-quiz-{{ $quiz->id }}">
                                    <div class="accordion-body">
                                        <div class="lesson-item" data-quiz-id="{{ $quiz->id }}">
                                            <a href="javascript:void(0)" class="quiz-link d-flex align-items-center">
                                                <i class="icon-help me-2"></i>
                                                <span>{{ $quiz->title }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- <script>
    $(document).ready(function() {
        console.log('URL de la requête AJAX:', '{{ route("training.lesson.content") }}');
        Gérer le clic sur un lien de leçon
        $('.lesson-link').on('click', function() {
            const lessonItem = $(this).closest('.lesson-item');
            const lessonId = lessonItem.data('lesson-id');

            console.log('Clic détecté sur une leçon - ID:', lessonId); // Debug

            // Ajouter la classe active et l'enlever des autres
            $('.lesson-item').removeClass('active');
            lessonItem.addClass('active');

            // Charger le contenu de la leçon via AJAX
            $.ajax({
                url: '{{ route("training.lesson.content") }}',
                method: 'POST',
                data: {
                    lesson_id: lessonId,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('#lesson-content-area').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
                success: function(response) {
                    console.log('Réponse reçue:', response.status); // Debug
                    if (response.status === 'success') {
                        $('#lesson-content-area').html(response.content);
                    } else {
                        $('#lesson-content-area').html(response.content);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', status, error); // Debug détaillé
                    $('#lesson-content-area').html('<div class="alert alert-danger">Une erreur est survenue lors du chargement du contenu: ' + error + '</div>');
                }
            });
        });


        // Gérer le clic sur un lien de quiz
        $('.quiz-link').on('click', function() {
            const quizItem = $(this).closest('.lesson-item');
            const quizId = quizItem.data('quiz-id');

            console.log('Clic détecté sur un quiz - ID:', quizId); // Debug

            // Ajouter la classe active et l'enlever des autres
            $('.lesson-item').removeClass('active');
            quizItem.addClass('active');

            // Charger le contenu du quiz via AJAX
            $.ajax({
                url: '{{ route("training.quiz.content") }}',
                method: 'POST',
                data: {
                    quiz_id: quizId,
                    _token: '{{ csrf_token() }}'
                },
                beforeSend: function() {
                    $('#lesson-content-area').html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
                },
                success: function(response) {
                    console.log('Réponse reçue:', response.status); // Debug
                    if (response.status === 'success') {
                        $('#lesson-content-area').html(response.content);
                    } else {
                        $('#lesson-content-area').html(response.content);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', status, error); // Debug détaillé
                    $('#lesson-content-area').html('<div class="alert alert-danger">Une erreur est survenue lors du chargement du contenu: ' + error + '</div>');
                }
            });
        });
    });
</script> --}}






@endsection
@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    .content-area {
        min-height: 600px;
    }

    .sidebar-area .card {
        position: sticky;
        top: 80px;
    }

    .course-structure {
        max-height: 600px;
        overflow-y: auto;
    }

    .lesson-item {
        padding: 8px 15px;
        border-bottom: 1px solid #eee;
        transition: all 0.2s;
    }

    .lesson-item:last-child {
        border-bottom: none;
    }

    .lesson-item.active {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
    }

    .lesson-link, .quiz-link {
        color: var(--bs-body-color);
        text-decoration: none;
    }

    .lesson-link:hover, .quiz-link:hover {
        color: var(--bs-primary);
    }

    .nested-accordion .accordion-button {
        padding-left: 2.5rem;
        font-size: 0.95rem;
    }

    .lesson-item {
        padding-left: 3.5rem;
    }
</style>
@endsection
