<?php

namespace App\Controllers;

// Inclure le fichier contenant la définition de la classe DbConnect
require_once __DIR__ . '/../Core/DbConnect.php';

require_once __DIR__ . '/../Models/UserModel.php';
//require_once __DIR__ . '/../Core/config.php';
require_once __DIR__ . '/../Models/EmprunteurModel.php';
require_once __DIR__ . '/../Models/EmpruntModel.php';
require_once __DIR__ . '/../Models/LivreModel.php';

abstract class Controller
{
    public function startSession()
    {
        // Démarrer ou initialiser une session si elle n'est pas déjà démarrée
        if (!isset($_SESSION)) {
            session_start();
        }
    }

    // Méthode pour échapper les données pour éviter les attaques XSS
    protected function escapeData($data)
    {
        // Implémentation de la logique d'échappement des données ici
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }

    // Méthode pour exécuter une requête préparée
    protected function executePreparedQuery($sql, $params = [])
    {
        // Instancier la classe DbConnect pour établir une connexion à la base de données
        $db = new \App\Core\DbConnect();

        // Exécuter la requête préparée en utilisant la méthode executeQuery de la classe DbConnect
        $stmt = $db->executeQuery($sql, $params);

        // Retourner le résultat de la requête préparée
        return $stmt;
    }

    // Méthode pour valider les données postées
    protected function validatePostData(array $post, array $fields): bool
    {
        // Vérifie si tous les champs requis sont présents dans les données postées
        foreach ($fields as $field) {
            if (!isset($post[$field]) || empty($post[$field])) {
                return false;
            }
        }
        return true;
    }

    // Méthode pour valider les fichiers postés
    protected function validatePostedFiles(array $files, array $fields): bool
    {
        // Vérifie si tous les champs de fichiers requis sont présents et sans erreurs
        foreach ($fields as $field) {
            if (!isset($files[$field]) || $files[$field]['error'] !== UPLOAD_ERR_OK) {
                return false;
            }
        }
        return true;
    }

    // Méthode pour gérer les attributs HTML
    protected function addAttributes(array $attributes): string
    {
        // Générer une chaîne d'attributs HTML à partir du tableau d'attributs
        $attributeString = '';
        foreach ($attributes as $attribute => $value) {
            $attributeString .= "$attribute=\"" . htmlspecialchars($value) . "\" ";
        }
        return $attributeString;
    }

    // Méthode pour rendre une vue avec les données fournies
    public function render(string $path, array $data = [])
    {
        // Démarrer ou initialiser la session
        $this->startSession();

        // Permet d'extraire les données récupérées sous forme de variables
        extract($data);

        // On crée le buffer de sortie
        ob_start();

        // Crée le chemin et inclut le fichier de la vue souhaitée
        include dirname(__DIR__) . '/Views/' . $path . '.php';

        // On vide le buffer dans la variable $content
        $content = ob_get_clean();

        // On échappe les données pour éviter les attaques XSS
        //$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

        // On fabrique le "template"
        include dirname(__DIR__) . '/Views/Base.php';
    }
}
