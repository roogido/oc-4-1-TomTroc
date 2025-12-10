<?php
/** @var string|null $lastBookTitle */
?>
<h1>TomTroc - Hello world from DB</h1>

<?php if ($lastBookTitle !== null): ?>
    <p>Dernier livre en base : <?php echo htmlspecialchars($lastBookTitle, ENT_QUOTES, 'UTF-8'); ?></p>
<?php else: ?>
    <p>Aucun livre en base pour le moment.</p>
<?php endif; ?>
