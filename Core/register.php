<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Inclusion de connect.php
include 'connect.php';

// Vérifier que le jeton CSRF soumis avec le formulaire correspond au jeton stocké dans la session de l'utilisateur
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erreur CSRF');
}

$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : NULL;
$password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : NULL;
$username = isset($_POST['username']) ? htmlspecialchars($_POST['username']) : NULL;

try {
    // Instanciation de la classe Connect pour obtenir la connexion à la base de données
    $connect = new App\Core\Connect();
    $connexion = $connect->getConnection();

    // Hashage du mot de passe
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Préparation de la requête SQL pour l'insertion d'un nouvel utilisateur
    $requete = $connexion->prepare("INSERT INTO users (email, password, username) VALUES (:email, :password, :username)");
    $requete->bindParam(':email', $email);
    $requete->bindParam(':password', $hash);
    $requete->bindParam(':username', $username);

    // Exécution de la requête SQL
    $requete->execute();

    // Régénération de l'identifiant de session pour prévenir le détournement de session
    session_regenerate_id();

    // Redirection vers la page d'accueil après l'enregistrement
    header("Location: /index.php?login=auth");
    exit;
} catch (Exception $e) {
    // En cas d'erreur lors de l'exécution de la requête, afficher un message d'erreur
    echo "Erreur: " . $e->getMessage();
}

// Supprimer le jeton CSRF de la session de l'utilisateur
unset($_SESSION['csrf_token']);
