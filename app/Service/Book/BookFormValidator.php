<?php declare(strict_types=1);
/**
 * Class BookFormValidator (final)
 *
 * Service de validation des données issues des formulaires liés à l’entité Book.
 * Centralise les règles de validation métier (champs obligatoires, statut, etc.)
 * afin d’éviter la duplication de logique dans les contrôleurs.
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
 * @todo         Ajouter des règles de validation avancées (longueur, formats).
 */


namespace App\Service\Book;

use App\Models\Book;


final class BookFormValidator
{
    /**
     * Valide les données d’un formulaire de livre.
     *
     * @param array $data Données du formulaire (title, author, description, status).
     * @param bool  $checkStatus Indique si la validation du statut doit être effectuée (pour edit()).
     * @return array Tableau associatif des erreurs indexées par nom de champ.
     */
    public function validate(array $data, bool $checkStatus = false): array
    {
        $errors = [];

        if (empty(trim($data['title'] ?? ''))) {
            $errors['title'] = 'Le titre est obligatoire.';
        }

        if (empty(trim($data['author'] ?? ''))) {
            $errors['author'] = 'L’auteur est obligatoire.';
        }

        if (empty(trim($data['description'] ?? ''))) {
            $errors['description'] = 'La description est obligatoire.';
        }

        // Validation optionnelle du statut métier du livre
        if ($checkStatus) {
            if (!in_array(
                $data['status'] ?? '',
                [Book::STATUS_AVAILABLE, Book::STATUS_UNAVAILABLE],
                true
            )) {
                $errors['status'] = 'Statut invalide.';
            }
        }

        return $errors;
    }
}
