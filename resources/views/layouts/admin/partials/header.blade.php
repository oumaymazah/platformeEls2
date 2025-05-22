
{{-- <script>
(function() {
    function updateFixedBadge() {
        const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
        const badge = document.getElementById('fixed-cart-badge');
        if (badge) {
            badge.textContent = cartCount.toString();
            badge.style.visibility = cartCount > 0 ? 'visible' : 'hidden';
            badge.style.opacity = cartCount > 0 ? '1' : '0';
            badge.style.display = cartCount > 0 ? 'flex' : 'none';
        }
    }

    // Mise à jour initiale
    updateFixedBadge();

    // Écouter les changements dans localStorage
    window.addEventListener('storage', function(e) {
        if (e.key === 'cartCount') {
            updateFixedBadge();
        }
    });

    // Écouter les changements dynamiques (par exemple, via setItem)
    const originalSetItem = localStorage.setItem;
    localStorage.setItem = function(key, value) {
        originalSetItem.apply(this, arguments);
        if (key === 'cartCount') {
            updateFixedBadge();
        }
    };
})();
</script>  --}}

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
      <div class="logo-wrapper"><a href="{{ route('index') }}"><img class="img-fluid" src="{{asset('assets/images/logo/logo.png')}}" alt=""></a></div>
      <div class="dark-logo-wrapper"><a href="{{ route('index') }}"><img class="img-fluid" src="{{asset('assets/images/logo/dark-logo.png')}}" alt=""></a></div>
      <div class="toggle-sidebar"><i class="status_toggle middle" data-feather="align-center" id="sidebar-toggle"></i></div>
    </div>
    
    <div class="nav-right col pull-right right-menu p-0">
      <ul class="nav-menus">
        {{-- <li><a class="text-dark" href="#!" onclick="javascript:toggleFullScreen()"><i data-feather="maximize"></i></a></li>
        <li> --}}
        <li>
          <div class="mode"><i class="fa fa-moon-o"></i></div>
        </li>

      @if(auth()->user()->hasRole('etudiant'))
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
        @endif
        
        {{-- <li>
          <a href="{{ route('panier.index') }}">
            <div class="cart-container" style="position: relative;">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
            </div>
          </a>
        </li> --}}
      {{-- @if(auth()->user()->hasRole('etudiant'))
        <li>
          <a href="{{ route('panier.index') }}">
            <div class="cart-container" style="position: relative;">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
              <!-- Badge prépositionné correctement -->
              <span id="fixed-cart-badge" class="custom-violet-badge" 
                    style="position: absolute; 
                           top: -8px; 
                           right: -14px; 
                           background-color: #2563EB; 
                           color: white; 
                           border-radius: 50%; 
                           width: 18px; 
                           height: 18px; 
                           font-size: 12px; 
                           display: none; 
                           align-items: center; 
                           justify-content: center; 
                           font-weight: bold; 
                           z-index: 10;">0</span>
            </div>
          </a>
        </li>
        @endif
<script>
// Script d'initialisation immédiate du badge sans attendre le chargement du DOM
(function() {
  // Récupérer la valeur du localStorage immédiatement
  const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
  const badge = document.getElementById('fixed-cart-badge');
  
  // Mettre à jour le badge s'il existe et si le compteur est > 0
  if (badge && cartCount > 0) {
    badge.textContent = cartCount.toString();
    badge.style.display = 'flex';
    badge.style.opacity = '1';
  }
})();
</script>

<!-- Ajouter ce script dans la section head de votre layout principal -->
<script>
// Initialisation globale des badges de panier
document.addEventListener('DOMContentLoaded', function() {
  // Charger le script optimisé pour les badges de panier
  if (!window.cartBadgeScriptLoaded) {
    window.cartBadgeScriptLoaded = true;
    const script = document.createElement('script');
    script.src = '/js/MonJs/formations/panier.js'; // Assurez-vous que ce fichier existe
    document.head.appendChild(script);
  }
});
</script>
         --}}

         {{-- @if(auth()->user()->hasRole('etudiant'))
<li>
  <a href="{{ route('panier.index') }}">
    <div class="cart-container" style="position: relative;">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
      </svg>
      <!-- Badge prépositionné correctement -->
      <span id="fixed-cart-badge" class="custom-violet-badge" 
            style="position: absolute; 
                   top: -8px; 
                   right: -14px; 
                   background-color: #2563EB; 
                   color: white; 
                   border-radius: 50%; 
                   width: 18px; 
                   height: 18px; 
                   font-size: 12px; 
                   display: none; 
                   align-items: center; 
                   justify-content: center; 
                   font-weight: bold; 
                   z-index: 10;">0</span>
    </div>
  </a>
</li>
@endif

<!-- Script d'initialisation immédiate sans attendre le chargement du DOM -->
<script>
// Initialisation immédiate du badge
(function() {
  const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
  const badge = document.getElementById('fixed-cart-badge');
  
  if (badge && cartCount > 0) {
    badge.textContent = cartCount.toString();
    badge.style.display = 'flex';
    badge.style.opacity = '1';
  }
})();
</script>

<!-- Chargement prioritaire du script de badge panier -->
<script>
// Chargement immédiat du script optimisé
document.addEventListener('DOMContentLoaded', function() {
  if (!window.cartBadgeInitialized) {
    window.cartBadgeInitialized = true;
    const script = document.createElement('script');
    script.src = '/js/MonJs/formations/panier.js';
    script.async = false; // Chargement synchrone pour priorité
    document.head.appendChild(script);
  }
});

// Sécurité: vérifier avant tout chargement de page
window.addEventListener('beforeunload', function() {
  // Sauvegarder la valeur actuelle dans sessionStorage pour restauration rapide
  const currentCount = document.getElementById('fixed-cart-badge')?.textContent || '0';
  if (currentCount !== '0') {
    sessionStorage.setItem('lastCartCount', currentCount);
  }
});

// Restauration ultra-rapide au début du chargement
if (!document.getElementById('fixed-cart-badge') && sessionStorage.getItem('lastCartCount')) {
  // Utilisé seulement si le badge n'est pas encore présent
  document.write('<style>.temp-cart-indicator{position:fixed;top:10px;right:10px;background:#2563EB;color:white;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:12px;z-index:9999;}</style>');
  document.write('<div class="temp-cart-indicator">' + sessionStorage.getItem('lastCartCount') + '</div>');
}
</script> --}}
{{-- @if(auth()->user()->hasRole('etudiant'))
<li>
  <a href="{{ route('panier.index') }}">
    <div class="cart-container" style="position: relative;">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="cart-icon">
        <circle cx="9" cy="21" r="1"></circle>
        <circle cx="20" cy="21" r="1"></circle>
        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
      </svg>
      <!-- Badge prépositionné correctement -->
      <span id="fixed-cart-badge" class="custom-violet-badge" 
            style="position: absolute; 
                   top: -8px; 
                   right: -8px; 
                   background-color: #2563EB; 
                   color: white; 
                   border-radius: 50%; 
                   width: 18px; 
                   height: 18px; 
                   font-size: 12px; 
                   display: none; 
                   align-items: center; 
                   justify-content: center; 
                   font-weight: bold; 
                   z-index: 10;">0</span>
    </div>
  </a>
</li>
@endif

<!-- Script d'initialisation immédiate du badge -->
<script>
// Initialisation immédiate du badge sans attendre le chargement du DOM
(function() {
  const cartCount = parseInt(localStorage.getItem('cartCount') || '0');
  const badge = document.getElementById('fixed-cart-badge');
  
  if (badge && cartCount > 0) {
    badge.textContent = cartCount.toString();
    badge.style.display = 'flex';
    badge.style.opacity = '1';
  }
})();
</script>

<!-- Injecter les styles CSS du badge directement pour éviter les FOUC -->
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
    opacity: 1 !important;
    transition: none !important;
}
</style>

<!-- Chargement prioritaire du script de badge panier -->
<script>
// Chargement du script principal du panier
document.addEventListener('DOMContentLoaded', function() {
  if (!window.cartBadgeInitialized) {
    window.cartBadgeInitialized = true;
    const script = document.createElement('script');
script.src = '{{ asset("assets/js/MonJs/formations/panier.js") }}';   
 document.head.appendChild(script);
  }
});
</script> --}}

