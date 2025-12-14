<?php declare(strict_types=1);
/**
 * class Router
 *
 * Routeur minimaliste chargé d’enregistré les routes GET/POST
 * et de dispatcher la requête vers le contrôler ou la fonction
 * correspondante. Génère une exception 404 si aucune route ne correspond.
 *
 * PHP version 8.2.12
 *
 * Date :      7 décembre 2025
 * Maj  :      10 décembre 2025
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        HttpNotFoundException
 * @todo       
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
     * Analyse l’URI et la méthode HTTP, recherche la route correspondante
     * puis exécute le handler associé (contrôleur ou callable).
     * Déclenche une exception 404 si aucune route ne correspond.
     *
     * @param string $uri    URI complète de la requête (ex.: "/books?id=3")
     * @param string $method Méthode HTTP utilisée (GET ou POST)
     */
    public function dispatch(string $uri, string $method): void
    {
         // Récupère uniquement le chemin sans query string
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';

        // Trouve le handler associé à la méthode et au chemin
        $handler = $this->routes[$method][$path] ?? null;

        // Aucune route correspondante : erreur 404
        if ($handler === null) {
            throw new HttpNotFoundException();
        }

        // Handler de type contrôleur : [NomDeClasse, 'methode']
        // On instancie la classe puis on exécute la méthode associée
        if (is_array($handler)) {
            [$class, $action] = $handler;
            $controller       = new $class(); // Instanciation du contrôleur
            $controller->{$action}();

            return;
        }

        // Handler simple : fonction ou closure exécutée directement
        call_user_func($handler);
    }
}
