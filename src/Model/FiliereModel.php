<?php // src/Model/FiliereModel.php

declare(strict_types=1);

namespace App\Model;

use PDO; // Importer la classe PDO pour l'utiliser

class FiliereModel
{
    private PDO $db; // Propriété pour stocker la connexion PDO

    /**
     * Constructeur qui reçoit la connexion PDO (Injection de Dépendance).
     */
    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    /**
     * Récupère toutes les filières, triées par nom.
     *
     * @return array Tableau contenant toutes les filières (ou un tableau vide).
     */
    public function findAll(): array
    {
        // Sélectionne les colonnes nécessaires
        $sql = "SELECT id, nom_filiere, description, niveau, duree_totale, created_at, updated_at
                FROM filiere
                ORDER BY nom_filiere ASC"; // Tri par nom pour un affichage logique
        $stmt = $this->db->query($sql); // Utilise query car pas de variable externe
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupère toutes les lignes
    }

    /**
     * Trouve une filière spécifique par son ID.
     *
     * @param int $id L'ID de la filière à rechercher.
     * @return array|false Les données de la filière ou false si non trouvée.
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT id, nom_filiere, description, niveau, duree_totale, created_at, updated_at
                FROM filiere
                WHERE id = :id"; // Utilise un placeholder
        $stmt = $this->db->prepare($sql); // Prépare la requête
        $stmt->bindValue(':id', $id, PDO::PARAM_INT); // Lie la valeur de l'ID (sécurisé)
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Récupère la ligne unique ou false
    }

    /**
     * Crée une nouvelle filière dans la base de données.
     * NOTE: La validation des données doit être faite *avant* d'appeler cette méthode (dans le contrôleur).
     *
     * @param array $data Tableau associatif contenant les données de la filière.
     *                    Attend les clés : 'nom_filiere', 'description', 'niveau', 'duree_totale'.
     * @return int|false L'ID de la nouvelle filière insérée ou false en cas d'échec.
     */
    public function create(array $data): int|false
    {
        // Vérifie que les clés attendues sont présentes (vérification basique)
        if (!isset($data['nom_filiere'], $data['niveau'], $data['duree_totale'])) {
             trigger_error("Données manquantes pour la création de la filière.", E_USER_WARNING);
             return false;
        }

        $sql = "INSERT INTO filiere (nom_filiere, description, niveau, duree_totale, created_at, updated_at)
                VALUES (:nom_filiere, :description, :niveau, :duree_totale, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);

        try {
            $stmt->bindValue(':nom_filiere', $data['nom_filiere']);
            // Utilise null si la description n'est pas fournie ou est vide
            $stmt->bindValue(':description', $data['description'] ?? null, $data['description'] ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':niveau', $data['niveau']);
            $stmt->bindValue(':duree_totale', $data['duree_totale'], PDO::PARAM_INT); // S'assurer que c'est un entier

            if ($stmt->execute()) {
                return (int) $this->db->lastInsertId(); // Retourne l'ID généré
            }
            return false;
        } catch (\PDOException $e) {
            // Gérer les erreurs spécifiques (ex: violation de clé unique sur nom_filiere)
            if ($e->getCode() == 23000) { // Code SQLSTATE pour violation de contrainte d'unicité
                error_log("Erreur création filière: Le nom '{$data['nom_filiere']}' existe déjà. " . $e->getMessage());
            } else {
                error_log("Erreur DB lors de la création de filière : " . $e->getMessage());
            }
            return false; // Indique un échec
        }
    }

    /**
     * Met à jour une filière existante.
     * NOTE: Validation des données à faire dans le contrôleur.
     *
     * @param int $id L'ID de la filière à mettre à jour.
     * @param array $data Tableau associatif des données à mettre à jour.
     *                    Clés possibles : 'nom_filiere', 'description', 'niveau', 'duree_totale'.
     * @return bool True si la mise à jour a réussi (ou si aucune ligne n'a été affectée), false en cas d'erreur.
     */
    public function update(int $id, array $data): bool
    {
         // Vérification simple : au moins une donnée doit être fournie
        if (empty($data)) {
             trigger_error("Aucune donnée fournie pour la mise à jour de la filière ID: $id", E_USER_WARNING);
             return false;
        }
         // Vérifier si la filière existe avant de tenter la mise à jour
         if ($this->findById($id) === false) {
            trigger_error("Tentative de mise à jour d'une filière inexistante ID: $id", E_USER_WARNING);
            return false;
         }


        // Construction dynamique de la requête UPDATE (uniquement pour les champs fournis)
        $fields = [];
        $params = [':id' => $id]; // Toujours besoin de l'ID pour le WHERE

        // Ajouter les champs à mettre à jour s'ils sont présents dans $data
        if (isset($data['nom_filiere'])) {
            $fields[] = 'nom_filiere = :nom_filiere';
            $params[':nom_filiere'] = $data['nom_filiere'];
        }
        if (isset($data['description'])) {
            $fields[] = 'description = :description';
            // Gérer la description vide comme NULL
            $params[':description'] = !empty($data['description']) ? $data['description'] : null;
        }
         if (isset($data['niveau'])) {
            $fields[] = 'niveau = :niveau';
            $params[':niveau'] = $data['niveau'];
        }
         if (isset($data['duree_totale'])) {
            $fields[] = 'duree_totale = :duree_totale';
            $params[':duree_totale'] = (int) $data['duree_totale']; // Assurer le type entier
        }

        // Si aucun champ à mettre à jour n'a été fourni (après vérification isset)
         if (empty($fields)) {
             trigger_error("Aucun champ valide fourni pour la mise à jour de la filière ID: $id", E_USER_WARNING);
             return false; // Ou retourner true car techniquement rien n'a échoué ? Débatable.
         }

        // Ajouter la date de mise à jour automatique
        $fields[] = 'updated_at = NOW()';

        // Construire la requête finale
        $sql = "UPDATE filiere SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        try {
            // Exécuter avec toutes les valeurs liées (PDO gère les types automatiquement pour bindValue dans execute)
            return $stmt->execute($params);
            // execute() retourne true même si 0 ligne affectée (si les données sont identiques)
        } catch (\PDOException $e) {
             if ($e->getCode() == 23000) {
                error_log("Erreur MàJ filière: Violation de contrainte (ex: nom dupliqué?) ID: $id. " . $e->getMessage());
             } else {
                error_log("Erreur DB lors de la MàJ filière ID: $id : " . $e->getMessage());
             }
             return false;
        }
    }

    /**
     * Supprime une filière par son ID.
     * ATTENTION: Vérifier les conséquences (ON DELETE CASCADE sur les modules liés).
     *
     * @param int $id L'ID de la filière à supprimer.
     * @return bool True si la suppression a réussi, false sinon.
     */
    public function delete(int $id): bool
    {
        // Vérifier si la filière existe avant de tenter la suppression (optionnel mais propre)
        if ($this->findById($id) === false) {
           trigger_error("Tentative de suppression d'une filière inexistante ID: $id", E_USER_WARNING);
           return false; // La filière n'existe pas, on peut considérer que la suppression a "réussi" ou échoué.
        }

        $sql = "DELETE FROM filiere WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (\PDOException $e) {
            // Gérer les erreurs potentielles (ex: contraintes FK si ON DELETE n'est pas CASCADE)
            error_log("Erreur DB lors de la suppression filière ID: $id : " . $e->getMessage());
            return false;
        }
    }
}