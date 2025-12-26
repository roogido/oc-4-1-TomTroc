<main class="books page">
    <div class="page-inner">
        <!-- HEADER PAGE -->
        <header class="books-header">
            <h1>Nos livres à l’échange</h1>

            <form method="get" action="/books" class="books-search">
                <input
                    type="search"
                    name="q"
                    placeholder="Rechercher un livre"
                    value="<?= htmlspecialchars($search ?? '') ?>"
                    aria-label="Rechercher un livre"
                >
            </form>
        </header>

        <?php if (empty($books)) : ?>
            <p class="books-empty">Aucun livre disponible.</p>
        <?php else : ?>
            <section class="books-grid">
                <?php foreach ($books as $book) : ?>
                    <article class="book-card">

                        <div class="book-card-image">
                            <?php if ($book->getStatus() === 'unavailable') : ?>
                                <span class="book-badge book-badge--unavailable">
                                    Non dispo.
                                </span>
                            <?php endif; ?>

                            <a href="/book/<?= (int) $book->getId() ?>">
                                <img
                                    src="<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                                    alt="<?= htmlspecialchars($book->getTitle()) ?>"
                                    loading="lazy"
                                >
                            </a>
                        </div>

                        <div class="book-card-body">
                            <h3><?= htmlspecialchars($book->getTitle()) ?></h3>

                            <p class="book-author">
                                <?= htmlspecialchars($book->getAuthor()) ?>
                            </p>

                            <p class="book-owner">
                                Propriétaire :
                                <a href="/users/<?= (int) $book->getUserId() ?>">
                                    <?= htmlspecialchars($book->getOwnerPseudo()) ?>
                                </a>
                            </p>
                        </div>

                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </div>
</main>
