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
 * Maj :         3 janvier 2026
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
use App\Service\Upload\FileUploader;


class AvatarUploader
{
    /**
     * Gère l’upload d’un avatar utilisateur et supprime l’ancien si nécessaire.
     *
     * @param array       $file      Données du fichier uploadé ($_FILES).
     * @param string|null $oldAvatar Chemin de l’avatar précédent.
     * @return string|null Chemin du nouvel avatar ou null si aucun fichier n’est fourni.
     */
    public static function upload(array $file, ?string $oldAvatar = null): ?string
    {
        if (empty($file['name'])) {
            return null;
        }

        $default = basename(User::DEFAULT_AVATAR);
        $oldFile = ($oldAvatar !== null && $oldAvatar !== $default)
            ? $oldAvatar
            : null;

        $uploadDir = Config::get('app.paths.avatar_uploads');

        return FileUploader::upload(
            $file,
            $uploadDir,
            'avatar_',
            $oldFile
        );
    }    

}

