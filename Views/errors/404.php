<!DOCTYPE html>
<html>
<? // Inclure le fichier config.php pour accéder à BASE_URL
require_once __DIR__ . '/../../Core/config.php'; ?>

<head>
    <meta charset="UTF-8">
    <title>Erreur 404 - Page non trouvée</title>
</head>

<body>
    <h1>Erreur 404 - Page non trouvée</h1>
    <p>Désolé, la page que vous cherchez n'existe pas ou n'est plus disponible.</p>
    <p><a href="<?= BASE_URL ?>/index.php?controller=home&action=index">Retourner à l'accueil</a></p>
</body>

</html>