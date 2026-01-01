<?php
/**
 * Partial : Modale de confirmation globale
 *
 * Utilisée pour confirmer des actions sensibles (ex : suppression).
 * Injectée dans le layout afin d’être disponible sur toutes les pages.
 * Le comportement (ouverture / validation / annulation) est géré en JavaScript.
 */
?>

<div id="confirm-modal" class="modal-overlay" aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true">
        <h2 class="modal-title">Confirmation</h2>

        <p class="modal-text">
            Es-tu sûr de vouloir supprimer ce livre ?
            <br>
            Cette action est définitive.
        </p>

        <div class="modal-actions">
            <button
                type="button"
                class="btn btn-secondary"
                data-confirm-cancel
            >
                Annuler
            </button>

            <button
                type="button"
                class="btn btn-danger"
                data-confirm-ok
            >
                Supprimer
            </button>
        </div>
    </div>
</div>
