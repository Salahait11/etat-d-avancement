<?php // src/Model/ContenuSeanceModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

class ContenuSeanceModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère tous les contenus de séance.
     *
     * @return array Liste des contenus de séance
     */
    public function findAll(): array
    {
        $sql = "SELECT id, contenu, created_at, updated_at 
                FROM contenu_seance 
                ORDER BY contenu";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère un contenu de séance par son ID.
     *
     * @param int $id ID du contenu de séance
     * @return array|false Données du contenu de séance ou false si non trouvé
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT id, contenu, created_at, updated_at 
                FROM contenu_seance 
                WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Crée un nouveau contenu de séance.
     *
     * @param array $data Données du contenu de séance (contenu)
     * @return int|false ID du nouveau contenu de séance ou false en cas d'échec
     */
    public function create(array $data): int|false
    {
        $sql = "INSERT INTO contenu_seance (contenu) 
                VALUES (:contenu)";
        
        $params = [
            ':contenu' => $data['contenu']
        ];
        
        if ($this->db->query($sql, $params)) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Met à jour un contenu de séance existant.
     *
     * @param int $id ID du contenu de séance à mettre à jour
     * @param array $data Nouvelles données (contenu)
     * @return bool Succès de la mise à jour
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE contenu_seance 
                SET contenu = :contenu, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':contenu' => $data['contenu']
        ];
        
        return $this->db->query($sql, $params) ? true : false;
    }

    /**
     * Supprime un contenu de séance.
     *
     * @param int $id ID du contenu de séance à supprimer
     * @return bool Succès de la suppression
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM contenu_seance WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Compte tous les contenus de séance (avec recherche possible)
     * @param string|null $search
     * @return int
     */
    public function countAll(?string $search = null): int
    {
        $sql = "SELECT COUNT(*) FROM contenu_seance";
        $params = [];
        if ($search) {
            $sql .= " WHERE contenu LIKE :s";
            $params[':s'] = "%$search%";
        }
        $stmt = $this->db->query($sql, $params);
        return $stmt ? (int)$stmt->fetchColumn() : 0;
    }

    /**
     * Récupère une page de contenus de séance (avec recherche possible)
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function findPaged(int $limit, int $offset, ?string $search = null): array
    {
        $sql = "SELECT * FROM contenu_seance";
        $params = [];
        if ($search) {
            $sql .= " WHERE contenu LIKE :s";
            $params[':s'] = "%$search%";
        }
        $sql .= " ORDER BY id ASC LIMIT :lim OFFSET :off";
        $stmt = $this->db->getPdo()->prepare($sql);
        if ($search) {
            $stmt->bindValue(':s', $params[':s'], PDO::PARAM_STR);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
