<?php if ($currentUser && $currentUser->isAdmin()) : ?>
    <li>
        <a
            href="/admin"
            class="<?= str_starts_with($currentPath, '/admin') ? 'is-active' : '' ?>"
        >
            Administration
        </a>
    </li>
<?php endif; ?>
