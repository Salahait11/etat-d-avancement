<?php // src/Core/BaseController.php

declare(strict_types=1);

namespace App\Core; // Namespace correspondant au dossier

// Pas besoin de 'use PDO' ici car on ne l'utilise pas directement

abstract class BaseController
{
    /**
     * Rend une vue avec des données, potentiellement dans un layout.
     * Injecte automatiquement isLoggedIn et currentUser.
     */
    protected function render(string $viewPath, array $data = [], ?string $layoutPath = 'layout'): void
    {
        $fullViewPath = VIEW_PATH . $viewPath . '.php';

        if (!file_exists($fullViewPath)) {
            trigger_error("Fichier de vue non trouvé : " . htmlspecialchars($fullViewPath), E_USER_ERROR);
            return;
        }

        $commonData = [
            'isLoggedIn' => $this->isUserLoggedIn(),
            'currentUser' => $this->getCurrentUser(),
        ];
        $viewData = array_merge($commonData, $data);
        extract($viewData, EXTR_SKIP);

        ob_start();
        require $fullViewPath;
        $content = ob_get_clean();

        if ($layoutPath) {
            $fullLayoutPath = VIEW_PATH . $layoutPath . '.php';
            if (!file_exists($fullLayoutPath)) {
                trigger_error("Fichier de layout non trouvé : " . htmlspecialchars($fullLayoutPath), E_USER_ERROR);
                echo $content;
            } else {
                require $fullLayoutPath; // Le layout a accès aux variables extraites + $content
            }
        } else {
            echo $content;
        }
    }

    /**
     * Redirige vers une URL interne de l'application (construit l'URL complète).
     */
    protected function redirect(string $path)
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    $location = $protocol . '://' . $host . $base . '/' . ltrim($path, '/');
    header('Location: ' . $location);
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
        return trim(strip_tags((string)$source[$key]));
    }

    /**
     * Définit un message flash dans la session.
     */
    protected function setFlashMessage(string $key, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * Récupère (et supprime) un message flash.
     */
    protected function getFlashMessage(string $key): ?string
    {
        if (session_status() === PHP_SESSION_NONE) { session_start(); }
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
         if (session_status() === PHP_SESSION_NONE) { session_start(); }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user']['id']);
    }

    /**
     * Exige la connexion, sinon redirige vers /login.
     */
    protected function requireLogin(): void
    {
        if (!$this->isUserLoggedIn()) {
            $this->setFlashMessage('warning', 'Veuillez vous connecter pour accéder à cette page.');
            $this->redirect('/login');
        }
    }

     /**
      * Récupère les données de l'utilisateur connecté depuis la session.
      */
    protected function getCurrentUser(): ?array
    {
        if ($this->isUserLoggedIn()) {
            return $_SESSION['user'] ?? null;
        }
        return null;
    }
}