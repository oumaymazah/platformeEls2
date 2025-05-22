{{-- 
@extends('layouts.admin.master')

@section('title') Liste des Catégories
{{ $title }}
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/table.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-style.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/prism.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
@endpush

@section('content')
@component('components.breadcrumb')
    @slot('breadcrumb_title')
        <h3>Liste des Catégories</h3>
    @endslot
    <li class="breadcrumb-item">Apps</li>
    <li class="breadcrumb-item active">Liste des Catégories</li>
@endcomponent

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Catégories Disponibles</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" id="success-message" style="display: none;">
                    </div>

                    @if(session('delete'))
                        <div class="alert alert-danger" id="delete-message">
                            {{ session('delete') }}
                        </div>
                    @endif
                    <div class="row project-cards">
                        <div class="col-md-12 project-list">
                            <div class="card">
                                <div class="row">
                                    <div class="col-md-6 p-0">
                                    </div>
                                    <div class="col-md-6 p-0 text-end">
                                        <a class="btn btn-primary custom-btn" href="{{ route('categoriecreate') }}">
                                            <i data-feather="plus-square"></i> Ajouter une categorie
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="dataTable display" id="categories-table">
                            <thead>
                                <tr>
                                    <th>Nom de la Catégorie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <meta name="csrf-token" content="{{ csrf_token() }}">

                                @foreach($categories as $categorie)
                                    <tr>
                                        <td>
                                            {{ $categorie->title }}
                                            <div class="dropdown float-right">
                                                <button class="btn btn-sm btn-light dropdown-toggle no-caret" type="button" id="actionMenu{{ $categorie->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionMenu{{ $categorie->id }}">
                                                    <a class="dropdown-item" href="{{ route('categorieedit', $categorie->id) }}">
                                                        <i class="icofont icofont-ui-edit"></i>
                                                    </a>
                                                    <a class="dropdown-item text-danger delete-action" href="javascript:void(0);" data-delete-url="{{ route('categoriedestroy', $categorie->id) }}" data-type="catégorie" data-name="{{ $categorie->title }}" data-csrf="{{ csrf_token() }}">
                                                        <i class="icofont icofont-ui-delete"></i> 
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/dropdown/dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
    <script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/datatables/datatables.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
@endpush
@endsection



 --}}


 {{-- @extends('layouts.admin.master')

@section('title') Liste des Catégories
{{ $title }}
@endsection

@push('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/prism.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .dropdown-toggle.no-caret::after {
            display: none !important;
        }

        .dropdown-toggle.no-caret {
            background: none !important; 
            padding: 0 !important;
            color: black !important; 
        }

     
        .dropdown-toggle.no-caret:hover,
        .dropdown-toggle.no-caret:focus {
            background: none !important;
            color: black !important;
            box-shadow: none !important;
        }

        
        .dropdown-toggle.no-caret:focus {
            outline: none !important; 
            box-shadow: none !important;
            border-color: transparent !important; 
        }

        .dropdown-toggle.no-caret i.fa-ellipsis-v {
            display: inline-block;
            vertical-align: middle;
            transform: rotate(90deg); 
        }

        .dropdown-menu {
            top: 100% !important;
            bottom: auto !important;
            margin-top: 0.125rem;
        }

        #categories-table {
            width: 100% !important;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        #categories-table thead th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }

        #categories-table tbody td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        #categories-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        #success-message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: none;
        }

        #delete-message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
@component('components.breadcrumb')
    @slot('breadcrumb_title')
        <h3>Liste des Catégories</h3>
    @endslot
    <li class="breadcrumb-item">Apps</li>
    <li class="breadcrumb-item active">Liste des Catégories</li>
@endcomponent

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Catégories Disponibles</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" id="success-message" style="display: none;">
                    </div>

                    @if(session('delete'))
                        <div class="alert alert-danger" id="delete-message">
                            {{ session('delete') }}
                        </div>
                    @endif
                    <div class="row project-cards">
                        <div class="col-md-12 project-list">
                            <div class="card">
                                <div class="row">
                                    <div class="col-md-6 p-0">
                                    </div>
                                    <div class="col-md-6 p-0 text-end">
                                        <a class="btn btn-primary custom-btn" href="{{ route('categoriecreate') }}">
                                            <i data-feather="plus-square"></i> Ajouter une categorie
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="dataTable display" id="categories-table">
                            <thead>
                                <tr>
                                    <th>Nom de la Catégorie</th>
                                </tr>
                            </thead>
                            <tbody>
                                <meta name="csrf-token" content="{{ csrf_token() }}">

                                @foreach($categories as $categorie)
                                    <tr>
                                        <td>
                                            {{ $categorie->title }}
                                            <div class="dropdown float-right">
                                                <button class="btn btn-sm dropdown-toggle no-caret" type="button" id="actionMenu{{ $categorie->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionMenu{{ $categorie->id }}">
                                                    <a class="dropdown-item" href="{{ route('categorieedit', $categorie->id) }}">
                                                        <i class="icofont icofont-edit"></i> Modifier
                                                    </a>
                                                    <a class="dropdown-item text-danger delete-action" href="javascript:void(0);" data-delete-url="{{ route('categoriedestroy', $categorie->id) }}" data-type="catégorie" data-name="{{ $categorie->title }}" data-csrf="{{ csrf_token() }}">
                                                        <i class="icofont icofont-ui-delete"></i> Supprimer
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('assets/js/MonJs/dropdown/dropdown.js') }}"></script>
    <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
    <script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    <script src="{{ asset('assets/js/MonJs/datatables/datatables.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
