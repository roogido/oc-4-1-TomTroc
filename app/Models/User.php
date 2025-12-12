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
 * Maj :         -
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
    private ?int $id = null;
    private string $pseudo;
    private string $email;
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
    public function __construct(string $pseudo, string $email, string $passwordHash)
    {
        $this->pseudo       = trim($pseudo);
        $this->email        = strtolower(trim($email));
        $this->passwordHash = $passwordHash;
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

    // Retourne le mot de passe déjà hashé (jamais le mot de passe en clair)
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}
