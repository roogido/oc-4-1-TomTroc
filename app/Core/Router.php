<?php declare(strict_types=1);
/**
 * Class Router
 *
 * Routeur chargé d’enregistrer les routes HTTP (GET / POST)
 * et de dispatcher les requêtes vers le handler approprié.
 *
 * Supporte :
 *  - routes statiques
 *  - routes dynamiques avec paramètres (ex: /book/{id})
 *  - handlers sous forme de contrôleur/méthode ou callable
 *
 * Déclenche une exception HttpNotFoundException si aucune route ne correspond.
 *
 * PHP version 8.2.12
 *
 * Date :      7 décembre 2025
 * Maj  :      15 décembre 2025
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        HttpNotFoundException
 */

namespace App\Core;

class Router
{
    private array $routes = [];


    /**
     * Enregistre une route GET associée à un handler contrôleur ou callable.
     *
     * @param string $path
     * @param callable|array $handler
     */    
    public function get(string $path, callable|array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    /**
     * Enregistre une route POST associée à un handler contrôleur ou callable.
     *
     * @param string $path
     * @param callable|array $handler
     */    
    public function post(string $path, callable|array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    /**
     * Analyse l’URL et exécute le handler correspondant à la route.
     *
     * Supporte les routes statiques et dynamiques avec paramètres ({id}).
     * Lève une exception 404 si aucune route ne correspond.
     *
     * @param string $uri    URI complète de la requête HTTP
     * @param string $method Méthode HTTP (GET, POST, ...)
     *
     * @return void
     * @throws HttpNotFoundException
     */
    public function dispatch(string $uri, string $method): void
    {
        // Extrait uniquement le chemin de l’URL (sans ?query=string)
        // Exemple : /book/42?q=test → /book/42
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Parcourt les routes définies pour la méthode HTTP (GET / POST)
        // Si aucune route n’existe pour cette méthode, on parcourt un tableau vide
        foreach ($this->routes[$method] ?? [] as $route => $handler) {

            // Cas 1 : route statique exacte
            // Exemple : route "/" et URL "/"
            if ($route === $path) {
                // Exécute le handler sans paramètre
                $this->executeHandler($handler, []);
                return;
            }

            // Cas 2 : route dynamique avec paramètres (ex : /book/{id})
            // Transformation de la route déclarative en expression régulière
            // Exemple :
            //   /book/{id} → /book/([^/]+)
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);

            // Encadre la regex pour forcer un match complet de l’URL
            // ^ = début de chaîne, $ = fin de chaîne
            $pattern = '#^' . $pattern . '$#';

            // Teste si l’URL courante correspond à la route dynamique
            if (preg_match($pattern, $path, $matches)) {

                // $matches contient :
                // [0] => URL complète matchée (inutile)
                // [1], [2], ... => valeurs des paramètres capturés
                // On supprime donc le match complet
                array_shift($matches);

                // Exécute le handler en lui passant les paramètres extraits
                // Exemple : /book/42 → show(42)
                $this->executeHandler($handler, $matches);
                return;
            }
        }

        // Aucune route ne correspond : erreur 404
        throw new HttpNotFoundException();
    }
 
    /**
     * Exécute le handler associé à une route.
     *
     * Gère les handlers sous forme de contrôleur/méthode
     * ou de fonction/closure avec passage de paramètres dynamiques.
     *
     * @param callable|array $handler Handler à exécuter
     * @param array          $params  Paramètres extraits de l’URL
     *
     * @return void
     */
    private function executeHandler(callable|array $handler, array $params): void
    {
        // Cas 1 : handler sous forme [Controller::class, 'method']
        if (is_array($handler)) {

            // Décomposition du tableau :
            // [0] → nom de la classe du contrôleur
            // [1] → nom de la méthode à appeler
            [$class, $action] = $handler;

            // Instanciation dynamique du contrôleur
            $controller = new $class();

            // Les paramètres extraits de l’URL sont toujours des strings
            // On convertit ici les valeurs numériques ("42") en entiers (42)
            // afin de respecter le typage strict des méthodes du contrôleur
            $params = array_map(
                static fn ($param) => ctype_digit($param) ? (int) $param : $param,
                $params
            );

            // Appel dynamique de la méthode avec paramètres variadiques
            // Exemple :
            //   $params = [42]
            //   → $controller->show(42)
            $controller->{$action}(...$params);
            return;
        }

        // Cas 2 : handler sous forme de fonction / closure
        // Appelle la fonction en lui passant les paramètres sous forme de tableau
        call_user_func_array($handler, $params);
    }
}
