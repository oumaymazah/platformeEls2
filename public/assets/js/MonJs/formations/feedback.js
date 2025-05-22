// formation-feedbacks.js - Fonctions pour gérer l'affichage des feedbacks des formations

/**
 * Génère le HTML pour afficher la notation avec étoiles
 * @param {number} averageRating - Note moyenne (ex: 4.2)
 * @param {number} totalFeedbacks - Nombre total d'avis
 * @returns {string} - HTML pour l'affichage des étoiles
 */
function generateRatingStarsHtml(averageRating, totalFeedbacks) {
    // Valeurs par défaut
    averageRating = averageRating ? parseFloat(averageRating).toFixed(1) : '0.0';
    totalFeedbacks = totalFeedbacks || 0;
    
    let ratingStarsHtml = '';

    if (totalFeedbacks > 0) {
        // Afficher d'abord la note moyenne
        ratingStarsHtml = `<span class="rating-value">${averageRating}</span> `;
        
        // Calculer le nombre d'étoiles pleines, demi-étoiles et vides
        const ratingValue = parseFloat(averageRating);
        const fullStars = Math.floor(ratingValue);
        const decimal = parseFloat((ratingValue - fullStars).toFixed(1)); // partie décimale arrondie à 1 chiffre après la virgule
        
        // Logique selon les règles spécifiées:
        let hasHalfStar = false;
        let additionalFullStar = false;
        
        if (decimal > 0.2 && decimal < 0.8) {
            hasHalfStar = true;
        } else if (decimal >= 0.8) {
            additionalFullStar = true;
        }
        
        const totalFilledStars = fullStars + (additionalFullStar ? 1 : 0);
        const emptyStars = 5 - totalFilledStars - (hasHalfStar ? 1 : 0);
        
        // Générer les étoiles pleines
        for (let i = 0; i < fullStars; i++) {
            ratingStarsHtml += '<i class="fa fa-star text-warning"></i>';
        }
        
        // Ajouter une étoile supplémentaire si nécessaire (pour 0.8+)
        if (additionalFullStar) {
            ratingStarsHtml += '<i class="fa fa-star text-warning"></i>';
        }
        
        // Ajouter une demi-étoile si nécessaire (pour 0.3-0.7)
        if (hasHalfStar) {
            ratingStarsHtml += '<i class="fa fa-star-half-alt text-warning"></i>';
        }
        
        // Ajouter les étoiles vides
        for (let i = 0; i < emptyStars; i++) {
            ratingStarsHtml += '<i class="far fa-star text-muted"></i>';
        }
        
        // Ajouter le nombre d'avis entre parenthèses
        
        // Pour le débogage - afficher dans la console uniquement
        console.log(`Note: ${averageRating}, Étoiles pleines: ${totalFilledStars}, Demi-étoile: ${hasHalfStar ? 'Oui' : 'Non'}, Étoiles vides: ${emptyStars}`);
    } else {
        // Toujours afficher 5 étoiles vides même s'il n'y a pas d'avis
        ratingStarsHtml = '<span class="rating-value">0.0</span> ';
        for (let i = 0; i < 5; i++) {
            ratingStarsHtml += '<i class="far fa-star text-muted"></i>';
        }
    }
    
    return ratingStarsHtml;
}

/**
 * Ajoute les styles CSS nécessaires pour les étoiles
 */
function addRatingStyles() {
    // Vérifier si les styles sont déjà ajoutés
    if (document.getElementById('rating-styles')) {
        return;
    }
    
    const ratingStyle = `
        <style id="rating-styles">
            .formation-title {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                text-overflow: ellipsis;
                line-height: 1.2;
                max-height: 2.4em;
                height: auto;
                word-break: break-word;
                margin-bottom: 8px;
                font-size: 1rem !important
            }
            .rating-wrapper {
                display: flex;
                align-items: center;
            }
            .fa-star, .fa-star-half-alt, .far.fa-star {
                font-size: 14px;
                margin-right: 1px;
            }
            .text-warning {
                color: #ffc107 !important;
            }
            .text-muted {
                color: #6c757d !important;
            }
            .rating-value {
                font-weight: bold;
                margin-right: 3px;
            }
            .badge-light-warning {
                background-color: rgba(255, 193, 7, 0.15);
                color: #ffc107;
            }
        </style>
    `;
    $('head').append(ratingStyle);
    
    // S'assurer que Font Awesome est chargé pour les étoiles
    if ($('link[href*="font-awesome"]').length === 0) {
        $('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">');
    }
}

/**
 * Initialise le système de feedbacks
 */
function initFeedbackSystem() {
    addRatingStyles();
    
    // Si vous utilisez Feather icons au lieu de Font Awesome, adaptez les icônes
    if (typeof feather !== 'undefined' && $('.fa-star').length === 0) {
        console.log('Utilisation de Feather icons pour les étoiles');
        $('.rating-wrapper').each(function(){
            const ratingText = $(this).text();
            $(this).html(ratingText.replace(/fa-star/g, 'feather-star'));
        });
    }
}

// Exporter les fonctions pour pouvoir les utiliser dans d'autres fichiers
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        generateRatingStarsHtml,
        addRatingStyles,
        initFeedbackSystem
    };
}

// Initialiser automatiquement au chargement du document
$(document).ready(function() {
    initFeedbackSystem();
});