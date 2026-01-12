<?php declare(strict_types=1);
/**
 * Class AdminController
 *
 * Contrôleur dédié à l’administration de l’application.
 * Il centralise les actions réservées aux utilisateurs disposant
 * de droits administrateur, telles que la gestion des utilisateurs
 * et des livres (consultation, modification de statut, visibilité).
 *
 * L’accès à ce contrôleur est protégé par un contrôle systématique
 * des droits administrateur dès son instanciation.
 *
 * PHP version 8.2.12
 *
 * Date : 9 janvier 2026
 * Maj  : 12 janvier 2026
 *
 * @category Controllers
 * @author   Salem Hadjali <salem.hadjali@gmail.com>
 * @version  1.0.0
 * @since    1.0.0
 * @see      Controller
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Repositories\UserRepository;
use App\Repositories\BookRepository;
use App\Models\Book;
use App\Core\Exception\CsrfException;
use App\Core\Config;


class AdminController extends Controller
{
    private UserRepository $userRepository;
    private BookRepository $bookRepository;


    /**
     * Initialise le contrôleur d’administration.
     *
     * Vérifie que l’utilisateur connecté dispose des droits administrateur
     * avant d’autoriser l’accès aux fonctionnalités associées.
     */
    public function __construct()
    {
        parent::__construct();

        // Accès seulement autorisé aux administrateurs 
        $this->requireAdmin();

        $this->userRepository = new UserRepository();
        $this->bookRepository = new BookRepository();
    }

    /**
     * Affiche le tableau de bord de l’administration.
     *
     * Méthode réservée aux administrateurs.
     *
     * @return void
     */
    public function dashboard(): void
    {
         $this->setPageTitle('Administration');

        $this->render('admin/dashboard', [
            'userCount' => $this->userRepository->countAll(),
            'bookCount' => $this->bookRepository->countAll(),
            'pageStyles'  => ['admin.css'],       
            'pageClass' => 'is-light-page',
            'pageNoticesClass' => 'has-light-page',
        ]);
    }

    /**
     * Affiche la liste paginée des utilisateurs.
     *
     * Méthode réservée aux administrateurs.
     *
     * @return void
     */
    public function users(): void
    {
        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $limit = Config::get('app.admin.pagination.users_limit', 5);;
        $offset = ($page - 1) * $limit;

        $users = $this->userRepository->findPaginated($limit, $offset);
        $total = $this->userRepository->countAll();

        $this->setPageTitle('Utilisateurs');

        $this->render('admin/users', [
            'users' => $users,
            'pagination' => [
                'current' => $page,
                'total'   => (int) ceil($total / $limit),
            ],
            'pageStyles'  => ['admin.css'],       
            'pageClass' => 'is-light-page',
            'pageNoticesClass' => 'has-light-page',
        ]);
    }

    /**
     * Active ou désactive un utilisateur.
     *
     * Vérifie la validité de la requête (POST + CSRF), empêche l’auto-désactivation
     * et applique la modification uniquement si l’utilisateur existe.
     *
     * Méthode réservée aux administrateurs.
     *
     * @return void
     */
    public function toggleUser(): void
    {
        // Contrôle la validité du token CSRF et type de requête POST
        try {
            $this->requireValidPost();
        } catch (CsrfException $e) {
            Session::addFlash('error', 'Action non autorisée.');
            $this->redirect('/admin/users');
        }

        // Récupération de l’ID
        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            $this->redirect('/admin/users');
        }

        // Interdiction d’auto-désactivation
        if ($userId === Session::getUserId()) {
            Session::addFlash(
                'error',
                'Vous ne pouvez pas désactiver votre propre compte.'
            );
            $this->redirect('/admin/users');
        }

        // Vérification d’existence
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            Session::addFlash('error', 'Utilisateur inexistant.');
            $this->redirect('/admin/users');
        }

        // Action métier
        $this->userRepository->toggleActive($userId);

        // Feedback
        Session::addFlash('success', 'Utilisateur mis à jour.');
        $this->redirect('/admin/users');
    }

    /**
     * Affiche la liste paginée des livres.
     *
     * Méthode réservée aux administrateurs.
     *
     * @return void
     */
    public function books(): void
    {
        $page  = max(1, (int) ($_GET['page'] ?? 1));
        $limit = Config::get('app.admin.pagination.books_limit', 5);
        $offset = ($page - 1) * $limit;

        $books = $this->bookRepository->findPaginated($limit, $offset);
        $total = $this->bookRepository->countAll();

        $this->setPageTitle('Livres');

        $this->render('admin/books', [
            'books' => $books,
            'pagination' => [
                'current' => $page,
                'total'   => (int) ceil($total / $limit),
            ],
            'pageStyles'  => ['admin.css'],       
            'pageClass' => 'is-light-page',
            'pageNoticesClass' => 'has-light-page',
        ]);
    }

    /**
     * Bascule la visibilité d’un livre.
     *
     * Vérifie la validité de la requête (POST + CSRF) et s’assure
     * de l’existence du livre avant d’appliquer la modification.
     *
     * Méthode réservée aux administrateurs.
     *
     * @return void
     */
    public function toggleBook(): void
    {
        try {
            $this->requireValidPost();
        } catch (CsrfException $e) {
            Session::addFlash('error', 'Action non autorisée.');
            $this->redirect('/admin/books');
        }

        $bookId = (int) ($_POST['book_id'] ?? 0);

        if ($bookId <= 0) {
            $this->redirect('/admin/books');
        }

        // Vérification d’existence du livre
        $book = $this->bookRepository->findById($bookId);

        if (!$book) {
            Session::addFlash('error', 'Livre inexistant.');
            $this->redirect('/admin/books');
        }

        // Action métier
        $this->bookRepository->toggleVisibility($bookId);

        // Feedback (optionnel mais recommandé)
        Session::addFlash('success', 'Visibilité du livre mise à jour.');

        // Redirection
        $this->redirect('/admin/books');
    }

    /**
     * Modifie le statut d’un livre.
     *
     * Vérifie la validité de la requête (POST + CSRF), contrôle les valeurs autorisées
     * et applique la modification uniquement si le livre existe.
     *
     * Méthode réservée aux administrateurs.
     *
     * @return void
     */
    public function changeBookStatus(): void
    {
        // POST + CSRF
        try {
            $this->requireValidPost();
        } catch (CsrfException $e) {
            Session::addFlash('error', 'Action non autorisée.');
            $this->redirect('/admin/books');
        }

        // Récupération + validation des données
        $bookId = (int) ($_POST['book_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');

        $allowedStatuses = [
            Book::STATUS_AVAILABLE,
            Book::STATUS_UNAVAILABLE,
        ];

        if ($bookId <= 0 || !in_array($status, $allowedStatuses, true)) {
            $this->redirect('/admin/books');
        }

        // Vérification d’existence du livre
        $book = $this->bookRepository->findById($bookId);

        if (!$book) {
            Session::addFlash('error', 'Livre inexistant.');
            $this->redirect('/admin/books');
        }

        // Action métier
        $this->bookRepository->updateStatus($bookId, $status);

        // Feedback utilisateur
        Session::addFlash(
            'success',
            $status === Book::STATUS_AVAILABLE
                ? 'Le livre est maintenant disponible.'
                : 'Le livre est désormais indisponible.'
        );

        // Redirection
        $this->redirect('/admin/books');
    }

}

