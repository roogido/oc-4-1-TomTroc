<?php

use App\Core\Session;

$errors = Session::getFlashes('error');
$success = Session::getFlashes('success');
$oldAll = Session::getFlashes('old');
$old = $oldAll[0] ?? [];
?>

<section class="auth-register">
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

    <form method="post" action="/register">
        <div class="form-group">
            <label for="pseudo">Pseudo</label>
            <input 
                type="text" 
                id="pseudo" 
                name="pseudo" 
                required 
                value="<?= htmlspecialchars($old['pseudo'] ?? '') ?>"
            >
        </div>

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

        <button type="submit">Créer mon compte</button>
    </form>

    <p class="auth-link">
        Déjà inscrit ?
        <a href="/login">Se connecter</a>
    </p>
</section>
