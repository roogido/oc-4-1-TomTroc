<?php declare(strict_types=1);
/**
 * Class BookRepository
 *
 * Repository chargé de la persistance des entités Book.
 * Centralise toutes les opérations CRUD liées à la table `books`
 * et isole l'accès à la base de données du reste de l'application.
 *
 * PHP version 8.2.12
 *
 * Date :        12 décembre 2025
 * Maj :         26 décembre 2025
 *
 * @category     Repository
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          Book
 * @todo         Ajouter la gestion de pagination et filtres avancés
 */

namespace App\Repositories;

use App\Core\Database;
use App\Models\Book;
use PDO;


class BookRepository
{
    private PDO $pdo;


    /**
     * Initialise le repository en récupérant la connexion PDO.
     */
    public function __construct()
    {
        $this->pdo = Database::getConnection(); // Instanciation de PDO
    }

    /**
     * Récupère un livre à partir de son identifiant.
     *
     * @param int $id Identifiant du livre
     * @return Book|null Instance de Book ou null si introuvable
     */
    public function findById(int $id): ?Book
    {
        $sql = "
            SELECT
                b.*,
                u.pseudo       AS owner_pseudo,
                u.avatar_path  AS owner_avatar_path
            FROM books b
            JOIN users u ON u.id = b.user_id
            WHERE b.id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        $data = $stmt->fetch();

        if (! $data) {
            return null;
        }

        $book = $this->hydrateBook($data);

        // Hydratation du pseudo propriétaire & avatar
        $book->setOwnerPseudo($data['owner_pseudo']);
        $book->setOwnerAvatarPath($data['owner_avatar_path']);

        return $book;
    }

    /**
     * Récupère tous les livres appartenant à un utilisateur.
     * Utilisation : privé (pour Mon compte)
     * 
     * @param int $userId Identifiant de l'utilisateur
     * @return Book[] Liste des livres de l'utilisateur
     */
    public function findByUser(int $userId): array
    {
        $sql = "
            SELECT *
            FROM books
            WHERE user_id = :user_id
            ORDER BY created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        // Contiendra un tableau d'objets de type Book.
        $books = [];

        while ($row = $stmt->fetch()) {
            $books[] = $this->hydrateBook($row);
        }

        return $books;
    }

