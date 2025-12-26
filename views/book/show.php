<div class="book-image">
    <img
        src="<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
        alt="<?= htmlspecialchars($book->getTitle()) ?>"
        style="max-width:300px;"
    >
</div>

<section class="book-show">
    <h1><?= htmlspecialchars($book->getTitle()) ?></h1>

    <div class="book-meta">
        <p>Par <?= htmlspecialchars($book->getAuthor()) ?></p>

        <div class="book-description">
            <p>
                <h2>DESCRIPTION</h2>
                <?= nl2br(htmlspecialchars($book->getDescription())) ?>
            </p>
        </div>
        
        <?php /*
        <p>
            <strong>Disponibilité :</strong>
            <?= $book->getStatus() === 'available' ? 'Disponible' : 'Indisponible' ?>
        </p>
        */ ?>
        <p>
            <h2>PROPRIÉTAIRE</h2>
            <img
                src="<?= htmlspecialchars($book->getOwnerAvatarPath()); ?>"
                alt="Avatar de <?= htmlspecialchars($book->getOwnerPseudo()); ?>"
                width="48"
                height="48"
            >            
            <a href="/users/<?= (int) $book->getUserId() ?>">
                <?= htmlspecialchars($book->getOwnerPseudo()) ?>
            </a>
        </p>
    </div>

    <!-- Bloc messagerie -->
    <?php if (\App\Core\Session::isLogged() && \App\Core\Session::getUserId() !== $book->getUserId()) : ?>
        <section class="book-contact">
            <h2>Contacter le propriétaire</h2>

            <form method="post" action="/messages/send">
                <input type="hidden" name="receiver_id" value="<?= (int) $book->getUserId() ?>">
                <textarea name="content" required></textarea>
                <button type="submit">Envoyer un message</button>
            </form>
        </section>

    <?php elseif (!\App\Core\Session::isLogged()) : ?>
        <p>
            <a href="/login">Connectez-vous</a> pour contacter le propriétaire.
        </p>
    <?php endif; ?>
    <!-- Fin bloc messagerie -->

    <p>
        <a href="/books">← Retour aux livres à l’échange</a>
    </p>
</section>

