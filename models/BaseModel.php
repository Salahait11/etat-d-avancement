<?php
/**
 * Classe de base pour tous les modèles
 * Fournit les fonctionnalités communes pour l'accès aux données
 */
class BaseModel {
    protected $db;
    protected $table;
    
    /**
     * Constructeur
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Exécute une requête SQL et retourne les résultats
     * 
     * @param string $sql Requête SQL à exécuter
     * @param array $params Paramètres de la requête
     * @return array Résultats de la requête
     */
    protected function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Log l'erreur
            error_log("Erreur SQL dans {$this->table}: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de l'accès aux données.");
        }
    }
    
    /**
     * Exécute une requête SQL sans retourner de résultats
     * 
     * @param string $sql Requête SQL à exécuter
     * @param array $params Paramètres de la requête
     * @return bool Succès de l'exécution
     */
    protected function execute($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            // Log l'erreur
            error_log("Erreur SQL dans {$this->table}: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de l'accès aux données.");
        }
    }
    
    /**
     * Récupère l'ID du dernier enregistrement inséré
     * 
     * @return int ID du dernier enregistrement inséré
     */
    protected function lastInsertId() {
        return $this->db->lastInsertId();
    }
    
    /**
     * Récupère tous les enregistrements d'une table
     * 
     * @param int $limit Nombre maximum d'enregistrements à retourner
     * @param int $offset Décalage pour la pagination
     * @return array Enregistrements récupérés
     */
    public function readAll($limit = 10, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} LIMIT ? OFFSET ?";
        return $this->query($sql, [$limit, $offset]);
    }
    
    /**
     * Récupère un enregistrement par son ID
     * 
     * @param int $id ID de l'enregistrement à récupérer
     * @return array|null Enregistrement récupéré ou null si non trouvé
     */
    public function readOne($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $result = $this->query($sql, [$id]);
        return $result[0] ?? null;
    }
    
    /**
     * Compte le nombre total d'enregistrements dans une table
     * 
     * @return int Nombre total d'enregistrements
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        $result = $this->query($sql);
        return $result[0]['total'] ?? 0;
    }
    
    /**
     * Vérifie si un enregistrement existe
     * 
     * @param int $id ID de l'enregistrement à vérifier
     * @return bool True si l'enregistrement existe, false sinon
     */
    public function exists($id) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table} WHERE id = ?";
        $result = $this->query($sql, [$id]);
        return ($result[0]['total'] ?? 0) > 0;
    }
    
    /**
     * Démarre une transaction
     */
    protected function beginTransaction() {
        $this->db->beginTransaction();
    }
    
    /**
     * Valide une transaction
     */
    protected function commit() {
        $this->db->commit();
    }
    
    /**
     * Annule une transaction
     */
    protected function rollback() {
        $this->db->rollBack();
    }
    
    /**
     * Exécute une requête dans une transaction
     * 
     * @param callable $callback Fonction à exécuter dans la transaction
     * @return mixed Résultat de la fonction de callback
     */
    protected function transaction($callback) {
        try {
            $this->beginTransaction();
            $result = $callback();
            $this->commit();
            return $result;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }
    
    /**
     * Échappe une valeur pour l'utiliser dans une requête SQL
     * 
     * @param mixed $value Valeur à échapper
     * @return string Valeur échappée
     */
    protected function escape($value) {
        if (is_null($value)) {
            return 'NULL';
        }
        
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        
        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }
        
        return "'" . str_replace("'", "''", $value) . "'";
    }
    
    /**
     * Construit une clause WHERE à partir d'un tableau de conditions
     * 
     * @param array $conditions Conditions à appliquer
     * @return array Clause WHERE et paramètres
     */
    protected function buildWhereClause($conditions) {
        $where = [];
        $params = [];
        
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                // Opérateur spécifié
                $operator = $value[0];
                $val = $value[1];
                
                if ($operator === 'LIKE') {
                    $where[] = "{$field} LIKE ?";
                    $params[] = "%{$val}%";
                } elseif ($operator === 'IN') {
                    $placeholders = str_repeat('?,', count($val) - 1) . '?';
                    $where[] = "{$field} IN ({$placeholders})";
                    $params = array_merge($params, $val);
                } else {
                    $where[] = "{$field} {$operator} ?";
                    $params[] = $val;
                }
            } else {
                // Égalité par défaut
                $where[] = "{$field} = ?";
                $params[] = $value;
            }
        }
        
        return [
            'where' => !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '',
            'params' => $params
        ];
    }
    
    /**
     * Construit une clause ORDER BY à partir d'un tableau de tri
     * 
     * @param array $order Tri à appliquer
     * @return string Clause ORDER BY
     */
    protected function buildOrderClause($order) {
        if (empty($order)) {
            return '';
        }
        
        $clauses = [];
        
        foreach ($order as $field => $direction) {
            $direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
            $clauses[] = "{$field} {$direction}";
        }
        
        return 'ORDER BY ' . implode(', ', $clauses);
    }
    
    /**
     * Construit une clause LIMIT pour la pagination
     * 
     * @param int $limit Nombre maximum d'enregistrements
     * @param int $offset Décalage
     * @return string Clause LIMIT
     */
    protected function buildLimitClause($limit, $offset) {
        return "LIMIT {$limit} OFFSET {$offset}";
    }
}
?> 