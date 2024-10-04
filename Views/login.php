<?php
session_start();

// Générer un jeton CSRF unique
$csrf_token = bin2hex(random_bytes(32));

// Stocker le jeton CSRF dans la session de l'utilisateur
$_SESSION['csrf_token'] = $csrf_token;
?>

<form action="http://monsite/projects/php/cefiiBiblio/Core/auth.php" method="post">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    <div class="mb-3">
        <label for="email" class="form-label">Votre email</label>
        <input type="email" class="form-control" id="email" name="email">
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Votre mot de passe</label>
        <input type="password" class="form-control" id="password" name="password">
    </div>
    <div class="col-12">
        <button class="btn btn-primary" type="submit">Se connecter</button>
    </div>
</form>