    /**
     * Récupère que les livres disponibles appartenant à un utilisateur.
     * Utilisation : public (pour profil/biblio publique)
     *
     * @param int $userId Identifiant de l'utilisateur
     * @return Book[] Liste des livres de l'utilisateur
     */    
    public function findPublicByUser(int $userId): array
    {
        $sql = "
            SELECT *
            FROM books
            WHERE user_id = :user_id
            AND status = :status
            ORDER BY created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
            'status'  => \App\Models\Book::STATUS_AVAILABLE,
        ]);

        $books = [];

        while ($row = $stmt->fetch()) {
            $books[] = $this->hydrateBook($row);
        }

        return $books;
    }

    /**
     * Récupère les livres disponibles à l’échange, avec filtre optionnel par titre.
     *
     * Les livres sont filtrés par statut "available".
     * Si un terme de recherche est fourni, seuls les titres correspondants sont retournés.
     * Le résultat est trié par date de création décroissante.
     *
     * @param string|null $search Terme de recherche sur le titre (optionnel)
     * @return Book[] Liste des livres disponibles
     */
    public function findAllAvailable(?string $search = null): array
    {
        $sql = "
            SELECT b.*, u.pseudo AS owner_pseudo
            FROM books b
            JOIN users u ON u.id = b.user_id
            WHERE b.status = :status
        ";

        $params = [
            'status' => Book::STATUS_AVAILABLE,
        ];

        // Paramètre et  clause conditionnels concaténés si recherche
        if ($search !== null && $search !== '') {
            $sql .= " AND b.title LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY b.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $books = [];

        while ($row = $stmt->fetch()) {
            $book = $this->hydrateBook($row);
            $book->setOwnerPseudo($row['owner_pseudo']); // voir point 2
            $books[] = $book;
        }

        return $books;
    }

    /**
     * Récupère tous les livres (disponibles et indisponibles),
     * avec filtre optionnel par titre.
     *
     * @param string|null $search
     * @return Book[]
     */
    public function findAll(?string $search = null): array
    {
        $sql = "
            SELECT b.*, u.pseudo AS owner_pseudo
            FROM books b
            JOIN users u ON u.id = b.user_id
            WHERE 1 = 1
        ";

        $params = [];

        if ($search !== null && $search !== '') {
            $sql .= " AND b.title LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY b.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $books = [];

        while ($row = $stmt->fetch()) {
            $book = $this->hydrateBook($row);
            $book->setOwnerPseudo($row['owner_pseudo']);
            $books[] = $book;
        }

        return $books;
    }

    /**
     * Retourne les derniers livres ajoutés disponibles.
     *
     * Les livres sont filtrés par statut "available"
     * et sont triés par date de création décroissante
     * et limités au nombre spécifié.
     *
     * @param int $limit Nombre maximum de livres à retourner
     * @return Book[] Liste des derniers livres
     */
    public function findLast(int $limit = 4): array
    {
        $sql = "
            SELECT b.*, u.pseudo AS owner_pseudo
            FROM books b
            JOIN users u ON u.id = b.user_id
            WHERE b.status = :status
            ORDER BY b.created_at DESC
            LIMIT :limit
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':status', Book::STATUS_AVAILABLE, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        $books = [];

        while ($row = $stmt->fetch()) {
            $book = $this->hydrateBook($row);
            $book->setOwnerPseudo($row['owner_pseudo']);
            $books[] = $book;
        }

        return $books;
    }

    /**
     * Insère un nouveau livre en base de données.
     *
     * @param Book $book (obj) Livre à persister
     * @return bool True en cas de succès, false sinon
     */
    public function create(Book $book): bool
    {
        $sql = "
            INSERT INTO books (user_id, title, author, description, image_path, status)
            VALUES (:user_id, :title, :author, :description, :image_path, :status)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'user_id'     => $book->getUserId(),
            'title'       => $book->getTitle(),
            'author'      => $book->getAuthor(),
            'description' => $book->getDescription(),
            'image_path'  => $book->getImagePath(),
            'status'      => $book->getStatus(),
        ]);
    }

    /**
     * Met à jour l’image associée à un livre.
     *
     * @param int         $bookId    Identifiant du livre.
     * @param string|null $imagePath Chemin relatif de l’image (ex: uploads/books/xxx.webp).
     *
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function updateImage(int $bookId, ?string $imagePath): bool
    {
        $sql = "
            UPDATE books
            SET image_path = :image_path,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'image_path' => $imagePath,
            'id'         => $bookId,
        ]);
    }

    /**
     * Met à jour les informations d’un livre existant.
     *
     * @param Book $book Livre à mettre à jour
     * @return bool True en cas de succès, false sinon
     */
    public function update(Book $book): bool
    {
        $sql = "
            UPDATE books
            SET title = :title,
                author = :author,
                description = :description,
                image_path = :image_path,
                status = :status
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id'          => $book->getId(),
            'title'       => $book->getTitle(),
            'author'      => $book->getAuthor(),
            'description' => $book->getDescription(),
            'image_path'  => $book->getImagePath(),
            'status'      => $book->getStatus(),
        ]);
    }

    /**
     * Supprime un livre à partir de son identifiant.
     *
     * @param int $id Identifiant du livre
     * @return bool True en cas de succès, false sinon
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM books WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }

    /**
     * Crée et hydrate une instance de Book à partir des données de la base.
     *
     * @param array $data Données issues de la base
     * @return Book Instance hydratée du modèle Book
     */
    private function hydrateBook(array $data): Book
    {
        $book = new Book(
            (int) $data['user_id'],
            $data['title'],
            $data['author'],
            $data['description'],
            $data['status'],
            $data['image_path'] ?? null
        );

        $book->setId((int) $data['id']);

        return $book;
    }
}
