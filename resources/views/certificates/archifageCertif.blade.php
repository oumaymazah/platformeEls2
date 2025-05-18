<div class="certification-container">
    <div class="section-header">
        <h4><i class="fas fa-certificate me-2"></i>Mes Certifications</h4>
        <p class="text-muted">Vos certifications et attestations obtenues</p>
    </div>


    @if($certifications->count() > 0)
        <div class="certification-grid">
            @foreach($certifications as $certification)
                <div class="certification-card" data-user="{{ auth()->id() }}" data-training="{{ $certification->training->id }}">
                    <div class="certification-icon">
                        <i class="fas fa-award"></i>
                    </div>

                    <div class="certification-content">
                        <h5>Formation : {{ $certification->training->title ?? 'Formation' }}</h5>
                        <div class="certification-details">
                            <span><i class="fas fa-calendar-alt me-1"></i> Obtenu le: {{ \Carbon\Carbon::parse($certification->created_at)->format('d/m/Y') }}</span>

                        </div>
                        <div class="certification-footer">
                            <button class="btn btn-sm btn-primary download-certificate" data-user="{{ auth()->id() }}" data-training="{{ $certification->training->id }}">
                                <i class="fas fa-download me-1"></i> Télécharger
                            </button>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="no-certification">
            <div class="empty-state">
                <i class="fas fa-certificate"></i>
                <h5>Aucune certification disponible</h5>
                <p>Vous n'avez pas encore obtenu de certification. Terminez vos formations pour obtenir des certificats.</p>
            </div>
        </div>
    @endif
</div>
<style>
.certification-container {
    padding: 15px 5px;
}



.section-header {
    position: relative;
    padding: 1.5rem;
    background: linear-gradient(135deg, #ffffff, #f9f9f9);
    border-left: 4px solid #4a6fdc;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin-bottom: 2rem;
    transition: all 0.3s ease;
}


.section-header h4 {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.section-header .fas {
    color: #4a6fdc;
    font-size: 1.1em;
}

.section-header p {
    margin-bottom: 0;
    font-size: 0.9rem;
    color: #7f8c8d;
}
.certification-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}

.certification-card {
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    display: flex;
    padding: 20px;
    border-left: 4px solid #4361ee;
}

.certification-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.certification-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #4361ee, #3f37c9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    flex-shrink: 0;
}

.certification-icon i {
    font-size: 24px;
    color: white;
}

.certification-content {
    flex-grow: 1;
}

.certification-content h5 {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
}

.certification-details {
    display: flex;
    flex-direction: column;
    gap: 5px;
    margin-bottom: 15px;
    font-size: 14px;
    color: #666;
}

.certification-footer {
    display: flex;
    justify-content: flex-end;
}

.download-certificate {
    background: #4361ee;
    border-color: #4361ee;
    transition: all 0.3s ease;
}

.download-certificate:hover {
    background: #3f37c9;
    border-color: #3f37c9;
}

.no-certification {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 200px;
}

.empty-state {
    text-align: center;
    padding: 30px;
    background: #f9f9f9;
    border-radius: 10px;
    width: 100%;
}

.empty-state i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 15px;
}

.empty-state h5 {
    font-weight: 600;
    margin-bottom: 10px;
    color: #555;
}

.empty-state p {
    color: #777;
    max-width: 400px;
    margin: 0 auto;
}

@media (max-width: 768px) {
    .certification-grid {
        grid-template-columns: 1fr;
    }
}
</style>
<script>
$(document).ready(function() {
    // Gérer le clic sur la carte de certification
    $('.certification-card').on('click', function() {
        const userId = $(this).data('user');
        const trainingId = $(this).data('training');

        window.location.href = `{{ route('certificates.download', ['user' => ':userId', 'training' => ':trainingId']) }}`
            .replace(':userId', userId)
            .replace(':trainingId', trainingId);
    });

    // Gérer le clic sur le bouton de téléchargement
    $('.download-certificate').on('click', function(e) {
        e.stopPropagation(); // Empêcher la propagation du clic au parent

        const userId = $(this).data('user');
        const trainingId = $(this).data('training');

        window.location.href = `{{ route('certificates.download', ['user' => ':userId', 'training' => ':trainingId']) }}`
            .replace(':userId', userId)
            .replace(':trainingId', trainingId);
    });
    // Éviter que le hover soit interrompu par le clic
    $('.download-certificate').hover(
        function() {
            $(this).addClass('hover-active');
        },
        function() {
            $(this).removeClass('hover-active');
        }
    );
});
</script>
