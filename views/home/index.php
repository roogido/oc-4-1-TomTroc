<h1>Rejoignez nos lecteurs passionnés</h1>

<section class="home-last-books">
    <h2>Les derniers livres ajoutés</h2>

    <?php if (empty($books)) : ?>
        <p>Aucun livre disponible pour le moment.</p>
    <?php else : ?>
        <div>
            <?php foreach ($books as $book) : ?>
                <div style="display:inline-block; margin-right:10px; text-align:center;">
                    <?php if ($book->getImagePath()) : ?>
                        <a href="/book/<?= (int) $book->getId() ?>">
                            <img
                                src="/<?= htmlspecialchars($book->getImagePath()) ?>"
                                alt="<?= htmlspecialchars($book->getTitle()) ?>"
                                width="200"
                                height="200"
                            >

                            <p><strong><?= htmlspecialchars($book->getTitle()) ?></strong></p>
                            <p><?= htmlspecialchars($book->getAuthor()) ?></p>
                            <p><em>Vendu par : <?= htmlspecialchars($book->getOwnerPseudo()) ?></em></p>                              
                        </a>
                    <?php else : ?>
                        <div
                            style="width:200px; height:200px; border:1px solid #ccc;"
                        >
                            Pas d’image
                        </div>
                    <?php endif; ?>                    
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

