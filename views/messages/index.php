<?php declare(strict_types=1);

use App\Core\Session;

/**
 * @var array      $conversations
 * @var array|null $thread
 * @var App\Models\User|null $otherUser
 */

$currentUserId = Session::getUserId();

function formatMessageDate(string $datetime): array
{
    $date = new DateTime($datetime);
    $now  = new DateTime();

    // Aujourd’hui → heure seule
    if ($date->format('Y-m-d') === $now->format('Y-m-d')) {
        return [
            'date' => null,
            'time' => $date->format('H:i'),
        ];
    }

    // Même année → DD.MM + heure
    if ($date->format('Y') === $now->format('Y')) {
        return [
            'date' => $date->format('d.m'),
            'time' => $date->format('H:i'),
        ];
    }

    // Année différente → DD.MM.YYYY + heure
    return [
        'date' => $date->format('d.m.Y'),
        'time' => $date->format('H:i'),
    ];
}
?>

<div class="messages-page">
    <div class="messages-inner">
        <div class="messages-layout">

            <!-- ======================
                COLONNE CONVERSATIONS
                ====================== -->
            <aside class="messages-list">
                <h1 class="page-title">Messagerie</h1>

                <?php if (empty($conversations)) : ?>
                    <ul class="conversations conversations--users">

                        <?php foreach ($users as $user) : ?>
                            <li class="conversation">
                                <a
                                    href="/messages/<?= (int) $user['id'] ?>"
                                    class="conversation-link"
                                >
                                    <div class="conversation-inner">
                                        <img
                                            src="<?= htmlspecialchars(
                                                $user['avatar_path']
                                                    ? '/uploads/avatars/' . $user['avatar_path']
                                                    : \App\Models\User::DEFAULT_AVATAR
                                            ) ?>"
                                            alt="Avatar de <?= htmlspecialchars($user['pseudo']) ?>"
                                            class="avatar avatar--md avatar--portrait"
                                        >

                                        <div class="conversation-body">
                                            <strong class="conversation-name">
                                                <?= htmlspecialchars($user['pseudo']) ?>
                                            </strong>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                <?php else : ?>
                    <ul class="conversations">

                        <?php foreach ($conversations as $conversation) : ?>
                            <?php
                                $avatarPath = !empty($conversation['avatar_path'])
                                    ? '/uploads/avatars/' . $conversation['avatar_path']
                                    : \App\Models\User::DEFAULT_AVATAR;

                                $isActive = (
                                    $otherUser !== null
                                    && (int) $conversation['user_id'] === (int) $otherUser->getId()
                                );
                            ?>

                            <li class="conversation <?= $isActive ? 'conversation--active' : '' ?>">
                                <a
                                    href="/messages/<?= (int) $conversation['user_id'] ?>"
                                    class="conversation-link"
                                >
                                    <div class="conversation-inner">
                                        <img
                                            src="<?= htmlspecialchars($avatarPath) ?>"
                                            alt=""
                                            class="avatar avatar--md avatar--portrait"
                                        >

                                        <div class="conversation-body">

                                            <div class="conversation-head">
                                                <span class="conversation-name">
                                                    <?= htmlspecialchars($conversation['pseudo']) ?>
                                                </span>

                                                <?php if (!empty($conversation['created_at'])) : ?>
                                                    <?php $date = formatMessageDate($conversation['created_at']); ?>

                                                    <span class="conversation-date">
                                                        <?= htmlspecialchars(
                                                            trim(($date['date'] ? $date['date'] . ' ' : '') . $date['time'])
                                                        ) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <p class="conversation-preview">
                                                <?= htmlspecialchars(
                                                    mb_strimwidth($conversation['last_message'] ?? '', 0, 48, '…')
                                                ) ?>
                                            </p>

                                        </div>

                                        <?php if ($conversation['unread_count'] > 0) : ?>
                                            <span class="conversation-badge">
                                                <?= (int) $conversation['unread_count'] ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                </a>
                            </li>
                        <?php endforeach; ?>

                    </ul>
                <?php endif; ?>

            </aside>

            <!-- ======================
                ZONE DISCUSSION
                ====================== -->
            <div class="messages-thread" aria-labelledby="thread-title">

                <?php if ($otherUser === null) : ?>
                    <div class="messages-thread--empty">
                        <div class="messages-empty">

                            <?php if (empty($conversations)) : ?>
                                <p class="messages-empty-title">Aucune discussion</p>
                                <p>
                                    Pour le moment, vous n’avez échangé avec aucun utilisateur.
                                    <br>
                                    Démarrez une discussion pour commencer.
                                </p>
                            <?php else : ?>
                                <p class="messages-empty-title">Sélectionnez une discussion</p>
                                <p>
                                    Choisissez une conversation dans la colonne de gauche
                                    <br>
                                    pour afficher les messages.
                                </p>
                            <?php endif; ?>

                        </div>
                    </div>
                <?php else : ?>

                    <!-- En-tête discussion -->
                    <header class="thread-header">

                        <a href="/messages" class="thread-back">
                            ← retour
                        </a>

                        <div class="thread-user">
                            <img
                                src="<?= htmlspecialchars($otherUser->getAvatarPath()) ?>"
                                alt="Avatar de <?= htmlspecialchars($otherUser->getPseudo()) ?>"
                                class="avatar avatar--md avatar--portrait"
                            >

                            <span class="thread-username">
                                <?= htmlspecialchars($otherUser->getPseudo()) ?>
                            </span>
                        </div>

                    </header>

                    <!-- Messages -->
                    <div
                        class="thread-body"
                        tabindex="0"
                        role="region"
                        aria-labelledby="thread-title"
                    >
                        <?php foreach ($thread ?? [] as $message) : ?>
                            <?php
                                $isMine = ((int) $message['sender_id'] === $currentUserId);
                                $formattedDate = formatMessageDate($message['created_at']);
                            ?>

                            <div class="message <?= $isMine ? 'message--mine' : 'message--other' ?>">

                                <div class="message-meta">
                                    <?php if (! $isMine) : ?>
                                        <img
                                            src="<?= htmlspecialchars($otherUser->getAvatarPath()) ?>"
                                            alt="Avatar de <?= htmlspecialchars($otherUser->getPseudo()) ?>"
                                            class="avatar avatar--thread"
                                        >
                                    <?php endif; ?>

                                    <span class="message-date">
                                        <?php if (!empty($formattedDate['date'])) : ?>
                                            <span class="message-date__day">
                                                <?= htmlspecialchars($formattedDate['date']) ?>
                                            </span>
                                        <?php endif; ?>

                                        <span class="message-date__time">
                                            <?= htmlspecialchars($formattedDate['time']) ?>
                                        </span>
                                    </span>
                                </div>

                                <div class="message-content">
                                    <p class="message-text">
                                        <?= nl2br(htmlspecialchars($message['content'])) ?>
                                    </p>
                                </div>

                            </div>
                        <?php endforeach; ?>

                    </div>

                    <!-- Formulaire envoi -->
                    <form
                        method="post"
                        action="/messages/send"
                        class="thread-form"
                    >
                        <input
                            type="hidden"
                            name="receiver_id"
                            value="<?= (int) $otherUser->getId() ?>"
                        >

                        <textarea
                            name="content"
                            placeholder="Tapez votre message ici"
                            required
                        ></textarea>

                        <button
                            type="submit"
                            class="btn btn-primary btn--md"
                        >
                            Envoyer
                        </button>
                    </form>

                <?php endif; ?>

            </div>

        </div>
    </div>
</div>