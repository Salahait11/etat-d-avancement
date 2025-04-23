<?php // public/index.php

declare(strict_types=1);

// --- Initialisation ---

// Démarrer la session si elle n'est pas déjà active.
// Nécessaire pour les messages flash, l'état de connexion, etc.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Charger l'autoloader de Composer pour charger automatiquement les classes (PSR-4)
// __DIR__ donne le répertoire courant (public), '/../' remonte d'un niveau.
require_once __DIR__ . '/../vendor/autoload.php';

// Charger la configuration de la base de données.
// Rend la variable $pdo (instance PDO) disponible.
require_once __DIR__ . '/../config/database.php'; // Assurez-vous que ce fichier existe et fonctionne

// --- Constantes Utiles ---

// Chemin racine du projet (ex: C:/wamp64/www/gestion_ecoles/)
define('BASE_PATH', dirname(__DIR__));
// Chemin vers le dossier des vues
define('VIEW_PATH', BASE_PATH . '/src/View/');
// Définir l'environnement ('development' ou 'production')
// Ceci contrôle l'affichage des erreurs détaillées. Mettre 'production' en ligne.
define('APP_ENV', 'development'); // Changez en 'production' sur le serveur live

// --- Récupération de la Requête HTTP ---

// URI complète demandée (ex: /gestion_ecoles/public/login?param=1)
$requestUri = $_SERVER['REQUEST_URI'];
// Extrait uniquement le chemin de l'URI (ex: /gestion_ecoles/public/login)
$requestPath = parse_url($requestUri, PHP_URL_PATH);
// Méthode HTTP utilisée (GET, POST, PUT, DELETE, etc.)
$requestMethod = $_SERVER['REQUEST_METHOD'];

// --- Calcul de la Route ---

// Normaliser le chemin : supprimer le slash de fin sauf pour la racine
$route = ($requestPath === '/' || $requestPath === '') ? '/' : rtrim($requestPath, '/');

// Ajustement si le projet est dans un sous-dossier de la racine web (ex: /public/)
// Adaptez '/gestion_ecoles/public' si votre structure d'URL locale est différente.
// Si vous utilisez des Virtual Hosts, $basePath peut être juste '/'.
$basePath = '/gestion_ecoles/public'; // IMPORTANT: Adaptez ceci à votre configuration locale
if (str_starts_with($route, $basePath)) {
   // Enlève le préfixe du sous-dossier pour obtenir la route applicative (ex: /login)
   $route = substr($route, strlen($basePath));
   // Si après suppression, la route est vide, c'est qu'on est à la racine du sous-dossier
   if (empty($route)) $route = '/';
}
// Si vous utilisez un Virtual Host pointant directement sur /public, la logique $basePath n'est pas nécessaire
// et $route = rtrim($requestPath, '/'); (avec gestion de '/' vide) suffira.

// --- Routage Principal ---

