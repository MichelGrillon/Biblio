<?php

namespace App\Controllers;

use DateTime;
use App\Core\Form;
use App\Core\connect;
use App\Entities\Emprunteur; // Mise à jour de l'importation
use App\Models\EmprunteurModel;
use App\Models\EmpruntModel;
use App\Models\LivreModel;
use PDO;
use Exception;

// Vérification si la session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class EmprunteurController extends Controller
{
    // Méthode pour afficher la liste des emprunteurs
    public function index()
    {
        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }
        // On instancie la classe EmprunteurModel
        $emprunteurs = new EmprunteurModel();

        // On stocke dans une variable le retour de la méthode findAll
        $list = $emprunteurs->findAll();
        $this->render('emprunteurs/index', ['list' => $list]);
    }

    // Méthode pour ajouter un emprunteur
    public function addEmprunteur()
    {
        // Générer un token CSRF
        $csrfToken = bin2hex(random_bytes(32));

        // Stocker le token CSRF dans la session de l'utilisateur
        $_SESSION['csrf_token'] = $csrfToken;

        // Contrôle si les champs du formulaire sont remplis
        if (Form::validatePost($_POST, ['nom', 'prenom', 'date_naissance', 'adresse', 'numero_telephone', 'email', 'date_inscription'])) {

            // Conversion du format de la date de naissance
            $date_naissance = DateTime::createFromFormat('Y-m-d', $_POST['date_naissance'])->format('Y-m-d');

            // Instanciation de l'entité "Emprunteur"
            $emprunteur = new Emprunteur();

            // Hydratation de l'entité avec les données du formulaire
            $emprunteur->setNom($_POST['nom']);
            $emprunteur->setPrenom($_POST['prenom']);
            $emprunteur->setDateNaissance($date_naissance);
            $emprunteur->setAdresse($_POST['adresse']);
            $emprunteur->setNumeroTelephone($_POST['numero_telephone']);
            $emprunteur->setEmail($_POST['email']);
            $emprunteur->setDateInscription($_POST['date_inscription']);

            // Instanciation du modèle "emprunteur" pour la création
            $model = new EmprunteurModel();
            $model->create($emprunteur);

            // Redirection vers la liste des emprunteurs
            header("Location:index.php?controller=emprunteur&action=index");
            exit();
        } else {
            // Affichage d'un message d'erreur si le formulaire n'a pas été correctement rempli
            $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
        }

        // Construction du formulaire d'ajout
        $form = new Form();

        $form->startForm(
            "#",
            "POST"
        );
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken]);
        $form->addLabel("nom", "Nom", ["class" => "form-label"]);
        $form->addInput("text", "nom", ["id" => "nom", "class" => "form-control", "placeholder" => "Nom"]);
        $form->addLabel("prenom", "Prénom", ["class" => "form-label"]);
        $form->addInput("text", "prenom", [
            "id" => "prenom", "class" => "form-control", "placeholder" => "Prénom"
        ]);
        $form->addLabel("date_naissance", "Date de naissance", ["class" => "form-label"]);
        $form->addInput("date", "date_naissance", ["id" => "date_naissance", "class" => "form-control"]);
        $form->addLabel("adresse", "Adresse", ["class" => "form-label"]);
        $form->addTextarea("adresse", "adresse de l'emprunteur", ["id" => "adresse", "class" => "form-control", "rows" => 5]);
        $form->addLabel("numero_telephone", "Numéro de téléphone", ["class" => "form-label"]);
        $form->addInput("text", "numero_telephone", ["id" => "numero_telephone", "class" => "form-control", "placeholder" => "Numéro de téléphone"]);
        $form->addLabel("email", "Email", ["class" => "form-label"]);
        $form->addInput("email", "email", [
            "id" => "email", "class" => "form-control", "placeholder" => "Email"
        ]);
        $form->addLabel("date_inscription", "Date d'inscription", ["class" => "form-label"]);
        $form->addInput("date", "date_inscription", ["id" => "date_inscription", "class" => "form-control"]);
        $form->addInput("submit", "addEmprunteur", ["value" => "Ajouter", "class" => "btn btn-primary"]);
        $form->endForm();

        // Ajout du token CSRF au formulaire
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken, "hidden" => ""]);

        // Envoi du formulaire dans la vue addEmprunteur.php
        $this->render('emprunteurs/addEmprunteur', ["addForm" => $form->getFormElements(), "erreur" => $erreur]);
    }

    // Méthode pour afficher les détails d'un emprunteur spécifique
    public function showEmprunteur($id)
    {
        // Convertir l'identifiant en entier
        $id = intval($id);

        // Instanciation de la classe EmprunteurModel
        $emprunteurModel = new EmprunteurModel();

        // Récupération de l'emprunteur spécifique à partir de la base de données
        $emprunteur = $emprunteurModel->find($id);

        // Récupération des emprunts en cours de l'emprunteur spécifique
        $empruntModel = new EmpruntModel();

        $emprunts = $empruntModel->getEmpruntsEnCoursByEmprunteurId($id);

        // Transmission de la variable $emprunteur à la vue en tant que variable locale
        $data = ['emprunteur' => $emprunteur, 'emprunts' => $emprunts];

        // Rendu de la vue avec les données fournies
        $this->render('emprunteurs/showEmprunteur', $data);
    }

    public function showAction(int $id)
    {
        $model = new EmprunteurModel();
        $emprunteur = $model->find($id);

        if (!$emprunteur) {
            throw new Exception("Emprunteur non trouvé");
        }

        $emprunts = $model->getEmpruntsEnCoursByEmprunteurId($id);

        // Ajout de débogage
        var_dump($emprunteur);
        var_dump($emprunts);

        return $this->render('showEmprunteur', [
            'emprunteur' => $emprunteur,
            'emprunts' => $emprunts
        ]);
    }

    public function getEmpruntsEnCoursByEmprunteurId($id)
    {
        // Instanciation du modèle EmpruntModel
        $empruntModel = new EmpruntModel();

        // Instanciation de la classe Connect pour accéder à la base de données
        $connect = new Connect();

        // Récupération des emprunts en cours de l'emprunteur spécifique
        $sql = "SELECT e.*, l.titre AS titre_livre
            FROM Emprunt e
            INNER JOIN Livre l ON e.id_livre = l.id_livre
            WHERE e.id_emprunteur = :id_emprunteur
            AND e.date_retour IS NULL";

        $stmt = $connect->getConnection()->prepare($sql);
        $stmt->bindValue(':id_emprunteur', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Récupération des résultats de la requête
        $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retour des emprunts en cours avec les informations sur les livres empruntés
        return $emprunts;
    }

    // Méthode pour la mise à jour d'un emprunteur
    public function updateEmprunteur($id)
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

        // Contrôle si les champs du formulaire sont remplis
        if (Form::validatePost($_POST, ['nom', 'prenom', 'date_naissance', 'adresse', 'numero_telephone', 'email', 'date_inscription'])) {

            // Instanciation de l'entité "Emprunteur"
            $emprunteur = new Emprunteur();

            // Hydratation de l'entité avec les données du formulaire
            $emprunteur->setIdemprunteur($id);
            $emprunteur->setNom($_POST['nom']);
            $emprunteur->setPrenom($_POST['prenom']);
            $emprunteur->setDateNaissance($_POST['date_naissance']);
            $emprunteur->setAdresse($_POST['adresse']);
            $emprunteur->setNumeroTelephone($_POST['numero_telephone']);
            $emprunteur->setEmail($_POST['email']);
            $emprunteur->setDateInscription($_POST['date_inscription']);

            // Instanciation du modèle "emprunteur" pour la mise à jour
            $emprunteurs = new EmprunteurModel();
            $emprunteurs->update($emprunteur);

            // Redirection vers la liste des emprunteurs
            header("Location:index.php?controller=emprunteur&action=index");
            exit();
        } else {
            // Affichage d'un message d'erreur si le formulaire n'a pas été correctement rempli
            $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
        }

        // Instanciation du modèle EmprunteurModel pour récupérer les informations de l'emprunteur
        $emprunteurs = new EmprunteurModel();
        $emprunteur = $emprunteurs->find($id);

        // Construction du formulaire de mise à jour
        $form = new Form();

        $form->startForm("#", "POST");
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken]);
        $form->addLabel("nom", "Nom", ["class" => "form-label"]);
        $form->addInput("text", "nom", ["id" => "nom", "class" => "form-control", "placeholder" => "Nom", "value" => htmlspecialchars($emprunteur->getNom())]);
        $form->addLabel("prenom", "Prénom", ["class" => "form-label"]);
        $form->addInput("text", "prenom", ["id" => "prenom", "class" => "form-control", "placeholder" => "Prénom", "value" => htmlspecialchars($emprunteur->getPrenom())]);
        $form->addLabel("date_naissance", "Date de naissance", ["class" => "form-label"]);
        $form->addInput("date", "date_naissance", ["id" => "date_naissance", "class" => "form-control", "value" => htmlspecialchars($emprunteur->getDateNaissance())]);
        $form->addLabel("adresse", "Adresse", ["class" => "form-label"]);
        $form->addTextarea("adresse", "adresse de l'emprunteur", ["id" => "adresse", "class" => "form-control", "rows" => 5, "value" => htmlspecialchars($emprunteur->getAdresse())]);
        $form->addLabel("numero_telephone", "Numéro de téléphone", ["class" => "form-label"]);
        $form->addInput("text", "numero_telephone", ["id" => "numero_telephone", "class" => "form-control", "placeholder" => "Numéro de téléphone", "value" => htmlspecialchars($emprunteur->getNumeroTelephone())]);
        $form->addLabel("email", "Email", ["class" => "form-label"]);
        $form->addInput("email", "email", ["id" => "email", "class" => "form-control", "placeholder" => "Email", "value" => htmlspecialchars($emprunteur->getEmail())]);
        $form->addLabel("date_inscription", "Date d'inscription", ["class" => "form-label"]);
        $form->addInput("date", "date_inscription", ["id" => "date_inscription", "class" => "form-control", "value" => htmlspecialchars($emprunteur->getDateInscription())]);
        $form->addInput("submit", "update", ["value" => "Modifier", "class" => "btn btn-primary"]);
        $form->endForm();

        // Ajout du token CSRF au formulaire
        $form->addInput("hidden", "csrf_token", ["value" => $csrfToken, "hidden" => ""]);

        // Envoi du formulaire dans la vue update.php
        $this->render('emprunteurs/updateEmprunteur', ["updateForm" => $form->getFormElements(), "erreur" => $erreur]);
    }


    // Méthode pour supprimer un emprunteur
    public function deleteEmprunteur($id)
    {

        // Vérification du jeton CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            // Gérer l'erreur CSRF ici
            // Par exemple, afficher un message d'erreur et rediriger l'utilisateur
            $error_message = "Erreur CSRF : Token CSRF invalide.";
            $_SESSION['error'] = $error_message;
            header("Location: index.php?controller=emprunteur&action=index");
            exit();
        }
        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }
        // Convertir l'identifiant en entier
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

        // On récupère l'emprunteur avec la méthode find()
        $emprunteurs = new EmprunteurModel();
        $emprunteur = $emprunteurs->find($id);

        // Vérifie si l'emprunteur a été trouvé
        if (!$emprunteur) {
            // Gérer l'erreur ici, par exemple, rediriger vers une page d'erreur
            $error_message = "L'emprunteur avec l'identifiant $id n'a pas été trouvé.";
            $_SESSION['error'] = $error_message;
            header("Location: index.php?controller=emprunteur&action=index");
            exit();
        }

        // Logique de suppression si l'utilisateur confirme la suppression
        if (isset($_POST['confirm'])) {
            // On instancie la classe EmprunteurModel pour exécuter la suppression avec la méthode delete()
            // en récupérant l'id de l'emprunteur du lien
            $emprunteurs = new EmprunteurModel();
            $emprunteurs->delete($id);
            // On redirige l'utilisateur vers la liste des emprunteurs
            header("Location:index.php?controller=emprunteur&action=index");
            exit();
        } elseif (isset($_POST['cancel'])) {
            // Redirection si l'utilisateur annule la suppression
            // On redirige l'utilisateur vers la liste des emprunteurs
            header("Location:index.php?controller=emprunteur&action=index");
            exit();
        }

        // On renvoie vers la vue l'emprunteur sélectionné avec la variable $emprunteur définie
        $this->render('emprunteurs/deleteEmprunteur', ["emprunteur" => $emprunteur]);
    }
}
