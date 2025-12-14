<?php if ($book->getImagePath()) : ?>
    <div class="book-image">
        <img
            src="/<?= htmlspecialchars($book->getImagePath()) ?>"
            alt="<?= htmlspecialchars($book->getTitle()) ?>"
            style="max-width:300px;"
        >
    </div>
<?php endif; ?>

<section class="book-show">
    <h1><?= htmlspecialchars($book->getTitle()) ?></h1>

    <div class="book-meta">
        <p><strong>Auteur :</strong> <?= htmlspecialchars($book->getAuthor()) ?></p>
        <p>
            <strong>Disponibilité :</strong>
            <?= $book->getStatus() === 'available' ? 'Disponible' : 'Indisponible' ?>
        </p>
    </div>

    <div class="book-description">
        <p><?= nl2br(htmlspecialchars($book->getDescription())) ?></p>
    </div>

    <p>
        <a href="/books">← Retour aux livres à l’échange</a>
    </p>
</section>
