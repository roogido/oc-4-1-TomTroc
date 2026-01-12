<?php /** @var int $userCount */ ?>
<?php /** @var int $bookCount */ ?>

<section class="dashboard page admin-page">
    <div class="page-inner">
        
        <div class="admin-header">
            <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>
        </div>

        <div class="admin-panel">

            <div class="admin-stats">

                <div class="admin-stat-card">
                    <div class="admin-stat-label">Utilisateurs</div>
                    <div class="admin-stat-value"><?= $userCount ?></div>

                    <div class="admin-stat-action">
                        <a href="/admin/users" class="btn btn-outline btn--sm">
                            Gérer les utilisateurs
                        </a>
                    </div>
                </div>

                <div class="admin-stat-card">
                    <div class="admin-stat-label">Livres</div>
                    <div class="admin-stat-value"><?= $bookCount ?></div>

                    <div class="admin-stat-action">
                        <a href="/admin/books" class="btn btn-outline btn--sm">
                            Gérer les livres
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</section>
