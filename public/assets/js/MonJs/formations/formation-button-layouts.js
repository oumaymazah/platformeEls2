
function updateButtonLayout() {
    // Sélectionner tous les conteneurs de boutons
    document.querySelectorAll('.addcart-btn').forEach(function(buttonContainer) {
        // Ajouter une classe pour le style flexbox
        buttonContainer.classList.add('flex-container');
        
        // Si le conteneur n'a pas déjà le style appliqué
        if (!buttonContainer.hasAttribute('data-styled')) {
            buttonContainer.setAttribute('data-styled', 'true');

            // Appliquer le style directement
            buttonContainer.style.display = 'flex';
            buttonContainer.style.gap = '10px';
            buttonContainer.style.width = '100%';
            
            // Trouver tous les boutons à l'intérieur
            const buttons = buttonContainer.querySelectorAll('.btn');
            buttons.forEach(function(btn) {
                btn.style.flex = '1';
                btn.style.whiteSpace = 'nowrap';
            });
        }
    });
}

/**
 * Initialise les fonctionnalités de mise en page des boutons
 */
function initButtonLayout() {
    // Ajouter les styles CSS
    addButtonStyles();
    
    // Appliquer le layout horizontal aux boutons existants après un court délai
    setTimeout(updateButtonLayout, 500);
}

// Initialiser au chargement du document
document.addEventListener('DOMContentLoaded', function() {
    initButtonLayout();
});
// Écouteur d'événements pour les modals

// Exporter les fonctions si utilisé avec un module bundler (webpack, etc.)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        updateButtonLayout,
        // addButtonStyles,
        initButtonLayout
    };
}
function addButtonStyles() {
    // Ajouter un style global pour les conteneurs de boutons
    const style = document.createElement('style');
    style.textContent = `
        .addcart-btn {
            display: flex !important;
            gap: 10px !important;
            width: 100% !important;
        }
        .addcart-btn .btn {
            flex: 1 !important;
            white-space: nowrap !important;
        }
         .badge-bleu {
            background-color:  #2B6ED4; /* Couleur bleue */
            color: #ffffff !important; /* Texte blanc forcé */
        }
        /* Correction pour éviter le décalage à droite lors de l'ouverture des modals */
        body {
            padding-right: 0 !important;
            overflow-y: scroll !important;
        }
        
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
        }
        
        .modal-open .modal {
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        /* Styles améliorés pour tous les rubans */
.ribbon {
    width: 110px !important; /* Largeur fixe plutôt que auto */
    min-width: 110px !important; /* Même valeur que width */
    max-width: 110px !important; /* Même valeur que width */
    text-align: center !important;
    padding: 3px 10px !important;
    box-sizing: border-box !important;
    height: 27px !important;
    line-height: 20px !important;
    overflow: hidden !important;
    white-space: nowrap !important; /* Empêche le texte de passer à la ligne */
    text-overflow: ellipsis !important; /* Ajoute des points de suspension si le texte est trop long */
}     
        /* Position du ruban "Complet" légèrement plus bas que le haut */
        .ribbon-danger {
            top: 15px !important;  /* Ajusté pour ne pas être tout en haut */
            right: 0 !important;
            left: auto !important;
        }
        
        /* Position du ruban "Gratuite" en dessous du "Complet" */
        .ribbon-warning {
            top: 50px !important;  /* Ajusté pour maintenir l'espace sous le "Complet" */
            right: 0 !important;
            left: auto !important;
        }
        
        /* Position du ruban pourcentage également en dessous du "Complet" */
        .ribbon-success {
            top: 50px !important;  /* Ajusté pour maintenir l'espace sous le "Complet" */
            right: 0 !important;
            left: auto !important;
        }
    `;
    document.head.appendChild(style);
}
// window.isFormationComplete = isFormationComplete;
// window.updateAddToCartButton = updateAddToCartButton;
// window.showFormationDetails = showFormationDetails;