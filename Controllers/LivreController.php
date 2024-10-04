<?php

namespace App\Controllers;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Core\Form;
use App\Entities\Livre;
use App\Entities\Emprunt;
use App\Models\LivreModel;
use App\Models\EmprunteurModel;
use App\Models\EmpruntModel;
use PDO;


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class LivreController extends Controller
{
     // Méthode pour le calcul de la date de retour d'un livre
    public function afficherDateRetour($idLivre)
    {
        $empruntModel = new EmpruntModel();
        $emprunt = $empruntModel->findByLivreId(intval($idLivre));

        if ($emprunt) {
            return htmlspecialchars($emprunt->getDateRetourPrevue());
        } else {
            return null;
        }
    }

    // Fonction pour valider le format de date (exemple)
    function validateDate($date, $format = 'Y-m-d')
    {
        $dateTimeObj = \DateTime::createFromFormat($format, $date);
        return $dateTimeObj && $dateTimeObj->format($format) === $date;
    }

    // Méthode pour afficher la liste des livres
    public function index()
    {
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id();
        }

        $livres = new LivreModel();
        $list = $livres->findAll();
        $this->render('livres/index', ['list' => $list]);
    }

    // Méthode pour ajouter un livre
    public function addLivre()
    {
        // Générer un token CSRF
        $csrfToken = bin2hex(random_bytes(32));
        // Stocker le token CSRF dans la session de l'utilisateur
        $_SESSION['csrf_token'] = $csrfToken;

        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            // Régénérer l'identifiant de session
            session_regenerate_id();
        }

        $emprunteursModel = new EmprunteurModel();
        $emprunteursList = $emprunteursModel->getEmprunteursList();
        $livre = new Livre();
        $erreur = "";

        // Contrôle si les champs du formulaire sont remplis
        if (Form::validatePost($_POST, ['titre', 'auteur', 'genre', 'isbn'])) {

            // Hydratation de l'entité avec les données du formulaire
            $livre->setTitre(htmlspecialchars($_POST['titre']));
            $livre->setAuteur(htmlspecialchars($_POST['auteur']));
            $livre->setGenre(htmlspecialchars($_POST['genre']));
            $livre->setIsbn(htmlspecialchars($_POST['isbn']));

            // Instanciation de l'entité "Livre" - Mise à jour du nom de la classe
            $livres = new LivreModel();
            $livres->create($livre);

            // Redirection vers la liste des livres
            header("Location: index.php?controller=livre&action=index");
            exit();
        } else {
            // Affichage d'un message d'erreur si le formulaire n'a pas été correctement rempli
            $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
        }

        // Construction du formulaire d'ajout
        $form = new Form();
        $form->startForm("#", "POST");
        // Ajout du token CSRF au formulaire
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken]);
        $form->addLabel("titre", "Titre", ["class" => "form-label"]);
        $form->addInput("text", "titre", ["id" => "titre", "class" => "form-control", "placeholder" => "Titre"]);
        $form->addLabel("auteur", "Auteur", ["class" => "form-label"]);
        $form->addInput("text", "auteur", ["id" => "auteur", "class" => "form-control", "placeholder" => "Auteur"]);
        $form->addLabel("genre", "Genre", ["class" => "form-label"]);
        $form->addInput("text", "genre", ["id" => "genre", "class" => "form-control", "placeholder" => "Genre"]);
        $form->addLabel("isbn", "ISBN", ["class" => "form-label"]);
        $form->addInput("text", "isbn", ["id" => "isbn", "class" => "form-control", "placeholder" => "ISBN"]);
        $form->addInput("submit", "add", ["value" => "Ajouter", "class" => "btn btn-primary"]);
        $form->endForm();

        // Envoi du formulaire dans la vue addLivre.php
        $this->render('livres/addLivre', ["addForm" => $form->getFormElements(), "erreur" => $erreur]);
    }


    // Méthode pour afficher les détails d'un livre
    public function showLivre()
    {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        if ($id === 0) {
            echo "Identifiant du livre invalide.";
            return;
        }

        $livres = new LivreModel();
        $livre = $livres->find($id);

        if (!$livre) {
            echo "Le livre avec l'identifiant $id n'existe pas.";
            return;
        }

        $dateRetourPrevue = $this->afficherDateRetour($id);
        $this->render('livres/showLivre', ['livre' => $livre, 'dateRetourPrevue' => $dateRetourPrevue]);
    }

    // Méthode pour la mise à jour d'un livre
    public function updateLivre($id)
    {
        // Déclaration de la variable $updateForm
        $updateForm = null;

        // Génération d'un jeton CSRF
        $csrfToken = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $csrfToken;

        // Vérification de l'ID du livre
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Instanciation du modèle Livre
        $livreModel = new LivreModel();

        // Chargement des informations du livre à mettre à jour
        $livre = $livreModel->find($id);

        $date_emprunt = htmlspecialchars($livre->getDateEmprunt(), ENT_QUOTES, 'UTF-8');
        $date_retour_prevue = htmlspecialchars($livre->getDateRetourPrevue(), ENT_QUOTES, 'UTF-8');

        // Initialisation du message d'erreur - de la variable $erreur
        $erreur = "";

        // Initialisation de la variable $id_emprunteur
        $id_emprunteur = isset($_POST['id_emprunteur']) ? intval($_POST['id_emprunteur']) : null;

        // Validation du formulaire
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Récupération des données de formulaire
            $date_emprunt = !empty($_POST['date_emprunt']) ? $_POST['date_emprunt'] : null;
            $date_retour_prevue = !empty($_POST['date_retour_prevue']) ? $_POST['date_retour_prevue'] : null;

            // Nettoyage des données POST
            $_POST = array_map('htmlspecialchars', $_POST);

            // Vérification des champs requis
            if (Form::validatePost($_POST, ['titre', 'auteur', 'genre', 'isbn'])) {

                // Création d'un nouvel objet Livre
                $livre = new Livre();

                // Mise à jour des champs du livre
                $livre->setTitre(htmlspecialchars($_POST['titre']));
                $livre->setAuteur(htmlspecialchars($_POST['auteur']));
                $livre->setGenre(htmlspecialchars($_POST['genre']));
                $livre->setIsbn(htmlspecialchars($_POST['isbn']));

                // Validation des dates si elles ne sont pas vides
                if (!empty($date_emprunt) && !empty($date_retour_prevue)) {
                    if ($this->validateDate($date_emprunt) && $this->validateDate($date_retour_prevue)) {
                        $livre->setDateEmprunt($date_emprunt);
                        $livre->setDateRetourPrevue($date_retour_prevue);
                        $livre->setIdEmprunteur($id_emprunteur);
                    } else {
                        // Gestion des dates invalides
                        $erreur = "Les dates saisies ne sont pas valides. Veuillez saisir des dates au format YYYY-MM-DD.";

                        // Récupération des emprunteurs pour le champ de sélection
                        $emprunteursModel = new EmprunteurModel();
                        $emprunteursList = $emprunteursModel->getEmprunteursList();
                        $updateForm = $this->createUpdateForm($livre, $csrfToken, $emprunteursList, $erreur, $date_emprunt, $date_retour_prevue);

                        // Rendu du formulaire avec les erreurs
                        $this->render('livres/updateLivre', ['livre' => $livre, 'emprunteursList' => $emprunteursList, 'updateForm' => $updateForm, 'erreur' => $erreur, 'date_emprunt' => $date_emprunt, 'date_retour_prevue' => $date_retour_prevue]);
                        // Arrête l'exécution de la méthode
                        return;
                    }
                } else {
                    // Si les dates sont vides, réinitialiser les valeurs dans le livre
                    $livre->setDateEmprunt(null);
                    $livre->setDateRetourPrevue(null);
                }

                // Si le livre est rendu et disponible, les champs de date et d'emprunteur peuvent être vides
                if ($id_emprunteur === null) {
                    $date_emprunt = null;
                    $date_retour_prevue = null;
                }

                // Mise à jour du livre dans la base de données
                try {
                    // Mettre à jour la disponibilité du livre en fonction des dates d'emprunt et de retour prévues
                    if (!empty($date_emprunt) && !empty($date_retour_prevue)) {
                        // Si le livre est emprunté, définissez sa disponibilité sur 0
                        $livre->setDisponibilite(0);
                    } else {
                        // Sinon, définissez-la sur 1
                        $livre->setDisponibilite(1);
                    }

                    // Enregistrement du livre mis à jour dans la base de données
                    $livreModel->update($id, $livre);

                    header("Location: index.php?controller=livre&action=index");
                    exit();
                } catch (\PDOException $e) {
                    $erreur = "Erreur lors de la mise à jour du livre : " . $e->getMessage();
                }
            } else {
                $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
            }
        }

        // Préparation des emprunteurs pour le champ de sélection
        $emprunteursModel = new EmprunteurModel();
        $emprunteursList = $emprunteursModel->getEmprunteursList();

        // Création du formulaire de mise à jour
        $updateForm = $this->createUpdateForm($livre, $csrfToken, $emprunteursList, $erreur, $date_emprunt, $date_retour_prevue);

        // Ajout des valeurs des champs de date d'emprunt et de retour prévue à la vue
        $this->render('livres/updateLivre', ["updateForm" => $updateForm, "erreur" => $erreur, "date_emprunt" => $livre->getDateEmprunt(), "date_retour_prevue" => $livre->getDateRetourPrevue()]);
        //  }
    }
    // Méthode pour créer le formulaire de mise à jour
    private function createUpdateForm($livre, $csrfToken, $emprunteursList, $erreur, $date_emprunt, $date_retour_prevue)
    {
        $updateForm = new Form();
        $updateForm->startForm();

        // Ajout du token CSRF
        $updateForm->addInput("hidden", "csrf_token", ["value" => $csrfToken]);

        $updateForm->addLabel("titre", "Titre", ["class" => "form-label"]);
        $updateForm->addInput("text", "titre", ["id" => "titre", "class" => "form-control", "placeholder" => "Titre", "value" => htmlspecialchars($livre->getTitre())]);
        $updateForm->addLabel("auteur", "Auteur", ["class" => "form-label"]);
        $updateForm->addInput("text", "auteur", ["id" => "auteur", "class" => "form-control", "placeholder" => "Auteur", "value" => htmlspecialchars($livre->getAuteur())]);
        $updateForm->addLabel("genre", "Genre", ["class" => "form-label"]);
        $updateForm->addInput("text", "genre", ["id" => "genre", "class" => "form-control", "placeholder" => "Genre", "value" => htmlspecialchars($livre->getGenre())]);
        $updateForm->addLabel("isbn", "ISBN", ["class" => "form-label"]);
        $updateForm->addInput("text", "isbn", ["id" => "isbn", "class" => "form-control", "placeholder" => "ISBN", "value" => htmlspecialchars($livre->getIsbn())]);

        // Ajout des champs de date d'emprunt et de date de retour prévue
        $updateForm->addLabel("date_emprunt", "Date d'emprunt", ["class" => "form-label"]);
        $updateForm->addInput("date", "date_emprunt", ["id" => "date_emprunt", "class" => "form-control", "value" => htmlspecialchars($date_emprunt)]);
        $updateForm->addLabel("date_retour_prevue", "Date de retour prévue", ["class" => "form-label"]);
        $updateForm->addInput("date", "date_retour_prevue", ["id" => "date_retour_prevue", "class" => "form-control", "value" => htmlspecialchars($date_retour_prevue)]);


        // Ajout du champ pour l'emprunteur avec l'option "néant"
        $updateForm->addLabel("id_emprunteur", "Emprunteur", ["class" => "form-label"]);
        $updateForm->addSelect("id_emprunteur", ['' => 'Néant'] + $emprunteursList, ["class" => "form-select", "value" => htmlspecialchars($livre->getIdEmprunteur())]);

        // Ajout du champ caché pour la disponibilité
        $updateForm->addInput("hidden", "disponibilite", ["id" => "disponibilite", "value" => htmlspecialchars($livre->getDisponibilite(), ENT_QUOTES, 'UTF-8')]);

        // Bouton de soumission
        $updateForm->addInput("submit", "update", ["value" => "Mettre à jour", "class" => "btn btn-primary", "name" => "updateButton1"]);

        $updateForm->endForm();

        return $updateForm;
    }

    // Méthode pour supprimer un livre
    public function deleteLivre($id)
    {
        // Vérification du jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            // Gérer l'erreur CSRF ici
            // Par exemple, afficher un message d'erreur et rediriger l'utilisateur
            $error_message = "Erreur CSRF : Token CSRF invalide.";
            $_SESSION['error'] = $error_message;
            header("Location: index.php?controller=livre&action=index");
            exit();
        }
        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id();
        }
        // Convertir l'identifiant en entier
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        // On récupère le livre avec la méthode find()
        $livres = new LivreModel();
        $livre = $livres->find($id);

        // Vérifie si le livre a été trouvé
        if (!$livre) {
            // Gérer l'erreur ici, par exemple, rediriger vers une page d'erreur
            $error_message = "Le livre avec l'identifiant $id n'a pas été trouvé.";
            $_SESSION['error'] = $error_message;
            header("Location: index.php?controller=livre&action=index");
            exit();
        }
        // Logique de suppression si l'utilisateur confirme la suppression
        if (isset($_POST['confirm'])) {
            // On instancie la classe LivreModel pour exécuter la suppression avec la méthode delete()
            // en récupérant l'id du livre du lien
            $livres = new LivreModel();
            $livres->delete($id);
            // On redirige l'utilisateur vers la liste des livres
            header("Location: index.php?controller=livre&action=index");
            exit();
        } elseif (isset($_POST['cancel'])) {
            // Redirection si l'utilisateur annule la suppression, on redirige l'utilisateur vers la liste des livres
            header("Location: index.php?controller=livre&action=index");
            exit();
        }
        // On renvoie vers la vue le livre sélectionné avec la variable $livre définie
        $this->render('livres/deleteLivre', ["livre" => $livre]);
    }
    // Méthode pour créer un nouvel emprunt
    public function createEmprunt($idEmprunteur, $idLivre, $dateEmprunt, $dateRetourPrevue)
    {
        $empruntModel = new EmpruntModel();

        // Vérifiez si le livre est déjà emprunté
        if ($empruntModel->isLivreEmprunte($idLivre)) {
            // Gérer l'erreur (par exemple, afficher un message d'erreur à l'utilisateur)
            $this->render('livres/createEmprunt', ['error' => 'Le livre est déjà emprunté.']);
            return;
        }

        // Créer un nouvel objet Emprunt
        $emprunt = new Emprunt();
        $emprunt->setIdEmprunteur($idEmprunteur);
        $emprunt->setIdLivre($idLivre);
        $emprunt->setDateEmprunt($dateEmprunt);
        $emprunt->setDateRetourPrevue($dateRetourPrevue);

        // Procéder à l'emprunt
        $empruntModel->create($emprunt);

        // Rediriger ou afficher une vue de succès
        $this->render('emprunts/addEmprunt', ['success' => 'Emprunt créé avec succès.']);
    }
}
