
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

                   <div class="card-body pb-0 pt-3">
    <h5 class="mb-0">Liste des Réservations des Étudiants</h5>
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
                                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>En attente</option>
                                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Confirmées</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="Rechercher par ID réservation, ID user ou téléphone..."
                                                id="reservation-search-input" value="{{ request('search') ?? '' }}">
                                            <button class="btn btn-primary" type="button" id="apply-reservation-filters">
                                                <i class="fas fa-filter"></i>
                                            </button>
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
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

            <div class="table-responsive m-0">
                <table id="reservations-table" class="table table-borderless compact-table m-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-top-0">ID</th>
                            <th class="border-top-0">Nom Complet</th>
                            <th class="border-top-0">Téléphone</th>
                            <th class="border-top-0">Email</th>
                            <th class="border-top-0">Statut</th>
                            <th class="border-top-0">Date de paiement</th>
                            {{-- <th class="border-top-0 text-center">Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentsWithReservations as $student)
                        <tr data-reservation-id="{{ $student['reservation_id'] }}" class="{{ $student['status'] == 0 ? '' : 'bg-light-blue' }}">
                            <td class="fw-bold">{{ $student['reservation_id'] }}</td>
                            <td>{{ $student['nom'] }} {{ $student['prenom'] }}</td>
                            <td>{{ $student['telephone'] }}</td>
                            <td class="email-cell">{{ $student['email'] }}</td>
                            <td>
                                <span class="badge {{ $student['status'] == 0 ? 'bg-danger' : 'bg-primary' }} px-2 py-1">
                                    <i class="fas {{ $student['status'] == 0 ? 'fa-clock' : 'fa-check-circle' }} me-1"></i>
                                    {{ $student['status_text'] }}
                                </span>
                            </td>
                            <td>
                                @if($student['payment_date'])
                                    {{ \Carbon\Carbon::parse($student['payment_date'])->format('d/m/Y H:i') }}
                                @else
                                <span class="text-muted" style="margin-left: 70px"> - </span>

                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($student['status'] == 0)
                                        <form method="POST" action="{{ route('reservations.updateStatus') }}">
                                            @csrf
                                            <input type="hidden" name="reservation_id" value="{{ $student['reservation_id'] }}">
                                            <input type="hidden" name="status" value="1">
                                            <button type="submit" class="btn btn-success btn-sm py-1 px-2" title="Valider cette réservation">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('reservations.updateStatus') }}">
                                            @csrf
                                            <input type="hidden" name="reservation_id" value="{{ $student['reservation_id'] }}">
                                            <input type="hidden" name="status" value="0">
                                            <button type="submit" class="btn btn-sm py-1 px-2" style="background-color: #907b75; border-color: #907b75; color: white;" title="Annuler la validation">                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    @endif


                                    <div class="dropdown dropdown-user-actions">
                                        <button class="btn btn-sm btn-light dropdown-toggle py-1 px-2" type="button"
                                                id="dropdownMenuButton-{{ $student['reservation_id'] }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-{{ $student['reservation_id'] }}">
                                            <li>
                                                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#formationsModal{{ $student['reservation_id'] }}">
                                                    <i class="fas fa-book-open me-2"></i> Voir formations ({{ count($student['formations']) }})
                                                </button>
                                            </li>
                                            <li>
                                                <form method="POST" action="{{ route('reservations.updateStatus') }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer définitivement cette réservation ? Cette action est irréversible.')" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="reservation_id" value="{{ $student['reservation_id'] }}">
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
                        {{-- @endforeach --}}
                        @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-content">
                                    <i class="fas fa-search-minus"></i>
                                    <h3>Aucune Reservation trouvée</h3>
                                    <p>Modifiez vos critères de recherche ou essayez plus tard</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($reservations->hasPages())
                <div class="pagination-wrapper mt-4">
                    <div class="pagination-info text-muted small mb-2">
                        <i class="fas fa-file-alt me-1"></i> Affichage de
                        <span class="fw-bold">{{ $reservations->firstItem() }}</span>
                        à <span class="fw-bold">{{ $reservations->lastItem() }}</span>
                        sur <span class="fw-bold">{{ $reservations->total() }}</span> réservations
                    </div>

                    <div class="pagination-controls">
                        <ul class="pagination custom-pagination justify-content-center">
                            {{-- Lien Précédent --}}
                            <li class="page-item {{ $reservations->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link"
                                href="{{ $reservations->appends(request()->except('page'))->previousPageUrl() }}"
                                aria-label="Précédent"
                                @if(!$reservations->onFirstPage()) onclick="return paginateReservations(event)" @endif>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            {{-- Numéros de page --}}
                            @foreach ($reservations->getUrlRange(max(1, $reservations->currentPage() - 2), min($reservations->lastPage(), $reservations->currentPage() + 2)) as $page => $url)
                                <li class="page-item {{ $reservations->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link"
                                    href="{{ $url }}"
                                    onclick="return paginateReservations(event)">
                                        {{ $page }}
                                    </a>
                                </li>
                            @endforeach

                            {{-- Lien Suivant --}}
                            <li class="page-item {{ !$reservations->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link"
                                href="{{ $reservations->appends(request()->except('page'))->nextPageUrl() }}"
                                aria-label="Suivant"
                                @if($reservations->hasMorePages()) onclick="return paginateReservations(event)" @endif>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals for formations -->
