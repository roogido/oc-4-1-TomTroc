<?php declare(strict_types=1);
/* VUE POUR LA MESSAGERIE */

use App\Core\Session;

/**
 * @var array $conversations   // liste des discussions (colonne gauche)
 * @var array|null $thread     // messages de la discussion sélectionnée
 * @var App\Models\User|null $otherUser
 */

$currentUserId = Session::getUserId();

function formatMessageDate(string $datetime): string
{
    $date = new DateTime($datetime);
    $now  = new DateTime();

    if ($date->format('Y') === $now->format('Y')) {
        // même année
        return $date->format('d.m H:i');
    }

    // année différente
    return $date->format('d.m.Y H:i');
}
?>

<h1>Messagerie</h1>

<div class="messaging-layout">

    <!-- COLONNE GAUCHE : conversations -->
    <aside class="messaging-sidebar">
        <?php if (empty($conversations)) : ?>
            <p>Aucune discussion.</p>
        <?php else : ?>
            <ul>
                <?php foreach ($conversations as $conversation) : ?>
                    <li class="conversation-item">
                        <a href="/messages/<?= (int) $conversation['user_id'] ?>">

                            <img
                                src="/assets/images/avatars/avatar-default.webp"
                                alt=""
                                width="48"
                                height="48"
                            >

                            <div class="conversation-content">

                                <div class="conversation-header">
                                    <strong><?= htmlspecialchars($conversation['pseudo']) ?></strong>

                                    <?php if (!empty($conversation['created_at'])) : ?>
                                        <span class="conversation-date">
                                            <?= formatMessageDate($conversation['created_at']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="conversation-preview">
                                    <?= htmlspecialchars(
                                        mb_strimwidth($conversation['last_message'] ?? '', 0, 56, '…')
                                    ) ?>
                                </div>

                            </div>

                            <?php if ($conversation['unread_count'] > 0) : ?>
                                <span class="conversation-badge">
                                    <?= (int) $conversation['unread_count'] ?>
                                </span>
                            <?php endif; ?>

                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </aside>

    <!-- ZONE DROITE : fil de discussion -->
    <section class="messaging-thread">

        <?php if ($otherUser === null) : ?>

            <p>Sélectionnez une discussion.</p>

        <?php else : ?>

            <div class="thread-header">
                <img
                    src="/assets/images/avatars/avatar-default.webp"
                    alt=""
                    width="48"
                    height="48"
                    class="thread-avatar"
                >

                <h2><?= htmlspecialchars($otherUser->getPseudo()) ?></h2>
            </div>
         
            <div class="thread-messages">

                <?php if (!empty($thread)) : ?>
                    <?php foreach ($thread as $message) : ?>
                        <?php
                            $isMine = ((int) $message['sender_id'] === (int) $currentUserId);
                        ?>

                        <div class="message <?= $isMine ? 'message-mine' : 'message-other' ?>">

                            <?php if (!$isMine) : ?>
                                <img
                                    src="/assets/images/avatars/avatar-default.webp"
                                    alt=""
                                    width="24"
                                    height="24"
                                    class="message-avatar"
                                >
                            <?php endif; ?>

                            <div class="message-content">
                                <div class="message-meta">
                                    <span class="message-date">
                                        <?= formatMessageDate($message['created_at']) ?>
                                    </span>
                                </div>

                                <div class="message-text">
                                    <?= nl2br(htmlspecialchars($message['content'])) ?>
                                </div>

                            </div>

                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <!-- Le formulaire est TOUJOURS visible dès qu’un utilisateur est ciblé -->
            <form method="post" action="/messages/send">
                <input type="hidden" name="receiver_id" value="<?= (int) $otherUser->getId() ?>">
                <textarea name="content" required></textarea>
                <button type="submit">Envoyer</button>
            </form>

        <?php endif; ?>

    </section>

</div>
