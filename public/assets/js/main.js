/**
 * main.js
 *
 * Script JavaScript global du projet TomTroc.
 * Gère les interactions communes côté front-end.
 *
 * Auteur : Salem Hadjali
 * Date   : 21 décembre 2025
 * Maj    : 7 janvier 2026
 */

'use strict';

/* ============================================================
   UTILITAIRES GLOBAUX
   ============================================================ */

/**
 * Affiche un message global dans la page compte
 * @param {'error'|'success'} type
 * @param {string} message
 */
function showAccountMessage(type, message) {
    const container = document.querySelector('.account-page');
    if (!container) return;

    // Supprime tous les messages existants
    container.querySelectorAll('.alert').forEach(el => el.remove());

    const div = document.createElement('div');
    div.className = `alert alert-${type}`;
    div.setAttribute('role', 'alert');

    div.innerHTML = `
        <button type="button" class="alert-close" aria-label="Fermer le message">
            &times;
        </button>
        <p>${message}</p>
    `;

    const title = container.querySelector('h1');
    title.after(div);

    // Fermeture manuelle
    div.querySelector('.alert-close').addEventListener('click', () => {
        div.remove();
    });
}

/* ============================================================
   UPLOADS & FICHIERS
   ============================================================ */

/**
 * IIFE - Upload immédiatement un nouvel avatar utilisateur.
 * Met à jour l’aperçu et envoie le fichier au backend via AJAX.
 *
 * @param {HTMLInputElement} input Champ file contenant l’avatar sélectionné
 */
async function handleAvatarUpload(input) {
    const file = input.files[0];
    if (!file) return;

    const accountAvatar = document.getElementById('account-avatar-img');
    const headerAvatar  = document.getElementById('header-avatar-img');

    // Preview immédiat (UX)
    if (accountAvatar) {
        accountAvatar.src = URL.createObjectURL(file);
    }

    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const response = await fetch('/account/avatar', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        let data;
        try {
            data = await response.json();
        } catch {
            throw new Error('Réponse serveur invalide');
        }

        if (!response.ok || !data.success || !data.avatarPath) {
            throw new Error(data?.message || 'Erreur inconnue');
        }

        console.log(data.avatarPath);
        // Sync avatar account (sécurité cache)
        if (accountAvatar) {
            accountAvatar.src = data.avatarPath + '?v=' + Date.now();
        }

        // Sync avatar header
        if (headerAvatar) {
            headerAvatar.src = data.avatarPath + '?v=' + Date.now();
        }

        showAccountMessage(
            'success',
            'Votre avatar a été mis à jour avec succès.'
        );

    } catch (error) {
        console.error(error);

        showAccountMessage(
            'error',
            'Une erreur est survenue lors de la mise à jour de votre avatar.'
        );
    }
}

/**
 * Gère les changements sur les champs input de type file (images).
 * - Affiche le nom du fichier sélectionné si présent
 * - Déclenche l’upload immédiat de l’avatar sur la page compte
 */
document.addEventListener('change', async (e) => {
    const input = e.target;

    if (!input.matches('.form-file-input')) {
        return;
    }

    // --- CAS 1 : affichage du nom de fichier (register, book, etc.)
    const fileNameContainer = input.closest('.form-group')?.querySelector('.form-file-name');
    if (fileNameContainer) {
        const fileName = input.files[0]?.name || 'Aucun fichier sélectionné';
        fileNameContainer.textContent = fileName;
    }

    // --- CAS 2 : avatar account (upload immédiat)
    if (input.id === 'avatar' && input.closest('.account-avatar-form')) {
        await handleAvatarUpload(input);
    }
});

/* ============================================================
   NAVIGATION / MENU
   ============================================================ */

/**
 * IIFE - Initialise le menu mobile (burger).
 *
 * - Ouvre / ferme le menu
 * - Met à jour aria-expanded
 * - Ferme le menu au resize desktop, clic sur lien ou touche Escape
 *
 * Fonction auto-exécutée pour éviter la pollution du scope global.
 */
(() => {
 
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

    // UX : fermer au tap sur un lien
    mobileMenu.addEventListener('click', (event) => {
        const link = event.target.closest('a');
        if (link) {
            closeMenu();
        }
    });

    // accessibilité : fermer avec Escape
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    window.addEventListener('resize', handleResize);

    // Sécurité au chargement (si on arrive direct en desktop)
    handleResize();
})();

/* ============================================================
   FORMULAIRES & UX
   ============================================================ */

/**
 * Password visibility toggle.
 *
 * Gère l’affichage / masquage du mot de passe pour les champs `input[type="password"]`
 * associés à un bouton `data-password-toggle`.
 * - Le bouton n’apparaît que si une valeur réelle est saisie (hors placeholder).
 * - Le champ repasse automatiquement en mode masqué si la valeur est effacée.
 * - Accessibilité assurée via `aria-label` et `aria-pressed`.
 *
 * Utilisé dans les vues : login, register, account.
 */
