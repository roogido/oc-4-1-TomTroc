<?php declare(strict_types=1);
/**
 * Class UserRepository
 *
 * Repository responsable de l'accès aux données utilisateurs :
 * récupération, création, et vérification des identifiants en base.
 * Toutes les opérations SQL liées au modèle User sont centralisées ici.
 *
 * PHP version 8.2.12
 *
 * Date :        11 décembre 2025
 * Maj :         -
 *
 * @category     Repository
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          App\Models\User, App\Core\Database
 * @todo         Ajouter update() et delete() si nécessaire
 */


namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;


class UserRepository
{
    private PDO $pdo;

    
    /**
     * Constructeur.
     * 
     * Initialise le repository avec une connexion PDO.
     * La connexion provient du gestionnaire Database (singleton).
     */    
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Retourne un utilisateur par son ID.
     *
     * @param int $id   Identifiant utilisateur.
     *
     * @return User|null   Objet User si trouvé, null sinon.
     */
    public function findById(int $id): ?User
    {
        $sql = "SELECT * FROM users WHERE id = :id LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch();

        return $data ? $this->hydrateUser($data) : null;
    }

    /**
     * Retourne un utilisateur par son email.
     *
     * @param string $email   Adresse email recherchée.
     *
     * @return User|null      Objet User si trouvé, null sinon.
     */
    public function findByEmail(string $email): ?User
    {
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);

        $data = $stmt->fetch();

        return $data ? $this->hydrateUser($data) : null;
    }

    /**
     * Recherche un utilisateur par son pseudonyme.
     *
     * @param string $pseudo  Pseudonyme recherché.
     *
     * @return User|null      Retourne l'objet User si trouvé, sinon null.
     */    
    public function findByPseudo(string $pseudo): ?User
    {
        $sql = "SELECT * FROM users WHERE pseudo = :pseudo LIMIT 1";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['pseudo' => $pseudo]);

        $data = $stmt->fetch();

        return $data ? $this->hydrateUser($data) : null;
    }

    /**
     * Crée un nouvel utilisateur en base.
     *
     * @param User $user   Instance User à persister (mot de passe déjà hashé).
     *
     * @return bool        true si l'insertion a réussi, false sinon.
     */
    public function create(User $user): bool
    {
        $sql = "
            INSERT INTO users (pseudo, email, password_hash)
            VALUES (:pseudo, :email, :password_hash)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'pseudo'        => $user->getPseudo(),
            'email'         => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
        ]);
    }

    /**
     * Vérifie les identifiants d'un utilisateur.
     *
     * @param string $email     Email saisi.
     * @param string $password  Mot de passe en clair saisi.
     *
     * @return User|null        L'utilisateur authentifié ou null si échec.
     */
    public function verifyCredentials(string $email, string $password): ?User
    {
        $user = $this->findByEmail($email);

        if (! $user) {
            return null;
        }

        if (! password_verify($password, $user->getPasswordHash())) {
            return null;
        }

        return $user;
    }

    /**
     * Hydrate un objet User à partir d'un tableau issu de PDO.
     *
     * @param array $data  Données de la table 'users' (fetch associatif).
     *
     * @return User        Instance User hydratée.
     */
    private function hydrateUser(array $data): User
    {
        $user = new User(
            $data['pseudo'],
            $data['email'],
            $data['password_hash']
        );

        $user->setId((int) $data['id']);

        return $user;
    }
}
