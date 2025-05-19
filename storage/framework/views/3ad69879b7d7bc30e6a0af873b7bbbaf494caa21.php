

<div class="modal fade" id="formation-modal-<?php echo e($formation->id); ?>">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e($formation->title); ?></h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="product-box row">
                    <div class="product-img col-lg-6">
                        <img class="img-fluid" src="<?php echo e(asset('storage/' . $formation->image)); ?>" alt="<?php echo e($formation->title); ?>" />
                    </div>
                    <div class="product-details col-lg-6 text-start">
                        
                            <h4><?php echo e($formation->title); ?></h4>
                        </a>
                        <div class="product-price">
                            <?php if($formation->type == 'payante'): ?>
                                <?php if($formation->discount > 0): ?>
                                <?php echo e(number_format($formation->final_price, 2)); ?> Dt
                                <del><?php echo e(number_format($formation->price, 2)); ?> Dt</del>
                                <?php else: ?>
                                <?php echo e(number_format($formation->price, 2)); ?> Dt
                                <?php endif; ?>
                            <?php else: ?>
                                 
                            <?php endif; ?>
                        </div>
                        <div class="product-view">
                            <p class="mb-0"><?php echo e($formation->description); ?></p>
                            <div class="mt-3">
                                <p><strong>Places:</strong> <?php echo e($formation->total_seats); ?></p>
                                <p><strong>Durée:</strong> <?php echo e($formation->duration); ?></p>
                                <p><strong>Date début:</strong> <?php echo e(\Carbon\Carbon::parse($formation->start_date)->format('d/m/Y')); ?></p>
                                <p><strong>Date fin:</strong> <?php echo e(\Carbon\Carbon::parse($formation->end_date)->format('d/m/Y')); ?></p>
                                <p><strong>Nombre de cours:</strong> <?php echo e($formation->courses->count()); ?></p>
                            </div>
                        </div>
                        <div class="addcart-btn">
                            <?php
                                $isComplete = $formation->remaining_seats == 0 && $formation->total_seats > 0;
                                $inCart = in_array($formation->id, session('cart_formations', []));
                            ?>
                            <a class="btn <?php echo e($inCart ? 'btn-primary' : ($isComplete ? 'btn-secondary disabled' : 'btn-primary')); ?>" 
                               href="/panier" 
                               <?php echo e($isComplete && !$inCart ? 'disabled' : ''); ?>

                               <?php echo e($inCart ? 'data-in-cart="true"' : ''); ?>>
                                <?php echo e($inCart ? 'Accéder au panier' : ($isComplete ? 'FORMATION COMPLETE' : 'Ajouter au panier')); ?>

                            </a>
                            <a class="btn btn-primary" href="<?php echo e(route('formationshow', $formation->id)); ?>">Voir détails</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="<?php echo e(asset('assets/js/MonJs/cart.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/toast/toast.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/feedback.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/reservation.js')); ?>"></script>
<script src="<?php echo e(asset('assets/js/MonJs/formations/formation-button-layouts.js')); ?>"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<script>
    window.formationsData = <?php echo $formations->map(function ($formation) {
        return [
            'id' => $formation->id,
            'total_seats' => $formation->total_seats,
            'remaining_seats' => $formation->remaining_seats,
            'is_complete' => $formation->remaining_seats == 0 && $formation->total_seats > 0,
        ];
    })->keyBy('id')->toJson(); ?>;
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/admin/apps/formation/formation-modal.blade.php ENDPATH**/ ?>