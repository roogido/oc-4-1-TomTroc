<?php
use App\Core\Config;
use App\View\FlashHelper;

/** @var string $pageTitle */
/** @var string $viewFile */
/** @var \App\Models\User|null $currentUser */

// Récupération des messages flash normalisés :
//  - (erreurs globales, erreurs par champ, anciennes valeurs, succès)
//  extract() : créer automatiquement les variables utilisables directement dans la vue
extract(FlashHelper::extract());

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($htmlTitle ?? Config::get('app.title')) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Google Fonts : Inter + Playfair Display -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@200;300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap"
        rel="stylesheet"
    >

    <link rel="stylesheet" href="/assets/css/reset.css">
    <link rel="stylesheet" href="/assets/css/layout.css">
    <link rel="stylesheet" href="/assets/css/components.css">

    <?php if (!empty($pageStyles)) : ?>
        <?php foreach ($pageStyles as $css) : ?>
            <link rel="stylesheet" href="/assets/css/<?= htmlspecialchars($css) ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>

    <div class="site-wrapper<?= !empty($pageClass) ? ' ' . htmlspecialchars($pageClass) : '' ?>">
        <header class="site-header desktop-header">
            <nav class="header-container">

                <!-- Bloc gauche : logo + menu -->
                <div class="header-left">

                    <?php if (($appEnv ?? '') === 'DEV') : ?>
                        <span class="env-badge" aria-hidden="true">DEV</span>
                    <?php endif; ?>                  

                    <!-- Logo -->
                    <a href="/" class="logo">
                        <img
                            src="/assets/images/brand/logo-header.webp"
                            alt="Tom Troc"
                            width="155"
                            height="51"
                        >
                    </a>

                    <!-- Menu principal -->
                    <ul class="main-nav">
                        <li>
                            <a
                                href="/"
                                class="<?= $currentPath === '/' ? 'is-active' : '' ?>"
                            >
                                Accueil
                            </a>
                        </li>

                        <li>
                            <a
                                href="/books"
                                class="<?= str_starts_with($currentPath, '/books') ? 'is-active' : '' ?>"
                            >
                                Nos livres à l’échange
                            </a>
                        </li>
                    </ul>

                </div>

                <span class="header-separator" aria-hidden="true"></span>

                <!-- Bloc droit : actions utilisateur -->
                <ul class="header-right">
                    <?php if (\App\Core\Session::isLogged()) : ?>
                        <li>
                            <a
                                href="/messages"
                                class="header-action <?= str_starts_with($currentPath, '/messages') ? 'is-active' : '' ?>"
                            >
                                
                                <img
                                    src="/assets/icons/message.webp"
                                    alt=""
                                    aria-hidden="true"
                                >
                                <span>Messagerie</span>

                                <?php if (!empty($unreadMessagesCount)) : ?>
                                    <span class="badge">
                                        <?= (int) $unreadMessagesCount ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <?php require __DIR__ . '/partials/header.php'; ?>  

                        <li>
                            <a
                                href="/account"
                                class="header-action <?= str_starts_with($currentPath, '/account') ? 'is-active' : '' ?>"
                            >
                                <img
                                    src="<?= htmlspecialchars($currentUser->getAvatarPath()); ?>"
                                    alt="Avatar de <?= htmlspecialchars($currentUser->getPseudo()); ?>"
                                    class="avatar avatar--header"
                                    width="40"
                                    height="40"
                                    id="header-avatar-img"
                                >

                                <span>Mon compte</span>
                            </a>
                        </li>
                    <?php else : ?>
                        <li>
                            <a
                                href="/login"
                                class="header-action <?= str_starts_with($currentPath, '/login') ? 'is-active' : '' ?>"
                            >
                                <img
                                    src="/assets/icons/account.webp"
                                    alt=""
                                    aria-hidden="true"
                                >
                                <span>Connexion</span>
                            </a>
                        </li>

                    <?php endif; ?>
                </ul>
            </nav>
        </header>

        <header class="site-header mobile-header">
            <div class="header-container">

                <a href="/" class="logo">
                    <img
                        src="/assets/images/brand/logo-header.webp"
                        alt="Tom Troc"
                        width="28"
                        height="28"
                    >
                </a>

                <button
                    class="burger"
                    aria-label="Ouvrir le menu"
                    aria-expanded="false"
                    aria-controls="mobile-menu"
                >
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                    <span class="burger-line"></span>
                </button>

            </div>
        </header>

        <!-- Menu pour mobile -->
        <nav
            id="mobile-menu"
            class="mobile-menu"
            aria-label="Menu principal"
            hidden
        >
            <ul class="mobile-nav">
                <li>
                    <a href="/" class="<?= $currentPath === '/' ? 'is-active' : '' ?>">
                        Accueil
                    </a>
                </li>

                <li>
                    <a
                        href="/books"
                        class="<?= str_starts_with($currentPath, '/books') ? 'is-active' : '' ?>"
                    >
                        Nos livres à l’échange
                    </a>
                </li>
            </ul>

            <ul class="mobile-actions">
                <?php if (\App\Core\Session::isLogged()) : ?>
                    <li>
                        <a
                            href="/messages"
                            class="<?= str_starts_with($currentPath, '/messages') ? 'is-active' : '' ?>"
                        >
                            <img
                                src="/assets/icons/message.webp"
                                alt=""
                                aria-hidden="true"
                            >                        
                            <span>Messagerie</span>
                            <?php if (!empty($unreadMessagesCount)) : ?>
                                <span class="badge">
                                    <?= (int) $unreadMessagesCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <?php require __DIR__ . '/partials/header.php'; ?>                

                    <li>
                        <a
                            href="/account"
                            class="<?= str_starts_with($currentPath, '/account') ? 'is-active' : '' ?>"
                        >
                            <img
                                src="<?= htmlspecialchars($currentUser->getAvatarPath()); ?>"
                                alt="Avatar de <?= htmlspecialchars($currentUser->getPseudo()); ?>"
                                class="avatar"
                                width="40"
                                height="40"
                            >
                            <span>Mon compte</span>
                        </a>
                    </li>
                <?php else : ?>
                    <li>
                        <a
                            href="/login"
                            class="<?= str_starts_with($currentPath, '/login') ? 'is-active' : '' ?>"
                        >
                            <img
                                src="/assets/icons/account.webp"
                                alt=""
                                aria-hidden="true"
                            >
                            <span>Connexion</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Zone de notification -->
        <div class="page-notices<?= !empty($pageNoticesClass) ? ' ' . htmlspecialchars($pageNoticesClass) : '' ?>">
            <?php require __DIR__ . '/partials/alerts.php'; ?>
        </div>        

        <!-- Contenu principal de la page -->
        <main>
            <?php require $viewFile; ?>
        </main>

        <footer class="site-footer desktop-footer">
            <div class="footer-container">
                <nav class="footer-links" aria-label="Liens légaux">
                    <a href="/privacy">Politique de confidentialité</a>
                    <a href="/legal">Mentions légales</a>
                </nav>

                <div class="footer-brand">
                    <span class="footer-name">Tom Troc ©</span>
                    <img
                        src="/assets/images/brand/logo-footer.webp"
                        alt=""
                        aria-hidden="true"
                        class="footer-logo"
                    >
                </div>
            </div>
        </footer>

        <footer class="site-footer mobile-footer">
            <nav aria-label="Liens du pied de page">
                <ul class="footer-links">
                    <li><a href="/privacy">Politique de confidentialité</a></li>
                    <li><a href="/legal">Mentions légales</a></li>
                    <li class="footer-copy">Tom Troc ©</li>
                </ul>
            </nav>

            <div class="footer-logo-mobile" aria-hidden="true">
                <img
                    src="/assets/images/brand/logo-footer.webp"
                    alt=""
                >
            </div>
        </footer>
    </div>

    <?php require __DIR__ . '/partials/confirm-modal.php'; ?>

    <script src="/assets/js/main.js"></script>
</body>
</html>
