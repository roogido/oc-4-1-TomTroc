<?php if (!empty($globalError)) : ?>
    <!-- ===== Global errors (fallback) ===== -->
    <div class="alert alert-error" role="alert">
        <div class="alert-content">
            <p><?= htmlspecialchars($globalError) ?></p>
        </div>

        <button
            type="button"
            class="alert-close"
            aria-label="Fermer le message"
            data-alert-close
        >
            ×
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($success)) : ?>
    <!-- ===== Success messages ===== -->
    <div class="alert alert-success" role="status">
        <div class="alert-content">
            <?php foreach ($success as $message) : ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endforeach; ?>
        </div>

        <button
            type="button"
            class="alert-close"
            aria-label="Fermer le message"
            data-alert-close
        >
            ×
        </button>
    </div>
<?php endif; ?>
