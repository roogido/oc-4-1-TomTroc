<?php
/* VUE REGISTER-INDEX (ACCOUNT) */

use App\Core\Session;

// Récupération des données flash en Session
$errors  = Session::getFlashes('error');
$success = Session::getFlashes('success');
?>

<section class="account-page">
    <h1>Mon compte</h1>

    <section class="account-profile">
        <!-- Carte profil gauche -->
        <div class="account-profile-summary">

            <div class="account-avatar">
                <img
                    src="<?= htmlspecialchars($user->getAvatarPath()); ?>"
                    alt="Avatar de <?= htmlspecialchars($user->getPseudo()); ?>"
                    width="135"
                    height="157"
                >
                <form
                    method="post"
                    action="/account/avatar"
                    enctype="multipart/form-data"
                >
                    <input
                        type="file"
                        name="avatar"
                        id="avatar"
                        accept="image/*"
                        style="display:none"
                        onchange="this.form.submit()"
                    >

                    <label for="avatar">
                        <small><u>modifier</u></small>
                    </label>
                </form>
            </div>

            <p class="account-pseudo">
                <?= htmlspecialchars($user->getPseudo()); ?>
            </p>

            <p class="account-member-since">
                Membre depuis <?= htmlspecialchars($memberSince); ?>
            </p>

            <p class="account-library-count">
                <strong>BIBLIOTHÈQUE</strong><br>
                <?= (int) $booksCount; ?> livres
            </p>

            <p>
                <a href="/logout">Se déconnecter</a>
            </p>
        </div>

        <!-- Carte profil droite : formulaire -->
        <div class="account-profile-form">

            <h2>Vos informations personnelles</h2>

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

            <form method="post" action="/account">
                <div>
                    <label for="email">Adresse email</label><br>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($user->getEmail()); ?>"
                        required
                    >
                </div>

                <div>
                    <label for="password">Mot de passe</label><br>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="••••••••"
                    >
                </div>

                <div>
                    <label for="pseudo">Pseudo</label><br>
                    <input
                        type="text"
                        id="pseudo"
                        name="pseudo"
                        value="<?= htmlspecialchars($user->getPseudo()); ?>"
                        required
                    >
                </div>

                <div>
                    <button type="submit">Enregistrer</button>
                </div>

            </form>

        </div>
    </section>

    <hr>

    <section class="account-library">
        <h2>Ma bibliothèque</h2>

        <p>
            <a href="/book/add" class="btn-add-book">➕ Ajouter un livre</a>
        </p>

        <?php if (empty($books)) : ?>
            <p>Vous n’avez encore ajouté aucun livre.</p>
        <?php else : ?>
            <table class="library-table">
                <thead>
                    <tr>
                        <th>PHOTO</th>
                        <th>TITRE</th>
                        <th>AUTEUR</th>
                        <th>DESCRIPTION</th>
                        <th>DISPONIBILITÉ</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book) : ?>
                        <tr>
                            <td>
                                <img
                                    src="<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                                    alt="<?= htmlspecialchars($book->getTitle()) ?>"
                                    width="78"
                                    height="78"
                                >
                            </td>

                            <td><?= htmlspecialchars($book->getTitle()) ?></td>
                            <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                            <td><?= htmlspecialchars(mb_strimwidth($book->getDescription(), 0, 83, '…')) ?></td>
                            <td>
                                <?= $book->getStatus() === 'available'
                                    ? 'Disponible'
                                    : 'Indisponible'
                                ?>
                            </td>
                            <td>
                                <!-- <a href="/book/<?= (int) $book->getId() ?>">Voir</a>
                                | -->
                                <a href="/book/<?= (int) $book->getId() ?>/edit">Modifier</a>
                                |
                                <form
                                    method="post"
                                    action="/book/<?= (int) $book->getId() ?>/delete"
                                    style="display:inline;"
                                    onsubmit="return confirm('Supprimer ce livre ?');"
                                >
                                    <button type="submit">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</section>
