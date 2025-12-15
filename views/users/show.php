<h1><?= htmlspecialchars($user->getPseudo()) ?></h1>

<p>Membre depuis <?= htmlspecialchars($memberSince) ?></p>

<p>
    <strong>BIBLIOTHÈQUE</strong><br>
    <?= count($books) ?> livres
</p>

<?php if (\App\Core\Session::isLogged() && \App\Core\Session::getUserId() !== $user->getId()) : ?>
    <p>
        <a href="/messages/<?= (int) $user->getId() ?>">
            Écrire un message
        </a>
    </p>
<?php endif; ?>

<hr>

<section>
    <?php if (empty($books)) : ?>
        <p>Aucun livre.</p>
    <?php else : ?>
        <?php foreach ($books as $book) : ?>
            <div style="margin-bottom:20px;">
                <?php if ($book->getImagePath()) : ?>
                    <img
                        src="/<?= htmlspecialchars($book->getImagePath()) ?>"
                        width="78"
                        height="78"
                        alt=""
                    >
                <?php endif; ?>

                <a href="/book/<?= (int) $book->getId() ?>">
                    <?= htmlspecialchars($book->getTitle()) ?>
                </a>
                <br>
                <?= htmlspecialchars($book->getAuthor()) ?>

                <p style="margin-top:8px;">
                    <?= nl2br(htmlspecialchars($book->getDescription())) ?>
                </p>                
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
