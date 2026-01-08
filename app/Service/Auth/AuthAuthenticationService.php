<?php declare(strict_types=1);
/**
 * Class AuthAuthenticationService (final)
 *
 * Service d’authentification des utilisateurs.
 * Centralise la logique de vérification des identifiants
 * et retourne l’utilisateur authentifié ou lève une exception
 * en cas d’échec.
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
 * @see          \App\Repositories\UserRepository
 * @todo         Étendre la gestion des échecs (tentatives, délai, journalisation).
 */

namespace App\Service\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use RuntimeException;


final class AuthAuthenticationService
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Authentifie un utilisateur à partir de ses identifiants.
     *
     * Vérifie la correspondance email / mot de passe et retourne
     * l’utilisateur associé en cas de succès.
     *
     * @param string $email    Adresse email fournie.
     * @param string $password Mot de passe fourni.
     * @return User Utilisateur authentifié.
     *
     * @throws RuntimeException Si les identifiants sont invalides.
     */
    public function authenticate(string $email, string $password): User
    {
        $user = $this->users->verifyCredentials($email, $password);

        if (! $user) {
            throw new RuntimeException('Identifiants incorrects.');
        }

        return $user;
    }
}