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


                    <div class="card-body pb-0">

                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="stat-card bg-white shadow-sm border border-light rounded-3 flex-grow-1">
                                            <div class="d-flex p-3">
                                                <div class="stat-icon-container rounded-circle bg-primary-ultra-light p-3 me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-clipboard-list fa-lg text-primary"></i>
                                                </div>
                                                <div>
                                                    <h3 class="fs-4 fw-bold mb-0">{{ $reservations->total() }}</h3>
                                                    <p class="text-muted mb-0">Total des réservations</p>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $studentsWithReservationsCollection = collect($studentsWithReservations);
                                        @endphp
                                        <div class="stat-card bg-white shadow-sm border border-light rounded-3 flex-grow-1">
                                            <div class="d-flex p-3">
                                                <div class="stat-icon-container rounded-circle bg-primary-ultra-light p-3 me-3 d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-check-circle fa-lg text-primary"></i>
                                                </div>
                                                <div>
                                                    <h3 class="fs-4 fw-bold mb-0">{{ $studentsWithReservationsCollection->where('status', 1)->count() }}</h3>
                                                    <p class="text-muted mb-0">Réservations confirmées</p>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>
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
                                            <input type="text" class="form-control" placeholder="Rechercher par ID réservation,ou téléphone..."
                                                id="reservation-search-input" value="{{ request('search') ?? '' }}">

                                        </div>
                                    </div>


                                    <div class="col-md-2">

                                        <button class="btn btn-sm btn-light border-0 shadow-sm rounded-pill px-3 py-2 d-inline-flex align-items-center justify-content-center" id="reset-reservation-filters" data-bs-toggle="tooltip" title="Effacer tous les filtres">
                                            <i class="fas fa-undo-alt text-primary"></i>
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
                                    @forelse($studentsWithReservations as $student)
                                    <tr data-reservation-id="{{ $student['reservation_id'] }}" >
                                        <td class="fw-bold">{{ $student['reservation_id'] }}</td>
                                        <td>{{ $student['nom'] }} {{ $student['prenom'] }}</td>
                                        <td>{{ $student['telephone'] }}</td>

                                        <td>
                                            <span class="badge {{ $student['status'] == 0 ? 'bg-danger' : 'bg-primary' }} px-2 py-1 d-inline-flex align-items-center" style="font-size: 0.85rem;">
                                                <i class="fas {{ $student['status'] == 0 ? 'fa-clock' : 'fa-check-circle' }} me-1"></i>
                                                <strong>{{ $student['status_text'] }}</strong>
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
                                                    <form class="reservation-status-form" method="POST" action="{{ route('reservations.updateStatus') }}">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $student['reservation_id'] }}">
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit" class="btn btn-success btn-sm py-1 px-2" title="Valider cette réservation">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form class="reservation-status-form" method="POST" action="{{ route('reservations.updateStatus') }}">
                                                        @csrf
                                                        <input type="hidden" name="reservation_id" value="{{ $student['reservation_id'] }}">
                                                        <input type="hidden" name="status" value="0">
                                                        <button type="submit" class="btn btn-sm py-1 px-2" style="background-color: #907b75; border-color: #907b75; color: white;" title="Annuler la validation">
                                                            <i class="fas fa-times"></i>
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
                                                            <a class="dropdown-item delete-reservation" href="#" data-url="{{ route('admin.reservations.destroy',$student['reservation_id']) }}">
                                                                <i class="fas fa-trash me-2"></i> Supprimer
                                                            </a>

                                                        </li>
                                                    </ul>
                                                </div>

                                            </div>
                                        </td>
                                    </tr>

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
        </div>
