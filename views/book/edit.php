<?php
/* VUE EDIT (BOOK) */
use App\Core\Session;

$errors = Session::getFlashes('error');
?>

<section class="book-edit">
    <h1>Modifier le livre</h1>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $e) : ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/book/<?= (int) $book->getId(); ?>/edit">
        <div class="form-group">
            <label for="title">Titre</label>
            <input
                type="text"
                id="title"
                name="title"
                required
                value="<?= htmlspecialchars($book->getTitle()) ?>"
            >
        </div>

        <div class="form-group">
            <label for="author">Auteur</label>
            <input
                type="text"
                id="author"
                name="author"
                required
                value="<?= htmlspecialchars($book->getAuthor()) ?>"
            >
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea
                id="description"
                name="description"
                rows="5"
                required
            ><?= htmlspecialchars($book->getDescription()) ?></textarea>
        </div>

        <div class="form-group">
            <label for="status">Disponibilité</label>
            <select id="status" name="status">
                <option value="available" <?= $book->getStatus() === 'available' ? 'selected' : '' ?>>
                    Disponible
                </option>
                <option value="unavailable" <?= $book->getStatus() === 'unavailable' ? 'selected' : '' ?>>
                    Indisponible
                </option>
            </select>
        </div>

        <button type="submit">Enregistrer les modifications</button>
    </form>

    <p>
        <a href="/account">← Retour à mon compte</a>
    </p>
</section>
