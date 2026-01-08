<?php declare(strict_types=1);
/**
 * Class AccountProfileService (final)
 *
 * Service de gestion des mises à jour du profil utilisateur.
 * Encapsule la logique de persistance des informations du compte
 * (email, pseudo, mot de passe) afin de découpler les contrôleurs
 * de la couche d’accès aux données.
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
 * @todo         Étendre la gestion à d’autres informations de profil si nécessaire.
 */

namespace App\Service\Account;

use App\Models\User;
use App\Repositories\UserRepository;


final class AccountProfileService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Met à jour les informations du profil utilisateur.
     *
     * @param User        $user
     * @param string      $email
     * @param string      $pseudo
     * @param string|null $passwordHash
     *
     * @return void
     */
    public function updateProfile(
        User $user,
        string $email,
        string $pseudo,
        ?string $passwordHash
    ): void {
        $this->users->updateProfile(
            $user->getId(),
            $email,
            $pseudo,
            $passwordHash
        );
    }
}