@endpush
@endsection --}}


@extends('layouts.admin.master')

@section('title') Liste des Catégories
{{ $title }}
@endsection

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    {{-- <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/table.css') }}"> --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/prism.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .swal2-no-focus .swal2-styled:focus {
    box-shadow: none !important;
    outline: none !important;
}
        /* Styles pour les boutons d'action */
        .action-btn {
            width: 26px;
            height: 26px;
            border: none;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 5px;
            border-radius: 3px;
        }

        .view-btn {
            background-color: #78bbf8; /* Bleu */
        }

        .edit-btn {
            background-color: #ffcd56 !important; /* Jaune */
        }

        .delete-btn {
            background-color: #ff6b6b !important; /* Rouge */
        }

        .actions-cell {
            text-align: right;
            white-space: nowrap;
        }
        .action-btn.edit-btn:hover {
    background-color: none !important; /* Garde la même couleur jaune au hover */
}

        /* Styles pour le tableau */
        #categories-table {
            width: 100% !important;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
        }

        /* Styles pour l'en-tête du tableau */
        #categories-table thead th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: bold;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }

        /* Styles pour les cellules du tableau */
        #categories-table tbody td {
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
            vertical-align: middle;
        }

        /* Styles pour les lignes du tableau */
        #categories-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        /* Styles pour le message de succès */
        #success-message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: none;
        }

        /* Styles pour le message de suppression */
        #delete-message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
    </style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header pb-0">
                    <h5>Catégories Disponibles</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger" id="success-message" style="display: none;">
                    </div>

                    @if(session('delete'))
                        <div class="alert alert-danger" id="delete-message">
                            {{ session('delete') }}
                        </div>
                    @endif
                    <div class="row project-cards">
                        <div class="col-md-12 project-list">
                            <div class="card">
                                <div class="row">
                                    <div class="col-md-6 p-0">
                                    </div>
                                    <div class="col-md-6 p-0 text-end">
                                        <a class="btn btn-primary custom-btn" href="{{ route('categoriecreate') }}">
                                            <i data-feather="plus-square"></i> Ajouter une categorie
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="dataTable display" id="categories-table">
                            <thead>
                                <tr>
                                    <th>Titre de la Catégorie</th>
                                    <th class="text-right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <meta name="csrf-token" content="{{ csrf_token() }}">

                                @foreach($categories as $categorie)
                                    <tr>
                                        <td>{{ $categorie->title }}</td>
                                        <td class="actions-cell">
                                            {{-- <a href="{{ route('categorieshow', $categorie->id) }}" class="action-btn view-btn" title="Voir">
                                                <i class="fa fa-eye"></i>
                                            </a> --}}
                                        <button onclick="window.location.href='{{ route('categorieedit', $categorie->id) }}'" 
                                                class="action-btn edit-btn" 
                                                title="Modifier">
                                            <i class="fa fa-pencil-alt"></i>
                                        </button>

                                            <button type="button" class="action-btn delete-btn delete-action" 
                                                data-delete-url="{{ route('categoriedestroy', $categorie->id) }}" 
                                                data-type="catégorie" 
                                                data-name="{{ $categorie->title }}" 
                                                data-csrf="{{ csrf_token() }}"
                                                title="Supprimer">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <script src="{{ asset('assets/js/MonJs/dropdown/dropdown.js') }}"></script> --}}
    <script src="{{ asset('assets/js/prism/prism.min.js') }}"></script>
    <script src="{{ asset('assets/js/clipboard/clipboard.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom-card/custom-card.js') }}"></script>
    <script src="{{ asset('assets/js/height-equal.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/MonJs/datatables/datatables.js') }}"></script> --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    {{-- <script>
        // Script pour la confirmation de suppression
        $(document).ready(function() {
            $('.delete-action').on('click', function(e) {
                e.preventDefault();
                
                var deleteUrl = $(this).data('delete-url');
                var itemType = $(this).data('type');
                var itemName = $(this).data('name');
                var csrf = $(this).data('csrf');
                
                if (confirm('Êtes-vous sûr de vouloir supprimer ' + itemType + ' "' + itemName + '" ?')) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        data: {
                            '_token': csrf
                        },
                        success: function(response) {
                            $('#success-message').html('La catégorie a été supprimée avec succès.').show();
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        },
                        error: function(xhr) {
                            $('#success-message').html('Erreur lors de la suppression.').show();
                        }
                    });
                }
            });
            
            // Initialisation de DataTables avec pagination et recherche
            $('#categories-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
                },
                "paging": true,         // Activer la pagination
                "pageLength": 10,       // Nombre d'éléments par page
                "lengthChange": true,   // Option pour changer le nombre d'éléments par page
                "searching": true,      // Activer la recherche
                "ordering": true,       // Permettre le tri des colonnes
                "info": true,           // Afficher les informations de pagination
                "autoWidth": false,     // Désactiver l'ajustement automatique de la largeur
                "responsive": true      // Rendre le tableau responsive
            });
            
            // Initialiser Feather Icons
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });
    </script> --}}

    <script>
        $(document).ready(function() {
    $('.delete-action').on('click', function(e) {
        e.preventDefault();
        
        var deleteUrl = $(this).data('delete-url');
        var itemType = $(this).data('type');
        var itemName = $(this).data('name');
        var csrf = $(this).data('csrf');
        
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: 'Voulez-vous vraiment supprimer la ' + itemType + ' "' + itemName + '" ?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Annuler',
            focusConfirm: false, // Désactive le focus automatique
            customClass: {
        actions: 'swal2-no-focus' // Classe supplémentaire
    }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        '_token': csrf
                    },
                    success: function(response) {
                        Swal.fire(
                            'Supprimé!',
                            'La catégorie a été supprimée avec succès.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Erreur!',
                            'Une erreur est survenue lors de la suppression.',
                            'error'
                        );
                    }
                });
            }
        });
    });
    
    // Initialisation de DataTables avec pagination et recherche
    $('#categories-table').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.22/i18n/French.json"
        },
        "paging": true,         // Activer la pagination
        "pageLength": 10,       // Nombre d'éléments par page
        "lengthChange": true,   // Option pour changer le nombre d'éléments par page
        "searching": true,      // Activer la recherche
        "ordering": true,       // Permettre le tri des colonnes
        "info": true,           // Afficher les informations de pagination
        "autoWidth": false,     // Désactiver l'ajustement automatique de la largeur
        "responsive": true      // Rendre le tableau responsive
    });
    
    // Initialiser Feather Icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
    </script>
@endpush
@endsection