// Fonction pour afficher les détails d'une question
function showQuestionDetails(questionIndex) {
    // Supprimer la classe active de toutes les cartes
    document.querySelectorAll('.question-card').forEach(function(card) {
        card.classList.remove('active');
    });

    // Ajouter la classe active à la carte cliquée
    document.getElementById('question-card-' + questionIndex).classList.add('active');

    // Récupérer le contenu du template
    const template = document.getElementById('question-details-' + questionIndex);
    const container = document.getElementById('questionDetailsContainer');

    // Afficher les détails de la question
    if (template && container) {
        container.innerHTML = template.innerHTML;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-quiz-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('.delete-quiz-form');

            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Vous êtes sur le point de supprimer ce quiz. Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Attacher l'événement aux boutons de suppression
    document.querySelectorAll('.delete-question-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const deleteUrl = this.getAttribute('data-delete-url');
            const csrfToken = this.getAttribute('data-csrf');

            Swal.fire({
                title: 'Êtes-vous sûr?',
                text: "Cette action est irréversible!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2B6ED4',
                cancelButtonColor: '#ea553d',
                confirmButtonText: 'Oui, supprimer!',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Créer et soumettre le formulaire de suppression
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = deleteUrl;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });
    });
});
