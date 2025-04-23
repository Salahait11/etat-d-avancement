<?php
require_once __DIR__ . '/BaseModel.php';

class Competence extends BaseModel {
    protected $table = 'competence';
    
    // Propriétés
    public $id;
    public $code;
    public $intitule;
    public $description;
    public $niveau;
    public $id_module;
    public $created_at;
    public $updated_at;
    
    // Constructeur
    public function __construct() {
        parent::__construct();
    }
    
    // Créer une nouvelle compétence
    public function create($data) {
        // Vérifier si la compétence existe déjà
        if($this->checkExisting($data['code'], $data['id_module'])) {
            return false;
        }
        
        // Protection contre les injections XSS
        $data['code'] = htmlspecialchars($data['code']);
        $data['intitule'] = htmlspecialchars($data['intitule']);
        $data['description'] = htmlspecialchars($data['description']);
        $data['niveau'] = htmlspecialchars($data['niveau']);
        
        return parent::create($data);
    }
    
    // Lire toutes les compétences
    public function read() {
        $query = "SELECT c.*, m.titre as module_titre 
                 FROM " . $this->table . " c
                 LEFT JOIN module m ON c.id_module = m.id
                 ORDER BY c.code, c.niveau";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Lire une compétence par son ID
    public function readOne($id) {
        $query = "SELECT c.*, m.titre as module_titre 
                 FROM " . $this->table . " c
                 LEFT JOIN module m ON c.id_module = m.id
                 WHERE c.id = :id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($row) {
            $this->id = $row['id'];
            $this->code = $row['code'];
            $this->intitule = $row['intitule'];
            $this->description = $row['description'];
            $this->niveau = $row['niveau'];
            $this->id_module = $row['id_module'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
        }
        
        return $row;
    }
    
    // Mettre à jour une compétence
    public function update($id, $data) {
        // Protection contre les injections XSS
        $data['code'] = htmlspecialchars($data['code']);
        $data['intitule'] = htmlspecialchars($data['intitule']);
        $data['description'] = htmlspecialchars($data['description']);
        $data['niveau'] = htmlspecialchars($data['niveau']);
        
        return parent::update($id, $data);
    }
    
    // Supprimer une compétence
    public function delete($id) {
        return parent::delete($id);
    }
    
    // Obtenir les compétences par module
    public function getByModule($id_module) {
        $query = "SELECT c.*, m.titre as module_titre 
                 FROM " . $this->table . " c
                 LEFT JOIN module m ON c.id_module = m.id
                 WHERE c.id_module = :id_module
                 ORDER BY c.code, c.niveau";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":id_module", $id_module);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtenir les compétences par niveau
    public function getByNiveau($niveau) {
        $query = "SELECT c.*, m.titre as module_titre 
                 FROM " . $this->table . " c
                 LEFT JOIN module m ON c.id_module = m.id
                 WHERE c.niveau = :niveau
                 ORDER BY c.code";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":niveau", $niveau);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Rechercher des compétences
    public function search($keyword) {
        $query = "SELECT c.*, m.titre as module_titre 
                 FROM " . $this->table . " c
                 LEFT JOIN module m ON c.id_module = m.id
                 WHERE c.code LIKE :keyword 
                 OR c.intitule LIKE :keyword 
                 OR c.description LIKE :keyword 
                 OR m.titre LIKE :keyword
                 ORDER BY c.code, c.niveau";
        
        $keyword = "%{$keyword}%";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":keyword", $keyword);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Vérifier si une compétence existe déjà
    public function checkExisting($code, $id_module) {
        $query = "SELECT COUNT(*) FROM " . $this->table . " 
                 WHERE code = :code AND id_module = :id_module";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":code", $code);
        $stmt->bindParam(":id_module", $id_module);
        $stmt->execute();
        
        return $stmt->fetchColumn() > 0;
    }
}
?> 