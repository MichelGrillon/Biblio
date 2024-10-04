<?php

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclusion du fichier Autoloader.php au début
require_once(__DIR__ . '/../../Autoloader.php');
require_once(__DIR__ . '/../../Controllers/EmprunteurController.php');

// Appel de la méthode register de la classe Autoloader pour l'enregistrement de l'autoloader
App\Autoloader::register();

// Vérifier si la session n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'existence du jeton CSRF dans la session
if (!isset($_SESSION['csrf_token'])) {
    // Jeton CSRF non trouvé, générer un nouveau jeton
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}

// Échapper le titre avant de l'utiliser
$title = htmlspecialchars("Gestion des emprunteurs - Liste des emprunteurs", ENT_QUOTES, 'UTF-8');
?>
<h2><?php echo $title; ?></h2>
<a href="index.php?controller=emprunteur&action=addEmprunteur"><button type="button" class="btn btn-primary">Ajouter un emprunteur</button></a>
<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nom</th>
            <th scope="col">Prénom</th>
            <th scope="col">Date de Naissance</th>
            <th scope="col">Adresse</th>
            <th scope="col">Téléphone</th>
            <th scope="col">Email</th>
            <th scope="col">Date d'Inscription</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // On boucle dans le tableau $list qui contient la liste des emprunteurs
        foreach ($list as $value) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($value->getIdEmprunteur(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getNom(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getPrenom(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getDateNaissance(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getAdresse(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getNumeroTelephone(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getEmail(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getDateInscription(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>";
            echo "<a href='index.php?controller=emprunteur&action=showEmprunteur&id=" . htmlspecialchars($value->getIdEmprunteur(), ENT_QUOTES, 'UTF-8') . "'><i class='fas fa-eye'></i></a>";
            echo "<a href='index.php?controller=emprunteur&action=updateEmprunteur&id=" . htmlspecialchars($value->getIdEmprunteur(), ENT_QUOTES, 'UTF-8') . "'><i class='fas fa-pen'></i></a>";
            echo "<form action='index.php?controller=emprunteur&action=deleteEmprunteur' method='POST'>";
            echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($value->getIdEmprunteur(), ENT_QUOTES, 'UTF-8') . "'>";
            echo "<button type='submit' class='btn btn-link'><i class='fas fa-trash-alt'></i></button>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
