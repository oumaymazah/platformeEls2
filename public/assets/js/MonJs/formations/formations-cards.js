
/**Calcule les informations relatives aux places disponibles d'une formation
 * @param {Object} formation - Objet contenant les données de la formation
 * @returns {Object} Objet contenant le nombre total de places, places restantes, places occupées et taux d'occupation
 */
function calculateSeatsInfo(formation) {
    const totalSeats = formation.total_seats || 0;
    const remainingSeats = formation.remaining_seats !== undefined ? formation.remaining_seats : totalSeats;
    const occupiedSeats = totalSeats - remainingSeats;

    // Calculer le pourcentage d'occupation pour la barre de progression
    let occupancyRate = totalSeats > 0 ? Math.round((occupiedSeats / totalSeats) * 100) : 0;

    // S'assurer que les cours complets ont une barre à 100%
    if (remainingSeats === 0 && totalSeats > 0) {
        occupancyRate = 100;
    }

    // Déterminer la classe de couleur pour la barre de progression
    let progressClass = 'bg-bleu';
    if (occupancyRate > 75) {
        progressClass = 'bg-danger';
    } else if (occupancyRate > 50) {
        progressClass = 'bg-warning';
    }

    return {
        totalSeats,
        remainingSeats,
        occupiedSeats,
        occupancyRate,
        progressClass,
        isComplete: remainingSeats === 0 && totalSeats > 0
    };
}
/**
 * Empêche le décalage de la page lors de l'ouverture des modals
 */
function preventPageShift() {
    // Calculer la largeur de la barre de défilement
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;

    // Appliquer un padding compensatoire au conteneur principal si nécessaire
    document.body.style.paddingRight = '0px';

    // Ajouter un style pour garder la mise en page stable
    const style = document.createElement('style');
    style.id = 'prevent-shift-style';
    style.textContent = `
        body.modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
            width: 100% !important;
        }
    `;

    // Supprimer l'ancien style s'il existe
    const oldStyle = document.getElementById('prevent-shift-style');
    if (oldStyle) oldStyle.remove();

    // Ajouter le nouveau style
    document.head.appendChild(style);
}

/**
 * Vérifie si une formation est complète en se basant sur les données du backend
 * @param {string|number} formationId - ID de la formation à vérifier
 * @param {boolean} forceRefresh - Force une nouvelle vérification auprès du serveur
 * @returns {Promise<boolean>} Promise résolvant à true si la formation est complète, false sinon
 */
function isFormationComplete(formationId, forceRefresh = true) {
    console.log(`Vérification de l'état de formation pour ID: ${formationId}, forceRefresh: ${forceRefresh}`);

    // Si on ne force pas le rafraîchissement, on peut vérifier le cache
    if (!forceRefresh && window.formationsData && window.formationsData[formationId]) {
        const formationData = window.formationsData[formationId];
        if (formationData.is_complete !== undefined) {
            console.log(`Données en cache pour formation #${formationId}: is_complete=${formationData.is_complete}`);
            return Promise.resolve(formationData.is_complete);
        }
    }

    // Appeler l'API backend, en ajoutant un timestamp pour éviter le cache du navigateur
    const timestamp = new Date().getTime();
    const url = `/get-remaining-seats/${formationId}?_=${timestamp}`;
    console.log(`Appel API pour vérifier les places de formation #${formationId} à ${url}`);

    return fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Réponse API reçue:", JSON.stringify(data));

            // Vérifier si la réponse contient directement is_complete
            let isComplete = false;

            if (data.is_complete !== undefined) {
                // Utiliser la valeur is_complete directement si disponible
                isComplete = Boolean(data.is_complete);
                console.log(`is_complete depuis API: ${data.is_complete}, converti en: ${isComplete}`);
            } else if (data.remaining_seats !== undefined && data.total_seats !== undefined) {
                // Sinon calculer is_complete sur base des places disponibles
                isComplete = (parseInt(data.remaining_seats) === 0 && parseInt(data.total_seats) > 0);
                console.log(`Calcul is_complete: remaining_seats=${data.remaining_seats}, total_seats=${data.total_seats}, resultat=${isComplete}`);
            }

            console.log(`Formation #${formationId} est complète: ${isComplete}`);

            // Mettre en cache le résultat
            if (!window.formationsData) window.formationsData = {};
            window.formationsData[formationId] = {
                is_complete: isComplete,
                total_seats: parseInt(data.total_seats) || 0,
                remaining_seats: parseInt(data.remaining_seats) || 0
            };

            return isComplete;
        })
        .catch(error => {
            console.error("Erreur lors de la vérification des places disponibles:", error);
            return false; // Par défaut, considérer que la formation n'est pas complète
        });
}


