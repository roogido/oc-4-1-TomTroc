<?php declare(strict_types=1);
/**
 * index.php
 *
 * Front-controller de l'application.
 * Point d'entrée unique : charge l'autoloader, initialise la session,
 * configure l'environnement et délègue la requête au router.
 *
 * PHP version 8.2.12
 *
 * Date  :     7 décembre 2025
 * Maj   :     8 janvier 2026
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        App\Core\Router
 * @todo       Les routes devront être définie dans un fichier dédié (routes.php) !!!
 */

use App\Core\Config;
use App\Core\Router;
use App\Core\Controller;
use App\Core\HttpForbiddenException;
use App\Core\HttpNotFoundException;
use App\Core\Session;

// Autoloader 
require __DIR__ . '/../app/Core/Autoloader.php';

Session::start();

$ENV = Config::get('app.env', 'DEV');

try {
    // Instanciation du router
    $router = new Router();

    // Chargement des routes
    $routesFile = Config::get('app.paths.routes');

    if (!file_exists($routesFile)) {
        throw new RuntimeException('Routes file not found.');
    }

    require $routesFile;

    // Dispatch de la requête courante
    // Reçoit l'url et la méthode http (GET/POST)
    $router->dispatch(
        $_SERVER['REQUEST_URI'] ?? '/',
        $_SERVER['REQUEST_METHOD'] ?? 'GET'
    );

// Capture d'une éventuelle erreur :
//  - 403 (page non autorisée)
//  - 404 (page non trouvée)
//  - 500 (erreur interne)
} catch (HttpForbiddenException $e) {
    // Envooie au navigateur le code de statut HTTP 403
    http_response_code(403);

    // Affiche la vue/page d'ereur
    Controller::renderError('403', [
        'message' => $e->getMessage()
    ]);

} catch (HttpNotFoundException $e) { 

    http_response_code(404);

    Controller::renderError('404', [
        'message' => $e->getMessage()
    ]);

} catch (Throwable $e) { 

    http_response_code(500);

    if ($ENV === 'DEV') {

        Controller::renderError('dev', [
            'message' => $e->getMessage(),
            'trace'   => $e->getTraceAsString()
        ]);

    } else {

        // Vue générique
        Controller::renderError('500');

        // Log en interne (error_log)
        error_log('[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage());
        error_log($e->getTraceAsString()); // Affiche la pile d'appels
    }

    exit;
}
