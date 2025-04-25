<?php // src/Core/Database.php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement; // Importe la classe PDOStatement

/**
 * Classe Singleton pour gérer la connexion à la base de données via PDO.
 * Fournit des méthodes pour exécuter des requêtes préparées.
 */
class Database
{
    private static ?Database $instance = null; // Instance unique (Singleton)
    private PDO $pdo; // L'objet PDO

    /**
     * Constructeur privé pour empêcher l'instanciation directe (Singleton).
     * Charge la configuration et établit la connexion.
     *
     * @throws \RuntimeException Si la connexion échoue.
     */
    private function __construct()
    {
        // Charger la configuration depuis le fichier
        $dbConfig = require_once __DIR__ . '/../../config/database.php'; // Chemin relatif depuis Core/ vers config/

        try {
            $dsn = "{$dbConfig['driver']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
            $this->pdo = new PDO(
                $dsn,
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['options'] // Utilise les options définies dans le fichier de config
            );
        } catch (PDOException $e) {
            // Log l'erreur et relance une exception plus générique
            error_log("ERREUR PDO Construction : " . $e->getMessage());
            throw new \RuntimeException("Erreur de connexion à la base de données.", 503, $e);
        }
    }

    /**
     * Méthode statique pour obtenir l'instance unique de la classe Database (Singleton).
     * Crée l'instance si elle n'existe pas encore.
     *
     * @return Database L'instance unique.
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self(); // Appelle le constructeur privé
        }
        return self::$instance;
    }

    /**
     * Méthode pratique pour exécuter une requête préparée (SELECT, INSERT, UPDATE, DELETE).
     *
     * @param string $sql La requête SQL avec des placeholders (ex: :email, ?).
     * @param array $params Un tableau associatif ou indexé des paramètres à lier.
     * @return PDOStatement|false L'objet PDOStatement en cas de succès, false en cas d'échec de préparation/exécution.
     */
    public function query(string $sql, array $params = []): PDOStatement|false
    {
        try {
            $stmt = $this->pdo->prepare($sql);
            // Exécute la requête en passant les paramètres directement à execute()
            // PDO gère le liage des valeurs et leurs types (la plupart du temps).
            $success = $stmt->execute($params);

            if ($success) {
                return $stmt; // Retourne l'objet PDOStatement pour pouvoir récupérer les résultats (fetch, fetchAll...)
            } else {
                // L'exécution a échoué mais prepare() a réussi (rare avec PDO::ERRMODE_EXCEPTION)
                error_log("Erreur lors de l'exécution de la requête: " . $sql . " avec params: " . print_r($params, true));
                return false;
            }
        } catch (PDOException $e) {
            // Erreur lors de prepare() ou execute()
            error_log("Erreur PDO query : {$e->getMessage()} | SQL : {$sql} | Params : " . print_r($params, true));
            // Ne pas retourner false ici car on veut peut-être laisser l'erreur remonter
            // ou la gérer différemment selon le contexte. Pour l'instant, on la loggue.
             // Si on veut absolument retourner false :
             return false;
             // Si on veut laisser l'exception être attrapée plus haut (ex: dans le modèle ou contrôleur) :
             // throw $e;
        }
         // Si on arrive ici (ne devrait pas avec ERRMODE_EXCEPTION sauf si on retourne false dans le catch), retourne false.
         // return false;
    }


    /**
     * Récupère l'objet PDO brut si nécessaire (à utiliser avec précaution).
     *
     * @return PDO L'instance PDO.
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    
    /**
     * Récupère l'ID de la dernière ligne insérée.
     *
     * @return string L'ID de la dernière insertion.
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * Empêche le clonage de l'instance (Singleton).
     */
    private function __clone() {}

    /**
     * Empêche la désérialisation de l'instance (Singleton).
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}