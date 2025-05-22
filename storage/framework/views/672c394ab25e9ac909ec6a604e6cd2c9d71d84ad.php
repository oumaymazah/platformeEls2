


<!-- Script critique à placer le plus haut possible dans le head -->
 <script>
(function() {
    // Récupérer le compteur immédiatement
    var cartCount = localStorage.getItem('cartCount');
    cartCount = cartCount ? parseInt(cartCount) : 0;
    
    // Injecter directement dans le document pour éviter le flash de "0"
    if (cartCount > 0) {
        document.write(`
            <style>
                #fixed-cart-badge {
                    display: flex !important;
                    content: "${cartCount}";
                }
               
            </style>
        `);
    }
})();
</script> 

<div class="page-main-header">
  <div class="main-header-right row m-0">
    <div class="main-header-left">
      <div class="logo-wrapper"><a href="<?php echo e(route('index')); ?>"><img class="img-fluid" src="<?php echo e(asset('assets/images/logo/logo.png')); ?>" alt=""></a></div>
      <div class="dark-logo-wrapper"><a href="<?php echo e(route('index')); ?>"><img class="img-fluid" src="<?php echo e(asset('assets/images/logo/dark-logo.png')); ?>" alt=""></a></div>
      <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle"></i></div>
    </div>
    
    <div class="nav-right col pull-right right-menu p-0">
      <ul class="nav-menus">
        
        <li>
          <div class="mode"><i class="fa fa-moon-o"></i></div>
        </li>

      <?php if(auth()->user()->hasRole('etudiant')): ?>
        <li>
          <a href="/mes-reservations" title="Mes réservations">
            <div class="position-relative">
              <i data-feather="calendar" class="feather-icon"></i>
              <span id="reservation-indicator" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="display: none; font-size: 0.5rem; transform: translate(-50%, -50%);">
                <span class="visually-hidden">réservations actives</span>
              </span>
            </div>
          </a>
        </li>
        <?php endif; ?>
        
        
      

         


<?php if(auth()->user()->hasRole('etudiant')): ?>
<li>
  <a href="<?php echo e(route('panier.index')); ?>">
    <div class="cart-container" style="position: relative;">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
      </svg>
      <!-- Badge avec état initial basé sur localStorage -->
      <span id="fixed-cart-badge" class="custom-violet-badge" 
            style="position: absolute; top: -8px; right: -8px; background-color: #2563EB; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 12px; display: flex; align-items: center; justify-content: center; font-weight: bold; z-index: 10; visibility: hidden; opacity: 0;"></span>
    </div>
  </a>
</li>
<?php endif; ?>

<!-- Script inline pour initialisation immédiate -->
<script>
(function() {
  const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
  const badge = document.getElementById('fixed-cart-badge');
  if (badge) {
    badge.textContent = cartCount.toString();
    badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
    badge.style.opacity = cartCount > 0 ? '1' : '0';
  }
})();
</script>

<!-- Styles inline pour éviter FOUC -->
<style>
.cart-badge, .custom-violet-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #2563EB;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    z-index: 10;
}
</style>

<!-- Chargement du script panier -->
<script src="<?php echo e(asset('assets/js/MonJs/formations/panier.js')); ?>"></script>

        <li class="onhover-dropdown p-0">
          <a class="btn btn-primary-light" href="<?php echo e(route('logout')); ?>"
             onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">
            <?php echo e(__('Logout')); ?>

          </a>
          <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="d-none">
            <?php echo csrf_field(); ?>
          </form>
        </li>
      </ul>
    </div>
    <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
  </div>
</div>

<!-- Placez ce script dans la section head de votre document -->


<?php $__env->startPush('styles'); ?>

<?php $__env->stopPush(); ?><?php /**PATH C:\Users\msi\Desktop\Centre_Formation-main\resources\views/layouts/admin/partials/header.blade.php ENDPATH**/ ?>