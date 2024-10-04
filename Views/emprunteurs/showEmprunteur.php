<?php

// Activer l'affichage des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérification si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'existence du jeton CSRF dans la session
if (!isset($_SESSION['csrf_token'])) {
    // Jeton CSRF non trouvé, génération d'un nouveau jeton
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}

//require_once(__DIR__ . '/../../Core/config.php');
require_once(__DIR__ . '/../../Controllers/EmprunteurController.php');
require_once(__DIR__ . '/../../Models/EmprunteurModel.php');

$title = htmlspecialchars("Gestion des emprunteurs - Détails de l'emprunteur", ENT_QUOTES, 'UTF-8');
?>

<article class="row justify-content-center text-center">
    <!-- Affichage des détails de l'emprunteur -->
    <h2>Détails de l'emprunteur</h2>
    <h1 class="col-12"><?php echo htmlspecialchars($emprunteur->getPrenom() . ' ' . $emprunteur->getNom(), ENT_QUOTES, 'UTF-8'); ?></h1>
    <p>Date de naissance: <?php echo htmlspecialchars(date("d/m/Y", strtotime($emprunteur->getDateNaissance())), ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Adresse: <?php echo htmlspecialchars($emprunteur->getAdresse(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Numéro de téléphone: <?php echo htmlspecialchars($emprunteur->getNumeroTelephone(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Email: <?php echo htmlspecialchars($emprunteur->getEmail(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Date d'inscription: <?php echo htmlspecialchars(date("d/m/Y", strtotime($emprunteur->getDateInscription())), ENT_QUOTES, 'UTF-8'); ?></p>

    <h2 class="col-12">Prêts en cours :</h2>
      <table class="table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Titre du livre</th>
                <th scope="col">Auteur</th>
                <th scope="col">Date d'emprunt</th>
                <th scope="col">Date de retour prévue</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($emprunts as $emprunt) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($emprunt['id_emprunt'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($emprunt['titre'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($emprunt['auteur'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($emprunt['date_emprunt'])), ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($emprunt['date_retour_prevue'])), ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
                <!-- Comparaison de la date de retour prévue avec la date actuelle -->
                <?php
                $dateRetourPrevue = strtotime($emprunt['date_retour_prevue']);
                $dateActuelle = time();
                $delai = 0; // Définissez la valeur appropriée pour le délai

                if ($dateRetourPrevue + $delai < $dateActuelle) {
                    $id_emprunt_safe = htmlspecialchars($emprunt['id_emprunt'], ENT_QUOTES, 'UTF-8');
                    echo "<script>alert('La date de restitution est dépassée pour l'emprunt #" . $id_emprunt_safe . " !');</script>";
                    echo "<div class='alert alert-danger' role='alert'>La date de restitution est dépassée pour l'emprunt #" . $id_emprunt_safe . " !</div>";
                }
                ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Formulaire avec jeton CSRF pour la protection CSRF -->
    <form action="#" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    </form>
</article>