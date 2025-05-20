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
        
        // Initialisation du token CSRF s'il n'existe pas
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
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
    
    /**
     * Récupère les rôles de l'utilisateur connecté depuis la session.
     *
     * @return array Tableau des noms de rôles, ou tableau vide si non connecté ou pas de rôles.
     */
    protected function getUserRoles(): array
    {
        if ($this->isUserLoggedIn() && isset($_SESSION['user']['roles']) && is_array($_SESSION['user']['roles'])) {
            return $_SESSION['user']['roles'];
        }
        return []; // Retourne un tableau vide par défaut
    }

    /**
     * Vérifie si l'utilisateur connecté possède un rôle spécifique.
     *
     * @param string $roleName Le nom exact du rôle à vérifier (sensible à la casse).
     * @return bool True si l'utilisateur a le rôle, false sinon.
     */
    protected function hasRole(string $roleName): bool
    {
        $userRoles = $this->getUserRoles();
        // in_array() vérifie si une valeur existe dans un tableau
        return in_array($roleName, $userRoles, true); // Le 'true' rend la comparaison stricte (type et valeur)
    }

    /**
     * Raccourci pour vérifier si l'utilisateur est Administrateur.
     * Suppose que le rôle admin s'appelle exactement 'admin'.
     * @return bool
     */
    protected function isAdmin(): bool
    {
         return $this->hasRole('admin'); // Adaptez 'admin' si le nom est différent dans votre DB
    }

    /**
     * Raccourci pour vérifier si l'utilisateur est Formateur.
     * Suppose que le rôle formateur s'appelle exactement 'formateur'.
     * @return bool
     */
     protected function isFormateur(): bool
     {
          return $this->hasRole('formateur'); // Adaptez 'formateur' si besoin
     }

     // Ajouter d'autres raccourcis si nécessaire (isEtudiant, isSecretaire...)

    /**
     * Exige un accès Admin, sinon redirige.
     */
    protected function requireAdmin(): void
    {
        $this->requireLogin();
        if (!$this->isAdmin()) {
            $this->setFlashMessage('error', 'Accès refusé. Vous devez être administrateur pour accéder à cette page.');
            $this->redirect('/dashboard');
        }
    }
}