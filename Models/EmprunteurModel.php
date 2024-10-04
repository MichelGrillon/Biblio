<?php

namespace App\Models;

use PDO;
use Exception;
use App\Core\Connect;
use App\Entities\Emprunteur;

class EmprunteurModel
{
    // Propriété pour la connexion à la base de données
    protected $connexion;

    // Constructeur de la classe
    public function __construct()
    {
        // Instanciation de la classe Connect pour accéder à la base de données
        $connect = new Connect();
        $this->connexion = $connect->getConnection();
    }

    // Méthode pour récupérer tous les emprunteurs
    public function findAll()
    {
        // Construction de la requête SELECT
        $sql = "SELECT * FROM Emprunteur";
        $stmt = $this->connexion->query($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Entities\Emprunteur');

        // Récupération des résultats de la requête sous forme d'objets Emprunteur
        $emprunteurs = $stmt->fetchAll();

        // Retourne le tableau d'objets Emprunteur
        return $emprunteurs;
    }

    // Méthode pour récupérer un emprunteur par son ID
    public function find($id)
    {
        // Récupération de l'emprunteur par son ID
        $sql = "SELECT * FROM Emprunteur WHERE id_emprunteur = :id_emprunteur";
        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(':id_emprunteur', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'App\Entities\Emprunteur');

        // Récupération de l'objet Emprunteur
        $emprunteur = $stmt->fetch();

        // Retour de l'emprunteur sous forme d'objet
        return $emprunteur;
    }

    // Méthode pour créer un nouvel emprunteur
    public function create(Emprunteur $emprunteur)
    {
        // Préparation de la requête INSERT INTO
        $stmt = $this->connexion->prepare("INSERT INTO Emprunteur (nom, prenom, date_naissance, adresse, numero_telephone, email, date_inscription) VALUES (:nom, :prenom, :date_naissance, :adresse, :numero_telephone, :email, :date_inscription)");

        // Liaison des paramètres avec les valeurs de l'objet Emprunteur
        $stmt->bindValue(":nom", $emprunteur->getNom());
        $stmt->bindValue(":prenom", $emprunteur->getPrenom());
        $stmt->bindValue(":date_naissance", $emprunteur->getDateNaissance());
        $stmt->bindValue(":adresse", $emprunteur->getAdresse());
        $stmt->bindValue(":numero_telephone", $emprunteur->getNumeroTelephone());
        $stmt->bindValue(":email", $emprunteur->getEmail());
        $stmt->bindValue(":date_inscription", $emprunteur->getDateInscription());

        // Vérification si la méthode prepare() a renvoyé un objet PDOStatement valide
        if ($stmt !== false) {
            // Exécution de la requête avec gestion des erreurs
            $this->executeTryCatch($stmt);
        } else {
            // En cas d'erreur, affiche le message d'erreur et termine le script
            die('Erreur : échec de la préparation de la requête');
        }
    }

    // Méthode pour mettre à jour un emprunteur existant
    public function update(Emprunteur $emprunteur)
    {
        // Préparation de la requête UPDATE avec une clause WHERE
        $stmt = $this->connexion->prepare("UPDATE Emprunteur SET nom = :nom, prenom = :prenom, date_naissance = :date_naissance, adresse = :adresse, numero_telephone = :numero_telephone, email = :email, date_inscription = :date_inscription WHERE id_emprunteur = :id_emprunteur");
        // Liaison des paramètres avec les valeurs de l'objet Emprunteur et l'identifiant
        $stmt->bindValue(":id_emprunteur", $emprunteur->getIdEmprunteur(), PDO::PARAM_INT);
        $stmt->bindValue(":nom", $emprunteur->getNom());
        $stmt->bindValue(":prenom", $emprunteur->getPrenom());
        $stmt->bindValue(":date_naissance", $emprunteur->getDateNaissance());
        $stmt->bindValue(":adresse", $emprunteur->getAdresse());
        $stmt->bindValue(":numero_telephone", $emprunteur->getNumeroTelephone());
        $stmt->bindValue(":email", $emprunteur->getEmail());
        $stmt->bindValue(":date_inscription", $emprunteur->getDateInscription());

        // Vérification si la méthode prepare() a renvoyé un objet PDOStatement valide
        if ($stmt !== false) {
            // Exécution de la requête avec gestion des erreurs
            $this->executeTryCatch($stmt);
        } else {
            // En cas d'erreur, affiche le message d'erreur et termine le script
            die('Erreur : échec de la préparation de la requête');
        }
    }

    // Méthode pour supprimer un emprunteur
    public function delete(int $id)
    {
        // Préparation de la requête DELETE avec une clause WHERE
        $stmt = $this->connexion->prepare("DELETE FROM Emprunteur WHERE id_emprunteur = :id_emprunteur");
        // Liaison du paramètre
        $stmt->bindParam(":id_emprunteur", $id, PDO::PARAM_INT);

        // Vérification si la méthode prepare() a renvoyé un objet PDOStatement valide
        if ($stmt !== false) {
            // Exécution de la requête avec gestion des erreurs
            $this->executeTryCatch($stmt);
        } else {
            // En cas d'erreur, affiche le message d'erreur et termine le script
            die('Erreur : échec de la préparation de la requête');
        }
    }

    // Méthode privée pour exécuter une requête avec gestion des erreurs
    private function executeTryCatch(\PDOStatement $stmt)
    {
        try {
            $stmt->execute();
        } catch (Exception $e) {
            // En cas d'erreur, affiche le message d'erreur et termine le script
            die('Erreur : ' . $e->getMessage());
        }
        // Ferme le curseur, permettant à la requête d'être de nouveau exécutée
        $stmt->closeCursor();
    }

    // Méthode pour récupérer tous les emprunteurs pour les livres
    public function getEmprunteursList()
    {
        $emprunteurs = $this->findAll();
        $emprunteursList = [];
        foreach ($emprunteurs as $emprunteur) {
            $emprunteursList[$emprunteur->getIdEmprunteur()] = $emprunteur->getPrenom() . ' ' . $emprunteur->getNom();
        }
        return $emprunteursList;
    }

    // Méthode pour récupérer les emprunts en cours de l'emprunteur spécifique
    public function getEmpruntsEnCoursByEmprunteurId($id)
    {
        // Récupération des emprunts en cours de l'emprunteur spécifique
        $sql = "SELECT e.*, l.*, ee.*
            FROM Emprunt e
            INNER JOIN Livre l ON e.id_livre = l.id_livre
            INNER JOIN Emprunteur ee ON e.id_emprunteur = ee.id_emprunteur
            WHERE e.id_emprunteur = :id_emprunteur
            AND e.date_retour IS NULL";

        $stmt = $this->connexion->prepare($sql);
        $stmt->bindValue(':id_emprunteur', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Récupération des résultats de la requête
        $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        var_dump($emprunts);

        // Retour des emprunts en cours avec les informations sur les livres et les emprunteurs
        return $emprunts;
    }
}
