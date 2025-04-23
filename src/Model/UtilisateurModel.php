<?php // src/Model/UtilisateurModel.php

declare(strict_types=1);

namespace App\Model;

use PDO; // Importer la classe PDO

class UtilisateurModel
{
    private PDO $db; // Stocke la connexion PDO

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
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
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Retourne l'utilisateur ou false
    }

    /**
     * Vérifie les identifiants de connexion.
     */
    public function verifyLogin(string $email, string $plainPassword): array|false
    {
        $user = $this->findByEmail($email);

        if ($user === false || $user['statut'] !== 'actif') {
            return false; // Utilisateur non trouvé ou inactif
        }

        // Vérifie le mot de passe haché
        if (password_verify($plainPassword, $user['mot_de_passe'])) {
            unset($user['mot_de_passe']); // Ne pas garder le hash en mémoire/session
            return $user; // Succès
        }

        return false; // Mot de passe incorrect
    }

    // Ajouter ici plus tard : createUser, findById, update, delete...
}