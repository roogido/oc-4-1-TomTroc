<?php declare(strict_types=1);
/**
 * Class BookFormErrorHandler (final)
 *
 * Service de gestion des erreurs de formulaire liées à l’entité Book.
 * Centralise la mise en session des messages d’erreur et des données précédemment saisies
 * afin de simplifier les contrôleurs et uniformiser le comportement des formulaires.
 *
 * PHP version 8.2.12
 *
 * Date :        3 janvier 2026
 * Maj :         
 *
 * @category     Service
 * @package      App\Service\Book
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          \App\Core\Session
 * @todo         Étendre la gestion à d’autres types de formulaires si nécessaire.
 */

namespace App\Service\Book;

use App\Core\Session;


final class BookFormErrorHandler
{
    /**
     * Stocke les erreurs de validation et les données du formulaire en session.
     *
     * @param array $errors  Erreurs de validation indexées par champ.
     * @param array $oldData Données précédemment saisies.
     * @return void
     */    
    public function handle(array $errors, array $oldData): void
    {
        Session::addFlash(
            'error',
            'Données invalides. Veuillez corriger les champs en erreur.'
        );

        Session::addFlash('error', $errors);
        Session::addFlash('old', $oldData);
    }
}


