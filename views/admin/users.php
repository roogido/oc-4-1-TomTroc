<section class="users page admin-page">
    <div class="page-inner">

        <div class="admin-header">
            <a href="<?= htmlspecialchars($backUrl ?? '/admin') ?>" class="link-back">
                ← retour
            </a>   

            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <div class="library-card">

            <div class="library-head">
                <div>Pseudo</div>
                <div>Email</div>
                <div>Statut</div>
                <div>Actions</div>
            </div>

            <?php foreach ($users as $user): ?>
                <div class="library-row">

                    <div class="col-title" data-label="Pseudo">
                        <?= htmlspecialchars($user->getPseudo()) ?>
                    </div>

                    <div class="col-email" data-label="Email">
                        <?= htmlspecialchars($user->getEmail()) ?>
                    </div>

                    <div class="col-status" data-label="Statut">
                        <?php if ($user->isActive()): ?>
                            <span class="admin-badge admin-badge--active">Actif</span>
                        <?php else: ?>
                            <span class="admin-badge admin-badge--inactive">Inactif</span>
                        <?php endif; ?>
                    </div>

                    <div class="col-actions admin-actions">
                        <form method="post" action="/admin/users/toggle">
                            <input type="hidden" name="csrf_token" value="<?= $this->generateCsrfToken() ?>">
                            <input type="hidden" name="user_id" value="<?= $user->getId() ?>">

                            <button type="submit" class="btn btn-outline btn--sm">
                                <?= $user->isActive() ? 'Désactiver' : 'Activer' ?>
                            </button>
                        </form>
                    </div>

                </div>
            <?php endforeach; ?>

        </div>

        <?php if ($pagination['total'] > 1): ?>
            <nav class="admin-pagination" aria-label="Pagination utilisateurs">
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
