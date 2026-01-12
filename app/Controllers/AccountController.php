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
 * Maj :         3 janvier 2026
 *
 * @category     Controllers
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          UserRepository, Session
 * @todo         Ajouter actions de mise à jour du profil (avatar, email, mot de passe...)
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Exception\CsrfException;
use App\Core\Session;
use App\Core\HttpForbiddenException;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\BookRepository;
use App\Service\Account\AccountFormValidator;
use App\Service\Account\AccountProfileService;
use App\Service\Account\AvatarService;


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
            $this->redirect('/login');
        }

        $this->renderAccountPage($user);
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
     * @throws CsrfException  Si token CSRF inexistant ou invalide.
     */
    public function update(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException('Vous devez être connecté.');
        }
       
		// Contrôle la validité du token CSRF et type de requête POST
        try {
            $this->requireValidPost();
        } catch (CsrfException $e) {
            Session::addFlash('error', 'Action non autorisée.');
            $this->redirect('/account');
        }

        // Logique métier
        $userId = Session::getUserId();
        $user   = $this->users->findById($userId);

        if (! $user) {
            Session::destroy();
            $this->redirect('/login');
        }

        // Données brutes
        $data = [
            'email'    => $_POST['email'] ?? '',
            'pseudo'   => $_POST['pseudo'] ?? '',
            'password' => $_POST['password'] ?? '',
        ];

        // Validation
        $validator = new AccountFormValidator($this->users);
        $result    = $validator->validate($data, $user);

        if (!empty($result['errors'])) {
            Session::addFlash(
                'error',
                'Données invalides. Veuillez corriger les champs en erreur.'
            );

            Session::addFlash('error', $result['errors']);
            Session::addFlash('old', $_POST);

            $this->renderAccountPage($user);

            return;
        }

        // Mise à jour profil
        $profileService = new AccountProfileService($this->users);
        $profileService->updateProfile(
            $user,
            trim($data['email']),
            trim($data['pseudo']),
            $result['passwordHash']
        );

        Session::addFlash('success', 'Vos informations ont été mises à jour.');
        $this->redirect('/account');
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
     *                                 ans un contexte non-AJAX.
     * @throws CsrfException  Si token CSRF inexistant ou invalide.
     */
    public function updateAvatar(): void
    {
        $isAjax = (
            isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest'
        );

        if (!Session::isLogged()) {
            if ($isAjax) {
                $this->jsonError('Accès non autorisé.', 403);
            }

            throw new HttpForbiddenException('Vous devez être connecté.');
        }

        $user = $this->users->findById(Session::getUserId());

        if (! $user) {
            Session::destroy();

            if ($isAjax) {
                $this->jsonError('Session invalide.', 401);
            }

            $this->redirect('/login');
        }

        try {
            $this->requireValidPost();
        } catch (CsrfException $e) {
            if ($isAjax) {
                $this->jsonError('Action non autorisée (CSRF).', 403);
            }

            Session::addFlash('error', 'Action non autorisée.');
            $this->redirect('/account');
        }        

        // Gestion du fichier transmis (Métier)
        if (empty($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
            if ($isAjax) {
                $this->jsonError('Aucun fichier valide envoyé.', 400);
            }

            Session::addFlash('error', 'Aucun fichier sélectionné.');
            $this->redirect('/account');
        }

        try {
            $avatarService = new AvatarService();
            $newAvatar     = $avatarService->updateAvatar(
                $user,
                $_FILES['avatar'] ?? []
            );

            $this->users->updateAvatar($user->getId(), $newAvatar);
        } catch (\RuntimeException $e) {

            if ($isAjax) {
                $this->jsonError(
                    'Impossible de mettre à jour votre avatar.',
                    400
                );
            }

            Session::addFlash('error', $e->getMessage());
            $this->redirect('/account');
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success'     => true,
                'avatarPath'  => User::AVATAR_UPLOAD_DIR . $newAvatar
            ]);
            exit;
        }

        Session::addFlash('success', 'Avatar mis à jour avec succès.');
        $this->redirect('/account');
    }

    /**
     * Affiche la page de compte de l’utilisateur.
     *
     * Récupère les informations nécessaires (livres, ancienneté, statistiques)
     * puis rend la vue utilisateur.
     *
     * @param User $user Utilisateur connecté.
     * @return void
     */
    private function renderAccountPage(User $user): void
    {
        $userId = $user->getId();

        // Données nécessaires au rendu
        // récupération des livres du user
        $userBooks   = $this->books->findVisibleByUser($userId);

        // Ancienneté de l'utilisateur
        $memberSince = $this->users->getMemberSince($userId);

        // Nombre de livres
        $booksCount  = count($userBooks);

        $this->setPageTitle('Mon compte');

        $this->render('account/index', [
            'user'        => $user,
            'books'       => $userBooks,
            'memberSince' => $memberSince,
            'booksCount'  => $booksCount,
            'pageStyles'  => ['account.css'],
            'pageClass'   => 'is-light-page',
            'pageNoticesClass' => 'has-light-page',
        ]);
    }

    /**
     * Envoie une réponse JSON d’erreur et interrompt l’exécution du script.
     *
     * @param string $message Message d’erreur à retourner.
     * @param int    $status  Code HTTP à renvoyer.
     *
     * @return void
     */
    private function jsonError(string $message, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => $message
        ]);
        exit;
    }   

}

