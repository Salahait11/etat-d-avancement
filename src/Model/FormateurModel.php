<?php // src/Model/FormateurModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

class FormateurModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Liste tous les formateurs avec infos utilisateur
     */
    public function findAll(): array
    {
        $sql = "SELECT f.id, f.id_utilisateur, u.prenom, u.nom, u.email, f.specialite
                FROM formateur f
                JOIN utilisateur u ON f.id_utilisateur = u.id";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère un formateur par son ID
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT f.id, f.id_utilisateur, u.prenom, u.nom, u.email, f.specialite
                FROM formateur f
                JOIN utilisateur u ON f.id_utilisateur = u.id
                WHERE f.id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Crée un nouveau formateur et assigne le rôle
     */
    public function create(array $data): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Insertion formateur
            $sql = "INSERT INTO formateur (id_utilisateur, specialite) VALUES (:user_id, :specialite)";
            $res = $this->db->query($sql, [
                ':user_id' => $data['id_utilisateur'],
                ':specialite' => $data['specialite']
            ]);
            if (!$res) {
                throw new \Exception("Erreur lors de l'insertion dans la table formateur");
            }
            
            // Assigner rôle formateur (ID 6 pour le rôle formateur)
            $sql2 = "INSERT INTO utilisateur_roles (id_utilisateur, id_roles) VALUES (:user_id, 6)";
            $res2 = $this->db->query($sql2, [':user_id' => $data['id_utilisateur']]);
            if (!$res2) {
                throw new \Exception("Erreur lors de l'assignation du rôle formateur");
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Récupère tous les utilisateurs n'ayant pas encore le statut formateur
     */
    public function findUsersNotFormateur(): array
    {
        $sql = "SELECT id, prenom, nom, email
                FROM utilisateur
                WHERE id NOT IN (SELECT id_utilisateur FROM formateur) 
                ORDER BY nom, prenom";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Met à jour la spécialité d'un formateur
     */
    public function update(int $id, array $data): bool
    {
        $sql = "UPDATE formateur SET specialite = :specialite WHERE id = :id";
        $res = $this->db->query($sql, [
            ':specialite' => $data['specialite'],
            ':id' => $id
        ]);
        return (bool) $res;
    }

    /**
     * Supprime un formateur et retire le rôle
     */
    public function delete(int $id): bool
    {
        // Récupérer id_utilisateur
        $f = $this->findById($id);
        if (!$f) {
            return false;
        }
        $userId = $f['id_utilisateur'];
        // Supprimer formateur
        $this->db->query("DELETE FROM formateur WHERE id = :id", [':id' => $id]);
        // Retirer rôle formateur
        $this->db->query("DELETE FROM utilisateur_roles WHERE id_utilisateur = :user_id AND id_roles = 6", [':user_id' => $userId]);
        return true;
    }
}
