<?php

namespace App\Entities;

class Emprunteur
{
    // Propriétés privées représentant les champs de la table "Emprunteur"
    private $id_emprunteur;
    private $nom;
    private $prenom;
    private $date_naissance;
    private $adresse;
    private $numero_telephone;
    private $email;
    private $date_inscription;

    // Getters et setters avec échappement des données pour les champs nécessitant un traitement

    /**
     * Getter pour récupérer la valeur du nom en échappant les caractères spéciaux
     */
    public function getNom()
    {
        return htmlspecialchars($this->nom);
    }

    /**
     * Setter pour définir la valeur du nom
     * @return self
     */
    public function setNom($nom)
    {
        $this->nom = htmlspecialchars($nom);
        return $this;
    }

    /**
     * Getter pour récupérer la valeur du prénom en échappant les caractères spéciaux
     */
    public function getPrenom()
    {
        return htmlspecialchars($this->prenom);
    }

    /**
     * Setter pour définir la valeur du prénom
     * @return self
     */
    public function setPrenom($prenom)
    {
        $this->prenom = htmlspecialchars($prenom);
        return $this;
    }

    /**
     * Getter pour récupérer la valeur de la date de naissance
     */
    public function getDateNaissance()
    {
        return $this->date_naissance;
    }

    /**
     * Setter pour définir la valeur de la date de naissance
     * @return self
     */
    public function setDateNaissance($date_naissance)
    {
        $this->date_naissance = $date_naissance;
        return $this;
    }

    /**
     * Getter pour récupérer la valeur de l'adresse en échappant les caractères spéciaux
     */
    public function getAdresse()
    {
        return htmlspecialchars($this->adresse);
    }

    /**
     * Setter pour définir la valeur de l'adresse
     * @return self
     */
    public function setAdresse($adresse)
    {
        $this->adresse = htmlspecialchars($adresse);
        return $this;
    }

    /**
     * Getter pour récupérer la valeur du numéro de téléphone
     */
    public function getNumeroTelephone()
    {
        return $this->numero_telephone;
    }

    /**
     * Setter pour définir la valeur du numéro de téléphone
     * @return self
     */
    public function setNumeroTelephone($numero_telephone)
    {
        $this->numero_telephone = htmlspecialchars($numero_telephone);
        return $this;
    }

    /**
     * Getter pour récupérer la valeur de l'email en échappant les caractères spéciaux
     */
    public function getEmail()
    {
        return htmlspecialchars($this->email);
    }

    /**
     * Setter pour définir la valeur de l'email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = htmlspecialchars($email);
        return $this;
    }

    /**
     * Getter pour récupérer la valeur de la date d'inscription
     */
    public function getDateInscription()
    {
        return $this->date_inscription;
    }

    /**
     * Setter pour définir la valeur de la date d'inscription
     * @return self
     */
    public function setDateInscription($date_inscription)
    {
        $this->date_inscription = $date_inscription;
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

    // Méthode statique pour hydrater un objet Emprunteur à partir d'un tableau de données
    public static function hydrate(array $data)
    {
        $emprunteur = new Emprunteur();
        $emprunteur->setIdEmprunteur($data['id_emprunteur']);
        $emprunteur->setNom($data['nom']);
        $emprunteur->setPrenom($data['prenom']);
        $emprunteur->setDateNaissance($data['date_naissance']);
        $emprunteur->setAdresse($data['adresse']);
        $emprunteur->setNumeroTelephone($data['numero_telephone']);
        $emprunteur->setEmail($data['email']);
        $emprunteur->setDateInscription($data['date_inscription']);

        return $emprunteur;
    }
}
