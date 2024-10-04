<?php
require_once(__DIR__ . '/../../Core/config.php');
// Inclusion du fichier Autoloader.php au début
require_once '../Autoloader.php';
require_once(__DIR__ . '/../../Controllers/EmpruntController.php');

// Appel de la méthode register de la classe Autoloader pour l'enregistrement de l'autoloader
App\Autoloader::register();

// Vérification si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'existence du jeton CSRF dans la session
if (!isset($_SESSION['csrf_token'])) {
    // Jeton CSRF non trouvé, génération d'un nouveau jeton
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}

// Échapper le titre avant de l'utiliser
$title = htmlspecialchars("Gestion des emprunts - Liste des emprunts en cours", ENT_QUOTES, 'UTF-8');
?>
<h2><?php echo $title; ?></h2>
<a href="index.php?controller=emprunt&action=addEmprunt"><button type="button" class="btn btn-primary">Ajouter un emprunt</button></a>
<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Prénom emprunteur</th>
            <th scope="col">Nom emprunteur</th>
            <th scope="col">Id livre</th>
            <th scope="col">Titre livre emprunté</th>
            <th scope="col">Date d'emprunt</th>
            <th scope="col">Date de retour prévue</th>
            <th scope="col">Statut</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        // On boucle dans le tableau $list qui contient la liste des emprunts en cours
        foreach ($list as $value) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($value->getIdEmprunt(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getPrenomEmprunteur(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getNomEmprunteur(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getIdLivre(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars($value->getTitre(), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars(date("d/m/Y", strtotime($value->getDateEmprunt())), ENT_QUOTES, 'UTF-8') . "</td>";
            echo "<td>" . htmlspecialchars(date("d/m/Y", strtotime($value->getDateRetourPrevue())), ENT_QUOTES, 'UTF-8') . "</td>";

            $dateRetourPrevue = strtotime($value->getDateRetourPrevue());
            $dateActuelle = time();
            $delai = 3 * 7 * 24 * 60 * 60; // 3 semaines en secondes

            echo "<td>";
            if ($dateRetourPrevue + $delai < $dateActuelle) {
                $id_emprunt_safe = htmlspecialchars($value->getIdEmprunt(), ENT_QUOTES, 'UTF-8');
                echo "<div class='alert alert-danger' role='alert'>La date de restitution est dépassée pour l'emprunt #" . $id_emprunt_safe . " !</div>";
            } else {
                echo "En cours";
            }
            echo "</td>";
            echo "<td><a href='index.php?controller=emprunt&action=showEmprunt&id=" . htmlspecialchars($value->getIdEmprunt(), ENT_QUOTES, 'UTF-8') . "'><i class='fas fa-eye'></i></a></td>";
            echo "<td><a href='index.php?controller=emprunt&action=updateEmprunt&id=" . htmlspecialchars($value->getIdEmprunt(), ENT_QUOTES, 'UTF-8') . "'><i class='fas fa-pen'></i></a></td>";
            echo "<td><form action='index.php?controller=emprunt&action=deleteEmprunt' method='POST'>";
            echo "<input type='hidden' name='csrf_token' value='" . $_SESSION['csrf_token'] . "'>";
            echo "<input type='hidden' name='id' value='" . htmlspecialchars($value->getIdEmprunt(), ENT_QUOTES, 'UTF-8') . "'>";
            echo "<button type='submit' class='btn btn-link'><i class='fas fa-trash-alt'></i></button>";
            echo "</form></td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>