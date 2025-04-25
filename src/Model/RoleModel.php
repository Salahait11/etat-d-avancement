<?php // src/Model/RoleModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;
use PDOStatement;

class RoleModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Trouve un rôle par son nom.
     * @param string $roleName Nom exact du rôle.
     * @return array|false Rôle ou false si non trouvé.
     */
    public function findByName(string $roleName): array|false
    {
        $sql = "SELECT id, nom, description FROM roles WHERE nom = :nom LIMIT 1";
        $stmt = $this->db->query($sql, [':nom' => $roleName]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Trouve un rôle par son ID.
     * @param int $id
     * @return array|false
     */
     public function findById(int $id): array|false
     {
          $sql = "SELECT id, nom, description FROM roles WHERE id = :id";
          $stmt = $this->db->query($sql, [':id' => $id]);
          return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
     }

    /**
     * Récupère tous les rôles.
     * @return array
     */
    public function findAll(): array
    {
         $sql = "SELECT id, nom, description FROM roles ORDER BY nom ASC";
         $stmt = $this->db->query($sql);
         return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    // --- Méthodes Create, Update, Delete (pour Admin plus tard) ---
    /*
    public function create(array $data): int|false { ... }
    public function update(int $id, array $data): bool { ... }
    public function delete(int $id): bool { ... }
    */
}
