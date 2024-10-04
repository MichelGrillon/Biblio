<?php

namespace App\Core;

use PDO;
use Exception;

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

class Connect
{
    // Propriété protégée pour la connexion à la base de données
    protected $connexion;

    // Constructeur de la classe
    public function __construct()
    {
        // Vérification si les constantes sont déjà définies
        if (!defined('SERVER')) {
            define('SERVER', '#');
        }
        if (!defined('USER')) {
            define('USER', '#');
        }
        if (!defined('PASSWORD')) {
            define('PASSWORD', '#');
        }
        if (!defined('BASE')) {
            define('BASE', '#');
        }

        try {
            // Connexion à la base de données en utilisant les informations fournies
            $this->connexion = new PDO("mysql:host=" . SERVER . ";dbname=" . BASE, USER, PASSWORD);

            // Configuration supplémentaire pour n'utiliser que la table 'users'
            $this->connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Définir le charset de la connexion à UTF-8 pour éviter les problèmes d'encodage
            $this->connexion->exec("SET NAMES 'utf8'");
        } catch (Exception $e) {
            // En cas d'erreur lors de la connexion, afficher un message d'erreur
            echo 'Erreur : ' . $e->getMessage();
        }
    }

    // Méthode pour obtenir la connexion à la base de données
    public function getConnection()
    {
        return $this->connexion;
    }

    // Méthode pour exécuter une requête préparée
    public function executePreparedQuery($query, $params)
    {
        try {
            $statement = $this->connexion->prepare($query);
            $statement->execute($params);
            return $statement;
        } catch (Exception $e) {
            // En cas d'erreur lors de l'exécution de la requête, afficher un message d'erreur
            echo 'Erreur : ' . $e->getMessage();
        }
    }
}
