<?php
require_once __DIR__ . '/BaseModel.php';

class SessionApprenant extends BaseModel {
    protected $table = 'session_apprenant';
    
    /**
     * Récupère toutes les inscriptions avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_session = null, $id_apprenant = null, $statut = null, $limit = 10, $offset = 0) {
        $sql = "SELECT sa.*, 
                       s.titre as session_titre, s.date_debut, s.date_fin,
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom,
                       u.nom as apprenant_nom, u.prenom as apprenant_prenom
                FROM {$this->table} sa 
                JOIN session s ON sa.id_session = s.id 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                JOIN utilisateur u ON sa.id_apprenant = u.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (s.titre LIKE ? OR m.intitule LIKE ? OR f.nom LIKE ? OR u.nom LIKE ? OR u.prenom LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_session) {
            $sql .= " AND sa.id_session = ?";
            $params[] = $id_session;
        }
        
        if ($id_apprenant) {
            $sql .= " AND sa.id_apprenant = ?";
            $params[] = $id_apprenant;
        }
        
        if ($statut) {
            $sql .= " AND sa.statut = ?";
            $params[] = $statut;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY s.date_debut DESC, u.nom, u.prenom, sa.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total d'inscriptions
     */
    public function count($search = '', $id_session = null, $id_apprenant = null, $statut = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} sa 
                JOIN session s ON sa.id_session = s.id 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                JOIN utilisateur u ON sa.id_apprenant = u.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (s.titre LIKE ? OR m.intitule LIKE ? OR f.nom LIKE ? OR u.nom LIKE ? OR u.prenom LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_session) {
            $sql .= " AND sa.id_session = ?";
            $params[] = $id_session;
        }
        
        if ($id_apprenant) {
            $sql .= " AND sa.id_apprenant = ?";
            $params[] = $id_apprenant;
        }
        
