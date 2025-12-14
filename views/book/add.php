<?php
/* VUE ADD (BOOK) */

use App\Core\Session;

// Récupération des données flash en Session
$errors  = Session::getFlashes('error');
$success = Session::getFlashes('success');
$oldAll  = Session::getFlashes('old');
$old     = $oldAll[0] ?? [];
?>

<section class="book-add">
    <h1>Ajouter un livre</h1>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $e) : ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/book/add" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Titre</label>
            <input
                type="text"
                id="title"
                name="title"
                required
                value="<?= htmlspecialchars($old['title'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="author">Auteur</label>
            <input
                type="text"
                id="author"
                name="author"
                required
                value="<?= htmlspecialchars($old['author'] ?? '') ?>"
            >
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                required
            ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="status">Disponibilité</label>
            <select id="status" name="status">
                <option value="available" <?= (($old['status'] ?? '') === 'available') ? 'selected' : '' ?>>
                    Disponible
                </option>
                <option value="unavailable" <?= (($old['status'] ?? '') === 'unavailable') ? 'selected' : '' ?>>
                    Indisponible
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Image (optionnelle)</label>
            <input
                type="file"
                id="image"
                name="image"
                accept="image/jpeg, image/png, image/webp"
            >
        </div>

        <button type="submit">Ajouter le livre</button>
    </form>

    <p>
        <a href="/account">← Retour à mon compte</a>
    </p>
</section>
