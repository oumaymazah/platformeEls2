/**
 * Traite la réponse HTTP et vérifie si elle est OK
 * @param {Response} response - La réponse HTTP
 * @return {Promise<Object>} - Promesse résolue avec le contenu JSON de la réponse
 */
function handleResponse(response) {
    if (!response.ok) {
        throw new Error('Erreur réseau');
    }
    return response.json();
}

/**
 * Vérifier si des formations ont la même date de début
 * @return {Promise<Object>} - Promesse résolue avec les informations sur les formations ayant la même date
 */
function checkSameDateFormations() {
    return fetch('/panier/details', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(handleResponse)
    .then(data => {
        if (!data.success || !data.trainings || data.trainings.length === 0) {
            return { hasSameDates: false };
        }
        
        // Grouper les formations par date de début
        const formationsByDate = {};
        data.trainings.forEach(training => {
            if (training.start_date) {
                if (!formationsByDate[training.start_date]) {
                    formationsByDate[training.start_date] = [];
                }
                formationsByDate[training.start_date].push(training);
            }
        });
        
        // Trouver les dates qui ont plus d'une formation
        const sameDateFormations = {};
        Object.keys(formationsByDate).forEach(date => {
            if (formationsByDate[date].length > 1) {
                sameDateFormations[date] = formationsByDate[date];
            }
        });
        
        const hasSameDates = Object.keys(sameDateFormations).length > 0;
        
        return {
            hasSameDates,
            sameDateFormations
        };
    });
}

/**
 * Affiche une alerte personnalisée pour les formations avec la même date
 * @param {Object} sameDateFormations - Les formations regroupées par date commune
 * @return {Promise<boolean>} - Promesse résolue avec la décision de l'utilisateur (true pour réserver quand même)
 */
function showSameDateAlert(sameDateFormations) {
    return new Promise((resolve) => {
        // Créer l'élément de fond modal avec animation de fondu
        const modalBackground = document.createElement('div');
        modalBackground.className = 'modal-background';
        modalBackground.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;
        
        // Créer la boîte de dialogue modale avec animation
        const modalBox = document.createElement('div');
        modalBox.className = 'modal-box';
        modalBox.style.cssText = `
            background-color: white;
            padding: 24px;
            border-radius: 10px;
            max-width: 550px;
            width: 90%;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.3);
            transform: translateY(-20px);
            transition: transform 0.3s ease;
            position: relative;
        `;
        
        // Ajouter un bouton de fermeture en haut à droite
        const closeButton = document.createElement('button');
        closeButton.innerHTML = '&times;';
        closeButton.style.cssText = `
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            padding: 0;
            line-height: 1;
            height: 24px;
            width: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        `;
        closeButton.addEventListener('click', () => {
            closeModal();
            resolve(false);
        });
        
        // Icône d'avertissement
        const warningIcon = document.createElement('div');
        warningIcon.innerHTML = `
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 9V14M12 17.5V18M4.9522 19H19.0478C20.6913 19 21.9052 17.3921 21.1474 15.8922L14.0996 3.55761C13.3419 2.05771 11.2581 2.05771 10.5004 3.55761L3.45264 15.8922C2.69485 17.3921 3.90867 19 5.55219 19H19.0478H4.9522Z" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        `;
        warningIcon.style.cssText = `
            margin-bottom: 12px;
            display: flex;
            justify-content: center;
            color: #CE2029;
        `;
        
        // Titre de l'alerte
        const title = document.createElement('h3');
        title.textContent = 'Attention : Conflit de planning détecté';
        title.style.cssText = `
            margin-top: 0;
            color: #CE2029;
            font-size: 20px;
            text-align: center;
            margin-bottom: 16px;
            font-weight: 600;
        `;
        
        // Contenu de l'alerte
        const content = document.createElement('div');
        content.className = 'modal-content';
        content.style.cssText = `
            margin-bottom: 24px;
            max-height: 350px;
            overflow-y: auto;
            padding-right: 8px;
        `;
        
        let contentHTML = `
            <p style="margin-bottom: 16px; text-align: center; color: #555; line-height: 1.5;">
                Nous avons détecté que les formations suivantes sont programmées le même jour, ce qui pourrait créer un conflit dans votre emploi du temps.
            </p>
        `;
        
        Object.keys(sameDateFormations).forEach(date => {
            const formattedDate = new Date(date).toLocaleDateString('fr-FR', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            
            contentHTML += `
                <div style="background-color: #f8f9fa; border-radius: 8px; padding: 16px; margin-bottom: 16px; border-left: 4px solid #e74c3c;">
                    <p style="font-weight: 600; margin-top: 0; margin-bottom: 12px; color: #333;">
                        <span style="display: inline-flex; align-items: center; gap: 6px;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <rect x="3" y="4" width="18" height="18" rx="2" stroke="#e74c3c" stroke-width="2" />
                                <path d="M3 10H21" stroke="#e74c3c" stroke-width="2" />
                                <path d="M16 2V6" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" />
                                <path d="M8 2V6" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            ${formattedDate}
                        </span>
                    </p>
                    <ul style="padding-left: 0; margin: 0; list-style-type: none;">
            `;
            
            sameDateFormations[date].forEach(formation => {
                contentHTML += `
                    <li style="padding: 8px 0; border-bottom: 1px solid #e0e0e0; display: flex; align-items: center; gap: 10px;">
                        <span style="color: #CE2029; flex-shrink: 0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 6L6 18M6 6L18 18" stroke="#e74c3c" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span style="color: #333; font-weight: 500;">${formation.title}</span>
                    </li>
                `;
            });
            
            contentHTML += `
                    </ul>
                </div>
            `;
        });
        
        contentHTML += `
            <p style="color:rgb(14, 16, 18); text-align: center; margin-bottom: 0;">
                Voulez-vous quand même réserver ces formations malgré le conflit d'horaires ?
            </p>
        `;
        
        content.innerHTML = contentHTML;
        
        // Conteneur pour les boutons
        const buttonContainer = document.createElement('div');
        buttonContainer.style.cssText = `
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 8px;
        `;
        
        // Bouton d'annulation
        const cancelButton = document.createElement('button');
        cancelButton.textContent = 'Annuler';
        cancelButton.style.cssText = `
            padding: 10px 20px;
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            min-width: 120px;
            transition: background-color 0.2s;
            outline: none;
        `;
        cancelButton.addEventListener('mouseover', () => {
            cancelButton.style.backgroundColor = '#f5f5f5';
        });
        cancelButton.addEventListener('mouseout', () => {
            cancelButton.style.backgroundColor = '#f5f5f5';
        });
        cancelButton.addEventListener('click', () => {
            closeModal();
            resolve(false);
        });
        
        // Bouton pour réserver quand même
        const confirmButton = document.createElement('button');
        confirmButton.textContent = 'Réserver quand même';
        confirmButton.style.cssText = `
            padding: 10px 20px;
            background-color: #2B6ED4;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            min-width: 180px;
            transition: background-color 0.2s;
            outline: none;
        `;
        confirmButton.addEventListener('mouseover', () => {
            confirmButton.style.backgroundColor = ' #2B6ED4';
        });
        confirmButton.addEventListener('mouseout', () => {
            confirmButton.style.backgroundColor = ' #2B6ED4';
        });
        confirmButton.addEventListener('click', () => {
            closeModal();
            resolve(true);
        });
        
        // Fonction pour fermer le modal avec animation
        function closeModal() {
            modalBackground.style.opacity = '0';
            modalBox.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                document.body.removeChild(modalBackground);
            }, 300);
        }
        
        // Ajouter les éléments au DOM
        buttonContainer.appendChild(cancelButton);
        buttonContainer.appendChild(confirmButton);
        
        modalBox.appendChild(closeButton);
        modalBox.appendChild(warningIcon);
        modalBox.appendChild(title);
        modalBox.appendChild(content);
        modalBox.appendChild(buttonContainer);
        
        modalBackground.appendChild(modalBox);
        document.body.appendChild(modalBackground);
        
        // Déclencher l'animation d'entrée
        setTimeout(() => {
            modalBackground.style.opacity = '1';
            modalBox.style.transform = 'translateY(0)';
        }, 10);
    });
}

