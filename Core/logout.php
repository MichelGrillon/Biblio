<?php
session_start();
session_unset();
session_destroy();
header("Location: https://michel-grillon.fr/projects/php/cefiiBiblio/index.php");
