<section class="user-public">
    <!-- Colonne gauche : profil -->
    <aside class="user-profile">
        <div class="user-avatar">
            <?php if ($user->getAvatarPath()) : ?>
                <img
                    src="<?= htmlspecialchars($user->getAvatarPath()) ?>"
                    alt="Avatar de <?= htmlspecialchars($user->getPseudo()) ?>"
                >
            <?php endif; ?>
        </div>

        <h1 class="user-name"><?= htmlspecialchars($user->getPseudo()) ?></h1>

        <p class="user-since">
            Membre depuis <?= htmlspecialchars($memberSince) ?>
        </p>

        <p class="user-library-count">
            <strong>BIBLIOTHÈQUE</strong><br>
            <?= count($books) ?> livres
        </p>

        <?php if (\App\Core\Session::isLogged() && \App\Core\Session::getUserId() !== $user->getId()) : ?>
            <a
                class="btn btn-primary user-message-btn"
                href="/messages/<?= (int) $user->getId() ?>"
            >
                Écrire un message
            </a>
        <?php endif; ?>
    </aside>

    <!-- Colonne droite : bibliothèque -->
    <section class="user-library">
        <div class="library-header">
            <div class="col-photo">PHOTO</div>
            <div class="col-title">TITRE</div>
            <div class="col-author">AUTEUR</div>
            <div class="col-description">DESCRIPTION</div>
        </div>
        <?php if (empty($books)) : ?>
            <p>Aucun livre.</p>
        <?php else : ?>
            <?php foreach ($books as $book) : ?>
                <div class="library-row">

                    <!-- PHOTO -->
                    <div class="col-photo">
                        <img
                            src="<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                            alt="<?= htmlspecialchars($book->getTitle()) ?>"
                            width="78"
                            height="78"
                        >
                    </div>

                    <!-- TITRE -->
                    <div class="col-title">
                        <a href="/book/<?= (int) $book->getId() ?>">
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

                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</section>


