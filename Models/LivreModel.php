<?php

namespace App\Models;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use PDO;
use Exception;
use App\Core\DbConnect;
use App\Entities\Livre;
use App\Models\EmpruntModel; 

class LivreModel extends DbConnect
{
    // Méthode pour le calcul de la date de retour d'un livre
    public function afficherDateRetour($idLivre)
    {
        $empruntModel = new EmpruntModel();
        $emprunt = $empruntModel->findByLivreId($idLivre);

        if ($emprunt) {
            // Retourner la date de retour prévue
            return $emprunt['date_retour_prevue'];
        } else {
            // Retourner null ou une autre valeur par défaut si aucun emprunt trouvé
            return null;
        }
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

    public function findAll()
    {
    // Construction de la requête SELECT avec une jointure entre les tables Livre et Emprunteur
    $this->request = "SELECT Livre.id_livre, Livre.titre, Livre.auteur, Livre.genre, Livre.isbn, Livre.date_emprunt, Livre.date_retour_prevue, Emprunteur.nom AS nom_emprunteur, Emprunteur.prenom AS prenom_emprunteur, Emprunteur.id_emprunteur, Livre.disponibilite FROM Livre LEFT JOIN Emprunteur ON Livre.id_emprunteur = Emprunteur.id_emprunteur";
    // Exécution de la requête
    $result = $this->connection->query($this->request);
    // Récupération des résultats dans un tableau associatif
    $list = $result->fetchAll(PDO::FETCH_CLASS, 'App\Entities\Livre');
    // Retourne le tableau de résultats
    return $list;
}


    // Méthode pour récupérer un livre par son identifiant
    public function find(int $id)
    {
        // Préparation de la requête SELECT avec une clause WHERE
        $this->request = $this->connection->prepare("SELECT * FROM Livre WHERE id_livre = :id_livre");
        // Liaison du paramètre
        $this->request->bindParam(":id_livre", $id, PDO::PARAM_INT);
        // Exécution de la requête
        $this->request->execute();
        // Récupération du livre sous forme d'un objet de la classe Livre
        $livre = $this->request->fetchObject('App\Entities\Livre');
        // Retourne le livre
        return $livre;
    }

    // Méthode pour créer un nouveau livre
    public function create(Livre $livre)
    {
        // Préparation de la requête INSERT INTO
        $this->request = $this->connection->prepare("INSERT INTO Livre (titre, auteur, genre, isbn) VALUES (:titre, :auteur, :genre, :isbn)");

        // Liaison des paramètres avec les valeurs de l'objet CreationLivre
        $this->request->bindValue(":titre", $livre->getTitre());
        $this->request->bindValue(":auteur", $livre->getAuteur());
        $this->request->bindValue(":genre", $livre->getGenre());
        $this->request->bindValue(":isbn", $livre->getIsbn());

        // Exécution de la requête avec gestion des erreurs :
        $this->executeTryCatch();
    }

    // Méthode pour mettre à jour un livre existant
    public function update(int $id, Livre $livre)
    {
        // Récupération des valeurs depuis l'objet Livre avec vérifications
        $date_emprunt = $livre->getDateEmprunt() ? $livre->getDateEmprunt() : null;
        $date_retour_prevue = $livre->getDateRetourPrevue() ? $livre->getDateRetourPrevue() : null;
        $id_emprunteur = $livre->getIdEmprunteur() ? $livre->getIdEmprunteur() : null;

        // Préparation de la requête UPDATE
        $this->request = $this->connection->prepare("UPDATE Livre SET titre = :titre, auteur = :auteur, genre = :genre, isbn = :isbn, date_emprunt = :date_emprunt, date_retour_prevue = :date_retour_prevue, id_emprunteur = :id_emprunteur, disponibilite = :disponibilite WHERE id_livre = :id_livre");

        // Liaison des paramètres
        $this->request->bindValue(":id_livre", $id, PDO::PARAM_INT);
        $this->request->bindValue(":titre", $livre->getTitre());
        $this->request->bindValue(":auteur", $livre->getAuteur());
        $this->request->bindValue(":genre", $livre->getGenre());
        $this->request->bindValue(":isbn", $livre->getIsbn());
        $this->request->bindValue(":date_emprunt", $date_emprunt); 
        $this->request->bindValue(":date_retour_prevue", $date_retour_prevue); 
        $this->request->bindValue(":id_emprunteur", $id_emprunteur, PDO::PARAM_INT);

        // Mise à jour de la disponibilité basée sur l'existence d'un emprunteur
        $disponibilite = ($id_emprunteur === null) ? 1 : 0;
        $this->request->bindValue(":disponibilite", $disponibilite, PDO::PARAM_INT);

        // Exécution de la requête
        $this->executeTryCatch();
    }

    // Méthode pour supprimer un livre
    public function delete(int $id)
    {
        // Préparation de la requête DELETE avec une clause WHERE
        $this->request = $this->connection->prepare("DELETE FROM Livre WHERE id_livre = :id_livre");
        // Liaison du paramètre
        $this->request->bindParam(":id_livre", $id, PDO::PARAM_INT);
        // Exécution de la requête avec gestion des erreurs
        $this->executeTryCatch();
    }

    // Méthode privée pour exécuter une requête avec gestion des erreurs
    private function executeTryCatch()
    {
        try {
            $this->request->execute();
        } catch (Exception $e) {
            // En cas d'erreur, affiche le message d'erreur et termine le script
            die('Erreur : ' . $e->getMessage());
            // Lancer une exception peut être capturée ailleurs dans votre application
            // throw new Exception("Erreur de base de données : " . $e->getMessage());
        }
        // Ferme le curseur, permettant à la requête d'être de nouveau exécutée
        $this->request->closeCursor();
    }
}
