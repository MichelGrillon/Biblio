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

require_once(__DIR__ . '/../../Core/DbConnect.php');
require_once(__DIR__ . '/../../Models/EmpruntModel.php');
require_once(__DIR__ . '/../../Models/EmprunteurModel.php');
require_once(__DIR__ . '/../../Models/LivreModel.php');
require_once(__DIR__ . '/../../Core/config.php');
require_once(__DIR__ . '/../../Controllers/EmpruntController.php');

use App\Models\EmpruntModel;
use App\Models\EmprunteurModel;
use App\Models\LivreModel;

$title = htmlspecialchars("Gestion des emprunts - Détails de l'emprunt", ENT_QUOTES, 'UTF-8');

// Vérifier si l'identifiant de l'emprunt est passé en paramètre
if (isset($_GET['id_emprunt'])) {
    $id_emprunt = $_GET['id_emprunt'];

    $empruntModel = new EmpruntModel();
    $emprunt = $empruntModel->find($id_emprunt);

    if ($emprunt) {
        $emprunteurModel = new EmprunteurModel();
        $emprunteur = $emprunteurModel->find($emprunt->getIdEmprunteur());

        $livreModel = new LivreModel();
        $livre = $livreModel->find($emprunt->getIdLivre());
?>
        <article class="row justify-content-center text-center">
            <h2>Détails de l'emprunt</h2>
            <h1 class="col-12">Emprunt #<?php echo htmlspecialchars($emprunt->getIdEmprunt(), ENT_QUOTES, 'UTF-8'); ?></h1>

            <p>Emprunteur: <?php echo htmlspecialchars($emprunteur->getPrenom() . ' ' . $emprunteur->getNom(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Livre emprunté: <?php echo htmlspecialchars($livre->getTitre(), ENT_QUOTES, 'UTF-8'); ?> (ID: <?php echo htmlspecialchars($livre->getIdLivre(), ENT_QUOTES, 'UTF-8'); ?>)</p>
            <p>Date d'emprunt: <?php echo htmlspecialchars(date("d/m/Y", strtotime($emprunt->getDateEmprunt())), ENT_QUOTES, 'UTF-8'); ?></p>
            <p>Date de retour prévue: <?php echo htmlspecialchars(date("d/m/Y", strtotime($emprunt->getDateRetourPrevue())), ENT_QUOTES, 'UTF-8'); ?></p>

            <?php
            // Comparaison de la date de retour prévue avec la date actuelle
            $dateRetourPrevue = strtotime($emprunt->getDateRetourPrevue());
            $dateActuelle = time();
            $delai = 3 * 7 * 24 * 60 * 60; // 3 semaines en secondes

            if ($dateRetourPrevue + $delai < $dateActuelle) {
                $id_emprunt_safe = htmlspecialchars($emprunt->getIdEmprunt(), ENT_QUOTES, 'UTF-8');
                echo "<script>alert('La date de restitution est dépassée pour l\'emprunt #" . $id_emprunt_safe . " !');</script>";
                echo "<div class='alert alert-danger' role='alert'>La date de restitution est dépassée pour l'emprunt #" . $id_emprunt_safe . " !</div>";
            }
            ?>

            <!-- Formulaire avec jeton CSRF pour la protection CSRF -->
            <form action="#" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            </form>
        </article>
<?php
    } else {
        echo "<p>" . htmlspecialchars("L'emprunt sélectionné n'existe pas", ENT_QUOTES, 'UTF-8') . "</p>";
    }
} else {
    echo "<p>" . htmlspecialchars("Aucun emprunt sélectionné.", ENT_QUOTES, 'UTF-8') . "</p>";
}
?>