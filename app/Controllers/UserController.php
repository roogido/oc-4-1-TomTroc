<?php declare(strict_types=1);
/**
 * Class UserController
 *
 *
 * PHP version 8.2.12
 *
 * Date :      15 décembre 2025
 * Maj  :      
 *
 * @category   Controllers
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 * @see        
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\HttpNotFoundException;
use App\Core\Session;
use App\Repositories\UserRepository;
use App\Repositories\BookRepository;


class UserController extends Controller
{
    private UserRepository $users;
    private BookRepository $books;


    public function __construct()
    {
        parent::__construct();
        $this->users = new UserRepository();
        $this->books = new BookRepository();
    }

    public function show(int $id): void
    {
        $user = $this->users->findById($id);
        if (! $user) {
            throw new HttpNotFoundException();
        }

        // Livres publics du user
        $books = $this->books->findPublicByUser($id);

        // Ancienneté
        $memberSince = $this->users->getMemberSince($id);

        $this->setPageTitle('Bibliothèque de ' . $user->getPseudo());

        $this->render('users/show', [
            'user'        => $user,
            'books'       => $books,
            'memberSince' => $memberSince,
        ]);
    }
}
