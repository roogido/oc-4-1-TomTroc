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
            <a href="/books">Découvrir</a>
        </p>
    </div>

    <div class="home-hero-right">
        <img
            src="/assets/images/home/hero-right.png"
            alt="Lecteur en train de lire"
        >
        <p>Hamza</p>
    </div>
</section>

<hr>

<!-- ================= DERNIERS LIVRES ================= -->
<section class="home-last-books">
    <h2>Les derniers livres ajoutés</h2>

    <?php if (empty($books)) : ?>
        <p>Aucun livre disponible pour le moment.</p>
    <?php else : ?>
        <div>
            <?php foreach ($books as $book) : ?>
                <div style="display:inline-block; margin-right:20px; text-align:center; width:200px;">
                    <?php if ($book->getImagePath()) : ?>
                        <a href="/book/<?= (int) $book->getId() ?>">
                            <img
                                src="/<?= htmlspecialchars($book->getImagePath()) ?>"
                                alt="<?= htmlspecialchars($book->getTitle()) ?>"
                                width="200"
                                height="200"
                            >
                        </a>
                    <?php endif; ?>

                    <p><strong><?= htmlspecialchars($book->getTitle()) ?></strong></p>
                    <p><?= htmlspecialchars($book->getAuthor()) ?></p>

                    <p>
                        <em>Vendu par :</em>
                        <a href="/users/<?= (int) $book->getUserId() ?>">
                            <?= htmlspecialchars($book->getOwnerPseudo()) ?>
                        </a>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <p>
            <a href="/books">Voir tous les livres</a>
        </p>
    <?php endif; ?>
</section>

<hr>

<!-- ================= COMMENT ÇA MARCHE ================= -->
<section class="home-how-it-works">
    <h2>Comment ça marche ?</h2>

    <p>
        Échanger des livres avec TomTroc c’est simple et amusant !
        Suivez ces étapes pour commencer :
    </p>

    <div>
        <p>Inscrivez-vous gratuitement sur notre plateforme.</p>
        <p>Ajoutez les livres que vous souhaitez échanger à votre profil.</p>
        <p>Parcourez les livres disponibles chez d'autres membres</p>
        <p>Proposez un échange et discutez avec d'autres passionnés de lecture.</p>
    </div>

    <p>
        <a href="/books">Voir tous les livres</a>
    </p>
</section>

<hr>

<!-- ================= BANDEAU IMAGE ================= -->
<section class="home-banner">
    <img
        src="/assets/images/home/hero-banner.png"
        alt=""
    >
</section>

<hr>

<!-- ================= NOS VALEURS ================= -->
<section class="home-values">
    <h2>Nos valeurs</h2>

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

    <img
        src="/assets/images/home/heart.svg"
        alt=""
    >
</section>
