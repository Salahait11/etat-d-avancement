<?php // public/index.php

declare(strict_types=1); // Active le typage strict (bonne pratique)

// 1. Démarrer la session (si nécessaire, souvent pour l'authentification)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Charger l'autoloader de Composer
require_once __DIR__ . '/../vendor/autoload.php';

// 3. Charger la configuration (accès DB notamment)
// Le fichier database.php rend la variable $pdo disponible globalement,
// ce n'est pas idéal en POO stricte, mais simple pour commencer.
// Une meilleure approche serait d'injecter la connexion où elle est nécessaire (Dependency Injection)
// ou d'utiliser un conteneur de services. Pour l'instant, on fait simple :
require_once __DIR__ . '/../config/database.php'; // $pdo est maintenant disponible

// 4. Définir des constantes ou charger d'autres configurations si besoin
define('BASE_PATH', dirname(__DIR__)); // Chemin racine du projet (gestion_ecoles/)
define('VIEW_PATH', BASE_PATH . '/src/View/'); // Chemin vers les vues

// 5. Récupérer l'URL demandée (Routing très basique pour l'instant)
$requestUri = $_SERVER['REQUEST_URI'];
$requestPath = parse_url($requestUri, PHP_URL_PATH); // Extrait le chemin de l'URL (ex: /login, /filieres/list)

// ----- Début du Routing Simplifié -----
// (On créera un vrai routeur dans src/Core/ plus tard)

// Normaliser le chemin (enlever le slash de fin s'il y en a un, sauf pour la racine)
$route = ($requestPath === '/' || $requestPath === '') ? '/' : rtrim($requestPath, '/');

// Définir les routes et les associer aux contrôleurs/méthodes
// Ceci sera externalisé dans un fichier de routes ou géré par une classe Router
try {
    // Exemple de routes (à adapter et compléter)
    switch ($route) {
        case '/':
            // Page d'accueil (peut-être le tableau de bord si connecté, sinon login)
            $controller = new App\Controller\HomeController($pdo); // Exemple, on n'a pas encore créé HomeController
            $controller->index();
            break;

        case '/login':
            $controller = new App\Controller\AuthController($pdo); // On passera $pdo au contrôleur
            $controller->login(); // Méthode pour afficher/traiter le formulaire de login
            break;

        case '/logout':
            $controller = new App\Controller\AuthController($pdo);
            $controller->logout();
            break;

        case '/filieres':
            $controller = new App\Controller\FiliereController($pdo);
            $controller->list(); // Méthode pour lister les filières
            break;

        case '/filieres/add':
            $controller = new App\Controller\FiliereController($pdo);
            $controller->add(); // Méthode pour afficher/traiter l'ajout de filière
            break;

        // Ajouter d'autres routes ici...
        // /filieres/edit/{id} nécessitera un routeur plus avancé gérant les paramètres

        default:
            // Gérer les erreurs 404 Not Found
            http_response_code(404);
            // On pourrait charger un contrôleur d'erreur ou une vue spécifique
            require VIEW_PATH . 'errors/404.php'; // Assurez-vous que ce fichier existe
            break;
    }

} catch (\Throwable $e) { // Capture toutes les erreurs (Exceptions et Errors)
    // Gérer les erreurs 500 Internal Server Error
    error_log("Erreur Globale : " . $e->getMessage() . "\n" . $e->getTraceAsString()); // Log l'erreur
    http_response_code(500);
    // Afficher une page d'erreur générique (NE PAS afficher $e->getMessage() en production)
    require VIEW_PATH . 'errors/500.php'; // Assurez-vous que ce fichier existe
}

// ----- Fin du Routing Simplifié -----

?>