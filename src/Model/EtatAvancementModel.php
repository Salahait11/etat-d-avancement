<?php // src/Model/EtatAvancementModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;

class EtatAvancementModel
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère tous les états d'avancement avec les informations liées (module, formateur)
     *
     * @param array $filters Filtres optionnels (date_seance, module_id)
     * @return array Liste des états d'avancement
     */
    public function findAllWithDetails(array $filters = []): array
    {
        // Construction de la requête avec filtres optionnels
        $sql = "SELECT ea.*, 
                       m.intitule as module_intitule, 
                       CONCAT(u.prenom, ' ', u.nom) as formateur_nom
                FROM etat_avancement ea
                LEFT JOIN module m ON ea.id_module = m.id
                LEFT JOIN utilisateur u ON ea.id_formateur = u.id";
        $conditions = [];
        $params = [];
        if (!empty($filters['date_seance'])) {
            $conditions[] = 'ea.date_seance = :date_seance';
            $params[':date_seance'] = $filters['date_seance'];
        }
        if (!empty($filters['module_id'])) {
            $conditions[] = 'ea.id_module = :module_id';
            $params[':module_id'] = $filters['module_id'];
        }
        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY ea.date_seance DESC';
        $stmt = $this->db->query($sql, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère un état d'avancement par son ID avec toutes les informations liées
     *
     * @param int $id ID de l'état d'avancement
     * @return array|false Données de l'état d'avancement ou false si non trouvé
     */
    public function findByIdWithDetails(int $id): array|false
    {
        $sql = "SELECT ea.*, 
                       m.intitule as module_intitule, 
                       CONCAT(u.prenom, ' ', u.nom) as formateur_nom
                FROM etat_avancement ea
                LEFT JOIN module m ON ea.id_module = m.id
                LEFT JOIN utilisateur u ON ea.id_formateur = u.id
                WHERE ea.id = :id";
        
        $stmt = $this->db->query($sql, [':id' => $id]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    /**
     * Récupère les objectifs pédagogiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return array Liste des objectifs pédagogiques
     */
    public function getObjectifsByEtatId(int $etatId): array
    {
        $sql = "SELECT op.id, op.objectif, eao.statut
                FROM etat_avancement_objectif eao
                JOIN objectif_pedagogique op ON eao.id_objectif_pedagogique = op.id
                WHERE eao.id_etat_avancement = :etat_id";
        
        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère les contenus de séance liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return array Liste des contenus de séance
     */
    public function getContenusByEtatId(int $etatId): array
    {
        $sql = "SELECT cs.id, cs.contenu, eac.statut
                FROM etat_avancement_contenu eac
                JOIN contenu_seance cs ON eac.id_contenu_seance = cs.id
                WHERE eac.id_etat_avancement = :etat_id";
        
        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère les moyens didactiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return array Liste des moyens didactiques
     */
    public function getMoyensByEtatId(int $etatId): array
    {
        $sql = "SELECT md.id, md.moyen, eam.statut
                FROM etat_avancement_moyen eam
                JOIN moyen_didactique md ON eam.id_moyen_didactique = md.id
                WHERE eam.id_etat_avancement = :etat_id";
        
        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère les stratégies d'évaluation liées à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return array Liste des stratégies d'évaluation
     */
    public function getStrategiesByEtatId(int $etatId): array
    {
        $sql = "SELECT se.id, se.strategie, eas.statut
                FROM etat_avancement_strategie eas
                JOIN strategie_evaluation se ON eas.id_strategie_evaluation = se.id
                WHERE eas.id_etat_avancement = :etat_id";
        
        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Crée un nouvel état d'avancement
     *
     * @param array $data Données de l'état d'avancement
     * @return int|false ID du nouvel état d'avancement ou false en cas d'échec
     */
    public function create(array $data): int|false
    {
        $this->db->getPdo()->beginTransaction();
        
        try {
            // Insertion de l'état d'avancement principal
            $sql = "INSERT INTO etat_avancement (
                        id_module, 
                        id_formateur, 
                        date_seance, 
                        duree_realisee, 
                        commentaire,
                        difficultes,
                        solutions
                    ) VALUES (
                        :id_module, 
                        :id_formateur, 
                        :date_seance, 
                        :duree_realisee, 
                        :commentaire,
                        :difficultes,
                        :solutions
                    )";
            
            $params = [
                ':id_module' => $data['id_module'],
                ':id_formateur' => $data['id_formateur'],
                ':date_seance' => $data['date_seance'],
                ':duree_realisee' => $data['duree_realisee'],
                ':commentaire' => $data['commentaire'] ?? '',
                ':difficultes' => $data['difficultes'] ?? '',
                ':solutions' => $data['solutions'] ?? ''
            ];
            
            if (!$this->db->query($sql, $params)) {
                throw new \Exception("Erreur lors de l'insertion de l'état d'avancement");
            }
            
            $etatId = (int)$this->db->lastInsertId();
            
            // Insertion des objectifs pédagogiques
            if (!empty($data['objectifs'])) {
                $this->insertObjectifs($etatId, $data['objectifs']);
            }
            
            // Insertion des contenus de séance
            if (!empty($data['contenus'])) {
                $this->insertContenus($etatId, $data['contenus']);
            }
            
            // Insertion des moyens didactiques
            if (!empty($data['moyens'])) {
                $this->insertMoyens($etatId, $data['moyens']);
            }
            
            // Insertion des stratégies d'évaluation
            if (!empty($data['strategies'])) {
                $this->insertStrategies($etatId, $data['strategies']);
            }
            
            $this->db->getPdo()->commit();
            return $etatId;
            
        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            error_log("Erreur lors de la création de l'état d'avancement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour un état d'avancement existant
     *
     * @param int $id ID de l'état d'avancement
     * @param array $data Nouvelles données
     * @return bool Succès de la mise à jour
     */
    public function update(int $id, array $data): bool
    {
        $this->db->getPdo()->beginTransaction();
        
        try {
            // Mise à jour de l'état d'avancement principal
            $sql = "UPDATE etat_avancement SET 
                        id_module = :id_module, 
                        id_formateur = :id_formateur, 
                        date_seance = :date_seance, 
                        duree_realisee = :duree_realisee, 
                        commentaire = :commentaire,
                        difficultes = :difficultes,
                        solutions = :solutions,
                        updated_at = NOW()
                    WHERE id = :id";
            
            $params = [
                ':id' => $id,
                ':id_module' => $data['id_module'],
                ':id_formateur' => $data['id_formateur'],
                ':date_seance' => $data['date_seance'],
                ':duree_realisee' => $data['duree_realisee'],
                ':commentaire' => $data['commentaire'] ?? '',
                ':difficultes' => $data['difficultes'] ?? '',
                ':solutions' => $data['solutions'] ?? ''
            ];
            
            if (!$this->db->query($sql, $params)) {
                throw new \Exception("Erreur lors de la mise à jour de l'état d'avancement");
            }
            
            // Suppression des relations existantes pour les recréer
            $this->deleteRelations($id);
            
            // Insertion des objectifs pédagogiques
            if (!empty($data['objectifs'])) {
                $this->insertObjectifs($id, $data['objectifs']);
            }
            
            // Insertion des contenus de séance
            if (!empty($data['contenus'])) {
                $this->insertContenus($id, $data['contenus']);
            }
            
            // Insertion des moyens didactiques
            if (!empty($data['moyens'])) {
                $this->insertMoyens($id, $data['moyens']);
            }
            
            // Insertion des stratégies d'évaluation
            if (!empty($data['strategies'])) {
                $this->insertStrategies($id, $data['strategies']);
            }
            
            $this->db->getPdo()->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            error_log("Erreur lors de la mise à jour de l'état d'avancement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un état d'avancement et toutes ses relations
     *
     * @param int $id ID de l'état d'avancement
     * @return bool Succès de la suppression
     */
    public function delete(int $id): bool
    {
        $this->db->getPdo()->beginTransaction();
        
        try {
            // Suppression des relations
            $this->deleteRelations($id);
            
            // Suppression de l'état d'avancement principal
            $sql = "DELETE FROM etat_avancement WHERE id = :id";
            if (!$this->db->query($sql, [':id' => $id])) {
                throw new \Exception("Erreur lors de la suppression de l'état d'avancement");
            }
            
            $this->db->getPdo()->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->getPdo()->rollBack();
            error_log("Erreur lors de la suppression de l'état d'avancement: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime toutes les relations d'un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return void
     */
    private function deleteRelations(int $etatId): void
    {
        // Suppression des objectifs pédagogiques
        $sql = "DELETE FROM etat_avancement_objectif WHERE id_etat_avancement = :etat_id";
        $this->db->query($sql, [':etat_id' => $etatId]);
        
        // Suppression des contenus de séance
        $sql = "DELETE FROM etat_avancement_contenu WHERE id_etat_avancement = :etat_id";
        $this->db->query($sql, [':etat_id' => $etatId]);
        
        // Suppression des moyens didactiques
        $sql = "DELETE FROM etat_avancement_moyen WHERE id_etat_avancement = :etat_id";
        $this->db->query($sql, [':etat_id' => $etatId]);
        
        // Suppression des stratégies d'évaluation
        $sql = "DELETE FROM etat_avancement_strategie WHERE id_etat_avancement = :etat_id";
        $this->db->query($sql, [':etat_id' => $etatId]);
    }

    /**
     * Insère les objectifs pédagogiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @param array $objectifs Tableau d'IDs d'objectifs avec leur statut
     * @return void
     */
    private function insertObjectifs(int $etatId, array $objectifs): void
    {
        $sql = "INSERT INTO etat_avancement_objectif (id_etat_avancement, id_objectif_pedagogique, statut) 
                VALUES (:etat_id, :objectif_id, :statut)";
        
        foreach ($objectifs as $objectif) {
            $params = [
                ':etat_id' => $etatId,
                ':objectif_id' => $objectif['id'],
                ':statut' => $objectif['statut']
            ];
            
            $this->db->query($sql, $params);
        }
    }

    /**
     * Insère les contenus de séance liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @param array $contenus Tableau d'IDs de contenus avec leur statut
     * @return void
     */
    private function insertContenus(int $etatId, array $contenus): void
    {
        $sql = "INSERT INTO etat_avancement_contenu (id_etat_avancement, id_contenu_seance, statut) 
                VALUES (:etat_id, :contenu_id, :statut)";
        
        foreach ($contenus as $contenu) {
            $params = [
                ':etat_id' => $etatId,
                ':contenu_id' => $contenu['id'],
                ':statut' => $contenu['statut']
            ];
            
            $this->db->query($sql, $params);
        }
    }

    /**
     * Insère les moyens didactiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @param array $moyens Tableau d'IDs de moyens avec leur statut
     * @return void
     */
    private function insertMoyens(int $etatId, array $moyens): void
    {
        $sql = "INSERT INTO etat_avancement_moyen (id_etat_avancement, id_moyen_didactique, statut) 
                VALUES (:etat_id, :moyen_id, :statut)";
        
        foreach ($moyens as $moyen) {
            $params = [
                ':etat_id' => $etatId,
                ':moyen_id' => $moyen['id'],
                ':statut' => $moyen['statut']
            ];
            
            $this->db->query($sql, $params);
        }
    }

    /**
     * Insère les stratégies d'évaluation liées à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @param array $strategies Tableau d'IDs de stratégies avec leur statut
     * @return void
     */
    private function insertStrategies(int $etatId, array $strategies): void
    {
        $sql = "INSERT INTO etat_avancement_strategie (id_etat_avancement, id_strategie_evaluation, statut) 
                VALUES (:etat_id, :strategie_id, :statut)";
        
        foreach ($strategies as $strategie) {
            $params = [
                ':etat_id' => $etatId,
                ':strategie_id' => $strategie['id'],
                ':statut' => $strategie['statut']
            ];
            
            $this->db->query($sql, $params);
        }
    }
}
