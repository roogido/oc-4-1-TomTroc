<?php declare(strict_types=1);
/**
 * class HomeController
 *
 * 
 * PHP version 8.2.12
 * 
 * Date :      8 décembre 2025
 * Maj  :
 * 
 * @category   
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      
 * @see      
 * @todo       ...  
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\BookRepository;


class HomeController extends Controller
{
    private BookRepository $books;

    public function __construct()
    {
        parent::__construct();
        $this->books = new BookRepository();
    }

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
