<?php
require_once __DIR__ . '/BaseModel.php';

class Utilisateur extends BaseModel {
    protected $table = 'utilisateur';
    
    /**
     * Récupère tous les utilisateurs avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $role = null, $statut = null, $limit = 10, $offset = 0) {
        $sql = "SELECT u.*, 
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} u 
                LEFT JOIN filiere f ON u.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR f.nom LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $params[] = $role;
        }
        
        if ($statut) {
            $sql .= " AND u.statut = ?";
            $params[] = $statut;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY u.nom, u.prenom, u.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total d'utilisateurs
     */
    public function count($search = '', $role = null, $statut = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} u 
                LEFT JOIN filiere f ON u.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR f.nom LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        if ($role) {
            $sql .= " AND u.role = ?";
            $params[] = $role;
        }
        
        if ($statut) {
            $sql .= " AND u.statut = ?";
            $params[] = $statut;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée un nouvel utilisateur
     */
    public function create($data) {
        // Vérifier si l'email est déjà utilisé
        $sql = "SELECT id FROM {$this->table} WHERE email = ?";
        $result = $this->query($sql, [$data['email']]);
        if (!empty($result)) {
            throw new Exception("L'adresse email est déjà utilisée.");
        }
        
        // Vérifier si la filière existe si elle est spécifiée
        if (isset($data['id_filiere'])) {
            $sql = "SELECT id FROM filiere WHERE id = ?";
            $result = $this->query($sql, [$data['id_filiere']]);
            if (empty($result)) {
                throw new Exception("La filière n'existe pas.");
            }
        }
        
        // Préparer les données
        $fields = ['nom', 'prenom', 'email', 'password', 'role', 'statut', 'id_filiere'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $field === 'password' ? password_hash($data[$field], PASSWORD_DEFAULT) : $data[$field];
            }
        }
        
        // Insérer l'utilisateur
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un utilisateur
     */
    public function update($id, $data) {
        // Vérifier si l'utilisateur existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'utilisateur n'existe pas.");
        }
        
        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        if (isset($data['email']) && $data['email'] !== $existing['email']) {
            $sql = "SELECT id FROM {$this->table} WHERE email = ? AND id != ?";
            $result = $this->query($sql, [$data['email'], $id]);
            if (!empty($result)) {
                throw new Exception("L'adresse email est déjà utilisée.");
            }
        }
        
        // Vérifier si la filière existe si elle est spécifiée
        if (isset($data['id_filiere'])) {
            $sql = "SELECT id FROM filiere WHERE id = ?";
            $result = $this->query($sql, [$data['id_filiere']]);
            if (empty($result)) {
                throw new Exception("La filière n'existe pas.");
            }
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['nom', 'prenom', 'email', 'role', 'statut', 'id_filiere'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            } elseif ($field === 'password' && !empty($value)) {
                $updates[] = "password = ?";
                $params[] = password_hash($value, PASSWORD_DEFAULT);
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour l'utilisateur
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un utilisateur
     */
    public function delete($id) {
        // Vérifier si l'utilisateur existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'utilisateur n'existe pas.");
        }
        
        // Vérifier si l'utilisateur est utilisé dans d'autres tables
        $tables = [
            'session' => 'id_formateur',
            'session_apprenant' => 'id_apprenant',
            'evaluation' => 'id_apprenant'
        ];
        
        foreach ($tables as $table => $field) {
            $sql = "SELECT COUNT(*) as total FROM {$table} WHERE {$field} = ?";
            $result = $this->query($sql, [$id]);
            if ($result[0]['total'] > 0) {
                throw new Exception("L'utilisateur est utilisé dans d'autres tables et ne peut pas être supprimé.");
            }
        }
        
        // Supprimer l'utilisateur
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'un utilisateur
     */
    public function getDetails($id) {
        $sql = "SELECT u.*, 
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} u 
                LEFT JOIN filiere f ON u.id_filiere = f.id 
                WHERE u.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère un utilisateur par son email
     */
    public function getByEmail($email) {
        $sql = "SELECT u.*, 
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} u 
                LEFT JOIN filiere f ON u.id_filiere = f.id 
                WHERE u.email = ?";
        
        $result = $this->query($sql, [$email]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère tous les utilisateurs d'une filière
     */
    public function getByFiliere($id_filiere) {
        $sql = "SELECT * FROM {$this->table} WHERE id_filiere = ? ORDER BY nom, prenom, id";
        return $this->query($sql, [$id_filiere]);
    }
    
    /**
     * Récupère tous les utilisateurs d'un rôle
     */
    public function getByRole($role) {
        $sql = "SELECT u.*, 
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} u 
                LEFT JOIN filiere f ON u.id_filiere = f.id 
                WHERE u.role = ? 
                ORDER BY u.nom, u.prenom, u.id";
        return $this->query($sql, [$role]);
    }
    
    /**
     * Vérifie les identifiants d'un utilisateur
     */
    public function authenticate($email, $password) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ? AND statut = 'actif'";
        $result = $this->query($sql, [$email]);
        
        if (empty($result)) {
            return null;
        }
        
        $user = $result[0];
        
        if (password_verify($password, $user['password'])) {
            // Ne pas renvoyer le mot de passe
            unset($user['password']);
            return $user;
        }
        
        return null;
    }
    
    /**
     * Change le mot de passe d'un utilisateur
     */
    public function changePassword($id, $old_password, $new_password) {
        // Vérifier si l'utilisateur existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'utilisateur n'existe pas.");
        }
        
        // Vérifier l'ancien mot de passe
        if (!password_verify($old_password, $existing['password'])) {
            throw new Exception("L'ancien mot de passe est incorrect.");
        }
        
        // Mettre à jour le mot de passe
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $this->execute($sql, [password_hash($new_password, PASSWORD_DEFAULT), $id]);
        
        return true;
    }
    
    /**
     * Réinitialise le mot de passe d'un utilisateur
     */
    public function resetPassword($id, $new_password) {
        // Vérifier si l'utilisateur existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'utilisateur n'existe pas.");
        }
        
        // Mettre à jour le mot de passe
        $sql = "UPDATE {$this->table} SET password = ? WHERE id = ?";
        $this->execute($sql, [password_hash($new_password, PASSWORD_DEFAULT), $id]);
        
        return true;
    }
    
