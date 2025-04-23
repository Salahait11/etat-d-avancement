<?php
require_once __DIR__ . '/BaseModel.php';

class Role extends BaseModel {
    protected $table = 'role';
    
    /**
     * Récupère tous les rôles avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $limit = 10, $offset = 0) {
        $sql = "SELECT r.*, COUNT(u.id) as nombre_utilisateurs 
                FROM {$this->table} r 
                LEFT JOIN utilisateur u ON r.id = u.id_role 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND r.nom LIKE ?";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
        }
        
        // Grouper par rôle pour le comptage
        $sql .= " GROUP BY r.id";
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY r.nom LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de rôles
     */
    public function count($search = '') {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} r WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND r.nom LIKE ?";
            $searchParam = "%{$search}%";
            $params[] = $searchParam;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Récupère un rôle par son nom
     */
    public function getByNom($nom) {
        $sql = "SELECT * FROM {$this->table} WHERE nom = ?";
        $result = $this->query($sql, [$nom]);
        return $result[0] ?? null;
    }
    
    /**
     * Crée un nouveau rôle
     */
    public function create($data) {
        // Vérifier si le nom existe déjà
        if ($this->getByNom($data['nom'])) {
            throw new Exception("Un rôle avec ce nom existe déjà.");
        }
        
        // Préparer les données
        $fields = ['nom', 'description', 'permissions'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer le rôle
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un rôle
     */
    public function update($id, $data) {
        // Vérifier si le rôle existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le rôle n'existe pas.");
        }
        
        // Vérifier si le nom est déjà utilisé par un autre rôle
        if (isset($data['nom']) && $data['nom'] !== $existing['nom']) {
            $existingWithNom = $this->getByNom($data['nom']);
            if ($existingWithNom && $existingWithNom['id'] != $id) {
                throw new Exception("Un rôle avec ce nom existe déjà.");
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
        
        // Mettre à jour le rôle
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un rôle
     */
    public function delete($id) {
        // Vérifier si le rôle existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le rôle n'existe pas.");
        }
        
        // Vérifier si des utilisateurs ont ce rôle
        $sql = "SELECT COUNT(*) as count FROM utilisateur WHERE id_role = ?";
        $result = $this->query($sql, [$id]);
        
        if ($result[0]['count'] > 0) {
            throw new Exception("Impossible de supprimer ce rôle car il est attribué à des utilisateurs.");
        }
        
        // Supprimer le rôle
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les utilisateurs ayant un rôle spécifique
     */
    public function getUtilisateurs($id) {
        $sql = "SELECT u.* FROM utilisateur u WHERE u.id_role = ? ORDER BY u.nom, u.prenom";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Ajoute une permission à un rôle
     */
    public function addPermission($id, $permission) {
        $role = $this->readOne($id);
        if (!$role) {
            throw new Exception("Le rôle n'existe pas.");
        }
        
        $permissions = json_decode($role['permissions'], true) ?: [];
        
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            
            $sql = "UPDATE {$this->table} SET permissions = ? WHERE id = ?";
            $this->execute($sql, [json_encode($permissions), $id]);
        }
        
        return true;
    }
    
    /**
     * Retire une permission d'un rôle
     */
    public function removePermission($id, $permission) {
        $role = $this->readOne($id);
        if (!$role) {
            throw new Exception("Le rôle n'existe pas.");
        }
        
        $permissions = json_decode($role['permissions'], true) ?: [];
        
        if (($key = array_search($permission, $permissions)) !== false) {
            unset($permissions[$key]);
            $permissions = array_values($permissions); // Réindexer le tableau
            
            $sql = "UPDATE {$this->table} SET permissions = ? WHERE id = ?";
            $this->execute($sql, [json_encode($permissions), $id]);
        }
        
        return true;
    }
    
    /**
     * Vérifie si un rôle a une permission spécifique
     */
    public function hasPermission($id, $permission) {
        $role = $this->readOne($id);
        if (!$role) {
            return false;
        }
        
        $permissions = json_decode($role['permissions'], true) ?: [];
        return in_array($permission, $permissions);
    }
} 