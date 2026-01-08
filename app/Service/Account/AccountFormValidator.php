<?php declare(strict_types=1);
/**
 * Class AccountFormValidator (final)
 *
 * Service de validation des données du formulaire de gestion du compte utilisateur.
 * Centralise les règles de validation métier (email, pseudo, mot de passe)
 * et les contrôles d’unicité afin de garantir l’intégrité des données utilisateur.
 *
 * PHP version 8.2.12
 *
 * Date :        3 janvier 2026
 * Maj :         
 *
 * @category     Service
 * @package      App\Service\Account
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          \App\Models\User
 * @todo         Ajouter des règles de validation avancées (complexité du mot de passe).
 */

namespace App\Service\Account;

use App\Models\User;
use App\Repositories\UserRepository;


final class AccountFormValidator
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Valide les données du formulaire de mise à jour du compte.
     *
     * @param array $data Données brutes issues du POST
     * @param User  $user Utilisateur actuellement connecté
     *
     * @return array{
     *     errors: array<string,string>,
     *     passwordHash: string|null
     * }
     */
    public function validate(array $data, User $user): array
    {
        $errors = [];
        $passwordHash = null;

        $email    = trim($data['email'] ?? '');
        $pseudo   = trim($data['pseudo'] ?? '');
        $password = trim($data['password'] ?? '');

        // Email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse email invalide.';
        } else {
            $existing = $this->users->findByEmail($email);
            if ($existing && $existing->getId() !== $user->getId()) {
                $errors['email'] = 'Cet email est déjà utilisé.';
            }
        }

        // Pseudo
        if ($pseudo === '') {
            $errors['pseudo'] = 'Le pseudo est obligatoire.';
        } else {
            $existing = $this->users->findByPseudo($pseudo);
            if ($existing && $existing->getId() !== $user->getId()) {
                $errors['pseudo'] = 'Ce pseudo est déjà pris.';
            }
        }

        // Mot de passe (optionnel)
        if ($password !== '') {
            if (mb_strlen($password) < 6) {
                $errors['password'] = 'Le mot de passe doit contenir au moins 6 caractères.';
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        return [
            'errors'       => $errors,
            'passwordHash' => $passwordHash,
        ];
    }
}