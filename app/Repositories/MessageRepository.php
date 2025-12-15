<?php declare(strict_types=1);
/**
 * Class MessageRepository
 *
 *
 * PHP version 8.2.12
 *
 * Date :        15 décembre 2025
 * Maj :         
 *
 * @category     Repository
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          
 * @todo         
 */

namespace App\Repositories;

use App\Core\Database;
use App\Models\Message;
use PDO;


class MessageRepository
{
    private PDO $pdo;

    /**
     * Initialise le repository avec la connexion PDO.
     */
    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    /**
     * Enregistre un nouveau message en base.
     *
     * @param Message $message
     * @return bool
     */
    public function create(Message $message): bool
    {
        $sql = "
            INSERT INTO messages (sender_id, receiver_id, content)
            VALUES (:sender_id, :receiver_id, :content)
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'sender_id'   => $message->getSenderId(),
            'receiver_id' => $message->getReceiverId(),
            'content'     => $message->getContent(),
        ]);
    }

    /**
     * Récupère la liste des messages reçus par un utilisateur.
     *
     * Chaque message est accompagné du pseudo de l'expéditeur.
     *
     * @param int $userId
     * @return array
     */
    public function findReceivedMessages(int $userId): array
    {
        $sql = "
            SELECT 
                m.id,
                m.sender_id,
                m.receiver_id,
                m.content,
                m.created_at,
                m.read_at,
                u.pseudo AS sender_pseudo
            FROM messages m
            JOIN users u ON u.id = m.sender_id
            WHERE m.receiver_id = :user_id
            ORDER BY m.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'user_id' => $userId,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Récupère le fil de discussion entre deux utilisateurs.
     *
     * @param int $userA
     * @param int $userB
     * @return array
     */
    public function findThread(int $userA, int $userB): array
    {
        $sql = "
            SELECT
                m.id,
                m.sender_id,
                m.receiver_id,
                m.content,
                m.created_at,
                m.read_at
            FROM messages m
            WHERE
                (m.sender_id = :userA1 AND m.receiver_id = :userB1)
                OR
                (m.sender_id = :userB2 AND m.receiver_id = :userA2)
            ORDER BY m.created_at ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'userA1' => $userA,
            'userB1' => $userB,
            'userB2' => $userB,
            'userA2' => $userA,
        ]);

        return $stmt->fetchAll();
    }

    /**
     * Marque comme lus tous les messages reçus d'un utilisateur donné.
     *
     * @param int $senderId
     * @param int $receiverId
     * @return void
     */
    public function markAsRead(int $senderId, int $receiverId): void
    {
        $sql = "
            UPDATE messages
            SET read_at = NOW()
            WHERE sender_id = :sender
            AND receiver_id = :receiver
            AND read_at IS NULL
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'sender'   => $senderId,
            'receiver' => $receiverId,
        ]);
    }
}
