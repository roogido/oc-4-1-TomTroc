<?php declare(strict_types=1);
/**
 * Class BookAccessChecker (final)
 *
 * Service de contrôle des droits d’accès liés à l’entité Book.
 * Vérifie que l’utilisateur courant est autorisé à accéder ou modifier une ressource.
 * Centralise la logique d’autorisation afin d’éviter toute duplication dans les contrôleurs.
 *
 * PHP version 8.2.12
 *
 * Date :        2 janvier 2026
 * Maj :         
 *
 * @category     Service
 * @package      App\Service\Book
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          \App\Models\Book
 * @todo         Étendre les contrôles à d’autres règles d’autorisation si nécessaire.
 */

namespace App\Service\Book;

use App\Core\Session;
use App\Models\Book;
use App\Core\HttpForbiddenException;


final class BookAccessChecker
{
    /**
     * Vérifie que l’utilisateur courant est propriétaire du livre.
     *
     * @param Book $book Livre à vérifier.
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas autorisé.
     */    
    public function checkOwnership(Book $book): void
    {
        if ($book->getUserId() !== Session::getUserId()) {
            throw new HttpForbiddenException('Accès interdit.');
        }
    }
}
