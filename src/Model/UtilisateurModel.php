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

  
}