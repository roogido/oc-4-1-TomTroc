<?php declare(strict_types=1);
/**
 * Class Controller
 *
 * Classe de base de l’ensemble des contrôleurs de l’application.
 *
 * Centralise la gestion du cycle de requête HTTP :
 * - contrôle global de l’état de session utilisateur
 * - politiques d’accès (utilisateur actif, administrateur)
 * - protection CSRF des formulaires
 * - gestion des redirections et du rendu des vues
 * - affichage unifié des pages d’erreur
 *
 * Cette classe garantit une séparation entre :
 * - la logique métier (déléguée aux services)
 * - l’orchestration HTTP et la sécurité applicative
 *
 * PHP version 8.2.12
 *
 * Date : 8 décembre 2025
 * Maj  : 10 janvier 2026
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.1.0
 * @since      1.0.0
 *
 * @see        Config::get() Pour la récupération des chemins de vues
 * @see        Session Pour la gestion de l’authentification et des sessions
 *
 * @todo       Ajouter un système de logs centralisé pour les erreurs critiques
 */

namespace App\Core;

use App\Repositories\MessageRepository;
use App\Core\Exception\CsrfException;
use App\Core\Session;
use App\Models\User;
use App\Repositories\UserRepository;


abstract class Controller
{
    protected string $pageTitle;
    protected string $htmlTitle;
    protected int $unreadMessagesCount = 0;



    public function __construct()
    {
        // Valeur par défaut du titre de la page
        $this->pageTitle = Config::get('app.title', 'TomTroc');

        // Révocation globale si l'utilisateur est désactivé
        $this->revokeSessionIfInactive();        

        // Permet l'affichage du nbre de messages non lus
        if (Session::isLogged()) {
            $repo = new MessageRepository();
            $this->unreadMessagesCount = $repo->countUnreadByUser(
                Session::getUserId()
            );
        }        
    }

    /**
     * Révoque la session si l'utilisateur connecté n'est plus actif.
     *
     * Cette méthode est destinée à un contrôle global du cycle de requête.
     * Si l'utilisateur est connecté mais désactivé en base de données, il est
     * automatiquement déconnecté et informé via un message flash, sans lever
     * d'exception HTTP.
     *
     * L'utilisateur redevient alors un visiteur non authentifié.
     *
     * @return void
     */
    protected function revokeSessionIfInactive(): void
    {
        if (Session::isLogged() && !$this->getActiveCurrentUser()) {
            Session::addFlash(
                'error',
                'Votre compte a été désactivé. Veuillez contacter un administrateur.'
            );

            Session::logout();
        }
    }
        
    /**
     * Exige un utilisateur authentifié et actif.
     *
     * Vérifie que l'utilisateur courant existe toujours et que son compte
     * est actif en base de données. En cas d'inactivité ou d'incohérence,
     * la session est révoquée et une exception HTTP 403 est levée afin
     * d'interdire l'accès à la ressource.
     *
     * Cette méthode est destinée aux pages nécessitant un utilisateur valide.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l'utilisateur n'est pas actif.
     */
    protected function requireActiveUser(): void
    {
        if (!$this->getActiveCurrentUser()) {
            Session::logout();
            throw new HttpForbiddenException('Votre compte a été désactivé.');
        }
    }

    /**
     * Exige un utilisateur authentifié disposant des droits administrateur.
     *
     * Vérifie la présence d'une session valide et contrôle le rôle
     * administrateur de l'utilisateur en base de données.
     * Toute tentative d'accès non autorisée entraîne une exception HTTP 403.
     *
     * Cette méthode est destinée aux routes d'administration.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l'utilisateur n'est pas administrateur.
     */
    protected function requireAdmin(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException();
        }        

        $userRepo = new UserRepository();
        $currentUser = $userRepo->findById(Session::getUserId());

