<h1>Nos livres à l’échange</h1>

<form method="get" action="/books">
    <label for="q">Rechercher par titre :</label>
    <input
        type="text"
        id="q"
        name="q"
        value="<?= htmlspecialchars($search ?? '') ?>"
    >
    <button type="submit">Rechercher</button>
</form>

<hr>

<?php if (empty($books)) : ?>
    <p>Aucun livre disponible.</p>
<?php else : ?>
    <div>
        <?php foreach ($books as $book) : ?>
            <div style="display:inline-block; width:220px; margin:10px; vertical-align:top;">
                
                <a href="/book/<?= (int) $book->getId() ?>">
                    <img
                        src="/<?= htmlspecialchars($book->getImagePath()) ?>"
                        alt="<?= htmlspecialchars($book->getTitle()) ?>"
                        width="200"
                        height="200"
                    >
                    <p><strong><?= htmlspecialchars($book->getTitle()) ?></strong></p>
                    <p><?= htmlspecialchars($book->getAuthor()) ?></p>                  
                </a>
                <p>
                    <em>Vendu par :</em>
                    <a href="/users/<?= (int) $book->getUserId() ?>">
                        <?= htmlspecialchars($book->getOwnerPseudo()) ?>
                    </a>
                </p>
            </div>
        <?php endforeach; ?>
    </div>    

<?php endif; ?>
