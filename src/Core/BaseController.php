<?php // src/Core/BaseController.php

declare(strict_types=1);

namespace App\Core; // Le namespace correspond à la structure des dossiers depuis src/

abstract class BaseController
{
    /**
     * Rend un fichier de vue.
     *
     * @param string $viewPath Chemin vers la vue relatif à VIEW_PATH (ex: 'auth/login').
     * @param array $data Données optionnelles à extraire dans la portée de la vue.
     * @param string|null $layoutPath Fichier de layout optionnel pour envelopper la vue (ex: 'layout').
     * @return void
     */
    protected function render(string $viewPath, array $data = [], ?string $layoutPath = 'layout'): void
{
    $fullViewPath = VIEW_PATH . $viewPath . '.php';

    if (!file_exists($fullViewPath)) {
        trigger_error("Fichier de vue non trouvé : " . htmlspecialchars($fullViewPath), E_USER_ERROR);
        return;
    }

    // Préparer les données communes nécessaires au layout (et potentiellement à la vue)
    // On appelle les méthodes *ici*, dans le scope du contrôleur où $this est valide.
    $commonData = [
        'isLoggedIn' => $this->isUserLoggedIn(),
        'currentUser' => $this->getCurrentUser(),
    ];

    // Fusionner les données communes avec les données spécifiques à la vue.
    // Les données spécifiques ($data) écraseront les données communes en cas de conflit de clé.
    $viewData = array_merge($commonData, $data);

    // Extraire toutes les données pour qu'elles soient disponibles comme variables
    // (ex: $isLoggedIn, $currentUser, $title, $error, etc.)
    extract($viewData, EXTR_SKIP);

    // Commencer la mise en tampon pour le contenu de la vue principale
    ob_start();
    require $fullViewPath; // La vue a accès aux variables extraites
    $content = ob_get_clean(); // $content est prêt pour le layout

    // Si un layout est spécifié, le rendre
    if ($layoutPath) {
        $fullLayoutPath = VIEW_PATH . $layoutPath . '.php';
        if (!file_exists($fullLayoutPath)) {
            trigger_error("Fichier de layout non trouvé : " . htmlspecialchars($fullLayoutPath), E_USER_ERROR);
            echo $content; // Fallback: affiche le contenu sans layout
        } else {
            // Le fichier layout aura aussi accès aux variables extraites ($isLoggedIn, $currentUser, $title...)
            // ET à la variable $content qui contient le rendu de la vue principale.
            require $fullLayoutPath;
        }
    } else {
        // Si pas de layout, affiche directement le contenu de la vue
        echo $content;
    }
}
    /**
     * Redirige vers un chemin d'URL donné dans l'application.
     *
     * @param string $path Chemin interne (ex: '/dashboard', '/login').
     * @return void
     */
    protected function redirect(string $path): void
    {
        // Pour simplifier, on suppose des chemins relatifs à la racine du domaine
        header('Location: ' . $path);
        exit; // Important: arrêter l'exécution après l'en-tête de redirection
    }

    /**
     * Récupère les données d'entrée POST ou GET avec une sanitization très basique.
     * ATTENTION: C'est TRES basique. Utiliser une bibliothèque dédiée pour la validation/sanitization robuste.
     *
     * @param string $key La clé des données (ex: 'email').
     * @param mixed $default Valeur par défaut si la clé n'est pas trouvée.
     * @param string $method 'post' ou 'get'.
     * @return mixed Valeur d'entrée "nettoyée" ou par défaut.
     */
    protected function input(string $key, mixed $default = null, string $method = 'post'): mixed
    {
        $source = ($method === 'post') ? $_POST : $_GET;
        if (!isset($source[$key])) {
            return $default;
        }
        // Sanitization basique : supprime les balises et espaces superflus.
        // PAS SUFFISANT pour la sécurité (SQL Injection, XSS). Utiliser requêtes préparées & échappement HTML.
        return trim(strip_tags($source[$key]));
    }

    /**
     * Définit un message flash dans la session.
     *
     * @param string $key Clé du message (ex: 'success', 'error').
     * @param string $message Contenu du message.
     * @return void
     */
    protected function setFlashMessage(string $key, string $message): void
    {
        // Assure-toi que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['_flash'][$key] = $message;
    }

    /**
     * Récupère (et supprime) un message flash de la session.
     *
     * @param string $key Clé du message.
     * @return string|null Le message ou null si non trouvé.
     */
    protected function getFlashMessage(string $key): ?string
    {
        // Assure-toi que la session est démarrée
        if (session_status() === PHP_SESSION_NONE) {
           session_start();
        }

        if (isset($_SESSION['_flash'][$key])) {
            $message = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]); // Supprime après lecture
            return $message;
        }
        return null;
    }

    /**
     * Vérifie si l'utilisateur est connecté.
     *
     * @return bool
     */
    protected function isUserLoggedIn(): bool
    {
         // Assure-toi que la session est démarrée
         if (session_status() === PHP_SESSION_NONE) {
            session_start();
         }
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user']['id']);
    }

    /**
     * Exige que l'utilisateur soit connecté. Redirige vers /login sinon.
     * À appeler au début des méthodes de contrôleur nécessitant une authentification.
     *
     * @return void
     */
    protected function requireLogin(): void
    {
        if (!$this->isUserLoggedIn()) {
            $this->setFlashMessage('warning', 'Veuillez vous connecter pour accéder à cette page.');
            $this->redirect('/login');
        }
    }

     /**
     * Récupère les données de l'utilisateur connecté.
     *
     * @return array|null
     */
    protected function getCurrentUser(): ?array
    {
        if ($this->isUserLoggedIn()) {
            return $_SESSION['user'];
        }
        return null;
    }
}