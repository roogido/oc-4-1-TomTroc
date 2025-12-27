<?php
// VUE HOME / PAGE D'ACCUEIL
?>

<!-- ================= HERO ================= -->
<section class="home-hero">
    <div class="home-hero-left">
        <h1>
            Rejoignez nos<br>
            lecteurs passionnés
        </h1>

        <p>
            Donnez une nouvelle vie à vos livres en les échangeant
            avec d'autres amoureux de la lecture.
            Nous croyons en la magie du partage de connaissances
            et d'histoires à travers les livres.
        </p>
        <p>
            <a href="/books" class="btn btn-primary btn--md btn--responsive">
                Découvrir
            </a>
        </p>
    </div>

    <div class="home-hero-right">
        <img
            src="/assets/images/home/hero-right.webp"
            alt="Bibliothèque remplie de livres, illustrant l’univers de l’échange et de la lecture"
        >
        <p>Hamza</p>
    </div>
</section>

<!-- ================= DERNIERS LIVRES ================= -->
<section class="home-last-books">
    <h2>Les derniers livres ajoutés</h2>

    <?php if (empty($books)) : ?>
        <p class="home-last-books-empty">
            Aucun livre disponible pour le moment.
        </p>
    <?php else : ?>
        <div class="home-last-books-grid">
            <?php foreach ($books as $book) : ?>
                <article class="book-card">
                    <a href="/book/<?= (int) $book->getId() ?>">
                        <img
                            src="<?= htmlspecialchars($book->getImagePathOrDefault()) ?>"
                            alt="<?= htmlspecialchars($book->getTitle()) ?>"
                        >
                    </a>

                    <div class="book-card-body">
                        <h3><?= htmlspecialchars($book->getTitle()) ?></h3>
                        <p class="book-author"><?= htmlspecialchars($book->getAuthor()) ?></p>

                        <p class="book-owner">
                            Propriétaire :
                            <a href="/users/<?= (int) $book->getUserId() ?>">
                                <?= htmlspecialchars($book->getOwnerPseudo()) ?>
                            </a>
                        </p>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="home-last-books-cta">
            <a href="/books" class="btn btn-primary btn--md btn--responsive">
                Voir tous les livres
            </a>
        </div>
    <?php endif; ?>
</section>

<!-- ================= COMMENT ÇA MARCHE ================= -->
<section class="home-how-it-works">
    <h2>Comment ça marche ?</h2>

    <p class="how-intro">
        Échanger des livres avec TomTroc c’est simple et amusant !
        Suivez ces étapes pour commencer :
    </p>

    <div class="how-steps">
        <div class="how-step">
            <p>Inscrivez-vous gratuitement sur notre plateforme.</p>
        </div>

        <div class="how-step">
            <p>Ajoutez les livres que vous souhaitez échanger à votre profil.</p>
        </div>

        <div class="how-step">
            <p>Parcourez les livres disponibles chez d'autres membres.</p>
        </div>

        <div class="how-step">
            <p>Proposez un échange et discutez avec d'autres passionnés de lecture.</p>
        </div>
    </div>

    <div class="how-cta">
        <a href="/books" class="btn btn-outline btn--md btn--responsive">
            Voir tous les livres
        </a>
    </div>
</section>

<!-- ================= BANDEAU IMAGE ================= -->
<section class="home-banner">
    <picture>
        <!-- Mobile -->
        <source
            srcset="/assets/images/home/banner-mobile.webp"
            media="(max-width: 768px)"
        >

        <!-- Desktop / tablette -->
        <img
            src="/assets/images/home/hero-banner.webp"
            alt="Lectrice cherchant un livre dans une bibliothèque"
        >
    </picture>
</section>

<!-- ================= NOS VALEURS ================= -->
<section class="home-values">
    <h2>Nos valeurs</h2>

    <div class="home-values-content">
        <p>
            Chez Tom Troc, nous mettons l'accent sur le 
            partage, la découverte et la communauté. Nos 
            valeurs sont ancrées dans notre passion pour les 
            livres et notre désir de créer des liens entre les 
            lecteurs. Nous croyons en la puissance des histoires 
            pour rassembler les gens et inspirer des 
            conversations enrichissantes.
        </p>

        <p>
            Notre association a été fondée avec une conviction 
            profonde : chaque livre mérite d'être lu et partagé.
        </p>

        <p>
            Nous sommes passionnés par la création d'une 
            plateforme conviviale qui permet aux lecteurs de se 
            connecter, de partager leurs découvertes littéraires 
            et d'échanger des livres qui attendent patiemment 
            sur les étagères.
        </p>

        <p><em>L’équipe Tom Troc</em></p>
    </div>

    <img
        src="/assets/images/home/heart.svg"
        alt="Coeur stylisé de couleur verte"
    >
</section>
