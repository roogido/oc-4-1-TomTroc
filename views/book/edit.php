<?php
/* VUE EDIT (BOOK) */

use App\Core\Session;

/** @var \App\Models\Book $book */

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

// Configuration du formulaire (EDIT)
$allowImageEdit = true;
$showStatus     = true;
$submitLabel    = 'Valider';

$pageTitle     ??= 'Modifier les informations';
$backUrl       ??= '/account';

require __DIR__ . '/_book-form.php';
