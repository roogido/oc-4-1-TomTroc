<?php declare(strict_types=1);
/**
 * class Controller
 *
 * Classe de base des contrôleurs de l'application. Fournit les méthodes
 * de rendu des vues et gère l'injection sécurisée des paramètres dans
 * les templates. Centralise également l'affichage des pages d'erreur.
 *
 * PHP version 8.2.12
 *
 * Date :      8 décembre 2025
 * Maj  :      6 janvier 2026
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        Config::get()  Pour la récupération des chemins de vues
 * @todo       Ajouter un système de logs pour les erreurs de rendu
 */

namespace App\Core;

use App\Repositories\MessageRepository;
use App\Core\Session;


abstract class Controller
{
    protected string $pageTitle;
    protected int $unreadMessagesCount = 0;



    public function __construct()
    {
        // Valeur par défaut du titre de la page
        $this->pageTitle = Config::get('app.title', 'TomTroc');

        // Permet l'affichage du nbre de messages non lus
        if (Session::isLogged()) {
            $repo = new MessageRepository();
            $this->unreadMessagesCount = $repo->countUnreadByUser(
                Session::getUserId()
            );
        }        
    }

    /**
     * Permet au contrôleur enfant de redéfinir le titre de la page.
     */
    protected function setPageTitle(string $title): void
    {
        if (!str_ends_with($title, 'TomTroc')) {
            $title .= ' - ' . Config::get('app.title');
        }

        $this->pageTitle = $title;
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

        // Ajout du titre à la liste des paramètres transmis à la vue
        $params['pageTitle'] = $this->pageTitle;        

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
