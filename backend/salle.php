<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/salle.php';

$database = Database::getInstance();
$db = $database->getConnection();
$salle = new Salle();

$method = $_SERVER['REQUEST_METHOD'];
$response = array();

switch($method) {
    case 'GET':
        if(isset($_GET['id'])) {
            $result = $salle->readOne($_GET['id']);
            if($result) {
                $response = $result;
            } else {
                http_response_code(404);
                $response['message'] = "Salle non trouvée";
            }
        } else if(isset($_GET['search'])) {
            $response = $salle->search($_GET['search']);
        } else {
            $response = $salle->read();
        }
        break;
        
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(!isset($data['nom']) || !isset($data['capacite'])) {
            http_response_code(400);
            $response['message'] = "Le nom et la capacité sont requis";
            break;
        }
        
        if($salle->checkExisting($data['nom'])) {
            http_response_code(400);
            $response['message'] = "Une salle avec ce nom existe déjà";
            break;
        }
        
        if($salle->create($data)) {
            http_response_code(201);
            $response['message'] = "Salle créée avec succès";
        } else {
            http_response_code(503);
            $response['message'] = "Impossible de créer la salle";
        }
        break;
        
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(!isset($data['id']) || !isset($data['nom']) || !isset($data['capacite'])) {
            http_response_code(400);
            $response['message'] = "L'ID, le nom et la capacité sont requis";
            break;
        }
        
        if(!$salle->readOne($data['id'])) {
            http_response_code(404);
            $response['message'] = "Salle non trouvée";
            break;
        }
        
        if($salle->update($data['id'], $data)) {
            $response['message'] = "Salle mise à jour avec succès";
        } else {
            http_response_code(503);
            $response['message'] = "Impossible de mettre à jour la salle";
        }
        break;
        
    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        
        if(!isset($data['id'])) {
            http_response_code(400);
            $response['message'] = "L'ID est requis";
            break;
        }
        
        if(!$salle->readOne($data['id'])) {
            http_response_code(404);
            $response['message'] = "Salle non trouvée";
            break;
        }
        
        if($salle->delete($data['id'])) {
            $response['message'] = "Salle supprimée avec succès";
        } else {
            http_response_code(503);
            $response['message'] = "Impossible de supprimer la salle";
        }
        break;
        
    default:
        http_response_code(405);
        $response['message'] = "Méthode non autorisée";
        break;
}

echo json_encode($response);
?> 