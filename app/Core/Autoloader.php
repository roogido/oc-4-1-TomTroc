<?php declare(strict_types=1);
/**
 * Autoloader minimaliste conforme PSR-4 pour le namespace App.
 *
 * Charge automatiquement les classes en convertissant leur namespace
 * en chemin de fichier relatif à /app.
 *
 * Date    : 6 décembre 2025
 * Maj     : -
 * 
 * @author Salem Hadjali <salem.hadjali@gmail.com>
 * 
 */


spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../';

    // Contrôle si $class débute bien par 'App\'
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return; // Classe hors du namespace App
    }

    // convertit le namespace en chemin de fichiier
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});