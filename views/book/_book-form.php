<?php
/* VUE BOOK-ADD/EDIT */

/**
 * Partial: Book form (add / edit)
 * Variables attendues :
 * - $allowImageEdit (bool)
 * - $showStatus     (bool)
 * - $submitLabel    (string)
 * - $book           (?Book)
 * - $old            (array)
 */

$allowImageEdit ??= false;
$showStatus     ??= false;
$submitLabel    ??= 'Valider';
$book           ??= null;
$old            ??= [];
?>

<section class="book-form-page">
    <!-- ================= PAGE HEADER ================= -->
    <header class="book-form-header">

        <?php if (!empty($pageTitle)) : ?>
            <header class="book-form-header">
                <a href="<?= htmlspecialchars($backUrl ?? '/account') ?>" class="book-form-back">
                    ← retour
                </a>

                <!-- ================= PAGE TITLE ================= -->
                <h1 class="page-title">
                    <?= htmlspecialchars($pageTitle) ?>
                </h1>
            </header>
        <?php endif; ?>

    </header>

    <!-- ===== Alertes : success/errors  ===== -->
    <?php require __DIR__ . '/../partials/alerts.php'; ?>
   
    <div class="book-form-card">

        <form method="post" enctype="multipart/form-data" novalidate>

            <div class="book-form-layout">

                <!-- ================= IMAGE ================= -->
                <div class="book-form-media">

                    <label class="book-form-label">Photo</label>

                    <div
                        class="book-form-image <?= isset($errors['image']) ? 'is-invalid' : '' ?>"
                        aria-invalid="<?= isset($errors['image']) ? 'true' : 'false' ?>"
                        <?= isset($errors['image']) ? 'aria-describedby="image-error"' : '' ?>
                    >
                        <img
                            id="book-image-preview"
                            src="/<?= htmlspecialchars(
                                $book
                                    ? $book->getImagePathOrDefault()
                                    : ($old['image'] ?? 'uploads/books/book-default.webp')
                            ) ?>"
                            alt="Couverture du livre"
                        >
                    </div>

                    <?php if ($allowImageEdit) : ?>
                        <label class="book-form-image-action">
                            Modifier la photo
                            <input
                                type="file"
                                id="image"
                                name="image"
                                accept="image/*"
                                hidden
                            >
                        </label>
                    <?php endif; ?>

                    <?php if (isset($errors['image'])) : ?>
                        <div id="image-error" class="form-error" role="alert">
                            <?= htmlspecialchars($errors['image']) ?>
                        </div>
                    <?php endif; ?>

                </div>

                <!-- ================= FIELDS ================= -->
                <div class="book-form-fields">
                    <div class="form-group<?= isset($errors['title']) ? ' form-group--error' : '' ?>">
                        <label for="title">Titre</label>

                        <input
                            id="title"
                            type="text"
                            name="title"
                            value="<?= htmlspecialchars($old['title'] ?? ($book?->getTitle() ?? '')) ?>"
                            <?= isset($errors['title']) ? 'aria-invalid="true" aria-describedby="title-error"' : '' ?>
                            required
                        >

                        <?php if (isset($errors['title'])) : ?>
                            <p class="form-error" id="title-error">
                                <?= htmlspecialchars($errors['title']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group<?= isset($errors['author']) ? ' form-group--error' : '' ?>">
                        <label for="author">Auteur</label>

                        <input
                            id="author"
                            type="text"
                            name="author"
                            value="<?= htmlspecialchars($old['author'] ?? ($book?->getAuthor() ?? '')) ?>"
                            <?= isset($errors['author']) ? 'aria-invalid="true" aria-describedby="author-error"' : '' ?>
                            required
                        >

                        <?php if (isset($errors['author'])) : ?>
                            <p class="form-error" id="author-error">
                                <?= htmlspecialchars($errors['author']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group<?= isset($errors['description']) ? ' form-group--error' : '' ?>">
                        <label for="description">Commentaire</label>

                        <textarea
                            id="description"
                            name="description"
                            rows="6"
                            <?= isset($errors['description']) ? 'aria-invalid="true" aria-describedby="description-error"' : '' ?>
                        ><?= htmlspecialchars(
                            $old['description'] ?? ($book?->getDescription() ?? '')
                        ) ?></textarea>

                        <?php if (isset($errors['description'])) : ?>
                            <p class="form-error" id="description-error">
                                <?= htmlspecialchars($errors['description']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($showStatus) : ?>
                        <div class="form-group form-group--select<?= isset($errors['status']) ? ' form-group--error' : '' ?>">
                            <label for="status">Disponibilité</label>

                            <select
                                id="status"
                                name="status"
                                <?= isset($errors['status']) ? 'aria-invalid="true" aria-describedby="status-error"' : '' ?>
                            >
                                <option value="available"
                                    <?= (($old['status'] ?? $book?->getStatus()) === 'available') ? 'selected' : '' ?>>
                                    disponible
                                </option>

                                <option value="unavailable"
                                    <?= (($old['status'] ?? $book?->getStatus()) === 'unavailable') ? 'selected' : '' ?>>
                                    non dispo.
                                </option>
                            </select>

                            <?php if (isset($errors['status'])) : ?>
                                <p class="form-error" id="status-error">
                                    <?= htmlspecialchars($errors['status']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="book-form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?= htmlspecialchars($submitLabel) ?>
                        </button>
                    </div>

                </div>

            </div>

        </form>

    </div>

</section>
