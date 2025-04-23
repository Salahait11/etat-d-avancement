<?php
require_once __DIR__ . '/BaseModel.php';

class ObjectifPedagogique extends BaseModel {
    protected $table = 'objectif_pedagogique';
    
    /**
     * Récupère tous les objectifs pédagogiques avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_module = null, $limit = 10, $offset = 0) {
        $sql = "SELECT op.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} op 
                JOIN module m ON op.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (op.description LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND op.id_module = ?";
            $params[] = $id_module;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY m.code, op.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total d'objectifs pédagogiques
     */
    public function count($search = '', $id_module = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} op 
                JOIN module m ON op.id_module = m.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (op.description LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND op.id_module = ?";
            $params[] = $id_module;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée un nouvel objectif pédagogique
     */
    public function create($data) {
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$data['id_module']]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Préparer les données
        $fields = ['id_module', 'description', 'ordre'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer l'objectif
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un objectif pédagogique
     */
    public function update($id, $data) {
        // Vérifier si l'objectif existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'objectif pédagogique n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['description', 'ordre'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour l'objectif
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un objectif pédagogique
     */
    public function delete($id) {
        // Vérifier si l'objectif existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'objectif pédagogique n'existe pas.");
        }
        
        // Supprimer l'objectif
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'un objectif pédagogique
     */
    public function getDetails($id) {
        $sql = "SELECT op.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} op 
                JOIN module m ON op.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE op.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère tous les objectifs pédagogiques d'un module
     */
    public function getByModule($id_module) {
        $sql = "SELECT * FROM {$this->table} WHERE id_module = ? ORDER BY ordre, id";
        return $this->query($sql, [$id_module]);
    }
    
    /**
     * Réorganise l'ordre des objectifs pédagogiques d'un module
     */
    public function reorder($id_module, $new_order) {
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$id_module]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Mettre à jour l'ordre de chaque objectif
        foreach ($new_order as $ordre => $id_objectif) {
            $sql = "UPDATE {$this->table} SET ordre = ? WHERE id = ? AND id_module = ?";
            $this->execute($sql, [$ordre, $id_objectif, $id_module]);
        }
        
        return true;
    }
    
    /**
     * Récupère les statistiques des objectifs pédagogiques d'un module
     */
    public function getStatsModule($id_module) {
        $stats = [
            'nombre_objectifs' => 0,
            'objectifs' => []
        ];
        
        // Récupérer tous les objectifs du module
        $sql = "SELECT * FROM {$this->table} WHERE id_module = ? ORDER BY ordre, id";
        $objectifs = $this->query($sql, [$id_module]);
        
        foreach ($objectifs as $objectif) {
            $stats['nombre_objectifs']++;
            $stats['objectifs'][] = [
                'id' => $objectif['id'],
                'description' => $objectif['description'],
                'ordre' => $objectif['ordre']
            ];
        }
        
        return $stats;
    }
} 