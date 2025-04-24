<?php // src/Controller/HomeController.php

declare(strict_types=1);

namespace App\Controller;

use App\Core\BaseController; // Important

class HomeController extends BaseController // Hérite de BaseController
{
    /**
     * Affiche la page d'accueil.
     */
    public function index(): void
    {
        // On pourrait récupérer des données ici si nécessaire
        $data = [
            'title' => 'Accueil - Gestion Ecoles v2',
            'welcomeMessage' => 'Bienvenue sur la nouvelle version !'
        ];
        // Rend la vue 'home/index.php' en utilisant le layout par défaut
        $this->render('home/index', $data);
    }

    /**
     * Affiche une page de test simple.
     */
    public function test(): void
    {
        $data = ['title' => 'Page de Test'];
        $this->render('home/test', $data);
    }
}