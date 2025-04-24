<?php // public/index.php


declare(strict_types=1);

// --- Chargement Initial ---
// Autoloader Composer (pour charger les classes App\...)
require_once __DIR__ . '/../vendor/autoload.php';

// Configuration de l'application (définit BASE_URL, APP_ENV)
$appConfig = require_once __DIR__ . '/../config/app.php'; // Assurez-vous que BASE_URL est correctement définie dans ce fichier !

define('BASE_PATH', dirname(__DIR__)); // Définit le chemin racine du projet (gestion_ecoles_v2/)
define('VIEW_PATH', BASE_PATH . '/src/View/');
// --- Démarrer la Session ---
// Doit être démarrée avant toute sortie ou accès à $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Récupération de la Requête HTTP ---
$requestUri = $_SERVER['REQUEST_URI'];
// Extrait le chemin (ex: /gestion_ecoles_v2/public/login) - Utilise '' comme fallback
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?? '';
$requestMethod = $_SERVER['REQUEST_METHOD']; // GET, POST, etc.

// --- Calcul de la Route Applicative ---
// Récupère le chemin de base de l'URL depuis la constante définie dans app.php
// Utilise '' comme fallback si BASE_URL n'est pas définie ou si parse_url échoue
$urlBasePath = parse_url(defined('BASE_URL') ? BASE_URL : '', PHP_URL_PATH) ?? '';

// Initialise la route applicative (ce qui vient après le chemin de base)
$route = '/'; // Route par défaut

// Si l'URL de base n'est pas vide et que le chemin demandé commence par elle...
if (!empty($urlBasePath) && $urlBasePath !== '/' && str_starts_with($requestPath, $urlBasePath)) {
    // Extrait la partie de la route qui vient APRÈS le chemin de base
    $route = substr($requestPath, strlen($urlBasePath));
} elseif (empty($urlBasePath) || $urlBasePath === '/') {
    // Si l'application est à la racine web, la route est simplement le chemin demandé
    $route = $requestPath;
}

// Nettoyage final de la route calculée :
// 1. S'assurer qu'elle commence par un slash
if (empty($route)) {
     $route = '/'; // Si vide (accès à la racine du sous-dossier), mettre '/'
} elseif (!str_starts_with($route, '/')) {
    $route = '/' . $route;
}
// 2. Enlever le slash de fin, sauf si c'est la racine elle-même
$route = ($route === '/') ? '/' : rtrim($route, '/');


// --- Routage (Basé sur la route applicative calculée) ---
// Ce bloc détermine quel contrôleur et quelle méthode appeler.
try {
    // Instanciation "paresseuse" des contrôleurs (seulement si nécessaire)
    $homeController = null;
    $authController = null;
    // $filiereController = null; // Pour plus tard

    // Logique de routage simple avec un switch
    switch ($route) {
        case '/':
            // Option 1: La page d'accueil est gérée par HomeController
            $homeController = $homeController ?? new \App\Controller\HomeController();
            $homeController->index();
            // Option 2: La page d'accueil est la page de login si non connecté
            // $authController = $authController ?? new \App\Controller\AuthController();
            // $authController->handleHomepage();
            break;

        case '/test-route':
            $homeController = $homeController ?? new \App\Controller\HomeController();
            $homeController->test();
            break;

        // --- Routes d'Authentification ---
        case '/login':
             $authController = $authController ?? new \App\Controller\AuthController();
             if ($requestMethod === 'GET') {
                  $authController->showLoginForm(); // Appel réel
             } elseif ($requestMethod === 'POST') {
                  $authController->processLogin(); // Appel réel
             } else {
                  http_response_code(405);
                  if ($homeController === null) $homeController = new \App\Controller\HomeController();
                  $homeController->render('errors/405', ['title' => 'Méthode Non Autorisée']);
             }
             break;

         case '/logout':
              $authController = $authController ?? new \App\Controller\AuthController();
              if ($requestMethod === 'GET' || $requestMethod === 'POST') {
                   $authController->logout(); // Appel réel
              } else {
                   http_response_code(405);
                   if ($homeController === null) $homeController = new \App\Controller\HomeController();
                   $homeController->render('errors/405', ['title' => 'Méthode Non Autorisée']);
              }
              break;

         case '/dashboard':
              $authController = $authController ?? new \App\Controller\AuthController();
              // requireLogin est appelé DANS showDashboard maintenant
              $authController->showDashboard(); // Appel réel
              break;

         // --- Routes pour Filières (Exemple pour plus tard) ---
         case '/filieres':
               $authController = $authController ?? new \App\Controller\AuthController();
               $authController->requireLogin();

               // $filiereController = new \App\Controller\FiliereController(); // Instancier
               // $filiereController->list(); // Appeler la méthode list

               echo "<h1>Liste des Filières (Implémentation TODO)</h1>";
               break;


        // --- Route Non Trouvée ---
        default:
            http_response_code(404);
            if ($homeController === null) $homeController = new \App\Controller\HomeController();
            $homeController->render('errors/404', ['title' => 'Page Non Trouvée']);
            break;
    }

} catch (\Throwable $e) { // Gestionnaire d'erreur global pour toutes les exceptions/erreurs
    // Loguer l'erreur de manière détaillée côté serveur
    error_log("ERREUR APPLICATION : [Code {$e->getCode()}] {$e->getMessage()}\n{$e->getTraceAsString()}");

    // Définir le code de statut HTTP (utiliser celui de l'exception si valide, sinon 500)
    $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    http_response_code($statusCode);

    // Afficher une page d'erreur appropriée selon l'environnement
    // Utiliser la constante APP_ENV définie depuis config/app.php
    if (defined('APP_ENV') && APP_ENV === 'development') {
        // Affichage détaillé en développement
        echo "<!DOCTYPE html><html><head><title>Erreur Application</title><style>body{font-family: sans-serif; margin: 20px;} h1{color: #dc3545;} pre{background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 4px; white-space: pre-wrap; word-wrap: break-word; font-size: 14px;}</style></head><body>";
        echo "<h1>Erreur Application (Code: {$statusCode})</h1>";
        echo "<pre><strong>Type:</strong> " . get_class($e) . "</pre>";
        echo "<pre><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . " @ Ligne: " . htmlspecialchars((string)$e->getLine()) . "</pre>";
        echo "<h3>Trace:</h3><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        // Afficher l'erreur précédente si elle existe (utile pour les erreurs PDO)
        if ($e->getPrevious()) {
             echo "<h3>Erreur Précédente:</h3><pre>" . htmlspecialchars($e->getPrevious()->getMessage()) . "\n" . htmlspecialchars($e->getPrevious()->getTraceAsString()) . "</pre>";
        }
        echo "</body></html>";
    } else {
        // Affichage générique en production (utiliser une vue serait mieux)
        // Tenter d'utiliser le système de rendu pour la page 500
        try {
             // On a besoin d'une instance de BaseController pour utiliser render
             // Créer une instance anonyme ou utiliser un contrôleur déjà chargé
             $errorController = new class extends \App\Core\BaseController {}; // Instance anonyme
             $errorController->render('errors/500', ['title' => 'Erreur Serveur']);
        } catch (\Throwable $renderError) {
             // Si même le rendu de la page 500 échoue, afficher du HTML basique
             error_log("ERREUR lors du rendu de la page 500: " . $renderError->getMessage());
             echo "<!DOCTYPE html><html><head><title>Erreur Serveur</title></head><body><h1>Erreur Serveur</h1><p>Une erreur interne critique est survenue.</p></body></html>";
        }
    }
    exit; // Arrêter l'exécution après gestion de l'erreur
}

?>