<?php
/**
 * Fichier de définition des routes web de l’application TomTroc.
 *
 * Ce fichier centralise l’ensemble des routes HTTP (GET / POST)
 * et associe chaque URI à un contrôleur et une action.
 *
 * Les routes couvrent :
 *  - les pages publiques
 *  - l’authentification et l’espace utilisateur
 *  - la gestion des livres (CRUD)
 *
 * Les routes dynamiques utilisent la syntaxe {param}
 * et sont résolues par le routeur applicatif.
 *
 * PHP version 8.2.12
 *
 * Date :        15 décembre 2025
 * Maj :         17 décembre 2025
 *
 * @category   Routes
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        App\Core\Router
 */

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\AccountController;
use App\Controllers\BookController;
use App\Controllers\MessageController;
use App\Controllers\PageController;
use App\Controllers\UserController;

/** @var \App\Core\Router $router */


// Pages publiques
$router->get('/', [HomeController::class, 'index']);

// Utilisateurs (public)
$router->get('/users/{id}', [UserController::class, 'show']);

// Authentification
$router->get('/register', [AuthController::class, 'registerForm']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Espace utilisateur (privé)
$router->get('/account', [AccountController::class, 'index']);
$router->post('/account', [AccountController::class, 'update']);
$router->post('/account/avatar', [AccountController::class, 'updateAvatar']);

// Bibliothèque publique
$router->get('/books', [BookController::class, 'index']);
$router->get('/book/{id}', [BookController::class, 'show']);

// Bibliothèque privée (CRUD)
$router->get('/book/add', [BookController::class, 'addForm']);
$router->post('/book/add', [BookController::class, 'add']);
$router->get('/book/{id}/edit', [BookController::class, 'editForm']);
$router->post('/book/{id}/edit', [BookController::class, 'edit']);
$router->post('/book/{id}/delete', [BookController::class, 'delete']);

// Messagerie
$router->get('/messages', [MessageController::class, 'inbox']);
$router->get('/messages/{userId}', [MessageController::class, 'thread']);
$router->post('/messages/send', [MessageController::class, 'send']);

// Pages légales
$router->get('/privacy', [PageController::class, 'privacy']);
$router->get('/legal', [PageController::class, 'legal']);