<?php declare(strict_types=1);
/**
 * Class HttpForbiddenException
 *
 * Exception dédiée aux erreurs 403. Permet de signaler
 * qu’une ressource ou une page est non autorisée.
 *
 * PHP version 8.2.12
 *
 * Date :      10 décembre 2025
 * Maj  :      -
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        Exception
 * @todo       Étendre avec un code HTTP explicite si nécessaire
 */

namespace App\Core;

use Exception;


class HttpForbiddenException extends Exception
{
    protected $message = 'Accès interdit';
}
