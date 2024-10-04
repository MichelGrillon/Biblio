<?php

namespace App\Core;

class Router
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function routes()
    {
        // Récupération du nom du contrôleur depuis $_GET
        $controllerName = isset($_GET['controller']) ? ucfirst($_GET['controller']) : 'Home';
        $controllerName = '\\App\\Controllers\\' . $controllerName . 'Controller';

        // Récupération de l'action depuis $_GET
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';

        // Validation des noms de contrôleur et d'action
        if (class_exists($controllerName) && method_exists($controllerName, $action)) {
            // Instanciation du contrôleur avec la connexion à la base de données
            $controller = new $controllerName($this->connection);

            // Appel de la méthode
            if (!empty($_REQUEST)) {
                // Utilisation de call_user_func_array() pour appeler la méthode avec les paramètres
                call_user_func_array([$controller, $action], [$_REQUEST]); // <-- Utilisation d'un tableau pour passer les paramètres
            } else {
                $controller->$action();
            }
        } elseif ($controllerName === '\App\Controllers\UserController') {
            // Ajout des routes pour la gestion des utilisateurs
            $controller = new \App\Controllers\UserController($this->connection);
            if ($action === 'login') {
                $controller->login();
            } elseif ($action === 'register') {
                $controller->register();
            } elseif ($action === 'logout') {
                $controller->logout();
            }
        } else {
            // Affichage d'une erreur 404
            http_response_code(404);
            echo "La page recherchée n'existe pas";
        }
    }
}
