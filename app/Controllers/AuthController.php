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
 * Maj :         3 janvier 2026
 *
 * @category     Controllers
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          App\Repositories\UserRepository
 * @todo         
 */


namespace App\Controllers;

use App\Core\AvatarUploader;
use App\Core\Controller;
use App\Core\Session;
use App\Core\HttpForbiddenException;
use App\Repositories\UserRepository;
use App\Service\Auth\AuthRegisterValidator;
use App\Service\Auth\AuthRegistrationService;
use App\Service\Auth\AuthLoginValidator;
use App\Service\Auth\AuthAuthenticationService;


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
        if (Session::isLogged()) {
            throw new HttpForbiddenException("Vous êtes déjà connecté.");
        }

        // Validation
        $validator = new AuthRegisterValidator($this->users);
        $result    = $validator->validate($_POST);

        if (!empty($result['errors'])) {
            Session::addFlash('error', $result['errors']);
            Session::addFlash('old', [
                'pseudo' => $result['data']['pseudo'],
                'email'  => $result['data']['email'],
            ]);

            header('Location: /register');
            exit;
        }

        // Upload avatar (optionnel, non bloquant)
        $avatar = null;
        try {
            if (!empty($_FILES['avatar'])) {
                $avatar = AvatarUploader::upload($_FILES['avatar']);
            }
        } catch (\RuntimeException $e) {
            Session::addFlash('error', $e->getMessage());
        }

        // Création utilisateur
        $service = new AuthRegistrationService($this->users);
        $service->register(
            $result['data']['pseudo'],
            $result['data']['email'],
            $result['data']['password'],
            $avatar
        );

        Session::addFlash(
            'success',
            "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter."
        );

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

        // Validation des données
        $validator = new AuthLoginValidator();
        $result    = $validator->validate($_POST);

        if (!empty($result['errors'])) {
            Session::addFlash('error', $result['errors']);
            Session::addFlash('old', ['email' => $result['email']]);

            header('Location: /login');
            exit;
        }

        // Authentification
        try {
            $authService = new AuthAuthenticationService($this->users);
            $user = $authService->authenticate(
                $result['email'],
                $result['password']
            );
        } catch (\RuntimeException $e) {
            Session::addFlash('error', $e->getMessage());
            Session::addFlash('old', ['email' => $result['email']]);

            header('Location: /login');
            exit;
        }

        // Connexion réussie
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
