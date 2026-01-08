<?php
/* VUE REGISTER (INSCRIPTION) */

use App\View\FlashHelper;

// Récupération des messages flash normalisés :
//  - (erreurs globales, erreurs par champ, anciennes valeurs, succès)
//  extract() : créer automatiquement les variables utilisables directement dans la vue
extract(FlashHelper::extract());
?>

<section class="auth-page auth-register">
    <div class="auth-layout">

        <!-- ================= LEFT : FORM ================= -->
        <div class="auth-form">

            <h1 class="page-title">Inscription</h1>

            <!-- ===== Alertes : success/errors  ===== -->
            <?php require __DIR__ . '/../partials/alerts.php'; ?>

            <form method="post" action="/register" enctype="multipart/form-data" novalidate>

                <div class="auth-form-fields">

                    <!-- ===== PSEUDO ===== -->
                    <div class="form-group<?= isset($errors['pseudo']) ? ' form-group--error' : '' ?>">
                        <label for="pseudo">Pseudo</label>

                        <input
                            id="pseudo"
                            type="text"
                            name="pseudo"
                            value="<?= htmlspecialchars($old['pseudo'] ?? '') ?>"
                            <?= isset($errors['pseudo']) ? 'aria-invalid="true" aria-describedby="pseudo-error"' : '' ?>
                            required
                        >

                        <?php if (isset($errors['pseudo'])) : ?>
                            <p class="form-error" id="pseudo-error" role="alert">
                                <?= htmlspecialchars($errors['pseudo']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- ===== EMAIL ===== -->
                    <div class="form-group<?= isset($errors['email']) ? ' form-group--error' : '' ?>">
                        <label for="email">Adresse email</label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                            <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>
                            required
                        >

                        <?php if (isset($errors['email'])) : ?>
                            <p class="form-error" id="email-error" role="alert">
                                <?= htmlspecialchars($errors['email']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- ===== PASSWORD ===== -->
                    <div class="form-group<?= isset($errors['password']) ? ' form-group--error' : '' ?>">
                        <label for="password">Mot de passe</label>

                        <div class="password-field">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                <?= isset($errors['password']) ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?>
                                required
                            >
                            <button
                            type="button"
                            class="password-toggle"
                            data-password-toggle
                            aria-label="Afficher le mot de passe"
                            aria-pressed="false"
                            >
                            <span class="eye" aria-hidden="true">👁</span>
                            </button>
                        </div>                            

                        <?php if (isset($errors['password'])) : ?>
                            <p class="form-error" id="password-error" role="alert">
                                <?= htmlspecialchars($errors['password']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- ================= AVATAR (OPTIONNEL) ================= -->
                    <div class="form-group form-group--file">

                        <label class="form-label" for="avatar">
                            Avatar (optionnel)
                        </label>

                        <!-- Input file caché mais accessible -->
                        <input
                            type="file"
                            name="avatar"
                            id="avatar"
                            accept="image/*"
                            class="form-file-input"
                        >

                        <!-- Bouton custom -->
                        <label for="avatar" class="btn btn-outline btn--md form-file-btn">
                            Choisir une image
                        </label>

                        <!-- Nom du fichier sélectionné -->
                        <span
                            class="form-file-name"
                            id="avatar-filename"
                            aria-live="polite"
                        >
                            Aucun fichier sélectionné
                        </span>

                    </div>
                </div>

                <!-- ===== ACTION ===== -->
                <button type="submit" class="btn btn-primary btn--full">
                    S'inscrire
                </button>

            </form>

            <!-- ===== Helper ===== -->
            <p class="auth-helper">
                Déjà inscrit ?
                <a href="/login">Connectez-vous</a>
            </p>

        </div>

        <!-- ================= RIGHT : VISUAL ================= -->
        <div class="auth-visual">
            <img
                src="/assets/images/auth/auth-illustration.webp"
                alt=""
                aria-hidden="true"
                width="720"
                height="886"
            >
        </div>

    </div>
</section>
