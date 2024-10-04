<?php

namespace App\Entities;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Livre
{
// Propriétés privées représentant les champs de la table "Livre"
private $id_livre;
private $titre;
private $auteur;
private $genre;
private $isbn;
private $date_emprunt = null;
private $date_retour_prevue = null;
private $id_emprunteur = null;
private $disponibilite = null;
private $nom_emprunteur = null;
private $prenom_emprunteur = null;

// Getters et setters avec échappement des données pour le titre et la description

/**
* Getter pour récupérer la valeur du titre en échappant les caractères spéciaux
*/
public function getTitre()
{
return htmlspecialchars($this->titre);
}

/**
* Setter pour définir la valeur du titre
* @return self
*/
public function setTitre($titre)
{
$this->titre = $titre;
return $this;
}

/**
* Getter pour récupérer la valeur de l'auteur en échappant les caractères spéciaux
*/
public function getAuteur()
{
return htmlspecialchars($this->auteur);
}

/**
* Setter pour définir la valeur de l'auteur
* @return self
*/
public function setAuteur($auteur)
{
$this->auteur = $auteur;
return $this;
}

/**
* Getter pour récupérer la valeur du genre en échappant les caractères spéciaux
*/
public function getGenre()
{
return htmlspecialchars($this->genre);
}

/**
* Setter pour définir la valeur du genre
* @return self
*/
public function setGenre($genre)
{
$this->genre = $genre;
return $this;
}

/**
* Getter pour récupérer la valeur de l'ISBN en échappant les caractères spéciaux
*/
public function getIsbn()
{
return htmlspecialchars($this->isbn);
}

/**
* Setter pour définir la valeur de l'ISBN
* @return self
*/
public function setIsbn($isbn)
{
$this->isbn = $isbn;
return $this;
}

/**
* Getter pour récupérer la valeur de la disponibilité
*/
public function getDisponibilite()
{
return $this->disponibilite;
}

/**
* Setter pour définir la valeur de la disponibilité
* @return self
*/
public function setDisponibilite($disponibilite)
{
$this->disponibilite = $disponibilite;
return $this;
}

/**
* Getter pour récupérer la valeur de la date d'emprunt
*/
public function getDateEmprunt()
{
return $this->date_emprunt;
}

/**
* Setter pour définir la valeur de la date d'emprunt
* @return self
*/
public function setDateEmprunt($date_emprunt)
{
$this->date_emprunt = $date_emprunt;
return $this;
}

/**
* Getter pour récupérer la valeur de la date de retour prévue
*/
public function getDateRetourPrevue()
{
return $this->date_retour_prevue;
}

/**
* Setter pour définir la valeur de la date de retour prévue
* @return self
*/
public function setDateRetourPrevue($date_retour_prevue)
{
$this->date_retour_prevue = $date_retour_prevue;
return $this;
}

/**
* Getter pour récupérer la valeur de l'ID de l'emprunteur
*/
public function getIdEmprunteur()
{
return $this->id_emprunteur;
}

/**
* Setter pour définir la valeur de l'ID de l'emprunteur
* @return self
*/
public function setIdEmprunteur($id_emprunteur)
{
$this->id_emprunteur = $id_emprunteur;
return $this;
}

/**
* Getter pour récupérer la valeur de l'ID du livre
*/
public function getIdLivre()
{
return $this->id_livre;
}

/**
* Setter pour définir la valeur de l'ID du livre
* @return self
*/
public function setIdLivre($id_livre)
{
$this->id_livre = $id_livre;
return $this;
}

/**
* Getter pour récupérer la valeur du nom de l'emprunteur
*/
public function getNom()
{
return htmlspecialchars($this->nom_emprunteur);
}

/**
* Setter pour définir la valeur du nom de l'emprunteur
* @return self
*/
public function setNom($nom_emprunteur)
{
$this->nom_emprunteur = $nom_emprunteur;
return $this;
}

/**
* Getter pour récupérer la valeur du prénom de l'emprunteur
*/
public function getPrenom()
{
return htmlspecialchars($this->prenom_emprunteur);
}

/**
* Setter pour définir la valeur du prénom de l'emprunteur
* @return self
*/
public function setPrenom($prenom_emprunteur)
{
$this->prenom_emprunteur = $prenom_emprunteur;
return $this;
}
    
}