<section class="error-page error-page--dev">
    <div class="error-box" role="alert" aria-labelledby="error-title">

        <h1 class="page-title" id="error-title">
            Erreur interne (DEV)
        </h1>

        <?php if (!empty($message)) : ?>
            <p class="error-message">
                <strong><?= htmlspecialchars($message) ?></strong>
            </p>
        <?php endif; ?>

        <?php if (!empty($trace)) : ?>
            <h2 class="error-trace-title">
                Trace d’exécution
            </h2>

            <pre class="error-trace">
<?= htmlspecialchars($trace) ?>
            </pre>
        <?php endif; ?>

        <div class="error-actions">
            <a href="javascript:location.reload()" class="btn btn-secondary">
                Recharger la page
            </a>

            <a href="/" class="btn btn-primary">
                Accueil
            </a>
        </div>

    </div>
</section>

