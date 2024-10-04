<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Core\Connect;
use App\Models\UserModel;
use App\Models\User;

class UserController extends Controller
{
    protected $userModel;

    public function __construct($connection)
    {
        $this->userModel = new UserModel($connection);
    }

    public function showRegistrationForm()
    {
        $csrfToken = bin2hex(openssl_random_pseudo_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        $this->render('register', ['csrf_token' => $csrfToken]);
    }

    public function register()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "Erreur CSRF : Jeton CSRF invalide.";
            return;
        }

        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
        $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : null;
        $username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : null;

        $user = new User($email, $password, $username);

        try {
            $this->userModel->createUser($user);

            header("Location: https://michel-grillon.fr/projects/php/cefiiBiblio/index.php?login=auth");
            exit;
        } catch (\Exception $e) {
            echo "Erreur: " . $e->getMessage();
        }
    }

    public function showLoginForm()
    {
        $csrfToken = bin2hex(openssl_random_pseudo_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        $this->render('login', ['csrf_token' => $csrfToken]);
    }

    public function login()
    {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            echo "Erreur CSRF : Jeton CSRF invalide.";
            return;
        }

        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
        $password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : null;

        if ($this->userModel->loginUser($email, $password)) {
            header("Location: https://michel-grillon.fr/projects/php/cefiiBiblio/index.php");
            exit;
        } else {
            echo "Erreur: Email ou mot de passe incorrect.";
        }
    }

    public function logout()
    {
        $this->userModel->logoutUser();
        header("Location: https://michel-grillon.fr/projects/php/cefiiBiblio/index.php?login=auth");
        exit;
    }
}