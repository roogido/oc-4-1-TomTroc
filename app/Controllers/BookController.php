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
 * Maj :         -
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
     * (Soumission via form de la page "Mon compte", lien "Ajouter" - Maquette p. 15-23)
     * 
     * Valide les données, crée l’entité Book et persiste le livre.
     * Redirige vers le compte utilisateur en cas de succès.
     *
     * @return void
     * @throws HttpForbiddenException Si l’utilisateur n’est pas connecté
     */
    public function add(): void
    {
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

        $title       = trim($_POST['title'] ?? '');
        $author      = trim($_POST['author'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $status      = $_POST['status'] ?? Book::STATUS_AVAILABLE;

        $errors = [];

        // Contrôle la validité des données reçues
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

        // En cas d'erreur 
        if (! empty($errors)) {
            // On sauvegarde les erreurs en flash
            foreach ($errors as $e) {
                Session::addFlash('error', $e);
            }

            // On renvoie les anciennes valeurs du formulaire
            Session::addFlash('old', [
                'title'       => $title,
                'author'      => $author,
                'description' => $description,
                'status'      => $status,
            ]);

            $this->render('book/add');
            return;
        }

        // Gestion de l’image
        $imagePath = null;

        if (!empty($_FILES['image']['name'])) {
            $file = $_FILES['image'];

            if ($file['error'] !== UPLOAD_ERR_OK) {
                Session::addFlash('error', 'Erreur lors de l’upload de l’image.');
                $this->render('book/add');
                return;
            }

            // Sécurité : types autorisés
            $allowedMimeTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (!in_array(mime_content_type($file['tmp_name']), $allowedMimeTypes, true)) {
                Session::addFlash('error', 'Format d’image non autorisé.');
                $this->render('book/add');
                return;
            }

            // Génération d’un nom unique
            // Format, Ex. : book_656f4c9c9a3c24.12345678.png
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename  = uniqid('book_', true) . '.' . $extension;

            $uploadDir  = __DIR__ . '/../../public/uploads/books/';
            $uploadPath = $uploadDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                Session::addFlash('error', 'Impossible d’enregistrer l’image.');
                $this->render('book/add');
                return;
            }

            // Chemin stocké en BDD (relatif au public/)
            $imagePath = 'uploads/books/' . $filename;
        }

        // Création du livre
        $book = new Book(
            Session::getUserId(),
            $title,
            $author,
            $description,
            $status,
            $imagePath
        );

        $this->books->create($book);

        Session::addFlash('success', 'Livre ajouté avec succès.');
        header('Location: /account'); // Redirection
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