/**
 * Met à jour l'affichage des boutons d'ajout au panier
 * @param {string|number} formationId - ID de la formation
 * @param {boolean} inCart - Indique si la formation est dans le panier
 * @param {boolean} isComplete - Indique si la formation est complète
 */
function updateAddToCartButton(formationId, inCart, isComplete) {
    console.log(`Mise à jour bouton pour formation #${formationId}: inCart=${inCart}, isComplete=${isComplete}`);

    // Sélectionner tous les boutons correspondant à cette formation (cartes et modals)
    const buttons = document.querySelectorAll(`.formation-item[data-id="${formationId}"] .addcart-btn .btn[href="/panier"],
                                          .formation-item[data-formation-id="${formationId}"] .addcart-btn .btn[href="/panier"],
                                          .formation-item[data-category-id="${formationId}"] .addcart-btn .btn[href="/panier"],
                                          #formation-modal-${formationId} .addcart-btn .btn[href="/panier"]`);

    if (buttons.length === 0) {
        console.warn(`Aucun bouton trouvé pour la formation #${formationId}`);
        return;
    }

    buttons.forEach((button, index) => {
        console.log(`Traitement du bouton ${index + 1}/${buttons.length} pour formation #${formationId}`);

        // Réinitialiser complètement les classes et attributs du bouton
        button.classList.remove('btn-secondary', 'disabled');
        button.classList.add('btn-primary');
        button.disabled = false;

        // Mettre à jour le texte et les attributs en fonction de l'état
        if (inCart) {
            console.log(`Formation #${formationId} dans le panier. Configuration du bouton en "Accéder au panier"`);
            button.textContent = 'Accéder au panier';
            button.setAttribute('data-in-cart', 'true');
        } else if (isComplete) {
            console.log(`Formation #${formationId} complète. Configuration du bouton en "FORMATION COMPLETE"`);
            button.textContent = 'FORMATION COMPLETE';
            button.classList.remove('btn-primary');
            button.classList.add('btn-secondary', 'disabled');
            button.disabled = true;
            button.removeAttribute('data-in-cart');
        } else {
            console.log(`Formation #${formationId} disponible. Configuration du bouton en "Ajouter au panier"`);
            button.textContent = 'Ajouter au panier';
            button.removeAttribute('data-in-cart');
        }

        // Vérifier l'état final du bouton
        console.log(`État final du bouton ${index + 1}: texte="${button.textContent}", disabled=${button.disabled}, classes="${button.className}"`);
    });
}

/**
 * Affiche les détails d'une formation dans un modal
 * @param {string|number} formationId - ID de la formation
 */

