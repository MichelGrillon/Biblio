<?php

namespace App\Controllers;

use App\Core\Form;
use App\Entities\Livre;
use App\Models\LivreModel;
use App\Models\EmprunteurModel;
use App\Models\EmpruntModel;

// Vérification si la session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class LivreController extends Controller
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

    // Méthode pour afficher la liste des livres
    public function index()
    {
        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }
        // On instancie la classe LivreModel
        $livres = new LivreModel();

        // On stocke dans une variable le retour de la méthode findAll
        $list = $livres->findAll();
        $this->render('livres/index', ['list' => $list]);
    }

    // Méthode pour ajouter un livre
    public function addLivre()
    {
        // Initialisation de la variable d'erreur
        $erreur = "";

        // Générer un token CSRF
        $csrfToken = bin2hex(random_bytes(32));

        // Stocker le token CSRF dans la session de l'utilisateur
        $_SESSION['csrf_token'] = $csrfToken;

        // Exemple de vérification d'authentification
        if (isset($_SESSION['user_id'])) {
            session_regenerate_id(); // Régénérer l'identifiant de session
        }

        // Instanciation du modèle EmprunteurModel pour récupérer la liste des emprunteurs
        $emprunteursModel = new EmprunteurModel();
        $emprunteursList = $emprunteursModel->getEmprunteursList();

        // Instanciation de l'entité "CreationLivre"
        $livre = new Livre();

        // Contrôle si le formulaire est soumis
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            // Contrôle si les champs du formulaire sont remplis
            if ($_SERVER["REQUEST_METHOD"] === "POST" && Form::validatePost($_POST, ['titre', 'auteur', 'genre', 'isbn'])) {
                if (empty($_POST['date_emprunt']) && empty($_POST['id_emprunteur'])) {
                    // Le livre n'est pas emprunté, on peut l'ajouter sans date d'emprunt ni d'emprunteur
                    $livre->setDateEmprunt(null);
                    $livre->setDateRetourPrevue(null);
                    $livre->setIdEmprunteur(null);
                } elseif (!empty($_POST['date_emprunt']) && !empty($_POST['id_emprunteur'])) {
                    // Le livre est emprunté, on vérifie que les dates et l'emprunteur sont valides
                    $date_emprunt = \DateTime::createFromFormat('d/m/Y', $_POST['date_emprunt']);
                    $date_retour_prevue = \DateTime::createFromFormat('d/m/Y', $_POST['date_retour_prevue']);

                    if ($date_emprunt && $date_retour_prevue && $date_emprunt < $date_retour_prevue) {
                        $livre->setDateEmprunt($date_emprunt->format('Y-m-d'));
                        $livre->setDateRetourPrevue($date_retour_prevue->format('Y-m-d'));
                        $livre->setIdEmprunteur($_POST['id_emprunteur']);

                        // Si le livre est rendu avant la date prévue, mettre à jour la date de retour prévue avec la date actuelle
                        if ($date_retour_prevue > new \DateTime()) {
                            $livre->setDateRetourPrevue((new \DateTime())->format('Y-m-d'));
                        }
                    } else {
                        $erreur = "Les dates d'emprunt et de retour prévue sont invalides ou l'emprunteur n'a pas été sélectionné.";
                    }
                } else {
                    $erreur = "Vous devez sélectionner un emprunteur et une date d'emprunt, soit choisir 'Aucun' pour l'emprunteur.";
                }

                if (empty($erreur)) {
                    // Hydratation de l'entité avec les données du formulaire
                    $livre->setTitre($_POST['titre']);
                    $livre->setAuteur($_POST['auteur']);
                    $livre->setGenre($_POST['genre']);
                    $livre->setIsbn($_POST['isbn']);

                    // Instanciation du modèle "livre" pour la création
                    $livres = new LivreModel();

                    // Appel de la méthode "create" du modèle "livre"
                    if (!$livres->create($livre)) {
                        $erreur = "Une erreur s'est produite lors de l'ajout du livre.";
                    } else {
                        // Redirection vers la liste des livres
                        header("Location:index.php?controller=livre&action=index");
                        exit();
                    }
                } else {
                    // Affichage d'un message d'erreur si le formulaire n'a pas été correctement rempli
                    $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
                }
            }
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
        $form->addLabel("date_emprunt", "Date d'emprunt", ["class" => "form-label"]);
        $form->addInput("date", "date_emprunt", ["id" => "date_emprunt", "class" => "form-control"]);
        $form->addLabel("date_retour_prevue", "Date de retour prévue", ["class" => "form-label"]);
        $form->addInput("text", "date_retour_prevue", ["id" => "date_retour_prevue", "class" => "form-control"]);
        $form->addLabel("id_emprunteur", "ID de l'emprunteur", ["class" => "form-label"]);
        $form->addSelect("id_emprunteur", ["" => "Aucun"] + $emprunteursList, ["class" => "form-control"]);
        $form->addInput("submit", "add", ["value" => "Ajouter", "class" => "btn btn-primary"]);
        $form->endForm();

        // Envoi du formulaire dans la vue addLivre.php
        $this->render('livres/addLivre', ["addForm" => $form->getFormElements(), "erreur" => $erreur]);
    }

    // Méthode pour afficher les détails d'un livre
    public function showLivre()
    {
        // Récupérer l'identifiant du livre depuis l'URL
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        // Si l'identifiant est nul, afficher un message d'erreur
        if ($id === 0) {
            echo "Identifiant du livre invalide.";
            return;
        }

        // On instancie la classe LivreModel
        $livres = new LivreModel();

        // On récupère les informations du livre avec la méthode find()
        $livre = $livres->find($id);

        // Vérifier si le livre existe
        if (!$livre) {
            // Gérer le cas où le livre n'existe pas
            // Par exemple, afficher un message d'erreur et rediriger l'utilisateur
            echo "Le livre avec l'identifiant $id n'existe pas.";
            return;
        }

        // Appel de la méthode pour récupérer la date de retour prévue
        $dateRetourPrevue = $this->afficherDateRetour($id);

        // Afficher les détails du livre et la date de retour prévue
        $this->render('livres/showLivre', ['livre' => $livre, 'dateRetourPrevue' => $dateRetourPrevue]);
    }

    // Méthode pour la mise à jour d'un livre
    public function updateLivre($id)
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

        // Appel de la méthode pour récupérer la date de retour prévue
        $dateRetourPrevue = $this->afficherDateRetour($id);

        // Vérifier si une date de retour prévue a été retournée
        if ($dateRetourPrevue !== null) {
            // Utiliser la date de retour prévue
        } else {
            // Gérer le cas où aucune date de retour prévue n'a été trouvée
        }

        // Contrôle si les champs du formulaire sont remplis
        if (Form::validatePost($_POST, ['titre', 'auteur', 'genre', 'isbn', 'date_emprunt', 'id_emprunteur'])) {

            // Instanciation de l'entité "Livre"
            $livre = new Livre();

            // Hydratation de l'entité avec les données du formulaire
            $livre->setTitre($_POST['titre']);
            $livre->setAuteur($_POST['auteur']);
            $livre->setGenre($_POST['genre']);
            $livre->setIsbn($_POST['isbn']);
            $livre->setDateEmprunt($_POST['date_emprunt']);

            // Calcul de la date de retour prévue en JavaScript
            $date_retour_prevue = $_POST['date_retour_prevue'];
            $livre->setDateRetourPrevue($date_retour_prevue);

            $livre->setIdEmprunteur($_POST['id_emprunteur']);

            // Instanciation du modèle "livre" pour la mise à jour
            $livres = new LivreModel();
            $livres->update($id, $livre);

            // Redirection vers la liste des livres
            header("Location:index.php?controller=livre&action=index");
            exit();
        } else {
            // Affichage d'un message d'erreur si le formulaire n'a pas été correctement rempli
            $erreur = !empty($_POST) ? "Le formulaire n'a pas été correctement rempli" : "";
        }

        // Instanciation du modèle LivreModel pour récupérer les informations du livre
        $livres = new LivreModel();
        $livre = $livres->find($id);

        // Instanciation du modèle EmprunteurModel pour récupérer la liste des emprunteurs
        $emprunteursModel = new EmprunteurModel();
        $emprunteursList = $emprunteursModel->getEmprunteursList();

        // Construction du formulaire de mise à jour
        $updateForm = new Form();

        $updateForm->startForm();
        $updateForm->addInput(
            "hidden",
            "csrf_token",
            ["value" => $csrfToken]
        );
        $updateForm->addLabel("titre", "Titre", ["class" => "form-label"]);
        $updateForm->addInput("text", "titre", ["id" => "titre", "class" => "form-control", "placeholder" => "Titre", "value" => htmlspecialchars($livre->getTitre())]);
        $updateForm->addLabel("auteur", "Auteur", ["class" => "form-label"]);
        $updateForm->addInput("text", "auteur", ["id" => "auteur", "class" => "form-control", "placeholder" => "Auteur", "value" => htmlspecialchars($livre->getAuteur())]);
        $updateForm->addLabel("genre", "Genre", ["class" => "form-label"]);
        $updateForm->addInput("text", "genre", ["id" => "genre", "class" => "form-control", "placeholder" => "Genre", "value" => htmlspecialchars($livre->getGenre())]);
        $updateForm->addLabel("isbn", "ISBN", ["class" => "form-label"]);
        $updateForm->addInput("text", "isbn", ["id" => "isbn", "class" => "form-control", "placeholder" => "ISBN", "value" => htmlspecialchars($livre->getIsbn())]);
        $updateForm->addLabel("date_emprunt", "Date d'emprunt", ["class" => "form-label"]);
        $updateForm->addInput("date", "date_emprunt", ["id" => "date_emprunt", "class" => "form-control", "value" => htmlspecialchars($livre->getDate_Emprunt())]);
        $updateForm->addLabel("date_retour_prevue", "Date de retour prévue", ["class" => "form-label"]);
        $updateForm->addInput("text", "date_retour_prevue", ["id" => "date_retour_prevue", "class" => "form-control"]);
        $updateForm->addLabel("id_emprunteur", "ID de l'emprunteur", ["class" => "form-label"]);
        $updateForm->addSelect("id_emprunteur", ["" => "Aucun"] + $emprunteursList, ["class" => "form-control"]);
        $updateForm->addInput("submit", "update", ["value" => "Mettre à jour", "class" => "btn btn-primary"]);
        $updateForm->endForm();

        // Ajout du token CSRF au formulaire
        $updateForm->addInput("hidden", "csrf_token", ["value" => $csrfToken, "hidden" => ""]);

        // Envoi du formulaire dans la vue update.php
        $this->render('livres/updateLivre', ["updateForm" => $updateForm, "erreur" => $erreur]);
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
            session_regenerate_id(); // Régénérer l'identifiant de session
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
        if (isset($_POST['true'])) {
            // On instancie la classe LivreModel pour exécuter la suppression avec la méthode delete()
            // en récupérant l'id du livre du lien
            $livres = new LivreModel();
            $livres->delete($id);
            // On redirige l'utilisateur vers la liste des livres
            header("Location:index.php?controller=livre&action=index");
            exit();
        } elseif (isset($_POST['false'])) {
            // Redirection si l'utilisateur annule la suppression
            // On redirige l'utilisateur vers la liste des livres
            header("Location:index.php?controller=livre&action=index");
            exit();
        }

        // On renvoie vers la vue le livre sélectionné avec la variable $livre définie
        $this->render('livres/deleteLivre', ["livre" => $livre]);
    }
}
