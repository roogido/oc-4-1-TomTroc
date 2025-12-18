<?php declare(strict_types=1);
/**
 * Class User
 *
 * Modèle représentant un utilisateur de l'application : identité, contact,
 * informations d'authentification et métadonnées de création.
 *
 * PHP version 8.2.12
 *
 * Date :        11 décembre 2025
 * Maj :         17 décembre 2025
 *
 * @category     Models
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          UserRepository, AuthController
 * @todo         Ajouter validateEmail() ou méthode métier si nécessaire
 */

namespace App\Models;


class User
{
    public const DEFAULT_AVATAR = '/assets/images/avatars/avatar-default.webp';

    private ?int $id = null;
    private string $pseudo;
    private string $email;
    private ?string $avatarPath = null;
    private string $passwordHash;


    /**
     * Constructeur.
     *
     * Initialise les propriétés essentielles d'un utilisateur. Le pseudo et l'email
     * sont normalisés (trim, lowercase). Le mot de passe doit être fourni déjà
     * hashé avant l'appel du constructeur.
     *
     * @param string $pseudo        Pseudonyme de l'utilisateur.
     * @param string $email         Adresse email normalisée (non hashée).
     * @param string $passwordHash  Mot de passe déjà hashé (password_hash).
     *
     * @return void
     */ 
    public function __construct(
        string $pseudo,
        string $email,
        string $passwordHash,
        ?string $avatarPath = null
    ) {
        $this->pseudo       = trim($pseudo);
        $this->email        = strtolower(trim($email));
        $this->passwordHash = $passwordHash;
        $this->avatarPath   = $avatarPath;
    }

    // ----- Getters / Setters -----
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setAvatarPath(?string $avatarPath): void
    {
        $this->avatarPath = $avatarPath;
    }

    /**
     * Retourne le chemin brut de l’avatar tel qu’il est stocké en base de données.
     *
     * Cette valeur correspond uniquement au nom du fichier (ou null si aucun avatar
     * personnalisé n’est défini). Elle est destinée à un usage interne
     * (persistance, repository) et ne doit pas être utilisée directement
     * pour l’affichage.
     *
     * @return string|null Nom du fichier avatar ou null.
     */  
    public function getRawAvatarPath(): ?string
    {
        return $this->avatarPath;
    }

    /**
     * Retourne le chemin public de l’avatar prêt pour l’affichage.
     *
     * Si l’utilisateur possède un avatar personnalisé, l’URL publique complète
     * est générée. Sinon, le chemin vers l’avatar par défaut est retourné.
     * Cette méthode est destinée exclusivement aux vues et à l’affichage HTML.
     *
     * @return string URL publique de l’avatar.
     */
    public function getAvatarPath(): string
    {
        if (!empty($this->avatarPath)) {
            return '/assets/images/avatars/' . ltrim($this->avatarPath, '/');
        }

        return self::DEFAULT_AVATAR;
    }  

    // Retourne le mot de passe déjà hashé (jamais le mot de passe en clair)
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}
