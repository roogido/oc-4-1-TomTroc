<?php declare(strict_types=1);
/**
 * Class UserController
 *
 * Contrôleur chargé de l’affichage public du profil d’un utilisateur.
 *
 * Permet de consulter la bibliothèque publique d’un membre :
 *  - informations de base (pseudo, ancienneté)
 *  - liste des livres disponibles à l’échange
 *
 * Aucune action sensible (lecture seule).
 * Aucune authentification requise.
 *
 * PHP version 8.2.12
 *
 * Date :      15 décembre 2025
 * Maj  :      19 décembre 2025
 *
 * @category   Controllers
 * @author     Salem Hadjali <salem.hadjali@gmail.com>
 * @version    1.0.0
 * @since      1.0.0
 *
 * @see        App\Repositories\UserRepository
 * @see        App\Repositories\BookRepository
 * @see        App\Core\Controller
 * @see        App\Core\HttpNotFoundException
 */

namespace App\Controllers;

use App\Core\Controller;
use App\Core\HttpNotFoundException;
use App\Repositories\UserRepository;
use App\Repositories\BookRepository;


class UserController extends Controller
{
    private UserRepository $users;
    private BookRepository $books;


    /**
     * Initialise le contrôleur utilisateur.
     *
     * Instancie les repositories nécessaires à l’accès
     * aux données des utilisateurs et de leurs livres.
     */
    public function __construct()
    {
        parent::__construct();
        $this->users = new UserRepository();
        $this->books = new BookRepository();
    }

    /**
     * Affiche le profil public d’un utilisateur.
     *
     * Présente les informations publiques de l’utilisateur
     * ainsi que la liste de ses livres disponibles à l’échange.
     *
     * @param int $id Identifiant de l’utilisateur
     *
     * @throws HttpNotFoundException Si l’utilisateur n’existe pas
     */
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
            'pageStyles' => ['user-public.css'],
            'pageClass' => 'is-light-page',
        ]);
    }
}