function showFormationDetails(formationId) {
    console.log('Démarrage de showFormationDetails pour formation ID:', formationId);

    // IMPORTANT: Vérifier si cette formation est dans le panier AVANT d'afficher le modal
    const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
    const inCart = cartFormations.includes(formationId.toString());

    // IMPORTANT: Pré-configurer les boutons selon le statut de panier AVANT d'afficher le modal
    const modalButtons = document.querySelectorAll(`#formation-modal-${formationId} .addcart-btn .btn[href="/panier"]`);
    modalButtons.forEach(button => {
        if (inCart) {
            // Immédiatement mettre à jour le bouton si déjà dans le panier
            button.textContent = 'Accéder au panier';
            button.setAttribute('data-in-cart', 'true');
        }
    });

    // Nettoyer d'abord tout backdrop existant
    const existingBackdrops = document.querySelectorAll('.modal-backdrop');
    existingBackdrops.forEach(backdrop => backdrop.remove());

    // Afficher le modal
    const modal = document.getElementById(`formation-modal-${formationId}`);
    if (modal) {
        modal.classList.remove('show');
        modal.style.display = 'none';
        document.body.classList.remove('modal-open');

        const bsModal = new bootstrap.Modal(modal, {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        bsModal.show();
    } else {
        console.error(`Modal pour la formation #${formationId} non trouvé`);
        return;
    }

    // PUIS vérifier l'état complet/non-complet auprès du serveur
    isFormationComplete(formationId, true)
        .then(isComplete => {
            console.log('Formation ID:', formationId, 'inCart:', inCart, 'isComplete:', isComplete);
            // Mettre à jour le bouton avec l'état complet/non-complet
            // Mais ne pas modifier l'état "dans le panier" qui est déjà correct
            updateAddToCartButton(formationId, inCart, isComplete);
        })
        .catch(error => {
            console.error('Erreur lors de la vérification de l\'état de la formation:', error);
        });
}


function refreshAllFormationsStatus() {
    console.log("Rafraîchissement de l'état de toutes les formations");

    // Réinitialiser complètement le cache des formations
    window.formationsData = {};

    // Trouver toutes les formations affichées
    const formationItems = document.querySelectorAll('.formation-item[data-id]');
    console.log(`Nombre de formations trouvées: ${formationItems.length}`);

    formationItems.forEach(item => {
        const formationId = item.getAttribute('data-id');
        if (!formationId) return;

        // Vérifier l'état de chaque formation auprès du serveur
        isFormationComplete(formationId, true)
            .then(isComplete => {
                // Vérifier si cette formation est dans le panier
                const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
                const inCart = cartFormations.includes(formationId.toString());

                // Mettre à jour l'affichage
                updateAddToCartButton(formationId, inCart, isComplete);

                // Mettre à jour l'attribut data
                item.setAttribute('data-is-complete', isComplete);

                // Mise à jour visuelle (ruban "Complète" si nécessaire)
                updateCompleteBadge(item, isComplete);
            });
    });
}

/**
 * Met à jour l'affichage du ruban "Complète" sur une carte de formation
 * @param {HTMLElement} formationElement - Élément DOM de la formation
 * @param {boolean} isComplete - Indique si la formation est complète
 */
function updateCompleteBadge(formationElement, isComplete) {
    // Trouver la div product-img à l'intérieur de cet élément
    const productImg = formationElement.querySelector('.product-img');
    if (!productImg) return;

    // Vérifier si le ruban "Complète" existe déjà
    let ribbon = productImg.querySelector('.ribbon-danger');

    if (isComplete) {
        // Si la formation est complète et qu'il n'y a pas de ruban, l'ajouter
        if (!ribbon) {
            ribbon = document.createElement('div');
            ribbon.className = 'ribbon ribbon-danger';
            ribbon.textContent = 'Complète';
            productImg.appendChild(ribbon);
        }
    } else {
        // Si la formation n'est pas complète mais qu'il y a un ruban, le supprimer
        if (ribbon) {
            ribbon.remove();
        }
    }
}

/**
 * Met à jour le bouton d'ajout au panier selon l'état de la formation
 * @param {string|number} formationId - ID de la formation
 * @param {boolean} inCart - Indique si la formation est déjà dans le panier
 * @param {boolean} isComplete - Indique si la formation est complète
 */
// function updateAddToCartButton(formationId, inCart, isComplete) {
//     console.log(`Mise à jour bouton pour formation #${formationId}: inCart=${inCart}, isComplete=${isComplete}`);

//     // Sélectionner tous les boutons concernant cette formation
//     const buttons = document.querySelectorAll(`.formation-item[data-id="${formationId}"] .addcart-btn .btn[href="/panier"],
//                                              .formation-item[data-formation-id="${formationId}"] .addcart-btn .btn[href="/panier"],
//                                              .formation-item[data-category-id="${formationId}"] .addcart-btn .btn[href="/panier"],
//                                              #formation-modal-${formationId} .addcart-btn .btn[href="/panier"]`);

//     if (buttons.length === 0) {
//         console.warn(`Aucun bouton trouvé pour la formation #${formationId}`);
//         return;
//     }

//     buttons.forEach(button => {
//         // Réinitialiser d'abord les classes et attributs
//         button.classList.remove('btn-secondary', 'disabled');
//         button.classList.add('btn-primary');
//         button.disabled = false;

//         if (inCart) {
//             // Si déjà dans le panier: "Accéder au panier" peu importe si la formation est complète
//             button.textContent = 'Accéder au panier';
//             button.setAttribute('data-in-cart', 'true');
//         } else if (isComplete) {
//             // Si pas dans le panier mais formation complète: désactiver le bouton
//             button.textContent = 'FORMATION COMPLETE';
//             button.classList.remove('btn-primary');
//             button.classList.add('btn-secondary', 'disabled');
//             button.disabled = true;
//             button.removeAttribute('data-in-cart');
//         } else {
//             // Formation disponible et pas dans le panier
//             button.textContent = 'Ajouter au panier';
//             button.removeAttribute('data-in-cart');
//         }
//     });
// }

/**
 * Affiche les détails d'une formation dans un modal
 * @param {string|number} formationId - ID de la formation
 */
// function showFormationDetails(formationId) {
//     // Vérifier d'abord si cette formation est dans le panier
//     const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
//     const inCart = cartFormations.includes(formationId.toString());

//     console.log('Démarrage de showFormationDetails pour formation ID:', formationId);

//     // Nettoyer d'abord tout backdrop existant qui pourrait causer des problèmes
//     const existingBackdrops = document.querySelectorAll('.modal-backdrop');
//     existingBackdrops.forEach(backdrop => backdrop.remove());

//     // Afficher d'abord le bouton en état de chargement si nécessaire
//     const modalButtons = document.querySelectorAll(`#formation-modal-${formationId} .addcart-btn .btn[href="/panier"]`);
//     modalButtons.forEach(button => {
//         // Optionnel: Afficher un état de chargement
//         if (!inCart) {
//             button.textContent = 'Chargement...';
//         }
//     });

//     // Vérifier si la formation est complète via l'API backend
//     isFormationComplete(formationId)
//         .then(isComplete => {
//             console.log('Formation ID:', formationId, 'inCart:', inCart, 'isComplete:', isComplete);

//             // Mettre à jour le bouton APRÈS avoir reçu l'information
//             updateAddToCartButton(formationId, inCart, isComplete);

//             // Afficher le modal s'il existe
//             const modal = document.getElementById(`formation-modal-${formationId}`);
//             if (modal) {
//                 // S'assurer que le modal est dans l'état correct avant de l'ouvrir
//                 modal.classList.remove('show');
//                 modal.style.display = 'none';
//                 document.body.classList.remove('modal-open');

//                 // Créer et configurer le modal avec des options explicites
//                 const bsModal = new bootstrap.Modal(modal, {
//                     backdrop: true,
//                     keyboard: true,
//                     focus: true
//                 });

//                 // Ouvrir le modal
//                 bsModal.show();
//             } else {
//                 console.error(`Modal pour la formation #${formationId} non trouvé`);
//             }
//         })
//         .catch(error => {
//             console.error('Erreur lors de la vérification de l\'état de la formation:', error);

//             // En cas d'erreur, afficher quand même le modal avec un état par défaut
//             const modal = document.getElementById(`formation-modal-${formationId}`);
//             if (modal) {
//                 // Mettre à jour le bouton avec l'état connu (seulement inCart)
//                 updateAddToCartButton(formationId, inCart, false);
//                 const bsModal = new bootstrap.Modal(modal);
//                 bsModal.show();
//             }
//         });
// }
/**
 * Génère l'HTML d'une carte de formation
 * @param {Object} formation - Objet contenant les données de la formation
 * @param {boolean} inCart - Indique si la formation est déjà dans le panier
 * @returns {string} HTML de la carte de formation
 */
function createFormationCard(formation, inCart = false) {
    // Format des prix
    let priceHtml = '';
    if (formation.type === 'payante') {
        if (formation.discount > 0) {
            priceHtml = `
                ${parseFloat(formation.final_price).toFixed(2)} Dt
                <del>${parseFloat(formation.price).toFixed(2)} Dt</del>
            `;
        } else {
            priceHtml = `${parseFloat(formation.price).toFixed(2)} Dt`;
        }
    } else {
        priceHtml = ``;
    }
    const detailUrl = `/training/${formation.id}`;
    // Formatage des dates
    const startDate = new Date(formation.start_date);
    const endDate = new Date(formation.end_date);
    const formattedStartDate = `${startDate.getDate().toString().padStart(2, '0')}/${(startDate.getMonth()+1).toString().padStart(2, '0')}/${startDate.getFullYear()}`;
    const formattedEndDate = `${endDate.getDate().toString().padStart(2, '0')}/${(endDate.getMonth()+1).toString().padStart(2, '0')}/${endDate.getFullYear()}`;

    // S'assurer que les propriétés existent
    const coursCount = formation.cours_count || (formation.courses ? formation.courses.length : 0);
    const userName = formation.user ? formation.user.name : '';
    const userLastname = formation.user ? formation.user.lastname : '';

    // Informations sur les feedbacks
    const totalFeedbacks = formation.total_feedbacks || 0;
    const averageRating = formation.average_rating || 0;

    // Utilisation de la fonction pour calculer les informations sur les places
    const seatsInfo = calculateSeatsInfo(formation);
    const { totalSeats, remainingSeats, occupiedSeats, occupancyRate, progressClass, isComplete } = seatsInfo;

    console.log(`Formation: ${formation.title}, Total: ${totalSeats}, Remaining: ${remainingSeats}, Occupancy Rate: ${occupancyRate}%`);

    // Générer l'HTML des étoiles de notation
    const ratingStarsHtml = generateRatingStarsHtml(averageRating, totalFeedbacks);
    // Stocker les données de la formation dans un objet global pour y accéder facilement
    // Assurer que l'objet global existe
    if (!window.formationsData) {
        window.formationsData = {};
    }
    // Stocker les données de cette formation
    window.formationsData[formation.id] = {
        total_seats: totalSeats,
        remaining_seats: remainingSeats,
        is_complete: isComplete
    };

    // Attributs data pour filtrage côté client
    const dataAttributes = `
        data-category-id="${formation.category_id}"
        data-status="${formation.status}"
        data-id="${formation.id}"
        data-is-complete="${isComplete}"
        data-description="${(formation.description || '').replace(/"/g, '&quot;')}"
    `;

    // Déterminer l'emplacement du badge "Complet"
    // Déterminer l'emplacement du badge "Complet"
let completeRibbonClass = '';

// Si la formation est gratuite ou a une remise, positionner le badge "Complet" différemment
if (formation.type === 'gratuite' || formation.discount > 0) {
    // Si la formation a déjà à la fois un badge gratuit et une remise, mettre le badge complet en bas à gauche
    if (formation.type === 'gratuite' && formation.discount > 0) {
        completeRibbonClass = 'ribbon-bottom-right';
    }
    // Si seulement gratuite, mettre à droite
    else if (formation.type === 'gratuite') {
        completeRibbonClass = 'ribbon-right';
    }
    // Si seulement remise, mettre en bas à gauche
    else if (formation.discount > 0) {
        completeRibbonClass = 'ribbon-bottom-left';
    }
} else {
    // Si aucun autre badge, mettre à gauche (position par défaut)
    completeRibbonClass = '';
}
const showCartButtons = userRoles.includes('etudiant');

    return `
            <div class="col-xl-3 col-sm-6 xl-4 formation-item" ${dataAttributes}>
                <div class="card h-100">
                    <div class="product-box d-flex flex-column h-100">
                      <div class="product-img" style="height: 200px; overflow: hidden; position: relative;">
                        ${isComplete ? `<div class="ribbon ribbon-danger ${completeRibbonClass}">Complète</div>` : ''}
                        ${formation.type === 'gratuite' ? '<div class="ribbon ribbon-warning">Gratuite</div>' : ''}
                        ${formation.discount > 0 ? `<div class="ribbon ribbon-success ribbon-right">${formation.discount}%</div>` : ''}
                        <img class="img-fluid" src="${window.location.origin}/storage/${formation.image}" alt="${formation.title}" style="width: 100%; height: 100%; object-fit: cover;" />
                        <div class="product-hover">
                            <ul>
                                <li>
                                    <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#formation-modal-${formation.id}">
                                        <i class="icon-eye"></i>
                                    </a>
                                </li>
                                 ${showCartButtons ? `
                                <li>
                                    <a href="/panier"><i class="icon-shopping-cart"></i></a>
                                </li>
                                ` : ''}

                                ${userRoles.includes('admin') || userRoles.includes('super-admin') || userRoles.includes('professeur') ? `
                                <li>
                                    <a href="/formation/${formation.id}/edit"><i class="icon-pencil"></i></a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" class="delete-formation" data-id="${formation.id}">
                                        <i class="icon-trash"></i>
                                    </a>
                                </li>
                                ` : ''}
                            </ul>
                        </div>
                    </div>
                        <!-- Card details section -->
                        <div class="product-details flex-grow-1 d-flex flex-column p-3">
                            <div class="card-content">
                                <a href="/admin/formation/${formation.id}">
                                    <h4 class="formation-title" title="${formation.title}">${formation.title}</h4>
                                </a>
                                <p class="mb-1">Par ${userName} ${userLastname}</p>
                                <div class="rating-wrapper mb-2">
                                    ${ratingStarsHtml} <span>(${totalFeedbacks})</span>
                                </div>

                            </div>
                            <div class="mt-auto product-price-container">
                                <div class="product-price mb-2">
                                    ${formation.type === 'payante' ? priceHtml : ''}
                                </div>
                            </div>
                        </div>

                        <!-- Modal for detailed view -->
                        <div class="modal fade" id="formation-modal-${formation.id}">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">${formation.title}</h5>
                                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="product-box row">
                                            <div class="product-img col-lg-6" style="height: 300px; overflow: hidden; position: relative;">
                                                ${formation.type === 'gratuite' ? '<div class="ribbon ribbon-warning">Gratuite</div>' : ''}
                                                ${formation.discount > 0 ? `<div class="ribbon ribbon-success ribbon-right">${formation.discount}%</div>` : ''}
                                                ${isComplete ? `<div class="ribbon ribbon-danger ${completeRibbonClass}">Complète</div>` : ''}
                                                <img class="img-fluid" src="${window.location.origin}/storage/${formation.image}" alt="${formation.title}" style="width: 100%; height: 100%; object-fit: cover;" />
                                            </div>
                                            <div class="product-details col-lg-6 text-start">
                                                <a href="/admin/formation/${formation.id}">
                                                    <h4>${formation.title}</h4>
                                                </a>
                                                <div class="rating-wrapper mb-2">
                                                    ${ratingStarsHtml}
                                                </div>
                                                <div class="product-price">
                                                    ${formation.type === 'payante' ? priceHtml : ''}
                                                </div>
                                                <div class="product-view">
                                                    <p class="mb-0">${formation.description || ''}</p>
                                                    <div class="mt-3">
                                                        <p><strong>Places occupées:</strong>
<span class="badge ${remainingSeats < totalSeats * 0.2 ? 'badge-danger' : (remainingSeats < totalSeats * 0.5 ? 'badge-warning' : 'badge-bleu')} text-white">
                                                        ${occupiedSeats} / ${totalSeats}
                                                        </span>
                                                        </p>
                                                        <div class="progress mb-3" style="height: 5px;">
                                                            <div class="progress-bar ${progressClass}" role="progressbar"
                                                                style="width: ${occupancyRate}%"
                                                                aria-valuenow="${occupancyRate}"
                                                                aria-valuemin="0"
                                                                aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <p><strong>Durée:</strong> ${formation.duration || '0 heures'}</p>
                                                        <p><strong>Date début:</strong> ${formattedStartDate}</p>
                                                        <p><strong>Date fin:</strong> ${formattedEndDate}</p>
                                                        <p><strong>Nombre de cours:</strong> ${coursCount}</p>
                                                    </div>
                                                </div>
                                                <div class="addcart-btn">
                                                   ${showCartButtons ? `
                                                    <a class="btn ${inCart ? 'btn-primary' : (isComplete ? 'btn-secondary' : 'btn-primary')}"
                                                       ${isComplete && !inCart ? 'disabled' : ''}
                                                       ${inCart ? 'data-in-cart="true"' : ''}
                                                       href="/panier">
                                                        ${inCart ? 'Accéder au panier' : (isComplete ? 'FORMATION COMPLETE' : 'Ajouter au panier')}
                                                    </a>
                                                    ` : ''}

                                                    <a class="btn btn-primary" href="${detailUrl}">Voir détails</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

// Initialiser au chargement du document
document.addEventListener('DOMContentLoaded', function() {
    initButtonLayout();
});

// Écouteur d'événements pour les modals
document.addEventListener('shown.bs.modal', function(event) {
    const modal = event.target;
    if (modal.id && modal.id.startsWith('formation-modal-')) {
        const formationId = modal.id.split('-').pop();

        // Vérifier si cette formation est dans le panier
        const cartFormations = JSON.parse(localStorage.getItem('cartFormations') || '[]');
        const inCart = cartFormations.includes(formationId.toString());

        // Utiliser notre fonction pour vérifier si la formation est complète via l'API
        isFormationComplete(formationId)
            .then(isComplete => {
                updateAddToCartButton(formationId, inCart, isComplete);
            });
    }
});


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
// Rendre les fonctions disponibles globalement d'abord
window.isFormationComplete = isFormationComplete;
window.updateAddToCartButton = updateAddToCartButton;
window.showFormationDetails = showFormationDetails;
window.addButtonStyles = addButtonStyles;
window.calculateSeatsInfo = calculateSeatsInfo;
window.createFormationCard = createFormationCard;
window.updateButtonLayout = updateButtonLayout;
window.initButtonLayout = initButtonLayout;

// Export CommonJS si nécessaire
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        calculateSeatsInfo,
        isFormationComplete,
        updateAddToCartButton,
        showFormationDetails,
        createFormationCard,
        updateButtonLayout,
        addButtonStyles,
        initButtonLayout
    };
}
