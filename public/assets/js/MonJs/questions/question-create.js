document.addEventListener("DOMContentLoaded", function() {
    // Éléments DOM
    let responseCountInput = document.getElementById("response_count");
    let questionType = document.getElementById("question_type");
    let reponsesContainer = document.getElementById("reponses-container");
    let form = document.getElementById("question-form");
    let submitBtn = document.getElementById("submit-btn");
    let errorMessage = document.getElementById("error-message");
    let statementInput = document.getElementById("statement");
    let cancelBtn = document.querySelector('button[data-bs-dismiss="modal"]'); // Bouton Annuler
    let addQuestionModal = document.getElementById('addQuestionModal'); // Modal

    // Variable pour suivre si le formulaire a été soumis
    let formSubmitted = false;

    // Vérifier que tous les éléments sont présents
    if (!responseCountInput || !reponsesContainer || !form || !submitBtn || !errorMessage || !questionType) {
        console.error("Un ou plusieurs éléments du formulaire sont manquants.");
        return;
    }

    // Fonction pour générer les champs de réponse en fonction du nombre et du type
    function generateResponseFields() {
        const responseCount = parseInt(responseCountInput.value);
        const isSingleChoice = questionType.value === "single";
        const inputType = isSingleChoice ? "radio" : "checkbox";
        const inputName = isSingleChoice ? "correct_answer" : "correct_answers[]";

        reponsesContainer.innerHTML = "";

        for (let i = 0; i < responseCount; i++) {
            let reponseDiv = document.createElement("div");
            reponseDiv.classList.add("mb-3");

            reponseDiv.innerHTML = `
                <div class="input-group">
                    <div class="input-group-text">
                        <input type="${inputType}" name="${inputName}" value="${i}" class="form-check-input"
                            ${isSingleChoice && i === 0 ? 'checked' : ''}>
                    </div>
                    <input type="text" name="answers[]" class="form-control response-input"
                           placeholder="Réponse ${i + 1}" required>
                    <div class="invalid-feedback">Ce champ de réponse est obligatoire.</div>
                </div>
            `;

            reponsesContainer.appendChild(reponseDiv);
        }

        // Ajout des écouteurs pour supprimer les erreurs uniquement après soumission
        document.querySelectorAll(".response-input").forEach(input => {
            input.addEventListener("input", function() {
                if (formSubmitted && this.value.trim() !== "") {
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                }
            });
        });
    }

    // Fonction pour réinitialiser le formulaire complètement
    function resetForm() {
        // Réinitialiser la variable de soumission
        formSubmitted = false;

        // Réinitialisation du formulaire HTML
        form.reset();

        // Réinitialiser l'état de validation - supprimer toutes les classes
        if (statementInput) {
            statementInput.classList.remove("is-invalid", "is-valid");
            statementInput.value = "";
        }

        // Masquer le message d'erreur
        errorMessage.classList.add("d-none");
        errorMessage.innerHTML = "";

        // Réinitialiser les réponses avec les valeurs par défaut
        if (responseCountInput) {
            responseCountInput.value = responseCountInput.getAttribute("data-initial") || "4";
        }

        if (questionType) {
            questionType.value = "single";
        }

        // Regénérer les champs de réponse
        generateResponseFields();
    }

    // Initialisation au chargement
    generateResponseFields();

    // Écouteurs d'événements
    responseCountInput.addEventListener("change", generateResponseFields);
    questionType.addEventListener("change", generateResponseFields);

    // Ajout d'un écouteur pour le champ de titre - actif uniquement après soumission
    if (statementInput) {
        statementInput.addEventListener("input", function() {
            if (formSubmitted && this.value.trim() !== "") {
                this.classList.remove("is-invalid");
                this.classList.add("is-valid");
            }
        });
    }

    // Validation à la soumission
    submitBtn.addEventListener("click", function(event) {
        event.preventDefault();

        // Marquer le formulaire comme soumis
        formSubmitted = true;

        let isValid = true;
        let errorMessages = [];

        // Réinitialiser toutes les validations précédentes
        document.querySelectorAll(".is-valid, .is-invalid").forEach(element => {
            element.classList.remove("is-valid", "is-invalid");
        });

        // Valider l'énoncé
        if (statementInput) {
            if (statementInput.value.trim() === "") {
                isValid = false;
                statementInput.classList.add("is-invalid");
            } else {
                statementInput.classList.add("is-valid");
            }
        }

        // Vérifier si un quiz est sélectionné
        let hiddenQuizId = document.getElementById("hidden_quiz_id");
        if (!hiddenQuizId || hiddenQuizId.value === "") {
            isValid = false;
            errorMessages.push("Aucun quiz n'est sélectionné.");
        }

        // Vérifier qu'au moins une réponse est cochée comme correcte
        const isSingleChoice = questionType.value === "single";
        const selector = isSingleChoice ? 'input[type="radio"][name="correct_answer"]:checked' : 'input[type="checkbox"][name="correct_answers[]"]:checked';
        let checkedAnswers = form.querySelectorAll(selector);

        if (checkedAnswers.length === 0) {
            isValid = false;
            errorMessages.push("Veuillez marquer au moins une réponse comme correcte.");
        }

        // Vérifier que tous les champs de réponse sont remplis
        let responseFields = form.querySelectorAll('input[name="answers[]"]');
        responseFields.forEach(function(input) {
            if (input.value.trim() === '') {
                isValid = false;
                input.classList.add("is-invalid");
            } else {
                input.classList.add("is-valid");
            }
        });

        // Afficher les erreurs ou soumettre le formulaire
        if (!isValid && errorMessages.length > 0) {
            errorMessage.innerHTML = errorMessages.join("<br>");
            errorMessage.classList.remove("d-none");

            // Masquer le message d'erreur après 3 secondes
            setTimeout(function() {
                errorMessage.classList.add("d-none");
            }, 3000);
        } else if (isValid) {
            form.submit();
        }
    });

    // NOUVELLES PARTIES POUR RÉINITIALISER LE FORMULAIRE

    // 1. Réinitialiser quand on clique sur Annuler
    if (cancelBtn) {
        cancelBtn.addEventListener("click", resetForm);
    }

    // 2. Réinitialiser quand on ferme la modal (en cliquant hors de la modal ou sur X)
    if (addQuestionModal) {
        addQuestionModal.addEventListener('hidden.bs.modal', resetForm);
    }

    // 3. Réinitialiser quand on ouvre la modal
    if (addQuestionModal) {
        addQuestionModal.addEventListener('show.bs.modal', resetForm);
    }
});
