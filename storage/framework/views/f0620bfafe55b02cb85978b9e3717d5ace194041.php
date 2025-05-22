<div class="container-fluid">
    <div class="page-header">
      <div class="row">
        <div class="col-lg-6">
          <?php echo e($breadcrumb_title ?? ''); ?>

          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('index')); ?>">Accueil</a></li>
              <?php echo e($slot ?? ''); ?>

          </ol>
        </div>
        <div class="col-lg-6">
          <!-- Bookmark Start-->
          
          <!-- Bookmark Ends-->
        </div>
      </div>
    </div>
</div>
<?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/components/breadcrumb.blade.php ENDPATH**/ ?>