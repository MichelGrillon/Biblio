<?php

namespace App;

class Autoloader
{

    static function register()
    {
        // Enregistrement de la fonction autoload
        spl_autoload_register([
            __CLASS__,
            'autoload'
        ]);
    }

    static function autoload($class)
    {
        // Validation du nom de la classe
        if (strpos($class, __NAMESPACE__ . '\\') === 0) {
            $class = str_replace(__NAMESPACE__ . '\\', '', $class);
            $class = str_replace('\\', '/', $class);
            $file = __DIR__ . '/' . $class . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    // Méthode pour régénérer l'ID de session si l'utilisateur est authentifié
    static function regenerateSessionIdIfAuthenticated()
    {
        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
            session_regenerate_id();
        }
    }
}
