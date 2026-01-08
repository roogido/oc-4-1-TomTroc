<?php declare(strict_types=1);
/**
 * Class AuthRegisterValidator (final)
 *
 *
 * PHP version 8.2.12
 *
 * Date :        3 janvier 2026
 * Maj :         
 *
 * @category     Service
 * @package      
 * @author       Salem Hadjali <salem.hadjali@gmail.com>
 * @version      1.0.0
 * @since        1.0.0
 * @see          
 * @todo         
 */

namespace App\Service\Auth;

use App\Repositories\UserRepository;


final class AuthRegisterValidator
{
    private UserRepository $users;

    public function __construct(UserRepository $users)
    {
        $this->users = $users;
    }

    /**
     * Valide les données du formulaire d’inscription utilisateur.
     *
     * Nettoie les champs, vérifie les règles métier (format et unicité)
     * et retourne les erreurs ainsi que les données validées.
     *
     * @param array $data Données brutes issues du formulaire.
     * @return array{
     *     errors: array<string,string>,
     *     data: array{
     *         pseudo: string,
     *         email: string,
     *         password: string
     *     }
     * }
     */
    public function validate(array $data): array
    {
        $errors = [];

        // Nettoyage des données du formulaire
        $pseudo   = trim($data['pseudo'] ?? '');
        $email    = trim($data['email'] ?? '');
        $password = trim($data['password'] ?? '');

        // Validation du pseudo
        if ($pseudo === '') {
            $errors['pseudo'] = 'Le pseudo est requis.';
        } elseif ($this->users->findByPseudo($pseudo)) {
            $errors['pseudo'] = 'Ce pseudo est déjà pris.';
        }

        // Validation du format de l'email
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Adresse email invalide.';
        } elseif ($this->users->findByEmail($email)) {
            $errors['email'] = 'Cet email est déjà utilisé.';
        }

        if ($password === '' || strlen($password) < 6) {
            $errors['password'] = 'Minimum 6 caractères requis.';
        }

        return [
            'errors' => $errors,
            'data'   => [
                'pseudo'   => $pseudo,
                'email'    => $email,
                'password' => $password,
            ],
        ];
    }
}