        if ($statut) {
            $sql .= " AND sa.statut = ?";
            $params[] = $statut;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée une nouvelle inscription
     */
    public function create($data) {
        // Vérifier si la session existe
        $sql = "SELECT id FROM session WHERE id = ?";
        $result = $this->query($sql, [$data['id_session']]);
        if (empty($result)) {
            throw new Exception("La session n'existe pas.");
        }
        
        // Vérifier si l'apprenant existe
        $sql = "SELECT id FROM utilisateur WHERE id = ? AND role = 'apprenant'";
        $result = $this->query($sql, [$data['id_apprenant']]);
        if (empty($result)) {
            throw new Exception("L'apprenant n'existe pas ou n'a pas le rôle approprié.");
        }
        
        // Vérifier si l'apprenant n'est pas déjà inscrit
        $sql = "SELECT id FROM {$this->table} WHERE id_session = ? AND id_apprenant = ?";
        $result = $this->query($sql, [$data['id_session'], $data['id_apprenant']]);
        if (!empty($result)) {
            throw new Exception("L'apprenant est déjà inscrit à cette session.");
        }
        
        // Préparer les données
        $fields = ['id_session', 'id_apprenant', 'date_inscription', 'statut', 'note', 'commentaire'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer l'inscription
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour une inscription
     */
    public function update($id, $data) {
        // Vérifier si l'inscription existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'inscription n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['statut', 'note', 'commentaire'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour l'inscription
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime une inscription
     */
    public function delete($id) {
        // Vérifier si l'inscription existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'inscription n'existe pas.");
        }
        
        // Supprimer l'inscription
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'une inscription
     */
    public function getDetails($id) {
        $sql = "SELECT sa.*, 
                       s.titre as session_titre, s.date_debut, s.date_fin,
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom,
                       u.nom as apprenant_nom, u.prenom as apprenant_prenom
                FROM {$this->table} sa 
                JOIN session s ON sa.id_session = s.id 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                JOIN utilisateur u ON sa.id_apprenant = u.id 
                WHERE sa.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère toutes les inscriptions d'une session
     */
    public function getBySession($id_session) {
        $sql = "SELECT sa.*, 
                       u.nom as apprenant_nom, u.prenom as apprenant_prenom
                FROM {$this->table} sa 
                JOIN utilisateur u ON sa.id_apprenant = u.id 
                WHERE sa.id_session = ? 
                ORDER BY u.nom, u.prenom, sa.id";
        return $this->query($sql, [$id_session]);
    }
    
    /**
     * Récupère toutes les inscriptions d'un apprenant
     */
    public function getByApprenant($id_apprenant) {
        $sql = "SELECT sa.*, 
                       s.titre as session_titre, s.date_debut, s.date_fin,
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} sa 
                JOIN session s ON sa.id_session = s.id 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE sa.id_apprenant = ? 
                ORDER BY s.date_debut DESC, sa.id";
        return $this->query($sql, [$id_apprenant]);
    }
    
    /**
     * Récupère les statistiques des inscriptions d'une session
     */
    public function getStatsSession($id_session) {
        $stats = [
            'nombre_inscriptions' => 0,
            'inscriptions_par_statut' => [],
            'moyenne_notes' => 0,
            'inscriptions' => []
        ];
        
        // Récupérer toutes les inscriptions de la session
        $sql = "SELECT sa.*, 
                       u.nom as apprenant_nom, u.prenom as apprenant_prenom
                FROM {$this->table} sa 
                JOIN utilisateur u ON sa.id_apprenant = u.id 
                WHERE sa.id_session = ? 
                ORDER BY u.nom, u.prenom, sa.id";
        $inscriptions = $this->query($sql, [$id_session]);
        
        $total_notes = 0;
        $nombre_notes = 0;
        
        foreach ($inscriptions as $inscription) {
            $stats['nombre_inscriptions']++;
            
            // Compter les inscriptions par statut
            if (!isset($stats['inscriptions_par_statut'][$inscription['statut']])) {
                $stats['inscriptions_par_statut'][$inscription['statut']] = 0;
            }
            $stats['inscriptions_par_statut'][$inscription['statut']]++;
            
            // Calculer la moyenne des notes
            if ($inscription['note'] !== null) {
                $total_notes += $inscription['note'];
                $nombre_notes++;
            }
            
            $stats['inscriptions'][] = [
                'id' => $inscription['id'],
                'apprenant' => $inscription['apprenant_nom'] . ' ' . $inscription['apprenant_prenom'],
                'date_inscription' => $inscription['date_inscription'],
                'statut' => $inscription['statut'],
                'note' => $inscription['note']
            ];
        }
        
        // Calculer la moyenne des notes
        if ($nombre_notes > 0) {
            $stats['moyenne_notes'] = $total_notes / $nombre_notes;
        }
        
        return $stats;
    }
    
    /**
     * Récupère les statistiques des inscriptions d'un apprenant
     */
    public function getStatsApprenant($id_apprenant) {
        $stats = [
            'nombre_inscriptions' => 0,
            'inscriptions_par_statut' => [],
            'moyenne_notes' => 0,
            'inscriptions' => []
        ];
        
        // Récupérer toutes les inscriptions de l'apprenant
        $sql = "SELECT sa.*, 
                       s.titre as session_titre, s.date_debut, s.date_fin,
                       m.code as module_code, m.intitule as module_intitule
                FROM {$this->table} sa 
                JOIN session s ON sa.id_session = s.id 
                JOIN module m ON s.id_module = m.id 
                WHERE sa.id_apprenant = ? 
                ORDER BY s.date_debut DESC, sa.id";
        $inscriptions = $this->query($sql, [$id_apprenant]);
        
        $total_notes = 0;
        $nombre_notes = 0;
        
        foreach ($inscriptions as $inscription) {
            $stats['nombre_inscriptions']++;
            
            // Compter les inscriptions par statut
            if (!isset($stats['inscriptions_par_statut'][$inscription['statut']])) {
                $stats['inscriptions_par_statut'][$inscription['statut']] = 0;
            }
            $stats['inscriptions_par_statut'][$inscription['statut']]++;
            
            // Calculer la moyenne des notes
            if ($inscription['note'] !== null) {
                $total_notes += $inscription['note'];
                $nombre_notes++;
            }
            
            $stats['inscriptions'][] = [
                'id' => $inscription['id'],
                'session' => $inscription['session_titre'],
                'module' => $inscription['module_code'] . ' - ' . $inscription['module_intitule'],
                'date_debut' => $inscription['date_debut'],
                'date_fin' => $inscription['date_fin'],
                'statut' => $inscription['statut'],
                'note' => $inscription['note']
            ];
        }
        
        // Calculer la moyenne des notes
        if ($nombre_notes > 0) {
            $stats['moyenne_notes'] = $total_notes / $nombre_notes;
        }
        
        return $stats;
    }
} 