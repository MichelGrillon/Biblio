<?php
session_start();

// Générer un jeton CSRF unique
$csrf_token = bin2hex(random_bytes(32));

// Stocker le jeton CSRF dans la session de l'utilisateur
$_SESSION['csrf_token'] = $csrf_token;
?>

<?php ob_start(); ?>
<form action="http://monsite/projects/php/cefiiBiblio/Core/register.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token); ?>">
    <div class="mb-3">
        <label for="email" class="form-label">Votre email</label>
        <input type="email" class="form-control" id="email" name="email">
    </div>
    <div class="mb-3">
        <label for="username" class="form-label">Votre pseudo</label>
        <input type="text" class="form-control" id="username" name="username">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Votre mot de passe</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <div class="col-12">
        <button class="btn btn-primary" type="submit">Créer un compte</button>
    </div>
</form>
<br>
<a href="index.php?login=auth" class="btn btn-primary">Veuillez vous connecter</a>
<?php $content = ob_get_clean(); ?>

<?php require_once 'Base.php'; ?>