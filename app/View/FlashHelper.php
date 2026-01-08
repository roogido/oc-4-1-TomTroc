<?php declare(strict_types=1);
/**
 * Class FlashHelper (final)
 *
 * Helper de vue chargé de centraliser l’extraction et la normalisation
 * des messages flash stockés en session (succès, erreurs globales,
 * erreurs par champ et anciennes valeurs).
 *
 * Cette classe permet d’éviter la duplication de logique dans les vues
 * et garantit une convention uniforme pour la gestion des formulaires.
 *
 * PHP version 8.2.12
 *
 * Date :        2 janvier 2026
 * Maj :         
 *
 * @category     View Helper
 * @package      App\View
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          App\Core\Session
 * @todo         Mutualiser la gestion des messages flash côté layout si nécessaire
 */

namespace App\View;

use App\Core\Session;


final class FlashHelper
{

    /**
     * Extrait et normalise les messages flash pour les vues
     *
     * @return array{
     *     globalError: string|null,
     *     errors: array<string,string>,
     *     old: array<string,mixed>,
     *     success: array<int,string>
     * }
     */    
    public static function extract(): array
    {

        /*
        * Convention :
        * - $globalError : string|null
        * - $errors      : array par champ
        * - $old         : anciennes valeurs
        */        
        $errorsAll = Session::getFlashes('error');
        $success   = Session::getFlashes('success');
        $oldAll    = Session::getFlashes('old');

        $globalError = null;
        $errors = [];
        $old = $oldAll[0] ?? [];

        // Sépare les erreurs globales (string) des erreurs par champ (tablea)
        foreach ($errorsAll as $err) {
            if (is_array($err)) {
                $errors = $err;
            } elseif (is_string($err)) {
                $globalError = $err;
            }
        }

        // Retourne les données normalisées pour les vues
        // compact() : Construit un tableau associatif à partir des noms de 
        // variables fournis équivalent à :
        // [
        //   'globalError' => $globalError,
        //   'errors'      => $errors,
        //   'old'         => $old,
        //   'success'     => $success
        // ]        
        return compact('globalError', 'errors', 'old', 'success');
    }
}
