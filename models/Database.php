<?php
/**
 * Classe Database
 * Implémente le pattern Singleton pour gérer la connexion à la base de données
 */
class Database {
    private static $instance = null;
    private $connection = null;
    
    // Paramètres de connexion à la base de données
    private $host = 'localhost';
    private $dbname = 'gestion_ecoles';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    /**
     * Constructeur privé pour empêcher l'instanciation directe
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Log l'erreur
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new Exception("Impossible de se connecter à la base de données.");
        }
    }
    
    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Empêche la désérialisation de l'instance
     */
    private function __wakeup() {}
    
    /**
     * Récupère l'instance unique de la classe Database
     * 
     * @return Database Instance unique de la classe Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Récupère la connexion PDO
     * 
     * @return PDO Connexion PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Vérifie si la connexion est active
     * 
     * @return bool True si la connexion est active, false sinon
     */
    public function isConnected() {
        return $this->connection !== null;
    }
    
    /**
     * Ferme la connexion à la base de données
     */
    public function closeConnection() {
        $this->connection = null;
    }
    
    /**
     * Définit les paramètres de connexion
     * 
     * @param array $config Paramètres de connexion
     */
    public function setConfig($config) {
        if (isset($config['host'])) $this->host = $config['host'];
        if (isset($config['dbname'])) $this->dbname = $config['dbname'];
        if (isset($config['username'])) $this->username = $config['username'];
        if (isset($config['password'])) $this->password = $config['password'];
        if (isset($config['charset'])) $this->charset = $config['charset'];
    }
} 