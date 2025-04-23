<?php
require_once __DIR__ . '/BaseModel.php';

class Competence extends BaseModel {
    protected $table = 'competence';
    
    /**
     * Récupère toutes les compétences avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_module = null, $niveau = null, $limit = 10, $offset = 0) {
        $sql = "SELECT c.*, 
                       m.intitule as module_intitule,
                       COUNT(ec.id) as nombre_evaluations,
                       AVG(ec.note) as moyenne_notes
                FROM {$this->table} c 
                JOIN module m ON c.id_module = m.id 
                LEFT JOIN evaluation_competence ec ON c.id = ec.id_competence
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (c.code LIKE ? OR c.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND c.id_module = ?";
            $params[] = $id_module;
        }
        
        if ($niveau) {
            $sql .= " AND c.niveau = ?";
            $params[] = $niveau;
        }
        
        // Grouper par compétence pour les calculs
        $sql .= " GROUP BY c.id";
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY c.code LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de compétences
     */
    public function count($search = '', $id_module = null, $niveau = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} c 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (c.code LIKE ? OR c.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND c.id_module = ?";
            $params[] = $id_module;
        }
        
        if ($niveau) {
            $sql .= " AND c.niveau = ?";
            $params[] = $niveau;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Récupère une compétence par son code
     */
    public function getByCode($code) {
        $sql = "SELECT c.*, m.intitule as module_intitule
                FROM {$this->table} c 
                JOIN module m ON c.id_module = m.id 
                WHERE c.code = ?";
        
        $result = $this->query($sql, [$code]);
        return $result[0] ?? null;
    }
    
    /**
     * Crée une nouvelle compétence
     */
    public function create($data) {
        // Vérifier si le code existe déjà
        if ($this->getByCode($data['code'])) {
            throw new Exception("Une compétence avec ce code existe déjà.");
        }
        
        // Préparer les données
        $fields = ['code', 'description', 'niveau', 'id_module'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer la compétence
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour une compétence
     */
    public function update($id, $data) {
        // Vérifier si la compétence existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La compétence n'existe pas.");
        }
        
        // Vérifier si le code est déjà utilisé par une autre compétence
        if (isset($data['code']) && $data['code'] !== $existing['code']) {
            $existingWithCode = $this->getByCode($data['code']);
            if ($existingWithCode && $existingWithCode['id'] != $id) {
                throw new Exception("Une compétence avec ce code existe déjà.");
            }
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $updates[] = "{$field} = ?";
            $params[] = $value;
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour la compétence
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime une compétence
     */
    public function delete($id) {
        // Vérifier si la compétence existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La compétence n'existe pas.");
        }
        
        // Vérifier si la compétence est utilisée dans des évaluations
        $sql = "SELECT COUNT(*) as count FROM evaluation_competence WHERE id_competence = ?";
        $result = $this->query($sql, [$id]);
        
        if ($result[0]['count'] > 0) {
            throw new Exception("Impossible de supprimer cette compétence car elle est utilisée dans des évaluations.");
        }
        
        // Supprimer la compétence
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les évaluations associées à une compétence
     */
    public function getEvaluations($id) {
        $sql = "SELECT ec.*, e.date_evaluation, a.nom as apprenant_nom, a.prenom as apprenant_prenom
                FROM evaluation_competence ec 
                JOIN evaluation e ON ec.id_evaluation = e.id 
                JOIN apprenant a ON e.id_apprenant = a.id 
                WHERE ec.id_competence = ?
                ORDER BY e.date_evaluation DESC";
        
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les critères d'évaluation d'une compétence
     */
    public function getCriteresEvaluation($id) {
        $sql = "SELECT * FROM critere_evaluation WHERE id_competence = ? ORDER BY ordre";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Ajoute un critère d'évaluation à une compétence
     */
    public function addCritereEvaluation($id, $data) {
        // Vérifier si la compétence existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La compétence n'existe pas.");
        }
        
        // Préparer les données
        $fields = ['description', 'ordre', 'id_competence'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if ($field === 'id_competence') {
                $values[] = "?";
                $params[] = $id;
            } elseif (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer le critère
        $sql = "INSERT INTO critere_evaluation (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un critère d'évaluation
     */
    public function updateCritereEvaluation($id_critere, $data) {
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            $updates[] = "{$field} = ?";
            $params[] = $value;
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id_critere;
        
        // Mettre à jour le critère
        $sql = "UPDATE critere_evaluation SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un critère d'évaluation
     */
    public function deleteCritereEvaluation($id_critere) {
        $sql = "DELETE FROM critere_evaluation WHERE id = ?";
        $this->execute($sql, [$id_critere]);
        
        return true;
    }
} 