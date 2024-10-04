<?php

namespace App\Core;

use PDO;
use PDOException;

class DbConnect
{
    // Propriétés protégées pour la connexion à la base de données et la requête
    protected $connection;
    protected $connexion; // Propriété pour stocker la connexion à la base de données admin
    protected $request;

    // Paramètres de connexion à la base de données
    const SERVER = 'localhost'; // Adresse du serveur de base de données
    const USER = 'root'; // Nom d'utilisateur de la base de données
    const PASSWORD = 'Tolos@31'; // Mot de passe de la base de données
    const BASE = 'michely31'; // Nom de la base de données

    // Constructeur de la classe
    public function __construct()
    {
        try {
            // Tentative de connexion à la base de données avec PDO
            $this->connection = new PDO('mysql:host=' . self::SERVER . ';dbname=' . self::BASE, self::USER, self::PASSWORD);

            // Activation des erreurs PDO pour faciliter le débogage
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Les résultats des requêtes seront renvoyés en tableau d'objets par défaut
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

            // Encodage des caractères spéciaux en 'utf8'
            $this->connection->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
        } catch (PDOException $e) {
            // En cas d'échec de la connexion, affiche le message d'erreur et termine le script
            die('Erreur : ' . $e->getMessage());
        }
    }

    // Méthode pour tester la connexion à la base de données
    public function ping()
    {
        try {
            // Envoi d'une requête simple pour tester la connexion
            $this->request = $this->connection->query('SELECT 1');
            return true;
        } catch (PDOException $e) {
            // Enregistrement ou affichage du message d'erreur en cas d'échec de la connexion
            error_log('Erreur de connexion à la base de données : ' . $e->getMessage());
            return false;
        }
    }

    // Méthode pour exécuter une requête SQL sécurisée
    public function executeQuery($sql, $params = [])
    {
        try {
            // Préparation de la requête SQL
            $stmt = $this->connection->prepare($sql);

            // Vérifier si des paramètres ont été fournis et les lier à la requête
            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }

            // Exécution de la requête
            $stmt->execute();

            // Renvoi du résultat de la requête
            return $stmt;
        } catch (PDOException $e) {
            // Enregistrement ou affichage du message d'erreur en cas d'échec de la requête
            error_log('Erreur lors de l\'exécution de la requête SQL : ' . $e->getMessage());
            return false;
        }
    }
    // Méthode pour obtenir la connexion à la base de données admin
    public function getConnection()
    {
        return $this->connexion;
    }
}
