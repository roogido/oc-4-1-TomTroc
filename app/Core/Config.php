<?php declare(strict_types=1);
/**
 * class Config
 * 
 * Classe centralisée de configuration.
 * Cette classe charge automatiquement les fichiers du dossier /config
 * et permet de récupérer n'importe quel paramètre via Config::get().
 *
 * Exemple :
 *   Config::get('app.env');
 *   Config::get('database.host'); 

 * PHP version 8.2.12
 * 
 * Date :      10 décembre 2025
 * Maj  :
 * 
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      
 * @see      
 * @todo       ...  
 */

namespace App\Core;


class Config
{
    private static array $config = [];


    /**
     * Charge tous les fichiers du dossier /config au premier appel.
     */
    private static function load(): void
    {
        if (!empty(self::$config)) {
            return; // Déjà chargé
        }

        $configDir = dirname(__DIR__, 2) . '/config';

        // Lecture de tous les fichier de la directory
        foreach (glob($configDir . '/*.php') as $file) {
            $key = basename($file, '.php');

            // Insère chaque tableau retourné dans le tableau $config ac. 
            // comme nom de clé, le préfixe du nom de fichier
            self::$config[$key] = require $file;
        }
    }

    /**
     * Récupère un paramètre de config via une notation pointée.
     * La première partie (avant le premier '.') correspond au
     * nom de fichier, les parties suivantes clé (ou sous-array)
     * 
     * Exemple : Config::get('database.host')
     * Retourne soit la valeur de la variable attendu soit un array
     * contenant l'ensemble des variables ou la valeur par défaut.
     *
     * @param string $path
     * @param mixed|null $default valeur de secours
     * @return mixed
     */
    public static function get(string $path, mixed $default = null): mixed
    {
        // Lecture des fichiers de la configuration (la première fois seulement)
        self::load();

        $segments = explode('.', $path);

        // Charge la configuration
        $value = self::$config;

        // Lecture des arrays (sous array) contenant les configurations
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default; // Retourne la valeur par défaut (ou null)
            }
            $value = $value[$segment];
        }

        return $value;
    }
}
