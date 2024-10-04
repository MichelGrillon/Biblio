<?php
// Vérifier si une session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    // Si aucune session n'est démarrée, démarrer une nouvelle session
    session_start();
}

// Inclure le fichier contenant la classe LivreModel
require_once(__DIR__ . '/../../Models/LivreModel.php');
require_once(__DIR__ . '/../../Models/EmpruntModel.php');
require_once(__DIR__ . '/../../Models/EmprunteurModel.php');
require_once(__DIR__ . '/../../Core/config.php');
require_once(__DIR__ . '/../../Controllers/LivreController.php');

// Utiliser l'espace de noms correct pour LivreModel
use App\Models\LivreModel;
use App\Models\EmpruntModel;
use App\Models\EmprunteurModel;

// Vérification de l'existence du jeton CSRF dans la session
if (!isset($_SESSION['csrf_token'])) {
    // Jeton CSRF non trouvé, génération d'un nouveau jeton
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}

// Instancier LivreModel
$livreModel = new LivreModel();
$empruntModel = new EmpruntModel();
$emprunteurModel = new EmprunteurModel();

// Récupérer l'identifiant du livre depuis les paramètres de l'URL
$id_livre = $_GET['id'] ?? null;

// Validation de l'ID du livre
if (!is_numeric($id_livre)) {
    die("Identifiant du livre invalide.");
}

// Récupérer les détails du livre
$livre = $livreModel->find($id_livre);

// Vérification de l'existence du livre
if (!$livre) {
    die("Le livre demandé n'existe pas.");
}

// Récupérer les détails de l'emprunt du livre (s'il est emprunté)
$emprunt = $empruntModel->findByLivreId($id_livre);

// Récupérer les détails de l'emprunteur (s'il est emprunté)
if ($emprunt) {
    $emprunteur = $emprunteurModel->find($emprunt->getIdEmprunteur());
}


// Récupère le titre pour l'affichage
$title = htmlspecialchars("Gestion des livres - Détails du livre : " . $livre->getTitre(), ENT_QUOTES, 'UTF-8');
?>

<article class="row justify-content-center text-center">
    <h1 class="col-12"><?php echo htmlspecialchars($livre->getTitre(), ENT_QUOTES, 'UTF-8'); ?></h1>
    <p>Auteur : <?php echo htmlspecialchars($livre->getAuteur(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p>Genre : <?php echo htmlspecialchars($livre->getGenre(), ENT_QUOTES, 'UTF-8'); ?></p>
    <p>ISBN : <?php echo htmlspecialchars($livre->getIsbn(), ENT_QUOTES, 'UTF-8'); ?></p>

    <?php if ($emprunt) : ?>
        <h2 class="col-12">Emprunteur :</h2>
        <p>Nom : <?php echo htmlspecialchars($emprunteur->getNom(), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Prénom : <?php echo htmlspecialchars($emprunteur->getPrenom(), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Date de naissance : <?php echo htmlspecialchars(date("d/m/Y", strtotime($emprunteur->getDateNaissance())), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Adresse : <?php echo htmlspecialchars($emprunteur->getAdresse(), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Numéro de téléphone : <?php echo htmlspecialchars($emprunteur->getNumeroTelephone(), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Email : <?php echo htmlspecialchars($emprunteur->getEmail(), ENT_QUOTES, 'UTF-8'); ?></p>

        <h2 class="col-12">Emprunt :</h2>
        <p>Date d'emprunt : <?php echo htmlspecialchars(date("d/m/Y", strtotime($emprunt->getDateEmprunt())), ENT_QUOTES, 'UTF-8'); ?></p>
        <p>Date de retour prévue : <?php echo htmlspecialchars(date("d/m/Y", strtotime($emprunt->getDateRetourPrevue())), ENT_QUOTES, 'UTF-8'); ?></p> <!-- Utilisation de la méthode getter -->

        <?php
        // Supposons que vous ayez défini ces variables correctement dans votre logique
        $dateRetourPrevue = strtotime($emprunt->getDateRetourPrevue()); // Utiliser le getter
        $dateActuelle = time(); // Date actuelle
        $delai = 0; // Mettez la valeur appropriée ici

        // Comparaison de la date de retour prévue avec la date actuelle
        if ($dateRetourPrevue + $delai < $dateActuelle) {
            $id_emprunt_safe = htmlspecialchars($emprunt->getIdEmprunt(), ENT_QUOTES, 'UTF-8');
            echo "<script>alert('La date de restitution est dépassée pour l\'emprunt #" . $id_emprunt_safe . " !');</script>";
            echo "<div class='alert alert-danger' role='alert'>La date de restitution est dépassée pour l'emprunt #" . $id_emprunt_safe . " !</div>";
        }
        ?>
    <?php endif; ?>



    <!-- Formulaire avec jeton CSRF pour la protection CSRF -->
    <form action="#" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    </form>
</article>