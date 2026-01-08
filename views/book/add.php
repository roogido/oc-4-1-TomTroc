<?php
/* VUE ADD (BOOK) */

use App\View\FlashHelper;

// Récupération des messages flash normalisés :
//  - (erreurs globales, erreurs par champ, anciennes valeurs, succès)
//  extract() : créer automatiquement les variables utilisables directement dans la vue
extract(FlashHelper::extract());

require __DIR__ . '/_book-form.php';
