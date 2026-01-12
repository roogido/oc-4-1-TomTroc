<?php declare(strict_types=1);
/**
 * Class AuthLoginValidator (final)
 *
 * Service de validation des données du formulaire de connexion.
 * Centralise le nettoyage et la vérification des champs requis
 * afin de garantir des données cohérentes avant l’authentification.
 *
 * PHP version 8.2.12
 *
 * Date :        3 janvier 2026
 * Maj :         10 janvier 2026
 *
 * @category     Service
 * @package      App\Service\Auth
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @todo         Ajouter des règles de validation avancées si nécessaire.
 */

namespace App\Service\Auth;


final class AuthLoginValidator
{
    /**
     * Valide les données du formulaire de connexion.
     *
     * Nettoie les champs, vérifie leur présence et le format de l’email,
     * puis retourne les erreurs éventuelles ainsi que les données validées.
     *
     * @param array $data Données brutes issues du formulaire.
     * @return array{
     *     errors: array<string,string>,
     *     email: string,
     *     password: string
     * }
     */
    public function validate(array $data): array
    {
        $errors = [];

        // Nettoyage des données
        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        // Validation email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse email invalide.';
        }

        // Validation mot de passe
        if ($password === '') {
            $errors['password'] = 'Le mot de passe est requis.';
        } elseif (strlen($password) < 6) {
            $errors['password'] = 'Minimum 6 caractères requis.';
        }

        return [
            'errors'   => $errors,
            'email'    => $email,
            'password' => $password,
        ];
    }
}
