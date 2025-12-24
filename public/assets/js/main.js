/**
 * main.js
 *
 * Script JavaScript global du projet TomTroc.
 * Gère les interactions communes côté front-end (ex. menu mobile).
 *
 * Auteur : Salem Hadjali
 * Date   : Dimanche 21 décembre 2025
 */



/**
 * Initialise le menu mobile (burger).
 *
 * - Ouvre / ferme le menu
 * - Met à jour aria-expanded
 * - Ferme le menu au resize desktop, clic sur lien ou touche Escape
 *
 * Fonction auto-exécutée pour éviter la pollution du scope global.
 */
(function () {
    'use strict';

    const MOBILE_BREAKPOINT = 768;

    const burgerButton = document.querySelector('.burger');
    const mobileMenu = document.getElementById('mobile-menu');

    if (!burgerButton || !mobileMenu) {
        return;
    }

    function openMenu() {
        mobileMenu.hidden = false;
        burgerButton.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
        mobileMenu.hidden = true;
        burgerButton.setAttribute('aria-expanded', 'false');
    }

    function toggleMenu() {
        const isExpanded = burgerButton.getAttribute('aria-expanded') === 'true';
        if (isExpanded) {
            closeMenu();
            return;
        }
        openMenu();
    }

    function handleResize() {
        // Si on repasse en desktop, on force la fermeture.
        if (window.innerWidth > MOBILE_BREAKPOINT) {
            closeMenu();
        }
    }

    burgerButton.addEventListener('click', toggleMenu);

    // Bonus UX : fermer au tap sur un lien
    mobileMenu.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (link) {
            closeMenu();
        }
    });

    // Bonus accessibilité : fermer avec Escape
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    window.addEventListener('resize', handleResize);

    // Sécurité au chargement (si on arrive direct en desktop)
    handleResize();
})();



