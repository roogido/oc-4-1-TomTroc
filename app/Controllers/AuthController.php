<?php declare(strict_types=1);
/**
 * Class AuthController
 *
 * Gère l'authentification utilisateur :
 * - Affichage et traitement des formulaires d'inscription et de connexion
 * - Vérification des identifiants via UserRepository
 * - Gestion des sessions (connexion / déconnexion)
 * - Protection des routes (ex. : interdiction d'accès si déjà connecté)
 *
 * PHP version 8.2.12
 *
 * Date :        11 décembre 2025
 * Maj :         29 décembre 2025
 *
 * @category     Controllers
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          App\Repositories\UserRepository
 * @todo         Ajouter la validation avancée (regex pseudo, force du mot de passe)
 */


namespace App\Controllers;

use App\Core\AvatarUploader;
use App\Core\Controller;
use App\Core\Session;
use App\Core\HttpForbiddenException;
use App\Repositories\UserRepository;
use App\Models\User;


class AuthController extends Controller
{
    private UserRepository $users;


    /**
     * Constructeur.
     *
     * Initialise le contrôleur parent et instancie le UserRepository
     * utilisé pour les opérations liées aux utilisateurs.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct(); // initialise le titre de page
        $this->users = new UserRepository();
    }

    /**
     * Affiche le formulaire d'inscription.
     *
     * Si l'utilisateur est déjà connecté, l'accès est interdit
     * et une exception HTTP 403 est levée.
     *
     * @return void
     */
    public function registerForm(): void
    {
        if (Session::isLogged()) {
            // Si ddjà connecté : pas d'accès à l'inscription
            throw new HttpForbiddenException("Vous êtes déjà connecté.");
        }

        $this->setPageTitle('Inscription');
        $this->render('auth/register', [
            'pageStyles' => ['auth.css'],
            'pageClass' => 'is-light-page',
        ]);
    }

    /**
     * Traite la soumission du formulaire d'inscription.
     *
     * Valide les données envoyées, vérifie l'unicité du pseudo et de l'email,
     * crée l'utilisateur en base si tout est correct, et stocke les éventuelles
     * erreurs en flash. Redirige vers /login en cas de succès.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l'utilisateur est déjà connecté.
     */
    public function register(): void
    {
        // Empêche l'inscription si l'utilisateur est déjà connecté
        if (Session::isLogged()) {
            throw new HttpForbiddenException("Vous êtes déjà connecté.");
        }

        // Récupération et nettoyage des données du formulaire
        $pseudo   = trim($_POST['pseudo']   ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        // Tableau collectant les erreurs de validation
        $errors = [];

        // Validation du pseudo
        if ($pseudo === '') {
            $errors['pseudo'] = 'Le pseudo est requis.';
        } elseif ($this->users->findByPseudo($pseudo)) {
            $errors['pseudo'] = "Ce pseudo est déjà pris.";
        }

        // Validation du format de l'email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse email invalide.';
        } elseif ($this->users->findByEmail($email)) {
            $errors['email'] = "Cet email est déjà utilisé.";
        }

        // Validation de la longueur du mot de passe
        if ($password === '' || strlen($password) < 6) {
            $errors['password'] = "Minimum 6 caractères requis.";
        }

        // En cas d'erreurs, on place les messages d'ereur en flash
        if (!empty($errors)) {

            Session::addFlash('error', $errors);

            Session::addFlash('old', [
                'pseudo' => $pseudo,
                'email'  => $email,
            ]);

            header('Location: /register');
            exit;
        }

        // Chemin de l'avatar utilisateur (optionnel)
        $avatarPath = null;

        try {
            // Upload de l'avatar si un fichier est fourni
            if (isset($_FILES['avatar'])) {
                $avatarPath = AvatarUploader::upload($_FILES['avatar']);
            }
        } catch (\RuntimeException $e) {
            // Gestion d'erreur d'upload sans bloquer l'inscription
            Session::addFlash('error', $e->getMessage());
        }

        // Hashage sécurisé du mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Instanciation de l'entité User
        $user = new User($pseudo, $email, $passwordHash, $avatarPath);

        // Persistance de l'utilisateur en base
        $this->users->create($user);

        // Message de confirmation
        Session::addFlash(
            'success',
            "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter."
        );

        // Redirection vers la page de connexion
        header('Location: /login');
        exit;
    }

    /**
     * Affiche le formulaire de connexion.
     *
     * Redirige avec erreur 403 si l'utilisateur est déjà connecté.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l'utilisateur est déjà connecté.
     */

    public function loginForm(): void
    {
        if (Session::isLogged()) {
            throw new HttpForbiddenException("Vous êtes déjà connecté.");
        }

        $this->setPageTitle('Connexion');
        $this->render('auth/login', [
            'pageStyles' => ['auth.css'],
            'pageClass' => 'is-light-page',
        ]);    
    }

    /**
     * Traite la soumission du formulaire de connexion.
     *
     * Valide les données, vérifie les identifiants via le UserRepository
     * et crée la session utilisateur en cas de succès. En cas d’erreur,
     * les messages sont stockés en flash et la vue du formulaire est renvoyée.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l'utilisateur est déjà connecté.
     */
    public function login(): void
    {
        // Empêche la connexion si l'utilisateur est déjà connecté
        if (Session::isLogged()) {
            throw new HttpForbiddenException("Vous êtes déjà connecté.");
        }

        // Récupération et nettoyage des données
        $email    = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Tableau d'erreurs par champ
        $errors = [];

        // Validation email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse email invalide.';
        }

        // Validation mot de passe
        if ($password === '') {
            $errors['password'] = 'Le mot de passe est requis.';
        }

        // Erreurs de validation
        if (!empty($errors)) {
            Session::addFlash('error', $errors);
            Session::addFlash('old', ['email' => $email]);

            header('Location: /login');
            exit;
        }

        // Vérification des identifiants
        $user = $this->users->verifyCredentials($email, $password);

        if (!$user) {
            // Erreur globale (pas liée à un champ précis)
            Session::addFlash('error', 'Identifiants incorrects.');
            Session::addFlash('old', ['email' => $email]);

            header('Location: /login');
            exit;
        }

        // Authentification OK
        Session::set('user_id', $user->getId());

        Session::addFlash('success', 'Connexion réussie !');
        header('Location: /account');
        exit;
    }

    /**
     * Déconnecte l'utilisateur actuellement connecté.
     *
     * Détruit la session puis redirige vers la page d'accueil.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si aucun utilisateur n'est connecté.
     */
    public function logout(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException("Vous n'êtes pas connecté.");
        }

        Session::destroy();

        header('Location: /');
        exit;
    }
}
