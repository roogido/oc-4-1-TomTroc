<?php declare(strict_types=1);
/**
 * Class AuthRegistrationService (final)
 *
 * Service d’enregistrement des nouveaux utilisateurs.
 * Encapsule la création du compte utilisateur, le hachage du mot de passe
 * et la persistance en base de données.
 *
 * PHP version 8.2.12
 *
 * Date :        3 janvier 2026
 * Maj :         
 *
 * @category     Service
 * @package      App\Service\Auth
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          \App\Models\User
 * @todo         Ajouter la gestion des erreurs et des événements post-inscription.
 */

namespace App\Service\Auth;

use App\Models\User;
use App\Repositories\UserRepository;


final class AuthRegistrationService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Enregistre un nouvel utilisateur.
     *
     * Crée l’entité User, applique le hachage du mot de passe
     * et persiste l’utilisateur en base de données.
     *
     * @param string      $pseudo   Pseudonyme de l’utilisateur.
     * @param string      $email    Adresse email de l’utilisateur.
     * @param string      $password Mot de passe en clair.
     * @param string|null $avatar   Chemin de l’avatar (optionnel).
     *
     * @return void
     */    
    public function register(
        string $pseudo,
        string $email,
        string $password,
        ?string $avatar = null
    ): void {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(
            $pseudo,
            $email,
            $passwordHash,
            $avatar
        );

        $this->users->create($user);
    }
}
