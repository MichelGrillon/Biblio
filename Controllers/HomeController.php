<?php

namespace App\Controllers;

class HomeController extends Controller
{
    // Méthode d'action par défaut
    public function index()
    {
        // Vérifier si une session est déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
        }

        // Rendu de la vue en incluant le token CSRF dans le formulaire
        $this->render('home/index');
    }
}
