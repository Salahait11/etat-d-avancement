<?php
require_once __DIR__ . '/BaseModel.php';

class ContenuSession extends BaseModel {
    protected $table = 'contenu_session';
    
    /**
     * Récupère tous les contenus de session avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_module = null, $limit = 10, $offset = 0) {
        $sql = "SELECT cs.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} cs 
                JOIN module m ON cs.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (cs.description LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND cs.id_module = ?";
            $params[] = $id_module;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY m.code, cs.ordre, cs.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de contenus de session
     */
    public function count($search = '', $id_module = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} cs 
                JOIN module m ON cs.id_module = m.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (cs.description LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND cs.id_module = ?";
            $params[] = $id_module;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée un nouveau contenu de session
     */
    public function create($data) {
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$data['id_module']]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Préparer les données
        $fields = ['id_module', 'description', 'duree', 'ordre'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer le contenu
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un contenu de session
     */
    public function update($id, $data) {
        // Vérifier si le contenu existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le contenu de session n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['description', 'duree', 'ordre'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour le contenu
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un contenu de session
     */
    public function delete($id) {
        // Vérifier si le contenu existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le contenu de session n'existe pas.");
        }
        
        // Supprimer le contenu
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'un contenu de session
     */
    public function getDetails($id) {
        $sql = "SELECT cs.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} cs 
                JOIN module m ON cs.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE cs.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère tous les contenus d'un module
     */
    public function getByModule($id_module) {
        $sql = "SELECT * FROM {$this->table} WHERE id_module = ? ORDER BY ordre, id";
        return $this->query($sql, [$id_module]);
    }
    
    /**
     * Réorganise l'ordre des contenus d'un module
     */
    public function reorder($id_module, $new_order) {
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$id_module]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Mettre à jour l'ordre de chaque contenu
        foreach ($new_order as $ordre => $id_contenu) {
            $sql = "UPDATE {$this->table} SET ordre = ? WHERE id = ? AND id_module = ?";
            $this->execute($sql, [$ordre, $id_contenu, $id_module]);
        }
        
        return true;
    }
    
    /**
     * Récupère les statistiques des contenus d'un module
     */
    public function getStatsModule($id_module) {
        $stats = [
            'nombre_contenus' => 0,
            'duree_totale' => 0,
            'contenus' => []
        ];
        
        // Récupérer tous les contenus du module
        $sql = "SELECT * FROM {$this->table} WHERE id_module = ? ORDER BY ordre, id";
        $contenus = $this->query($sql, [$id_module]);
        
        foreach ($contenus as $contenu) {
            $stats['nombre_contenus']++;
            $stats['duree_totale'] += $contenu['duree'];
            $stats['contenus'][] = [
                'id' => $contenu['id'],
                'description' => $contenu['description'],
                'duree' => $contenu['duree'],
                'ordre' => $contenu['ordre']
            ];
        }
        
        return $stats;
    }
} 