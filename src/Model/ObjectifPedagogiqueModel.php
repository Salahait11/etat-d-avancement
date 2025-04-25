<?php // src/Model/ObjectifPedagogiqueModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

class ObjectifPedagogiqueModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère tous les objectifs pédagogiques.
     *
     * @return array Liste des objectifs pédagogiques
     */
    public function findAll(): array
    {
        $sql = "SELECT id, objectif, description, created_at, updated_at 
                FROM objectif_pedagogique 
                ORDER BY objectif";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère un objectif pédagogique par son ID.
     *
     * @param int $id ID de l'objectif pédagogique
     * @return array|false Données de l'objectif pédagogique ou false si non trouvé
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT id, objectif, description, created_at, updated_at 
                FROM objectif_pedagogique 
                WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Crée un nouvel objectif pédagogique.
     *
     * @param array $data Données de l'objectif pédagogique (objectif, description)
     * @return int|false ID du nouvel objectif pédagogique ou false en cas d'échec
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO objectif_pedagogique (objectif, description) 
                VALUES (:objectif, :description)";
        
        $params = [
            ':objectif' => $data['objectif'],
            ':description' => $data['description'] ?? ''
        ];
        
        if ($this->db->query($sql, $params)) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour un objectif pédagogique existant.
     *
     * @param int $id ID de l'objectif pédagogique à mettre à jour
     * @param array $data Nouvelles données (objectif, description)
     * @return bool Succès de la mise à jour
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE objectif_pedagogique 
                SET objectif = :objectif, 
                    description = :description, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':objectif' => $data['objectif'],
            ':description' => $data['description'] ?? ''
        ];
        
        return $this->db->query($sql, $params) ? true : false;
    }

    /**
     * Supprime un objectif pédagogique.
     *
     * @param int $id ID de l'objectif pédagogique à supprimer
     * @return bool Succès de la suppression
     */
    public function delete(int $id): bool
    {
        // Vérifier si l'objectif pédagogique est utilisé dans etat_avancement_objectif
        $checkSql = "SELECT COUNT(*) FROM etat_avancement_objectif WHERE id_objectif_pedagogique = :id";
        $checkStmt = $this->db->query($checkSql, [':id' => $id]);
        
        if ($checkStmt && $checkStmt->fetchColumn() > 0) {
            // L'objectif est utilisé, ne pas supprimer
            return false;
        }
        
        // L'objectif n'est pas utilisé, on peut le supprimer
        $sql = "DELETE FROM objectif_pedagogique WHERE id = :id";
        return $this->db->query($sql, [':id' => $id]) ? true : false;
    }
}
