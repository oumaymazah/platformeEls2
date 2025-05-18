

document.addEventListener("DOMContentLoaded", function() {
    let ratingInput = document.getElementById('rating_count');
    let ratingDisplay = document.getElementById('rating-value');

    // Affichage par défaut vide
    ratingDisplay.textContent = "Pas de note";
    ratingInput.value = "";

    document.querySelectorAll('.star-half-left, .star-half-right').forEach(star => {
        star.addEventListener('click', function() {
            let rating = parseFloat(this.dataset.value);
            ratingInput.value = rating;
            ratingDisplay.textContent = rating.toFixed(1);
            updateStars(rating);
        });
    });

    function updateStars(rating) {
        document.querySelectorAll('.star-container').forEach(container => {
            let value = parseFloat(container.dataset.value);
            let starIcon = container.querySelector('i');

            starIcon.classList.remove('fa-star', 'fa-star-half-o');
            starIcon.classList.add('fa-star-o');

            if (rating >= value) {
                starIcon.classList.remove('fa-star-o', 'fa-star-half-o');
                starIcon.classList.add('fa-star');
            } else if (rating + 0.5 >= value && rating < value) {
                starIcon.classList.remove('fa-star-o', 'fa-star');
                starIcon.classList.add('fa-star-half-o');
            }
        });
    }

});





$(document).ready(function() {
    var feedbackTable = $('#feedback-table').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
        },
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Tous"]],
        dom: '<"top"fl>rt<"bottom"ip>',
        order: [[1, 'asc']]
    });

    // Fonction pour attacher les événements SweetAlert aux boutons de suppression
    function attachDeleteEvents() {
        $('.delete-button').each(function() {
            $(this).off('click').on('click', function(e) {
                e.preventDefault();
                const deleteUrl = $(this).data('delete-url');
                const csrf = $(this).data('csrf');

                Swal.fire({
                    title: 'Êtes-vous sûr?',
                    text: "Cette action est irréversible!",
                    icon: 'warning',
                    iconColor: '#f8bb86',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#9e9e9e',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Non, annuler',
                    buttonsStyling: true,
                    customClass: {
                        confirmButton: 'swal-confirm-button-no-border',
                        cancelButton: 'swal-cancel-button-no-border',
                        popup: 'swal-custom-popup'
                    },
                    background: '#ffffff',
                    backdrop: 'rgba(0,0,0,0.4)'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Créer un formulaire dynamique
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = deleteUrl;

                        // Ajouter le token CSRF
                        const csrfInput = document.createElement('input');
                        csrfInput.type = 'hidden';
                        csrfInput.name = '_token';
                        csrfInput.value = csrf;
                        form.appendChild(csrfInput);

                        // Ajouter la méthode DELETE
                        const methodInput = document.createElement('input');
                        methodInput.type = 'hidden';
                        methodInput.name = '_method';
                        methodInput.value = 'DELETE';
                        form.appendChild(methodInput);

                        // Soumettre le formulaire
                        document.body.appendChild(form);
                        form.submit();
                    }
                });
            });
        });
    }

    // Attacher les événements immédiatement après l'initialisation de DataTables
    attachDeleteEvents();

    // Réattacher les événements après chaque redraw de DataTables
    feedbackTable.on('draw', function() {
        attachDeleteEvents();
    });

    function updateSelectedCount() {
        var selectedCount = $('.feedback-checkbox:checked').length;
        $('#selected-count').text(selectedCount);
        $('#selected-count-badge').text(selectedCount);
        $('#bulk-delete-btn').prop('disabled', selectedCount === 0);
    }

    $('#select-all').on('click', function() {
        if(this.checked) {
            feedbackTable.rows().every(function() {
                var checkbox = $(this.node()).find('.feedback-checkbox')[0];
                if(checkbox) {
                    checkbox.checked = true;
                }
            });
        } else {
            feedbackTable.rows().every(function() {
                var checkbox = $(this.node()).find('.feedback-checkbox')[0];
                if(checkbox) {
                    checkbox.checked = false;
                }
            });
        }

        updateSelectedCount();
    });

    $(document).on('change', '.feedback-checkbox', function() {
        updateSelectedCount();

        // Vérifier si toutes les checkboxes sont cochées
        var allChecked = $('.feedback-checkbox:not(:checked)').length === 0;
        $('#select-all').prop('checked', allChecked && $('.feedback-checkbox').length > 0);
    });

    // Correction: S'assurer que les boutons pour fermer la modal fonctionnent
    $('.close, .btn-secondary').on('click', function() {
        $('#deleteModal').modal('hide');
    });

    // Modification de la gestion de la suppression groupée
    $('#bulk-delete-btn').on('click', function(event) {
        event.preventDefault();

        var selectedCount = $('.feedback-checkbox:checked').length;
        if (selectedCount === 0) return;

        Swal.fire({
            title: 'Confirmation de suppression multiple',
            text: 'Êtes-vous sûr de vouloir supprimer les ' + selectedCount + ' feedbacks sélectionnés ? Cette action est irréversible.',
            icon: 'warning',
            iconColor: '#f8bb86',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#9e9e9e',
            confirmButtonText: 'Oui, supprimer!',
            cancelButtonText: 'Non, annuler',
            buttonsStyling: true,
            customClass: {
                confirmButton: 'swal-confirm-button-no-border',
                cancelButton: 'swal-cancel-button-no-border',
                popup: 'swal-custom-popup'
            },
            background: '#ffffff',
            backdrop: 'rgba(0,0,0,0.4)'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#bulk-delete-form')[0].submit();
            }
        });
    });

    // Filtrer les lignes du tableau en fonction de la note sélectionnée
    $('#rate-filter').on('change', function() {
        var selectedRate = $(this).val();

        if (selectedRate === "") {
            feedbackTable.search('').columns().search('').draw();
        } else {
            feedbackTable.column(2).search(selectedRate).draw();
        }
    });

    // Initialiser les tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
