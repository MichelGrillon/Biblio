<?php

namespace App\Controllers;

use App\Core\Form;
use App\Entities\Emprunt;
use App\Models\EmpruntModel;

// Vérification si la session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class EmpruntController extends Controller
{
    // Méthode pour afficher la liste des emprunts
    public function index()
    {
        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }
        // On instancie la classe EmpruntModel
        $emprunts = new EmpruntModel();

        // On stocke dans une variable le retour de la méthode findAll
        $list = $emprunts->findAll();
        $this->render('emprunts/index', ['list' => $list]);
    }

    // Méthode pour ajouter un emprunt
    public function addEmprunt()
    {
        // Générer un token CSRF
        $csrfToken = bin2hex(random_bytes(32));

        // Stocker le token CSRF dans la session de l'utilisateur
        $_SESSION['csrf_token'] = $csrfToken;

        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }

        // Initialisation des variables
        $prenom_emprunteur = ' ';
        $nom_emprunteur = '';
        $erreur = ' ';
        
        // Contrôle si les champs du formulaire sont remplis
        if (Form::validatePost($_POST, ['id_emprunteur', 'id_livre', 'date_emprunt', 'date_retour_prevue'])) {

            // Récupération des valeurs du formulaire
            $prenom_emprunteur = isset($_POST['prenom']) ? $_POST['prenom'] : '';
            $nom_emprunteur = isset($_POST['nom']) ? $_POST['nom'] : '';

            // Instanciation de l'entité "Emprunt"
            $emprunt = new Emprunt();

            // Hydratation de l'entité avec les données du formulaire
            $emprunt->setIdEmprunteur($_POST['id_emprunteur']);
            $emprunt->setPrenomEmprunteur($prenom_emprunteur);
            $emprunt->setNomEmprunteur($nom_emprunteur);
            $emprunt->setIdLivre($_POST['id_livre']);
            $emprunt->setTitre($_POST['titre']);
            $emprunt->setDateEmprunt($_POST['date_emprunt']);
            $emprunt->setDateRetourPrevue($_POST['date_retour_prevue']);

            // Instanciation du modèle "emprunt" pour la création
            $emprunts = new EmpruntModel();
            $emprunts->create($emprunt);

            // Redirection vers la liste des emprunts
            header("Location:index.php?controller=emprunt&action=index");
            exit();
        } else {
            // Affichage d'un message d'erreur si le formulaire n'a pas été correctement rempli
            $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
        }

        // Construction du formulaire d'ajout
        $form = new Form();

        $form->startForm("#", "POST");
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken]);
        $form->addLabel("id_emprunteur", "ID de l'emprunteur", ["class" => "form-label"]);
        $form->addInput("text", "id_emprunteur", ["id" => "id_emprunteur", "class" => "form-control", "placeholder" => "ID de l'emprunteur"]);
        $form->addLabel("prenom", "Prénom", ["class" => "form-label"]);
        $form->addInput("text", "prenom", ["id" => "prenom", "class" => "form-control", "placeholder" => "Prénom", "value" => htmlspecialchars($prenom_emprunteur)]);
        $form->addLabel("nom", "Nom", ["class" => "form-label"]);
        $form->addInput("text", "nom", ["id" => "nom", "class" => "form-control", "placeholder" => "Nom", "value" => htmlspecialchars($nom_emprunteur)]);
        $form->addLabel("id_livre", "ID du livre", ["class" => "form-label"]);
        $form->addInput("text", "id_livre", ["id" => "id_livre", "class" => "form-control", "placeholder" => "ID du livre"]);
        $form->addLabel("date_emprunt", "Date d'emprunt", ["class" => "form-label"]);
        $form->addInput("date", "date_emprunt", ["id" => "date_emprunt", "class" => "form-control"]);
        $form->addLabel("date_retour_prevue", "Date de retour prévue", ["class" => "form-label"]);
        $form->addInput("date", "date_retour_prevue", ["id" => "date_retour_prevue", "class" => "form-control"]);
        $form->addInput("submit", "add", ["value" => "Ajouter", "class" => "btn btn-primary"]);
        $form->endForm();

        // Ajout du token CSRF au formulaire
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken, "hidden" => ""]);

        // Envoi du formulaire dans la vue add.php
        $this->render('emprunts/addEmprunt', ["addForm" => $form->getFormElements(), "erreur" => $erreur]);
    }

    // Méthode pour afficher les détails d'un emprunt
    public function showEmprunt($id)
    {
        // Convertir l'identifiant en entier
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // On instancie la classe EmpruntModel
        $emprunts = new EmpruntModel();

        // On stocke dans une variable le retour de la méthode find()
        $emprunt = $emprunts->find($id);
        $this->render('emprunts/showEmprunt', ['emprunt' => $emprunt]);
    }

    // Méthode pour la mise à jour d'un emprunt
    public function updateEmprunt($id)
    {
        // Générer un token CSRF
        $csrfToken = bin2hex(random_bytes(32));

        // Stocker le token CSRF dans la session de l'utilisateur
        $_SESSION['csrf_token'] = $csrfToken;

        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }

        // Convertir l'identifiant en entier
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Vérification si l'identifiant est valide
        if ($id <= 0) {
            $erreur = "L'identifiant de l'emprunt est invalide.";
            $this->render('emprunts/updateEmprunt', ["updateForm" => [], "erreur" => $erreur]);
            return;
        }

        // Initialisation des variables
        $prenom_emprunteur = '';
        $nom_emprunteur = '';
        $erreur = '';

        // Contrôle si les champs du formulaire sont remplis
        if (Form::validatePost($_POST, ['id_emprunteur', 'id_livre', 'date_emprunt', 'date_retour_prevue'])) {

            // Instanciation de l'entité "Emprunt"
            $emprunt = new Emprunt();

            // Hydratation de l'entité avec les données du formulaire
            $emprunt->setIdEmprunteur($_POST['id_emprunteur']);
            $emprunt->setIdLivre($_POST['id_livre']);
            $emprunt->setDateEmprunt($_POST['date_emprunt']);
            $emprunt->setDateRetourPrevue($_POST['date_retour_prevue']);

            // Instanciation du modèle "emprunt" pour la mise à jour
            $emprunts = new EmpruntModel();
            $emprunts->update($id, $emprunt);

            // Redirection vers la liste des emprunts
            header("Location:index.php?controller=emprunt&action=index");
            exit();
        } else {
            // Affichage d'un message d'erreur si le formulaire n'a pas été correctement rempli
            $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
        }

        // Instanciation du modèle EmpruntModel pour récupérer les informations de l'emprunt
        $emprunts = new EmpruntModel();
        $emprunt = $emprunts->find($id);

        // Vérification si l'emprunt existe
        if (!$emprunt) {
            $erreur = "L'emprunt avec l'identifiant $id n'a pas été trouvé.";
            $this->render('emprunts/updateEmprunt', ["updateForm" => [], "erreur" => $erreur]);
            return;
        }

        // Initialisation des variables avec les valeurs de l'emprunt
        $prenom_emprunteur = $emprunt->getPrenomEmprunteur();
        $nom_emprunteur = $emprunt->getNomEmprunteur();

        // Construction du formulaire de mise à jour
        $form = new Form();

        $form->startForm("#", "POST");
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken]);
        $form->addLabel("id_emprunteur", "ID de l'emprunteur", ["class" => "form-label"]);
        $form->addInput("text", "id_emprunteur", ["id" => "id_emprunteur", "class" => "form-control", "placeholder" => "ID de l'emprunteur", "value" => htmlspecialchars($emprunt->getIdEmprunteur())]);
        $form->addLabel("nom", "Nom", ["class" => "form-label"]);
        $form->addInput("text", "nom", ["id" => "nom", "class" => "form-control", "placeholder" => "Nom", "value" => htmlspecialchars($nom_emprunteur)]);
        $form->addLabel("prenom", "Prénom", ["class" => "form-label"]);
        $form->addInput("text", "prenom", ["id" => "prenom", "class" => "form-control", "placeholder" => "Prénom", "value" => htmlspecialchars($prenom_emprunteur)]);
        $form->addLabel("id_livre", "ID du livre", ["class" => "form-label"]);
        $form->addInput("text", "id_livre", ["id" => "id_livre", "class" => "form-control", "placeholder" => "ID du livre", "value" => htmlspecialchars($emprunt->getIdLivre())]);
        $form->addLabel("titre", "Titre", ["class" => "form-label"]);
        $form->addInput("text", "titre", ["id" => "titre", "class" => "form-control", "placeholder" => "Titre"]);
        $form->addLabel("date_emprunt", "Date d'emprunt", ["class" => "form-label"]);
        $form->addInput("date", "date_emprunt", ["id" => "date_emprunt", "class" => "form-control", "value" => htmlspecialchars($emprunt->getDateEmprunt())]);
        $form->addLabel("date_retour_prevue", "Date de retour prévue", ["class" => "form-label"]);
        $form->addInput("date", "date_retour_prevue", ["id" => "date_retour_prevue", "class" => "form-control", "value" => htmlspecialchars($emprunt->getDateRetourPrevue())]);
        $form->addInput("submit", "update", ["value" => "Modifier", "class" => "btn btn-primary"]);
        $form->endForm();

        // Ajout du token CSRF au formulaire
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken, "hidden" => ""]);

        // Envoi du formulaire dans la vue update.php
        $this->render('emprunts/updateEmprunt', ["updateForm" => $form->getFormElements(), "erreur" => $erreur]);
    }

    // Méthode pour supprimer un emprunt
    public function deleteEmprunt($id)
    {
        // Vérification du jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            // Gérer l'erreur CSRF ici
            // Par exemple, afficher un message d'erreur et rediriger l'utilisateur
            $error_message = "Erreur CSRF : Token CSRF invalide.";
            $_SESSION['error'] = $error_message;
            header("Location: index.php?controller=emprunt&action=index");
            exit();
        }
        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }
        // Convertir l'identifiant en entier
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        // On récupère l'emprunt avec la méthode find()
        $emprunts = new EmpruntModel();
        $emprunt = $emprunts->find($id);

        // Vérifie si l'emprunt a été trouvé
        if (!$emprunt) {
            // Gérer l'erreur ici, par exemple, rediriger vers une page d'erreur
            $error_message = "L'emprunt avec l'identifiant $id n'a pas été trouvé.";
            $_SESSION['error'] = $error_message;
            header("Location: index.php?controller=emprunt&action=index");
            exit();
        }

        // Logique de suppression si l'utilisateur confirme la suppression
        if (isset($_POST['true'])) {
            // On instancie la classe EmpruntModel pour exécuter la suppression avec la méthode delete()
            // en récupérant l'id de l'emprunt du lien
            $emprunts = new EmpruntModel();
            $emprunts->delete($id);
            // On redirige l'utilisateur vers la liste des emprunts
            header("Location:index.php?controller=emprunt&action=index");
            exit();
        } elseif (isset($_POST['false'])) {
            // Redirection si l'utilisateur annule la suppression
            // On redirige l'utilisateur vers la liste des emprunts
            header("Location:index.php?controller=emprunt&action=index");
            exit();
        }

        // On renvoie vers la vue l'emprunt sélectionné avec la variable $emprunt définie
        $this->render('emprunts/deleteEmprunt', ["emprunt" => $emprunt]);
    }
}
