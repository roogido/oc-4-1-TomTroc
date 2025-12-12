<?php declare(strict_types=1);
/**
 * Class Session
 *
 * Gestion centralisée et sécurisée de la session utilisateur :
 * démarrage, lecture/écriture, flash messages et contrôle d'authentification.
 *
 * PHP version 8.2.12
 *
 * Date :        11 décembre 2025
 * Maj :         -
 *
 * @category     Core
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          Session::start(), Session::addFlash(), Session::isLogged()
 * @todo         ...
 */

namespace App\Core;


class Session
{
    private static bool $started = false;


    /**
     * Demarre la session de maniere securisee si ce n'est pas deja fait.
     * @return void
     */
    public static function start(): void
    {
        if (self::$started === true) {
            return;
        }

        // Démarre une session, si la session n'est pas active
        // Note : PHP_SESSION_ACTIVE est un constante interne php qui permet de savoir si une session est déjà active
        if (session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            return;
        }

        // Parametres de cookie securises
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        // Récupère les paramètres du cookie (valeurs par défaut, définies dans la configuration PHP) sous forme d'array 
        $cookieParams = session_get_cookie_params();

        // Configure les paramètres du cookie de session qui sera transmis
        session_set_cookie_params([
            'lifetime' => 0,                              // Durée du cookie : 0 = expire à la fermeture du navigateur (ex : cookie supprimé en quittant Chrome)
            'path'     => $cookieParams['path'] ?? '/',   // Chemin où le cookie est valide (ex : "/" = tout le site)
            'domain'   => $cookieParams['domain'] ?? '',  // Domaine associé (ex : "ashva.fr" ou vide en local)
            'secure'   => $secure,                        // Envoi uniquement via HTTPS (ex : true si https://ashva.fr)
            'httponly' => true,                           // Empêche l'accès depuis JS (ex : protège contre XSS - vole de session)
            'samesite' => 'Lax',                          // Limite l'envoi du cookie aux navigations sûres (ex : protège contre CSRF)
        ]);

        // Applique les paramètres définis avant (+ démaarre la la session + transmission cookie au navigateur)
        session_start();
        self::$started = true ;

        // Protection minimale contre la fixation de session
        if (! isset($_SESSION['_initialized'])) {
            $_SESSION['_initialized'] = true;
            self::regenerate();
        }
    }

    /**
     * Sécurité : anti-hijacking
     * Regeneration de l'identifiant de session et invalide l'ancien.
     * @return void
     */
    public static function regenerate(): void
    {
        if (self::$started === false) {
            self::start();
        }

        session_regenerate_id(true);
    }

    /**
     * Stocke une valeur en session.
     *
     * @param string $key   Nom de la clé sous laquelle la valeur sera stockée.
     * @param mixed  $value Valeur à stocker en session (types scalaires ou complexes).
     * 
     * @return void
     */
    public static function set(string $key, mixed $value): void
    {
        if (self::$started === false) {
            self::start();
        }

        $_SESSION[$key] = $value;
    }

    /**
     * Recupere une valeur depuis la session.
     *
     * @param string $key        Nom de la clé à récupérer depuis la session.
     * @param mixed  $default    Valeur retournée si la clé n'existe pas dans la session.
     *
     * @return mixed             La valeur stockée en session, ou $default si absente.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        if (self::$started === false) {
            self::start();
        }

        return $_SESSION[$key] ?? $default;
    }

    /**
     * Vérifie si une clé existe dans la session.
     *
     * @param string $key   Nom de la clé à vérifier.
     *
     * @return bool         true si la clé existe, false sinon.
     */
    public static function has(string $key): bool
    {
        if (self::$started === false) {
            self::start();
        }

        return array_key_exists($key, $_SESSION);
    }

    /**
     * Supprime une cle en session.
     * @param string $key        Nom de la clé à supprimer dans la session.
     * @return void
     */
    public static function remove(string $key): void
    {
        if (self::$started === false) {
            self::start();
        }

        unset($_SESSION[$key]);
    }

    /**
     * Detruit completement la session et cookie de session (nav).
     * @return void
     */
    public static function destroy(): void
    {
        // On démmarre la session (on ne peut supprimer une session qui n'existe pas)
        if (self::$started === false) {
            self::start();
        }

        // Clear de toutes les variables de session propre à l'utilisteur
        $_SESSION = [];

        // Si PHP utilise les cookies pour gérer la session (php.ini), alors supprime 
        // aussi ce dernier du navigateur.
        // Retourne 1 si PHP utilise un cookie pour stocker l’ID de session, sinon 0     
        if (ini_get('session.use_cookies')) {
            // Récupère les paramètres du cookie actuel sous forme d'array afin de le supprimer
            $params = session_get_cookie_params();

            // Supprime le cookie côté navigateur
            setcookie(
                session_name(),                                     // Ex. : PHPSESSID
                '',
                [
                    'expires'  => time() - 3600,                    // Valeur dans le passé = suppression du cookie
                    'path'     => $params['path'] ?? '/',
                    'domain'   => $params['domain'] ?? '',
                    'secure'   => $params['secure'] ?? false,
                    'httponly' => $params['httponly'] ?? true,
                    'samesite' => $params['samesite'] ?? 'Lax',
                ]
            );
        }

        // Détruit la session côté serveur
        session_destroy();
        self::$started = false;
    }

    /**
     * Indique si un utilisateur est connecte (convention: user_id en session).
     *
     * Vérifie simplement si la clé "user_id" est présente dans la session.
     * Cette clé est généralemnet définie lors de l'authentification réussie.
     *
     * @return bool   true si l'utilisateur est connecté, false sinon.
     */
    public static function isLogged(): bool
    {
        return self::has('user_id');
    }

    /**
     * Retourne l'identifiant de l'utilisateur connecte (user_id en session).
     *
     * Si aucun utilisateur n'est connecté, la méthode renvoie null.
     * Sinon, l'identifiant est retourné sous forme d'entier pour assuuré
     * une cohérence de type, même si la valeur en session est stockée en chaîne.
     *
     * @return int|null   ID utilisateur ou null si non connecté.
     */
    public static function getUserId(): ?int
    {
        $id = self::get('user_id');

        if ($id === null) {
            return null;
        }

        return (int) $id ;
    }

    /**
     * Ajoute un message flash pour un type donne (success, error, info...).
     *
     * Les messages flash sont stockes en session sous la cle "_flashes" et sont
     * destines a etre affiches une seule fois (ex. : apres une redirection).
     *
     * @param string       $type     Type de message (ex. "success", "error", "info", "old").
     * @param string|array $message  Contenu du message flash (texte ou tableau).
     *
     * @return void
     */
    public static function addFlash(string $type, string|array $message): void
    {
        if (self::$started === false) {
            self::start();
        }

        if (! isset($_SESSION['_flashes'][$type])) {
            $_SESSION['_flashes'][$type] = [];
        }

        $_SESSION['_flashes'][$type][] = $message;
    }

    /**
     * Recupere et efface les messages flash d'un type donne.
     * puis les supprime immédiatement de la session afin qu'ils 
     * ne soient affichés qu'une seule fois.
     *
     * @param string $type
     *
     * @return array Liste des messages flashes pour ce type.
     */
    public static function getFlashes(string $type): array
    {
        if (self::$started === false) {
            self::start();
        }

        $messages = $_SESSION['_flashes'][$type] ?? [];
        unset($_SESSION['_flashes'][$type]);

        return $messages;
    }
}
