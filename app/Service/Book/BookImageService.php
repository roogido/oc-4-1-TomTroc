<?php declare(strict_types=1);
/**
 * Class BookImageService (final)
 *
 * Service de gestion des images associées à l’entité Book.
 * Gère l'upload d’une nouvelle image et la suppression de l’ancienne
 * lorsque cela est nécessaire, tout en préservant l’image par défaut.
 *
 * PHP version 8.2.12
 *
 * Date :        2 janvier 2026
 * Maj :         3 janvier 2026
 *
 * @category     Service
 * @package      App\Service\Book
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          \App\Models\Book
 * @todo         
 */

namespace App\Service\Book;

use App\Core\Config;
use App\Models\Book;
use App\Service\Upload\FileUploader;


final class BookImageService
{
    /**
     * Met à jour l’image d’un livre si un fichier est fourni.
     * Supprime l’ancienne image si nécessaire.
     *
     * @param Book  $book Livre concerné.
     * @param array $file Données du fichier uploadé ($_FILES).
     * @return void
     */
    public function update(Book $book, array $file): void
    {
        if (empty($file['name'])) {
            return;
        }

        $oldImagePath = $book->getImagePath();

        $uploadDir = Config::get('app.paths.book_uploads');

        // Upload et contrôle du fichier transmis (taille, type MIME, ...)
        $newImage = FileUploader::upload(
            $file,
            $uploadDir,
            'book_'
        );

        // Chemin relatif stocké en BDD
        $book->setImagePath(Book::IMAGE_UPLOAD_DIR . $newImage);

        // Suppression du fichier si requise
        $this->deleteIfNeeded($oldImagePath);
    }

    /**
     * Supprime l’image associée à un livre (si nécessaire).
     *
     * @param Book  $book Livre concerné.
     * @return void
     */
    public function delete(Book $book): void
    {
        $this->deleteIfNeeded($book->getImagePath());
    }    

    /**
     * Supprime une image existante si elle n’est pas l’image par défaut.
     *
     * @param string|null $imagePath Chemin relatif de l’image à supprimer.
     * @return void
     */
    private function deleteIfNeeded(?string $imagePath): void
    {
        if ($imagePath === null || $imagePath === '') {
            return;
        }

        // Ne jamais supprimer l’image par défaut
        if ($imagePath === Book::DEFAULT_IMAGE) {
            return;
        }

        $publicDir = Config::get('app.paths.public');
        $fullPath  = $publicDir . '/' . ltrim($imagePath, '/');

        // suppression du fichier
        if (is_file($fullPath)) {
            unlink($fullPath);
        }
    }
}

