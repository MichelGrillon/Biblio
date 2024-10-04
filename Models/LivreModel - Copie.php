<?php

namespace App\Models;

use PDO;
use Exception;
use App\Core\DbConnect;
use App\Entities\Livre;

class LivreModel extends DbConnect
{
    // Méthode pour récupérer tous les livres
    public function findAll()
    {
        // Construction de la requête SELECT
        $this->request = "SELECT * FROM Livre";
        // Exécution de la requête
        $result = $this->connection->query($this->request);
        // Récupération des résultats dans un tableau associatif
        $list = $result->fetchAll();
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
        $this->request = $this->connection->prepare("INSERT INTO Livre (titre, auteur, genre, isbn, date_emprunt, date_retour_prevue, id_emprunteur) VALUES (:titre, :auteur, :genre, :isbn, :date_emprunt, :date_retour_prevue, :id_emprunteur)");
        // Liaison des paramètres avec les valeurs de l'objet CreationLivre
        $this->request->bindValue(":titre", $livre->getTitre());
        $this->request->bindValue(":auteur", $livre->getAuteur());
        $this->request->bindValue(":genre", $livre->getGenre());
        $this->request->bindValue(":isbn", $livre->getIsbn());
        $this->request->bindValue(":date_emprunt", $livre->getDate_emprunt());
        $this->request->bindValue(":date_retour_prevue", $livre->getDate_retour_prevue());
        $this->request->bindValue(":id_emprunteur", $livre->getId_emprunteur());

        $this->executeTryCatch();

        // Exécution de la requête d'insertion
        $this->request->execute();
    }

    // Méthode pour mettre à jour un livre existant
    public function update(int $id, Livre $livre)
    {
        // Préparation de la requête UPDATE avec une clause WHERE
        $this->request = $this->connection->prepare("UPDATE Livre SET titre = :titre, auteur = :auteur, genre = :genre, isbn = :isbn, date_emprunt = :date_emprunt, date_retour_prevue = :date_retour_prevue, id_emprunteur = :id_emprunteur WHERE id_livre = :id_livre");
        // Liaison des paramètres avec les valeurs de l'objet CreationLivre et l'identifiant
        $this->request->bindValue(":id_livre", $id, PDO::PARAM_INT);
        $this->request->bindValue(":titre", $livre->getTitre());
        $this->request->bindValue(":auteur", $livre->getAuteur());
        $this->request->bindValue(":genre", $livre->getGenre());
        $this->request->bindValue(":isbn", $livre->getIsbn());
        $this->request->bindValue(":date_emprunt", $livre->getDate_emprunt());
        $this->request->bindValue(":date_retour_prevue", $livre->getDate_retour_prevue());
        $this->request->bindValue(":id_emprunteur", $livre->getId_emprunteur());
        // Mise à jour automatique de la disponibilité
        if ($livre->getId_emprunteur() == null) {
            $this->request->bindValue(":disponibilite", 1, PDO::PARAM_INT);
        } else {
            $this->request->bindValue(":disponibilite", 0, PDO::PARAM_INT);
        }
        // Exécution de la requête avec gestion des erreurs
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
        }
        // Ferme le curseur, permettant à la requête d'être de nouveau exécutée
        $this->request->closeCursor();
    }
}
