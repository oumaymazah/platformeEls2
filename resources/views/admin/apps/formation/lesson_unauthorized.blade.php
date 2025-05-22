
<div class="unauthorized-content text-center py-5">
    @if($type == 'not_authenticated')
        <div class="mb-4">
            <img src="{{ asset('images/locked-content.png') }}" alt="Contenu verrouillé" class="img-fluid" style="max-width: 200px;">
        </div>
        <h4 class="mb-3">Connexion requise</h4>
        <p class="mb-4">Vous devez vous connecter pour accéder au contenu de cette formation.</p>
        <div>
            <a href="{{ route('login') }}" class="btn btn-primary">Se connecter</a>
            <a href="{{ route('sign-up') }}" class="btn btn-outline-primary ms-2">S'inscrire</a>
        </div>
    @elseif($type == 'not_purchased')
        <div class="mb-4">
            <img src="{{ asset('images/purchase-required.png') }}" alt="Achat requis" class="img-fluid" style="max-width: 200px;">
        </div>
        <h4 class="mb-3">Accès limité</h4>
        <p class="mb-4">Vous devez acheter cette formation pour accéder à son contenu.</p>

    @endif
</div>
