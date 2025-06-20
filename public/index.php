<?php // public/index.php

declare(strict_types=1);

// Configuration des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuration du logging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

// --- Chargement Initial ---
// Autoloader Composer (pour charger les classes App\...)
require_once __DIR__ . '/../vendor/autoload.php';

// Vérification de l'existence des fichiers de configuration
$configPath = __DIR__ . '/../config/app.php';
$dbConfigPath = __DIR__ . '/../config/database.php';

if (!file_exists($configPath)) {
    die('Erreur : Le fichier de configuration app.php est manquant.');
}

if (!file_exists($dbConfigPath)) {
    die('Erreur : Le fichier de configuration database.php est manquant.');
}

// Configuration de l'application (définit BASE_URL, APP_ENV)
$appConfig = require_once $configPath;

// Vérification de la configuration
if (!defined('BASE_URL')) {
    die('Erreur : BASE_URL n\'est pas définie dans la configuration.');
}

define('BASE_PATH', dirname(__DIR__)); // Définit le chemin racine du projet
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


// --- Nouveau Routage avec FastRoute ---
use FastRoute\RouteCollector;

// Log de débogage
error_log("=== DÉBUT DE LA REQUÊTE ===");
error_log("URI demandée : " . $requestUri);
error_log("Méthode HTTP : " . $requestMethod);
error_log("Chemin de base : " . $urlBasePath);
error_log("Route calculée : " . $route);

