<?php
require_once __DIR__ . '/BaseModel.php';

class EvaluationCompetence extends BaseModel {
    protected $table = 'evaluation_competence';
    
    /**
     * Récupère toutes les notes de compétences avec des détails supplémentaires
     */
    public function getAllWithDetails($id_evaluation = null, $id_competence = null, $limit = 10, $offset = 0) {
        $sql = "SELECT ec.*, 
                       e.date_evaluation, e.type_evaluation,
                       c.code as competence_code, c.intitule as competence_intitule,
                       a.nom as apprenant_nom, a.prenom as apprenant_prenom,
                       m.code as module_code, m.intitule as module_intitule
                FROM {$this->table} ec 
                JOIN evaluation e ON ec.id_evaluation = e.id 
                JOIN competence c ON ec.id_competence = c.id 
                JOIN apprenant a ON e.id_apprenant = a.id 
                JOIN module m ON e.id_module = m.id 
                WHERE 1=1";
        $params = [];
        
        if ($id_evaluation) {
            $sql .= " AND ec.id_evaluation = ?";
            $params[] = $id_evaluation;
        }
        
        if ($id_competence) {
            $sql .= " AND ec.id_competence = ?";
            $params[] = $id_competence;
        }
        
        // Ajouter le tri et la pagination
        $sql .= " ORDER BY e.date_evaluation DESC, c.code LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->query($sql, $params);
    }
    
    /**
     * Compte le nombre total de notes de compétences
     */
    public function count($id_evaluation = null, $id_competence = null) {
        $sql = "SELECT COUNT(*) as total 
                FROM {$this->table} ec 
                WHERE 1=1";
        $params = [];
        
        if ($id_evaluation) {
            $sql .= " AND ec.id_evaluation = ?";
            $params[] = $id_evaluation;
        }
        
        if ($id_competence) {
            $sql .= " AND ec.id_competence = ?";
            $params[] = $id_competence;
        }
        
        $result = $this->query($sql, $params);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Crée une nouvelle note de compétence
     */
    public function create($data) {
        // Vérifier si l'évaluation existe
        $sql = "SELECT id FROM evaluation WHERE id = ?";
        $result = $this->query($sql, [$data['id_evaluation']]);
        if (empty($result)) {
            throw new Exception("L'évaluation n'existe pas.");
        }
        
        // Vérifier si la compétence existe
        $sql = "SELECT id FROM competence WHERE id = ?";
        $result = $this->query($sql, [$data['id_competence']]);
        if (empty($result)) {
            throw new Exception("La compétence n'existe pas.");
        }
        
        // Vérifier si la note est déjà attribuée
        $sql = "SELECT id FROM {$this->table} WHERE id_evaluation = ? AND id_competence = ?";
        $result = $this->query($sql, [$data['id_evaluation'], $data['id_competence']]);
        if (!empty($result)) {
            throw new Exception("Cette compétence a déjà une note pour cette évaluation.");
        }
        
        // Préparer les données
        $fields = ['id_evaluation', 'id_competence', 'note', 'commentaire'];
        $values = [];
        $params = [];
        
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $values[] = "?";
                $params[] = $data[$field];
            }
        }
        
        // Insérer la note
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $this->execute($sql, $params);
        