        if (!$currentUser || !$currentUser->isAdmin()) {
            throw new HttpForbiddenException('Accès réservé aux administrateurs.');
        }
    }    

    /**
     * Retourne l'utilisateur courant uniquement s'il est authentifié et actif.
     *
     * Recharge l'utilisateur depuis la base de données afin de garantir
     * la validité de son état (ex : compte désactivé).
     * Retourne null si aucun utilisateur valide n'est disponible.
     *
     * Méthode utilitaire interne utilisée par les contrôles d'accès.
     *
     * @return User|null Utilisateur actif ou null.
     */
    private function getActiveCurrentUser(): ?User
    {
        if (!Session::isLogged()) {
            return null;
        }

        $userRepo = new UserRepository();
        $user = $userRepo->findById(Session::getUserId());

        return ($user && $user->isActive()) ? $user : null;
    }

    /**
     * Génère ou retourne le jeton CSRF de la session courante.
     *
     * Le jeton est généré une seule fois par session et stocké afin
     * de protéger les formulaires contre les attaques CSRF.
     *
     * @return string Jeton CSRF.
     */  
    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Vérifie la validité du jeton CSRF soumis.
     *
     * Compare le jeton transmis avec celui stocké en session en utilisant
     * une comparaison sécurisée. En cas d'invalidité, une exception est levée.
     *
     * @return void
     *
     * @throws CsrfException Si le jeton CSRF est invalide ou manquant.
     */
    protected function checkCsrfToken(): void
    {
        $token = $_POST['csrf_token'] ?? '';
        if (
            empty($_SESSION['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $token)
        )  {
            throw new CsrfException('CSRF token invalide');
        }
    }

    /**
     * Vérifie qu'une requête POST est valide et protégée contre le CSRF.
     *
     * @throws HttpForbiddenException
     * @throws CsrfException
     */
    protected function requireValidPost(): void
    {
        // Seules les requêtes POST sont admises
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new HttpForbiddenException();
        }

        // Contrôle la validité du token CSRF
        $this->checkCsrfToken();
    }    

    /**
     * Redirige immédiatement vers une autre URL.
     *
     * Envoie l'en-tête HTTP de redirection puis interrompt l'exécution
     * du script afin d'éviter tout traitement supplémentaire.
     *
     * @param string $path Chemin de destination.
     *
     * @return void
     */
    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }    

    /**
     * Permet au contrôleur enfant de redéfinir le titre de la page.
     */
    protected function setPageTitle(string $title): void
    {

        // Titre de la page
        $this->pageTitle = $title;

        // Titre HTML (head/title) avec suffixe marque
        $this->htmlTitle = $title . ' - ' . Config::get('app.title');
    }

    /**
     * Affiche une vue en injectant les paramètres fournis et en utilisant
     * le layout principal défini dans la configuration.
     *
     * @param string $view   Nom de la vue (sans extension)
     * @param array  $params Variables à rendre disponibles dans la vue
     *
     * @return void
     * @throws \RuntimeException Si la vue ou le layout sont introuvables
     */
    protected function render(string $view, array $params = []): void
    {
        // Paths complet pourles vues et le layout
        $viewsPath   = Config::get('app.paths.views');
        $layoutPath  = Config::get('app.paths.layout');        

        // Path complet de la vue
        $viewFile = $viewsPath . $view . '.php';

        if (! file_exists($layoutPath)) {
            throw new \RuntimeException("Critical error: error layout missing : {$layoutPath}");
        }

        if (! file_exists($viewFile)) {
            throw new \RuntimeException("View not found: {$viewFile}");
        }

        // Environnement (DEV/PROD) 
        $params['appEnv'] = Config::get('app.env');

        // Ajout du titre de la page à la liste des paramètres transmis à la vue
        $params['pageTitle'] = $this->pageTitle;        

        // Ajout du titre (head/title)
        $params['htmlTitle'] = $this->htmlTitle;        

        // Ajout du nbre de messages non lus
        $params['unreadMessagesCount'] = $this->unreadMessagesCount;

        // Injection de l'utilisateur connecté pour le layout
        if (Session::isLogged()) {
            $userRepo = new \App\Repositories\UserRepository();
            $params['currentUser'] = $userRepo->findById(Session::getUserId());
        } else {
            $params['currentUser'] = null;
        }

        // Transforme un tableau associatif en variables locales exploitable dans la vue.
        // EXTR_SKIP évite que les variables critiques du layout soient écrasées par erreur.
        extract($params, EXTR_SKIP);
        require $layoutPath;
    }

    /**
     * Affiche une page d’erreur en utilisant le layout dédié aux erreurs
     * et en injectant les paramètres fournis.
     *
     * @param string $view   Nom de la vue d’erreur
     * @param array  $params Variables à rendre disponibles dans la vue
     *
     * @return void
     */
    public static function renderError(string $view, array $params = []): void
    {
        // Sécurité : interdiction de la vue dev en PROD
        if ($view === 'dev' && Config::get('app.env') !== 'DEV') {
            $view = '500';
            unset($params['trace']);
        }

        $viewsErrorPath = Config::get('app.paths.views_error');
        $layoutPath    = Config::get('app.paths.layout');

        $viewFile = $viewsErrorPath . $view . '.php';

        if (!file_exists($layoutPath) || !file_exists($viewFile)) {
            http_response_code(500);
            echo 'Critical error.';
            return;
        }

        // Titre par défaut
        $params['pageTitle'] = $params['pageTitle']
            ?? 'Erreur - ' . Config::get('app.title');

        // Environnement (DEV/PROD) 
        $params['appEnv'] = Config::get('app.env');

        // CSS spécifique erreur
        $params['pageStyles'][] = 'error.css';

        // Variables attendue par le layout
        $params['currentUser'] = Session::isLogged()
            ? (new \App\Repositories\UserRepository())->findById(Session::getUserId())
            : null;

        $params['unreadMessagesCount'] = 0;

        extract($params, EXTR_SKIP);
        require $layoutPath;
    }

}
