<?php declare(strict_types=1);
/**
 * Class AvatarService (final)
 *
 * Service de gestion de l’avatar utilisateur.
 * Encapsule la logique de mise à jour de l’avatar en s’appuyant
 * sur le composant d’upload, tout en garantissant la protection
 * de l’avatar par défaut.
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
 * @see          \App\Core\AvatarUploader
 * @todo         
 */

namespace App\Service\Account;

use App\Core\AvatarUploader;
use App\Models\User;
use RuntimeException;


final class AvatarService
{
    /**
     * Met à jour l’avatar d’un utilisateur.
     *
     * @param User  $user
     * @param array $file Fichier issu de $_FILES['avatar']
     *
     * @return string Nom du nouveau fichier avatar
     *
     * @throws RuntimeException
     */
    public function updateAvatar(User $user, array $file): string
    {
        // Nom du fichier (uniquement)
        $oldAvatar = $user->getRawAvatarPath(); 

        // Sécurité : ne jamais supprimer l’avatar par défaut
        if ($oldAvatar === User::DEFAULT_AVATAR) {
            $oldAvatar = null;
        }

        return AvatarUploader::upload(
            $file,
            $oldAvatar
        );
    }
}
