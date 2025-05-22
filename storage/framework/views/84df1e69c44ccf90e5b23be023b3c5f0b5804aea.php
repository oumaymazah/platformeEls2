<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo e(asset('assets/css/MonCss/quizzes/quizShowPage.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Alert Messages -->
    <?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <!-- Titre du quiz en card header -->
    <div class="card quiz-header-card">
        <div class="card-body d-flex justify-content-between align-items-center">

            <a href="<?php echo e(route('admin.quizzes.index')); ?>" class="back-icon">
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
                        <?php if(!$quiz->is_published): ?>
                        <form action="<?php echo e(route('admin.quizzes.publish', $quiz->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="action-icon action-icon-publish" data-bs-toggle="tooltip" data-bs-placement="top" title="Publier">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                        <?php else: ?>
                        <form action="<?php echo e(route('admin.quizzes.toggle', $quiz->id)); ?>" method="POST" class="d-inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="action-icon action-icon-unpublish" data-bs-toggle="tooltip" data-bs-placement="top" title="Dépublier">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </form>
                        <?php endif; ?>
                        <form action="<?php echo e(route('admin.quizzes.destroy', $quiz->id)); ?>" method="POST" class="d-inline delete-quiz-form">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
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
                                <?php if($quiz->is_published): ?>
                                    <span class="badge status-published"><i class="fas fa-check-circle me-1"></i> Publié</span>
                                <?php else: ?>
                                    <span class="badge status-draft"><i class="fas fa-clock me-1"></i> Non publié</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Titre de Quiz</th>
                            <td><?php echo e($quiz->title); ?></td>
                        </tr>
                        <tr>
                            <th>Formation associée</th>
                            <td><?php echo e($quiz->training->title); ?></td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>
                                <?php if($quiz->isPlacementTest()): ?>
                                    <span class="badge badge-placement">Test de niveau</span>
                                <?php else: ?>
                                    <span class="badge badge-final">Quiz final</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Durée</th>
                            <td><?php echo e($quiz->duration); ?> minutes</td>
                        </tr>
                        <?php if($quiz->isFinalQuiz()): ?>
                        <tr>
                            <th>Score de passage</th>
                            <td><?php echo e($quiz->passing_score); ?>/20</td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Nombre de questions</th>
                            <td><?php echo e($quiz->questions->count()); ?></td>
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
                    <?php if($quiz->is_published && $quiz->attempts->count() > 0): ?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="quiz-stats-card" style="background-color: rgba(43, 110, 212, 0.1);">
                                <i class="fas fa-users text-primary"></i>
                                <h3><?php echo e($quiz->attempts->count()); ?></h3>
                                <p>Tentatives</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="quiz-stats-card" style="background-color: rgba(52, 195, 143, 0.1);">
                                <i class="fas fa-chart-pie text-success"></i>
                                <h3><?php echo e(number_format(($quiz->attempts->where('passed', true)->count() / $quiz->attempts->count()) * 100, 1)); ?>%</h3>
                                <p>Taux de réussite</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="quiz-stats-card" style="background-color: rgba(100, 116, 139, 0.1);">
                                <i class="fas fa-star text-warning"></i>
                                <h3><?php echo e(number_format($quiz->attempts->avg('score'), 1)); ?></h3>
                                <p>Score moyen / 20</p>
                            </div>
                        </div>
                    </div>
                    <?php elseif($quiz->is_published): ?>
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h4>Aucune statistique disponible</h4>
                        <p>Ce quiz n'a pas encore été tenté par des apprenants.</p>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-exclamation-circle text-warning"></i>
                        <h4>Quiz non publié</h4>
                        <p>Publiez ce quiz pour permettre aux apprenants de le passer et voir les statistiques.</p>
                    </div>
                    <?php endif; ?>
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
            <?php if($quiz->questions->isEmpty()): ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-question"></i>
                    <h4>Aucune question ajoutée</h4>
                    <p>Commencez par ajouter des questions à ce quiz pour que les étudiants puissent le passer.</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                        <i class="fas fa-plus me-2"></i> Ajouter une première question
                    </button>
                </div>
            <?php else: ?>
                <div class="two-panel-layout">
                    <!-- Questions Sidebar (maintenant à gauche) -->
                    <div class="questions-sidebar">
                        <div class="card mb-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Questions (<?php echo e($quiz->questions->count()); ?>)</h5>
                            </div>
                            <div class="card-body">
                                <?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="question-card" id="question-card-<?php echo e($index); ?>">
                                    <div class="question-header" onclick="showQuestionDetails(<?php echo e($index); ?>)">
                                        <div>
                                            <h6>Question <?php echo e($index + 1); ?></h6>
                                            <small class="text-muted"><?php echo e(Str::limit($question->question_text, 40)); ?></small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge-points me-3"><?php echo e($question->points); ?> pts</span>
                                            <a href="#" class="delete-question-btn"
                                               data-delete-url="<?php echo e(route('admin.questions.destroy', $question->id)); ?>"
                                               data-csrf="<?php echo e(csrf_token()); ?>" onclick="event.stopPropagation();">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Templates cachés pour les détails de questions -->
<?php $__currentLoopData = $quiz->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<template id="question-details-<?php echo e($index); ?>">
    <div class="question-details">
        <h5>Question <?php echo e($index + 1); ?></h5>
        <div class="mb-4">
            <h6 class="text-secondary"><strong>Énoncé</strong></h6>
            <p><?php echo e($question->question_text); ?></p>
        </div>

        <div>
            <h6 class="text-secondary"><strong>Réponses possibles</strong></h6>
            <div class="answer-list">
                <?php $__currentLoopData = $question->answers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $answer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="answer-item <?php echo e($answer->is_correct ? 'correct' : ''); ?>">
                    <div><?php echo e($answer->answer_text); ?></div>
                    <?php if($answer->is_correct): ?>
                    <span class="correct-badge">Correcte</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</template>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Modal pour ajouter une question (amélioré) -->
<div class="modal fade" id="addQuestionModal" tabindex="-1" role="dialog" aria-labelledby="addQuestionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.questions.store')); ?>" method="POST" class="needs-validation" novalidate id="question-form">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="quiz_id" value="<?php echo e($quiz->id); ?>" id="hidden_quiz_id">

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

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo e(asset('assets/js/MonJs/questions/question-create.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/questions/showpage.js')); ?>"></script>

<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/admin/quizzes/show.blade.php ENDPATH**/ ?>