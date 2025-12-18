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
 * Maj :         17 décembre 2025
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

        // Validations
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Adresse email invalide.";
        }

        if ($pseudo === '') {
            $errors[] = "Le pseudo est obligatoire.";
        }

        // Unicité email (si changé)
        $existingByEmail = $this->users->findByEmail($email);
        if ($existingByEmail && $existingByEmail->getId() !== $userId) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        // Unicité pseudo (si changé)
        $existingByPseudo = $this->users->findByPseudo($pseudo);
        if ($existingByPseudo && $existingByPseudo->getId() !== $userId) {
            $errors[] = "Ce pseudo est déjà pris.";
        }

        // Mot de passe optionnel
        $passwordHash = null;
        if ($password !== '') {
            if (strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $e) {
                Session::addFlash('error', $e);
            }
            header('Location: /account');
            exit;
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
     * Cette méthode vérifie l’authentification, traite l’upload d’un nouvel
     * avatar via le service AvatarUploader, supprime l’ancien fichier si
     * nécessaire, puis met à jour la référence en base de données.
     *
     * En cas d’erreur (upload invalide, format ou taille incorrecte), un
     * message flash est défini et l’utilisateur est redirigé vers la page
     * "Mon compte" selon le pattern PRG.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas authentifié.
     */
    public function updateAvatar(): void
    {
        // Accès réservé aux utilisateurs connectés
        if (!Session::isLogged()) {
            throw new HttpForbiddenException("Vous devez être connecté.");
        }

        // Récupération du contexte utilisateur courant
        $userId = Session::getUserId();
        $user   = $this->users->findById($userId);

        // Sécurité : session invalide ou utilisateur supprimé
        if (! $user) {
            Session::destroy();
            header('Location: /login');
            exit;
        }

        try {
            // Upload du nouvel avatar (avec suppression éventuelle de l'ancien)
            $newAvatar = AvatarUploader::upload(
                $_FILES['avatar'],
                $user->getRawAvatarPath()
            );
        } catch (\RuntimeException $e) {
            // Gestion d'erreur d'upload (format, taille, permissions...)
            Session::addFlash('error', $e->getMessage());
            header('Location: /account');
            exit;
        }

        // Mise à jour du chemin de l'avatar en base
        $this->users->updateAvatar($userId, $newAvatar);

        // Message de confirmation utilisateur
        Session::addFlash('success', "Avatar mis à jour avec succès.");

        // Redirection vers le compte utilisateur
        header('Location: /account');
        exit;
    }


}

