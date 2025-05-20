<?php // src/Model/EtatAvancementModel.php

declare(strict_types=1);

namespace App\Model;

use App\Core\Database;
use PDO;
use PDOException; // Importation de l'exception PDO

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
     * @param array $filters Filtres optionnels (date, module_id, formateur_id)
     * @return array Liste des états d'avancement
     */
    public function findAllWithDetails(array $filters = []): array
    {
        // Construction de la requête avec filtres optionnels
        $sql = "SELECT ea.*,
                       m.intitule as module_intitule,
                       CONCAT(u.prenom, ' ', u.nom) as formateur_nom,
                       ea.date as date_seance
                FROM etat_avancement ea
                LEFT JOIN module m ON ea.id_module = m.id
                LEFT JOIN formateur f ON ea.id_formateur = f.id
                LEFT JOIN utilisateur u ON f.id_utilisateur = u.id";
        $conditions = [];
        $params = [];

        if (!empty($filters['date'])) {
            $conditions[] = 'ea.date = :date';
            $params[':date'] = $filters['date'];
        }
        if (!empty($filters['module_id'])) {
            $conditions[] = 'ea.id_module = :module_id';
            $params[':module_id'] = (int) $filters['module_id']; // S'assurer que l'ID est un entier
        }
         if (!empty($filters['formateur_id'])) {
             $conditions[] = 'ea.id_formateur = :formateur_id';
             $params[':formateur_id'] = (int) $filters['formateur_id']; // S'assurer que l'ID est un entier
         }

        if ($conditions) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }
        $sql .= ' ORDER BY ea.date DESC, ea.heure DESC';

        // Assurez-vous que votre méthode $this->db->query gère correctement la préparation et l'exécution
        $stmt = $this->db->query($sql, $params);
        $results = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        
        // Formater l'heure pour chaque résultat
        foreach ($results as &$result) {
            if (isset($result['heure'])) {
                $datetime = new \DateTime($result['heure']);
                $result['heure'] = $datetime->format('H:i');
            }
        }
        
        return $results;
    }

    /**
     * Récupère un état d'avancement par son ID avec toutes les informations liées
     *
     * @param int $id ID de l'état d'avancement
     * @return array|false Données de l'état d'avancement ou false si non trouvé
     */
    public function findByIdWithDetails(int $id): array|false
    {
        error_log("Recherche de l'état d'avancement avec l'ID : " . $id);
        
        $sql = "SELECT ea.*,
                       m.intitule as module_intitule,
                       CONCAT(u.prenom, ' ', u.nom) as formateur_nom
                FROM etat_avancement ea
                LEFT JOIN module m ON ea.id_module = m.id
                LEFT JOIN formateur f ON ea.id_formateur = f.id
                LEFT JOIN utilisateur u ON f.id_utilisateur = u.id
                WHERE ea.id = :id";

        error_log("Requête SQL : " . $sql);
        error_log("Paramètres : " . print_r([':id' => $id], true));

        $stmt = $this->db->query($sql, [':id' => $id]);
        $result = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        
        error_log("Résultat de la requête : " . print_r($result, true));
        
        if ($result && isset($result['heure'])) {
            // Convertir le datetime en format HH:mm
            $datetime = new \DateTime($result['heure']);
            $result['heure'] = $datetime->format('H:i');
            error_log("Heure formatée : " . $result['heure']);
        }
        
        return $result;
    }

    /**
     * Récupère un état d'avancement par son ID
     *
     * @param int $id ID de l'état d'avancement
     * @return array|false Données de l'état d'avancement ou false si non trouvé
     */
    public function findById(int $id): array|false
    {
        $sql = "SELECT * FROM etat_avancement WHERE id = :id";
        $stmt = $this->db->query($sql, [':id' => $id]);
        $result = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        
        if ($result && isset($result['heure'])) {
            $datetime = new \DateTime($result['heure']);
            $result['heure'] = $datetime->format('H:i');
        }
        
        return $result;
    }

    /**
     * Récupère les objectifs pédagogiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return array Liste des objectifs pédagogiques
     */
    public function getObjectifsByEtatId(int $etatId): array
    {
        error_log("Récupération des objectifs pour l'état d'avancement ID : " . $etatId);
        
        $sql = "SELECT op.id, op.objectif as libelle, eao.statut
                FROM objectif_pedagogique op
                LEFT JOIN etat_avancement_objectif eao ON op.id = eao.id_objectif_pedagogique 
                    AND eao.id_etat_avancement = :etat_id
                ORDER BY op.objectif";

        error_log("Requête SQL pour les objectifs : " . $sql);
        error_log("Paramètres : " . print_r([':etat_id' => $etatId], true));

        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        $objectifs = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        
        error_log("Objectifs récupérés : " . print_r($objectifs, true));

        // Formater les résultats pour s'assurer que chaque objectif a un statut
        foreach ($objectifs as &$objectif) {
            if (!isset($objectif['statut']) || $objectif['statut'] === null) {
                $objectif['statut'] = 'non_atteint'; // Statut par défaut
            }
        }

        return $objectifs;
    }

    /**
     * Récupère les moyens didactiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return array Liste des moyens didactiques
     */
    public function getMoyensByEtatId(int $etatId): array
    {
        error_log("Récupération des moyens pour l'état d'avancement ID : " . $etatId);
        
        $sql = "SELECT md.id, md.moyen as libelle, eam.statut
                FROM moyen_didactique md
                LEFT JOIN etat_avancement_moyen eam ON md.id = eam.id_moyen_didactique 
                    AND eam.id_etat_avancement = :etat_id
                ORDER BY md.moyen";

        error_log("Requête SQL pour les moyens : " . $sql);
        error_log("Paramètres : " . print_r([':etat_id' => $etatId], true));

        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        $moyens = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        
        error_log("Moyens récupérés : " . print_r($moyens, true));

        // Formater les résultats pour s'assurer que chaque moyen a un statut
        foreach ($moyens as &$moyen) {
            if (!isset($moyen['statut']) || $moyen['statut'] === null) {
                $moyen['statut'] = 'non_utilise'; // Statut par défaut
            }
        }

        return $moyens;
    }

    /**
     * Récupère les stratégies d'évaluation liées à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @return array Liste des stratégies d'évaluation
     */
    public function getStrategiesByEtatId(int $etatId): array
    {
        error_log("Récupération des stratégies pour l'état d'avancement ID : " . $etatId);
        
        $sql = "SELECT se.id, se.strategie as libelle, eas.statut
                FROM strategie_evaluation se
                LEFT JOIN etat_avancement_strategie eas ON se.id = eas.id_strategie_evaluation 
                    AND eas.id_etat_avancement = :etat_id
                ORDER BY se.strategie";

        error_log("Requête SQL pour les stratégies : " . $sql);
        error_log("Paramètres : " . print_r([':etat_id' => $etatId], true));

        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        $strategies = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        
        error_log("Stratégies récupérées : " . print_r($strategies, true));

        // Formater les résultats pour s'assurer que chaque stratégie a un statut
        foreach ($strategies as &$strategie) {
            if (!isset($strategie['statut']) || $strategie['statut'] === null) {
                $strategie['statut'] = 'non_appliquee'; // Statut par défaut
            }
        }

        return $strategies;
    }

    /**
     * Récupère les objectifs pédagogiques pour un état d'avancement
     */
    public function getObjectifs(int $etatId): array
    {
        $sql = "SELECT op.*, eao.statut 
                FROM objectif_pedagogique op
                LEFT JOIN etat_avancement_objectif eao ON op.id = eao.id_objectif_pedagogique 
                    AND eao.id_etat_avancement = :etat_id
                ORDER BY op.objectif";
        
        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        $objectifs = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        // Formater les résultats pour s'assurer que chaque objectif a un statut
        foreach ($objectifs as &$objectif) {
            if (!isset($objectif['statut']) || $objectif['statut'] === null) {
                $objectif['statut'] = 'non_atteint'; // Statut par défaut
            }
        }

        return $objectifs;
    }

    /**
     * Récupère les moyens didactiques pour un état d'avancement
     */
    public function getMoyens(int $etatId): array
    {
        $sql = "SELECT md.*, eam.statut 
                FROM moyen_didactique md
                LEFT JOIN etat_avancement_moyen eam ON md.id = eam.id_moyen_didactique 
                    AND eam.id_etat_avancement = :etat_id
                ORDER BY md.moyen";
        
        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Récupère les stratégies d'évaluation pour un état d'avancement
     */
    public function getStrategies(int $etatId): array
    {
        $sql = "SELECT se.*, eas.statut 
                FROM strategie_evaluation se
                LEFT JOIN etat_avancement_strategie eas ON se.id = eas.id_strategie_evaluation 
                    AND eas.id_etat_avancement = :etat_id
                ORDER BY se.strategie";
        
        $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Met à jour les objectifs pédagogiques associés à un état d'avancement
     * 
     * @param int $etatId ID de l'état d'avancement
     * @param array $objectifs Tableau des objectifs avec leur statut
     * @return bool Succès de l'opération
     */
    public function updateObjectifs(int $etatId, array $objectifs): bool
    {
        try {
            // Supprimer les anciennes associations
            $sql = "DELETE FROM etat_avancement_objectif WHERE id_etat_avancement = :etat_id";
            $this->db->query($sql, [':etat_id' => $etatId]);

            // Insérer les nouvelles associations
            if (!empty($objectifs)) {
                $sql = "INSERT INTO etat_avancement_objectif (id_etat_avancement, id_objectif_pedagogique, statut) 
                        VALUES (:etat_id, :objectif_id, :statut)";
                
                foreach ($objectifs as $objectif) {
                    // Vérifier que $objectif est un tableau
                    if (!is_array($objectif)) {
                        error_log("Format d'objectif invalide : " . print_r($objectif, true));
                        continue;
                    }

                    // Vérifier la présence des clés requises
                    if (!isset($objectif['id']) || !isset($objectif['statut'])) {
                        error_log("Objectif incomplet : " . print_r($objectif, true));
                        continue;
                    }

                    // Vérifier que l'ID est un nombre
                    if (!is_numeric($objectif['id'])) {
                        error_log("ID d'objectif invalide : " . print_r($objectif['id'], true));
                        continue;
                    }

                    // Vérifier que le statut est valide
                    $statutsValides = ['atteint', 'en_cours', 'non_atteint', 'realise'];
                    if (!in_array($objectif['statut'], $statutsValides)) {
                        error_log("Statut d'objectif invalide : " . print_r($objectif['statut'], true));
                        continue;
                    }

                    $params = [
                        ':etat_id' => $etatId,
                        ':objectif_id' => (int)$objectif['id'],
                        ':statut' => $objectif['statut']
                    ];

                    $result = $this->db->query($sql, $params);
                    if (!$result) {
                        error_log("Erreur lors de l'insertion de l'objectif : " . print_r($objectif, true));
                    }
                }
            }
            
            return true;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour des objectifs : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour les moyens didactiques associés à un état d'avancement
     * 
     * @param int $etatId ID de l'état d'avancement
     * @param array $moyens Tableau des moyens avec leur statut
     * @return bool Succès de l'opération
     */
    public function updateMoyens(int $etatId, array $moyens): bool
    {
        try {
            // Supprimer les anciennes associations
            $sql = "DELETE FROM etat_avancement_moyen WHERE id_etat_avancement = :etat_id";
            $this->db->query($sql, [':etat_id' => $etatId]);

            // Insérer les nouvelles associations
            if (!empty($moyens)) {
                $sql = "INSERT INTO etat_avancement_moyen (id_etat_avancement, id_moyen_didactique, statut) 
                        VALUES (:etat_id, :moyen_id, :statut)";
                
                foreach ($moyens as $moyen) {
                    // Vérifier que $moyen est un tableau
                    if (!is_array($moyen)) {
                        error_log("Format de moyen invalide : " . print_r($moyen, true));
                        continue;
                    }

                    // Vérifier la présence des clés requises
                    if (!isset($moyen['id']) || !isset($moyen['statut'])) {
                        error_log("Moyen incomplet : " . print_r($moyen, true));
                        continue;
                    }

                    // Vérifier que l'ID est un nombre
                    if (!is_numeric($moyen['id'])) {
                        error_log("ID de moyen invalide : " . print_r($moyen['id'], true));
                        continue;
                    }

                    // Vérifier que le statut est valide
                    $statutsValides = ['utilise', 'non_utilise'];
                    if (!in_array($moyen['statut'], $statutsValides)) {
                        error_log("Statut de moyen invalide : " . print_r($moyen['statut'], true));
                        continue;
                    }

                    $params = [
                        ':etat_id' => $etatId,
                        ':moyen_id' => (int)$moyen['id'],
                        ':statut' => $moyen['statut']
                    ];

                    $this->db->query($sql, $params);
                }
            }
            
            return true;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour des moyens : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour les stratégies d'évaluation associées à un état d'avancement
     * 
     * @param int $etatId ID de l'état d'avancement
     * @param array $strategies Tableau des stratégies avec leur statut
     * @return bool Succès de l'opération
     */
    public function updateStrategies(int $etatId, array $strategies): bool
    {
        try {
            // Supprimer les anciennes associations
            $sql = "DELETE FROM etat_avancement_strategie WHERE id_etat_avancement = :etat_id";
            $this->db->query($sql, [':etat_id' => $etatId]);

            // Insérer les nouvelles associations
            if (!empty($strategies)) {
                $sql = "INSERT INTO etat_avancement_strategie (id_etat_avancement, id_strategie_evaluation, statut) 
                        VALUES (:etat_id, :strategie_id, :statut)";
                
                foreach ($strategies as $strategie) {
                    // Vérifier que $strategie est un tableau
                    if (!is_array($strategie)) {
                        error_log("Format de stratégie invalide : " . print_r($strategie, true));
                        continue;
                    }

                    // Vérifier la présence des clés requises
                    if (!isset($strategie['id']) || !isset($strategie['statut'])) {
                        error_log("Stratégie incomplète : " . print_r($strategie, true));
                        continue;
                    }

                    // Vérifier que l'ID est un nombre
                    if (!is_numeric($strategie['id'])) {
                        error_log("ID de stratégie invalide : " . print_r($strategie['id'], true));
                        continue;
                    }

                    // Vérifier que le statut est valide
                    $statutsValides = ['appliquee', 'non_appliquee', 'utilise'];
                    if (!in_array($strategie['statut'], $statutsValides)) {
                        error_log("Statut de stratégie invalide : " . print_r($strategie['statut'], true));
                        continue;
                    }

                    $params = [
                        ':etat_id' => $etatId,
                        ':strategie_id' => (int)$strategie['id'],
                        ':statut' => $strategie['statut']
                    ];

                    $this->db->query($sql, $params);
                }
            }
            
            return true;
        } catch (\PDOException $e) {
            error_log("Erreur lors de la mise à jour des stratégies : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour un état d'avancement et ses relations
     * 
     * @param int $id ID de l'état d'avancement
     * @param array $data Données à mettre à jour
     * @param array $objectifs Tableau des objectifs à mettre à jour
     * @param array $moyens Tableau des moyens à mettre à jour
     * @param array $strategies Tableau des stratégies à mettre à jour
     * @return bool Succès de l'opération
     */
    public function update(int $id, array $data, array $objectifs = [], array $moyens = [], array $strategies = []): bool
    {
        try {
            $this->db->beginTransaction();

            // Mise à jour de l'état d'avancement
            $sql = "UPDATE etat_avancement SET 
                    id_module = :id_module,
                    id_formateur = :id_formateur,
                    date = :date,
                    heure = :heure,
                    nbr_heure = :nbr_heure,
                    nbr_heure_cumulee = :nbr_heure_cumulee,
                    taux_realisation = :taux_realisation,
                    disposition = :disposition,
                    commentaire = :commentaire,
                    difficultes = :difficultes,
                    solutions = :solutions,
                    contenu_seance = :contenu_seance,
                    updated_at = NOW()
                    WHERE id = :id";

            $params = [
                ':id' => $id,
                ':id_module' => $data['id_module'],
                ':id_formateur' => $data['id_formateur'],
                ':date' => $data['date'],
                ':heure' => $data['heure'],
                ':nbr_heure' => $data['nbr_heure'],
                ':nbr_heure_cumulee' => $data['nbr_heure_cumulee'],
                ':taux_realisation' => $data['taux_realisation'],
                ':disposition' => $data['disposition'],
                ':commentaire' => $data['commentaire'],
                ':difficultes' => $data['difficultes'],
                ':solutions' => $data['solutions'],
                ':contenu_seance' => $data['contenu_seance']
            ];

            $result = $this->db->query($sql, $params);
            if (!$result) {
                throw new \PDOException("Erreur lors de la mise à jour de l'état d'avancement");
            }

            // Suppression des anciennes relations
            $this->db->query("DELETE FROM etat_avancement_objectif WHERE id_etat_avancement = :id", [':id' => $id]);
            $this->db->query("DELETE FROM etat_avancement_moyen WHERE id_etat_avancement = :id", [':id' => $id]);
            $this->db->query("DELETE FROM etat_avancement_strategie WHERE id_etat_avancement = :id", [':id' => $id]);

            // Insertion des nouvelles relations pour les objectifs
            if (!empty($objectifs)) {
                $sql = "INSERT INTO etat_avancement_objectif (id_etat_avancement, id_objectif_pedagogique, statut) VALUES (:id_etat, :id_objectif, :statut)";
                foreach ($objectifs as $objectif) {
                    $result = $this->db->query($sql, [
                        ':id_etat' => $id,
                        ':id_objectif' => $objectif['id'],
                        ':statut' => $objectif['statut']
                    ]);
                    if (!$result) {
                        throw new \PDOException("Erreur lors de l'insertion des objectifs");
                    }
                }
            }

            // Insertion des nouvelles relations pour les moyens
            if (!empty($moyens)) {
                $sql = "INSERT INTO etat_avancement_moyen (id_etat_avancement, id_moyen_didactique, statut) VALUES (:id_etat, :id_moyen, :statut)";
                foreach ($moyens as $moyen) {
                    $result = $this->db->query($sql, [
                        ':id_etat' => $id,
                        ':id_moyen' => $moyen['id'],
                        ':statut' => $moyen['statut']
                    ]);
                    if (!$result) {
                        throw new \PDOException("Erreur lors de l'insertion des moyens");
                    }
                }
            }

            // Insertion des nouvelles relations pour les stratégies
            if (!empty($strategies)) {
                $sql = "INSERT INTO etat_avancement_strategie (id_etat_avancement, id_strategie_evaluation, statut) VALUES (:id_etat, :id_strategie, :statut)";
                foreach ($strategies as $strategie) {
                    $result = $this->db->query($sql, [
                        ':id_etat' => $id,
                        ':id_strategie' => $strategie['id'],
                        ':statut' => $strategie['statut']
                    ]);
                    if (!$result) {
                        throw new \PDOException("Erreur lors de l'insertion des stratégies");
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Erreur lors de la mise à jour : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crée un nouvel état d'avancement
     */
    public function create(array $data): int|false
    {
        try {
            $this->db->beginTransaction();

            // Insertion de l'état d'avancement
            $sql = "INSERT INTO etat_avancement (
                id_module, id_formateur, date, heure, nbr_heure, 
                nbr_heure_cumulee, taux_realisation, disposition, 
                commentaire, difficultes, solutions, contenu_seance
            ) VALUES (
                :id_module, :id_formateur, :date, :heure, :nbr_heure,
                :nbr_heure_cumulee, :taux_realisation, :disposition,
                :commentaire, :difficultes, :solutions, :contenu_seance
            )";

            $params = [
                'id_module' => $data['id_module'],
                'id_formateur' => $data['id_formateur'],
                'date' => $data['date'],
                'heure' => $data['heure'],
                'nbr_heure' => $data['nbr_heure'],
                'nbr_heure_cumulee' => $data['nbr_heure_cumulee'],
                'taux_realisation' => $data['taux_realisation'],
                'disposition' => $data['disposition'],
                'commentaire' => $data['commentaire'],
                'difficultes' => $data['difficultes'],
                'solutions' => $data['solutions'],
                'contenu_seance' => $data['contenu_seance']
            ];

            $this->db->query($sql, $params);
            $etatId = (int)$this->db->lastInsertId();

            // Insertion des objectifs
            if (!empty($data['objectifs'])) {
                $this->insertObjectifs($etatId, $data['objectifs']);
            }

            // Insertion des moyens
            if (!empty($data['moyens'])) {
                $this->insertMoyens($etatId, $data['moyens']);
            }

            // Insertion des stratégies
            if (!empty($data['strategies'])) {
                $this->insertStrategies($etatId, $data['strategies']);
            }

            $this->db->commit();
            return $etatId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Erreur lors de la création de l'état d'avancement : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Supprime un état d'avancement et toutes ses relations
     *
     * @param int $id ID de l'état d'avancement
     * @return bool Succès de la suppression
     * @throws \PDOException En cas d'erreur de base de données
     */
    public function delete(int $id): bool
    {
        try {
            $this->db->beginTransaction();

            // Suppression des relations explicitement
            $this->deleteRelations($id);

            // Suppression de l'état d'avancement principal
            $sql = "DELETE FROM etat_avancement WHERE id = :id";
            $stmt = $this->db->query($sql, [':id' => $id]);
            if (!$stmt) {
                error_log("Erreur lors de la suppression de l'état d'avancement principal (ID: {$id})");
                throw new PDOException("Erreur lors de la suppression de l'état d'avancement principal.");
            }

            $this->db->commit();
            return true;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Erreur lors de la suppression de l'état d'avancement (ID: {$id}): " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Supprime toutes les relations d'un état d'avancement dans les tables de liaison
     */
    private function deleteRelations(int $etatId): void
    {
        try {
            // Suppression des relations dans les tables de liaison
            $tables = [
                'etat_avancement_objectif',
                'etat_avancement_moyen',
                'etat_avancement_strategie'
            ];

            foreach ($tables as $table) {
                $sql = "DELETE FROM {$table} WHERE id_etat_avancement = :etat_id";
                $stmt = $this->db->query($sql, [':etat_id' => $etatId]);
                if (!$stmt) {
                    throw new PDOException("Erreur lors de la suppression des relations dans {$table}");
                }
            }
        } catch (\Exception $e) {
            error_log("Erreur lors de la suppression des relations : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Insère les objectifs pédagogiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @param array $objectifs Tableau d'objectifs (chaque élément est un array avec 'id' et 'statut')
     * @return void
     * @throws \InvalidArgumentException Si le format des données est incorrect
     * @throws \PDOException En cas d'erreur de base de données
     */
    private function insertObjectifs(int $etatId, array $objectifs): void
    {
        $sql = "INSERT INTO etat_avancement_objectif (id_etat_avancement, id_objectif_pedagogique, statut)
                VALUES (:etat_id, :objectif_id, :statut)";

        try {
            foreach ($objectifs as $index => $objectif) {
                // Validation du format
                if (!is_array($objectif)) {
                    error_log("Objectif invalide à l'index {$index}: " . print_r($objectif, true));
                    throw new \InvalidArgumentException("Format d'objectif invalide à l'index {$index}. Attendu un tableau.");
                }

                // Validation des clés requises
                if (!isset($objectif['id']) || !isset($objectif['statut'])) {
                    error_log("Objectif incomplet à l'index {$index}: " . print_r($objectif, true));
                    throw new \InvalidArgumentException("Format d'objectif incomplet à l'index {$index}. Attendu ['id' => int, 'statut' => string].");
                }

                // Validation du type de l'ID
                if (!is_numeric($objectif['id'])) {
                    error_log("ID d'objectif invalide à l'index {$index}: " . print_r($objectif['id'], true));
                    throw new \InvalidArgumentException("ID d'objectif invalide à l'index {$index}. Attendu un nombre.");
                }

                // Validation du statut
                $statutsValides = ['atteint', 'en_cours', 'non_atteint', 'realise'];
                if (!in_array($objectif['statut'], $statutsValides)) {
                    error_log("Statut d'objectif invalide à l'index {$index}: " . print_r($objectif['statut'], true));
                    throw new \InvalidArgumentException("Statut d'objectif invalide à l'index {$index}. Valeurs acceptées : " . implode(', ', $statutsValides));
                }

                $params = [
                    ':etat_id' => $etatId,
                    ':objectif_id' => (int)$objectif['id'],
                    ':statut' => $objectif['statut']
                ];

                $stmt = $this->db->query($sql, $params);
                if (!$stmt) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Erreur PDO lors de l'insertion d'un objectif lié (Etat ID: {$etatId}, Objectif ID: {$objectif['id']}): " . ($errorInfo[2] ?? 'Inconnu'));
                    throw new PDOException("Erreur lors de l'insertion des objectifs liés.");
                }
            }
        } catch (\Exception $e) {
            error_log("Erreur détaillée lors de l'insertion des objectifs : " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Insère les moyens didactiques liés à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @param array $moyens Tableau de moyens (chaque élément est un array avec 'id' et 'statut')
     * @return void
     * @throws \InvalidArgumentException Si le format des données est incorrect
     * @throws \PDOException En cas d'erreur de base de données
     */
    private function insertMoyens(int $etatId, array $moyens): void
    {
        $sql = "INSERT INTO etat_avancement_moyen (id_etat_avancement, id_moyen_didactique, statut)
                VALUES (:etat_id, :moyen_id, :statut)";

        try {
            foreach ($moyens as $moyen) {
                if (!isset($moyen['id']) || !isset($moyen['statut'])) {
                    throw new \InvalidArgumentException("Format de moyen lié invalide. Attendu ['id' => int, 'statut' => string].");
                }
                if (!in_array($moyen['statut'], ['utilise', 'non_utilise'])) {
                    throw new \InvalidArgumentException("Statut de moyen lié invalide: " . $moyen['statut']);
                }

                $params = [
                    ':etat_id' => $etatId,
                    ':moyen_id' => (int)$moyen['id'],
                    ':statut' => $moyen['statut']
                ];

                $stmt = $this->db->query($sql, $params);
                if (!$stmt) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Erreur PDO lors de l'insertion d'un moyen lié (Etat ID: {$etatId}, Moyen ID: {$moyen['id']}): " . ($errorInfo[2] ?? 'Inconnu'));
                    throw new PDOException("Erreur lors de l'insertion des moyens liés.");
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Insère les stratégies d'évaluation liées à un état d'avancement
     *
     * @param int $etatId ID de l'état d'avancement
     * @param array $strategies Tableau de stratégies (chaque élément est un array avec 'id' et 'statut')
     * @return void
     * @throws \InvalidArgumentException Si le format des données est incorrect
     * @throws \PDOException En cas d'erreur de base de données
     */
    private function insertStrategies(int $etatId, array $strategies): void
    {
        $sql = "INSERT INTO etat_avancement_strategie (id_etat_avancement, id_strategie_evaluation, statut)
                VALUES (:etat_id, :strategie_id, :statut)";

        try {
            foreach ($strategies as $strategie) {
                if (!isset($strategie['id']) || !isset($strategie['statut'])) {
                    throw new \InvalidArgumentException("Format de stratégie lié invalide. Attendu ['id' => int, 'statut' => string].");
                }
                if (!in_array($strategie['statut'], ['appliquee', 'non_appliquee', 'utilise'])) {
                    throw new \InvalidArgumentException("Statut de stratégie lié invalide: " . $strategie['statut']);
                }

                $params = [
                    ':etat_id' => $etatId,
                    ':strategie_id' => (int)$strategie['id'],
                    ':statut' => $strategie['statut']
                ];

                $stmt = $this->db->query($sql, $params);
                if (!$stmt) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Erreur PDO lors de l'insertion d'une stratégie liée (Etat ID: {$etatId}, Stratégie ID: {$strategie['id']}): " . ($errorInfo[2] ?? 'Inconnu'));
                    throw new PDOException("Erreur lors de l'insertion des stratégies liées.");
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
}