<?php
use App\Core\Session;

/** @var string $pageTitle */
/** @var string $viewFile */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle ?? 'Tom Troc') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<header>
    <nav>
        <div>
            <a href="/">
                <strong>TT</strong> Tom Troc
            </a>
        </div>

        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/books">Nos livres à l’échange</a></li>
        </ul>

        <ul>
            <?php if (Session::isLogged()) : ?>
                <li><a href="/messages">Messagerie</a></li>
                <li><a href="/account">Mon compte</a></li>
            <?php else : ?>
                <li><a href="/login">Connexion</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <?php require $viewFile; ?>
</main>

<footer>
    <nav>
        <ul>
            <li><a href="/privacy">Politique de confidentialité</a></li>
            <li><a href="/legal">Mentions légales</a></li>
            <li>Tom Troc©</li>
        </ul>

        <div>
            <strong>TT</strong>
        </div>
    </nav>
</footer>

</body>
</html>
