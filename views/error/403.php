<section class="error-page">
    <div class="error-box" role="alert" aria-labelledby="error-title">

        <h1 class="page-title" id="error-title">
            Accès interdit
        </h1>

        <p class="error-code">
            Erreur 403
        </p>

        <?php if (!empty($message)) : ?>
            <p class="error-message">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php else : ?>
            <p class="error-message">
                Vous n’êtes pas autorisé à accéder à cette page.
            </p>
        <?php endif; ?>

        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                Retour à l’accueil
            </a>

            <?php if (!\App\Core\Session::isLogged()) : ?>
                <a href="/login" class="btn btn-secondary">
                    Se connecter
                </a>
            <?php endif; ?>
        </div>

    </div>
</section>
