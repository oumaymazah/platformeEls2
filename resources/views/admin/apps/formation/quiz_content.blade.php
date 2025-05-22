<div class="quiz-info mb-4">
    <p><strong>Type de quiz:</strong> {{ $quiz->type == 'placement' ? 'Test de niveau' : 'Quiz final' }}</p>
    <p><strong>Durée:</strong> {{ $quiz->duration }} minutes</p>
    @if($quiz->isFinalQuiz())
        <p><strong>Score de passage:</strong> {{ $quiz->passing_score }} /20</p>
    @endif

</div>

<div class="quiz-description mb-4">
    @if($quiz->type == 'placement')
        <div class="alert alert-info">
            <i class="icon-info-circle me-2"></i> Ce test de niveau vous permettra d'évaluer vos compétences initiales. Cela nous aidera à vous accompagner efficacement tout au long de votre parcours d'apprentissage.
        </div>
    @else
        <div class="alert alert-warning">
            <i class="icon-award me-2"></i> Ce quiz final vous permettra de valider les connaissances acquises durant cette formation.
        </div>
    @endif
</div>
@if(auth()->user()->hasRole('etudiant'))
    <form id="quiz-form" method="POST" action="{{ route('quizzes.start', $quiz->id) }}">
        @csrf
        <div class="card">
            <div class="card-body">
                <p>Ce quiz comporte {{ $quiz->questions->count() }} questions.</p>
                <p>Une fois commencé, vous aurez {{ $quiz->duration }} minutes pour compléter ce quiz.</p>
                <p>Pour réussir, vous devez obtenir un score d'au moins {{ $quiz->passing_score }}%.</p>
            </div>
            <div class="card-footer text-center">
                <button type="button" class="btn btn-primary" id="start-quiz-btn">
                    <i class="icon-play me-2"></i>Commencer le quiz
                </button>
            </div>
        </div>
    </form>
@endif
