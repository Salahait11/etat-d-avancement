<?php // src/Model/StrategieEvaluationModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

class StrategieEvaluationModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère toutes les stratégies d'évaluation.
     *
     * @return array Liste des stratégies d'évaluation
     */
    public function findAll(): array
    {
        $sql = "SELECT id, strategie, description, created_at, updated_at 
                FROM strategie_evaluation 
                ORDER BY strategie";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère une stratégie d'évaluation par son ID.
     *
     * @param int $id ID de la stratégie d'évaluation
     * @return array|false Données de la stratégie d'évaluation ou false si non trouvée
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT id, strategie, description, created_at, updated_at 
                FROM strategie_evaluation 
                WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Crée une nouvelle stratégie d'évaluation.
     *
     * @param array $data Données de la stratégie d'évaluation (strategie, description)
     * @return int|false ID de la nouvelle stratégie d'évaluation ou false en cas d'échec
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO strategie_evaluation (strategie, description) 
                VALUES (:strategie, :description)";
        
        $params = [
            ':strategie' => $data['strategie'],
            ':description' => $data['description'] ?? ''
        ];
        
        if ($this->db->query($sql, $params)) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour une stratégie d'évaluation existante.
     *
     * @param int $id ID de la stratégie d'évaluation à mettre à jour
     * @param array $data Nouvelles données (strategie, description)
     * @return bool Succès de la mise à jour
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE strategie_evaluation 
                SET strategie = :strategie, 
                    description = :description, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':strategie' => $data['strategie'],
            ':description' => $data['description'] ?? ''
        ];
        
        return $this->db->query($sql, $params) ? true : false;
    }

    /**
     * Supprime une stratégie d'évaluation.
     *
     * @param int $id ID de la stratégie d'évaluation à supprimer
     * @return bool Succès de la suppression
     */
    public function delete(int $id): bool
    {
        // Vérifier si la stratégie d'évaluation est utilisée dans etat_avancement_strategie
        $checkSql = "SELECT COUNT(*) FROM etat_avancement_strategie WHERE id_strategie_evaluation = :id";
        $checkStmt = $this->db->query($checkSql, [':id' => $id]);
        
        if ($checkStmt && $checkStmt->fetchColumn() > 0) {
            // La stratégie est utilisée, ne pas supprimer
            return false;
        }
        
        // La stratégie n'est pas utilisée, on peut la supprimer
        $sql = "DELETE FROM strategie_evaluation WHERE id = :id";
        return $this->db->query($sql, [':id' => $id]) ? true : false;
    }
}
