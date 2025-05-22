{{--
<div class="container py-5">

    <div class="card border-0 rounded-3 shadow-lg overflow-hidden">


        <div class="card-header border-0 d-flex align-items-center py-4 px-4" style="background-color: #2B6ED4; color: #FFFFFF;">
            <button class="btn btn-light me-3 back-btn" data-back-tab="evaluation">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h4 class="mb-0 fw-semibold mx-auto">
                <i class="fas fa-clipboard-check me-2"></i> Détails de la tentative
            </h4>
        </div>

        <div class="card-body p-4" style="background-color: #F8FAFC;">

            <div class="row g-4 mb-5">

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header py-3 px-4 border-0" style="background-color: #E6EEFA;">
                            <h5 class="mb-0 fw-medium" style="color: #1A4A93;">
                                <i class="fas fa-user-graduate me-2" style="color: #2B6ED4;"></i> Informations étudiant
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-id-card me-2" style="color: #2B6ED4;"></i> Nom complet
                                </div>
                                <div class="col-md-7 fw-semibold" style="color: #1F2937;">{{ $attempt->user->name }}  {{ $attempt->user->lastname ?? '' }}</div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-envelope me-2" style="color: #2B6ED4;"></i> Email
                                </div>
                                <div class="col-md-7" style="color: #1F2937;">{{ $attempt->user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header py-3 px-4 border-0" style="background-color: #E6EEFA;">
                            <h5 class="mb-0 fw-medium" style="color: #1A4A93;">
                                <i class="fas fa-chart-bar me-2" style="color: #2B6ED4;"></i> Résultats
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-book me-2" style="color: #2B6ED4;"></i> Quiz
                                </div>
                                <div class="col-md-7 fw-semibold" style="color: #1F2937;">{{ $attempt->quiz->title }}</div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-graduation-cap me-2" style="color: #2B6ED4;"></i> Formation
                                </div>
                                <div class="col-md-7" style="color: #1F2937;">{{ $attempt->quiz->training->title }}</div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-star me-2" style="color: #2B6ED4;"></i> Score
                                </div>
                                <div class="col-md-7">
                                    <span class="badge rounded-pill px-3 py-2 fw-medium" style="background-color:  #4361ee; color: #FFFFFF;">
                                        <i class="fas {{ $attempt->passed ? 'fa-trophy' : 'fa-times-circle' }} me-1"></i>
                                        {{ $attempt->score }}/20
                                    </span>
                                </div>
                            </div>
                            <div class="row {{ $attempt->isCheated() ? 'mb-3' : '' }} align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-clock me-2" style="color: #2B6ED4;"></i> Durée
                                </div>
                                <div class="col-md-7" style="color: #1F2937;">{{ $attempt->started_at->diff($attempt->finished_at)->format('%H:%I:%S') }}</div>
                            </div>
                            @if($attempt->isCheated())
                            <div class="row align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-exclamation-triangle me-2" style="color: #92400e;"></i> Alertes
                                </div>
                                <div class="col-md-7">
                                    <span class="badge rounded-pill px-3 py-2 fw-medium" style="background-color: #FBBF2426; color: #92400e;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $attempt->tab_switches }} changements d'onglet
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header py-3 px-4 border-0" style="background-color: #E6EEFA;">
                    <h5 class="mb-0 fw-medium" style="color: #1A4A93;">
                        <i class="fas fa-list-alt me-2" style="color: #2B6ED4;"></i> Réponses détaillées
                    </h5>
                </div>
                <div class="card-body p-4 bg-white">
                    @php
                        // Group answers by question
                        $questionGroups = $attempt->userAnswers->groupBy('question_id');
                    @endphp

                    <div class="accordion" id="accordionQuestions">
                        @foreach($questionGroups as $questionId => $userAnswers)
                            @php
                                $question = $userAnswers->first()->question;
                                $correctAnswers = $question->correctAnswers;
                                $allCorrect = $userAnswers->every(function($answer) { return $answer->is_correct; });
                            @endphp

                            <div class="accordion-item mb-3 border-0 rounded-3 shadow-sm overflow-hidden">
                                <h2 class="accordion-header" id="heading{{ $questionId }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} py-3 px-4" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $questionId }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $questionId }}"
                                            style="background-color: {{ $allCorrect ? '#e6f0fb' : '#FEF2F2' }}; border-left: 4px solid {{ $allCorrect ? '#2B6ED4' : '#EF4444' }};">
                                        <div class="d-flex justify-content-between align-items-center w-100 pe-3">
                                            <div class="d-flex align-items-center">
                                                <span class="badge rounded-pill me-2 fw-medium" style="background-color: #2B6ED4; color: #FFFFFF;">{{ $loop->iteration }}</span>
                                                <span class="fw-semibold" style="color: #1F2937;">{{ $question->question_text }}</span>
                                            </div>
                                            <span class="badge rounded-pill px-3 py-2 fw-medium"
                                                  style="background-color: {{ $allCorrect ? '#2B6ED4' : '#EF4444' }}; color: #FFFFFF;">
                                                <i class="fas {{ $allCorrect ? 'fa-check' : 'fa-times' }} me-1"></i>
                                                {{ $allCorrect ? 'Correct' : 'Incorrect' }}
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $questionId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                     aria-labelledby="heading{{ $questionId }}" data-bs-parent="#accordionQuestions">
                                    <div class="accordion-body p-4" style="background-color: #FFFFFF;">
                                        <div class="list-group">
                                            @foreach($userAnswers as $userAnswer)

                                                <div class="list-group-item mb-2 p-3 border-0 rounded-3 shadow-sm"
                                                    style="background-color: {{ $userAnswer->is_correct ? '#f3f8fd' : '#FEF2F2' }};
                                                            border-left: 3px solid {{ $userAnswer->is_correct ? '#2B6ED4' : '#EF4444' }};">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="badge rounded-circle d-flex align-items-center justify-content-center"
                                                                style="width: 24px; height: 24px; background-color: {{ $userAnswer->is_correct ? '#2B6ED4' : '#EF4444' }}; color: #FFFFFF;">
                                                                <i class="fas {{ $userAnswer->is_correct ? 'fa-check' : 'fa-times' }}"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium" style="color: #1F2937; font-size: 0.95rem;">
                                                                {{ $userAnswer->answer->answer_text }}
                                                            </p>
                                                            <small class="text-muted" style="font-size: 0.75rem;">Réponse de l'étudiant</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($correctAnswers->isNotEmpty())
                                                @php

                                                    $studentAnswerIds = $userAnswers->pluck('answer_id')->toArray();
                                                @endphp

                                                @foreach($correctAnswers as $correctAnswer)
                                                    @if(!in_array($correctAnswer->id, $studentAnswerIds))

                                                    <div class="list-group-item mb-2 p-3 border-0 rounded-3 shadow-sm"
                                                        style="background-color: #eaf3fc;
                                                                border-left: 3px solid #2B6ED4;">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <div class="me-2">
                                                                <span class="badge rounded-circle d-flex align-items-center justify-content-center"
                                                                    style="width: 24px; height: 24px; background-color: #2B6ED4; color: #FFFFFF;">
                                                                    <i class="fas fa-check"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <p class="mb-0 fw-medium" style="color: #1F2937; font-size: 0.95rem;">
                                                                    {{ $correctAnswer->answer_text }}
                                                                </p>
                                                                <small class="text-muted" style="font-size: 0.75rem;">Bonne réponse non cochée</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}


    <div class="card ">


        <div class="card-header border-0 d-flex align-items-center py-4 px-4" style="background-color: #2B6ED4; color: #FFFFFF;">
            <button class="btn btn-light me-3 back-btn"  data-back-tab="evaluation">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h4 class="mb-0 fw-semibold mx-auto">
                <i class="fas fa-clipboard-check me-2"></i> Détails de la tentative
            </h4>
        </div>

        <div class="card-body p-4" style="background-color: #F8FAFC;">

            <div class="row g-4 mb-5">

                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header py-3 px-4 border-0" style="background-color: #E6EEFA;">
                            <h6 class="mb-0 fw-medium" style="color: #1A4A93;">
                                <i class="fas fa-user-graduate me-2" style="color: #2B6ED4;"></i> Informations étudiant
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-id-card me-2" style="color: #2B6ED4;"></i> Nom complet
                                </div>
                                <div class="col-md-7 fw-semibold" style="color: #1F2937;">{{ $attempt->user->name }}  {{ $attempt->user->lastname ?? '' }}</div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-envelope me-2" style="color: #2B6ED4;"></i> Email
                                </div>
                                <div class="col-md-7" style="color: #1F2937;">{{ $attempt->user->email }}</div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                        <div class="card-header py-3 px-4 border-0" style="background-color: #E6EEFA;">
                            <h6 class="mb-0 fw-medium" style="color: #1A4A93;">
                                <i class="fas fa-chart-bar me-2" style="color: #2B6ED4;"></i> Résultats
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-book me-2" style="color: #2B6ED4;"></i> Quiz
                                </div>
                                <div class="col-md-7 fw-semibold" style="color: #1F2937;">{{ $attempt->quiz->title }}</div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-graduation-cap me-2" style="color: #2B6ED4;"></i> Formation
                                </div>
                                <div class="col-md-7" style="color: #1F2937;">{{ $attempt->quiz->training->title }}</div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-star me-2" style="color: #2B6ED4;"></i> Score
                                </div>
                                <div class="col-md-7">
                                    <span class="badge rounded-pill px-3 py-2 fw-medium" style="background-color:  #4361ee; color: #FFFFFF;">
                                        <i class="fas {{ $attempt->passed ? 'fa-trophy' : 'fa-times-circle' }} me-1"></i>
                                        {{ $attempt->score }}/20
                                    </span>
                                </div>
                            </div>
                            <div class="row {{ $attempt->isCheated() ? 'mb-3' : '' }} align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-clock me-2" style="color: #2B6ED4;"></i> Durée
                                </div>
                                <div class="col-md-7" style="color: #1F2937;">{{ $attempt->started_at->diff($attempt->finished_at)->format('%H:%I:%S') }}</div>
                            </div>
                            @if($attempt->isCheated())
                            <div class="row align-items-center">
                                <div class="col-md-5 text-muted fw-medium">
                                    <i class="fas fa-exclamation-triangle me-2" style="color: #92400e;"></i> Alertes
                                </div>
                                <div class="col-md-7">
                                    <span class="badge rounded-pill px-3 py-2 fw-medium" style="background-color: #FBBF2426; color: #92400e;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        {{ $attempt->tab_switches }} changements d'onglet
                                    </span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header py-2 px-3 border-0" style="background-color: #E6EEFA;">
                    <h5 class="mb-0 fw-medium" style="color: #1A4A93; font-size: 1rem;">
                        <i class="fas fa-list-alt me-1" style="color: #2B6ED4;"></i> Réponses détaillées
                    </h5>
                </div>
                <div class="card-body p-3 bg-white">
                    @php
                        $questionGroups = $attempt->userAnswers->groupBy('question_id');
                    @endphp

                    <div class="accordion" id="accordionQuestions">
                        @foreach($questionGroups as $questionId => $userAnswers)
                            @php
                                $question = $userAnswers->first()->question;
                                $correctAnswers = $question->correctAnswers;
                                $allCorrect = $userAnswers->every(function($answer) { return $answer->is_correct; });
                            @endphp

                            <div class="accordion-item mb-2 border-0 rounded-3 shadow-sm overflow-hidden">
                                <h2 class="accordion-header" id="heading{{ $questionId }}">
                                    <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }} py-2 px-3" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $questionId }}"
                                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                                            aria-controls="collapse{{ $questionId }}"
                                            style="background-color: {{ $allCorrect ? '#e6f0fb' : '#FEF2F2' }}; border-left: 3px solid {{ $allCorrect ? '#2B6ED4' : '#EF4444' }};">
                                        <div class="d-flex justify-content-between align-items-center w-100 pe-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge rounded-pill me-1 fw-medium" style="background-color: #2B6ED4; color: #FFFFFF; font-size: 0.75rem;">{{ $loop->iteration }}</span>
                                                <span class="fw-semibold" style="color: #1F2937; font-size: 0.9rem;">{{ $question->question_text }}</span>
                                            </div>
                                            <span class="badge rounded-pill px-2 py-1 fw-medium"
                                                  style="background-color: {{ $allCorrect ? '#2B6ED4' : '#EF4444' }}; color: #FFFFFF; font-size: 0.75rem;">
                                                <i class="fas {{ $allCorrect ? 'fa-check' : 'fa-times' }} me-1"></i>
                                                {{ $allCorrect ? 'Correct' : 'Incorrect' }}
                                            </span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse{{ $questionId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                                     aria-labelledby="heading{{ $questionId }}" data-bs-parent="#accordionQuestions">
                                    <div class="accordion-body p-3" style="background-color: #FFFFFF;">
                                        <div class="list-group">
                                            @foreach($userAnswers as $userAnswer)
                                                <div class="list-group-item mb-1 p-2 border-0 rounded-3 shadow-sm"
                                                     style="background-color: {{ $userAnswer->is_correct ? '#f3f8fd' : '#FEF2F2' }};
                                                            border-left: 2px solid {{ $userAnswer->is_correct ? '#2B6ED4' : '#EF4444' }};">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="badge rounded-circle d-flex align-items-center justify-content-center"
                                                                  style="width: 18px; height: 18px; background-color: {{ $userAnswer->is_correct ? '#2B6ED4' : '#EF4444' }}; color: #FFFFFF; line-height: 18px; display: inline-flex;">
                                                                <i class="fas {{ $userAnswer->is_correct ? 'fa-check' : 'fa-times' }}" style="font-size: 0.75rem; margin: 0 auto; vertical-align: middle;"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium" style="color: #1F2937; font-size: 0.85rem;">
                                                                {{ $userAnswer->answer->answer_text }}
                                                            </p>
                                                            <small class="text-muted" style="font-size: 0.65rem;">Réponse de l'étudiant</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if($correctAnswers->isNotEmpty())
                                                @php
                                                    $studentAnswerIds = $userAnswers->pluck('answer_id')->toArray();
                                                @endphp
                                                @foreach($correctAnswers as $correctAnswer)
                                                    @if(!in_array($correctAnswer->id, $studentAnswerIds))
                                                        <div class="list-group-item mb-1 p-2 border-0 rounded-3 shadow-sm"
                                                             style="background-color: #eaf3fc; border-left: 2px solid #2B6ED4;">
                                                            <div class="d-flex align-items-center mb-1">
                                                                <div class="me-2"> <!-- Augmentation de la marge à droite -->
                                                                    <span class="badge rounded-circle d-flex align-items-center justify-content-center"
                                                                          style="width: 18px; height: 18px; background-color: #2B6ED4; color: #FFFFFF; line-height: 1;">
                                                                        <i class="fas fa-check" style="font-size: 0.75rem;"></i>
                                                                    </span>
                                                                </div>
                                                                <div>
                                                                    <p class="mb-0 fw-medium" style="color: #1F2937; font-size: 0.85rem;">
                                                                        {{ $correctAnswer->answer_text }}
                                                                    </p>
                                                                    <small class="text-muted" style="font-size: 0.65rem;">Bonne réponse non cochée</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .back-btn {
            background-color: #ffffff; /* Couleur par défaut */
            color: #000000; /* Couleur par défaut */
        }

        .back-btn:hover {
            background-color: #f8f9fa; /* Couleur de fond au survol */
            color: #3a3f63; /* Couleur de texte au survol */
        }
        </style>

