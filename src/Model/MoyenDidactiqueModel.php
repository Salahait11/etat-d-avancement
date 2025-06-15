<?php // src/Model/MoyenDidactiqueModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;
use PDOException;

class MoyenDidactiqueModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère tous les moyens didactiques.
     *
     * @return array Liste des moyens didactiques
     */
    public function findAll(): array
    {
        $sql = "SELECT id, moyen, description, created_at, updated_at 
                FROM moyen_didactique 
                ORDER BY moyen";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère un moyen didactique par son ID.
     *
     * @param int $id ID du moyen didactique
     * @return array|false Données du moyen didactique ou false si non trouvé
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT id, moyen, description, created_at, updated_at 
                FROM moyen_didactique 
                WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Crée un nouveau moyen didactique.
     *
     * @param array $data Données du moyen didactique (moyen, description)
     * @return int|false ID du nouveau moyen didactique ou false en cas d'échec
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO moyen_didactique (moyen, description) 
                VALUES (:moyen, :description)";
        
        $params = [
            ':moyen' => $data['moyen'],
            ':description' => $data['description'] ?? ''
        ];
        
        if ($this->db->query($sql, $params)) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour un moyen didactique existant.
     *
     * @param int $id ID du moyen didactique à mettre à jour
     * @param array $data Nouvelles données (moyen, description)
     * @return bool Succès de la mise à jour
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE moyen_didactique 
                SET moyen = :moyen, 
                    description = :description, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':moyen' => $data['moyen'],
            ':description' => $data['description'] ?? ''
        ];
        
        return $this->db->query($sql, $params) ? true : false;
    }

    /**
     * Vérifie si un moyen didactique est utilisé dans des états d'avancement
     * @param int $moyenId ID du moyen didactique
     * @return bool True si le moyen est utilisé, false sinon
     */
    public function isUsedInEtatsAvancement(int $moyenId): bool
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM etat_avancement_moyen WHERE id_moyen_didactique = :moyen_id";
            $stmt = $this->db->query($sql, [':moyen_id' => $moyenId]);
            if ($stmt) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                return $result['count'] > 0;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de l'utilisation du moyen didactique : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un moyen didactique.
     *
     * @param int $id ID du moyen didactique à supprimer
     * @return bool Succès de la suppression
     */
    public function delete(int $id): bool
    {
        try {
            // Vérifier d'abord si le moyen est utilisé
            if ($this->isUsedInEtatsAvancement($id)) {
                error_log("Tentative de suppression d'un moyen didactique utilisé dans des états d'avancement : " . $id);
                return false;
            }

            $sql = "DELETE FROM moyen_didactique WHERE id = :id";
            $stmt = $this->db->query($sql, [':id' => $id]);
            return $stmt !== false;
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression du moyen didactique : " . $e->getMessage());
            return false;
        }
    }
}