try {
    $dispatcher = FastRoute\simpleDispatcher(function(RouteCollector $r) {
        // Page d'accueil
        $r->addRoute('GET', '/', function() {
            (new \App\Controller\HomeController())->index();
        });
        $r->addRoute('GET', '/test-route', function() {
            (new \App\Controller\HomeController())->test();
        });

        // Authentification
        $r->addRoute(['GET', 'POST'], '/login', function() {
            $ctrl = new \App\Controller\AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $ctrl->showLoginForm();
            } else {
                $ctrl->processLogin();
            }
        });
        $r->addRoute('GET', '/logout', function() {
            (new \App\Controller\AuthController())->logout();
        });
        $r->addRoute('GET', '/dashboard', function() {
            (new \App\Controller\AuthController())->showDashboard();
        });

        // Utilisateurs
        $r->addRoute('GET', '/utilisateurs', function() {
            (new \App\Controller\UtilisateurController())->index();
        });
        $r->addRoute('GET', '/utilisateurs/create', function() {
            (new \App\Controller\UtilisateurController())->create();
        });
        $r->addRoute('POST', '/utilisateurs/store', function() {
            (new \App\Controller\UtilisateurController())->store();
        });
        $r->addRoute('GET', '/utilisateurs/edit/{id:\d+}', function($args) {
            (new \App\Controller\UtilisateurController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/utilisateurs/update/{id:\d+}', function($args) {
            (new \App\Controller\UtilisateurController())->update((int)$args['id']);
        });
        $r->addRoute(['POST','GET'], '/utilisateurs/delete/{id:\d+}', function($args) {
            (new \App\Controller\UtilisateurController())->delete((int)$args['id']);
        });

        // Formateurs
        $r->addRoute('GET', '/formateurs', function() {
            (new \App\Controller\FormateurController())->index();
        });
        $r->addRoute('GET', '/api/formateurs/{module_id:\d+}', function($args) {
            (new \App\Controller\FormateurController())->getByModule((int)$args['module_id']);
        });
        $r->addRoute('GET', '/formateurs/add', function() {
            (new \App\Controller\FormateurController())->add();
        });
        $r->addRoute('POST', '/formateurs/store', function() {
            (new \App\Controller\FormateurController())->store();
        });
        $r->addRoute('GET', '/formateurs/edit/{id:\\d+}', function($args) {
            (new \App\Controller\FormateurController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/formateurs/update/{id:\\d+}', function($args) {
            (new \App\Controller\FormateurController())->update((int)$args['id']);
        });
        $r->addRoute('POST', '/formateurs/delete/{id:\\d+}', function($args) {
            (new \App\Controller\FormateurController())->delete((int)$args['id']);
        });

        // Objectifs pédagogiques
        $r->addRoute('GET', '/objectifs-pedagogiques', function() {
            (new \App\Controller\ObjectifPedagogiqueController())->index();
        });
        $r->addRoute('GET', '/objectifs-pedagogiques/add', function() {
            (new \App\Controller\ObjectifPedagogiqueController())->add();
        });
        $r->addRoute('POST', '/objectifs-pedagogiques/store', function() {
            (new \App\Controller\ObjectifPedagogiqueController())->store();
        });
        $r->addRoute('GET', '/objectifs-pedagogiques/edit/{id:\d+}', function($args) {
            (new \App\Controller\ObjectifPedagogiqueController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/objectifs-pedagogiques/update/{id:\d+}', function($args) {
            (new \App\Controller\ObjectifPedagogiqueController())->update((int)$args['id']);
        });
        $r->addRoute(['POST','GET'], '/objectifs-pedagogiques/delete/{id:\d+}', function($args) {
            (new \App\Controller\ObjectifPedagogiqueController())->delete((int)$args['id']);
        });

        // Modules (CRUD)
        $r->addRoute('GET', '/modules', function() {
            (new \App\Controller\ModuleController())->list();
        });
        $r->addRoute('GET', '/modules/add', function() {
            (new \App\Controller\ModuleController())->add();
        });
        $r->addRoute('POST', '/modules/store', function() {
            (new \App\Controller\ModuleController())->store();
        });
        $r->addRoute('GET', '/modules/edit/{id:\d+}', function($args) {
            (new \App\Controller\ModuleController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/modules/update/{id:\d+}', function($args) {
            (new \App\Controller\ModuleController())->update((int)$args['id']);
        });
        $r->addRoute(['POST','GET'], '/modules/delete/{id:\d+}', function($args) {
            (new \App\Controller\ModuleController())->delete((int)$args['id']);
        });

        // Filieres (CRUD)
        $r->addRoute('GET', '/filieres', function() {
            (new \App\Controller\FiliereController())->index();
        });
        $r->addRoute('GET', '/filieres/create', function() {
            (new \App\Controller\FiliereController())->create();
        });
        $r->addRoute('POST', '/filieres/store', function() {
            (new \App\Controller\FiliereController())->store();
        });
        $r->addRoute('GET', '/filieres/edit/{id:\d+}', function($args) {
            (new \App\Controller\FiliereController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/filieres/update/{id:\d+}', function($args) {
            (new \App\Controller\FiliereController())->update((int)$args['id']);
        });
        $r->addRoute(['POST','GET'], '/filieres/delete/{id:\d+}', function($args) {
            (new \App\Controller\FiliereController())->delete((int)$args['id']);
        });

        // États d'avancement
        $r->addRoute('GET', '/etats-avancement', function() {
            (new \App\Controller\EtatAvancementController())->index();
        });
        $r->addRoute('GET', '/etats-avancement/add', function() {
            (new \App\Controller\EtatAvancementController())->add();
        });
        $r->addRoute('POST', '/etats-avancement/store', function() {
            (new \App\Controller\EtatAvancementController())->store();
        });
        $r->addRoute('GET', '/etats-avancement/view/{id:\d+}', function($args) {
            (new \App\Controller\EtatAvancementController())->view((int)$args['id']);
        });
        $r->addRoute('GET', '/etats-avancement/edit/{id:\d+}', function($args) {
            (new \App\Controller\EtatAvancementController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/etats-avancement/update/{id:\d+}', function($args) {
            (new \App\Controller\EtatAvancementController())->update((int)$args['id']);
        });
        $r->addRoute('POST', '/etats-avancement/delete/{id:\d+}', function($args) {
            (new \App\Controller\EtatAvancementController())->delete((int)$args['id']);
        });

        // Ajoutez ici les routes pour les autres entités (contenus-seance, moyens-didactiques, etc.)
        // Contenus de séance (CRUD)
        $r->addRoute('GET', '/contenus-seance', function() {
            (new \App\Controller\ContenuSeanceController())->index();
        });
        $r->addRoute('GET', '/contenus-seance/add', function() {
            (new \App\Controller\ContenuSeanceController())->add();
        });
        $r->addRoute('POST', '/contenus-seance/store', function() {
            (new \App\Controller\ContenuSeanceController())->store();
        });
        $r->addRoute('GET', '/contenus-seance/edit/{id:\\d+}', function($args) {
            (new \App\Controller\ContenuSeanceController())->edit($args['id']);
        });
        $r->addRoute('POST', '/contenus-seance/update/{id:\\d+}', function($args) {
            (new \App\Controller\ContenuSeanceController())->update($args['id']);
        });
        $r->addRoute('POST', '/contenus-seance/delete/{id:\\d+}', function($args) {
            (new \App\Controller\ContenuSeanceController())->delete($args['id']);
        });

        // Moyens didactiques (CRUD)
        $r->addRoute('GET', '/moyens-didactiques', function() {
            (new \App\Controller\MoyenDidactiqueController())->index();
        });
        $r->addRoute('GET', '/moyens-didactiques/add', function() {
            (new \App\Controller\MoyenDidactiqueController())->add();
        });
        $r->addRoute('POST', '/moyens-didactiques/store', function() {
            (new \App\Controller\MoyenDidactiqueController())->store();
        });
        $r->addRoute('GET', '/moyens-didactiques/edit/{id:\d+}', function($args) {
            (new \App\Controller\MoyenDidactiqueController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/moyens-didactiques/update/{id:\d+}', function($args) {
            (new \App\Controller\MoyenDidactiqueController())->update((int)$args['id']);
        });
        $r->addRoute(['POST','GET'], '/moyens-didactiques/delete/{id:\d+}', function($args) {
            (new \App\Controller\MoyenDidactiqueController())->delete((int)$args['id']);
        });

        // Stratégies d'évaluation (CRUD)
        $r->addRoute('GET', '/strategies-evaluation', function() {
            (new \App\Controller\StrategieEvaluationController())->index();
        });
        $r->addRoute('GET', '/strategies-evaluation/add', function() {
            (new \App\Controller\StrategieEvaluationController())->add();
        });
        $r->addRoute('POST', '/strategies-evaluation/store', function() {
            (new \App\Controller\StrategieEvaluationController())->store();
        });
        $r->addRoute('GET', '/strategies-evaluation/edit/{id:\d+}', function($args) {
            (new \App\Controller\StrategieEvaluationController())->edit((int)$args['id']);
        });
        $r->addRoute('POST', '/strategies-evaluation/update/{id:\d+}', function($args) {
            (new \App\Controller\StrategieEvaluationController())->update((int)$args['id']);
        });
        $r->addRoute(['POST','GET'], '/strategies-evaluation/delete/{id:\d+}', function($args) {
            (new \App\Controller\StrategieEvaluationController())->delete((int)$args['id']);
        });

        // Routes du profil
        $r->addRoute('GET', '/profile', function() {
            (new \App\Controller\ProfileController())->index();
        });
        $r->addRoute('GET', '/profile/edit', function() {
            (new \App\Controller\ProfileController())->edit();
        });
        $r->addRoute('POST', '/profile/update', function() {
            (new \App\Controller\ProfileController())->update();
        });

    });

    // --- Dispatching FastRoute ---
    $httpMethod = $_SERVER['REQUEST_METHOD'];
    $uri = $route;
    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    $routeInfo = $dispatcher->dispatch($httpMethod, $uri);
    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            http_response_code(404);
            echo "<h1>404 - Page Non Trouvée</h1>";
            break;
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            http_response_code(405);
            echo "<h1>405 - Méthode Non Autorisée</h1>";
            break;
        case FastRoute\Dispatcher::FOUND:
            $handler = $routeInfo[1];
            $vars = $routeInfo[2];
            if (is_callable($handler)) {
                $handler($vars);
            } else {
                throw new Exception("Handler non callable pour la route.");
            }
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo "<h1>Erreur interne du serveur</h1>";
    echo "<p>Une erreur est survenue : " . $e->getMessage() . "</p>";
}

?>