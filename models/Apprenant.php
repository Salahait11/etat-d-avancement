<?php
require_once __DIR__ . '/BaseModel.php';

class Apprenant extends BaseModel {
    protected $table = 'apprenant';
    
    /**
     * Récupère tous les apprenants avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_filiere = null, $limit = 10, $offset = 0) {
        $sql = "SELECT a.*, f.nom_filiere 
                FROM {$this->table} a 
                LEFT JOIN filiere f ON a.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (a.nom LIKE ? OR a.prenom LIKE ? OR a.email LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_filiere) {
            $sql .= " AND a.id_filiere = ?";
            $params[] = $id_filiere;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY a.nom, a.prenom LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total d'apprenants
     */
    public function count($search = '', $id_filiere = null) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} a WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (a.nom LIKE ? OR a.prenom LIKE ? OR a.email LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_filiere) {
            $sql .= " AND a.id_filiere = ?";
            $params[] = $id_filiere;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Récupère un apprenant par son email
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM {$this->table} WHERE email = ?";
        $result = $this->query($sql, [$email]);
        return $result[0] ?? null;
    }
    
    /**
     * Crée un nouvel apprenant
     */
    public function create($data) {
        // Vérifier si l'email existe déjà
        if ($this->getByEmail($data['email'])) {
            throw new Exception("Un apprenant avec cet email existe déjà.");
        }
        
        // Préparer les données
        $fields = ['nom', 'prenom', 'email', 'telephone', 'date_naissance', 'id_filiere'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer l'apprenant
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour un apprenant
     */
    public function update($id, $data) {
        // Vérifier si l'apprenant existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'apprenant n'existe pas.");
        }
        
        // Vérifier si l'email est déjà utilisé par un autre apprenant
        if (isset($data['email']) && $data['email'] !== $existing['email']) {
            $existingWithEmail = $this->getByEmail($data['email']);
            if ($existingWithEmail && $existingWithEmail['id'] != $id) {
                throw new Exception("Un apprenant avec cet email existe déjà.");
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
        
        // Mettre à jour l'apprenant
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime un apprenant
     */
    public function delete($id) {
        // Vérifier si l'apprenant existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'apprenant n'existe pas.");
        }
        
        // Supprimer l'apprenant
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les évaluations d'un apprenant
     */
    public function getEvaluations($id) {
        $sql = "SELECT e.*, m.intitule as module_intitule 
                FROM evaluation e 
                JOIN module m ON e.id_module = m.id 
                WHERE e.id_apprenant = ? 
                ORDER BY e.date_evaluation DESC";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les statistiques d'un apprenant
     */
    public function getStats($id) {
        $stats = [
            'total_evaluations' => 0,
            'moyenne_generale' => 0,
            'meilleure_note' => 0,
            'pire_note' => 20,
            'evaluations_par_module' => []
        ];
        
        // Récupérer toutes les évaluations
        $sql = "SELECT e.*, m.intitule as module_intitule, 
                       AVG(ec.note) as moyenne_module,
                       COUNT(DISTINCT e.id) as total_evaluations_module
                FROM evaluation e 
                JOIN module m ON e.id_module = m.id 
                JOIN evaluation_competence ec ON e.id = ec.id_evaluation
                WHERE e.id_apprenant = ?
                GROUP BY e.id_module";
        
        $evaluations = $this->query($sql, [$id]);
        
        foreach ($evaluations as $eval) {
            $stats['total_evaluations'] += $eval['total_evaluations_module'];
            $stats['evaluations_par_module'][$eval['id_module']] = [
                'module' => $eval['module_intitule'],
                'moyenne' => $eval['moyenne_module'],
                'total' => $eval['total_evaluations_module']
            ];
            
            // Mettre à jour la meilleure et la pire note
            if ($eval['moyenne_module'] > $stats['meilleure_note']) {
                $stats['meilleure_note'] = $eval['moyenne_module'];
            }
            if ($eval['moyenne_module'] < $stats['pire_note']) {
                $stats['pire_note'] = $eval['moyenne_module'];
            }
        }
        
        // Calculer la moyenne générale
        if ($stats['total_evaluations'] > 0) {
            $total = array_sum(array_column($stats['evaluations_par_module'], 'moyenne'));
            $stats['moyenne_generale'] = $total / count($stats['evaluations_par_module']);
        }
        
        return $stats;
    }
} 