/**
 * Nouvelle version du gestionnaire de clic pour le bouton de réservation
 * qui vérifie uniquement les dates communes
 * @param {Event} e - L'événement de clic
 * @param {Function} processReservationCallback - La fonction à appeler pour traiter la réservation
 */
// 1. Correction de la fonction validatedReservationClick dans reservation-validation.js
function validatedReservationClick(e, processReservationCallback) {
    e.preventDefault();
    e.stopPropagation(); // Empêcher la propagation de l'événement
    
    // Récupérer le bouton et son texte original pour la restauration en cas d'annulation
    const reservButton = e.currentTarget;
    const originalText = reservButton.innerHTML;
    
    // Changer le texte du bouton immédiatement
    reservButton.innerHTML = 'Réservation en cours...';
    reservButton.disabled = true;
    
    // Vérifier uniquement les formations qui ont la même date
    checkSameDateFormations()
        .then(result => {
            if (result.hasSameDates) {
                // Montrer l'alerte et attendre la décision de l'utilisateur
                return showSameDateAlert(result.sameDateFormations).then(shouldProceed => {
                    if (shouldProceed) {
                        // L'utilisateur a choisi de réserver malgré les dates communes
                        processReservationCallback();
                    } else {
                        // L'utilisateur a annulé, restaurer l'état du bouton
                        reservButton.disabled = false;
                        reservButton.innerHTML = originalText;
                    }
                    return Promise.resolve();
                });
            } else {
                // Aucune formation avec la même date, procéder directement à la réservation
                processReservationCallback();
                return Promise.resolve();
            }
        })
        .catch(error => {
            console.error('Erreur lors de la validation de la réservation:', error);
            // En cas d'erreur, restaurer également l'état du bouton
            reservButton.disabled = false;
            reservButton.innerHTML = originalText;
        });
}

// Exposer les fonctions nécessaires globalement
window.checkSameDateFormations = checkSameDateFormations;
window.showSameDateAlert = showSameDateAlert;
window.validatedReservationClick = validatedReservationClick;