<?php
/*Ce fichier ne contient pas de logique d'authentification, il ne traite que des opérations CRUD 
(create, read, update, delete) sur les utilisateurs dans la base de données.
Voici comment on peut organiser nos méthodes dans la classe UserModel en fonction des fonctionnalités 
de gestion des utilisateurs (fichier Controllers/UserController.php) :
// Méthode pour enregistrer un nouvel utilisateur dans la base de données
    public function registerUser($email, $password, $username)
    {
        // Logique pour insérer un nouvel utilisateur dans la base de données
    }

    // Méthode pour récupérer les informations d'un utilisateur à partir de la base de données
    public function getUserByEmail($email)
    {
        // Logique pour récupérer les informations de l'utilisateur à partir de son email
    }

    // Méthode pour mettre à jour les informations d'un utilisateur dans la base de données
    public function updateUser($userId, $newData)
    {
        // Logique pour mettre à jour les informations de l'utilisateur dans la base de données
    }

    // Méthode pour supprimer un utilisateur de la base de données
    public function deleteUser($userId)
    {
        // Logique pour supprimer l'utilisateur de la base de données
    }

    // Méthode pour vérifier si un utilisateur existe dans la base de données
    public function userExists($email)
    {
        // Logique pour vérifier si un utilisateur existe dans la base de données
    }

    // Méthode pour gérer la connexion de l'utilisateur
    public function loginUser($email, $password)
    {
        // Logique pour vérifier les informations de connexion de l'utilisateur
    }

    // Méthode pour gérer la déconnexion de l'utilisateur
    public function logoutUser()
    {
        // Logique pour déconnecter l'utilisateur
    }
    Ces méthodes peuvent être appelées à partir du UserController pour effectuer diverses opérations de gestion 
    des utilisateurs,     telles que l'enregistrement, la connexion, la déconnexion, etc.
*/

namespace App\Models;

use PDO;

class UserModel
{
    protected $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    // Méthode pour insérer un nouvel utilisateur dans la base de données
    public function createUser(User $user)
    {
        // Hashage du mot de passe
        $hash = password_hash($user->getPassword(), PASSWORD_DEFAULT);

        // Préparation de la requête SQL
        $query = "INSERT INTO users (email, password, username) VALUES (?, ?, ?)";
        $statement = $this->connection->prepare($query);

        // Exécution de la requête avec les paramètres fournis
        $statement->execute([$user->getEmail(), $hash, $user->getUsername()]);

        // Vérification du succès de l'opération
        return $statement->rowCount() > 0;
    }

    // Autres méthodes...

    public function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$email]);
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser(User $user)
    {
        $hash = $user->getPassword() ? password_hash($user->getPassword(), PASSWORD_DEFAULT) : null;

        $query = "UPDATE users SET email = ?, password = ?, username = ? WHERE id_users = ?";
        $statement = $this->connection->prepare($query);

        $statement->execute([$user->getEmail(), $hash, $user->getUsername(), $user->getId()]);
        return $statement->rowCount() > 0;
    }

    public function deleteUser($userId)
    {
        $query = "DELETE FROM users WHERE id_users = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$userId]);
        return $statement->rowCount() > 0;
    }

    public function userExists($email)
    {
        $query = "SELECT COUNT(*) FROM users WHERE email = ?";
        $statement = $this->connection->prepare($query);
        $statement->execute([$email]);
        return $statement->fetchColumn() > 0;
    }

    // Méthode pour gérer la connexion de l'utilisateur
    public function loginUser($email, $password)
    {
        // Récupérer les informations de l'utilisateur par email
        $user = $this->getUserByEmail($email);

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if ($user && password_verify($password, $user['password'])) {
            // Démarrer la session et stocker les informations de l'utilisateur
            session_start();
            $_SESSION['user'] = $user;
            return true;
        } else {
            return false;
        }
    }

    // Méthode pour gérer la déconnexion de l'utilisateur
    public function logoutUser()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Détruire la session de l'utilisateur
        session_destroy();
    }
}
