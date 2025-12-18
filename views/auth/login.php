<?php

use App\Core\Session;

// Récupération des données flash en Session
$errors = Session::getFlashes('error');
$success = Session::getFlashes('success');
$oldAll = Session::getFlashes('old');
$old = $oldAll[0] ?? [];
?>

<section class="auth-page auth-login">
    <div class="auth-layout">
        <!-- Colonne gauche : formulaire -->
        <div class="auth-form">

            <h1>Connexion</h1>

            <?php if (!empty($errors)) : ?>
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $e) : ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)) : ?>
                <div class="alert alert-success">
                    <?php foreach ($success as $msg) : ?>
                        <p><?= htmlspecialchars($msg) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/login">

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        required
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <div class="form-actions">
                    <button type="submit">Se connecter</button>
                </div>

            </form>

            <p class="auth-link">
                Pas de compte ?
                <a href="/register">Inscrivez-vous</a>
            </p>

        </div>

        <!-- Colonne droite : image décorative -->
        <div class="auth-visual">
            <img
                src="/assets/images/home/hero-right.png"
                alt=""
                aria-hidden="true"
                width="720"
                height="886"
            >
        </div>

    </div>
</section>

