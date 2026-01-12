<?php declare(strict_types=1);
/**
 * Class CsrfException
 *
 * Exception levée lorsqu’une vérification CSRF échoue.
 * Elle permet d’identifier clairement les erreurs liées
 * à la protection contre les attaques de type Cross-Site Request Forgery.
 *
 * Utilisée pour interrompre le traitement d’une requête
 * lorsque le token CSRF est manquant, invalide ou expiré.
 *
 * PHP version 8.2.12
 *
 * Date : 10 janvier 2026
 * Maj  : -
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        RuntimeException
 */



namespace App\Core\Exception;

use RuntimeException;

class CsrfException extends RuntimeException {}
