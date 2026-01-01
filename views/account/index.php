<?php
/* VUE ACCOUNT (MON COMPTE) */

use App\Core\Session;

// Récupération flash
$errorsAll = Session::getFlashes('error');
$success   = Session::getFlashes('success');
$oldAll    = Session::getFlashes('old');

/*
 * Convention :
 * - $globalError : string|null
 * - $errors      : array par champ
 * - $old         : anciennes valeurs
 */
$globalError = null;
$errors = [];
$old    = $oldAll[0] ?? [];

foreach ($errorsAll as $err) {
    if (is_array($err)) {
        $errors = $err;
    } elseif (is_string($err)) {
        $globalError = $err;
    }
}
?>

<section class="account-page">

    <!-- ================= PAGE TITLE ================= -->
    <header class="account-header">
        <h1 class="page-title">Mon compte</h1>
    </header>

    <!-- ===== Global errors (fallback) ===== -->
    <?php if ($globalError) : ?>
        <div class="alert alert-error" role="alert">
            <p><?= htmlspecialchars($globalError) ?></p>
        </div>
    <?php endif; ?>

    <!-- ===== Success messages ===== -->
    <?php if (!empty($success)) : ?>
        <div class="alert alert-success" role="alert">
            <?php foreach ($success as $message) : ?>
                <p><?= htmlspecialchars($message) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


    <!-- ================= PROFILE + SETTINGS ================= -->
    <div class="account-layout">

        <!-- ===== PROFILE CARD ===== -->
        <section class="profile-card account-profile-card">

            <!-- ===== AVATAR ===== -->
            <div class="profile-avatar account-avatar">

                <div class="avatar">
                    <img
                        src="<?= htmlspecialchars($user->getAvatarPath()); ?>"
                        alt="Avatar de <?= htmlspecialchars($user->getPseudo()); ?>"
                        id="account-avatar-img"
                    >
                </div>

                <form
                    method="post"
                    action="/account/avatar"
                    enctype="multipart/form-data"
                    class="account-avatar-form"
                >
                    <input
                        type="file"
                        name="avatar"
                        id="avatar"
                        accept="image/*"
                        class="form-file-input"
                    >

                    <label for="avatar" class="account-avatar-edit">
                        Modifier
                    </label>
                </form>

            </div>

            <hr class="profile-separator">

            <!-- ===== META ===== -->
            <p class="profile-name">
                <?= htmlspecialchars($user->getPseudo()); ?>
            </p>

            <p class="profile-since">
                Membre depuis <?= htmlspecialchars($memberSince); ?>
            </p>

            <div class="profile-library">
                <p class="profile-library-label">BIBLIOTHÈQUE</p>

                <div class="profile-library-meta">
                    <img
                        src="/assets/icons/books_small_icon.svg"
                        alt=""
                        class="profile-library-icon"
                    >
                    <span class="profile-library-count">
                        <?= (int) $booksCount; ?> livres
                    </span>
                </div>
            </div>

            <hr class="profile-separator">

            <!-- ===== LOGOUT ===== -->
            <div class="account-logout">
                <a href="/logout" class="btn btn-outline btn--md">
                    Se déconnecter
                </a>
            </div>

        </section>

        <!-- ===== SETTINGS CARD ===== -->
        <section class="account-settings">

            <div class="profile-card account-settings-card">

                <h2 class="account-section-title">
                    Vos informations personnelles
                </h2>

                <form method="post"
                    action="/account"
                    class="account-form"
                    novalidate
                    aria-labelledby="account-settings-title">

                    <!-- EMAIL -->
                    <div class="form-group<?= isset($errors['email']) ? ' form-group--error' : '' ?>">
                        <label for="email">Adresse email</label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($old['email'] ?? $user->getEmail()) ?>"
                            <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="email-error"' : '' ?>
                            required
                        >

                        <?php if (isset($errors['email'])) : ?>
                            <p class="form-error" id="email-error">
                                <?= htmlspecialchars($errors['email']) ?>
                            </p>
                        <?php endif; ?>
                    </div>


                    <!-- PASSWORD -->
                    <div class="form-group<?= isset($errors['password']) ? ' form-group--error' : '' ?>">
                        <label for="password">Mot de passe</label>

                        <div class="password-field">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                <?= isset($errors['password']) ? 'aria-invalid="true" aria-describedby="password-error"' : '' ?>
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

                    <!-- PSEUDO -->
                    <div class="form-group<?= isset($errors['pseudo']) ? ' form-group--error' : '' ?>">
                        <label for="pseudo">Pseudo</label>

                        <input
                            id="pseudo"
                            type="text"
                            name="pseudo"
                            value="<?= htmlspecialchars($old['pseudo'] ?? $user->getPseudo()) ?>"
                            <?= isset($errors['pseudo']) ? 'aria-invalid="true" aria-describedby="pseudo-error"' : '' ?>
                            required
                        >

                        <?php if (isset($errors['pseudo'])) : ?>
                            <p class="form-error" id="pseudo-error">
                                <?= htmlspecialchars($errors['pseudo']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- SUBMIT -->
                    <button type="submit" class="btn btn-outline btn--md">
                        Enregistrer
                    </button>

                </form>

            </div>

        </section>

    </div>

    <!-- ================= LIBRARY ================= -->
    <section class="library-card account-library">

        <header class="account-library-header">
            <a href="/book/add" class="btn btn-primary btn--md">
                Ajouter un livre
            </a>
        </header>

        <div class="library-table">

            <!-- ===== Header desktop ===== -->
            <div class="library-head">
                <div class="col-photo">PHOTO</div>
                <div class="col-title">TITRE</div>
                <div class="col-author">AUTEUR</div>
                <div class="col-description">DESCRIPTION</div>
                <div class="col-status">DISPONIBILITÉ</div>
                <div class="col-actions">ACTION</div>
            </div>

            <?php if (empty($books)) : ?>
                <p class="library-empty">
                    Vous n’avez encore ajouté aucun livre.
                </p>
            <?php else : ?>

                <?php foreach ($books as $book) : ?>
                    <article class="library-row">

                        <!-- PHOTO -->
                        <div class="col-photo">
                            <img
                                class="library-book-image"
                                src="/<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                                alt="<?= htmlspecialchars($book->getTitle()) ?>"
                                width="78"
                                height="78"
                            >
                        </div>

                        <!-- TITRE -->
                        <div class="col-title">
                            <a
                                class="library-book-link"
                                href="/book/<?= (int) $book->getId() ?>"
                            >
                                <?= htmlspecialchars($book->getTitle()) ?>
                            </a>
                        </div>

                        <!-- AUTEUR -->
                        <div class="col-author">
                            <?= htmlspecialchars($book->getAuthor()) ?>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="col-description">
                            <?= htmlspecialchars(
                                mb_strimwidth(
                                    $book->getDescription(),
                                    0,
                                    83,
                                    '…'
                                )
                            ) ?>
                        </div>

                        <!-- DISPONIBILITÉ -->
                        <div class="col-status">
                            <span class="book-status <?= htmlspecialchars($book->getStatus()) ?>">
                                <?= $book->getStatus() === 'available'
                                    ? 'disponible'
                                    : 'non dispo.' ?>
                            </span>
                        </div>

                        <!-- ACTIONS -->
                        <div class="col-actions library-actions">
                            <a
                                href="/book/<?= (int) $book->getId() ?>/edit"
                                class="library-action-form"
                            >
                                Éditer
                            </a>

                            <form
                                method="post"
                                action="/book/<?= (int) $book->getId() ?>/delete"
                                class="js-confirm-delete"
                            >
                                <button
                                    type="submit"
                                    class="library-action delete"
                                >
                                    Supprimer
                                </button>
                            </form>
                        </div>

                    </article>
                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </section>

</section>

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

