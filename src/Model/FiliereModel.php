<?php // src/Model/FiliereModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

class FiliereModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère toutes les filières
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM filiere ORDER BY nom_filiere";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère une filière par son ID
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM filiere WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Crée une nouvelle filière
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO filiere (nom_filiere, description, niveau, duree_totale) 
                VALUES (:nom_filiere, :description, :niveau, :duree_totale)";
        
        $params = [
            ':nom_filiere' => $data['nom_filiere'],
            ':description' => $data['description'] ?? null,
            ':niveau' => $data['niveau'],
            ':duree_totale' => (int)$data['duree_totale']
        ];
        
        $stmt = $this->db->query($sql, $params);
        
        if ($stmt) {
            return (int)$this->db->getPdo()->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour une filière existante
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE filiere 
                SET nom_filiere = :nom_filiere, 
                    description = :description, 
                    niveau = :niveau, 
                    duree_totale = :duree_totale
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':nom_filiere' => $data['nom_filiere'],
            ':description' => $data['description'] ?? null,
            ':niveau' => $data['niveau'],
            ':duree_totale' => (int)$data['duree_totale']
        ];
        
        $stmt = $this->db->query($sql, $params);
        
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Supprime une filière par son ID
     */
    public function delete(int $id): bool
    {
        // Vérifier d'abord si la filière a des modules associés
        $sql = "SELECT COUNT(*) FROM module WHERE id_filiere = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        if ($stmt && (int)$stmt->fetchColumn() > 0) {
            // La filière a des modules, impossible de la supprimer
            return false;
        }
        
        // Supprimer la filière
        $sql = "DELETE FROM filiere WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        return $stmt && $stmt->rowCount() > 0;
    }
    
    /**
     * Vérifie si un nom de filière existe déjà (pour éviter les doublons)
     */
    public function existsByName(string $nomFiliere, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM filiere WHERE nom_filiere = :nom_filiere";
        $params = [':nom_filiere' => $nomFiliere];
        
        if ($excludeId !== null) {
            $sql .= " AND id != :exclude_id";
            $params[':exclude_id'] = $excludeId;
        }
        
        $stmt = $this->db->query($sql, $params);
        
        return $stmt && (int)$stmt->fetchColumn() > 0;
    }
}
