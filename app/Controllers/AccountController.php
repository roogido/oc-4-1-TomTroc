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
 * Maj :         -
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
use App\Core\Session;
use App\Core\HttpForbiddenException;
use App\Repositories\UserRepository;


class AccountController extends Controller
{
    private UserRepository $users;


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
    }

    /**
     * Affiche la page "Mon compte" pour l'utilisateur connecté.
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
            // Cas : si session invalide ou utilisateur supprimé
            Session::destroy();
            header('Location: /login');
            exit;
        }

        $this->setPageTitle("Mon compte");
        $this->render('account/index', [
            'user' => $user,
        ]);
    }
}
