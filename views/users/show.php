<section class="user-public page">

    <div class="page-inner user-public-inner">

        <div class="user-public-layout">

            <!-- ================= LEFT : PROFILE CARD ================= -->
            <aside class="profile-card">

                <div class="profile-avatar">
                    <?php if ($user->getAvatarPath()) : ?>
                        <div class="avatar">
                            <img
                                src="<?= htmlspecialchars($user->getAvatarPath()) ?>"
                                alt="Avatar de <?= htmlspecialchars($user->getPseudo()) ?>"
                                class="img"
                            >
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="profile-separator">

                <h2 class="profile-name"><?= htmlspecialchars($user->getPseudo()) ?></h2>

                <p class="profile-since">
                    Membre depuis <?= htmlspecialchars($memberSince) ?>
                </p>

                <div class="profile-library">
                    <p class="profile-library-label">BIBLIOTHÈQUE</p>

                    <div class="profile-library-meta">
                        <img
                            src="/assets/icons/books_small_icon.svg"
                            alt=""
                            class="library-icon"
                        >
                        <span class="library-count">
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
            <section class="library-card user-library-card">

                <!-- Header desktop -->
                <div class="library-head">
                    <div class="col-photo">PHOTO</div>
                    <div class="col-title">TITRE</div>
                    <div class="col-author">AUTEUR</div>
                    <div class="col-description">DESCRIPTION</div>
                </div>

                <?php if (empty($books)) : ?>
                    <p class="library-empty">Aucun livre.</p>
                <?php else : ?>
                    <?php foreach ($books as $book) : ?>
                        <article class="library-row">

                            <!-- PHOTO -->
                            <div class="col-photo">
                                <img
                                    src="/<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
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

                        </article>
                    <?php endforeach; ?>
                <?php endif; ?>

            </section>

        </div>
    </div>
</section>
