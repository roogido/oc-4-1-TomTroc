<section class="error-page">
    <div class="error-box" role="alert" aria-labelledby="error-title">

        <h1 class="page-title" id="error-title">
            Page introuvable
        </h1>

        <p class="error-code">
            Erreur 404
        </p>

        <?php if (!empty($message)) : ?>
            <p class="error-message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php else : ?>
            <p class="error-message">
                La page que vous recherchez n’existe pas ou a été déplacée.
            </p>
        <?php endif; ?>

        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                Retour à l’accueil
            </a>

            <a href="/books" class="btn btn-secondary">
                Voir les livres
            </a>
        </div>

    </div>
</section>
