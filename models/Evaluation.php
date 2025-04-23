<?php
require_once __DIR__ . '/BaseModel.php';

class Evaluation extends BaseModel {
    protected $table = 'evaluation';
    
    /**
     * Récupère toutes les évaluations avec des détails supplémentaires
     */
    public function getAllWithDetails($search = '', $id_apprenant = null, $id_module = null, $date_debut = null, $date_fin = null, $limit = 10, $offset = 0) {
        $sql = "SELECT e.*, 
                       a.nom as apprenant_nom, a.prenom as apprenant_prenom,
                       m.code as module_code, m.intitule as module_intitule,
                       COUNT(ec.id) as nombre_competences,
                       AVG(ec.note) as moyenne_notes
                FROM {$this->table} e 
                JOIN apprenant a ON e.id_apprenant = a.id 
                JOIN module m ON e.id_module = m.id 
                LEFT JOIN evaluation_competence ec ON e.id = ec.id_evaluation
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (a.nom LIKE ? OR a.prenom LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_apprenant) {
            $sql .= " AND e.id_apprenant = ?";
            $params[] = $id_apprenant;
        }
        
        if ($id_module) {
            $sql .= " AND e.id_module = ?";
            $params[] = $id_module;
        }
        
        if ($date_debut) {
            $sql .= " AND e.date_evaluation >= ?";
            $params[] = $date_debut;
        }
        
        if ($date_fin) {
            $sql .= " AND e.date_evaluation <= ?";
            $params[] = $date_fin;
        }
        
        // Grouper par évaluation pour les calculs
        $sql .= " GROUP BY e.id";
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY e.date_evaluation DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total d'évaluations
     */
    public function count($search = '', $id_apprenant = null, $id_module = null, $date_debut = null, $date_fin = null) {
        $sql = "SELECT COUNT(DISTINCT e.id) as total 
                FROM {$this->table} e 
                JOIN apprenant a ON e.id_apprenant = a.id 
                JOIN module m ON e.id_module = m.id 
                WHERE 1=1";
        $params = [];
        
        // Ajouter les conditions de recherche
        if (!empty($search)) {
            $sql .= " AND (a.nom LIKE ? OR a.prenom LIKE ? OR m.intitule LIKE ?)";
            $searchParam = "%{$search}%";
            $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
        }
        
        if ($id_apprenant) {
            $sql .= " AND e.id_apprenant = ?";
            $params[] = $id_apprenant;
        }
        
        if ($id_module) {
            $sql .= " AND e.id_module = ?";
            $params[] = $id_module;
        }
        
        if ($date_debut) {
            $sql .= " AND e.date_evaluation >= ?";
            $params[] = $date_debut;
        }
        
        if ($date_fin) {
            $sql .= " AND e.date_evaluation <= ?";
            $params[] = $date_fin;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée une nouvelle évaluation
     */
    public function create($data) {
        // Vérifier si l'apprenant existe
        $sql = "SELECT id FROM apprenant WHERE id = ?";
        $result = $this->query($sql, [$data['id_apprenant']]);
        if (empty($result)) {
            throw new Exception("L'apprenant n'existe pas.");
        }
        
        // Vérifier si le module existe
        $sql = "SELECT id FROM module WHERE id = ?";
        $result = $this->query($sql, [$data['id_module']]);
        if (empty($result)) {
            throw new Exception("Le module n'existe pas.");
        }
        
        // Préparer les données
        $fields = ['id_apprenant', 'id_module', 'date_evaluation', 'type_evaluation', 'commentaire'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer l'évaluation
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        $id_evaluation = $this->lastInsertId();
        
        // Ajouter les notes des compétences
        if (isset($data['competences']) && is_array($data['competences'])) {
            foreach ($data['competences'] as $competence) {
                $sql = "INSERT INTO evaluation_competence (id_evaluation, id_competence, note, commentaire) VALUES (?, ?, ?, ?)";
                $this->execute($sql, [
                    $id_evaluation,
                    $competence['id_competence'],
                    $competence['note'],
                    $competence['commentaire'] ?? null
                ]);
            }
        }
        
        return $id_evaluation;
    }
    
    /**
     * Met à jour une évaluation
     */
    public function update($id, $data) {
        // Vérifier si l'évaluation existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'évaluation n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if ($field !== 'competences') {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour l'évaluation
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        // Mettre à jour les notes des compétences
        if (isset($data['competences'])) {
            // Supprimer les anciennes notes
            $sql = "DELETE FROM evaluation_competence WHERE id_evaluation = ?";
            $this->execute($sql, [$id]);
            
            // Ajouter les nouvelles notes
            foreach ($data['competences'] as $competence) {
                $sql = "INSERT INTO evaluation_competence (id_evaluation, id_competence, note, commentaire) VALUES (?, ?, ?, ?)";
                $this->execute($sql, [
                    $id,
                    $competence['id_competence'],
                    $competence['note'],
                    $competence['commentaire'] ?? null
                ]);
            }
        }
        
        return true;
    }
    
    /**
     * Supprime une évaluation
     */
    public function delete($id) {
        // Vérifier si l'évaluation existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("L'évaluation n'existe pas.");
        }
        
        // Supprimer les notes des compétences
        $sql = "DELETE FROM evaluation_competence WHERE id_evaluation = ?";
        $this->execute($sql, [$id]);
        
        // Supprimer l'évaluation
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'une évaluation
     */
    public function getDetails($id) {
        $sql = "SELECT e.*, 
                       a.nom as apprenant_nom, a.prenom as apprenant_prenom,
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} e 
                JOIN apprenant a ON e.id_apprenant = a.id 
                JOIN module m ON e.id_module = m.id 
                JOIN filiere f ON a.id_filiere = f.id 
                WHERE e.id = ?";
        
        $result = $this->query($sql, [$id]);
        if (empty($result)) {
            return null;
        }
        
        $evaluation = $result[0];
        
        // Récupérer les notes des compétences
        $sql = "SELECT ec.*, c.code as competence_code, c.intitule as competence_intitule
                FROM evaluation_competence ec 
                JOIN competence c ON ec.id_competence = c.id 
                WHERE ec.id_evaluation = ?
                ORDER BY c.code";
        
        $evaluation['competences'] = $this->query($sql, [$id]);
        
        return $evaluation;
    }
    
    /**
     * Récupère les statistiques d'une évaluation
     */
    public function getStats($id) {
        $stats = [
            'nombre_competences' => 0,
            'moyenne_notes' => 0,
            'meilleure_note' => 0,
            'pire_note' => 20,
            'competences_evaluees' => []
        ];
        
        // Récupérer les notes des compétences
        $sql = "SELECT ec.*, c.code as competence_code, c.intitule as competence_intitule
                FROM evaluation_competence ec 
                JOIN competence c ON ec.id_competence = c.id 
                WHERE ec.id_evaluation = ?";
        
        $competences = $this->query($sql, [$id]);
        
        foreach ($competences as $competence) {
            $stats['nombre_competences']++;
            $stats['competences_evaluees'][] = [
                'code' => $competence['competence_code'],
                'intitule' => $competence['competence_intitule'],
                'note' => $competence['note']
            ];
            
            // Mettre à jour la meilleure et la pire note
            if ($competence['note'] > $stats['meilleure_note']) {
                $stats['meilleure_note'] = $competence['note'];
            }
            if ($competence['note'] < $stats['pire_note']) {
                $stats['pire_note'] = $competence['note'];
            }
        }
        
        // Calculer la moyenne des notes
        if ($stats['nombre_competences'] > 0) {
            $total = array_sum(array_column($stats['competences_evaluees'], 'note'));
            $stats['moyenne_notes'] = $total / $stats['nombre_competences'];
        }
        
        return $stats;
    }
}
?> 