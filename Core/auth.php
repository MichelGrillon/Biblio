<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Inclure le fichier connect.php en utilisant un chemin relatif
include 'connect.php';

// Créer une instance de la classe Connect pour obtenir la connexion à la base de données
$connect = new App\Core\Connect();
$connexion = $connect->getConnection();

// Vérification de l'existence du jeton CSRF dans la session et dans la requête
if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
    die('Erreur CSRF');
}

$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : NULL;
$password = isset($_POST['password']) ? htmlspecialchars($_POST['password']) : NULL;

try {
    // Préparation de la requête SQL pour récupérer l'utilisateur par email
    $query = "SELECT * FROM users WHERE email = :email";
    $statement = $connexion->prepare($query);
    $statement->bindParam(':email', $email);
    $statement->execute();
    $user = $statement->fetch(PDO::FETCH_OBJ);

    if ($user) {
        // Vérification du mot de passe avec password_verify
        if (password_verify($password, $user->password)) {
            // Régénération de l'identifiant de session pour prévenir le détournement de session
            session_regenerate_id(true);
            // Création de la session
            $_SESSION['username'] = $user->username;
            // Redirection vers la page d'accueil des créations après connexion réussie
            header("Location: /../public/index.php?page=home");
            exit;
        } else {
            echo "Mot de passe incorrect";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }
} catch (PDOException $e) {
    echo "Erreur lors de l'exécution de la requête : " . $e->getMessage();
}

// Supprimer le jeton CSRF de la session de l'utilisateur
unset($_SESSION['csrf_token']);
