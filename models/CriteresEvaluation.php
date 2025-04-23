<?php
require_once __DIR__ . '/BaseModel.php';

class CriteresEvaluation extends BaseModel {
    protected $table = 'criteres_evaluation';
    
    /**
     * Récupère tous les critères d'évaluation avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_competence = null, $limit = 10, $offset = 0) {
        $sql = "SELECT ce.*, 
                       c.code as competence_code, c.intitule as competence_intitule,
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} ce 
                JOIN competence c ON ce.id_competence = c.id 
                JOIN module m ON c.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (ce.description LIKE ? OR c.intitule LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_competence) {
            $sql .= " AND ce.id_competence = ?";
            $params[] = $id_competence;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY m.code, c.code, ce.ordre, ce.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de critères d'évaluation
     */
    public function count($search = '', $id_competence = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} ce 
                JOIN competence c ON ce.id_competence = c.id 
                JOIN module m ON c.id_module = m.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (ce.description LIKE ? OR c.intitule LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_competence) {
            $sql .= " AND ce.id_competence = ?";
            $params[] = $id_competence;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée un nouveau critère d'évaluation
     */
    public function create($data) {
        // Vérifier si la compétence existe
        $sql = "SELECT id FROM competence WHERE id = ?";
        $result = $this->query($sql, [$data['id_competence']]);
        if (empty($result)) {
            throw new Exception("La compétence n'existe pas.");
        }
        
        // Préparer les données
        $fields = ['id_competence', 'description', 'niveau_attendu', 'ordre'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer le critère
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un critère d'évaluation
     */
    public function update($id, $data) {
        // Vérifier si le critère existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le critère d'évaluation n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['description', 'niveau_attendu', 'ordre'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour le critère
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un critère d'évaluation
     */
    public function delete($id) {
        // Vérifier si le critère existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le critère d'évaluation n'existe pas.");
        }
        
        // Supprimer le critère
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'un critère d'évaluation
     */
    public function getDetails($id) {
        $sql = "SELECT ce.*, 
                       c.code as competence_code, c.intitule as competence_intitule,
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} ce 
                JOIN competence c ON ce.id_competence = c.id 
                JOIN module m ON c.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE ce.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère tous les critères d'une compétence
     */
    public function getByCompetence($id_competence) {
        $sql = "SELECT * FROM {$this->table} WHERE id_competence = ? ORDER BY ordre, id";
        return $this->query($sql, [$id_competence]);
    }
    
    /**
     * Réorganise l'ordre des critères d'une compétence
     */
    public function reorder($id_competence, $new_order) {
        // Vérifier si la compétence existe
        $sql = "SELECT id FROM competence WHERE id = ?";
        $result = $this->query($sql, [$id_competence]);
        if (empty($result)) {
            throw new Exception("La compétence n'existe pas.");
        }
        
        // Mettre à jour l'ordre de chaque critère
        foreach ($new_order as $ordre => $id_critere) {
            $sql = "UPDATE {$this->table} SET ordre = ? WHERE id = ? AND id_competence = ?";
            $this->execute($sql, [$ordre, $id_critere, $id_competence]);
        }
        
        return true;
    }
    
    /**
     * Récupère les statistiques des critères d'une compétence
     */
    public function getStatsCompetence($id_competence) {
        $stats = [
            'nombre_criteres' => 0,
            'niveaux_attendus' => [],
            'criteres' => []
        ];
        
        // Récupérer tous les critères de la compétence
        $sql = "SELECT * FROM {$this->table} WHERE id_competence = ? ORDER BY ordre, id";
        $criteres = $this->query($sql, [$id_competence]);
        
        foreach ($criteres as $critere) {
            $stats['nombre_criteres']++;
            
            // Compter les critères par niveau attendu
            if (!isset($stats['niveaux_attendus'][$critere['niveau_attendu']])) {
                $stats['niveaux_attendus'][$critere['niveau_attendu']] = 0;
            }
            $stats['niveaux_attendus'][$critere['niveau_attendu']]++;
            
            $stats['criteres'][] = [
                'id' => $critere['id'],
                'description' => $critere['description'],
                'niveau_attendu' => $critere['niveau_attendu'],
                'ordre' => $critere['ordre']
            ];
        }
        
        return $stats;
    }
} 