@foreach($studentsWithReservations as $student)
<div class="modal fade" id="formationsModal{{ $student['reservation_id'] }}" tabindex="-1" aria-labelledby="formationsModalLabel{{ $student['reservation_id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="formationsModalLabel{{ $student['reservation_id'] }}">
                    <i class="fas fa-graduation-cap me-2"></i>
                    Formations réservées (ID: {{ $student['reservation_id'] }})
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(count($student['formations']) > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <h6 class="fw-bold">Nombre total de formations :
                                <span class="badge bg-primary rounded-pill fs-7">{{ count($student['formations']) }}</span>
                            </h6>
                            <h6 class="fw-bold">Date de réservation :
                                <span class="badge bg-secondary rounded-pill fs-7">
                                    {{ \Carbon\Carbon::parse($student['reservation_date'])->format('d/m/Y') }}
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
                                @php
                                $totalOriginal = 0;
                                $totalDiscount = 0;
                                $totalFinal = 0;
                                $hasAnyDiscount = false;
                                @endphp

                                @foreach($student['formations'] as $formation)
                                    @php
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
                                    @endphp

                                    <tr>
                                        <td style="color: black;">
                                            <i class="fas fa-certificate text-primary me-2"></i>
                                            {{ $formation['title'] }}
                                        </td>
                                        <td class="text-end" style="color: black;">{{ number_format($originalPrice, 2) }} Dt</td>
                                        <td class="text-end">
                                            @if($discount > 0)
                                                <span class="text-danger">
                                                    <small>{{ $discount }}%</small>
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="text-end fw-bold" style="color: black;">{{ number_format($finalPrice, 2) }} Dt</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-primary">
                                <tr>
                                    <th style="color: black;">Total</th>
                                    @if($hasAnyDiscount)
                                        <th class="text-end" style="color: black;">{{ number_format($totalOriginal, 2) }} Dt</th>
                                        <th class="text-end text-danger">
                                            @if($totalOriginal > 0)
                                                <small>{{ number_format(($totalDiscount / $totalOriginal) * 100, 2) }}%</small>
                                            @endif
                                        </th>
                                        <th class="text-end" style="color: black;">{{ number_format($totalFinal, 2) }} Dt</th>
                                    @else
                                        <th></th>
                                        <th></th>
                                        <th class="text-end" style="color: black;">{{ number_format($totalFinal, 2) }} Dt</th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <p class="lead text-muted">Aucune formation dans cette réservation</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <div class="text-end mt-2">
                    <span class="badge px-3 py-2 fs-6" style="background-color: #CFE2FF; color: #161616;">
                        Prix Total: {{ number_format($totalFinal ?? 0, 2) }} Dt
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>


<script>
    $(document).ready(function() {
        // À ajouter dans votre fichier JavaScript (AdminManager.js ou équivalent)

    $('#reservations-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        scrollX: false,
        order: [[0, 'desc']],
        responsive: true,
        stateSave: true,
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "Tous"]],
        dom: '<"d-flex justify-content-between align-items-center mb-3"lf>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
        initComplete: function() {
            $('.dataTables_length select').addClass('form-select form-select-sm');
            $('.dataTables_filter input').addClass('form-control form-control-sm');
        },
        columnDefs: [
            { "width": "4%", "targets": 0 },
            { "width": "18%", "targets": 1 },
            { "width": "10%", "targets": 2 },
            { "width": "16%", "targets": 3 },
            { "width": "8%", "targets": 4 },
            { "width": "12%", "targets": 5 },
            { "width": "18%", "targets": 6 }
        ]
    });

    // Filtre de statut
    $('#status-filter').change(function() {
        let status = $(this).val();
        let table = $('#reservations-table').DataTable();

        if (status === 'pending') {
            table.column(4).search('0').draw();
        } else if (status === 'confirmed') {
            table.column(4).search('1').draw();
        } else {
            table.column(4).search('').draw();
        }
    });

    // Ajuster la position des dropdowns qui pourraient être coupés
    $(document).on('show.bs.dropdown', '.dropdown-role-actions', function() {
        const $dropdownMenu = $(this).find('.dropdown-menu');
        const dropdownPosition = $dropdownMenu.offset();
        const tableWidth = $('.table-responsive').width();
        const windowWidth = $(window).width();

        // S'assurer que le dropdown ne dépasse pas à droite
        if (dropdownPosition.left + $dropdownMenu.outerWidth() > windowWidth) {
            $dropdownMenu.addClass('dropdown-menu-end');
        }
    });
});
</script>
<style>


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
    .pagination-wrapper {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-top: 2rem;
}

.custom-pagination .page-item {
    margin: 0 3px;
}

.custom-pagination .page-link {
    border: 1px solid #dee2e6;
    color: #2B6ED4;
    min-width: 40px;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 40px;
}

.custom-pagination .page-item.active .page-link {
    background-color: #2B6ED4;
    border-color: #2B6ED4;
    color: white;
}

.custom-pagination .page-item.disabled .page-link {
    color: #6c757d;
}

.pagination-info {
    font-size: 0.9rem;
    text-align: center;
}

.pagination-info .highlight {
    font-weight: 600;
    color: #2B6ED4;
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
@endsection
