<?php // src/Model/UtilisateurModel.php

declare(strict_types=1);

namespace App\Model;

use PDO; // Importer la classe PDO

class UtilisateurModel
{
    private PDO $db; // Pour stocker la connexion PDO

    /**
     * Le constructeur reçoit la connexion PDO (Injection de dépendance).
     */
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Trouve un utilisateur par son email.
     *
     * @param string $email L'email à rechercher.
     * @return array|false Données utilisateur (tableau associatif) ou false si non trouvé.
     */
    public function findByEmail(string $email): array|false
    {
        // Requêtes préparées pour éviter les injections SQL !
        $sql = "SELECT id, nom, prenom, email, mot_de_passe, statut
                FROM utilisateur
                WHERE email = :email
                LIMIT 1"; // Bonne pratique pour résultat unique attendu

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'utilisateur ou false
    }

    /**
     * Trouve un utilisateur par son ID.
     *
     * @param int $id L'ID utilisateur.
     * @return array|false Données utilisateur ou false si non trouvé.
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT id, nom, prenom, email, statut, created_at, updated_at
                FROM utilisateur
                WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Vérifie les identifiants de connexion d'un utilisateur.
     *
     * @param string $email L'email fourni.
     * @param string $plainPassword Le mot de passe en clair fourni.
     * @return array|false Données utilisateur si succès, false sinon.
     */
    public function verifyLogin(string $email, string $plainPassword): array|false
    {
        $user = $this->findByEmail($email);

        // 1. Utilisateur existe ?
        if ($user === false) {
            return false;
        }

        // 2. Compte actif ?
        if ($user['statut'] !== 'actif') {
            error_log("Tentative de connexion pour l'utilisateur inactif : " . $email);
            return false;
        }

        // 3. Vérifier le mot de passe fourni contre le hash stocké
        if (password_verify($plainPassword, $user['mot_de_passe'])) {
            // Mot de passe correct !
            unset($user['mot_de_passe']); // Ne pas retourner le hash
            return $user; // Retourne les données utilisateur
        } else {
            // Mot de passe incorrect
            return false;
        }
    }

    /**
     * Crée un nouvel utilisateur (pour tests ou future fonction d'inscription).
     * Hashe le mot de passe automatiquement.
     * NOTE : Suppose que la validation a été faite avant.
     *
     * @param string $nom
     * @param string $prenom
     * @param string $email
     * @param string $plainPassword Mot de passe en clair.
     * @param string $statut Défaut 'actif'.
     * @return int|false ID du nouvel utilisateur ou false en cas d'échec.
     */
    public function createUser(string $nom, string $prenom, string $email, string $plainPassword, string $statut = 'actif'): int|false
    {
        // Hasher le mot de passe de manière sécurisée
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);
        if ($hashedPassword === false) {
            error_log("Échec du hachage de mot de passe pour : " . $email);
            return false;
        }

        $sql = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, statut, created_at, updated_at)
                VALUES (:nom, :prenom, :email, :mot_de_passe, :statut, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':mot_de_passe', $hashedPassword); // Stocke le HASH
        $stmt->bindValue(':statut', $statut);

        try {
            if ($stmt->execute()) {
                return (int) $this->db->lastInsertId();
            }
            return false;
        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { // Violation de contrainte (ex: email unique)
                error_log("Tentative de création d'utilisateur avec email dupliqué : " . $email);
            } else {
                error_log("Erreur DB lors de la création d'utilisateur : " . $e->getMessage());
            }
            return false;
        }
    }

    // Ajouter ici plus tard : updateUser, deleteUser, getUserRoles, etc.
}