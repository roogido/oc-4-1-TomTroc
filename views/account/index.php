<?php
// Messages flash éventuels

use App\Core\Session;

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

        <p>Ici apparaîtra la liste des livres de l’utilisateur.</p>
        <p>(Fonctionnalité à implémenter plus tard.)</p>

        <a href="/book/add" class="btn-add-book">Ajouter un livre</a>
    </section>
</section>
