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
 * Maj :         17 décembre 2025
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
     * Retourne une durée lisible indiquant depuis combien de temps
     * un utilisateur est inscrit sur la plateforme.
     *
     * La durée est calculée à partir de la date d’inscription (created_at)
     * et exprimée en ces termes : aujourd’hui, quelques jours, X mois, X ans.
     *
     * @param int $userId Identifiant de l’utilisateur
     * @return string Durée d’inscription formatée pour l’affichage
     */
    public function getMemberSince(int $userId): string
    {
        // Récupère uniquement la date d’inscription de l’utilisateur
        $sql = "SELECT created_at FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $userId]);

        // fetchColumn() retourne la valeur de la première colonne
        // ou false si aucun résultat
        $createdAt = $stmt->fetchColumn();

        // Si l’utilisateur n’existe pas ou que la date est absente
        if (! $createdAt) {
            return '';
        }

        // Création des objets DateTime pour calculer l’écart
        $created = new \DateTime($createdAt);
        $now     = new \DateTime();

        // diff() retourne un DateInterval contenant années, mois, jours, etc.
        $diff = $created->diff($now);

        // Cas : inscription le jour même
        if ($diff->y === 0 && $diff->m === 0 && $diff->d === 0) {
            return 'aujourd’hui';
        }

        // Cas : moins d’un mois (quelques jours)
        if ($diff->y === 0 && $diff->m === 0) {
            return 'quelques jours';
        }

        // Cas : moins d’un an (affichage en mois)
        if ($diff->y === 0) {
            return $diff->m === 1
                ? '1 mois'
                : $diff->m . ' mois';
        }

        // Cas : un an ou plus (affichage en années)
        return $diff->y === 1
            ? '1 an'
            : $diff->y . ' ans';
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
            INSERT INTO users (pseudo, email, password_hash, avatar_path)
            VALUES (:pseudo, :email, :password_hash, :avatar_path)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'pseudo'        => $user->getPseudo(),
            'email'         => $user->getEmail(),
            'password_hash' => $user->getPasswordHash(),
            'avatar_path'   => $user->getRawAvatarPath(),
        ]);
    }

    /**
     * Met à jour les informations du profil utilisateur.
     *
     * Met à jour l’email et le pseudo de l’utilisateur.
     * Le mot de passe est mis à jour uniquement s’il est fourni.
     *
     * @param int         $userId        Identifiant de l’utilisateur
     * @param string      $email         Nouvelle adresse email
     * @param string      $pseudo        Nouveau pseudo
     * @param string|null $passwordHash  Nouveau hash de mot de passe ou null
     *
     * @return bool True si la mise à jour a réussi, false sinon
     */
    public function updateProfile(
        int $userId,
        string $email,
        string $pseudo,
        ?string $passwordHash
    ): bool {
        $fields = [
            'email'  => $email,
            'pseudo' => $pseudo,
        ];

        $sql = "UPDATE users SET email = :email, pseudo = :pseudo" ; 

        if ($passwordHash !== null) {
            $sql .= ", password_hash = :password_hash";
            $fields['password_hash'] = $passwordHash;
        }

        $sql .= " WHERE id = :id";
        $fields['id'] = $userId;

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($fields);
    }

    /**
     * Met à jour l’avatar d’un utilisatueur.
     *
     * @param int    $userId      Identifiant de l’utilisateur.
     * @param string $avatarPath  Nom du fichier avatar (valeur brute BDD).
     *
     * @return bool  true si la mise à jour a réussi, false sinon.
     */
    public function updateAvatar(int $userId, string $avatarPath): bool
    {
        $sql = "
            UPDATE users
            SET avatar_path = :avatar_path
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'avatar_path' => $avatarPath,
            'id'          => $userId,
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
            $data['password_hash'],
            $data['avatar_path'] ?? null
        );

        $user->setId((int) $data['id']);

        return $user;
    }
}
