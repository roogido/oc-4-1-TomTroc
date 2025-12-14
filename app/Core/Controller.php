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
 * Maj  :      13 décembre 2025
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        Config::get()  Pour la récupération des chemins de vues
 * @todo       Ajouter un système de logs pour les erreurs de rendu
 */

namespace App\Core;

abstract class Controller
{
    protected string $pageTitle;


    public function __construct()
    {
        // Valeur par défaut du titre de la page
        $this->pageTitle = Config::get('app.title', 'TomTroc');
    }

    /**
     * Permet au contrôleur enfant de redéfinir le titre de la page.
     */
    protected function setPageTitle(string $title): void
    {
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

        // Ajout du titre à la liste des paramètres transmis à la vue
        $params['pageTitle'] = $this->pageTitle;        

        // Transforme un tableau associatif en variables locales EXTR_SKIP évite
        // que les variables critiques du layout soient écrasées par erreur
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
        $viewsErrorPath  = Config::get('app.paths.views_error');
        $layoutErrorPath = Config::get('app.paths.layout_error');

        $viewFile = $viewsErrorPath . $view . '.php';

        if (! file_exists($layoutErrorPath)) {
            http_response_code(500);
            echo "Critical error: error layout missing.";
            return;
        }

        if (! file_exists($viewFile)) {
            http_response_code(500);
            echo "Critical error: error view missing.";
            return;
        }

        $params['pageTitle'] = $params['pageTitle'] ?? 'Erreur - ' . Config::get('app.title');
    
        extract($params, EXTR_SKIP);
        require $layoutErrorPath;
    }
}
