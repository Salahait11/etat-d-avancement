<?php
require_once __DIR__ . '/BaseModel.php';

class Filiere extends BaseModel {
    protected $table = 'filiere';
    
    /**
     * Récupère toutes les filières avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $limit = 10, $offset = 0) {
        $sql = "SELECT f.*, 
                       COUNT(m.id) as nombre_modules,
                       COUNT(DISTINCT a.id) as nombre_apprenants,
                       COUNT(DISTINCT e.id) as nombre_evaluations
                FROM {$this->table} f 
                LEFT JOIN module m ON f.id = m.id_filiere 
                LEFT JOIN apprenant a ON f.id = a.id_filiere 
                LEFT JOIN evaluation e ON a.id = e.id_apprenant
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (f.code LIKE ? OR f.nom LIKE ? OR f.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        // Grouper par filière pour les calculs
        $sql .= " GROUP BY f.id";
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY f.code LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de filières
     */
    public function count($search = '') {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} f 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (f.code LIKE ? OR f.nom LIKE ? OR f.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Récupère une filière par son code
     */
    public function getByCode($code) {
        $sql = "SELECT * FROM {$this->table} WHERE code = ?";
        
        $result = $this->query($sql, [$code]);
        return $result[0] ?? null;
    }
    
    /**
     * Crée une nouvelle filière
     */
    public function create($data) {
        // Vérifier si le code existe déjà
        if ($this->getByCode($data['code'])) {
            throw new Exception("Une filière avec ce code existe déjà.");
        }
        
        // Préparer les données
        $fields = ['code', 'nom', 'description', 'duree', 'niveau'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer la filière
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        $id_filiere = $this->lastInsertId();
        
        // Ajouter les modules
        if (isset($data['modules']) && is_array($data['modules'])) {
            foreach ($data['modules'] as $module) {
                $module['id_filiere'] = $id_filiere;
                $sql = "INSERT INTO module (code, intitule, description, duree, id_filiere) VALUES (?, ?, ?, ?, ?)";
                $this->execute($sql, [
                    $module['code'],
                    $module['intitule'],
                    $module['description'],
                    $module['duree'],
                    $id_filiere
                ]);
            }
        }
        
        return $id_filiere;
    }
    
    /**
     * Met à jour une filière
     */
    public function update($id, $data) {
        // Vérifier si la filière existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La filière n'existe pas.");
        }
        
        // Vérifier si le code est déjà utilisé par une autre filière
        if (isset($data['code']) && $data['code'] !== $existing['code']) {
            $existingWithCode = $this->getByCode($data['code']);
            if ($existingWithCode && $existingWithCode['id'] != $id) {
                throw new Exception("Une filière avec ce code existe déjà.");
            }
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if ($field !== 'modules') {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour la filière
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        // Mettre à jour les modules
        if (isset($data['modules'])) {
            // Supprimer les anciens modules
            $sql = "DELETE FROM module WHERE id_filiere = ?";
            $this->execute($sql, [$id]);
            
            // Ajouter les nouveaux modules
            foreach ($data['modules'] as $module) {
                $module['id_filiere'] = $id;
                $sql = "INSERT INTO module (code, intitule, description, duree, id_filiere) VALUES (?, ?, ?, ?, ?)";
                $this->execute($sql, [
                    $module['code'],
                    $module['intitule'],
                    $module['description'],
                    $module['duree'],
                    $id
                ]);
            }
        }
        
        return true;
    }
    
    /**
     * Supprime une filière
     */
    public function delete($id) {
        // Vérifier si la filière existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La filière n'existe pas.");
        }
        
        // Vérifier si la filière est utilisée par des apprenants
        $sql = "SELECT COUNT(*) as count FROM apprenant WHERE id_filiere = ?";
        $result = $this->query($sql, [$id]);
        
        if ($result[0]['count'] > 0) {
            throw new Exception("Impossible de supprimer cette filière car elle est utilisée par des apprenants.");
        }
        
        // Supprimer les modules associés
        $sql = "DELETE FROM module WHERE id_filiere = ?";
        $this->execute($sql, [$id]);
        
        // Supprimer la filière
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les modules d'une filière
     */
    public function getModules($id) {
        $sql = "SELECT * FROM module WHERE id_filiere = ? ORDER BY code";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les apprenants d'une filière
     */
    public function getApprenants($id) {
        $sql = "SELECT a.*, 
                       COUNT(e.id) as nombre_evaluations,
                       AVG(ec.note) as moyenne_notes
                FROM apprenant a 
                LEFT JOIN evaluation e ON a.id = e.id_apprenant 
                LEFT JOIN evaluation_competence ec ON e.id = ec.id_evaluation
                WHERE a.id_filiere = ?
                GROUP BY a.id
                ORDER BY a.nom, a.prenom";
        
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les statistiques d'une filière
     */
    public function getStats($id) {
        $stats = [
            'nombre_modules' => 0,
            'nombre_apprenants' => 0,
            'nombre_evaluations' => 0,
            'moyenne_generale' => 0,
            'meilleure_note' => 0,
            'pire_note' => 20,
            'evaluations_par_module' => []
        ];
        
        // Construire la requête de base
        $sql = "SELECT m.id as id_module, m.code as module_code, m.intitule as module_intitule,
                       COUNT(DISTINCT e.id) as total_evaluations,
                       AVG(ec.note) as moyenne_module
                FROM module m 
                LEFT JOIN evaluation e ON m.id = e.id_module 
                LEFT JOIN evaluation_competence ec ON e.id = ec.id_evaluation
                WHERE m.id_filiere = ?
                GROUP BY m.id";
        
        $results = $this->query($sql, [$id]);
        
        foreach ($results as $result) {
            $stats['nombre_modules']++;
            $stats['evaluations_par_module'][$result['id_module']] = [
                'module' => $result['module_intitule'],
                'total' => $result['total_evaluations'],
                'moyenne' => $result['moyenne_module']
            ];
            
            $stats['nombre_evaluations'] += $result['total_evaluations'];
            
            // Mettre à jour la meilleure et la pire note
            if ($result['moyenne_module'] > $stats['meilleure_note']) {
                $stats['meilleure_note'] = $result['moyenne_module'];
            }
            if ($result['moyenne_module'] < $stats['pire_note']) {
                $stats['pire_note'] = $result['moyenne_module'];
            }
        }
        
        // Calculer la moyenne générale
        if ($stats['nombre_evaluations'] > 0) {
            $total = array_sum(array_column($stats['evaluations_par_module'], 'moyenne'));
            $stats['moyenne_generale'] = $total / count($stats['evaluations_par_module']);
        }
        
        // Compter le nombre d'apprenants
        $sql = "SELECT COUNT(*) as count FROM apprenant WHERE id_filiere = ?";
        $result = $this->query($sql, [$id]);
        $stats['nombre_apprenants'] = $result[0]['count'];
        
        return $stats;
    }
} 