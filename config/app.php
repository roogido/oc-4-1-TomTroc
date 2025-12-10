<?php

return [   
    // ENVIRONMENT
    'env'   => 'DEV', // ou PROD
    'debug' => true,

    'title' => 'TomTroc', // Titre global par défaut

    // PATHS
    'paths' => [
        'views'        => dirname(__DIR__) . '/views/',
        'views_error'  => dirname(__DIR__) . '/views/error/',
        'layout'       => dirname(__DIR__) . '/views/layout.php',
        'layout_error' => dirname(__DIR__) . '/views/error/layout_error.php',    
    ],
];