// Utilisation d'un bloc try-catch global pour attraper toutes les erreurs non gérées
try {
    // Instanciation des contrôleurs requis.
    // Pourrait être amélioré avec un conteneur d'injection de dépendances ou un chargement à la demande.
    // Le contrôleur d'authentification est souvent nécessaire.
    $authController = new \App\Controller\AuthController($pdo);
    // Les autres contrôleurs peuvent être instanciés dans leurs cases respectives si besoin.
    // Exemple: $filiereController = null;

    // Structure de décision basée sur la route calculée
    switch ($route) {
        case '/':
            // Gère la page d'accueil. La logique (rediriger si connecté, sinon afficher login)
            // est maintenant DANS la méthode handleHomepage du contrôleur.
            $authController->handleHomepage();
            break;

        case '/login':
            // Gère l'affichage du formulaire (GET) et sa soumission (POST)
            if ($requestMethod === 'GET') {
                $authController->showLoginForm();
            } elseif ($requestMethod === 'POST') {
                $authController->processLogin();
            } else {
                // Méthode HTTP non autorisée pour cette route
                http_response_code(405);
                require VIEW_PATH . 'errors/405.php'; // Charge la vue d'erreur 405
            }
            break;

        case '/logout':
            // Gère la déconnexion. Peut être appelée via GET (lien) ou POST (bouton).
            if ($requestMethod === 'GET' || $requestMethod === 'POST') {
                 $authController->logout();
            } else {
                http_response_code(405);
                require VIEW_PATH . 'errors/405.php';
            }
            break;

        case '/dashboard':
            // Protège la route : l'utilisateur doit être connecté
            $authController->requireLogin();
            // Affiche la vue du tableau de bord via la méthode render du BaseController
            // Cela assure que le layout est utilisé et que les variables ($isLoggedIn, $currentUser) sont disponibles.
            $authController->render('dashboard/index', ['title' => 'Tableau de Bord']); // Passe le titre
            break;

        // --- Routes pour les Filières (Exemple à implémenter) ---
        case '/filieres':
             $authController->requireLogin(); // Protéger cette section
             // Ici, on instancierait et appellerait le FiliereController
             // $filiereController = new \App\Controller\FiliereController($pdo);
             // $filiereController->list();
             echo "Page Liste des Filières (TODO)"; // Placeholder
             break;

        case '/filieres/add':
              $authController->requireLogin(); // Protéger
              // $filiereController = new \App\Controller\FiliereController($pdo);
              if ($requestMethod === 'GET') {
                   // $filiereController->showAddForm();
                   echo "Page Formulaire Ajout Filière (GET - TODO)"; // Placeholder
              } elseif ($requestMethod === 'POST') {
                   // $filiereController->processAddForm();
                   echo "Traitement Ajout Filière (POST - TODO)"; // Placeholder
              } else {
                  http_response_code(405); require VIEW_PATH . 'errors/405.php';
              }
              break;
        // Il faudra un routeur plus avancé pour gérer /filieres/edit/{id} etc.

        // --- Route Non Trouvée ---
        default:
            http_response_code(404); // Définit le code de statut HTTP
            // Idéalement, utiliser la méthode render ou un contrôleur d'erreur pour l'affichage
            // $authController->render('errors/404', ['title' => 'Page Non Trouvée']);
            // Pour l'instant, un simple require :
            require VIEW_PATH . 'errors/404.php'; // Charge la vue d'erreur 404
            break;
    }

} catch (\Throwable $e) { // Capture toutes les erreurs et exceptions non interceptées
    // Loguer l'erreur de manière détaillée (important pour le débogage)
    // Assurez-vous que PHP a les droits d'écrire dans le fichier/répertoire de log du serveur.
    error_log("ERREUR GLOBALE : " . $e->getMessage() . "\n" . $e->getTraceAsString());

    // Envoyer un code de réponse HTTP 500 (Erreur Interne du Serveur)
    http_response_code(500);

    // Afficher une page d'erreur appropriée selon l'environnement
    if (defined('APP_ENV') && APP_ENV === 'development') {
        // En DÉVELOPPEMENT : Afficher les détails de l'erreur pour faciliter le débogage
        // ATTENTION: Ne JAMAIS faire ça en production !
        echo "<!DOCTYPE html><html><head><title>Erreur Serveur</title><style>body{font-family: sans-serif;} pre{background-color: #eee; padding: 10px; border: 1px solid #ccc; white-space: pre-wrap; word-wrap: break-word;}</style></head><body>";
        echo "<h1>Erreur Serveur</h1>";
        echo "<p>Une erreur critique est survenue. Détails (Mode Développement) :</p>";
        echo "<pre><strong>Type:</strong> " . get_class($e) . "</pre>";
        echo "<pre><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</pre>";
        echo "<pre><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . " @ Ligne: " . htmlspecialchars((string)$e->getLine()) . "</pre>";
        echo "<h3>Trace de la Pile :</h3><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        echo "</body></html>";
    } else {
        // En PRODUCTION : Afficher une page d'erreur générique et conviviale
        // Vous pouvez utiliser la méthode render ici aussi si vous avez un contrôleur d'erreur
        // $errorController->render('errors/500', ['title' => 'Erreur Serveur']);
        // Pour l'instant, un simple require :
        if (file_exists(VIEW_PATH . 'errors/500.php')) {
            require VIEW_PATH . 'errors/500.php';
        } else {
            // Fallback très basique si même la vue 500 est introuvable
            echo "<!DOCTYPE html><html><head><title>Erreur Serveur</title></head><body><h1>Erreur Serveur</h1><p>Une erreur interne est survenue. Veuillez réessayer plus tard.</p></body></html>";
        }
    }
    exit; // Arrêter l'exécution après avoir géré l'erreur critique
}

?>