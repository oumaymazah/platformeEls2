<div class="container-fluid px-0">
    <div class="card rounded-0 border-0 shadow-sm">
        <div class="card-header bg-primary text-white py-3 rounded-0 mb-4">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-white p-2 me-3">
                         <i class="fas fa-calendar-check text-primary fa-lg"></i>
                </div>
                <h3 class="fw-bold mb-0">Gestion des Réservations</h3>
            </div>
         </div>

        

                    <!-- Nouvelle carte de filtrage avec espace -->
                    <div class="card-body pb-0">
                        <div class="card shadow-sm mb-3">


                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-filter"></i>
                                            </span>
                                            <select class="form-select filter-select" id="reservation-status-filter" aria-label="Filtrer par statut">
                                                <option value="">Tous les statuts</option>
                                                <option value="0" <?php echo e(request('status') == '0' ? 'selected' : ''); ?>>En attente</option>
                                                <option value="1" <?php echo e(request('status') == '1' ? 'selected' : ''); ?>>Confirmées</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="Rechercher par ID réservation,ou téléphone..."
                                                id="reservation-search-input" value="<?php echo e(request('search') ?? ''); ?>">
                                            
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <button class="btn btn-outline-secondary w-100" id="reset-reservation-filters">
                                            <i class="fas fa-undo me-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">



                        <div class="table-responsive m-0">
                            <table id="reservations-table" class="table table-borderless compact-table m-0">
                                <thead class="table-light">
                                    <tr>
                                        <th  width="20%" class="border-top-0 ">Code</th>
                                        <th class="border-top-0">Nom Complet</th>
                                        <th class="border-top-0">Téléphone</th>
                                        <th class="border-top-0">Statut</th>
                                        <th class="border-top-0">Date de paiement</th>
                                        <th class="border-top-0 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $studentsWithReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr data-reservation-id="<?php echo e($student['reservation_id']); ?>" >
                                        <td class="fw-bold"><?php echo e($student['reservation_id']); ?></td>
                                        <td><?php echo e($student['nom']); ?> <?php echo e($student['prenom']); ?></td>
                                        <td><?php echo e($student['telephone']); ?></td>
                                        <td>
                                            <span class="badge <?php echo e($student['status'] == 0 ? 'bg-danger' : 'bg-primary'); ?> px-2 py-1">
                                                <i class="fas <?php echo e($student['status'] == 0 ? 'fa-clock' : 'fa-check-circle'); ?> me-1"></i>
                                                <?php echo e($student['status_text']); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($student['payment_date']): ?>
                                                <?php echo e(\Carbon\Carbon::parse($student['payment_date'])->format('d/m/Y H:i')); ?>

                                            <?php else: ?>
                                            <span class="text-muted" style="margin-left: 70px"> - </span>

                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex gap-1 justify-content-center">
                                                <?php if($student['status'] == 0): ?>
                                                    <form method="POST" action="<?php echo e(route('reservations.updateStatus')); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="reservation_id" value="<?php echo e($student['reservation_id']); ?>">
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm py-1 px-2" title="Valider cette réservation">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                <?php else: ?>
                                                    <form method="POST" action="<?php echo e(route('reservations.updateStatus')); ?>">
                                                        <?php echo csrf_field(); ?>
                                                        <input type="hidden" name="reservation_id" value="<?php echo e($student['reservation_id']); ?>">
                                                        <input type="hidden" name="status" value="0">
                                                        <button type="submit" class="btn btn-sm py-1 px-2" style="background-color: #907b75; border-color: #907b75; color: white;" title="Annuler la validation">                                                <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>


                                                <div class="dropdown dropdown-user-actions">
                                                    <button class="btn btn-sm btn-light dropdown-toggle py-1 px-2" type="button"
                                                            id="dropdownMenuButton-<?php echo e($student['reservation_id']); ?>" data-bs-toggle="dropdown"
                                                            aria-expanded="false">
                                                            <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-<?php echo e($student['reservation_id']); ?>">
                                                        <li>
                                                            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#formationsModal<?php echo e($student['reservation_id']); ?>">
                                                                <i class="fas fa-book-open me-2"></i> Voir formations (<?php echo e(count($student['formations'])); ?>)
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="<?php echo e(route('reservations.updateStatus')); ?>" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette réservation ? Cette action est irréversible.')" class="d-inline">
                                                                <?php echo csrf_field(); ?>
                                                                <input type="hidden" name="reservation_id" value="<?php echo e($student['reservation_id']); ?>">
                                                                <button type="submit" class="dropdown-item text-danger">
                                                                    <i class="fas fa-trash-alt me-2"></i> Supprimer
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7" class="empty-state">
                                            <div class="empty-content">
                                                <i class="fas fa-search-minus"></i>
                                                <h3>Aucune Reservation trouvée</h3>
                                                <p>Modifiez vos critères de recherche ou essayez plus tard</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if($reservations->hasPages()): ?>
                            <div class="pagination-wrapper mt-4">
                                <div class="pagination-info text-muted small mb-2">
                                    <i class="fas fa-file-alt me-1"></i> Affichage de
                                    <span class="fw-bold"><?php echo e($reservations->firstItem()); ?></span>
                                    à <span class="fw-bold"><?php echo e($reservations->lastItem()); ?></span>
                                    sur <span class="fw-bold"><?php echo e($reservations->total()); ?></span> réservations
                                </div>

                                <div class="pagination-controls">
                                    <ul class="pagination custom-pagination justify-content-center">
                                        
                                        <li class="page-item <?php echo e($reservations->onFirstPage() ? 'disabled' : ''); ?>">
                                            <a class="page-link"
                                            href="<?php echo e($reservations->appends(request()->except('page'))->previousPageUrl()); ?>"
                                            aria-label="Précédent"
                                            <?php if(!$reservations->onFirstPage()): ?> onclick="return paginateReservations(event)" <?php endif; ?>>
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>

                                        
                                        <?php $__currentLoopData = $reservations->getUrlRange(max(1, $reservations->currentPage() - 2), min($reservations->lastPage(), $reservations->currentPage() + 2)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li class="page-item <?php echo e($reservations->currentPage() == $page ? 'active' : ''); ?>">
                                                <a class="page-link"
                                                href="<?php echo e($url); ?>"
                                                onclick="return paginateReservations(event)">
                                                    <?php echo e($page); ?>

                                                </a>
                                            </li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                        
                                        <li class="page-item <?php echo e(!$reservations->hasMorePages() ? 'disabled' : ''); ?>">
                                            <a class="page-link"
                                            href="<?php echo e($reservations->appends(request()->except('page'))->nextPageUrl()); ?>"
                                            aria-label="Suivant"
                                            <?php if($reservations->hasMorePages()): ?> onclick="return paginateReservations(event)" <?php endif; ?>>
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<!-- Modals for formations -->
<?php $__currentLoopData = $studentsWithReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="formationsModal<?php echo e($student['reservation_id']); ?>" tabindex="-1" aria-labelledby="formationsModalLabel<?php echo e($student['reservation_id']); ?>" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="formationsModalLabel<?php echo e($student['reservation_id']); ?>">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Formations réservées (ID: <?php echo e($student['reservation_id']); ?>)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if(count($student['formations']) > 0): ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold">Nombre total de formations :
                                <span class="badge bg-primary rounded-pill fs-7"><?php echo e(count($student['formations'])); ?></span>
                            </h6>
                            <h6 class="fw-bold">Date de réservation :
                                <span class="badge bg-secondary rounded-pill fs-7">
                                    <?php echo e(\Carbon\Carbon::parse($student['reservation_date'])->format('d/m/Y')); ?>

                                </span>
                            </h6>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Formation</th>
                                    <th class="text-end">Prix</th>
                                    <th class="text-end">Remise</th>
                                    <th class="text-end">Prix final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $totalOriginal = 0;
                                $totalDiscount = 0;
                                $totalFinal = 0;
                                $hasAnyDiscount = false;
                                ?>

                                <?php $__currentLoopData = $student['formations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $formation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    $originalPrice = $formation['price'];
                                    $discount = $formation['discount'] ?? 0;
                                    $discountAmount = 0;
                                    $finalPrice = $originalPrice;

                                    if ($discount > 0) {
                                        $hasAnyDiscount = true;
                                        $discountAmount = ($originalPrice * $discount) / 100;
                                        $finalPrice = $originalPrice - $discountAmount;
                                    }

                                    $totalOriginal += $originalPrice;
                                    $totalDiscount += $discountAmount;
                                    $totalFinal += $finalPrice;
                                    ?>

                                    <tr>
                                        <td style="color: black;">
                                            <i class="fas fa-certificate text-primary me-2"></i>
                                            <?php echo e($formation['title']); ?>

                                        </td>
                                        <td class="text-end" style="color: black;"><?php echo e(number_format($originalPrice, 2)); ?> Dt</td>
                                        <td class="text-end">
                                            <?php if($discount > 0): ?>
                                                <span class="text-danger">
                                                    <small><?php echo e($discount); ?>%</small>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end fw-bold" style="color: black;"><?php echo e(number_format($finalPrice, 2)); ?> Dt</td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <th style="color: black;">Total</th>
                                    <?php if($hasAnyDiscount): ?>
                                        <th class="text-end" style="color: black;"><?php echo e(number_format($totalOriginal, 2)); ?> Dt</th>
                                        <th class="text-end text-danger">
                                            <?php if($totalOriginal > 0): ?>
                                                <small><?php echo e(number_format(($totalDiscount / $totalOriginal) * 100, 2)); ?>%</small>
                                            <?php endif; ?>
                                        </th>
                                        <th class="text-end" style="color: black;"><?php echo e(number_format($totalFinal, 2)); ?> Dt</th>
                                    <?php else: ?>
                                        <th></th>
                                        <th></th>
                                        <th class="text-end" style="color: black;"><?php echo e(number_format($totalFinal, 2)); ?> Dt</th>
                                    <?php endif; ?>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="lead text-muted">Aucune formation dans cette réservation</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="modal-footer">
                <div class="text-end mt-2">
                    <span class="badge px-3 py-2 fs-6" style="background-color: #CFE2FF; color: #161616;">
                        Prix Total: <?php echo e(number_format($totalFinal ?? 0, 2)); ?> Dt
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


<style>
     /* Empty State */



    .dropdown-toggle::after {
        display: none;
    }
    .dropdown-menu {
        min-width: 10rem;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    .dropdown-item {
        padding: 0.35rem 1.5rem;
        font-size: 0.875rem;
    }
    .form-switch .form-check-input {
        width: 2.5em;
        height: 1.5em;
        cursor: pointer;
    }
    .btn-light {
        border: 1px solid #dee2e6;
        background-color: #f8f9fa;
    }
    .dropdown-toggle {
        padding: 0.25rem 0.5rem;
    }
    .badge {
        margin-right: 3px;
    }

    /* Modifications pour corriger le problème d'affichage du dropdown */
    .dropdown-user-actions {
        position: relative;
    }
    .dropdown-menu-end {
        right: 0;
        left: auto !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
        /* Pagination Styles */
    .empty-state {
            padding: 2.5rem 1rem;
            text-align: center;
        }

    .empty-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 400px;
        margin: 0 auto;
    }

    .empty-content i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-content h3 {
        margin: 0 0 0.5rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .empty-content p {
        color: var(--text-secondary);
        margin: 0;
        font-size: 0.95rem;
    }

    .pagination-wrapper {
        padding: 0.85rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-info {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .highlight {
        color: var(--primary);
        font-weight: 600;
    }

    .pagination-controls {
        display: flex;
        justify-content: flex-end;
    }

    .custom-pagination {
        display: flex;
        gap: 0.25rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .page-item {
        margin: 0;
    }

    .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: var(--radius);
        font-size: 0.9rem;
        transition: var(--transition);
        border: 1px solid var(--border);
        background-color: white;
        color: var(--text-secondary);
        text-decoration: none;
    }

    .page-item.active .page-link {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .page-item:not(.active) .page-link:hover {
        background-color: var(--primary-ultra-light);
        color: var(--primary);
    }

    .page-item.disabled .page-link {
        opacity: 0.5;
        pointer-events: none;
    }

</style>
<style>
    /* Reset des marges et paddings */
    .container-fluid.px-0 {
        padding-left: 0;
        padding-right: 0;
    }

    .card.rounded-0 {
        border-radius: 0 !important;
    }

    .card-body.p-0 {
        padding: 0 !important;
    }

    /* Styles pour le tableau */
    .compact-table {
        width: 100% !important;
        margin: 0 !important;
    }

    .compact-table th,
    .compact-table td {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        white-space: nowrap;
        vertical-align: middle;
    }

    .compact-table th {
        font-weight: 600;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .compact-table th.border-top-0 {
        border-top: none !important;
    }

    /* Largeurs des colonnes */
    .compact-table th:nth-child(1) { width: 4%; }
    .compact-table th:nth-child(2) { width: 15%; }
    .compact-table th:nth-child(3) { width: 10%; }
    .compact-table th:nth-child(4) { width: 16%; }
    .compact-table th:nth-child(5) { width: 8%; }
    .compact-table th:nth-child(6) { width: 12%; }
    .compact-table th:nth-child(7) { width: 18%; }

    .email-cell {
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Couleur de fond pour les lignes confirmées */
    .bg-light-blue,
.bg-light-blue td {
    background-color: #CFE2FF !important;
    border-color: #CFE2FF !important; /* Pour les bordures */
}

    /* Styles pour les petits écrans */
    @media (max-width: 992px) {
        .compact-table th:nth-child(3),
        .compact-table td:nth-child(3),
        .compact-table th:nth-child(4),
        .compact-table td:nth-child(4) {
            display: none;
        }

        .compact-table th:nth-child(1) { width: 8%; }
        .compact-table th:nth-child(2) { width: 30%; }
        .compact-table th:nth-child(5) { width: 15%; }
        .compact-table th:nth-child(6) { width: 20%; }
        .compact-table th:nth-child(7) { width: 27%; }
    }

    /* Styles pour les badges */
    .badge {
        font-weight: 20;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.5rem;
    }

    /* Styles pour les boutons */
    .btn-sm.py-1.px-2 {
        padding: 0.25rem 0.5rem;
    }

    /* Suppression des bordures du tableau */
    .table-borderless td,
    .table-borderless th {
        border: none;
    }

    /* Empêcher le défilement lors de l'ouverture du dropdown */
    body.dropdown-no-scroll {
        overflow: hidden !important;
    }


</style>

<?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/admin/apps/reservations/reservations-list.blade.php ENDPATH**/ ?>