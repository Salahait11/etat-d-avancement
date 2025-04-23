<?php
// Définir le chemin de base
define('BASE_PATH', __DIR__);

// Inclure les fichiers nécessaires
require_once BASE_PATH . '/config/database.php';

// Démarrer la session
session_start();

// Déterminer la route demandée
$route = isset($_GET['route']) ? $_GET['route'] : 'accueil';

// Router simple
switch($route) {
    case 'accueil':
        require_once BASE_PATH . '/controllers/AccueilController.php';
        $controller = new AccueilController();
        $controller->index();
        break;
        
    case 'utilisateurs':
        require_once BASE_PATH . '/controllers/UtilisateurController.php';
        $controller = new UtilisateurController();
        $controller->index();
        break;
        
    case 'filieres':
        require_once BASE_PATH . '/controllers/FiliereController.php';
        $controller = new FiliereController();
        $controller->index();
        break;
        
    case 'modules':
        require_once BASE_PATH . '/controllers/ModuleController.php';
        $controller = new ModuleController();
        $controller->index();
        break;
        
    case 'formateurs':
        require_once BASE_PATH . '/controllers/FormateurController.php';
        $controller = new FormateurController();
        $controller->index();
        break;
        
    case 'etats-avancement':
        require_once BASE_PATH . '/controllers/EtatAvancementController.php';
        $controller = new EtatAvancementController();
        $controller->index();
        break;
        
    case 'salles':
        require_once BASE_PATH . '/controllers/SalleController.php';
        $controller = new SalleController();
        
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        switch($action) {
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'show':
                $controller->show($id);
                break;
            case 'edit':
                $controller->edit($id);
                break;
            case 'update':
                $controller->update($id);
                break;
            case 'delete':
                $controller->delete($id);
                break;
            case 'search':
                $controller->search();
                break;
            default:
                $controller->index();
        }
        break;
        
    case 'competences':
        require_once BASE_PATH . '/controllers/CompetenceController.php';
        $controller = new CompetenceController();
        
        $action = isset($_GET['action']) ? $_GET['action'] : 'index';
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        
        switch($action) {
            case 'create':
                $controller->create();
                break;
            case 'store':
                $controller->store();
                break;
            case 'show':
                $controller->show($id);
                break;
            case 'edit':
                $controller->edit($id);
                break;
            case 'update':
                $controller->update($id);
                break;
            case 'delete':
                $controller->delete($id);
                break;
            case 'search':
                $controller->search();
                break;
            case 'by-module':
                $id_module = isset($_GET['id_module']) ? $_GET['id_module'] : null;
                $controller->getByModule($id_module);
                break;
            case 'by-niveau':
                $niveau = isset($_GET['niveau']) ? $_GET['niveau'] : null;
                $controller->getByNiveau($niveau);
                break;
            default:
                $controller->index();
        }
        break;
        
    default:
        // Page 404
        header("HTTP/1.0 404 Not Found");
        require_once BASE_PATH . '/views/404.php';
        break;
}
?> 