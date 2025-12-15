<?php declare(strict_types=1);
/**
 * Class Message
 *
 *
 * PHP version 8.2.12
 *
 * Date :        15 décembre 2025
 * Maj :         -
 *
 * @category     Models
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          
 * @todo         
 */

namespace App\Models;


class Message
{
    private ?int $id = null;
    private int $senderId;
    private int $receiverId;
    private string $content;


    /**
     * Constructeur.
     *
     * @param int    $senderId    ID de l'expéditeur.
     * @param int    $receiverId  ID du destinataire.
     * @param string $content     Contenu du message.
     */
    public function __construct(int $senderId, int $receiverId, string $content)
    {
        $this->senderId   = $senderId;
        $this->receiverId = $receiverId;
        $this->content    = trim($content);
    }

    /**
     * Définit l'identifiant du message (après insertion).
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Retourne l'identifiant du message.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne l'ID de l'expéditeur.
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    /**
     * Retourne l'ID du destinataire.
     */
    public function getReceiverId(): int
    {
        return $this->receiverId;
    }

    /**
     * Retourne le contenu du message.
     */
    public function getContent(): string
    {
        return $this->content;
    }
}
