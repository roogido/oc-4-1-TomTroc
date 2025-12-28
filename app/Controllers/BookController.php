<?php declare(strict_types=1);
/**
 * Class BookController
 *
 * Contrôleur responsable des actions liées aux livres :
 * affichage public, ajout, modification et suppression.
 * Orchestration des flux HTTP, validation des entrées
 * et délégation au Repository pour la persistance.
 *
 * PHP version 8.2.12
 *
 * Date :        13 décembre 2025
 * Maj :         26 décembre 2025
 *
 * @category     Controllers
 * @package      App\Controllers
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          BookRepository
 * @todo         Extraire la validation métier dans un Service dédié
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\FileUploader;
use App\Core\Session;
use App\Core\HttpForbiddenException;
use App\Core\HttpNotFoundException;
use App\Repositories\BookRepository;
use App\Models\Book;


class BookController extends Controller
{
    private BookRepository $books;


    /**
     * Initialise le contrôleur Book et le repository associé.
     */
    public function __construct()
    {
        parent::__construct();
        $this->books = new BookRepository();  // Instanciation du reposository pour l'accès à la BDD
    }

    /**
     * Affiche la liste des livres disponibles à l’échange.
     *
     * Permet une recherche optionnelle par titre via le paramètre GET "q".
     * Charge les livres disponibles et rend la vue correspondante.
     *
     * @return void
     */
    public function index(): void
    {
        $search = isset($_GET['q']) ? trim($_GET['q']) : null;

        // Pour bien repérer les valeurs nulles
        if ($search === '') {
            $search = null;
        }

        // $books = $this->books->findAllAvailable($search);
        $books = $this->books->findAll($search);

        $this->setPageTitle('Nos livres à l’échange');

        $this->render('book/index', [
            'books'  => $books,
            'search' => $search,
            'pageStyles' => ['books.css'],
        ]);
    }

    /**
     * Affiche le détail public d’un livre à partir de son identifiant.
     * (Maquette p. 9-10 - accès par clic sur l’image / le titre d’un livre)
     *
     * @param int $id Identifiant du livre
     * @return void
     * @throws HttpNotFoundException Si le livre n’existe pas
     */
    public function show(int $id): void
    {
        $book = $this->books->findById($id);

        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        $this->setPageTitle($book->getTitle());
        $this->render('book/show', [
            'book' => $book,
            'pageStyles' => ['book-detail.css'],
            'pageClass' => 'is-light-page',
        ]);
    }

    /**
     * Affiche le formulaire d’ajout d’un livre.
     * (Accès via la page "Mon compte", lien "Ajouter" - Maquette p. 15-23)
     *
     * @return void
     * @throws HttpForbiddenException Si l’utilisateur n’est pas connecté
     */
    public function addForm(): void
    {
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

        $this->setPageTitle('Ajouter un livre');
        $this->render('book/add');
    }

    /**
     * Traite la soumission du formulaire d’ajout d’un livre.
     *
     * Vérifie l’authentification, valide les données reçues,
     * gère l’upload optionnel de l’image et persiste le livre.
     *
     * @return void
     * @throws HttpForbiddenException Si l’utilisateur n’est pas connecté
     */
    public function add(): void
    {
        // Accès réservé aux utilisateurs connectés
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

        // Récupération et nettoyage des données du formulaire
        $title       = trim($_POST['title'] ?? '');
        $author      = trim($_POST['author'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status      = $_POST['status'] ?? Book::STATUS_AVAILABLE;

        $errors = [];

        // Validation des champs obligatoires
        if ($title === '') {
            $errors[] = 'Le titre est obligatoire.';
        }
        if ($author === '') {
            $errors[] = 'L’auteur est obligatoire.';
        }
        if ($description === '') {
            $errors[] = 'La description est obligatoire.';
        }

        // Validation du statut métier
        if (! in_array($status, [Book::STATUS_AVAILABLE, Book::STATUS_UNAVAILABLE], true)) {
            $errors[] = 'Statut invalide.';
        }

        // En cas d’erreurs, stockage en session et réaffichage du formulaire
        if (! empty($errors)) {
            foreach ($errors as $e) {
                Session::addFlash('error', $e);
            }

            // Conservation des valeurs saisies
            Session::addFlash('old', [
                'title'       => $title,
                'author'      => $author,
                'description' => $description,
                'status'      => $status,
            ]);

            $this->render('book/add');
            return;
        }

        // Gestion de l’image (optionnelle)
        $imagePath = null;

        // Upload uniquement si un fichier a été soumis
        if (! empty($_FILES['image']['name'])) {
            try {
                // Upload sécurisé du fichier image
                $newImage = FileUploader::upload(
                    $_FILES['image'],
                    __DIR__ . '/../../public/uploads/books',
                    'book_'
                );

                // Chemin relatif stocké en base de données
                $imagePath = 'uploads/books/' . $newImage;

            } catch (\RuntimeException $e) {
                // Gestion des erreurs d’upload
                Session::addFlash('error', $e->getMessage());
                $this->render('book/add');
                return;
            }
        }

        // Création de l’objet Book
        $book = new Book(
            Session::getUserId(),
            $title,
            $author,
            $description,
            $status,
            $imagePath
        );

        // Insertion en base
        $this->books->create($book);

        // Message de succès
        Session::addFlash('success', 'Livre ajouté avec succès.');

        // Redirection (PRG)
        header('Location: /account');
        exit;
    }

    /**
     * Met à jour l’image associée à un livre existant.
     *
     * Vérifie l’authentification, la propriété du livre,
     * traite l’upload de l’image et met à jour le chemin en base.
     *
     * @param int $id Identifiant du livre
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas autorisé
     * @throws HttpNotFoundException  Si le livre n’existe pas
     */
    public function updateImage(int $id): void
    {
        // Vérifie que l’utilisateur est connecté
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

        // Récupère le livre depuis la base
        $book = $this->books->findById($id);

        // Livre inexistant → 404
        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        // Vérifie que l’utilisateur est le propriétaire du livre
        if ($book->getUserId() !== Session::getUserId()) {
            throw new HttpForbiddenException('Ce livre ne vous appartient pas.');
        }

        // Vérifie qu’un fichier image a bien été envoyé
        if (! isset($_FILES['image'])) {
            Session::addFlash('error', 'Aucune image envoyée.');
            header('Location: /book/' . $id . '/edit');
            exit;
        }

        try {
            // Upload de la nouvelle image (et suppression de l’ancienne si nécessaire)
            $newImage = FileUploader::upload(
                $_FILES['image'],
                __DIR__ . '/../../public/uploads/books', // dossier de destination
                'book_',                                 // préfixe du nom de fichier
                $book->getImagePath()
                    ? basename($book->getImagePath())   // ancienne image à supprimer
                    : null
            );
        } catch (\RuntimeException $e) {
            // Gestion des erreurs d’upload
            Session::addFlash('error', $e->getMessage());
            header('Location: /book/' . $id . '/edit');
            exit;
        }

        // Mise à jour du chemin de l’image en base de données
        $this->books->updateImage(
            $id,
            'uploads/books/' . $newImage
        );

        // Message de succès + redirection
        Session::addFlash('success', 'Photo du livre mise à jour.');
        header('Location: /book/' . $id . '/edit');
        exit;
    }

    /**
     * Affiche le formulaire d’édition d’un livre existant.
     * (Accès via la page "Mon compte", lien "Editer" - Maquette p. 19 - 20)
     *
     * Vérifie l’authentification et la propriété du livre
     * avant d’autoriser l’accès au formulaire.
     *
     * @param int $id Identifiant du livre
     * @return void
     * @throws HttpForbiddenException Si l’utilisateur n’est pas autorisé
     * @throws HttpNotFoundException Si le livre n’existe pas
     */
    public function editForm(int $id): void
    {
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

        $book = $this->books->findById($id);

        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        if ($book->getUserId() !== Session::getUserId()) {
            throw new HttpForbiddenException('Ce livre ne vous appartient pas.');
        }

        $this->setPageTitle('Modifier un livre');
        $this->render('book/edit', [
            'book' => $book,
        ]);
    }

    /**
     * Traite la soumission du formulaire d’édition d’un livre.
     * (Accès via la page "Mon compte", lien "Editer" - Maquette p. 19 - 20)
     *
     * Vérifie l’authentification, la propriété du livre,
     * valide les données puis met à jour le livre en base.
     *
     * @param int $id Identifiant du livre
     * @return void
     * @throws HttpForbiddenException Si l’utilisateur n’est pas autorisé
     * @throws HttpNotFoundException Si le livre n’existe pas
     */
    public function edit(int $id): void
    {
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

        $book = $this->books->findById($id);

        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        if ($book->getUserId() !== Session::getUserId()) {
            throw new HttpForbiddenException('Ce livre ne vous appartient pas.');
        }

        $title       = trim($_POST['title'] ?? '');
        $author      = trim($_POST['author'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status      = $_POST['status'] ?? Book::STATUS_AVAILABLE;

        $errors = [];

        if ($title === '') {
            $errors[] = 'Le titre est obligatoire.';
        }
        if ($author === '') {
            $errors[] = 'L’auteur est obligatoire.';
        }
        if ($description === '') {
            $errors[] = 'La description est obligatoire.';
        }

        if (! in_array($status, [Book::STATUS_AVAILABLE, Book::STATUS_UNAVAILABLE], true)) {
            $errors[] = 'Statut invalide.';
        }

        if (! empty($errors)) {
            foreach ($errors as $e) {
                Session::addFlash('error', $e);
            }

            header("Location: /book/{$id}/edit");
            exit;
        }

        $book->setTitle($title);
        $book->setAuthor($author);
        $book->setDescription($description);
        $book->setStatus($status);

        $this->books->update($book);

        Session::addFlash('success', 'Livre modifié avec succès.');
        header('Location: /account');
        exit;
    }

    /**
     * Supprime un livre appartenant à l’utilisateur courant.
     * (Accès via la page "Mon compte", lien "Supprimer" - Maquette p. 19 - 20)
     *
     * Vérifie l’authentification et la propriété du livre
     * avant d’effectuer la suppression.
     *
     * @param int $id Identifiant du livre
     * @return void
     * @throws HttpForbiddenException Si l’utilisateur n’est pas autorisé
     * @throws HttpNotFoundException Si le livre n’existe pas
     */
    public function delete(int $id): void
    {
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

        $book = $this->books->findById($id);

        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        if ($book->getUserId() !== Session::getUserId()) {
            throw new HttpForbiddenException('Ce livre ne vous appartient pas.');
        }

        $this->books->delete($id);

        Session::addFlash('success', 'Livre supprimé.');
        header('Location: /account');
        exit;
    }
}
