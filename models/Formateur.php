<?php
require_once __DIR__ . '/BaseModel.php';

class Formateur extends BaseModel {
    protected $table = 'formateur';
    
    public function __construct() {
        parent::__construct();
    }
    
    // Créer un nouveau formateur
    public function create($data) {
        try {
            $this->db->beginTransaction();
            
            // Créer d'abord l'utilisateur
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'],
                'role' => 'formateur'
            ];
            
            $userId = $this->createUser($userData);
            if (!$userId) {
                throw new Exception("Erreur lors de la création de l'utilisateur");
            }
            
            // Créer le formateur
            $query = "INSERT INTO formateur (id_utilisateur, specialite, commentaires, created_at, updated_at) 
                     VALUES (:id_utilisateur, :specialite, :commentaires, NOW(), NOW())";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_utilisateur', $userId);
            $stmt->bindParam(':specialite', $data['specialite']);
            $stmt->bindParam(':commentaires', $data['commentaires']);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la création du formateur");
            }
            
            $formateurId = $this->db->lastInsertId();
            
            // Assigner les modules
            if (!empty($data['modules'])) {
                $query = "INSERT INTO formateur_module (id_formateur, id_module) VALUES (:id_formateur, :id_module)";
                $stmt = $this->db->prepare($query);
                
                foreach ($data['modules'] as $moduleId) {
                    $stmt->bindParam(':id_formateur', $formateurId);
                    $stmt->bindParam(':id_module', $moduleId);
                    if (!$stmt->execute()) {
                        throw new Exception("Erreur lors de l'assignation des modules");
                    }
                }
            }
            
            $this->db->commit();
            return $formateurId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Lire un formateur par son ID
    public function readOne($id) {
        $query = "SELECT f.*, u.nom, u.prenom, u.email, u.telephone 
                 FROM formateur f 
                 JOIN utilisateur u ON f.id_utilisateur = u.id 
                 WHERE f.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $formateur = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($formateur) {
            // Récupérer les modules assignés
            $formateur['modules'] = $this->getModulesByFormateur($id);
        }
        
        return $formateur;
    }
    
    // Mettre à jour un formateur
    public function update($id, $data) {
        try {
            $this->db->beginTransaction();
            
            // Récupérer l'ID de l'utilisateur
            $query = "SELECT id_utilisateur FROM formateur WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $userId = $stmt->fetchColumn();
            
            if (!$userId) {
                throw new Exception("Formateur non trouvé");
            }
            
            // Mettre à jour l'utilisateur
            $userData = [
                'id' => $userId,
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone']
            ];
            
            if (!$this->updateUser($userData)) {
                throw new Exception("Erreur lors de la mise à jour de l'utilisateur");
            }
            
            // Mettre à jour le formateur
            $query = "UPDATE formateur 
                     SET specialite = :specialite, 
                         commentaires = :commentaires, 
                         updated_at = NOW() 
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':specialite', $data['specialite']);
            $stmt->bindParam(':commentaires', $data['commentaires']);
            
            if (!$stmt->execute()) {
                throw new Exception("Erreur lors de la mise à jour du formateur");
            }
            
            // Mettre à jour les modules
            $query = "DELETE FROM formateur_module WHERE id_formateur = :id_formateur";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_formateur', $id);
            $stmt->execute();
            
            if (!empty($data['modules'])) {
                $query = "INSERT INTO formateur_module (id_formateur, id_module) VALUES (:id_formateur, :id_module)";
                $stmt = $this->db->prepare($query);
                
                foreach ($data['modules'] as $moduleId) {
                    $stmt->bindParam(':id_formateur', $id);
                    $stmt->bindParam(':id_module', $moduleId);
                    if (!$stmt->execute()) {
                        throw new Exception("Erreur lors de l'assignation des modules");
                    }
                }
            }
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Supprimer un formateur
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Récupérer l'ID de l'utilisateur
            $query = "SELECT id_utilisateur FROM formateur WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $userId = $stmt->fetchColumn();
            
            if (!$userId) {
                throw new Exception("Formateur non trouvé");
            }
            
            // Supprimer les assignations de modules
            $query = "DELETE FROM formateur_module WHERE id_formateur = :id_formateur";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id_formateur', $id);
            $stmt->execute();
            
            // Supprimer le formateur
            $query = "DELETE FROM formateur WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            // Supprimer l'utilisateur
            $query = "DELETE FROM utilisateur WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $userId);
            $stmt->execute();
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Vérifier si un email existe déjà
    public function checkEmailExists($email, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM utilisateur u 
                 JOIN formateur f ON u.id = f.id_utilisateur 
                 WHERE u.email = :email";
        
        if ($excludeId) {
            $query .= " AND f.id != :exclude_id";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
    // Obtenir les modules d'un formateur
    public function getModulesByFormateur($id) {
        $query = "SELECT m.* FROM module m 
                 JOIN formateur_module fm ON m.id = fm.id_module 
                 WHERE fm.id_formateur = :id_formateur 
                 ORDER BY m.nom";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_formateur', $id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtenir la liste des spécialités
    public function getSpecialites() {
        $query = "SELECT DISTINCT specialite FROM formateur ORDER BY specialite";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Obtenir le nombre de séances d'un formateur
    public function getSeancesCount($id, $period = 'month') {
        $query = "SELECT COUNT(*) FROM seance WHERE id_formateur = :id_formateur";
        
        if ($period === 'month') {
            $query .= " AND date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_formateur', $id);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    
    // Obtenir le nombre d'heures de formation
    public function getHeuresFormation($id) {
        $query = "SELECT SUM(TIMESTAMPDIFF(HOUR, heure_debut, heure_fin)) 
                 FROM seance 
                 WHERE id_formateur = :id_formateur";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_formateur', $id);
        $stmt->execute();
        
        return $stmt->fetchColumn() ?: 0;
    }
    
    // Méthodes privées pour la gestion des utilisateurs
    private function createUser($data) {
        $query = "INSERT INTO utilisateur (nom, prenom, email, telephone, role, created_at, updated_at) 
                 VALUES (:nom, :prenom, :email, :telephone, :role, NOW(), NOW())";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':telephone', $data['telephone']);
        $stmt->bindParam(':role', $data['role']);
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    private function updateUser($data) {
        $query = "UPDATE utilisateur 
                 SET nom = :nom, 
                     prenom = :prenom, 
                     email = :email, 
                     telephone = :telephone, 
                     updated_at = NOW() 
                 WHERE id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $data['id']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':telephone', $data['telephone']);
        
        return $stmt->execute();
    }
}
?> 