<?php declare(strict_types=1);
/**
 * Class HttpNotFoundException
 *
 * Exception dédiée aux erreurs 404. Permet de signaler
 * proprement qu’une ressource ou une page est introuvable.
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


class HttpNotFoundException extends Exception
{
    protected $message = 'Page non trouvée';
}