<!-- Modals for formations -->
{{-- @foreach($studentsWithReservations as $student)
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
@endforeach --}}
@foreach($studentsWithReservations as $student)
<div class="modal fade" id="formationsModal{{ $student['reservation_id'] }}" tabindex="-1" aria-labelledby="formationsModalLabel{{ $student['reservation_id'] }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title d-flex align-items-center" id="formationsModalLabel{{ $student['reservation_id'] }}">
                    <span class="modal-icon-container bg-white rounded-circle p-2 me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-graduation-cap text-primary"></i>
                    </span>
                    Formations réservées <span class="badge bg-white text-primary ms-2"><strong>ID: {{ $student['reservation_id'] }}</strong></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if(count($student['formations']) > 0)
                    <div class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-list-alt text-white"></i>
                                        </div>
                                        <div>
                                            <p class="card-text mb-0 text-dark small">Nombre total de formations</p>
                                            <h6 class="card-title mb-0 text-muted">{{ count($student['formations']) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light border-0 shadow-sm h-100">
                                    <div class="card-body p-3 d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 d-flex align-items-center justify-content-center">
                                            <i class="fas fa-calendar-alt text-white"></i>
                                        </div>
                                        <div>
                                            <p class="card-text mb-0 text-dark small">Date de réservation</p>
                                            <h6 class="card-title mb-0  text-muted">{{ \Carbon\Carbon::parse($student['reservation_date'])->format('d/m/Y') }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive rounded shadow-sm">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-3 border-0">Formation</th>
                                    <th class="px-3 py-3 border-0 text-end">Prix</th>
                                    <th class="px-3 py-3 border-0 text-end">Remise</th>
                                    <th class="px-3 py-3 border-0 text-end">Prix final</th>
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
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-certificate text-white"></i>
                                                </div>
                                                <span>{{ $formation['title'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-end">{{ number_format($originalPrice, 2) }} Dt</td>
                                        <td class="px-3 py-3 text-end">
                                            @if($discount > 0)
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-2 py-1">
                                                    {{ $discount }}%
                                                </span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-end fw-bold">{{ number_format($finalPrice, 2) }} Dt</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-primary ">
                                    <th class="px-3 py-3 border-0 text-white">Total</th>
                                    @if($hasAnyDiscount)
                                        <th class="px-3 py-3 border-0 text-white">{{ number_format($totalOriginal, 2) }} Dt</th>
                                        <th class="px-3 py-3 border-0 text-white">
                                            @if($totalOriginal > 0)
                                                <span class="badge bg-danger bg-opacity-10 text-white px-2 py-1">
                                                    {{ number_format(($totalDiscount / $totalOriginal) * 100, 2) }}%
                                                </span>
                                            @endif
                                        </th>
                                        <th class="px-3 py-3 border-0 text-white">{{ number_format($totalFinal, 2) }} Dt</th>
                                    @else
                                        <th class="px-3 py-3 border-0"></th>
                                        <th class="px-3 py-3 border-0"></th>
                                        <th class="px-3 py-3 border-0 text-white">{{ number_format($totalFinal, 2) }} Dt</th>
                                    @endif
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="empty-state-icon bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                            <i class="fas fa-info-circle fa-2x text-muted"></i>
                        </div>
                        <h5 class="text-muted mb-1">Aucune formation</h5>
                        <p class="text-muted small mb-0">Cette réservation ne contient aucune formation.</p>
                    </div>
                @endif
            </div>

            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Fermer
                </button>
                <div class="ms-auto">
                    <div class="price-badge bg-primary text-white px-3 py-2 rounded-pill shadow-sm">
                        <i class="fas fa-tags me-1"></i>
                        Prix Total: <span class="fw-bold">{{ number_format($totalFinal ?? 0, 2) }} Dt</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach


<style>


    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --primary-light: #6184ff;
        --primary-ultra-light: #eef1ff;
        --secondary: #3f37c9;
        --success: #4c89e8;
        --success-ultra-light: #e6f3ff;
        --danger: #f87171;
        --danger-ultra-light: #fee2e2;
        --warning: #fbbf24;
        --dark: #1f2937;
        --light: #f9fafb;
        --border: #e5e7eb;
        --text-primary: #333;
        --text-secondary: #6b7280;
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.1);
        --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        --radius-sm: 0.25rem;
        --radius: 0.5rem;
        --radius-lg: 0.75rem;
        --transition: all 0.3s ease;
    }

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
     .stat-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow);
    }

    .stat-icon-container {
        width: 50px;
        height: 50px;
    }


    .modal-icon-container {
        width: 32px;
        height: 32px;
    }

    /* Animation pour la modale */
    .modal.fade .modal-dialog {
        transition: transform 0.3s ease-out;
    }

    .modal.show .modal-dialog {
        transform: none;
    }

    /* Styles pour le tableau dans la modale */
    .table-hover tbody tr:hover {
        background-color: var(--primary-ultra-light);
    }

    /* Style pour le badge de prix total */
    .price-badge {
        display: inline-block;
        font-size: 0.95rem;
    }

    /* Style pour les états vides */
    .empty-state-icon {
        transition: transform 0.3s ease;
    }

    .empty-state-icon:hover {
        transform: scale(1.05);
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 1rem;
        }

        .modal-footer {
            flex-direction: column-reverse;
            gap: 1rem;
        }

        .modal-footer .ms-auto {
            margin-left: 0 !important;
            width: 100%;
        }

        .price-badge {
            width: 100%;
            text-align: center;
        }
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

