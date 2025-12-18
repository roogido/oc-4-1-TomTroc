<?php
/* VUE REGISTER (INSCRIPTION) */

use App\Core\Session;

// Récupération des données flash en Session
$errors = Session::getFlashes('error');
$success = Session::getFlashes('success');
$oldAll = Session::getFlashes('old');
$old = $oldAll[0] ?? [];
?>

<section class="auth-page auth-register">
    <div class="auth-layout">

        <!-- Colonne gauche : formulaire -->
        <div class="auth-form">

            <h1>Inscription</h1>

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

            <form method="post" action="/register" enctype="multipart/form-data">

                <div>
                    <label>
                        Pseudo
                        <input type="text" name="pseudo" required>
                    </label>
                </div>

                <div>
                    <label>
                        Adresse email
                        <input type="email" name="email" required>
                    </label>
                </div>

                <div>
                    <label>
                        Mot de passe
                        <input type="password" name="password" required>
                    </label>
                </div>

                <div>
                    <label>
                        Avatar (optionnel)
                        <input type="file" name="avatar" accept="image/*">
                    </label>
                </div>

                <div>
                    <button type="submit">S'inscrire</button>
                </div>

            </form>

            <p class="auth-link">
                Déjà inscrit ?
                <a href="/login">Connectez-vous</a>
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

