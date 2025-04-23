<?php
require_once __DIR__ . '/BaseModel.php';

class Session extends BaseModel {
    protected $table = 'session';
    
    /**
     * Récupère toutes les sessions avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_module = null, $date_debut = null, $date_fin = null, $limit = 10, $offset = 0) {
        $sql = "SELECT s.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom,
                       u.nom as formateur_nom, u.prenom as formateur_prenom
                FROM {$this->table} s 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                JOIN utilisateur u ON s.id_formateur = u.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (s.titre LIKE ? OR m.intitule LIKE ? OR f.nom LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND s.id_module = ?";
            $params[] = $id_module;
        }
        
        if ($date_debut) {
            $sql .= " AND s.date_debut >= ?";
            $params[] = $date_debut;
        }
        
        if ($date_fin) {
            $sql .= " AND s.date_fin <= ?";
            $params[] = $date_fin;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY s.date_debut DESC, m.code, s.id LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de sessions
     */
    public function count($search = '', $id_module = null, $date_debut = null, $date_fin = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} s 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (s.titre LIKE ? OR m.intitule LIKE ? OR f.nom LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_module) {
            $sql .= " AND s.id_module = ?";
            $params[] = $id_module;
        }
        
        if ($date_debut) {
            $sql .= " AND s.date_debut >= ?";
            $params[] = $date_debut;
        }
        
        if ($date_fin) {
            $sql .= " AND s.date_fin <= ?";
            $params[] = $date_fin;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée une nouvelle session
     */
    public function create($data) {
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$data['id_module']]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Vérifier si le formateur existe
        $sql = "SELECT id FROM utilisateur WHERE id = ? AND role = 'formateur'";
        $result = $this->query($sql, [$data['id_formateur']]);
        if (empty($result)) {
            throw new Exception("Le formateur n'existe pas ou n'a pas le rôle approprié.");
        }
        
        // Préparer les données
        $fields = ['id_module', 'id_formateur', 'titre', 'description', 'date_debut', 'date_fin', 'duree', 'statut'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer la session
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour une session
     */
    public function update($id, $data) {
        // Vérifier si la session existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La session n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['titre', 'description', 'date_debut', 'date_fin', 'duree', 'statut'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour la session
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime une session
     */
    public function delete($id) {
        // Vérifier si la session existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La session n'existe pas.");
        }
        
        // Supprimer la session
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'une session
     */
    public function getDetails($id) {
        $sql = "SELECT s.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom,
                       u.nom as formateur_nom, u.prenom as formateur_prenom
                FROM {$this->table} s 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                JOIN utilisateur u ON s.id_formateur = u.id 
                WHERE s.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère toutes les sessions d'un module
     */
    public function getByModule($id_module) {
        $sql = "SELECT s.*, 
                       u.nom as formateur_nom, u.prenom as formateur_prenom
                FROM {$this->table} s 
                JOIN utilisateur u ON s.id_formateur = u.id 
                WHERE s.id_module = ? 
                ORDER BY s.date_debut DESC, s.id";
        return $this->query($sql, [$id_module]);
    }
    
    /**
     * Récupère toutes les sessions d'un formateur
     */
    public function getByFormateur($id_formateur) {
        $sql = "SELECT s.*, 
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} s 
                JOIN module m ON s.id_module = m.id 
                JOIN filiere f ON m.id_filiere = f.id 
                WHERE s.id_formateur = ? 
                ORDER BY s.date_debut DESC, s.id";
        return $this->query($sql, [$id_formateur]);
    }
    
    /**
     * Récupère les statistiques des sessions d'un module
     */
    public function getStatsModule($id_module) {
        $stats = [
            'nombre_sessions' => 0,
            'duree_totale' => 0,
            'sessions_par_statut' => [],
            'sessions' => []
        ];
        
        // Récupérer toutes les sessions du module
        $sql = "SELECT s.*, 
                       u.nom as formateur_nom, u.prenom as formateur_prenom
                FROM {$this->table} s 
                JOIN utilisateur u ON s.id_formateur = u.id 
                WHERE s.id_module = ? 
                ORDER BY s.date_debut DESC, s.id";
        $sessions = $this->query($sql, [$id_module]);
        
        foreach ($sessions as $session) {
            $stats['nombre_sessions']++;
            $stats['duree_totale'] += $session['duree'];
            
            // Compter les sessions par statut
            if (!isset($stats['sessions_par_statut'][$session['statut']])) {
                $stats['sessions_par_statut'][$session['statut']] = 0;
            }
            $stats['sessions_par_statut'][$session['statut']]++;
            
            $stats['sessions'][] = [
                'id' => $session['id'],
                'titre' => $session['titre'],
                'date_debut' => $session['date_debut'],
                'date_fin' => $session['date_fin'],
                'duree' => $session['duree'],
                'statut' => $session['statut'],
                'formateur' => $session['formateur_nom'] . ' ' . $session['formateur_prenom']
            ];
        }
        
        return $stats;
    }
} 