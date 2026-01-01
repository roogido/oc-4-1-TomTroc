<?php declare(strict_types=1);
/**
 * Class AccountController
 *
 * Gère l'espace utilisateur "Mon compte". Ce contrôleur assure l'accès
 * aux informations personnelles du membre connecté (lecture uniquement).
 * L'accès est protégé : un utilisateur non authentifié est bloqué.
 *
 * PHP version 8.2.12
 *
 * Date :        12 décembre 2025
 * Maj :         31 décembre 2025
 *
 * @category     Controllers
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          UserRepository, Session
 * @todo         Ajouter actions de mise à jour du profil (avatar, email, mot de passe...)
 */

namespace App\Controllers;

use App\Core\AvatarUploader;
use App\Core\Controller;
use App\Core\Session;
use App\Core\HttpForbiddenException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\BookRepository;


class AccountController extends Controller
{
    private UserRepository $users;
    private BookRepository $books;

    /**
     * Constructeur.
     * 
     * Initialise le contrôleur et charge le UserRepository.
     *
     * @return void
     */    
    public function __construct()
    {
        parent::__construct();
        $this->users = new UserRepository();
        $this->books = new BookRepository();
    }

    /**
     * Affiche la page "Mon compte" pour l'utilisateur connecté. Et
     * Affchage de ses livres.
     * 
     * Vérifie l'authentification, charge les données du profil via le
     * UserRepository, puis rend la vue correspondante.
     *
     * @throws HttpForbiddenException Si l'utilisateur n'est pas authentifié.
     * @return void
     */    
    public function index(): void
    {
        if (! Session::isLogged()) {
            throw new HttpForbiddenException("Vous devez être connecté pour accéder à cette page.");
        }

        $userId = Session::getUserId();
        $user   = $this->users->findById($userId);

        if (! $user) {
            Session::destroy();
            header('Location: /login');
            exit;
        }

        // récupération des livres du user
        $userBooks = $this->books->findByUser($userId);

        // Ancienneté de l'utilisateur
        $memberSince = $this->users->getMemberSince($userId);

        // Nombre de livres
        $booksCount = count($userBooks);

        $this->setPageTitle("Mon compte");

        $this->render('account/index', [
            'user'  => $user,
            'books' => $userBooks,
            'memberSince' => $memberSince,
            'booksCount'  => $booksCount,    
            'pageStyles' => ['account.css'],        
            'pageClass' => 'is-light-page',
        ]);
    }

    /**
     * Met à jour les informations du profil utilisateur connecté.
     *
     * Traite la soumission du formulaire de modification du compte :
     * - vérifie l’authentification
     * - valide email, pseudo et mot de passe
     * - contrôle l’unicité email/pseudo
     * - met à jour le profil en base de données
     *
     * Le mot de passe est optionnel et uniquement mis à jour s’il est fourni.
     * Redirige vers la page compte avec messages flash en cas de succès ou d’erreur.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas connecté
     */
    public function update(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException("Vous devez être connecté.");
        }

        $userId = Session::getUserId();
        $user   = $this->users->findById($userId);

        if (! $user) {
            Session::destroy();
            header('Location: /login');
            exit;
        }