@if(auth()->user()->hasRole('etudiant'))
<li>
  <a href="{{ route('panier.index') }}">
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
@endif

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
<script src="{{ asset('assets/js/MonJs/formations/panier.js') }}"></script>

        <li class="onhover-dropdown p-0">
          <a class="btn btn-primary-light" href="{{ route('logout') }}"
             onclick="event.preventDefault();
                      document.getElementById('logout-form').submit();">
            {{ __('Logout') }}
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
          </form>
        </li>
      </ul>
    </div>
    <div class="d-lg-none mobile-toggle pull-right w-auto"><i data-feather="more-horizontal"></i></div>
  </div>
</div>

<!-- Placez ce script dans la section head de votre document -->
{{-- <script>
  document.addEventListener('DOMContentLoaded', function() {
    // Récupérer la valeur stockée dès que possible
    const storedCount = parseInt(localStorage.getItem('cartCount') || '0');
    const badge = document.querySelector('.cart-badge');
    
    if (badge) {
      if (storedCount > 0) {
        badge.textContent = storedCount;
        badge.style.display = 'block';
      } else {
        badge.style.display = 'none';
      }
    }
  });
</script> --}}

@push('styles')
{{-- <style>
  /* Ce sélecteur est plus spécifique que celui qui définit la couleur rouge */
  .custom-blue-badge {
    background-color: #3b82f6; /* ou toute autre nuance de bleu de votre choix */
    /* Les autres propriétés sont déjà définies dans votre style inline */
  }
</style> --}}
@endpush