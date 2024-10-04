<?php
require_once(__DIR__ . '/../../Core/config.php');
require_once(__DIR__ . '/../../Controllers/LivreController.php');

// Échapper le titre avant de l'utiliser
$title = htmlspecialchars("Gestion des livres - Ajout d'un livre", ENT_QUOTES, 'UTF-8');
?>
<h1><?php echo $title; ?></h1>
<?php
echo $addForm;
?>