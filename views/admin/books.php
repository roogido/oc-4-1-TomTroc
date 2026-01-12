<?php 
use App\Models\Book;
?>

<section class="books page admin-page">
    <div class="page-inner">

        <div class="admin-header">
            <a href="<?= htmlspecialchars($backUrl ?? '/admin') ?>" class="link-back">
                ← retour
            </a>   

            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <div class="library-card">

            <div class="library-head">
                <div>Livre</div>
                <div>Propriétaire</div>
                <div>Disponibilité</div>
                <div>Visibilité</div>
                <div>Actions</div>
            </div>

            <?php foreach ($books as $book): ?>
                <div class="library-row">

                    <div class="col-title" data-label="Livre">
                        <?= htmlspecialchars($book->getTitle()) ?>
                    </div>

                    <div class="col-author" data-label="Propriétaire">
                        <?= htmlspecialchars($book->getOwnerPseudo()) ?>
                    </div>

                    <div class="col-status" data-label="Disponibilité">
                        <?php if ($book->getStatus() === Book::STATUS_AVAILABLE): ?>
                            <span class="admin-badge admin-badge--active">Disponible</span>
                        <?php else: ?>
                            <span class="admin-badge admin-badge--inactive">Indisponible</span>
                        <?php endif; ?>
                    </div>

                    <div class="col-status" data-label="Visibilité">
                        <?php if ($book->isVisible()): ?>
                            <span class="admin-badge admin-badge--active">Visible</span>
                        <?php else: ?>
                            <span class="admin-badge admin-badge--inactive">Masqué</span>
                        <?php endif; ?>
                    </div>

                    <div class="col-actions admin-actions">

                        <form method="post" action="/admin/books/toggle">
                            <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                            <input type="hidden" name="book_id" value="<?= $book->getId() ?>">

                            <button type="submit" class="btn btn-outline btn--sm">
                                <?= $book->isVisible() ? 'Masquer' : 'Afficher' ?>
                            </button>
                        </form>

                        <form method="post" action="/admin/books/status">
                            <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                            <input type="hidden" name="book_id" value="<?= $book->getId() ?>">
                            <input type="hidden" name="status"
                                value="<?= $book->getStatus() === Book::STATUS_AVAILABLE
                                    ? Book::STATUS_UNAVAILABLE
                                    : Book::STATUS_AVAILABLE ?>">

                            <button type="submit" class="btn btn-outline btn--sm">
                                Changer statut
                            </button>
                        </form>

                    </div>

                </div>
            <?php endforeach; ?>

        </div>

        <?php if ($pagination['total'] > 1): ?>
            <nav class="admin-pagination" aria-label="Pagination livres">
                <ul class="admin-pagination-list">

                    <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                        <li>
                            <a
                                href="?page=<?= $i ?>"
                                class="admin-pagination-link <?= $i === $pagination['current'] ? 'is-active' : '' ?>"
                                aria-current="<?= $i === $pagination['current'] ? 'page' : 'false' ?>"
                            >
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                </ul>
            </nav>
        <?php endif; ?>

    </div>
</section>
