<?php

namespace App\Models;

use PDO;
use Exception;
use App\Core\DbConnect;
use App\Entities\Emprunt;

class EmpruntModel extends DbConnect
{
    // Méthode pour vérifier si un livre est déjà emprunté
    public function isLivreEmprunte($idLivre)
    {
        $sql = "SELECT COUNT(*) FROM Emprunt WHERE id_livre = :id_livre AND date_retour_effectif IS NULL";
        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':id_livre', $idLivre, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    // Méthode pour récupérer tous les emprunts
    public function findAll()
    {
        // Construction de la requête SELECT avec une jointure entre les tables Emprunt, Livre et Emprunteur
        $this->request = "SELECT Emprunt.id_emprunt, Emprunt.id_emprunteur, Emprunt.id_livre, Emprunt.date_emprunt, Emprunt.date_retour_prevue, Livre.titre AS titre_livre, Emprunteur.nom AS nom_emprunteur, Emprunteur.prenom AS prenom_emprunteur FROM Emprunt JOIN Livre ON Emprunt.id_livre = Livre.id_livre JOIN Emprunteur ON Emprunt.id_emprunteur = Emprunteur.id_emprunteur";
        // Exécution de la requête
        $result = $this->connection->query($this->request);
        // Récupération des résultats dans un tableau associatif
        $list = $result->fetchAll(PDO::FETCH_CLASS, 'App\Entities\Emprunt');
        // Retourne le tableau de résultats
        return $list;
    }
    
    // Méthode pour récupérer un emprunt par son identifiant
    public function find(int $id)
    {
        $this->request = $this->connection->prepare("SELECT * FROM Emprunt WHERE id_emprunt = :id_emprunt");
        $this->request->bindParam(":id_emprunt", $id, PDO::PARAM_INT);
        $this->request->execute();
        return $this->request->fetch(PDO::FETCH_ASSOC); // Retourne un tableau associatif
    }

    // Méthode pour créer un nouvel emprunt
    public function create(Emprunt $emprunt)
    {
        $this->request = $this->connection->prepare("INSERT INTO Emprunt VALUES (NULL, :id_emprunteur, :id_livre, :date_emprunt, :date_retour_prevue)");
        $this->request->bindValue(":id_emprunteur", $emprunt->getIdEmprunteur()); 
        $this->request->bindValue(":id_livre", $emprunt->getIdLivre()); 
        $this->request->bindValue(":date_emprunt", $emprunt->getDateEmprunt()); 
        $this->request->bindValue(":date_retour_prevue", $emprunt->getDateRetourPrevue()); 
        $this->executeTryCatch();
    }

    // Méthode pour mettre à jour un emprunt existant
    public function update(int $id, Emprunt $emprunt)
    {
        $this->request = $this->connection->prepare("UPDATE Emprunt SET id_emprunteur = :id_emprunteur, id_livre = :id_livre, date_emprunt = :date_emprunt, date_retour_prevue = :date_retour_prevue WHERE id_emprunt = :id_emprunt");
        $this->request->bindValue(":id_emprunt", $id, PDO::PARAM_INT);
        $this->request->bindValue(":id_emprunteur", $emprunt->getIdEmprunteur()); // Mis à jour
        $this->request->bindValue(":id_livre", $emprunt->getIdLivre()); // Mis à jour
        $this->request->bindValue(":date_emprunt", $emprunt->getDateEmprunt()); // Mis à jour
        $this->request->bindValue(":date_retour_prevue", $emprunt->getDateRetourPrevue()); // Mis à jour
        $this->executeTryCatch();
    }

    // Méthode pour supprimer un emprunt
    public function delete(int $id)
    {
        $this->request = $this->connection->prepare("DELETE FROM Emprunt WHERE id_emprunt = :id_emprunt");
        $this->request->bindParam(":id_emprunt", $id, PDO::PARAM_INT);
        $this->executeTryCatch();
    }

    // Méthode privée pour exécuter une requête avec gestion des erreurs
    private function executeTryCatch()
    {
        try {
            $this->request->execute();
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
        $this->request->closeCursor();
    }

    public function findByLivreId($id_livre)
    {
        $this->request = $this->connection->prepare("SELECT * FROM Emprunt WHERE id_livre = :id_livre");
        $this->request->bindParam(":id_livre", $id_livre, PDO::PARAM_INT);
        $this->request->execute();
        $empruntData = $this->request->fetch(PDO::FETCH_ASSOC);

        if ($empruntData) {
            return new Emprunt($empruntData);
        }
        return null;
    }

    public function getEmpruntsEnCoursByEmprunteurId($id_emprunteur)
    {
        $sql = "SELECT e.*, l.titre, l.auteur, ee.nom AS emprunteur_nom, ee.prenom AS emprunteur_prenom
            FROM Emprunt e
            INNER JOIN Livre l ON e.id_livre = l.id_livre
            INNER JOIN Emprunteur ee ON e.id_emprunteur = ee.id_emprunteur
            WHERE e.id_emprunteur = :id_emprunteur
            AND e.date_retour_effectif IS NULL";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(":id_emprunteur", $id_emprunteur, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
