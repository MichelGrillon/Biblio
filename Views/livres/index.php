<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/../../Core/config.php');
// Inclusion du fichier Autoloader.php au début
require_once(__DIR__ . '/../../Autoloader.php');
require_once(__DIR__ . '/../../Controllers/LivreController.php');

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
$title = htmlspecialchars("Gestion des livres - Liste des livres", ENT_QUOTES, 'UTF-8');
?>
<h2><?php echo $title; ?></h2>
<a href="index.php?controller=livre&action=addLivre"><button type="button" class="btn btn-primary">Ajouter un livre</button></a>
<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Titre</th>
            <th scope="col">Auteur</th>
            <th scope="col">Genre</th>
            <th scope="col">ISBN</th>
            <th scope="col">Date d'emprunt</th>
            <th scope="col">Date de retour prévue</th>
            <th scope="col">Emprunteur</th>
            <th scope="col">Actions</th>
            <th scope="col">Disponibilité</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($list as $value) : ?>
            <tr>
                <td><?= htmlspecialchars($value->getIdLivre(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($value->getTitre(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($value->getAuteur(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($value->getGenre(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($value->getIsbn(), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= $value->getDateEmprunt() ? date('d-m-Y', strtotime($value->getDateEmprunt())) : 'N/A' ?></td>
                <td><?= $value->getDateRetourPrevue() ? date('d-m-Y', strtotime($value->getDateRetourPrevue())) : 'N/A' ?></td>
                <td>
                    <?= $value->getNom() ? htmlspecialchars($value->getNom(), ENT_QUOTES, 'UTF-8') : 'N/A' ?>
                    <?= $value->getPrenom() ? htmlspecialchars($value->getPrenom(), ENT_QUOTES, 'UTF-8') : '' ?>
                </td>
                <td>
                    <a href="index.php?controller=livre&action=showLivre&id=<?= htmlspecialchars($value->getIdLivre(), ENT_QUOTES, 'UTF-8') ?>"><i class="fas fa-eye"></i></a>
                    <a href="index.php?controller=livre&action=updateLivre&id=<?= htmlspecialchars($value->getIdLivre(), ENT_QUOTES, 'UTF-8') ?>"><i class="fas fa-pen"></i></a>
                    <?php if (!$value->getIdEmprunteur()) : ?>
                        <form action="index.php?controller=livre&action=deleteLivre&id=<?= htmlspecialchars($value->getIdLivre(), ENT_QUOTES, 'UTF-8') ?>" method="POST">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($value->getIdLivre(), ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" class="btn btn-link"><i class="fas fa-trash-alt"></i></button>
                        </form>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($value->getDisponibilite(), ENT_QUOTES, 'UTF-8') ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>