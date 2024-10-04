<?php
// Vérification si la session est déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Vérification de l'existence du jeton CSRF dans la session
if (!isset($_SESSION['csrf_token'])) {
    // Jeton CSRF non trouvé, générer un nouveau jeton
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
}

require_once(__DIR__ . '/../../Core/config.php');
require_once(__DIR__ . '/../../Core/Form.php');
require_once(__DIR__ . '/../../Controllers/LivreController.php');

$title = htmlspecialchars("Gestion des livres - Modification d'un livre", ENT_QUOTES, 'UTF-8');
?>

<h1><?php echo $title; ?></h1>
<?php
if (!empty($erreur)) {
?>
    <div class="alert alert-danger" role="alert">
        <?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php
}
?>
<section class="row">
    <div class="col-12">
        <!-- Ajout d'un formulaire avec le jeton CSRF pour la protection CSRF -->
        <form action="#" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <?php echo $updateForm->getFormElements(); ?>
        </form>
    </div>
</section>