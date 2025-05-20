<?php // src/Model/UtilisateurModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database; // Utilise notre classe Database
use PDO;            // Juste pour les constantes PDO::PARAM_*
use PDOStatement;

class UtilisateurModel
{
    private Database $db; // Instance de notre classe Database

    public function __construct() // Le constructeur n'a plus besoin de $pdo
    {
        $this->db = Database::getInstance(); // Obtient l'instance via le Singleton
    }
    
    /**
     * Récupère tous les utilisateurs.
     * 
     * @return array Liste de tous les utilisateurs
     */
    public function findAll(): array
    {
        $sql = "SELECT id, nom, prenom, email, statut, created_at, updated_at 
                FROM utilisateur 
                ORDER BY nom, prenom";
        $stmt = $this->db->query($sql);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
    
    /**
     * Trouve un utilisateur par son ID.
     * 
     * @param int $id ID de l'utilisateur
     * @return array|false Données de l'utilisateur ou false si non trouvé
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT id, nom, prenom, email, mot_de_passe, statut, created_at, updated_at 
                FROM utilisateur 
                WHERE id = :id 
                LIMIT 1";
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Trouve un utilisateur par son email.
     */
    public function findByEmail(string $email): array|false
    {
        $sql = "SELECT id, nom, prenom, email, mot_de_passe, statut
                FROM utilisateur
                WHERE email = :email
                LIMIT 1";
        // Utilise la méthode query de notre classe Database
        $stmt = $this->db->query($sql, [':email' => $email]);
        // Vérifie si la requête a réussi avant de fetch
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Vérifie les identifiants de connexion.
     */
    public function verifyLogin(string $email, string $plainPassword): array|false
    {
        $user = $this->findByEmail($email);

        // Utilisateur non trouvé ou inactif
        if ($user === false || $user['statut'] !== 'actif') {
            return false;
        }

        // Vérification du mot de passe (password_verify est globale, pas besoin de $this)
        if (password_verify($plainPassword, $user['mot_de_passe'])) {
            unset($user['mot_de_passe']); // Ne pas retourner le hash
            return $user; // Succès
        }

        // Mot de passe incorrect
        return false;
    }
    
    /**
     * Crée un nouvel utilisateur.
     * 
     * @param array $data Données de l'utilisateur (nom, prenom, email, mot_de_passe, statut)
     * @return int|false ID du nouvel utilisateur ou false en cas d'échec
     */
    public function create(array $data): int|false
    {
        // Vérifier si l'email existe déjà
        if ($this->findByEmail($data['email'])) {
            return false; // Email déjà utilisé
        }
        
        // Hacher le mot de passe
        $hashedPassword = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, statut) 
                VALUES (:nom, :prenom, :email, :mot_de_passe, :statut)";
        
        $params = [
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':mot_de_passe' => $hashedPassword,
            ':statut' => $data['statut'] ?? 'actif'
        ];
        
        if ($this->db->query($sql, $params)) {
            return (int)$this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour un utilisateur existant.
     * 
     * @param int $id ID de l'utilisateur à mettre à jour
     * @param array $data Données à mettre à jour
     * @return bool Succès de la mise à jour
     */
    public function update(int $id, array $data): bool
    {
        // Vérifier si l'utilisateur existe
        $user = $this->findById($id);
        if (!$user) {
            return false;
        }
        
        // Vérifier si l'email est déjà utilisé par un autre utilisateur
        if (isset($data['email']) && $data['email'] !== $user['email']) {
            $existingUser = $this->findByEmail($data['email']);
            if ($existingUser && $existingUser['id'] !== $id) {
                return false; // Email déjà utilisé par un autre utilisateur
            }
        }
        
        // Construire la requête SQL dynamiquement en fonction des champs fournis
        $fields = [];
        $params = [];
        
        if (isset($data['nom'])) {
            $fields[] = "nom = :nom";
            $params[':nom'] = $data['nom'];
        }
        
        if (isset($data['prenom'])) {
            $fields[] = "prenom = :prenom";
            $params[':prenom'] = $data['prenom'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        
        if (isset($data['statut'])) {
            $fields[] = "statut = :statut";
            $params[':statut'] = $data['statut'];
        }
        
        // Mettre à jour le mot de passe uniquement s'il est fourni
        if (isset($data['mot_de_passe']) && !empty($data['mot_de_passe'])) {
            $fields[] = "mot_de_passe = :mot_de_passe";
            $params[':mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT);
        }
        
        // Ajouter la date de mise à jour
        $fields[] = "updated_at = NOW()";
        
        // S'il n'y a rien à mettre à jour, retourner true
        if (empty($fields)) {
            return true;
        }
        
        $sql = "UPDATE utilisateur SET " . implode(", ", $fields) . " WHERE id = :id";
        $params[':id'] = $id;
        
        return $this->db->query($sql, $params) ? true : false;
    }
    
    /**
     * Supprime un utilisateur.
     * 
     * @param int $id ID de l'utilisateur à supprimer
     * @return bool Succès de la suppression
     */
    public function delete(int $id): bool
    {
        // Vérifier si l'utilisateur existe
        if (!$this->findById($id)) {
            return false;
        }
        
        // Supprimer d'abord les rôles associés à l'utilisateur
        $this->removeAllRoles($id);
        
        // Supprimer l'utilisateur
        $sql = "DELETE FROM utilisateur WHERE id = :id";
        return $this->db->query($sql, [':id' => $id]) ? true : false;
    }

    /**
     * Récupère les noms des rôles associés à un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur.
     * @return array Tableau contenant les noms des rôles (ex: ['admin', 'formateur']). Vide si aucun rôle.
     */
    public function getUserRoleNames(int $userId): array
    {
        $sql = "SELECT r.nom -- Sélectionne seulement le nom du rôle
                FROM roles AS r
                JOIN utilisateur_roles AS ur ON r.id = ur.id_roles -- Jointure avec la table pivot
                WHERE ur.id_utilisateur = :userId"; // Filtre par l'ID utilisateur

        $stmt = $this->db->query($sql, [':userId' => $userId]);

        if (!$stmt) {
            return []; // Retourne tableau vide en cas d'erreur
        }

        // fetchAll(PDO::FETCH_COLUMN, 0) récupère seulement la première colonne (r.nom) de chaque ligne
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    /**
     * Récupère les IDs des rôles associés à un utilisateur.
     *
     * @param int $userId L'ID de l'utilisateur.
     * @return array Tableau contenant les IDs des rôles.
     */
    public function getUserRoleIds(int $userId): array
    {
        $sql = "SELECT id_roles FROM utilisateur_roles WHERE id_utilisateur = :userId";
        $stmt = $this->db->query($sql, [':userId' => $userId]);
        
        if (!$stmt) {
            return [];
        }
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }
    
    /**
     * Trouve tous les utilisateurs ayant un rôle spécifique.
     *
     * @param string $roleName Nom du rôle (ex: 'admin', 'formateur')
     * @return array Liste des utilisateurs ayant ce rôle
     */
    public function findByRole(string $roleName): array
    {
        $sql = "SELECT u.id, u.nom, u.prenom, u.email, u.statut, u.created_at, u.updated_at 
                FROM utilisateur u
                JOIN utilisateur_roles ur ON u.id = ur.id_utilisateur
                JOIN roles r ON ur.id_roles = r.id
                WHERE r.nom = :role_name
                AND u.statut = 'actif'
                ORDER BY u.nom, u.prenom";
        
        return $this->db->query($sql, [':role_name' => $roleName])->fetchAll();
    }
    
    /**
     * Attribue un rôle à un utilisateur.
     *
     * @param int $userId ID de l'utilisateur
     * @param int $roleId ID du rôle
     * @return bool Succès de l'opération
     */
    public function assignRole(int $userId, int $roleId): bool
    {
        // Vérifier si l'association existe déjà
        $sql = "SELECT COUNT(*) FROM utilisateur_roles 
                WHERE id_utilisateur = :userId AND id_roles = :roleId";
        $stmt = $this->db->query($sql, [':userId' => $userId, ':roleId' => $roleId]);
        
        if ($stmt && $stmt->fetchColumn() > 0) {
            return true; // L'association existe déjà
        }
        
        // Créer l'association
        $sql = "INSERT INTO utilisateur_roles (id_utilisateur, id_roles) 
                VALUES (:userId, :roleId)";
        return $this->db->query($sql, [':userId' => $userId, ':roleId' => $roleId]) ? true : false;
    }
    
    /**
     * Retire un rôle à un utilisateur.
     *
     * @param int $userId ID de l'utilisateur
     * @param int $roleId ID du rôle
     * @return bool Succès de l'opération
     */
    public function removeRole(int $userId, int $roleId): bool
    {
        $sql = "DELETE FROM utilisateur_roles 
                WHERE id_utilisateur = :userId AND id_roles = :roleId";
        return $this->db->query($sql, [':userId' => $userId, ':roleId' => $roleId]) ? true : false;
    }
    
    /**
     * Retire tous les rôles d'un utilisateur.
     *
     * @param int $userId ID de l'utilisateur
     * @return bool Succès de l'opération
     */
    public function removeAllRoles(int $userId): bool
    {
        $sql = "DELETE FROM utilisateur_roles WHERE id_utilisateur = :userId";
        return $this->db->query($sql, [':userId' => $userId]) ? true : false;
    }
    
    /**
     * Met à jour les rôles d'un utilisateur.
     *
     * @param int $userId ID de l'utilisateur
     * @param array $roleIds IDs des rôles à attribuer
     * @return bool Succès de l'opération
     */
    public function updateRoles(int $userId, array $roleIds): bool
    {
        // Supprimer tous les rôles existants
        $this->removeAllRoles($userId);
        
        // Ajouter les nouveaux rôles
        $success = true;
        foreach ($roleIds as $roleId) {
            if (!$this->assignRole($userId, (int)$roleId)) {
                $success = false;
            }
        }
        
        return $success;
    }
}