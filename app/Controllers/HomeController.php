<?php declare(strict_types=1);
/**
 * Class HomeController
 *
 * Contrôleur de la page d’accueil.
 *
 * Responsable de l’affichage des contenus publics,
 * notamment la liste des derniers livres disponibles à l’échange.
 *
 * PHP version 8.2.12
 *
 * Date :      8 décembre 2025
 * Maj  :      14 décembre 2025
 *
 * @category   Controllers
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        BookRepository
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\BookRepository;


class HomeController extends Controller
{
    private BookRepository $books;

    /**
     * Initialise le contrôleur Home.
     *
     * Instancie le repository des livres nécessaire
     * à l’affichage des contenus de la page d’accueil.
     */
    public function __construct()
    {
        parent::__construct();
        $this->books = new BookRepository();
    }

    /**
     * Affiche la page d’accueil du site.
     *
     * Récupère les derniers livres disponibles à l’échange
     * et les transmet à la vue d’accueil.
     *
     * @return void
     */
    public function index(): void
    {
        // Récupère les 4 derniers livres disponibles
        $lastBooks = $this->books->findLast(4);

        $this->setPageTitle('Accueil - TomTroc');

        $this->render('home/index', [
            'books' => $lastBooks,
        ]);
    }
}
