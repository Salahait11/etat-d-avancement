<?php // public/index.php

// Pour afficher les erreurs PENDANT LE DEVELOPPEMENT (À ENLEVER EN PRODUCTION !)

declare(strict_types=1);

// --- Initialisation ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../vendor/autoload.php'; // Charger l'autoloader Composer
require_once __DIR__ . '/../config/database.php';  // Charger la connexion DB ($pdo)

// --- Constantes Utiles ---
define('BASE_PATH', dirname(__DIR__));
define('VIEW_PATH', BASE_PATH . '/src/View/');
define('APP_ENV', 'development'); // 'development' ou 'production'

// --- Récupération de la Requête ---
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

// --- Calcul de la Route (Simplifié) ---
$route = ($requestPath === '/' || $requestPath === '') ? '/' : rtrim($requestPath, '/');
// Adaptation pour sous-dossier (Adapter si nécessaire)
$basePath = '/gestion_ecoles/public'; // <<< Adapter si l'URL de base est différente
if (!empty($basePath) && $basePath !== '/' && str_starts_with($route, $basePath)) {
   $route = substr($route, strlen($basePath));
   if (empty($route)) $route = '/';
}

echo "Route demandée (après traitement) : " . htmlspecialchars($route) . "<br>";
echo "Méthode HTTP : " . htmlspecialchars($requestMethod) . "<br>";

// --- Routage Très Basique (Sera amélioré) ---
// Normalement, ici on utiliserait une classe Router dédiée.
// Pour l'instant, un simple switch pour démarrer.

try {
    // Instancier les contrôleurs nécessaires
    // Pour l'instant, principalement AuthController
    $authController = new \App\Controller\AuthController($pdo);
    // Les autres contrôleurs peuvent être instanciés plus tard ou à la demande

    switch ($route) {
        case '/':
            $authController->handleHomepage(); // Gère la logique de redirection ou affichage login
            break;

        case '/login':
            if ($requestMethod === 'GET') {
                $authController->showLoginForm();
            } elseif ($requestMethod === 'POST') {
                $authController->processLogin();
            } else {
                http_response_code(405); require VIEW_PATH . 'errors/405.php';
            }
            break;

        case '/logout':
            if ($requestMethod === 'GET' || $requestMethod === 'POST') {
                 $authController->logout();
            } else {
                http_response_code(405); require VIEW_PATH . 'errors/405.php';
            }
            break;

        case '/dashboard':
            $authController->requireLogin(); // Protège cette route
            // Affiche la vue du dashboard via la méthode render (utilise le layout)
            $authController->render('dashboard/index', ['title' => 'Tableau de Bord']);
            break;

         // --- Ajouter les routes pour Filières ici plus tard ---
        case '/filieres':
            $authController->requireLogin(); // Exemple de protection
             echo "<h1>Liste des Filières (TODO)</h1>"; // Placeholder
             // Plus tard: $filiereController = new \App\Controller\FiliereController($pdo);
             //          $filiereController->list();
             break;

        default:
            http_response_code(404);
             // Utiliser render pour une meilleure intégration
             $authController->render('errors/404', ['title' => 'Page Non Trouvée']);
            break;
    }

} catch (\Throwable $e) { // Gestion globale des erreurs
    error_log("ERREUR GLOBALE : " . $e->getMessage() . "\n" . $e->getTraceAsString());
    http_response_code(500);
    if (defined('APP_ENV') && APP_ENV === 'development') {
        // Affichage détaillé en dev
        echo "<!DOCTYPE html><html><head><title>Erreur Serveur</title><style>body{font-family: sans-serif;} pre{background-color: #eee; padding: 10px; border: 1px solid #ccc; white-space: pre-wrap; word-wrap: break-word;}</style></head><body>";
        echo "<h1>Erreur 500 - Erreur Serveur</h1>";
        echo "<pre><strong>Type:</strong> " . get_class($e) . "</pre>";
        echo "<pre><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . " @ Ligne: " . htmlspecialchars((string)$e->getLine()) . "</pre>";
        echo "<h3>Trace de la Pile :</h3><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</body></html>";
    } else {
        // Affichage générique en production
        // Utiliser render serait mieux
        if (file_exists(VIEW_PATH . 'errors/500.php')) {
            require VIEW_PATH . 'errors/500.php';
        } else { echo "<h1>Erreur Serveur</h1><p>Une erreur interne est survenue.</p>"; }
    }
    exit;
}

?>