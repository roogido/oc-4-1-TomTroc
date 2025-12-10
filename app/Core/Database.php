<?php declare(strict_types=1);
/**
 * class Database
 *
 * Gestion centralisée de la connexion PDO. Implémente un pattern Singleton
 * afin de garantir une unique connexion active durant le cycle de vie
 * de l'application. Paramètres chargés depuis la configuration.
 *
 * PHP version 8.2.12
 *
 * Date :      8 décembre 2025
 * Maj  :      
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        Config::get()   Pour la récupération des paramètres de connexion
 * @todo       Ajouter un mécanisme de log pouur les erreurs de connexion
 */

namespace App\Core;

use PDO;
use PDOException;


class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        // Charger la config
        $config = Config::get('database');

        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database']
        );

        try {
            $pdo = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            throw new PDOException('Database connection error.');
        }

        self::$connection = $pdo;

        return self::$connection;
    }
}

