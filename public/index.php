<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// On importe les namespaces de l'autoloader et du router
use App\Autoloader;
use App\Core\Router;
use App\Core\connect; // Importez la classe Connect
use App\Core\DbConnect; // Importez la classe DbConnect

// Inclusion de l'autoloader
include '../Autoloader.php';

// Enregistrement de l'autoloader
Autoloader::register();

// Connexion à la base de données utilisateurs
$connectUser = new Connect(); // Instanciation de la classe Connect
$connexionUser = $connectUser->getConnection(); // Obtention de la connexion à la base de données utilisateurs

// Connexion à la base de données admin
$connectAdmin = new DbConnect(); // Instanciation de la classe DbConnect
$connexionAdmin = $connectAdmin->getConnection(); // Obtention de la connexion à la base de données admin

// Instanciation de la classe Router en passant la connexion à la base de données utilisateurs
$route = new Router($connexionUser); // Passer la connexion à la base de données utilisateurs comme argument

// Lancement de l'application
$route->routes();
