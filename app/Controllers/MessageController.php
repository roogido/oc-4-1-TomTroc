<?php declare(strict_types=1);
/**
 * Class MessageController
 *
 * Contrôleur en charge de la messagerie utilisateur.
 * Gère l’affichage de la boîte de réception, des fils de discussion
 * ainsi que l’envoi des messages entre utilisateurs.
 *
 * PHP version 8.2.12
 *
 * Date :      15 décembre 2025
 * Maj  :      6 janvier 2026
 *
 * @category   Controller
 * @package    App\Controllers
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        \App\Repositories\MessageRepository
 * @see        \App\Repositories\UserRepository
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
     * Affiche la boîte de réception de l’utilisateur.
     *
     * Vérifie l’authentification, récupère les conversations
     * et rend la vue de messagerie.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas authentifié.
     */
    public function inbox(): void
    {
        if (!Session::isLogged()) {
            throw new HttpForbiddenException('Accès interdit.');
        }

        $currentUserId = Session::getUserId();

        $conversations = $this->messages->findConversationsByUser($currentUserId);

        // Utilisateurs disponibles pour discussion libre (sans passer par un livre)
        $users = [];
        if (empty($conversations)) {
            $users = $this->users->findAllExcept($currentUserId);
        }        

        $this->setPageTitle('Messagerie');
        $this->render('messages/index', [
            'conversations' => $conversations,
            'users'         => $users,   
            'thread'        => null,
            'otherUser'     => null,
            'pageStyles'    => ['messages.css'],
            'pageClass'     => 'site-wrapper--fixed messages--list',
        ]);
    }

    /**
     * Affiche le fil de discussion entre l’utilisateur courant et un autre utilisateur.
     *
     * Vérifie l’authentification, charge la conversation ciblée,
     * marque les messages reçus comme lus et rend la vue de messagerie.
     *
     * @param int $userId Identifiant de l’autre utilisateur.
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas authentifié.
     * @throws HttpNotFoundException  Si la conversation est invalide ou inexistante.
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
            'users'         => [], 
            'thread'        => $thread,
            'otherUser'     => $otherUser,
            'pageStyles'    => ['messages.css'],
            'pageClass'     => 'site-wrapper--fixed messages--thread',
        ]);
    }

    /**
     * Envoie un message à un autre utilisateur.
     *
     * Vérifie l’authentification, valide les données envoyées,
     * crée le message et redirige vers le fil de discussion.
     *
     * @return void
     *
     * @throws HttpForbiddenException Si l’utilisateur n’est pas authentifié ou tente une action interdite.
     * @throws HttpNotFoundException  Si le destinataire n’existe pas.
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