        // Récupération POST
        $email    = trim($_POST['email'] ?? '');
        $pseudo   = trim($_POST['pseudo'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $errors = [];

        // Validation email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Adresse email invalide.";
        }

        // Validation pseudo
        if ($pseudo === '') {
            $errors['pseudo'] = "Le pseudo est obligatoire.";
        }

        // Unicité email (si changé)
        $existingByEmail = $this->users->findByEmail($email);
        if ($existingByEmail && $existingByEmail->getId() !== $userId) {
            $errors['email'] = "Cet email est déjà utilisé.";
        }

        // Unicité pseudo (si changé)
        $existingByPseudo = $this->users->findByPseudo($pseudo);
        if ($existingByPseudo && $existingByPseudo->getId() !== $userId) {
            $errors['pseudo'] = "Ce pseudo est déjà pris.";
        }

        // Mot de passe optionnel
        $passwordHash = null;
        if ($password !== '') {
            if (strlen($password) < 6) {
                $errors['password'] = "Le mot de passe doit contenir au moins 6 caractères.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        // Erreurs 
        if (!empty($errors)) {
            // Erreur générique
            Session::addFlash(
                'error',
                'Données invalides. Veuillez corriger les champs en erreur.'
            );

            // Erreurs par champ
            Session::addFlash('error', $errors);

            // Anciennes valeurs
            Session::addFlash('old', $_POST);

            // récupération des livres du user
            $userBooks = $this->books->findByUser($userId);

            // Ancienneté de l'utilisateur
            $memberSince = $this->users->getMemberSince($userId);

            // Nombre de livres
            $booksCount = count($userBooks);

            $this->setPageTitle("Mon compte");

            $this->render('account/index', [
                'user'        => $user,
                'books'       => $userBooks,
                'memberSince' => $memberSince,
                'booksCount'  => $booksCount,
                'pageStyles'  => ['account.css'],
                'pageClass'   => 'is-light-page',
            ]);
            return;
        }

        // Mise à jour
        $this->users->updateProfile(
            $userId,
            $email,
            $pseudo,
            $passwordHash
        );

        Session::addFlash('success', "Vos informations ont été mises à jour.");
        header('Location: /account');
        exit;
    }

    /**
     * Met à jour l’avatar de l’utilisateur connecté.
     *
     * Cette méthode vérifie l’authentification de l’utilisateur, traite l’upload
     * d’un nouvel avatar via le service AvatarUploader (avec suppression éventuelle
     * de l’ancien fichier), puis met à jour le chemin de l’avatar en base de données.
     *
     * Elle gère deux contextes :
     * - Requête AJAX : retourne une réponse JSON standardisée (succès ou erreur)
     *   sans redirection.
     * - Requête classique : définit un message flash (succès ou erreur) et applique
     *   le pattern PRG (Post/Redirect/Get) avec redirection vers la page 
     *   "Mon compte".
     *
     * En cas d’erreur (fichier manquant, upload invalide, échec de persistance),
     * une réponse adaptée est renvoyée sans exposer de détails techniques à
     * l’utilisateur.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas authentifié
     *         dans un contexte non-AJAX.
     */
    public function updateAvatar(): void
    {
        $isAjax = (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
        );

        if (!Session::isLogged()) {
            if ($isAjax) {
                http_response_code(403);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Accès non autorisé.'
                ]);
                exit;
            }

            throw new HttpForbiddenException("Vous devez être connecté.");
        }

        $userId = Session::getUserId();
        $user   = $this->users->findById($userId);

        if (!$user) {
            Session::destroy();

            if ($isAjax) {
                http_response_code(401);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Session invalide.'
                ]);
                exit;
            }

            header('Location: /login');
            exit;
        }

        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            if ($isAjax) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Aucun fichier valide envoyé.'
                ]);
                exit;
            }

            Session::addFlash('error', 'Aucun fichier sélectionné.');
            header('Location: /account');
            exit;
        }

        try {
            $newAvatar = AvatarUploader::upload(
                $_FILES['avatar'],
                $user->getRawAvatarPath()
            );
        } catch (\RuntimeException $e) {

            if ($isAjax) {
                http_response_code(400);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Impossible de mettre à jour votre avatar.'
                ]);
                exit;
            }

            Session::addFlash('error', $e->getMessage());
            header('Location: /account');
            exit;
        }

        if (!$this->users->updateAvatar($userId, $newAvatar)) {
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur interne.'
                ]);
                exit;
            }

            Session::addFlash('error', 'Erreur lors de la mise à jour.');
            header('Location: /account');
            exit;
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'    => true,
                'avatarPath'=> User::AVATAR_UPLOAD_DIR . $newAvatar
            ]);
            exit;
        }

        Session::addFlash('success', 'Avatar mis à jour avec succès.');
        header('Location: /account');
        exit;
    }
}

