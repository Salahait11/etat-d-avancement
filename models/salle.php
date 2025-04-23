<?php
require_once __DIR__ . '/../config/database.php';

class Salle {
    private $db;
    private $id;
    private $nom;
    private $capacite;
    private $description;
    private $created_at;
    private $updated_at;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Créer une nouvelle salle
    public function create($data) {
        $sql = "INSERT INTO salle (nom, capacite, description, created_at, updated_at) 
                VALUES (:nom, :capacite, :description, NOW(), NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':capacite', $data['capacite']);
        $stmt->bindParam(':description', $data['description']);
        
        return $stmt->execute();
    }
    
    // Lire toutes les salles
    public function read() {
        $sql = "SELECT * FROM salle ORDER BY nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lire une salle par son ID
    public function readOne($id) {
        $sql = "SELECT * FROM salle WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result) {
            $this->id = $result['id'];
            $this->nom = $result['nom'];
            $this->capacite = $result['capacite'];
            $this->description = $result['description'];
            $this->created_at = $result['created_at'];
            $this->updated_at = $result['updated_at'];
        }
        
        return $result;
    }
    
    // Mettre à jour une salle
    public function update($id, $data) {
        $sql = "UPDATE salle 
                SET nom = :nom, 
                    capacite = :capacite, 
                    description = :description, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':capacite', $data['capacite']);
        $stmt->bindParam(':description', $data['description']);
        
        return $stmt->execute();
    }
    
    // Supprimer une salle
    public function delete($id) {
        $sql = "DELETE FROM salle WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
    
    // Rechercher des salles
    public function search($keyword) {
        $sql = "SELECT * FROM salle 
                WHERE nom LIKE :keyword 
                OR description LIKE :keyword 
                ORDER BY nom";
        
        $keyword = "%$keyword%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':keyword', $keyword);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Vérifier si une salle existe déjà
    public function checkExisting($nom) {
        $sql = "SELECT COUNT(*) FROM salle WHERE nom = :nom";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
    
    // Vérifier les conflits d'horaires
    public function checkDisponibilite($id, $date, $heure_debut, $heure_fin) {
        $sql = "SELECT COUNT(*) FROM seance 
                WHERE salle = :id 
                AND date = :date 
                AND (
                    (heure_debut <= :heure_debut AND heure_fin > :heure_debut) 
                    OR (heure_debut < :heure_fin AND heure_fin >= :heure_fin)
                    OR (heure_debut >= :heure_debut AND heure_fin <= :heure_fin)
                )";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':heure_debut', $heure_debut);
        $stmt->bindParam(':heure_fin', $heure_fin);
        $stmt->execute();
        
        return $stmt->fetchColumn() == 0;
    }
}
?> 