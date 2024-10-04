<?php

namespace App\Entities;

class Emprunt
{
private $id_emprunt;
private $id_emprunteur;
private $nom_emprunteur;
private $prenom_emprunteur;
private $id_livre;
private $titre;
private $date_emprunt;
private $date_retour_prevue;
private $date_retour_effectif; // Nouvelle propriété

public function __construct($data = []) {
$this->id_emprunt = $data['id_emprunt'] ?? null;
$this->id_emprunteur = $data['id_emprunteur'] ?? null;
$this->id_livre = $data['id_livre'] ?? null;
$this->titre = $data['titre'] ?? null;
$this->date_emprunt = $data['date_emprunt'] ?? null;
$this->date_retour_prevue = $data['date_retour_prevue'] ?? null;
$this->date_retour_effectif = $data['date_retour_effectif'] ?? null; // Initialisation
}

public function getIdEmprunt() {
return $this->id_emprunt;
}

public function setIdEmprunt($id_emprunt) {
$this->id_emprunt = $id_emprunt;
return $this;
}

public function getIdEmprunteur() {
return $this->id_emprunteur;
}

public function setIdEmprunteur($id_emprunteur) {
$this->id_emprunteur = $id_emprunteur;
return $this;
}

public function getPrenomEmprunteur()
{
    return $this->prenom_emprunteur;
}

public function setPrenomEmprunteur($prenom_emprunteur)
{
    $this->prenom_emprunteur = $prenom_emprunteur;
    return $this;
}

public function getNomEmprunteur()
{
    return $this->nom_emprunteur;
}

public function setNomEmprunteur($nom_emprunteur)
{
    $this->nom_emprunteur = $nom_emprunteur;
    return $this;
}

public function getIdLivre() {
return $this->id_livre;
}

public function setIdLivre($id_livre) {
$this->id_livre = $id_livre;
return $this;
}

public function getTitre()
{
    return htmlspecialchars($this->titre);
}

public function setTitre($titre)
{
    $this->titre = $titre;
    return $this;
}

public function getDateEmprunt() {
return $this->date_emprunt;
}

public function setDateEmprunt($date_emprunt) {
$this->date_emprunt = $date_emprunt;
return $this;
}

public function getDateRetourPrevue() {
return $this->date_retour_prevue;
}

public function setDateRetourPrevue($date_retour_prevue) {
$this->date_retour_prevue = $date_retour_prevue;
return $this;
}

public function getDateRetourEffectif() { // Getter pour date_retour_effectif
return $this->date_retour_effectif;
}

public function setDateRetourEffectif($date_retour_effectif) { // Setter pour date_retour_effectif
$this->date_retour_effectif = $date_retour_effectif;
return $this;
}
}
