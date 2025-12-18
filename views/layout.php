<?php
use App\Core\Session;

/** @var string $pageTitle */
/** @var string $viewFile */
/** @var \App\Models\User|null $currentUser */
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
            <!-- Logo -->
            <div>
                <a href="/">
                    <img
                        src="/assets/images/brand/logo-header.png"
                        alt="TomTroc"
                    >
                </a>
            </div>

            <!-- Menu principal -->
            <ul>
                <li><a href="/">Accueil</a></li>
                <li><a href="/books">Nos livres à l’échange</a></li>
            </ul>

            <!-- Menu utilisateur -->
            <ul>
                <?php if (\App\Core\Session::isLogged()) : ?>
                    <li>
                        <a href="/messages">
                            Messagerie
                            <?php if (!empty($unreadMessagesCount)) : ?>
                                <span class="badge">
                                    <?= (int) $unreadMessagesCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="/account">
                            <img
                                src="<?= htmlspecialchars($currentUser->getAvatarPath()); ?>"
                                alt="Avatar de <?= htmlspecialchars($currentUser->getPseudo()); ?>"
                                width="24"
                                height="24"
                            >
                            Mon compte
                        </a>
                    </li>
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
                <li>Tom Troc ©</li>
            </ul>

            <div>
                <img
                    src="/assets/images/brand/logo-footer.png"
                    alt="TomTroc"
                >
            </div>
        </nav>
    </footer>
</body>
</html>
