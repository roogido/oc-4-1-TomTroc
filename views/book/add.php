<?php
/* VUE ADD (BOOK) */

use App\Core\Session;

// Récupération des données flash
$errorsAll = Session::getFlashes('error');
$success  = Session::getFlashes('success');
$oldAll   = Session::getFlashes('old');

/*
 * Convention :
 * - $globalError : string|null
 * - $errors      : array par champ
 * - $old         : anciennes valeurs
 */
$globalError = null;
$errors = [];
$old    = $oldAll[0] ?? [];

foreach ($errorsAll as $err) {
    if (is_array($err)) {
        $errors = $err;
    } elseif (is_string($err)) {
        $globalError = $err;
    }
}

// Configuration formulaire ADD
$allowImageEdit = true;
$showStatus     = false;
$submitLabel    = 'Ajouter le livre';

$pageTitle     ??= 'Ajouter un livre';
$backUrl       ??= '/account';

require __DIR__ . '/_book-form.php';
