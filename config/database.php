<?php
/**
 * Configuration de la connexion à la base de données.
 *
 * Définit les paramètres nécessaires à l’établissement
 * de la connexion PDO (driver, hôte, port, base, identifiants).
 *
 * Ce fichier est chargé par le composant de gestion de la base
 * de données au démarrage de l’application.
 *
 * PHP version 8.2.12
 * 
 * Date :        7 décembre 2025
 * Maj :         8 janvier 2026
 * 
 * @category    Configuration
 * @package     App\Config
 * @author      Salem Hadjali <salem.hadjali@gmail.com>
 * @version     1.0.0
 * @since       1.0.0
 */


return [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'tomtroc',
    'username' => 'CHANGE_ME',
    'password' => 'CHANGE_ME',
];
