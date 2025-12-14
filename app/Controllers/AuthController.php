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
 * Maj :         13 décembre 2025
 *
 * @category     Controllers
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          App\Repositories\UserRepository
 * @todo         Ajouter la validation avancée (regex pseudo, force du mot de passe)
 */


namespace App\Controllers;

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
        $this->render('auth/register');
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

        // Vérification que les données POST existent
        $pseudo   = trim($_POST['pseudo']   ?? '');
        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validation 
        $errors = [];

        // Contrôle la validité des données reçues
        if ($pseudo === '') {
            $errors[] = "Le pseudo est obligatoire.";
        }
        // FILTER_VALIDATE_EMAIL : constante php, permettant de vlalider une addresse mail
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }
        if ($password === '' || strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }

        // Vérification unicité email et pseudo
        if ($this->users->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        if ($this->users->findByPseudo($pseudo)) {
            $errors[] = "Ce pseudo est déjà pris.";
        }

        if (!empty($errors)) {
            // On sauvegarde les erreurs en flash
            foreach ($errors as $err) {
                Session::addFlash('error', $err);
            }

            // On renvoie les anciennes valeurs du formulaire (sauf password)
            Session::addFlash('old', [
                'pseudo' => $pseudo,
                'email'  => $email,
            ]);

            $this->render('auth/register');
            return;
        }

        // Hashage sécurisé du mot de passe utilisateur.
        // password_hash() applique un algorithme moderne (bcrypt/argon2 selon config),
        // génère un salt aléatoire, et produit une empreinte non réversible.
        // Le résultat doit être stocké en base (jamais le mot de passe en clair).
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Création de l'objet User
        $user = new User($pseudo, $email, $passwordHash);

        // Insertion
        $this->users->create($user);

        Session::addFlash('success', "Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.");

        // Redirection vers login
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
        $this->render('auth/login');
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
        if (Session::isLogged()) {
            throw new HttpForbiddenException("Vous êtes déjà connecté.");
        }

        $email    = trim($_POST['email']    ?? '');
        $password = trim($_POST['password'] ?? '');

        $errors = [];

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }

        if ($password === '') {
            $errors[] = "Le mot de passe est obligatoire.";
        }

        if (!empty($errors)) {
            foreach ($errors as $err) {
                Session::addFlash('error', $err);
            }

            Session::addFlash('old', ['email' => $email]);

            $this->render('auth/login');
            return;
        }

        // Vérification des identifiants
        $user = $this->users->verifyCredentials($email, $password);

        if (!$user) {
            Session::addFlash('error', "Identifiants incorrects.");
            Session::addFlash('old', ['email' => $email]);
            $this->render('auth/login');
            return;
        }

        // Auth OK : on enregistre en session
        Session::set('user_id', $user->getId());

        Session::addFlash('success', "Connexion réussie !");
        header('Location: /account'); // Redirige vers la page Mon Compte
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