(() => {

    const SELECTOR = '[data-password-toggle]';

    const getInput = (btn) => {
        const wrapper = btn.closest('.password-field');
        if (!wrapper) return null;
        return wrapper.querySelector('input[type="password"], input[type="text"]');
    };

    const setButtonVisibility = (btn, input) => {
        const hasValue = input.value.trim().length > 0; // placeholder ne compte pas
        btn.classList.toggle('is-visible', hasValue);

        // Si l’utilisateur efface, on repasse en mode masqué
        if (!hasValue && input.type === 'text') {
            input.type = 'password';
            btn.setAttribute('aria-pressed', 'false');
            btn.setAttribute('aria-label', 'Afficher le mot de passe');
        }
    };

    document.addEventListener('click', (e) => {
        const btn = e.target.closest(SELECTOR);
        if (!btn) return;

        const input = getInput(btn);
        if (!input) return;

        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        btn.setAttribute('aria-pressed', isHidden ? 'true' : 'false');
        btn.setAttribute('aria-label', isHidden ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
    });

    document.addEventListener('input', (e) => {
        const input = e.target;
        if (!(input instanceof HTMLInputElement)) return;
        if (input.type !== 'password' && input.type !== 'text') return;

        const wrapper = input.closest('.password-field');
        if (!wrapper) return;

        const btn = wrapper.querySelector(SELECTOR);
        if (!btn) return;

        setButtonVisibility(btn, input);
    });

    // Init au chargement (cas: old values / autofill)
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll(SELECTOR).forEach((btn) => {
            const input = getInput(btn);
            if (!input) return;
            setButtonVisibility(btn, input);
        });
    });
})();

/**
 * Gère l’aperçu d’une image sélectionnée via un champ fichier.
 */
(() => {

    const fileInput = document.getElementById('image');
    const previewImg = document.getElementById('book-image-preview');

    if (!fileInput || !previewImg) {
        return;
    }

    fileInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            return;
        }

        if (!file.type.startsWith('image/')) {
            return;
        }

        const reader = new FileReader();

        reader.onload = function (e) {
            previewImg.src = e.target.result;
        };

        reader.readAsDataURL(file);
    });
})();

/* ============================================================
   MODALES & ALERTES
   ============================================================ */

/**
 * Gère la confirmation de suppression via une modale avant soumission de formulaire.
 */
(() => {

    const modal = document.getElementById('confirm-modal');
    if (!modal) {
        return;
    }

    let pendingForm = null;

    document.addEventListener('click', (e) => {
        const deleteForm = e.target.closest('.js-confirm-delete');
        if (!deleteForm) {
            return;
        }

        e.preventDefault();
        pendingForm = deleteForm;

        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    });

    modal.querySelector('[data-confirm-cancel]').addEventListener('click', () => {
        pendingForm = null;
        closeModal();
    });

    modal.querySelector('[data-confirm-ok]').addEventListener('click', () => {
        if (pendingForm) {
            pendingForm.submit();
        }
    });

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    }
})();

/**
 * Fermeture des messages d’alerte (success / error)
 * via le bouton [data-alert-close]
 */
(() => {

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-alert-close]');
        if (!btn) {
            return;
        }

        const alert = btn.closest('.alert');
        if (!alert) {
            return;
        }

        alert.remove();
    });
})();

/* ============================================================
   MESSAGERIE
   ============================================================ */

/**
 * Synchronise le badge de messages non lus du header
 * avec la conversation actuellement ouverte.
 *
 * Si une discussion est active, le nombre de messages non lus
 * est déduit du badge global du header et le badge de la
 * conversation est supprimé.
 */
(() => {

    const headerBadge = document.querySelector('.header-action .badge');
    if (!headerBadge) return;

    const currentPath = window.location.pathname;

    // Trouver la conversation actuellement ouverte
    const activeConversationLink = document.querySelector(
        `.conversation-link[href="${currentPath}"]`
    );

    if (!activeConversationLink) return;

    const conversationBadge = activeConversationLink.querySelector('.conversation-badge');
    if (!conversationBadge) return;

    const conversationCount = parseInt(conversationBadge.textContent, 10);
    if (isNaN(conversationCount) || conversationCount <= 0) return;

    // Décrémenter le badge header
    const headerCount = parseInt(headerBadge.textContent, 10) || 0;
    const newHeaderCount = headerCount - conversationCount;

    if (newHeaderCount > 0) {
        headerBadge.textContent = newHeaderCount;
    } else {
        headerBadge.remove();
    }

    // Supprimer le badge de la conversation ouverte
    conversationBadge.remove();
})();

/* ============================================================
   SCROLL & COMPORTEMENTS FINAUX
   ============================================================ */

/**
 * Scroll automatiquement la zone de messages vers le bas
 */
function scrollThreadToBottom() {
    const threadBody = document.querySelector('.thread-body');
    if (!threadBody) return;

    threadBody.scrollTop = threadBody.scrollHeight;
}

/**
 * Scroll automatique à l’ouverture de la conversation
 */
document.addEventListener('DOMContentLoaded', () => {
    scrollThreadToBottom();
});

/**
 * Scroll après clic sur "Envoyer"
 */
document.addEventListener('submit', (e) => {
    const form = e.target.closest('.thread-form');
    if (!form) return;

    // Laisse le POST se faire, mais force le scroll juste avant
    scrollThreadToBottom();
});
