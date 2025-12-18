<?php declare(strict_types=1);
/**
 * Class MessageController
 *
 * Contrôleur de la page d’accueil.
 *
 * Responsable de l’affichage des contenus publics,
 * notamment la liste des derniers livres disponibles à l’échange.
 *
 * PHP version 8.2.12
 *
 * Date :      15 décembre 2025
 * Maj  :      16 décembre 2025
 *
 * @category   Controllers
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        BookRepository
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Core\HttpForbiddenException;
use App\Core\HttpNotFoundException;
use App\Repositories\MessageRepository;
use App\Repositories\UserRepository;
use App\Models\Message;

class MessageController extends Controller
{
    private MessageRepository $messages;
    private UserRepository $users;

    public function __construct()
    {
        parent::__construct();
        $this->messages = new MessageRepository();
        $this->users    = new UserRepository();
    }

    /**
     * Messagerie (liste des conversations)
     * Route : GET /messages
     */
    public function inbox(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException('Accès interdit.');
        }

        $currentUserId = Session::getUserId();

        $conversations = $this->messages->findConversationsByUser($currentUserId);

        $this->setPageTitle('Messagerie');
        $this->render('messages/index', [
            'conversations' => $conversations,
            'thread'        => null,
            'otherUser'     => null,
        ]);
    }

    /**
     * Fil de discussion avec un utilisateur
     * Route : GET /messages/{userId}
     */
    public function thread(int $userId): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException('Accès interdit.');
        }

        $currentUserId = Session::getUserId();

        if ($userId === $currentUserId) {
            throw new HttpNotFoundException();
        }

        $otherUser = $this->users->findById($userId);
        if (!$otherUser) {
            throw new HttpNotFoundException();
        }

        $conversations = $this->messages->findConversationsByUser($currentUserId);
        $thread        = $this->messages->findThread($currentUserId, $userId);

        // Marquer les messages reçus comme lus
        $this->messages->markAsRead($userId, $currentUserId);

        $this->setPageTitle('Messagerie');
        $this->render('messages/index', [
            'conversations' => $conversations,
            'thread'        => $thread,
            'otherUser'     => $otherUser,
        ]);
    }

    /**
     * Envoi d'un message
     * Route : POST /messages/send
     */
    public function send(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException('Accès interdit.');
        }

        $senderId   = Session::getUserId();
        $receiverId = (int) ($_POST['receiver_id'] ?? 0);
        $content    = trim($_POST['content'] ?? '');

        if ($receiverId <= 0 || $content === '') {
            Session::addFlash('error', 'Message invalide.');
            header('Location: /messages');
            exit;
        }

        if ($receiverId === $senderId) {
            throw new HttpForbiddenException('Action interdite.');
        }

        if (!$this->users->findById($receiverId)) {
            throw new HttpNotFoundException();
        }

        $message = new Message($senderId, $receiverId, $content);
        $this->messages->create($message);

        header('Location: /messages/' . $receiverId);
        exit;
    }
}
