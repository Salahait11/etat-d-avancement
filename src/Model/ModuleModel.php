<?php // src/Model/ModuleModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;
use PDOStatement;

class ModuleModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère tous les modules avec le nom de leur filière associée.
     *
     * @return array Tableau des modules.
     */
    public function findAllWithFiliere(): array
    {
        // Jointure pour récupérer le nom de la filière
        $sql = "SELECT
                    m.id, m.intitule, m.objectif, m.duree, m.id_filiere,
                    f.nom_filiere, -- Sélectionne le nom de la filière
                    m.created_at, m.updated_at
                FROM module AS m
                JOIN filiere AS f ON m.id_filiere = f.id -- La condition de jointure
                ORDER BY f.nom_filiere ASC, m.intitule ASC"; // Trie par filière puis par intitulé

        $stmt = $this->db->query($sql); // Pas de paramètres externes
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : []; // Retourne un tableau vide en cas d'erreur
    }

    /**
     * Trouve un module spécifique par son ID, avec le nom de sa filière.
     *
     * @param int $id L'ID du module.
     * @return array|false Les données du module ou false si non trouvé.
     */
    public function findByIdWithFiliere(int $id): array|false
    {
        $sql = "SELECT
                    m.id, m.intitule, m.objectif, m.duree, m.id_filiere,
                    f.nom_filiere,
                    m.created_at, m.updated_at
                FROM module AS m
                JOIN filiere AS f ON m.id_filiere = f.id
                WHERE m.id = :id"; // Placeholder pour l'ID

        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Crée un nouveau module.
     *
     * @param array $data Doit contenir 'intitule', 'objectif', 'duree', 'id_filiere'.
     * @return int|false L'ID du nouveau module ou false en cas d'échec.
     */
    public function create(array $data): int|false
    {
        // Assurer que les clés nécessaires sont présentes (validation basique)
        if (!isset($data['intitule'], $data['objectif'], $data['duree'], $data['id_filiere'])) {
            trigger_error("Données manquantes pour la création du module.", E_USER_WARNING);
            return false;
        }

        $sql = "INSERT INTO module (intitule, objectif, duree, id_filiere, created_at, updated_at)
                VALUES (:intitule, :objectif, :duree, :id_filiere, NOW(), NOW())";

        $params = [
            ':intitule' => $data['intitule'],
            ':objectif' => $data['objectif'],
            ':duree' => $data['duree'], // Assumer que c'est déjà un entier validé
            ':id_filiere' => $data['id_filiere'] // Assumer que c'est déjà un entier validé
        ];

        $stmt = $this->db->query($sql, $params);

        // Vérifier si l'insertion a réussi et retourner l'ID
        if ($stmt) {
             // Pour INSERT/UPDATE/DELETE, query retourne un PDOStatement, mais on veut l'ID.
             // On utilise l'instance PDO sous-jacente.
            return (int) $this->db->getPdo()->lastInsertId();
        }

        // Log d'erreur déjà fait dans Database::query en cas d'exception
        return false;
    }


    /**
     * Met à jour un module existant.
     *
     * @param int $id L'ID du module à mettre à jour.
     * @param array $data Les nouvelles données du module.
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function update(int $id, array $data): bool
    {
        // Vérifier que les clés nécessaires sont présentes
        if (!isset($data['intitule'], $data['objectif'], $data['duree'], $data['id_filiere'])) {
            trigger_error("Données manquantes pour la mise à jour du module.", E_USER_WARNING);
            return false;
        }

        $sql = "UPDATE module 
                SET intitule = :intitule, 
                    objectif = :objectif, 
                    duree = :duree, 
                    id_filiere = :id_filiere, 
                    updated_at = NOW() 
                WHERE id = :id";

        $params = [
            ':intitule' => $data['intitule'],
            ':objectif' => $data['objectif'],
            ':duree' => $data['duree'],
            ':id_filiere' => $data['id_filiere'],
            ':id' => $id
        ];

        $stmt = $this->db->query($sql, $params);
        
        // Vérifier si la mise à jour a réussi
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Supprime un module par son ID.
     *
     * @param int $id L'ID du module à supprimer.
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM module WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        
        // Vérifier si la suppression a réussi
        return $stmt && $stmt->rowCount() > 0;
    }

    /**
     * Compte tous les modules (optionnellement filtrés par recherche)
     * @param string|null $search
     * @return int
     */
    public function countAllWithFiliere(?string $search = null): int
    {
        $sql = "SELECT COUNT(*) FROM module m JOIN filiere f ON m.id_filiere=f.id";
        $params = [];
        if ($search) {
            $sql .= " WHERE m.intitule LIKE :s1 OR f.nom_filiere LIKE :s2";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
        }
        $stmt = $this->db->query($sql, $params);
        return $stmt ? (int)$stmt->fetchColumn() : 0;
    }

    /**
     * Récupère une page de modules avec filière et recherche
     * @param int $limit
     * @param int $offset
     * @param string|null $search
     * @return array
     */
    public function findPagedWithFiliere(int $limit, int $offset, ?string $search = null): array
    {
        $sql = "SELECT m.id,m.intitule,m.objectif,m.duree,m.id_filiere,f.nom_filiere,m.created_at,m.updated_at
                FROM module m JOIN filiere f ON m.id_filiere=f.id";
        $params = [];
        if ($search) {
            $sql .= " WHERE m.intitule LIKE :s1 OR f.nom_filiere LIKE :s2";
            $params[':s1'] = "%$search%";
            $params[':s2'] = "%$search%";
        }
        $sql .= " ORDER BY f.nom_filiere ASC, m.intitule ASC LIMIT :lim OFFSET :off";
        $params[':lim'] = $limit;
        $params[':off'] = $offset;
        $stmt = $this->db->getPdo()->prepare($sql);
        foreach ($params as $k => $v) {
            $type = is_int($v) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($k, $v, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un module par son ID
     *
     * @param int $id ID du module
     * @return array|false Données du module ou false si non trouvé
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT m.*, f.nom as filiere_nom 
                FROM module m 
                LEFT JOIN filiere f ON m.id_filiere = f.id 
                WHERE m.id = :id";
        
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Récupère tous les modules
     *
     * @return array Liste des modules
     */
    public function findAll(): array
    {
        $sql = "SELECT * FROM module ORDER BY intitule ASC";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Vérifie si un module est utilisé dans un état d'avancement
     * 
     * @param int $moduleId ID du module à vérifier
     * @return bool True si le module est utilisé, false sinon
     */
    public function isUsedInEtatAvancement(int $moduleId): bool
    {
        $sql = "SELECT COUNT(*) as count FROM etat_avancement WHERE id_module = :module_id";
        $stmt = $this->db->query($sql, [':module_id' => $moduleId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
}
