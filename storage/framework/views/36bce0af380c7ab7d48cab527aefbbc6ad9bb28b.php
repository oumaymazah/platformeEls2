<?php $__env->startSection('content'); ?>
<div class="container-fluid px-0">
    <!-- Alerts -->
    <?php if(session('error') || session('success')): ?>
        <div class="alert alert-<?php echo e(session('error') ? 'danger' : 'success'); ?> shadow-sm fade show d-flex align-items-center mx-3 mb-3" role="alert">
            <div class="me-3">
                <i class="fas fa-<?php echo e(session('error') ? 'exclamation-circle' : 'check-circle'); ?>"></i>
            </div>
            <div class="flex-grow-1">
                <?php echo e(session('error') ?? session('success')); ?>

            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Header Card -->
    <div class="card shadow-sm border-0 mb-4 mx-3">
        <div class="card-header bg-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white p-2 me-3">
                        <i class="fas fa-clipboard-list text-primary fa-lg"></i>
                    </div>
                    <h1 class="h3 mb-0 text-white">Gestion des Quiz</h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mx-2 mb-3">
        <!-- Total Quiz Card -->
        <div class="col-md-4 mb-3">
            <div class="card stat-card border-left-primary shadow-sm h-100">
                <div class="card-body py-2 px-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="fs-6 font-weight-bold text-primary text-uppercase mb-1">
                                Total Quiz</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($quizzes->total()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Published Quiz Card -->
        <div class="col-md-4 mb-3">
            <div class="card stat-card border-left-success shadow-sm h-100">
                <div class="card-body py-2 px-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="fs-6 font-weight-bold text-secondary text-uppercase mb-1">
                                Quiz Publiés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($quizzes->where('is_published', true)->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unpublished Quiz Card -->
        <div class="col-md-4 mb-3">
            <div class="card stat-card border-left-warning shadow-sm h-100">
                <div class="card-body py-2 px-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="fs-6 font-weight-bold text-secondary text-uppercase mb-1">
                                Quiz Non Publiés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($quizzes->where('is_published', false)->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye-slash fa-lg text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4 mx-3 overflow-visible">
        <div class="card-header bg-white py-2 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                <i class="fas fa-list me-2"></i>Liste des Quiz
            </h6>

            <div class="d-flex align-items-center gap-2">
                <!-- Filter Dropdown -->
                <div class="form-group mb-0 me-2">
                    <select id="status-filter" class="form-select form-select-sm" onchange="window.location = this.value;">
                        <option value="<?php echo e(route('admin.quizzes.index')); ?>" <?php echo e(request('status') == null ? 'selected' : ''); ?>>Tous les quiz</option>
                        <option value="<?php echo e(route('admin.quizzes.index', ['status' => 'active'])); ?>" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Quiz publiés</option>
                        <option value="<?php echo e(route('admin.quizzes.index', ['status' => 'inactive'])); ?>" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Quiz non publiés</option>
                    </select>
                </div>

                <!-- Create Quiz Button -->
                <a href="<?php echo e(route('admin.quizzes.create')); ?>" class="btn btn-primary btn-sm d-flex align-items-center">
                    <i class="fas fa-plus me-1"></i> Nouveau Quiz
                </a>
            </div>
        </div>

        <div class="card-body p-0 overflow-visible">
            <div class="table-responsive overflow-visible">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>

                            <th width="20%">Titre</th>
                            <th width="20%">Formation</th>
                            <th width="15%">Type</th>
                            <th class="text-center" width="15%">Questions</th>
                            <th class="text-center" width="15%">Statut</th>
                            <th class="text-end pe-3" width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $quizzes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $quiz): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>

                            <td class="fw-medium"><?php echo e($quiz->title); ?></td>
                            <td><?php echo e($quiz->training->title); ?></td>
                            <td>
                                <?php if($quiz->isPlacementTest()): ?>
                                    <span class="badge bg-secondary"><i class="fas fa-tasks me-1"></i>Test de niveau</span>
                                <?php else: ?>
                                    <span class="badge bg-primary"><i class="fas fa-check-circle me-1"></i>Quiz final</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark"><?php echo e($quiz->questions_count); ?></span>
                            </td>
                            <td class="text-center">
                                <?php if($quiz->is_published): ?>
                                    <span class="badge bg-success"><i class="fas fa-eye me-1"></i>Publié</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-eye-slash me-1"></i>Non Publié</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end pe-3 position-static">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            id="dropdownMenuButton-<?php echo e($quiz->id); ?>" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-<?php echo e($quiz->id); ?>">
                                        <li>
                                            <a class="dropdown-item" href="<?php echo e(route('admin.quizzes.show', $quiz->id)); ?>">
                                                <i class="fas fa-eye"></i> Voir détails
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger delete-btn" href="#"
                                               data-url="<?php echo e(route('admin.quizzes.destroy', $quiz->id)); ?>">
                                                <i class="fas fa-trash me-2"></i> Supprimer
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-clipboard-list fa-2x mb-2 text-muted"></i>
                                    <h6>Aucun quiz trouvé</h6>
                                    <p class="mb-2 small">Créez votre premier quiz en cliquant sur "Nouveau Quiz"</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($quizzes->hasPages()): ?>
        <div class="card-footer bg-white border-top-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <div class="mb-2 mb-md-0">
                    <p class="small text-muted mb-0">
                        Affichage de <span class="fw-semibold"><?php echo e($quizzes->firstItem()); ?></span>
                        à <span class="fw-semibold"><?php echo e($quizzes->lastItem()); ?></span>
                        sur <span class="fw-semibold"><?php echo e($quizzes->total()); ?></span> résultats
                    </p>
                </div>

                <div>
                    <ul class="pagination pagination-sm mb-0">
                        
                        <li class="page-item <?php echo e($quizzes->onFirstPage() ? 'disabled' : ''); ?>">
                            <a class="page-link" href="<?php echo e($quizzes->previousPageUrl()); ?>" aria-label="Précédent">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        
                        <?php $__currentLoopData = $quizzes->getUrlRange(1, $quizzes->lastPage()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="page-item <?php echo e($quizzes->currentPage() == $page ? 'active' : ''); ?>">
                                <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        
                        <li class="page-item <?php echo e(!$quizzes->hasMorePages() ? 'disabled' : ''); ?>">
                            <a class="page-link" href="<?php echo e($quizzes->nextPageUrl()); ?>" aria-label="Suivant">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="<?php echo e(asset('assets/js/datatables/jquery.dataTables.min.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuration des boutons de suppression
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const deleteUrl = this.getAttribute('data-url');

                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Cette action est irréversible!",
                    icon: 'warning',
                    iconColor: '#f8bb86',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#9e9e9e',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Non, annuler',
                    buttonsStyling: true,
                    customClass: {
                        confirmButton: 'swal-confirm-button-no-border',
                        cancelButton: 'swal-cancel-button-no-border',
                        popup: 'swal-custom-popup'
                    },
                    background: '#ffffff',
                    backdrop: 'rgba(0,0,0,0.4)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Créer un formulaire dynamique
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;

                        // Ajouter le token CSRF
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = document.querySelector('meta[name="csrf-token"]').content;
                        form.appendChild(csrf);

                        // Ajouter la méthode DELETE
                        const method = document.createElement('input');
                        method.type = 'hidden';
                        method.name = '_method';
                        method.value = 'DELETE';
                        form.appendChild(method);

                        // Soumettre le formulaire
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });

        // Solution avancée pour les dropdowns avec positionnement attaché au body
        document.querySelectorAll('.dropdown-toggle').forEach(button => {
            button.addEventListener('click', function() {
                setTimeout(() => {
                    const dropdown = this.nextElementSibling;
                    if (dropdown && dropdown.classList.contains('dropdown-menu')) {
                        // Récupérer les positions et dimensions du bouton
                        const buttonRect = this.getBoundingClientRect();

                        // Repositionner le dropdown directement sous le bouton
                        dropdown.style.position = 'fixed';
                        dropdown.style.top = (buttonRect.bottom + window.scrollY + 5) + 'px';
                        dropdown.style.right = (window.innerWidth - buttonRect.right) + 'px';
                        dropdown.style.left = 'auto';
                        dropdown.style.minWidth = '10rem';
                        dropdown.style.zIndex = '1060';
                    }
                }, 10);
            });
        });

        // S'assurer que les icônes sont correctement chargées
        if (typeof FontAwesome === 'undefined') {
            const faScript = document.createElement('script');
            faScript.src = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js';
            document.head.appendChild(faScript);
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('css'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<link rel="stylesheet" type="text/css" href="<?php echo e(asset('assets/css/datatables.css')); ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="<?php echo e(asset('assets/css/MonCss/quizzes/quizIndexPage.css')); ?>">

<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/admin/quizzes/index.blade.php ENDPATH**/ ?>