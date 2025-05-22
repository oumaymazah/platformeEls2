 





 
 <?php $__env->startPush('css'); ?>
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
 <link rel="stylesheet" href="<?php echo e(asset('assets/css/MonCss/quizzes/createQuiz.css')); ?>">
 <?php $__env->stopPush(); ?>

 <?php $__env->startSection('content'); ?>

    <div class="container mb-5">
         <!-- Alertes -->

         <?php if(session('error')): ?>
            <div class="alert alert-danger">
                <?php echo e(session('error')); ?>

            </div>
        <?php endif; ?>



     
     <?php if(session('fileValidationErrors') && is_array(session('fileValidationErrors')) && count(session('fileValidationErrors')) > 0): ?>
         <div class="alert alert-warning">
             <strong>Erreurs détectées dans le fichier importé :</strong>
             <ul>
                 <?php $__currentLoopData = session('fileValidationErrors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fileError): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                     <li><?php echo e($fileError); ?></li>
                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             </ul>
             <small>Veuillez corriger votre fichier et réessayer.</small>
         </div>
     <?php endif; ?>

     
     <?php if(session('success')): ?>
         <div class="alert alert-success">
             <?php echo e(session('success')); ?>

         </div>
     <?php endif; ?>

        </div>

         <div class="card">

             <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-white p-2 me-3">
                                <i class="fas fa-graduation-cap fa-lg text-primary"></i>
                            </div>
                            <h3 class="h3 mb-0 text-white">Création d'un nouveau Quiz</h3>
                        </div>
                    </div>
                </div>
            </div>

             <div class="card-body">
                 <div class="alert alert-info d-flex align-items-start mb-4">
                     <i class="fas fa-info-circle me-3 mt-1"></i>
                     <div>
                         <strong>Information importante :</strong> Les nouveaux quiz sont créés comme "non publiés" par défaut et ne seront pas visibles par les étudiants.
                         Vous pourrez les publier après vérification depuis la liste des quiz.
                     </div>
                 </div>

                 <form action="<?php echo e(route('admin.quizzes.store')); ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                     <?php echo csrf_field(); ?>

                     <div class="form-section">
                         <h5><i class="fas fa-info-circle me-2"></i>Informations générales</h5>

                         <div class="mb-4 form-group">
                             <label for="training_id" class="form-label">Formation associée</label>
                             <select name="training_id" id="training_id" class="form-select <?php $__errorArgs = ['training_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                 <option value="">Sélectionner une formation</option>
                                 <?php $__currentLoopData = $trainings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $training): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                     <option value="<?php echo e($training->id); ?>" <?php echo e(old('training_id') == $training->id ? 'selected' : ''); ?>>
                                         <?php echo e($training->title); ?>

                                     </option>
                                 <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                             </select>
                            <div class="invalid-feedback js-error" style="display: none;">
                                Veuillez sélectionner une formation.
                            </div>

                            <!-- Message d'erreur Laravel (affiché côté serveur) -->
                            <?php $__errorArgs = ['training_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback laravel-error" style="display: block;"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                         </div>

                         <div class="mb-4 form-group">
                            <label for="title" class="form-label">Titre du Quiz</label>
                            <input type="text" name="title" id="title" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('title')); ?>" required>
                            <div class="invalid-feedback js-error">Veuillez entrer un titre valide.</div>
	                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
	                            <div class="invalid-feedback laravel-error" style="display: block;"><?php echo e($message); ?></div>
	                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                         </div>
                     </div>

                     <div class="form-section">
                         <h5><i class="fas fa-sliders-h me-2"></i>Paramètres du Quiz</h5>

                         <div class="mb-4 form-group">
                             <label class="form-label">Type de Quiz</label>
                             <div class="quiz-type-selector  <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                 <div class="quiz-type-option" data-type="final">
                                     <input type="radio" name="type" id="type_final" value="final" <?php echo e(old('type') == 'final' ? 'checked' : ''); ?>>
                                     <i class="fas fa-trophy"></i>
                                     <h6>Quiz Final</h6>
                                     <p>Évaluation finale pour tester la compréhension complète du contenu.</p>
                                 </div>
                                 <div class="quiz-type-option" data-type="placement">
                                    <input type="radio" name="type" id="type_placement"  value="placement" <?php echo e(old('type') == 'placement' ? 'checked' : ''); ?>>

                                     <i class="fas fa-language"></i>
                                     <h6>Test de Niveau</h6>
                                     <p>Test de placement pour évaluer le niveau initial (requiert 90 questions).</p>
                                 </div>
                                 <div class="invalid-feedback js-error" style="display: none;">
                                    Veuillez sélectionner un type de quiz.
                                </div>

                                <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback laravel-error" style="display: block;"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                             </div>

                        </div>

                         <div class="row">
                             <div class="col-md-6 mb-4">
                                 <label for="duration" class="form-label">Durée (minutes)</label>
                                 <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock"></i></span>
                                    <input type="number" name="duration" id="duration" class="form-control <?php $__errorArgs = ['duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" min="1" value="<?php echo e(old('duration')); ?>" required>
                                    <div class="invalid-feedback js-error">Veuillez entrer une durée valide.</div>
                                    <?php $__errorArgs = ['duration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback laravel-error" style="display: block;"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                 </div>
                             </div>

                             <div class="col-md-6 mb-4" id="passing-score-group" style="display: none;">
                                <label for="passing_score" class="form-label">Score minimum pour réussir</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-percent"></i></span>
                                    <input type="number" name="passing_score" id="passing_score" class="form-control <?php $__errorArgs = ['passing_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('passing_score')); ?>" min="1">
                                    <div class="invalid-feedback js-error">Veuillez entrer un titre valide.</div>
                                    <?php $__errorArgs = ['passing_score'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback laravel-error" style="display: block;"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                <small class="text-muted">Uniquement pour les quiz finaux</small>
                             </div>
                         </div>
                     </div>

                     <div class="form-section">
                         <h5><i class="fas fa-file-import me-2"></i>Importation des questions</h5>

                         <div class="file-upload mb-4 form-group">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Glissez-déposez votre fichier ici</p>
                            <small>ou cliquez pour sélectionner un fichier (CSV, Excel, JSON)</small>
                            <input type="file" name="quiz_file" id="quiz_file"  required>

                         </div>

                         <div class="templates-section">
                             <h6><i class="fas fa-download me-2"></i>Télécharger les modèles</h6>
                             <div class="d-flex flex-wrap">
                                 <a href="<?php echo e(route('admin.quizzes.download-template', ['type' => 'csv'])); ?>" class="template-btn">
                                     <i class="fas fa-file-csv"></i> Modèle CSV
                                 </a>
                                 <a href="<?php echo e(route('admin.quizzes.download-template', ['type' => 'excel'])); ?>" class="template-btn">
                                     <i class="fas fa-file-excel"></i> Modèle Excel
                                 </a>
                                 <a href="<?php echo e(route('admin.quizzes.download-template', ['type' => 'json'])); ?>" class="template-btn">
                                     <i class="fas fa-file-code"></i> Modèle JSON
                                 </a>
                             </div>
                         </div>
                     </div>

                     <div class="d-flex justify-content-end gap-3 mt-4">
                        <button type="submit" class="btn btn-primary submit-btn">
                            <i class="fas fa-plus-circle me-2"></i>Créer le Quiz
                        </button>
                        <a href="<?php echo e(route('admin.quizzes.index')); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times-circle me-2"></i>Annuler
                        </a>

                    </div>
                 </form>
             </div>
         </div>
    </div>
<?php $__env->startPush('scripts'); ?>

<script src="<?php echo e(asset('assets/js/form-validation/form_validation2.js')); ?>"></script>
<script>
    // Gestion des types de quiz
    document.querySelectorAll('.quiz-type-option').forEach(option => {
        option.addEventListener('click', function() {
            const type = this.dataset.type;

            // Retirer la classe active de toutes les options
            document.querySelectorAll('.quiz-type-option').forEach(el => {
                el.classList.remove('active');
            });

            // Ajouter la classe active à l'option sélectionnée
            this.classList.add('active');

            // Sélectionner le radio button correspondant
            document.getElementById('type_' + type).checked = true;

            // Afficher ou masquer le champ de score minimum
            const passingScoreGroup = document.getElementById('passing-score-group');
            if (type === 'final') {
                passingScoreGroup.style.display = 'block';
                document.getElementById('passing_score').setAttribute('required', '');
            } else {
                passingScoreGroup.style.display = 'none';
                document.getElementById('passing_score').removeAttribute('required');
            }
        });
    });

    // Initialiser l'affichage du type de quiz selon la valeur choisie auparavant
    window.addEventListener('DOMContentLoaded', () => {
        const typeValue = document.querySelector('input[name="type"]:checked')?.value;
        if (typeValue) {
            document.querySelector(`.quiz-type-option[data-type="${typeValue}"]`).click();
        }

        // Afficher le nom du fichier sélectionné
        document.getElementById('quiz_file').addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Aucun fichier sélectionné';
            const fileUpload = this.closest('.file-upload');

            const fileInfo = document.createElement('div');
            fileInfo.className = 'mt-3 text-center';
            fileInfo.innerHTML = `<i class="fas fa-file me-2"></i>${fileName}`;

            const existingInfo = fileUpload.querySelector('.mt-3');
            if (existingInfo) {
                existingInfo.remove();
            }

            fileUpload.appendChild(fileInfo);
        });
    });
</script>
<?php $__env->stopPush(); ?>

 <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/admin/quizzes/create.blade.php ENDPATH**/ ?>