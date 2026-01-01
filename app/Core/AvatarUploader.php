<?php declare(strict_types=1);
/**
 * Class AvatarUploader
 *
 * Service utilitaire chargé de la gestion sécurisée des avatars utilisateurs.
 *
 * Responsabilités :
 *  - Validation des fichiers uploadés (taille, extension)
 *  - Génération de noms uniques
 *  - Stockage dans le dossier public dédié
 *  - Suppression de l’ancien avatar si nécessaire
 *
 * PHP version 8.2.12
 *
 * Date :        17 décembre 2025
 * Maj :         19 décembre 2025
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        App\Models\User
 * @todo       Ajouter une validation MIME réelle (finfo)
 */

namespace App\Core;

use App\Models\User;

class AvatarUploader
{
    public static function upload(array $file, ?string $oldAvatar = null): ?string
    {
        if (empty($file['name'])) {
            return null;
        }

        $default = basename(User::DEFAULT_AVATAR);
        $oldFile = ($oldAvatar !== null && $oldAvatar !== $default)
            ? $oldAvatar
            : null;

        return FileUploader::upload(
            $file,
            __DIR__ . '/../../public' . User::AVATAR_UPLOAD_DIR,
            'avatar_',
            $oldFile
        );
    }
}

