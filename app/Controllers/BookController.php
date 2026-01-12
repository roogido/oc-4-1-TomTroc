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
 * Maj :         10 janvier 2026
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
use App\Core\Exception\CsrfException;
use App\Core\Session;
use App\Repositories\BookRepository;
use App\Models\Book;
use App\Service\Book\BookAccessChecker;
use App\Service\Book\BookFormErrorHandler;
use App\Service\Book\BookFormValidator;
use App\Service\Book\BookImageService;
use App\Core\HttpForbiddenException;
use App\Core\HttpNotFoundException;


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

        $books = $this->books->findAllVisible($search);

        $this->setPageTitle('Nos livres à l’échange');

        $this->render('book/index', [
            'books'  => $books,
            'search' => $search,
            'pageStyles' => ['books.css'],
            'pageClass' => 'has-soft-background',
            'pageNoticesClass' => 'has-soft-background',
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
        $book = $this->books->findVisibleById($id);

        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        $this->setPageTitle($book->getTitle());
        $this->render('book/show', [
            'book' => $book,
            'pageStyles' => ['book-detail.css'],
            'pageClass' => 'is-light-page',
            'pageNoticesClass' => 'is-light-page',
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
        $this->render('book/add', [
            'allowImageEdit' => true,
            'showStatus'     => false,
            'submitLabel'    => 'Ajouter le livre',
            'pageStyles' => ['book-form.css'],        
            'pageClass' => 'is-light-page', 
            'pageNoticesClass' => 'is-light-page',           
        ]);
    }

    /**
     * Traite l’ajout d’un nouveau livre.
     *
     * Vérifie l’authentification, valide les données du formulaire,
     * gère l’upload de l’image et crée le livre en base de données.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas authentifié.
     */
    public function add(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

		// Contrôle la validité du token CSRF et type de requête POST
		try {
			$this->requireValidPost();
		} catch (CsrfException $e) {
			Session::addFlash('error', 'Action non autorisée.');
			$this->redirect('/account');
		}        

        // Données brutes issues du formulaire (nettoyées)
        $data = [
            'title'       => trim($_POST['title'] ?? ''),
            'author'      => trim($_POST['author'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => Book::STATUS_AVAILABLE,
        ];

        // Validation métier des données du formulaire
        $validator = new BookFormValidator();
        $errors = $validator->validate($data);

        // En cas d'erreur réafficher le formulaire
        if (!empty($errors)) {
            $errorHandler = new BookFormErrorHandler();
            $errorHandler->handle($errors, $_POST);

            $this->renderBookForm(
                'book/add',
                'Ajouter un livre',
                [
                    'allowImageEdit' => true,
                    'submitLabel'    => 'Ajouter le livre',
                ]
            );
            return;
        }

        // Création de l’entité Book
        $book = new Book(
            Session::getUserId(),
            $data['title'],
            $data['author'],
            $data['description'],
            $data['status']
        );

        // Gestion de l’upload de l’image
        try {
            $imageService = new BookImageService();
            $imageService->update($book, $_FILES['image'] ?? []);
        } catch (\RuntimeException $e) {
            $errorHandler = new BookFormErrorHandler();
            $errorHandler->handle(['image' => $e->getMessage()], $_POST);

            $this->renderBookForm(
                'book/add',
                'Ajouter un livre',
                [
                    'allowImageEdit' => true,
                    'submitLabel'    => 'Ajouter le livre',
                ]
            );
            return;
        }

        // Persistance en BDD
        $this->books->create($book);

        Session::addFlash('success', 'Livre ajouté avec succès.');
        $this->redirect('/account');
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

        $book = $this->books->findVisibleById($id);

        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        if ($book->getUserId() !== Session::getUserId()) {
            throw new HttpForbiddenException('Ce livre ne vous appartient pas.');
        }

        $this->renderBookForm(
            'book/edit',
            'Modifier les informations',
            [
                'book'           => $book,
                'allowImageEdit' => true,
                'showStatus'     => true,
                'submitLabel'    => 'Valider',
            ]
        );
    }

    /**
     * Traite la modification d’un livre existant.
     *
     * Vérifie l’authentification et les droits d’accès, valide les données du formulaire,
     * met à jour l’image et les informations du livre, puis persiste les changements.
     *
     * @param int $id Identifiant du livre à modifier.
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas authentifié ou autorisé.
     * @throws HttpNotFoundException  Si le livre n’existe pas.
     */
    public function edit(int $id): void
    {
        // Accès réservé aux utilisateurs connectés
        if (!Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

		// Contrôle la validité du token CSRF et type de requête POST
		try {
			$this->requireValidPost();
		} catch (CsrfException $e) {
			Session::addFlash('error', 'Action non autorisée.');
			$this->redirect('/account');
		}
                
        // Récupération du livre
        $book = $this->books->findVisibleById($id);

        if (!$book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        // Vérification des droits d’accès :
        // l’utilisateur ne peut modifier que ses propres livres
        $accessChecker = new BookAccessChecker();
        $accessChecker->checkOwnership($book);

        // Données brutes issues du formulaire (nettoyées)
        $data = [
            'title'       => trim($_POST['title'] ?? ''),
            'author'      => trim($_POST['author'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'status'      => $_POST['status'] ?? Book::STATUS_AVAILABLE,
        ];

        // Validation métier des données du formulaire
        $validator = new BookFormValidator();
        $errors = $validator->validate($data, true);

        // En cas d’erreurs : messages flash + réaffichage du formulaire
        if (!empty($errors)) {
            Session::addFlash(
                'error',
                'Données invalides. Veuillez corriger les champs en erreur.'
            );
            Session::addFlash('error', $errors);
            Session::addFlash('old', $_POST);

            $this->renderBookForm(
                'book/edit',
                'Modifier les informations',
                [
                    'book'           => $book,
                    'allowImageEdit' => true,
                    'showStatus'     => true,
                    'submitLabel'    => 'Valider',
                ]
            );
        }

        // Mise à jour de l’image (upload + suppression éventuelle de l’ancienne)
        $imageService = new BookImageService();
        $imageService->update($book, $_FILES['image'] ?? []);

        // Mise à jour des propriétés du livre
        $book->setTitle($data['title']);
        $book->setAuthor($data['author']);
        $book->setDescription($data['description']);
        $book->setStatus($data['status']);

        // Persistance en BDD
        $this->books->update($book);

        // Message de succès + redirection (PRG)
        Session::addFlash('success', 'Livre modifié avec succès.');
        $this->redirect('/account');
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
     * @throws CsrfException  Si token CSRF inexistant ou invalide.
     */
    public function delete(int $id): void
    {
        if (! Session::isLogged()) {
            throw new HttpForbiddenException('Accès refusé.');
        }

		// Contrôle la validité du token CSRF et type de requête POST
        try {
            $this->requireValidPost();
        } catch (CsrfException $e) {
            Session::addFlash('error', 'Action non autorisée.');
            $this->redirect('/account');
        }        

        $book = $this->books->findVisibleById($id);

        if (! $book) {
            throw new HttpNotFoundException('Livre introuvable.');
        }

        // Vérifie que le livre appartient bien à l'utilisateur       
        $accessChecker = new BookAccessChecker();
        $accessChecker->checkOwnership($book);

        // Suppression de l’image associée (si nécessaire)
        $imageService = new BookImageService();
        $imageService->delete($book);

        // Suppression du livre en BDD
        $this->books->delete($id);

        Session::addFlash('success', 'Livre supprimé.');
        $this->redirect('/account');
    }

    /**
     * Affiche un formulaire de livre avec des paramètres communs.
     *
     * Centralise la configuration du formulaire (options, libellés, layout)
     * afin d’éviter la duplication entre les vues d’ajout et de modification.
     *
     * @param string $view      Vue à afficher.
     * @param string $pageTitle Titre de la page.
     * @param array  $data      Données spécifiques à fusionner avec les valeurs par défaut.
     * @return void
     */
    private function renderBookForm(
        string $view,
        string $pageTitle,
        array $data  = []
    ): void {
        $this->setPageTitle($pageTitle);

        $this->render($view, array_merge([
            // Valeurs par défaut du formulaire
            'allowImageEdit' => false,
            'showStatus'     => false,
            'submitLabel'    => 'Valider',
            'backUrl'        => '/account',

            // Layout
            'pageStyles'     => ['book-form.css'],
            'pageClass'      => 'is-light-page',
            'pageNoticesClass' => 'has-light-page',
        ], $data));
    }
  
}
