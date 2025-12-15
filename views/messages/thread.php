<?php declare(strict_types=1);

use App\Core\Session;

/** @var array $thread */
/** @var \App\Models\User $otherUser */

$errors  = Session::getFlashes('error');
$success = Session::getFlashes('success');

$currentUserId = Session::getUserId();
?>

<h1>Discussion avec <?= htmlspecialchars($otherUser->getPseudo()) ?></h1>

<?php if (!empty($errors)) : ?>
    <div>
        <ul>
            <?php foreach ($errors as $e) : ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($success)) : ?>
    <div>
        <?php foreach ($success as $msg) : ?>
            <p><?= htmlspecialchars($msg) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<section>
    <?php if (empty($thread)) : ?>
        <p>Aucun message pour le moment.</p>
    <?php else : ?>
        <?php foreach ($thread as $message) : ?>
            <?php
            $isMine = ((int) $message['sender_id'] === (int) $currentUserId);
            ?>
            <article style="margin-bottom:10px;">
                <p>
                    <strong><?= $isMine ? 'Moi' : htmlspecialchars($otherUser->getPseudo()) ?> :</strong>
                    <?= nl2br(htmlspecialchars($message['content'])) ?>
                </p>
                <small><?= htmlspecialchars($message['created_at']) ?></small>
                <?php if (!$isMine && !empty($message['read_at'])) : ?>
                    <small> — lu</small>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<hr>

<h2>Répondre</h2>

<form method="post" action="/messages/send">
    <input type="hidden" name="receiver_id" value="<?= (int) $otherUser->getId() ?>">
    <textarea name="content" required></textarea>
    <button type="submit">Envoyer</button>
</form>

<p>
    <a href="/messages">← Retour à la messagerie</a>
</p>