    /**
     * Récupère les statistiques des utilisateurs
     */
    public function getStats() {
        $stats = [
            'nombre_total' => 0,
            'utilisateurs_par_role' => [],
            'utilisateurs_par_statut' => [],
            'utilisateurs_par_filiere' => []
        ];
        
        // Récupérer tous les utilisateurs
        $sql = "SELECT u.*, f.nom as filiere_nom 
                FROM {$this->table} u 
                LEFT JOIN filiere f ON u.id_filiere = f.id";
        $utilisateurs = $this->query($sql);
        
        foreach ($utilisateurs as $utilisateur) {
            $stats['nombre_total']++;
            
            // Compter les utilisateurs par rôle
            if (!isset($stats['utilisateurs_par_role'][$utilisateur['role']])) {
                $stats['utilisateurs_par_role'][$utilisateur['role']] = 0;
            }
            $stats['utilisateurs_par_role'][$utilisateur['role']]++;
            
            // Compter les utilisateurs par statut
            if (!isset($stats['utilisateurs_par_statut'][$utilisateur['statut']])) {
                $stats['utilisateurs_par_statut'][$utilisateur['statut']] = 0;
            }
            $stats['utilisateurs_par_statut'][$utilisateur['statut']]++;
            
            // Compter les utilisateurs par filière
            $filiere = $utilisateur['filiere_nom'] ?? 'Sans filière';
            if (!isset($stats['utilisateurs_par_filiere'][$filiere])) {
                $stats['utilisateurs_par_filiere'][$filiere] = 0;
            }
            $stats['utilisateurs_par_filiere'][$filiere]++;
        }
        
        return $stats;
    }
}
?> 