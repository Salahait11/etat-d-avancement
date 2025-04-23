<?php
require_once __DIR__ . '/BaseModel.php';

class MoyenPedagogique extends BaseModel {
    protected $table = 'moyen_pedagogique';
    
    /**
     * Récupère tous les moyens pédagogiques avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_module = null, $limit = 10, $offset = 0) {
        $sql = "SELECT mp.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} mp 
                JOIN module m ON mp.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (mp.description LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND mp.id_module = ?";
            $params[] = $id_module;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY m.code, mp.ordre, mp.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de moyens pédagogiques
     */
    public function count($search = '', $id_module = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} mp 
                JOIN module m ON mp.id_module = m.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (mp.description LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND mp.id_module = ?";
            $params[] = $id_module;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée un nouveau moyen pédagogique
     */
    public function create($data) {
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$data['id_module']]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Préparer les données
        $fields = ['id_module', 'description', 'type', 'ordre'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer le moyen pédagogique
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un moyen pédagogique
     */
    public function update($id, $data) {
        // Vérifier si le moyen existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le moyen pédagogique n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['description', 'type', 'ordre'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour le moyen
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un moyen pédagogique
     */
    public function delete($id) {
        // Vérifier si le moyen existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le moyen pédagogique n'existe pas.");
        }
        
        // Supprimer le moyen
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'un moyen pédagogique
     */
    public function getDetails($id) {
        $sql = "SELECT mp.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} mp 
                JOIN module m ON mp.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE mp.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère tous les moyens pédagogiques d'un module
     */
    public function getByModule($id_module) {
        $sql = "SELECT * FROM {$this->table} WHERE id_module = ? ORDER BY ordre, id";
        return $this->query($sql, [$id_module]);
    }
    
    /**
     * Réorganise l'ordre des moyens pédagogiques d'un module
     */
    public function reorder($id_module, $new_order) {
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$id_module]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Mettre à jour l'ordre de chaque moyen
        foreach ($new_order as $ordre => $id_moyen) {
            $sql = "UPDATE {$this->table} SET ordre = ? WHERE id = ? AND id_module = ?";
            $this->execute($sql, [$ordre, $id_moyen, $id_module]);
        }
        
        return true;
    }
    
    /**
     * Récupère les statistiques des moyens pédagogiques d'un module
     */
    public function getStatsModule($id_module) {
        $stats = [
            'nombre_moyens' => 0,
            'moyens_par_type' => [],
            'moyens' => []
        ];
        
        // Récupérer tous les moyens du module
        $sql = "SELECT * FROM {$this->table} WHERE id_module = ? ORDER BY ordre, id";
        $moyens = $this->query($sql, [$id_module]);
        
        foreach ($moyens as $moyen) {
            $stats['nombre_moyens']++;
            
            // Compter les moyens par type
            if (!isset($stats['moyens_par_type'][$moyen['type']])) {
                $stats['moyens_par_type'][$moyen['type']] = 0;
            }
            $stats['moyens_par_type'][$moyen['type']]++;
            
            $stats['moyens'][] = [
                'id' => $moyen['id'],
                'description' => $moyen['description'],
                'type' => $moyen['type'],
                'ordre' => $moyen['ordre']
            ];
        }
        
        return $stats;
    }
} 