<?php
/* VUE EDIT (BOOK) */
use App\Core\Session;

$errors = Session::getFlashes('error');
?>

<section class="book-edit-page">
    <p class="book-edit-back">
        <a href="/account">← retour</a>
    </p>

    <h1>Modifier les informations</h1>

    <div class="book-edit-card">

        <div class="book-edit-layout">
            <!-- COLONNE GAUCHE : photo -->
            <aside class="book-edit-media">
                <p class="book-edit-media-label">Photo</p>

                <img
                    src="<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                    alt="<?= htmlspecialchars($book->getTitle()) ?>"
                    width="488"
                    height="553"
                    class="book-edit-image"
                >

                <form
                    method="post"
                    action="/book/<?= (int) $book->getId() ?>/image"
                    enctype="multipart/form-data"
                    class="book-edit-image-form"
                >
                    <input
                        type="file"
                        name="image"
                        id="book-image"
                        accept="image/*"
                        required
                    >

                    <button type="submit">Modifier la photo</button>
                </form>
            </aside>

            <!-- COLONNE DROITE : formulaire -->
            <div class="book-edit-form">
                <form method="post" action="/book/<?= (int) $book->getId() ?>/edit">

                    <div class="form-group">
                        <label for="title">Titre</label>
                        <input
                            type="text"
                            id="title"
                            name="title"
                            value="<?= htmlspecialchars($book->getTitle()) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="author">Auteur</label>
                        <input
                            type="text"
                            id="author"
                            name="author"
                            value="<?= htmlspecialchars($book->getAuthor()) ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="description">Commentaire</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="8"
                            required
                        ><?= htmlspecialchars($book->getDescription()) ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="status">Disponibilité</label>
                        <select id="status" name="status">
                            <option value="available" <?= $book->getStatus() === 'available' ? 'selected' : '' ?>>
                                disponible
                            </option>
                            <option value="unavailable" <?= $book->getStatus() === 'unavailable' ? 'selected' : '' ?>>
                                indisponible
                            </option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit">Valider</button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</section>

