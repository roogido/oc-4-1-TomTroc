<?php declare(strict_types=1);
/**
 * Class FileUploader
 *
 * Service utilitaire générique dédié à l’upload sécurisé de fichiers image.
 *
 * Centralise les règles communes :
 *  - validation (taille, extension, type MIME)
 *  - génération de noms uniques
 *  - enregistrement sur le disque
 *  - suppression éventuelle de l’ancien fichier
 *
 * Pensé pour être réutilisé (livres, avatars, images diverses)
 * sans dépendance au HTTP, aux contrôleurs ou à la base de données.
 *
 * PHP version 8.2.12
 *
 * Date :        19 décembre 2025
 * Maj :         20 décembre 2025
 *
 * @category   Core
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        App\Controllers\BookController
 * @see        App\Controllers\AccountController
 * @todo       
 */

namespace App\Core;

class FileUploader
{
    public const MAX_SIZE = 2 * 1024 * 1024; // 2 Mo
    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Upload  un fichier image de manière sécurisée.
     *
     * Valide la présence du fichier, sa taille, son extension et son type MIME,
     * génère un nom unique, enregistre le fichier et supprime l’ancien si fourni.
     *
     * @param array       $file      Données issues de $_FILES
     * @param string      $targetDir Répertoire de destination (chemin absolu)
     * @param string      $prefix    Préfixe du nom de fichier généré
     * @param string|null $oldFile   Ancien fichier à supprimer (optionnel)
     *
     * @return string Nom du fichier enregistré
     *
     * @throws \RuntimeException En cas d’erreur d’upload ou de validation
     */
    public static function upload(
        array $file,
        string $targetDir,
        string $prefix,
        ?string $oldFile = null
    ): string {
        // Aucun fichier sélectionné
        if (empty($file['name'])) {
            throw new \RuntimeException('Aucun fichier envoyé.');
        }

        // Erreur PHP lors de l’upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Erreur lors de l’upload du fichier.');
        }

        // Vérifie la taille maximale autorisée
        if ($file['size'] > self::MAX_SIZE) {
            throw new \RuntimeException('Le fichier ne doit pas dépasser 2 Mo.');
        }

        // Extraction et normalisation de l’extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Vérifie l’extension autorisée
        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw new \RuntimeException('Format de fichier non autorisé.');
        }

        // Liste des types MIME images acceptés
        $allowedMimeTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        // Détection du type MIME réel du fichier
        $mimeType = mime_content_type($file['tmp_name']);

        // Vérifie que le contenu correspond bien à une image
        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            throw new \RuntimeException('Type MIME du fichier non autorisé.');
        }

        // Génération d’un nom unique sécurisé
        $fileName = uniqid($prefix, true) . '.' . $extension;

        // Construction du chemin de destination
        $destination = rtrim($targetDir, '/') . '/' . $fileName;

        // Déplacement du fichier depuis le dossier temporaire
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('Impossible d’enregistrer le fichier.');
        }

        // Suppression de l’ancien fichier si fourni
        if ($oldFile !== null) {
            $oldPath = rtrim($targetDir, '/') . '/' . $oldFile;
            if (is_file($oldPath)) {
                unlink($oldPath);
            }
        }

        // Retourne le nom du fichier enregistré
        return $fileName;

    }
}
