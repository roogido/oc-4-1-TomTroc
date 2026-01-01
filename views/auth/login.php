<?php
/* VUE LOGIN (CONNEXION) */

use App\Core\Session;

// Récupération des données flash en Session
$errorsAll = Session::getFlashes('error');
$success  = Session::getFlashes('success');
$oldAll   = Session::getFlashes('old');

/*
 * Convention :
 * - $errors : tableau associatif par champ
 * - $old    : valeurs précédemment saisies
 */
$errors = $errorsAll[0] ?? [];
$old    = $oldAll[0] ?? [];

$isFieldErrors = is_array($errors);
$isGlobalError = !empty($errorsAll) && !$isFieldErrors;
?>

<section class="auth-page auth-login">
    <div class="auth-layout">

        <!-- ================= LEFT : FORM ================= -->
        <div class="auth-form">

            <h1 class="page-title">Connexion</h1>

            <!-- ===== Global errors (fallback) ===== -->
            <?php if ($isGlobalError) : ?>
                <div class="alert alert-error" role="alert">
                    <ul>
                        <?php foreach ($errorsAll as $e) : ?>
                            <li><?= htmlspecialchars($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- ===== Success messages ===== -->
            <?php if (!empty($success)) : ?>
                <div class="alert alert-success" role="status">
                    <?php foreach ($success as $msg) : ?>
                        <p><?= htmlspecialchars($msg) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="/login" novalidate>

                <div class="auth-form-fields">

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
                            <p class="form-error" id="email-error">
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
                            <p class="form-error" id="password-error">
                                <?= htmlspecialchars($errors['password']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- ===== ACTION ===== -->
                <button type="submit" class="btn btn-primary btn--full">
                    Se connecter
                </button>

            </form>

            <!-- ===== Helper ===== -->
            <p class="auth-helper">
                Pas de compte ?
                <a href="/register">Inscrivez-vous</a>
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
