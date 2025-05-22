/**
 * Gère l'affichage des descriptions avec des boutons "Voir plus" et "Voir moins"
 * pour les descriptions longues dans les modals de formation
 */
document.addEventListener('DOMContentLoaded', function() {
    initDescriptionToggle();
    
    // Initialiser également lors de l'ouverture d'un modal
    document.addEventListener('shown.bs.modal', function(event) {
        if (event.target.id && event.target.id.startsWith('formation-modal-')) {
            initDescriptionToggleInModal(event.target);
        }
    });
});

/**
 * Initialise les toggles de description pour tous les éléments existants
 */
function initDescriptionToggle() {
    // Sélectionner toutes les descriptions dans les modals de formation
    const descriptionContainers = document.querySelectorAll('.formation-description');
    
    descriptionContainers.forEach(container => {
        setupDescriptionToggle(container);
    });
}

/**
 * Initialise les toggles de description spécifiquement dans un modal
 * @param {HTMLElement} modal - L'élément modal
 */
function initDescriptionToggleInModal(modal) {
    const descriptionContainer = modal.querySelector('.formation-description');
    if (descriptionContainer) {
        setupDescriptionToggle(descriptionContainer);
    }
}

/**
 * Configure le toggle pour un conteneur de description spécifique
 * @param {HTMLElement} container - Le conteneur de description
 */
function setupDescriptionToggle(container) {
    // Vérifier si déjà configuré
    if (container.hasAttribute('data-toggle-initialized')) {
        return;
    }
    
    // Marquer comme initialisé
    container.setAttribute('data-toggle-initialized', 'true');
    
    const fullText = container.textContent || container.innerText;
    const maxLines = 4;
    const lineHeight = parseInt(window.getComputedStyle(container).lineHeight) || 20;
    const maxHeight = maxLines * lineHeight;
    
    // Stocker le texte complet dans un attribut data
    container.setAttribute('data-full-text', fullText);
    
    // Vérifier si le texte dépasse la hauteur maximale
    container.style.maxHeight = 'none'; // Temporairement enlever la limite pour mesurer
    const actualHeight = container.scrollHeight;
    
    if (actualHeight > maxHeight) {
        // Limiter la hauteur et ajouter les boutons
        container.style.maxHeight = maxHeight + 'px';
        container.style.overflow = 'hidden';
        container.style.position = 'relative';
        container.style.transition = 'max-height 0.3s ease';
        
        // Créer le bouton "Voir plus"
        const seeMoreBtn = document.createElement('button');
        seeMoreBtn.textContent = 'Voir plus';
        seeMoreBtn.className = 'btn btn-link text-primary p-0 see-more-btn';
        seeMoreBtn.style.marginLeft = '5px';
        
        // Créer le bouton "Voir moins"
        const seeLessBtn = document.createElement('button');
        seeLessBtn.textContent = 'Voir moins';
        seeLessBtn.className = 'btn btn-link text-primary p-0 see-less-btn';
        seeLessBtn.style.marginLeft = '5px';
        seeLessBtn.style.display = 'none';
        
        // Ajouter les boutons après le conteneur
        const buttonsContainer = document.createElement('div');
        buttonsContainer.className = 'description-toggle-buttons';
        buttonsContainer.style.marginTop = '5px';
        buttonsContainer.appendChild(seeMoreBtn);
        buttonsContainer.appendChild(seeLessBtn);
        
        container.parentNode.insertBefore(buttonsContainer, container.nextSibling);
        
        // Ajouter les listeners d'événements
        seeMoreBtn.addEventListener('click', function() {
            container.style.maxHeight = actualHeight + 'px';
            seeMoreBtn.style.display = 'none';
            seeLessBtn.style.display = 'inline-block';
        });
        
        seeLessBtn.addEventListener('click', function() {
            container.style.maxHeight = maxHeight + 'px';
            seeMoreBtn.style.display = 'inline-block';
            seeLessBtn.style.display = 'none';
        });
    }
}

/**
 * Réinitialise le toggle pour un conteneur de description spécifique
 * (utile lors des mises à jour dynamiques)
 * @param {HTMLElement} container - Le conteneur de description
 */
function resetDescriptionToggle(container) {
    // Supprimer l'attribut d'initialisation
    container.removeAttribute('data-toggle-initialized');
    
    // Supprimer les boutons existants
    const buttonsContainer = container.parentNode.querySelector('.description-toggle-buttons');
    if (buttonsContainer) {
        buttonsContainer.remove();
    }
    
    // Réinitialiser les styles
    container.style.maxHeight = '';
    container.style.overflow = '';
    
    // Réappliquer le toggle
    setupDescriptionToggle(container);
}

// Rendre les fonctions disponibles globalement
window.initDescriptionToggle = initDescriptionToggle;
window.setupDescriptionToggle = setupDescriptionToggle;
window.resetDescriptionToggle = resetDescriptionToggle;