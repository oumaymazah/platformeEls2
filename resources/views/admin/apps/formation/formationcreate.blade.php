@extends('layouts.admin.master')
@section('title') Ajouter une Formation @endsection
@push('css')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/MonCss/formationcreate.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/dropzone.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/MonCss/formation-create.css') }}">
<link href="{{ asset('assets/css/MonCss/custom-style.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/MonCss/SweatAlert2.css') }}" rel="stylesheet">

<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/MonCss/custom-calendar.css') }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<style>
    .swal2-no-focus .swal2-styled:focus {
    box-shadow: none !important;
    outline: none !important;
}

</style>
@endpush
@section('content')

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>Nouvelle formation</h5>
                        <span>Complétez les informations pour créer une nouvelle formation</span>
                    </div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                          <!-- Alerte d'information sur le calcul automatique de la durée -->
                          <div class="alert alert-info">
                            <i class="fa fa-info-circle me-2"></i>
                            <strong>Note:</strong> La durée sera calculée automatiquement en fonction des cours ajoutés à cette formation.
                        </div>


                        <div class="form theme-form">
                            <meta name="csrf-token" content="{{ csrf_token() }}">

                            <form id="formationForm"class="needs-validation" action="{{ route('formationstore') }}" method="POST" enctype="multipart/form-data" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col">
                                        <!-- Titre -->
                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Titre <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-tag"></i></span>
                                                    <input class="form-control" type="text" id="title" name="title" placeholder="Titre" value="{{ session('form_data.title') ?? old('title') }}" required />
                                                </div>
                                                <div class="invalid-feedback">Veuillez entrer un Titre valide.</div>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Description <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group" style="flex-wrap: nowrap;">
                                                    <div class="input-group-text d-flex align-items-stretch" style="height: auto;">
                                                        <i class="fa fa-align-left align-self-center"></i>
                                                    </div>
                                                    <textarea class="form-control" id="description" name="description" placeholder="Description" required>{{ session('form_data.description') ?? old('description') }}</textarea>
                                                </div>
                                                <div class="invalid-feedback">Veuillez entrer une description valide.</div>
                                            </div>
                                        </div>


                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Périodes <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="row">
                                                    <!-- Date de début -->
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            <input class="form-control datepicker" type="text" id="start_date" name="start_date" placeholder="" value="{{ session('form_data.start_date') ? \Carbon\Carbon::parse(session('form_data.start_date'))->format('d/m/Y') : old('start_date') }}" readonly required />
                                                        </div>
                                                        <small class="text-muted">Date début</small>
                                                        <div class="invalid-feedback">Veuillez sélectionner une date de début valide.</div>
                                                    </div>
                                                    <!-- Date de fin -->
                                                    <div class="col-md-6">
                                                        <div class="input-group">
                                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            <input class="form-control datepicker" type="text" id="end_date" name="end_date" placeholder="" value="{{ session('form_data.end_date') ? \Carbon\Carbon::parse(session('form_data.end_date'))->format('d/m/Y') : old('end_date') }}" readonly required />
                                                        </div>
                                                        <small class="text-muted">Date fin</small>
                                                        <div class="invalid-feedback">Veuillez sélectionner une date de fin valide.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>                              <!-- Type -->
                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Type <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-list"></i></span>
                                                    <select class="form-select" id="type" name="type" required>
                                                        <option value="" selected disabled>Choisir un type</option>
                                                        <option value="payante" {{ (session('form_data.type') == 'payante' || old('type') == 'payante') ? 'selected' : '' }}>Payante</option>
                                                        <option value="gratuite" {{ (session('form_data.type') == 'gratuite' || old('type') == 'gratuite') ? 'selected' : '' }}>Gratuite</option>
                                                    </select>
                                                </div>
                                                <div class="invalid-feedback">Veuillez sélectionner un type.</div>
                                            </div>
                                        </div>

                                        <!-- Prix -->
                                        <div class="mb-3 row" id="priceContainer">
                                            <label class="col-sm-2 col-form-label">Prix <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-text">Dt</span>
                                                    <input class="form-control"
                                                           type="number"
                                                           id="price"
                                                           name="price"
                                                           placeholder="Ex: 50.000"
                                                           step="0.001"
                                                           min="0"
                                                           value="{{ session('form_data.price') ?? old('price') }}" />
                                                </div>
                                                <small class="text-muted">Format: 000.000 (3 décimales obligatoires)</small>
                                                <div class="invalid-feedback">Veuillez entrer un prix valide (ex: 50.000 ou 45.500)</div>
                                            </div>
                                        </div>

                                        <!-- Catégorie -->
                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Catégorie <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <select class="form-select select2-categorie" id="categorie_id" name="category_id" required>
                                                        <option value="" selected disabled>Choisir une catégorie</option>
                                                        @foreach($categories as $categorie)
                                                            <option value="{{ $categorie->id }}" {{ (session('form_data.category_id') == $categorie->id || old('category_id') == $categorie->id) ? 'selected' : '' }}>
                                                                {{ $categorie->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="invalid-feedback">Veuillez sélectionner une catégorie valide.</div>
                                            </div>
                                        </div>

                                        <!-- Professeur -->
                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Professeur <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="row">
                                                    <div class="col-auto">
                                                        <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                    </div>
                                                    <div class="col">
                                                        <select id="user_id" class="form-select select2-professeur" name="user_id" required>
                                                            <option value="" disabled selected>Sélectionnez un professeur</option>
                                                            @foreach($professeurs as $professeur)
                                                                <option value="{{ $professeur->id }}" {{ (session('form_data.user_id') == $professeur->id || old('user_id') == $professeur->id) ? 'selected' : '' }}>
                                                                    {{ $professeur->name }} {{ $professeur->lastname ?? '' }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="invalid-feedback">Veuillez sélectionner un professeur valide.</div>
                                            </div>
                                        </div>
                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Nombre de places <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="fa fa-users"></i></span>
                                                    <input class="form-control"
                                                           type="number"
                                                           id="total_seats"
                                                           name="total_seats"
                                                           placeholder="Ex: 20"
                                                           min="1"
                                                           value="{{ session('form_data.total_seats') ?? old('total_seats') }}"
                                                           required />
                                                </div>
                                                <small class="text-muted">Nombre maximum de participants</small>
                                                <div class="invalid-feedback">Veuillez entrer un nombre de places valide (minimum 1).</div>
                                            </div>
                                        </div>



                                        <div class="mb-3 row">
                                            <label class="col-sm-2 col-form-label">Image <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                @if(session('success') && session('old_data') && isset(session('old_data')['image']))
                                                    <!-- Afficher uniquement l'image après création réussie -->
                                                    <div class="mt-3 text-center">
                                                        <img src="{{ asset('storage/' . session('old_data')['image']) }}"
                                                             class="img-fluid border rounded mx-auto"
                                                             alt="Aperçu de l'image"
                                                             style="display: block; width: auto; height: auto; max-width: 80%; min-height: 200px; margin: 0 auto;">
                                                        <input type="hidden" name="current_image" value="{{ session('old_data')['image'] }}">
                                                        <input type="hidden" name="keep_image" value="1">
                                                    </div>
                                                @else
                                                    <!-- Afficher l'interface normale d'upload pendant la création -->
                                                    <div class="input-group">
                                                        <span class="input-group-text"><i class="fa fa-image"></i></span>
                                                        <input class="form-control" type="file" id="image" name="image" accept="image/*" required />
                                                    </div>
                                                    <div class="invalid-feedback">Veuillez télécharger une image valide.</div>
                                                    <small class="text-muted">Formats acceptés: JPG, PNG, GIF. Taille max: 2Mo</small>
                                                @endif
                                            </div>
                                        </div>


                                        <!-- Conteneur de prévisualisation de l'image -->
                                        <div class="center-container">
                                            <div id="imagePreviewContainer" class="image-preview-container hidden">
                                                <img id="imagePreview" class="image-preview" src="#" alt="Prévisualisation de l'image" />
                                                <div class="image-preview-actions">
                                                    <button type="button" class="btn-icon" id="deleteImage">
                                                        <i class="fa fa-trash trash-icon"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Publication Section -->
                                        <div class="mb-3 row">
                                            <div class="col-12">
                                                <div class="d-flex justify-content-center">
                                                    <div class="form-group m-t-15 m-checkbox-inline mb-0 custom-radio-ml">
                                                        <div class="radio radio-primary mx-2">
                                                            <input id="publishNow" type="radio" name="publication_type" value="now" {{ (session('form_data.publication_type') == 'now' || old('publication_type', 'now') == 'now') ? 'checked' : '' }}>
                                                            <label class="mb-0" for="publishNow">Publier immédiatement</label>
                                                        </div>
                                                        <div class="radio radio-primary mx-2">
                                                            <input id="publishLater" type="radio" name="publication_type" value="later" {{ (session('form_data.publication_type') == 'later' || old('publication_type') == 'later') ? 'checked' : '' }}>
                                                            <label class="mb-0" for="publishLater">Programmer la publication</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div id="publishDateContainer" class="mt-3 text-center" {{ (session('form_data.publication_type') == 'later' || old('publication_type') == 'later') ? '' : 'style="display: none;"' }}>
                                                    <div class="d-flex justify-content-center">
                                                        <div class="input-group" style="max-width:500px;">
                                                            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                                            <input class="form-control datepicker"
                                                                type="text"
                                                                id="publish_date"
                                                                name="publish_date"
                                                                value="{{ session('form_data.publish_date') ? \Carbon\Carbon::parse(session('form_data.publish_date'))->format('d/m/Y') : old('publish_date') }}"
                                                                placeholder="Sélectionnez une date"
                                                                readonly>
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">Sélectionnez la date de publication</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Boutons -->
                                        <div class="row">
                                            <div class="col">
                                                <div class="text-end mt-4">
                                                    <button class="btn btn-primary" type="submit">
                                                        <i class="fa fa-save"></i> Ajouter
                                                    </button>
                                                    <button class="btn btn-danger" type="button" onclick="window.location.href='{{ route('formations') }}'">
                                                        <i class="fa fa-times"></i> Annuler
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('assets/js/dropzone/dropzone.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="{{ asset('assets/js/MonJs/select2-init/single-select.js') }}"></script>
<script src="{{ asset('assets/js/MonJs/form-validation/form-validation.js') }}"></script>
<script src="{{ asset('assets/js/MonJs/formations/formation-submit.js') }}"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="{{ asset('assets/css/MonCss/SweatAlert2.css') }}" rel="stylesheet">

<script src="{{ asset('assets/js/tinymce/js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/js/MonJs/description/description.js') }}"></script>
<script src="https://cdn.tiny.cloud/1/e4tuzbr3p233wnrsytc5nnxttupqflinuh73ptyk3dmtnt13/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js"></script>
<script src="{{ asset('assets/js/MonJs/calendar/custom-calendar.js') }}"></script>

<script>
    // Script pour gérer l'affichage conditionnel
    document.addEventListener('DOMContentLoaded', function() {
        // Type de publication
        const publishLater = document.getElementById('publishLater');
        const publishNow = document.getElementById('publishNow');
        const publishDateContainer = document.getElementById('publishDateContainer');

        // Type de formation (payante/gratuite)
        const typeSelect = document.getElementById('type');
        const priceContainer = document.getElementById('priceContainer');

        // Gestion de l'affichage du conteneur de date de publication
        function togglePublishDateContainer() {
            if (publishLater.checked) {
                publishDateContainer.style.display = 'block';
            } else {
                publishDateContainer.style.display = 'none';
            }
        }

        // Gestion de l'affichage du champ prix
        function togglePriceContainer() {
            if (typeSelect.value === 'payante') {
                priceContainer.style.display = 'flex';
                document.getElementById('price').setAttribute('required', 'required');
            } else {
                priceContainer.style.display = 'none';
                document.getElementById('price').removeAttribute('required');
            }
        }

        // Initial toggle
        togglePublishDateContainer();
        togglePriceContainer();

        // Event listeners
        publishLater.addEventListener('change', togglePublishDateContainer);
        publishNow.addEventListener('change', togglePublishDateContainer);
        typeSelect.addEventListener('change', togglePriceContainer);

        // Réinitialiser les Select2 après chargement des valeurs
        if (window.jQuery && $.fn.select2) {
            $('.select2-categorie').select2();
            $('.select2-professeur').select2();
        }
    });
</script>

<script>
    // Ce script devrait être inclus à la fin de la page formationcreate.blade.php
    document.addEventListener('DOMContentLoaded', function() {
        // Vérifiez si une formation vient d'être créée (via session)
        const formationCreated = "{{ session('success') && session('formation_id') }}";
        const formationId = "{{ session('formation_id') }}";

        console.log("Vérification de création de formation:", {
            formationCreated: formationCreated,
            formationId: formationId
        });

        if (formationCreated && formationId) {
            Swal.fire({
                title: "Formation ajoutée avec succès !",
                text: "Voulez-vous ajouter un cours à cette formation ?",
                icon: "success",
                showCancelButton: true,
                confirmButtonText: "Oui, ajouter un cours",
                cancelButtonText: "Non, revenir à la liste",
                showCloseButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                focusConfirm: false,
                customClass: {
                    actions: 'swal2-no-focus',
                    confirmButton: 'custom-confirm-btn'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Rediriger vers la page de création de cours avec l'ID de formation
                    window.location.href = "{{ route('courscreate') }}?training_id=" + formationId;
                } else if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                    // Rediriger vers la liste des formations
                    window.location.href = "{{ route('formations') }}";
                }
            });
        }
    });
</script>
@endpush
