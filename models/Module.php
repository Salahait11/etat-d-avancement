<?php
require_once __DIR__ . '/BaseModel.php';

class Module extends BaseModel {
    protected $table = 'module';
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * Récupère tous les modules avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_filiere = null, $limit = 10, $offset = 0) {
        $sql = "SELECT m.*, 
                       f.nom as filiere_nom,
                       COUNT(c.id) as nombre_competences,
                       COUNT(DISTINCT e.id) as nombre_evaluations,
                       AVG(ec.note) as moyenne_notes
                FROM {$this->table} m 
                JOIN filiere f ON m.id_filiere = f.id 
                LEFT JOIN competence c ON m.id = c.id_module 
                LEFT JOIN evaluation e ON m.id = e.id_module 
                LEFT JOIN evaluation_competence ec ON e.id = ec.id_evaluation
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (m.code LIKE ? OR m.intitule LIKE ? OR m.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_filiere) {
            $sql .= " AND m.id_filiere = ?";
            $params[] = $id_filiere;
        }
        
        // Grouper par module pour les calculs
        $sql .= " GROUP BY m.id";
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY m.code LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de modules
     */
    public function count($search = '', $id_filiere = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} m 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (m.code LIKE ? OR m.intitule LIKE ? OR m.description LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_filiere) {
            $sql .= " AND m.id_filiere = ?";
            $params[] = $id_filiere;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Récupère un module par son code
     */
    public function getByCode($code) {
        $sql = "SELECT m.*, f.nom as filiere_nom
                FROM {$this->table} m 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE m.code = ?";
        
        $result = $this->query($sql, [$code]);
        return $result[0] ?? null;
    }
    
    /**
     * Crée un nouveau module
     */
    public function create($data) {
        // Vérifier si le code existe déjà
        if ($this->getByCode($data['code'])) {
            throw new Exception("Un module avec ce code existe déjà.");
        }
        
        // Préparer les données
        $fields = ['code', 'intitule', 'description', 'duree', 'id_filiere'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer le module
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        $id_module = $this->lastInsertId();
        
        // Ajouter les objectifs pédagogiques
        if (isset($data['objectifs']) && is_array($data['objectifs'])) {
            foreach ($data['objectifs'] as $objectif) {
                $sql = "INSERT INTO objectif_pedagogique (id_module, description) VALUES (?, ?)";
                $this->execute($sql, [$id_module, $objectif]);
            }
        }
        
        // Ajouter les contenus de séance
        if (isset($data['contenus']) && is_array($data['contenus'])) {
            foreach ($data['contenus'] as $contenu) {
                $sql = "INSERT INTO contenu_seance (id_module, description) VALUES (?, ?)";
                $this->execute($sql, [$id_module, $contenu]);
            }
        }
        
        // Ajouter les moyens pédagogiques
        if (isset($data['moyens']) && is_array($data['moyens'])) {
            foreach ($data['moyens'] as $moyen) {
                $sql = "INSERT INTO moyen_pedagogique (id_module, description) VALUES (?, ?)";
                $this->execute($sql, [$id_module, $moyen]);
            }
        }
        
        return $id_module;
    }
    
    /**
     * Met à jour un module
     */
    public function update($id, $data) {
        // Vérifier si le module existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Vérifier si le code est déjà utilisé par un autre module
        if (isset($data['code']) && $data['code'] !== $existing['code']) {
            $existingWithCode = $this->getByCode($data['code']);
            if ($existingWithCode && $existingWithCode['id'] != $id) {
                throw new Exception("Un module avec ce code existe déjà.");
            }
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (!in_array($field, ['objectifs', 'contenus', 'moyens'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour le module
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        // Mettre à jour les objectifs pédagogiques
        if (isset($data['objectifs'])) {
            // Supprimer les anciens objectifs
            $sql = "DELETE FROM objectif_pedagogique WHERE id_module = ?";
            $this->execute($sql, [$id]);
            
            // Ajouter les nouveaux objectifs
            foreach ($data['objectifs'] as $objectif) {
                $sql = "INSERT INTO objectif_pedagogique (id_module, description) VALUES (?, ?)";
                $this->execute($sql, [$id, $objectif]);
            }
        }
        
        // Mettre à jour les contenus de séance
        if (isset($data['contenus'])) {
            // Supprimer les anciens contenus
            $sql = "DELETE FROM contenu_seance WHERE id_module = ?";
            $this->execute($sql, [$id]);
            
            // Ajouter les nouveaux contenus
            foreach ($data['contenus'] as $contenu) {
                $sql = "INSERT INTO contenu_seance (id_module, description) VALUES (?, ?)";
                $this->execute($sql, [$id, $contenu]);
            }
        }
        
        // Mettre à jour les moyens pédagogiques
        if (isset($data['moyens'])) {
            // Supprimer les anciens moyens
            $sql = "DELETE FROM moyen_pedagogique WHERE id_module = ?";
            $this->execute($sql, [$id]);
            
            // Ajouter les nouveaux moyens
            foreach ($data['moyens'] as $moyen) {
                $sql = "INSERT INTO moyen_pedagogique (id_module, description) VALUES (?, ?)";
                $this->execute($sql, [$id, $moyen]);
            }
        }
        
        return true;
    }
    
    /**
     * Supprime un module
     */
    public function delete($id) {
        // Vérifier si le module existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Vérifier si le module est utilisé dans des évaluations
        $sql = "SELECT COUNT(*) as count FROM evaluation WHERE id_module = ?";
        $result = $this->query($sql, [$id]);
        
        if ($result[0]['count'] > 0) {
            throw new Exception("Impossible de supprimer ce module car il est utilisé dans des évaluations.");
        }
        
        // Supprimer les objectifs pédagogiques
        $sql = "DELETE FROM objectif_pedagogique WHERE id_module = ?";
        $this->execute($sql, [$id]);
        
        // Supprimer les contenus de séance
        $sql = "DELETE FROM contenu_seance WHERE id_module = ?";
        $this->execute($sql, [$id]);
        
        // Supprimer les moyens pédagogiques
        $sql = "DELETE FROM moyen_pedagogique WHERE id_module = ?";
        $this->execute($sql, [$id]);
        
        // Supprimer les compétences associées
        $sql = "DELETE FROM competence WHERE id_module = ?";
        $this->execute($sql, [$id]);
        
        // Supprimer le module
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les objectifs pédagogiques d'un module
     */
    public function getObjectifsPedagogiques($id) {
        $sql = "SELECT * FROM objectif_pedagogique WHERE id_module = ? ORDER BY id";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les contenus de séance d'un module
     */
    public function getContenusSeance($id) {
        $sql = "SELECT * FROM contenu_seance WHERE id_module = ? ORDER BY id";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les moyens pédagogiques d'un module
     */
    public function getMoyensPedagogiques($id) {
        $sql = "SELECT * FROM moyen_pedagogique WHERE id_module = ? ORDER BY id";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les compétences d'un module
     */
    public function getCompetences($id) {
        $sql = "SELECT * FROM competence WHERE id_module = ? ORDER BY code";
        return $this->query($sql, [$id]);
    }
    
    /**
     * Récupère les évaluations d'un module
     */
    public function getEvaluations($id) {
        $sql = "SELECT e.*, 
                       a.nom as apprenant_nom, a.prenom as apprenant_prenom,
                       COUNT(ec.id) as nombre_competences,
                       AVG(ec.note) as moyenne
                FROM evaluation e 
                JOIN apprenant a ON e.id_apprenant = a.id 
                LEFT JOIN evaluation_competence ec ON e.id = ec.id_evaluation
                WHERE e.id_module = ?
                GROUP BY e.id
                ORDER BY e.date_evaluation DESC";
        
        return $this->query($sql, [$id]);
    }
    
    // Lire un module par son ID
    public function readOne($id) {
        $query = "SELECT m.*, 
                        GROUP_CONCAT(DISTINCT c.id) as competence_ids,
                        GROUP_CONCAT(DISTINCT c.code) as competence_codes,
                        GROUP_CONCAT(DISTINCT c.intitule) as competence_intitules
                 FROM module m 
                 LEFT JOIN module_competence mc ON m.id = mc.id_module
                 LEFT JOIN competence c ON mc.id_competence = c.id
                 WHERE m.id = :id
                 GROUP BY m.id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $module = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($module) {
            // Formater les compétences
            $module['competences'] = [];
            if ($module['competence_ids']) {
                $ids = explode(',', $module['competence_ids']);
                $codes = explode(',', $module['competence_codes']);
                $intitules = explode(',', $module['competence_intitules']);
                
                for ($i = 0; $i < count($ids); $i++) {
                    $module['competences'][] = [
                        'id' => $ids[$i],
                        'code' => $codes[$i],
                        'intitule' => $intitules[$i]
                    ];
                }
            }
            
            // Supprimer les champs temporaires
            unset($module['competence_ids']);
            unset($module['competence_codes']);
            unset($module['competence_intitules']);
        }
        
        return $module;
    }
    
    // Obtenir les formateurs d'un module
    public function getFormateurs($id) {
        $query = "SELECT f.*, u.nom, u.prenom, u.email 
                 FROM formateur f 
                 JOIN utilisateur u ON f.id_utilisateur = u.id 
                 JOIN formateur_module fm ON f.id = fm.id_formateur 
                 WHERE fm.id_module = :id_module 
                 ORDER BY u.nom, u.prenom";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_module', $id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtenir les séances d'un module
    public function getSeances($id, $period = null) {
        $query = "SELECT s.*, f.id as id_formateur, u.nom as formateur_nom, u.prenom as formateur_prenom 
                 FROM seance s 
                 JOIN formateur f ON s.id_formateur = f.id 
                 JOIN utilisateur u ON f.id_utilisateur = u.id 
                 WHERE s.id_module = :id_module";
        
        if ($period === 'month') {
            $query .= " AND s.date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        } elseif ($period === 'week') {
            $query .= " AND s.date >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)";
        }
        
        $query .= " ORDER BY s.date DESC, s.heure_debut";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_module', $id);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtenir les statistiques d'un module
    public function getStats($id) {
        $stats = [
            'seances_mois' => 0,
            'heures_formation' => 0,
            'formateurs_count' => 0,
            'competences_count' => 0
        ];
        
        // Nombre de séances du mois
        $query = "SELECT COUNT(*) FROM seance 
                 WHERE id_module = :id_module 
                 AND date >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_module', $id);
        $stmt->execute();
        $stats['seances_mois'] = $stmt->fetchColumn();
        
        // Heures de formation
        $query = "SELECT SUM(TIMESTAMPDIFF(HOUR, heure_debut, heure_fin)) 
                 FROM seance 
                 WHERE id_module = :id_module";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_module', $id);
        $stmt->execute();
        $stats['heures_formation'] = $stmt->fetchColumn() ?: 0;
        
        // Nombre de formateurs
        $query = "SELECT COUNT(DISTINCT id_formateur) 
                 FROM formateur_module 
                 WHERE id_module = :id_module";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_module', $id);
        $stmt->execute();
        $stats['formateurs_count'] = $stmt->fetchColumn();
        
        // Nombre de compétences
        $query = "SELECT COUNT(DISTINCT id_competence) 
                 FROM module_competence 
                 WHERE id_module = :id_module";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_module', $id);
        $stmt->execute();
        $stats['competences_count'] = $stmt->fetchColumn();
        
        return $stats;
    }
} 