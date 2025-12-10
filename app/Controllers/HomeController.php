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
use App\Core\Database;
use PDO;

class HomeController extends Controller
{
    public function index(): void
    {
        // Récupère la connexion à la BDD
        $pdo = Database::getConnection();

        // Pour le "hello world", on recupere un livre (ou rien si table vide)
        $statement = $pdo->query('SELECT title FROM books ORDER BY created_at DESC LIMIT 1');
        $book      = $statement->fetch(PDO::FETCH_ASSOC) ?: null;

        $this->setPageTitle('Accueil - TomTroc');
        
        $this->render('home/index', [
            'lastBookTitle' => $book['title'] ?? null,
        ]);
    }
}
