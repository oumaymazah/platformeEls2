<?php $__env->startSection('title'); ?>
    Liste des Formations
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>


<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<!-- Section pour afficher les quiz publiés -->
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Quiz associés</h4>
        </div>
        <div class="card-body">
            <?php if($formation->quizzes->count() > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Titre</th>

                                <th>Durée (minutes)</th>
                                <th>Score minimum</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $formation->quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($quiz->title); ?></td>

                                    <td><?php echo e($quiz->duration); ?></td>
                                    <td><?php echo e($quiz->isFinalQuiz() ? $quiz->passing_score . '/20' :  'le score depend des nombre des reponses correctes '); ?></td>
                                    <td>

                                        <?php if(auth()->user()->hasRole('etudiant')): ?>
                                        <form method="POST" action="<?php echo e(route('quizzes.start', $quiz->id)); ?>" style="display: inline;">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="fas fa-play"></i> Démarrer
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    Aucun quiz publié n'est disponible pour cette formation.
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/admin/apps/formation/formationshow.blade.php ENDPATH**/ ?>