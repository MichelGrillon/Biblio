<?php
// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Définition du titre de la page
$title = "Accueil - Bibliothèque du CEFII";

// Contenu dynamique à inclure dans Base.php
$content = '';

if (isset($_GET['login']) && !empty($_GET['login']) && $_GET['login'] == "auth") {

    // Échapper le paramètre login
    $login = htmlspecialchars($_GET['login']);

    // Démarrer la temporisation de sortie
    ob_start();

    // Inclure la vue login.php dans la temporisation
    include_once 'Views/login.php';

    // Récupérer le contenu de la temporisation et nettoyer la mémoire tampon
    $content = ob_get_clean();

    // Contenu de la page d'accueil, on peut définir le contenu ici si nécessaire
} elseif (isset($_GET['page']) && !empty($_GET['page']) && $_GET['page'] == "home" && isset($_SESSION['username']) && !empty($_SESSION['username'])) {
    // Afficher le contenu de la page d'accueil pour un utilisateur connecté, vous pouvez définir le contenu ici si nécessaire
    // Par exemple, vous pouvez inclure un message de bienvenue ou afficher le tableau de bord de l'utilisateur
    $content = "<h1>Bienvenue, " . htmlspecialchars($_SESSION['username']) . "!</h1>";
} else {

    // Démarrer la temporisation de sortie
    ob_start();

    // Afficher le lien pour se connecter si l'utilisateur n'est pas connecté
    if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
        echo '<br>';
        echo '<a href="' . htmlspecialchars("index.php?login=auth") . '" class="btn btn-success">Veuillez vous connecter</a>';
    }

    // Ajouter deux lignes pour séparer les liens
    echo '<br>';
    echo '<br>';

    // Afficher le texte "Pas encore inscrit ?"
    echo '<p>' . htmlspecialchars("Pas encore inscrit ?") . '</p>';

    // Afficher le lien d'inscription en dessous du texte
    echo '<a href="' . htmlspecialchars("Views/register.php") . '" class="btn btn-primary">Inscription</a>';
    echo '<br>';

    // Récupérer le contenu de la temporisation et nettoyer la mémoire tampon
    $content = ob_get_clean();
}

// Afficher la page en utilisant Base.php avec le contenu dynamique
require_once 'Views/Base.php';
