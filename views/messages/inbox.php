<?php declare(strict_types=1);

use App\Core\Session;

/** @var array $messages */

$errors  = Session::getFlashes('error');
$success = Session::getFlashes('success');
?>

<h1>Messagerie</h1>

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

<?php if (empty($messages)) : ?>
    <p>Aucun message reçu.</p>
<?php else : ?>
    <ul>
        <?php foreach ($messages as $message) : ?>
            <li>
                <p>
                    <strong><?= htmlspecialchars($message['sender_pseudo']) ?></strong>
                    — <?= nl2br(htmlspecialchars($message['content'])) ?>
                </p>

                <small><?= htmlspecialchars($message['created_at']) ?></small>

                <?php if ($message['read_at']) : ?>
                    <small> — lu</small>
                <?php else : ?>
                    <small> — non lu</small>
                <?php endif; ?>

                <p>
                    <a href="/messages/<?= (int) $message['sender_id'] ?>">
                        Ouvrir la discussion
                    </a>
                </p>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
