<?php // src/Core/BaseController.php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database; // Important: importe notre classe Database

/**
 * Contrôleur de base abstrait fournissant des fonctionnalités communes.
 */
abstract class BaseController
{
    protected Database $db; // Propriété pour tenir l'instance de Database

    /**
     * Constructeur : récupère l'instance de Database.
     */
    public function __construct()
    {
        $this->db = Database::getInstance(); // Obtient l'instance Singleton
    }

    /**
     * Rend une vue avec des données, potentiellement dans un layout.
     * Injecte automatiquement isLoggedIn et currentUser.
     * Utilise BASE_URL pour les URLs dans les vues.
     */
    protected function render(string $viewPath, array $data = [], ?string $layoutPath = 'layout'): void
    {
        $fullViewPath = VIEW_PATH . $viewPath . '.php';

        if (!file_exists($fullViewPath)) {
            trigger_error("Fichier de vue non trouvé : {$fullViewPath}", E_USER_ERROR);
            return;
        }

        // Données communes à injecter dans toutes les vues/layouts
        $commonData = [
            'isLoggedIn' => $this->isUserLoggedIn(),
            'currentUser' => $this->getCurrentUser(),
            'baseUrl' => BASE_URL // Rend BASE_URL disponible dans les vues pour les liens/ressources
        ];

        $viewData = array_merge($commonData, $data);
        // Attention: Ne pas extraire $this ou $db !
        extract($viewData, EXTR_SKIP);

        ob_start();
        require $fullViewPath;
        $content = ob_get_clean(); // $content contient le HTML de la vue

        if ($layoutPath) {
            $fullLayoutPath = VIEW_PATH . $layoutPath . '.php';
            if (file_exists($fullLayoutPath)) {
                // Le layout a accès aux mêmes variables que la vue + $content
                require $fullLayoutPath;
            } else {
                trigger_error("Fichier de layout non trouvé : {$fullLayoutPath}", E_USER_WARNING);
                echo $content; // Affiche sans layout si non trouvé
            }
        } else {
            echo $content; // Affiche sans layout
        }
    }

    /**
     * Redirige vers une URL interne (construite à partir de BASE_URL).
     */
    protected function redirect(string $path): void
    {
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }
        // Utilise la constante BASE_URL définie dans app.php
        $url = BASE_URL . $path;
        header('Location: ' . $url, true, 302);
        exit;
    }


    /**
     * Récupère une donnée d'entrée GET ou POST (nettoyage basique).
     */
    protected function input(string $key, mixed $default = null, string $method = 'post'): mixed
    {
         $source = ($method === 'post') ? $_POST : $_GET;
         if (!isset($source[$key]) || $source[$key] === '') {
             return $default;
         }
         // Important : Convertir explicitement en chaîne avant strip_tags
         return trim(strip_tags((string)$source[$key]));
    }


    /**
     * Définit un message flash dans la session.
     */
    protected function setFlashMessage(string $key, string $message): void
    {
        // Pas besoin de vérifier session_status ici, car index.php l'a déjà démarrée.
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * Récupère (et supprime) un message flash.
     */
    protected function getFlashMessage(string $key): ?string
    {
         // Pas besoin de vérifier session_status ici.
        if (isset($_SESSION['_flash'][$key])) {
            $message = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $message;
        }
        return null;
    }

    /**
     * Vérifie si l'utilisateur est connecté via la session.
     */
    protected function isUserLoggedIn(): bool
    {
        // Pas besoin de vérifier session_status ici.
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user']['id']);
    }

    /**
     * Exige la connexion, sinon redirige vers /login.
     */
    protected function requireLogin(): void
    {
        if (!$this->isUserLoggedIn()) {
            $this->setFlashMessage('warning', 'Veuillez vous connecter pour accéder à cette page.');
            $this->redirect('/login'); // Utilise la redirection basée sur BASE_URL
        }
    }

     /**
      * Récupère les données de l'utilisateur connecté depuis la session.
      */
    protected function getCurrentUser(): ?array
    {
        // Pas besoin de vérifier session_status ici.
        if ($this->isUserLoggedIn()) {
            return $_SESSION['user'] ?? null;
        }
        return null;
    }
}