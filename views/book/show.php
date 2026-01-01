<section class="book-detail page">

    <!-- Fil d’Ariane (plein largeur, aligné logo) -->
    <div class="book-detail-breadcrumb-wrapper">
        <nav class="breadcrumb book-detail-breadcrumb">
            <a href="/books">Nos livres</a>
            <span aria-hidden="true">›</span>
            <span><?= htmlspecialchars($book->getTitle()) ?></span>
        </nav>
    </div>

    <div class="page-inner">

        <!-- Layout 2 colonnes -->
        <div class="book-detail-layout">

            <!-- Colonne gauche : image -->
            <div class="book-detail-image book-card-image">
                <?php if ($book->getStatus() === 'unavailable') : ?>
                    <span class="book-badge book-badge--unavailable">
                        Non dispo.
                    </span>
                <?php endif; ?>

                <img
                    src="/<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                    alt="<?= htmlspecialchars($book->getTitle()) ?>"
                >
            </div>

            <!-- Colonne droite : contenu -->
            <div class="book-detail-content">

                <header class="book-detail-header">
                    <h1><?= htmlspecialchars($book->getTitle()) ?></h1>

                    <p class="book-detail-author">
                        par <?= htmlspecialchars($book->getAuthor()) ?>
                    </p>
                </header>

                <hr class="book-detail-separator">

                <section class="book-detail-description">
                    <h2>DESCRIPTION</h2>
                    <p><?= nl2br(htmlspecialchars($book->getDescription())) ?></p>
                </section>

                <section class="book-detail-owner">
                    <h2>PROPRIÉTAIRE</h2>

                    <a
                        href="/users/<?= (int) $book->getUserId() ?>"
                        class="book-owner-card"
                    >
                        <img
                            src="<?= htmlspecialchars($book->getOwnerAvatarPath()) ?>"
                            alt="Avatar de <?= htmlspecialchars($book->getOwnerPseudo()) ?>"
                            class="avatar avatar--md avatar--portrait"
                        >
                        <span><?= htmlspecialchars($book->getOwnerPseudo()) ?></span>
                    </a>
                </section>

                <?php if (\App\Core\Session::isLogged() && \App\Core\Session::getUserId() !== $book->getUserId()) : ?>
                    <div class="book-detail-action">
                        <a
                            href="/messages/<?= (int) $book->getUserId() ?>"
                            class="btn btn-primary btn--full btn--responsive"
                        >
                            Envoyer un message
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>
