<?php
/**
 * Configuration globale de l’application.
 *
 * Définit l’environnement d’exécution, les options de débogage,
 * ainsi que les chemins principaux utilisés par l’application
 * (vues, layouts, fichiers publics et uploads).
 *
 * Ce fichier est chargé au démarrage afin de centraliser
 * les paramètres structurants du projet.
 *
 * PHP version 8.2.12
 * 
 * Date :        7 décembre 2025
 * Maj :         12 janvier 2026
 * 
 * @category    Configuration
 * @package     App\Config
 * @author      Salem Hadjali <salem.hadjali@gmail.com>
 * @version     1.0.0
 * @since       1.0.0
 */

return [   
    // ENVIRONMENT
    'env'   => 'DEV', // DEV/PROD
    'debug' => true,

    'title' => 'TomTroc', // Titre global par défaut

    // PATHS
    'paths' => [
        // Views
        'views'        => dirname(__DIR__) . '/views/',
        'views_error'  => dirname(__DIR__) . '/views/error/',
        'layout'       => dirname(__DIR__) . '/views/layout.php',
        
        // Routes
        'routes'       => dirname(__DIR__) . '/app/Routes/web.php',
                
        // Public
        'public'          => dirname(__DIR__) . '/public',
        'uploads'         => dirname(__DIR__) . '/public/uploads',
        'book_uploads'    => dirname(__DIR__) . '/public/uploads/books',
        'avatar_uploads'  => dirname(__DIR__) . '/public/uploads/avatars',        
    ],

    // ADMIN
    'admin' => [
        'pagination' => [
            'users_limit' => 6,
            'books_limit' => 5,
        ],
    ],       

];
