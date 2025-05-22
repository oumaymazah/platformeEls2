<?php $__env->startSection('title', $training->title . ' - Détails'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">

</div>
<div class="container-fluid">
    <div class="row">
        <!-- Information de la formation (côté gauche) -->
        <div class="col-lg-8 content-area mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><strong><?php echo e($training->title); ?></strong></h5>

                    </div>
                </div>

                <div class="card-body" id="lesson-content-area">
                    <!-- Contenu par défaut qui sera remplacé par le contenu de la leçon -->
                    <div class="row">
                        <div id="blog-container" class="card">

                        </div>
                        
                        <div class="col-md-5">


                            <p><strong>Enseignant:</strong> <?php echo e($training->user->name); ?> <?php echo e($training->user->lastname); ?></p>
                            <p><strong>Type:</strong> <?php echo e($training->type == 'payante' ? 'Payante' : 'Gratuite'); ?></p>
                            <p><strong>Prix:</strong>
                                <?php if($training->type == 'payante'): ?>
                                    <?php if($training->discount > 0): ?>
                                        <span class="text-primary"><?php echo e(number_format($training->final_price, 2)); ?> Dt</span>
                                        <del class="text-muted"><?php echo e(number_format($training->price, 2)); ?> Dt</del>
                                    <?php else: ?>
                                        <span class="text-primary"><?php echo e(number_format($training->price, 2)); ?> Dt</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-success">Gratuite</span>
                                <?php endif; ?>
                            </p>
                            <p><strong>Durée:</strong> <?php echo e($training->formatted_duration); ?></p>
                            <p><strong>Places disponibles:</strong> <?php echo e($training->remaining_seats); ?> / <?php echo e($training->total_seats); ?></p>
                            <div class="d-flex align-items-center mb-2">
                                <div class="rating-stars me-2">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <?php if($i <= round($averageRating)): ?>
                                            <i class="fa fa-star text-warning"></i>
                                        <?php else: ?>
                                            <i class="fa fa-star-o text-muted"></i>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                </div>
                                <span><?php echo e(number_format($averageRating, 1)); ?> (<?php echo e($totalFeedbacks); ?> avis)</span>
                            </div>
                        </div>

                    </div>
                    <div class="mt-4">
                        <h5>Description</h5>
                        <p><?php echo e(strip_tags($training->description)); ?></p>
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
                    <?php $__currentLoopData = $training->quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($quiz->type == 'placement'): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-quiz-<?php echo e($quiz->id); ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-quiz-<?php echo e($quiz->id); ?>" aria-expanded="false">
                                        <i class="icon-help-circle me-2"></i> Test de niveau
                                    </button>
                                </h2>
                                <div id="collapse-quiz-<?php echo e($quiz->id); ?>" class="accordion-collapse collapse" aria-labelledby="heading-quiz-<?php echo e($quiz->id); ?>">
                                    <div class="accordion-body">
                                        <div class="lesson-item" data-quiz-id="<?php echo e($quiz->id); ?>">
                                            <a href="javascript:void(0)" class="quiz-link d-flex align-items-center">
                                                <i class="icon-help me-2"></i>
                                                <span><?php echo e($quiz->title); ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <!-- Cours -->
                    <?php $__currentLoopData = $training->courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-course-<?php echo e($course->id); ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-course-<?php echo e($course->id); ?>" aria-expanded="false">
                                    <i class="icon-book me-2"></i> <?php echo e($course->title); ?>

                                </button>
                            </h2>
                            <div id="collapse-course-<?php echo e($course->id); ?>" class="accordion-collapse collapse" aria-labelledby="heading-course-<?php echo e($course->id); ?>">
                                <div class="accordion-body p-0">
                                    <!-- Chapitres -->
                                    <?php $__currentLoopData = $course->chapters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chapter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="accordion-item nested-accordion">
                                            <h2 class="accordion-header" id="heading-chapter-<?php echo e($chapter->id); ?>">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-chapter-<?php echo e($chapter->id); ?>" aria-expanded="false">
                                                    <i class="icon-folder me-2"></i> <?php echo e($chapter->title); ?>

                                                </button>
                                            </h2>
                                            <div id="collapse-chapter-<?php echo e($chapter->id); ?>" class="accordion-collapse collapse" aria-labelledby="heading-chapter-<?php echo e($chapter->id); ?>">
                                                <div class="accordion-body">
                                                    <!-- Leçons -->
                                                    <?php $__currentLoopData = $chapter->lessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="lesson-item" data-lesson-id="<?php echo e($lesson->id); ?>">
                                                            <a href="javascript:void(0)" class="lesson-link d-flex align-items-center">
                                                                <i class="icon-file-text me-2"></i>
                                                                <span><?php echo e($lesson->title); ?></span>
                                                                <span class="ms-auto text-muted small"><?php echo e($lesson->duration); ?></span>
                                                            </a>
                                                        </div>
                                                        
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <!-- Quiz final si présent -->
                    <?php $__currentLoopData = $training->quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($quiz->type == 'final'): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading-quiz-<?php echo e($quiz->id); ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-quiz-<?php echo e($quiz->id); ?>" aria-expanded="false">
                                        <i class="icon-award me-2"></i> Quiz final
                                    </button>
                                </h2>
                                <div id="collapse-quiz-<?php echo e($quiz->id); ?>" class="accordion-collapse collapse" aria-labelledby="heading-quiz-<?php echo e($quiz->id); ?>">
                                    
                                    <div class="lesson-item" data-quiz-id="<?php echo e($quiz->id); ?>">
                                        <a href="javascript:void(0)" class="quiz-link d-flex align-items-center">
                                            <i class="icon-help me-2"></i>
                                            <span><?php echo e($quiz->title); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="route-lesson-content" data-url="<?php echo e(route('training.lesson.content')); ?>" style="display:none;"></div>
<div id="route-quiz-content" data-url="<?php echo e(route('training.quiz.content')); ?>" style="display:none;"></div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script src="<?php echo e(asset('assets/ajax/formation_detail.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('styles'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/admin/apps/formation/detail.blade.php ENDPATH**/ ?>