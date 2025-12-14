<?php
/* VUE REGISTER-INDEX (ACCOUNT) */

use App\Core\Session;

// Récupération des données flash en Session
$errors  = Session::getFlashes('error');
$success = Session::getFlashes('success');
?>

<section class="account-page">
    <h1>Mon compte</h1>

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

    <div class="account-info">
        <p><strong>Pseudo :</strong> <?= htmlspecialchars($user->getPseudo()); ?></p>
        <p><strong>Email :</strong> <?= htmlspecialchars($user->getEmail()); ?></p>
    </div>

    <div class="account-actions">
        <a href="/logout" class="btn-logout">Se déconnecter</a>
    </div>

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
                        <th>Photo</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($books as $book) : ?>
                        <tr>
                            <td>
                                <?php if ($book->getImagePath()) : ?>
                                    <img
                                        src="/<?= htmlspecialchars($book->getImagePath()) ?>"
                                        alt="<?= htmlspecialchars($book->getTitle()) ?>"
                                        width="78"
                                        height="78"
                                    >
                                <?php else : ?>
                                    —
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($book->getTitle()) ?></td>
                            <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                            <td>
                                <?= $book->getStatus() === 'available'
                                    ? 'Disponible'
                                    : 'Indisponible'
                                ?>
                            </td>
                            <td>
                                <a href="/book/<?= (int) $book->getId() ?>">Voir</a>
                                |
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
