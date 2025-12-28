<section class="user-public page">

    <div class="page-inner user-public-inner">

        <div class="user-public-layout">

            <!-- ================= LEFT : PROFILE CARD ================= -->
            <aside class="user-profile-card">

                <div class="user-profile-avatar">
                    <?php if ($user->getAvatarPath()) : ?>
                        <div class="user-avatar">
                            <img
                                src="<?= htmlspecialchars($user->getAvatarPath()) ?>"
                                alt="Avatar de <?= htmlspecialchars($user->getPseudo()) ?>"
                                class="user-avatar-img"
                            >
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="user-profile-separator">

                <h2 class="user-profile-name"><?= htmlspecialchars($user->getPseudo()) ?></h2>

                <p class="user-profile-since">
                    Membre depuis <?= htmlspecialchars($memberSince) ?>
                </p>

                <div class="user-profile-library">
                    <p class="user-profile-library-label">BIBLIOTHÈQUE</p>

                    <div class="user-profile-library-meta">
                        <img
                            src="/assets/icons/books_small_icon.svg"
                            alt=""
                            class="user-library-icon"
                        >
                        <span class="user-profile-library-count">
                            <?= count($books) ?> livres
                        </span>
                    </div>
                </div>

                <?php if (\App\Core\Session::isLogged() && \App\Core\Session::getUserId() !== $user->getId()) : ?>
                    <div class="user-profile-action">
                        <a                     
                            class="btn btn-outline btn--full user-message-btn"
                            href="/messages/<?= (int) $user->getId() ?>"
                        >
                            Écrire un message
                        </a>
                    </div>
                <?php endif; ?>

            </aside>


            <!-- ================= RIGHT : LIBRARY LIST ================= -->
            <section class="user-library-card">

                <div class="user-library-table">

                    <!-- Header desktop -->
                    <div class="user-library-head">
                        <div class="col-photo">PHOTO</div>
                        <div class="col-title">TITRE</div>
                        <div class="col-author">AUTEUR</div>
                        <div class="col-description">DESCRIPTION</div>
                    </div>

                    <?php if (empty($books)) : ?>
                        <p class="user-library-empty">Aucun livre.</p>
                    <?php else : ?>
                        <?php foreach ($books as $book) : ?>
                            <article class="user-library-row">

                                <!-- PHOTO -->
                                <div class="col-photo">
                                    <img
                                        class="user-library-book-image"
                                        src="<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                                        alt="<?= htmlspecialchars($book->getTitle()) ?>"
                                        width="78"
                                        height="78"
                                    >
                                </div>

                                <!-- TITRE -->
                                <div class="col-title">
                                    <a class="user-library-book-link" href="/book/<?= (int) $book->getId() ?>">
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

                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>

            </section>

        </div>
    </div>
</section>
