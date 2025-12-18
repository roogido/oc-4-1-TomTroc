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
 * Maj :         
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
    private const MAX_SIZE = 2 * 1024 * 1024; // 2 Mo
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Traite l’upload d’un avatar utilisateur.
     *
     * @param array       $file        Tableau $_FILES['avatar']
     * @param string|null $oldAvatar   Nom du fichier avatar existant (BDD) ou null
     *
     * @return string|null Nom du nouveau fichier avatar ou null si aucun upload
     *
     * @throws \RuntimeException En cas d’erreur de validation ou d’upload
     */
    public static function upload(array $file, ?string $oldAvatar = null): ?string
    {
        // Aucun fichier envoyé
        if (empty($file['name'])) {
            return null;
        }

        // Erreur PHP lors de l’upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException("Erreur lors de l’upload de l’avatar.");
        }

        // Contrôle de la taille maximale
        if ($file['size'] > self::MAX_SIZE) {
            throw new \RuntimeException("L’image ne doit pas dépasser 2 Mo.");
        }

        // Vérification de l’extension du fichier
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \RuntimeException("Format d’image non autorisé.");
        }

        // Génération d’un nom de fichier unique
        $fileName = uniqid('avatar_', true) . '.' . $extension;

        // Chemin de destination final sur le serveur
        $destination = __DIR__ . '/../../public/assets/images/avatars/' . $fileName;

        // Déplacement sécurisé du fichier uploadé
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException("Impossible d’enregistrer l’avatar.");
        }

        // Suppression de l’ancien avatar (sauf avatar par défaut)
        if ($oldAvatar !== null) {
            $default = basename(User::DEFAULT_AVATAR);
            if ($oldAvatar !== $default) {
                $oldPath = __DIR__ . '/../../public/assets/images/avatars/' . $oldAvatar;
                if (is_file($oldPath)) {
                    unlink($oldPath);
                }
            }
        }

        // Retourne le nom du fichier à stocker en base
        return $fileName;
    }
}
