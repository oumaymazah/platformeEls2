// export function initQuizMonitor(attemptId, csrfToken, routes) {
//     // Configuration
//     const config = {
//         maxSwitches: 3,
//         warningThreshold: 1,//nombre de changements avant d'afficher un avertissement
//         switchCount: 0
//     };

//     // Éléments du DOM
//     const elements = {
//         warning: document.getElementById('tab-warning'),
//         counter: document.getElementById('tab-count'),
//         timer: document.getElementById('timer')
//     };

//     // Fonctions principales
//     const handleTabSwitch = async () => {
//         config.switchCount++;
//         updateWarningDisplay();

//         try {
//             const response = await axios.post(routes.tabSwitch, {
//                 _token: csrfToken
//             });

//             if (response.data.force_submit) {
//                 handleForceSubmit();
//             }
//         } catch (error) {
//             console.error('Error reporting tab switch:', error);
//         }
//     };
//     //sert à avertir l'étudiant visuellement qu'il est surveillé après qu'il ait changé d'onglet plus d'une fois.
//     const updateWarningDisplay = () => {
//         if (config.switchCount > config.warningThreshold) {
//             elements.warning.style.display = 'block';
//             elements.counter.textContent = config.switchCount;
//         }
//     };

//     const handleForceSubmit = () => {
//         alert('Tentative de triche détectée. Le quiz sera soumis automatiquement.');
//         window.location.href = routes.result;
//     };

//     // Sécurité:Bloquer clic droit, copier, couper
//     const preventUserActions = (e) => {
//         e.preventDefault();
//     };
//     //Forcer le mode plein écran
//     const manageFullscreen = () => {
//         const methods = [
//             'requestFullscreen',
//             'webkitRequestFullscreen',
//             'msRequestFullscreen'
//         ];

//         const elem = document.documentElement;
//         methods.some(method => elem[method]?.call(elem));
//     };

//     // Initialisation
//     const init = () => {
//         // Surveillance des onglets
//         document.addEventListener('visibilitychange', () => {
//             if (document.hidden) handleTabSwitch();
//         });

//         // Protection contre les actions utilisateur
//         ['contextmenu', 'copy', 'cut'].forEach(event => {
//             document.addEventListener(event, preventUserActions);
//         });

//         // Gestion du plein écran
//         manageFullscreen();
//         document.addEventListener('fullscreenchange', () => {
//             if (!document.fullscreenElement) manageFullscreen();
//         });
//     };

//     init();
// }






/**
 * Système de surveillance pour les quizzes
 * Ce script permet de surveiller l'activité de l'utilisateur pendant un quiz
 * et de prendre des mesures en cas de comportement suspect.
 */

// On s'assure qu'Axios est disponible
if (typeof axios === 'undefined') {
    console.error("Axios n'est pas chargé. Veuillez l'inclure avant ce script.");
}

/**
 * Initialise le moniteur de quiz
 * @param {number} attemptId - ID de la tentative de quiz
 * @param {string} csrfToken - Token CSRF pour les requêtes AJAX
 * @param {Object} routes - Routes pour les actions (tabSwitch, result)
 */
export function initQuizMonitor(attemptId, csrfToken, routes) {
    console.log('Quiz monitor module loaded');

    // Configuration
    const config = {
        maxSwitches: 2,
        warningThreshold: 1, // nombre de changements avant d'afficher un avertissement
        switchCount: 0
    };

    // Éléments du DOM
    const elements = {
        warning: document.getElementById('tab-warning'),
        counter: document.getElementById('tab-count'),
        timer: document.getElementById('timer')
    };

    // Vérifier que tous les éléments DOM nécessaires sont disponibles
    if (!elements.warning || !elements.counter) {
        console.error('Éléments DOM requis non trouvés');
        return;
    }

    // Fonctions principales
    const handleTabSwitch = async () => {
        config.switchCount++;
        updateWarningDisplay();

        try {
            const response = await axios.post(routes.tabSwitch, {
                _token: csrfToken
            });

            if (response.data.force_submit) {
                handleForceSubmit();
            }
        } catch (error) {
            console.error('Error reporting tab switch:', error);
        }
    };

    // Sert à avertir l'étudiant visuellement qu'il est surveillé
    // après qu'il ait changé d'onglet plus d'une fois
    const updateWarningDisplay = () => {
        if (config.switchCount = config.warningThreshold) {
            elements.warning.style.display = 'block';
            elements.counter.textContent = config.switchCount;
        }
    };

    const handleForceSubmit = () => {
        alert('Tentative de triche détectée. Le quiz sera soumis automatiquement.');
        window.location.href = routes.result;
    };

    // Sécurité: Bloquer clic droit, copier, couper
    const preventUserActions = (e) => {
        e.preventDefault();
    };

    // Forcer le mode plein écran
    const manageFullscreen = () => {
        const methods = [
            'requestFullscreen',
            'webkitRequestFullscreen',
            'msRequestFullscreen'
        ];

        const elem = document.documentElement;
        methods.some(method => elem[method]?.call(elem));
    };

    // Initialisation
    const init = () => {
        console.log('Initializing quiz monitor');

        // Surveillance des onglets
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                console.log('Tab switch detected');
                handleTabSwitch();
            }
        });

        // Protection contre les actions utilisateur
        ['contextmenu', 'copy', 'cut'].forEach(event => {
            document.addEventListener(event, preventUserActions);
        });

        // Gestion du plein écran
        manageFullscreen();
        document.addEventListener('fullscreenchange', () => {
            if (!document.fullscreenElement) manageFullscreen();
        });

        console.log('Quiz monitor initialization complete');
    };

    // Démarrer l'initialisation
    init();
}

// Export par défaut pour faciliter l'import
export default { initQuizMonitor };
