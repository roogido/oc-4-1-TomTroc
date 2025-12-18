<?php declare(strict_types=1);
/**
 * Class Book
 *
 * Modèle métier représentant un livre appartenant à un utilisateur.
 * Encapsule les données de la table `books` sans logique de persistance
 * (celle-ci est déléguée au Repository). Permet l'hydration de l'obj.
 *
 * PHP version 8.2.12
 *
 * Date :        12 décembre 2025
 * Maj :         17 décembre 2025
 *
 * @category     Models
 * @package      App\Models
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          books (table MySQL)
 * @todo         Ajouter des règles de validation métier si nécessaire
 */

namespace App\Models;


class Book
{
    public const STATUS_AVAILABLE   = 'available';
    public const STATUS_UNAVAILABLE = 'unavailable';

    private ?int $id = null;
    private int $userId;
    private ?string $ownerPseudo = null;
    private ?string $ownerAvatarPath = null;
    private string $title;
    private string $author;
    private string $description;
    private ?string $imagePath;
    private string $status;


    /**
     * Constructeur.
     *
     * Représente un livre appartenant à un utilisateur.
     * Les champs correspondent strictement à la table `books`.
     *
     * @param int         $userId      Identifiant du propriétaire du livre.
     * @param string      $title       Titre du livre.
     * @param string      $author      Auteur du livre.
     * @param string      $description Description du livre (texte long).
     * @param string      $status      Statut de disponibilité ('available' ou 'unavailable').
     * @param string|null $imagePath   Chemin de l'image (optionnel).
     */
    public function __construct(
        int $userId,
        string $title,
        string $author,
        string $description,
        string $status = self::STATUS_AVAILABLE,
        ?string $imagePath = null
    ) {
        $this->userId      = $userId;
        $this->title       = trim($title);
        $this->author      = trim($author);
        $this->description = trim($description);
        $this->status      = $status;
        $this->imagePath   = $imagePath;
    }

    // ---------
    // Getters
    // ---------

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getOwnerPseudo(): ?string
    {
        return $this->ownerPseudo;
    }    

    public function getOwnerAvatarPath(): string
    {
        if (!empty($this->ownerAvatarPath)) {
            return '/assets/images/avatars/' . ltrim($this->ownerAvatarPath, '/');
        }

        return User::DEFAULT_AVATAR;
    }
    
    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    // ---------
    // Setters
    // ---------

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setOwnerPseudo(string $pseudo): void
    {
        $this->ownerPseudo = $pseudo;
    }    

    public function setOwnerAvatarPath(?string $avatarPath): void
    {
        $this->ownerAvatarPath = $avatarPath;
    }

    public function setTitle(string $title): void
    {
        $this->title = trim($title);
    }

    public function setAuthor(string $author): void
    {
        $this->author = trim($author);
    }

    public function setDescription(string $description): void
    {
        $this->description = trim($description);
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setImagePath(?string $imagePath): void
    {
        $this->imagePath = $imagePath;
    }
}
