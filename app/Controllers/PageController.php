<?php declare(strict_types=1);
/**
 * Class PageController
 *
 * Contrôleur dédié aux pages institutionnelles et statiques du site.
 *
 * Gère l’affichage des pages informatives accessibles publiquement
 * telles que la politique de confidentialité et les mentions légales.
 *
 * PHP version 8.2.12
 *
 * Date :      15 décembre 2025
 * Maj :      
 *
 * @category   Controllers
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        App\Core\Controller
 */

namespace App\Controllers;

use App\Core\Controller;


class PageController extends Controller
{
    public function privacy(): void
    {
        $this->setPageTitle('Politique de confidentialité');
        $this->render('pages/privacy');
    }

    public function legal(): void
    {
        $this->setPageTitle('Mentions légales');
        $this->render('pages/legal');
    }
}
