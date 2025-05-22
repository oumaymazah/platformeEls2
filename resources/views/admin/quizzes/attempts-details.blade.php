<!-- Page de gestion des tentatives avec design professionnel et créatif -->
<div >
    <!-- En-tête de page avec effet parallaxe léger -->
    <div class="carte-header">
        <div class="header-content">
            <div class="header-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <div class="header-text">
                <h1>Tableau de Bord des Évaluations</h1>
                <p>Visualisez et gérez les tentatives de quiz des apprenants</p>
            </div>
        </div>
    </div>

    <div class="container main-container">
        <div class="dashboard-card">
            <div class="card-header-custom">
                <div class="header-title">
                    <i class="fas fa-graduation-cap header-icon"></i>
                    <h2>Gestion des Tentatives</h2>
                </div>

                <div class="quick-stats">
                    <div class="stat-item">
                        <div class="stat-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value">{{ $attempts->where('passed', true)->count() }}</span>
                            <span class="stat-label">Réussites</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value">{{ $attempts->where('passed', false)->count() }}</span>
                            <span class="stat-label">Échecs</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                       
                        <div class="stat-content">
                            <span class="stat-value">{{ $attempts->where('tab_switches', '>=', 2)->count() }}</span>
                            <span class="stat-label">Tentatives de triche</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section des filtres avec design moderne -->
            <div class="filters-section">
                <div class="filters-header">
                    <h3><i class="fas fa-filter me-2"></i>Filtres</h3>
                    <a href="{{ route('admin.quiz-attempts.index') }}" class="btn-reset-all reset-filters">
                        <i class="fas fa-sync-alt"></i> Réinitialiser tous les filtres
                    </a>
                </div>

                <form method="GET" class="advanced-filters filter-form" action="{{ route('admin.quiz-attempts.index') }}">
                    <div class="filter-row">
                        <div class="filter-item">
                            <label for="training_id">
                                <i class="fas fa-book-open filter-icon"></i> Formation
                            </label>
                            <select name="training_id" id="training_id" class="form-control">
                                <option value="">Toutes formations</option>
                                @foreach($trainings as $training)
                                    <option value="{{ $training->id }}" {{ request('training_id') == $training->id ? 'selected' : '' }}>
                                        {{ $training->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-item">
                            <label for="quiz_type">
                                <i class="fas fa-tags filter-icon"></i> Type de quiz
                            </label>
                            <select name="quiz_type" id="quiz_type" class="form-control">
                                <option value="">Tous types</option>
                                @foreach($quizTypes as $key => $type)
                                    <option value="{{ $key }}" {{ request('quiz_type') == $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-item">
                            <label for="date">
                                <i class="fas fa-calendar-alt filter-icon"></i> Date
                            </label>
                            <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="btn-apply">
                                <i class="fas fa-search"></i> Rechercher
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Table des tentatives avec design amélioré -->
            <div class="table-container">
                <table class="evaluation-table">
                    <thead>
                        <tr>
                            <th  width="30%"><i class="fas fa-user me-2"></i>Étudiant</th>
                            <th width="20%"><i class="fas fa-question-circle me-2"></i>Quiz</th>
                            <th width="20%"><i class="fas fa-chalkboard-teacher me-2"></i>Formation</th>
                            <th width="10%"><i class="fas fa-chart-bar me-2"></i>Score</th>
                            <th width="10%"><i class="fas fa-check-circle me-2"></i>Statut</th>
                            <th><i class="fas fa-calendar me-2"></i>Date</th>
                            <th width="10%" ></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attempts as $attempt)
                        <tr class="data-row">
                            <td>
                                <div class="user-info">

                                    <span>{{ $attempt->user->name }} {{ $attempt->user->lastname ?? '' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="quiz-info">
                                    <span class="quiz-title">{{ $attempt->quiz->title }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="training-pill">
                                    {{ $attempt->quiz->training->title }}
                                </div>
                            </td>
                            <td>
                                <div class="score-gauge">
                                    <div class="gauge">
                                        <div class="gauge-fill" style="width: {{ min(100, ($attempt->score/20) * 100) }}%; background-color: {{ $attempt->score >= ($attempt->quiz->passing_score ?? 10) ? 'var(--success)' : 'var(--danger)' }};"></div>
                                    </div>
                                    <span class="score-value {{ $attempt->score >= ($attempt->quiz->passing_score ?? 10) ? 'success-text' : 'danger-text' }}">
                                        {{ $attempt->score }}/20
                                    </span>
                                </div>
                            </td>
                            <td>
                                @if($attempt->isCheated())
                                    <span class="status-badge warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Triche
                                    </span>
                                @else
                                    <span class="status-badge {{ $attempt->passed ? 'success' : 'danger' }}">
                                        @if($attempt->passed)
                                            <i class="fas fa-check-circle me-1"></i> Réussi
                                        @else
                                            <i class="fas fa-times-circle me-1"></i> Échoué
                                        @endif
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="date-info">
                                    <div class="date-day">{{ $attempt->created_at->format('d/m/Y') }}</div>
                                    <div class="date-time">{{ $attempt->created_at->format('H:i') }}</div>
                                </div>
                            </td>
                            <td>
                                {{-- <div class="action-buttons">
                                    <button class="btn-action view-quiz-detail" data-viewEvaluation-url="{{ route('admin.quiz-attempts.show', $attempt->id) }}">
                                        <i class="fa fa-info-circle"></i>
                                    </button>
                                    <button class="btn-action delete-action delete-role" data-url="{{ route('admin.quiz-attempts.destroy', $attempt) }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div> --}}
                                <div class="dropdown dropdown-evaluation-actions">
                                    <button class="btn btn-sm btn-light dropdown-toggle" type="button"
                                            id="dropdownMenuButton-{{ $attempt->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="fas fa-ellipsis-h" aria-hidden="true"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton-{{ $attempt->id }}">
                                        <li>

                                            <a class="dropdown-item view-quiz-detail" href="#"
                                               data-url="{{ route('admin.quiz-attempts.show', $attempt->id) }}" id="load-quiz-detail">
                                                <i class="fa fa-info-circle"></i> plus d'info
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item delete-role" href="#"
                                               data-url="{{ route('admin.quiz-attempts.destroy', $attempt) }}">
                                                <i class="fas fa-trash me-2"></i> Supprimer
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <div class="empty-content">
                                    <i class="fas fa-search-minus"></i>
                                    <h3>Aucune tentative trouvée</h3>
                                    <p>Modifiez vos critères de recherche ou essayez plus tard</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination améliorée -->
            @if($attempts->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <i class="fas fa-file-alt me-1"></i> Affichage de
                        <span class="highlight">{{ $attempts->firstItem() }}</span>
                        à <span class="highlight">{{ $attempts->lastItem() }}</span>
                        sur <span class="highlight">{{ $attempts->total() }}</span> résultats
                    </div>

                    <div class="pagination-controls">
                        <ul class="pagination custom-pagination">
                            {{-- Previous Page Link --}}
                            <li class="page-item {{ $attempts->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $attempts->appends(request()->except('page'))->previousPageUrl() }}" aria-label="Précédent">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            </li>

                            {{-- Pagination Elements --}}
                            @foreach ($attempts->appends(request()->except('page'))->getUrlRange(1, $attempts->lastPage()) as $page => $url)
                                <li class="page-item {{ $attempts->currentPage() == $page ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endforeach

                            {{-- Next Page Link --}}
                            <li class="page-item {{ !$attempts->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $attempts->appends(request()->except('page'))->nextPageUrl() }}" aria-label="Suivant">
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

<!-- Animation de chargement avec design amélioré -->
<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-spinner">
        <div class="spinner"></div>
        <div class="loading-text">Chargement des données...</div>
    </div>
</div>

<style>

    :root {
        --primary: #4361ee;
        --primary-dark: #3a56d4;
        --primary-light: #6184ff;
        --primary-ultra-light: #eef1ff;
        --secondary: #3f37c9;

        --success: #4c89e8;
        --danger: #f87171;
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
    .email-wrap p {
        margin-bottom: 0;
        color: #fff;
    }


    @keyframes fadeInDropdown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    .dropdown-toggle::after {
        display: none;
    }
    .dropdown-menu {
        min-width: 10rem;
        box-shadow: 0 5px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    .dropdown-item {
        padding: 0.35rem 1.5rem;
        font-size: 0.875rem;
    }

    .dropdown-toggle {
        padding: 0.25rem 0.5rem;
    }


    /* Modifications pour corriger le problème d'affichage du dropdown */
    .dropdown-evaluation-actions {
        position: relative;
    }
    .dropdown-menu-end {
        right: 0;
        left: auto !important;
    }
    .table-responsive {
        overflow: visible !important;
    }
    .data-row.dropdown-active:hover {
        transform: none !important;
        box-shadow: none !important;
    }




    body {
        font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f7fb;
        color: var(--text-primary);
        margin: 0;
        padding: 0;
        line-height: 1.6;
        overflow-x: hidden;
        max-width: 100%;
    }



    /* Page Header Styles with Parallax Effect */
    .carte-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        position: relative;
        overflow: hidden;
        padding: 1.5rem 2rem; /* Réduit de 3rem à 1.5rem */
        margin-bottom: 1rem; /* Réduit de 2rem à 1rem */
        box-shadow: var(--shadow-lg);

    }


    .header-content {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .header-icon {
        font-size: 2.5rem; /* Réduit de 3.5rem à 2.5rem */
        margin-right: 1.5rem; /* Réduit de 2rem à 1.5rem */
        height: 5rem; /* Réduit de 7rem à 5rem */
        width: 5rem; /* Réduit de 7rem à 5rem */
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border: 3px solid rgba(255, 255, 255, 0.3);
    }

    .header-text h1 {
        margin: 0;
        font-size: 2rem; /* Réduit de 2.5rem à 2rem */
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header-text p {
        font-size: 1rem; /* Réduit de 1.2rem à 1rem */
        margin: 0.5rem 0 0;
        opacity: 0.8;
    }

    /* Main Content Container */
    .main-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 1rem 1.5rem; /* Réduit latéralement */
        width: 100%;
    }

    /* Dashboard Card Styles */
    .dashboard-card {
        background: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 1.5rem;
        border: 1px solid var(--border);
        width: 100%;
        max-width: 100%;
    }

    /* Card Header with Stats */
    .card-header-custom {
        padding: 1.25rem; /* Réduit de 1.5rem à 1.25rem */
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        background: white;
        border-bottom: 1px solid var(--border);
    }

    .header-title {
        display: flex;
        align-items: center;
    }

    .header-title h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--dark);
    }

    /* Quick Stats Section */
    .quick-stats {
        display: flex;
        gap: 1.5rem;
    }

    .stat-item {
        display: flex;
        align-items: center;
        background: var(--light);
        padding: 0.75rem 1rem;
        border-radius: var(--radius);
        min-width: 130px;
        border: 1px solid var(--border);
    }

    .stat-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        font-size: 1rem;
    }

    .stat-icon.success {
        background-color: rgb(230, 240, 251);
        color: var(--success);
    }

    .stat-icon.danger {
        background-color: rgba(248, 113, 113, 0.15);
        color: var(--danger);
    }

    .stat-icon.warning {
        background-color: rgba(251, 191, 36, 0.15);
        color: var(--warning);
    }

    .stat-content {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-weight: 700;
        font-size: 1.1rem;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--text-secondary);
        margin-top: 0.2rem;
    }

    /* Filters Section */
    .filters-section {
        padding: 1.25rem; /* Réduit de 1.5rem à 1.25rem */
        background-color: var(--primary-ultra-light);
        border-bottom: 1px solid var(--border);
    }

    .filters-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .filters-header h3 {
        margin: 0;
        font-size: 1.1rem;
        color: var(--primary-dark);
        font-weight: 600;
    }

    .btn-reset-all {
        background: transparent;
        color: var(--primary);
        border: none;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: var(--radius-sm);
        transition: var(--transition);
        text-decoration: none;
    }

    .btn-reset-all:hover {
        background: rgba(67, 97, 238, 0.1);
    }

    .advanced-filters {
        background: white;
        border-radius: var(--radius);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
    }

    .filter-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: flex-end;
    }

    .filter-item {
        flex: 1;
        min-width: 200px;
    }

    .filter-item label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .filter-icon {
        color: var(--primary);
        margin-right: 0.25rem;
    }

    .filter-item .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--border);
        border-radius: var(--radius);
        font-size: 0.95rem;
        background-color: white;
        transition: var(--transition);
    }

    .filter-item .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        outline: none;
    }

    .filter-actions {
        display: flex;
        gap: 0.5rem;
        align-items: flex-end;
    }

    .btn-apply {
        background: var(--primary);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius);
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-apply:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    /* Table Styles */
    .table-container {
        padding: 0;
        overflow: hidden; /* Masquer le défilement par défaut */
        width: 100%;
    }

    .evaluation-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        min-width: auto; /* Permettre au tableau de s'adapter */
        table-layout: auto; /* Distribution équilibrée des colonnes */
    }

    .evaluation-table thead tr {
        background-color: #f8fafc;
        border-bottom: 2px solid var(--border);
    }

    .evaluation-table th {
        padding: 0.85rem; /* Réduit de 1rem à 0.85rem */
        text-align: left;
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.9rem;
        border-bottom: 2px solid var(--border);
        white-space: normal; /* Permettre le retour à la ligne */
    }

    .evaluation-table td {
        padding: 0.7rem 0.85rem; /* Réduit et optimisé */
        border-bottom: 1px solid var(--border);
        vertical-align: middle;
        white-space: normal; /* Permettre le retour à la ligne */
    }

    .data-row {
        transition: var(--transition);
    }

    .data-row:hover {
        background-color: var(--primary-ultra-light);
    }

    /* User Info Cell */
    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        max-width: 100%;
    }



    /* Quiz Info */
    .quiz-info {
        display: flex;
        flex-direction: column;
        max-width: 100%;
    }

    .quiz-title {
        font-weight: 500;
    }

    /* Training Pill */
    .training-pill {
        background: var(--primary-ultra-light);
        color: var(--primary);
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        display: inline-block;
        font-weight: 500;
        max-width: 180px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Score Gauge */
    .score-gauge {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .gauge {
        height: 8px;
        width: 80px;
        background: #eee;
        border-radius: 4px;
        overflow: hidden;
    }

    .gauge-fill {
        height: 100%;
        border-radius: 4px;
    }

    .score-value {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .success-text {
        color: var(--success);
    }

    .danger-text {
        color: var(--danger);
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .status-badge.success {
        background-color: rgb(230, 240, 251);
        color: var(--success);
    }

    .status-badge.danger {
        background-color: rgba(248, 113, 113, 0.15);
        color: #b91c1c;
    }

    .status-badge.warning {
        background-color: rgba(251, 191, 36, 0.15);
        color: #92400e;
    }

    /* Date Info */
    .date-info {
        display: flex;
        flex-direction: column;
        max-width: 100%;
    }

    .date-day {
        font-weight: 500;
    }

    .date-time {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .actions-column {
        width: 100px; /* Largeur fixe pour la colonne d'actions */
    }

    .btn-action {
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: var(--transition);
        color: white;
        font-size: 0.9rem;
    }

    .btn-action:first-child {
        background-color: var(--primary);
    }

    .btn-action:first-child:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .delete-action {
        background-color: var(--danger);
    }

    .delete-action:hover {
        background-color: #e63946;
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    /* Empty State */
    .empty-state {
        padding: 2.5rem 1rem; /* Réduit de 3rem à 2.5rem */
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

    /* Pagination Styles */
    .pagination-wrapper {
        padding: 0.85rem 1.25rem; /* Réduit de 1rem 1.5rem à 0.85rem 1.25rem */
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

    /* Loading Overlay */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(4px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-spinner {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: white;
        padding: 2rem;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--border);
    }

    .spinner {
        width: 3rem;
        height: 3rem;
        border: 3px solid rgba(67, 97, 238, 0.3);
        border-radius: 50%;
        border-top-color: var(--primary);
        animation: spin 1s ease-in-out infinite;
        margin-bottom: 1rem;
    }

    .loading-text {
        font-weight: 500;
        color: var(--primary);
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }



    /* Responsive Adjustments */
    @media (max-width: 1200px) {
        .card-header-custom {
            flex-direction: column;
            align-items: flex-start;
        }

        .quick-stats {
            width: 100%;
            margin-top: 1rem;
            justify-content: flex-start;
        }

        .table-container {
            overflow-x: auto; /* Permettre le défilement uniquement sur petits écrans */
        }
    }

    @media (max-width: 992px) {
        .filter-row {
            flex-direction: column;
            gap: 1rem;
        }

        .filter-item {
            width: 100%;
        }

        .filter-actions {
            width: 100%;
            justify-content: flex-end;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
        }

        .header-icon {
            margin-right: 0;
            margin-bottom: 1rem;
        }
    }

    @media (max-width: 768px) {
        .quick-stats {
            flex-direction: column;
            width: 100%;
        }

        .stat-item {
            width: 100%;
        }

        .pagination-wrapper {
            flex-direction: column;
            align-items: center;
        }

        .evaluation-table th,
        .evaluation-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }

        .user-avatar {
            width: 2rem;
            height: 2rem;
            font-size: 0.8rem;
        }

        .training-pill {
            max-width: 120px;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideInFromLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .dashboard-card {
        animation: fadeIn 0.4s ease-out;
    }

    .data-row {
        animation: slideInFromLeft 0.3s ease-out forwards;
        opacity: 0;
    }

    .data-row:nth-child(1) { animation-delay: 0.1s; }
    .data-row:nth-child(2) { animation-delay: 0.15s; }
    .data-row:nth-child(3) { animation-delay: 0.2s; }
    .data-row:nth-child(4) { animation-delay: 0.25s; }
    .data-row:nth-child(5) { animation-delay: 0.3s; }
    .data-row:nth-child(6) { animation-delay: 0.35s; }
    .data-row:nth-child(7) { animation-delay: 0.4s; }
    .data-row:nth-child(8) { animation-delay: 0.45s; }
    .data-row:nth-child(9) { animation-delay: 0.5s; }
    .data-row:nth-child(10) { animation-delay: 0.55s; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animer les entrées du tableau
        const rows = document.querySelectorAll('.data-row');
        rows.forEach((row, index) => {
            row.style.animationDelay = `${0.1 + (index * 0.05)}s`;
        });

        // Gérer l'overlay de chargement
        const filterForm = document.querySelector('.filter-form');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const resetButton = document.querySelector('.reset-filters');

        if (filterForm && loadingOverlay) {
            filterForm.addEventListener('submit', function() {
                loadingOverlay.style.display = 'flex';
            });
        }

        if (resetButton && loadingOverlay) {
            resetButton.addEventListener('click', function() {
                loadingOverlay.style.display = 'flex';
            });
        }

        // Animations des éléments au défilement
        function animateOnScroll() {
            const elements = document.querySelectorAll('.filters-section, .table-container, .pagination-wrapper');

            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight;

                if (elementPosition < screenPosition) {
                    element.style.opacity = '1';
                    element.style.transform = 'translateY(0)';
                }
            });
        }

        // Initialiser les animations
        animateOnScroll();
        window.addEventListener('scroll', animateOnScroll);

        // Effet d'hovering pour les lignes du tableau
        const tableRows = document.querySelectorAll('.data-row');
        tableRows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = 'var(--shadow-sm)';
            });

            row.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            });
        });


    });
</script>