        return $this->lastInsertId();
    }
    
    /**
     * Met à jour une note de compétence
     */
    public function update($id, $data) {
        // Vérifier si la note existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La note n'existe pas.");
        }
        
        // Préparer les données
        $updates = [];
        $params = [];
        
        foreach ($data as $field => $value) {
            if (in_array($field, ['note', 'commentaire'])) {
                $updates[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        // Ajouter l'ID aux paramètres
        $params[] = $id;
        
        // Mettre à jour la note
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updates) . " WHERE id = ?";
        $this->execute($sql, $params);
        
        return true;
    }
    
    /**
     * Supprime une note de compétence
     */
    public function delete($id) {
        // Vérifier si la note existe
        $existing = $this->readOne($id);
        if (!$existing) {
            throw new Exception("La note n'existe pas.");
        }
        
        // Supprimer la note
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $this->execute($sql, [$id]);
        
        return true;
    }
    
    /**
     * Récupère les détails d'une note de compétence
     */
    public function getDetails($id) {
        $sql = "SELECT ec.*, 
                       e.date_evaluation, e.type_evaluation,
                       c.code as competence_code, c.intitule as competence_intitule,
                       a.nom as apprenant_nom, a.prenom as apprenant_prenom,
                       m.code as module_code, m.intitule as module_intitule,
                       f.code as filiere_code, f.nom as filiere_nom
                FROM {$this->table} ec 
                JOIN evaluation e ON ec.id_evaluation = e.id 
                JOIN competence c ON ec.id_competence = c.id 
                JOIN apprenant a ON e.id_apprenant = a.id 
                JOIN module m ON e.id_module = m.id 
                JOIN filiere f ON a.id_filiere = f.id 
                WHERE ec.id = ?";
        
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Récupère les statistiques des notes d'une compétence
     */
    public function getStatsCompetence($id_competence) {
        $stats = [
            'nombre_evaluations' => 0,
            'moyenne_notes' => 0,
            'meilleure_note' => 0,
            'pire_note' => 20,
            'evaluations' => []
        ];
        
        // Récupérer toutes les notes de la compétence
        $sql = "SELECT ec.*, e.date_evaluation, e.type_evaluation,
                       a.nom as apprenant_nom, a.prenom as apprenant_prenom,
                       m.code as module_code, m.intitule as module_intitule
                FROM {$this->table} ec 
                JOIN evaluation e ON ec.id_evaluation = e.id 
                JOIN apprenant a ON e.id_apprenant = a.id 
                JOIN module m ON e.id_module = m.id 
                WHERE ec.id_competence = ?
                ORDER BY e.date_evaluation DESC";
        
        $evaluations = $this->query($sql, [$id_competence]);
        
        foreach ($evaluations as $evaluation) {
            $stats['nombre_evaluations']++;
            $stats['evaluations'][] = [
                'date' => $evaluation['date_evaluation'],
                'type' => $evaluation['type_evaluation'],
                'apprenant' => $evaluation['apprenant_nom'] . ' ' . $evaluation['apprenant_prenom'],
                'module' => $evaluation['module_intitule'],
                'note' => $evaluation['note']
            ];
            
            // Mettre à jour la meilleure et la pire note
            if ($evaluation['note'] > $stats['meilleure_note']) {
                $stats['meilleure_note'] = $evaluation['note'];
            }
            if ($evaluation['note'] < $stats['pire_note']) {
                $stats['pire_note'] = $evaluation['note'];
            }
        }
        
        // Calculer la moyenne des notes
        if ($stats['nombre_evaluations'] > 0) {
            $total = array_sum(array_column($stats['evaluations'], 'note'));
            $stats['moyenne_notes'] = $total / $stats['nombre_evaluations'];
        }
        
        return $stats;
    }
    
    /**
     * Récupère les statistiques des notes d'un apprenant pour une compétence
     */
    public function getStatsApprenantCompetence($id_apprenant, $id_competence) {
        $stats = [
            'nombre_evaluations' => 0,
            'moyenne_notes' => 0,
            'meilleure_note' => 0,
            'pire_note' => 20,
            'evaluations' => []
        ];
        
        // Récupérer toutes les notes de l'apprenant pour la compétence
        $sql = "SELECT ec.*, e.date_evaluation, e.type_evaluation,
                       m.code as module_code, m.intitule as module_intitule
                FROM {$this->table} ec 
                JOIN evaluation e ON ec.id_evaluation = e.id 
                JOIN module m ON e.id_module = m.id 
                WHERE ec.id_competence = ? AND e.id_apprenant = ?
                ORDER BY e.date_evaluation DESC";
        
        $evaluations = $this->query($sql, [$id_competence, $id_apprenant]);
        
        foreach ($evaluations as $evaluation) {
            $stats['nombre_evaluations']++;
            $stats['evaluations'][] = [
                'date' => $evaluation['date_evaluation'],
                'type' => $evaluation['type_evaluation'],
                'module' => $evaluation['module_intitule'],
                'note' => $evaluation['note']
            ];
            
            // Mettre à jour la meilleure et la pire note
            if ($evaluation['note'] > $stats['meilleure_note']) {
                $stats['meilleure_note'] = $evaluation['note'];
            }
            if ($evaluation['note'] < $stats['pire_note']) {
                $stats['pire_note'] = $evaluation['note'];
            }
        }
        
        // Calculer la moyenne des notes
        if ($stats['nombre_evaluations'] > 0) {
            $total = array_sum(array_column($stats['evaluations'], 'note'));
            $stats['moyenne_notes'] = $total / $stats['nombre_evaluations'];
        }
        
        return $stats;
    }
} 