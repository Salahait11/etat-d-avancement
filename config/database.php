<?php // config/database.php
declare(strict_types=1);

// Vérification de l'extension PDO
if (!extension_loaded('pdo_mysql')) {
    die('Erreur : L\'extension PDO MySQL n\'est pas installée.');
}

// Configuration de la base de données
$config = [
    'driver'   => 'mysql',
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'database' => 'gestion_ecoles',   // <<< TON NOM DE DB
    'username' => 'root',             // <<< TON USER MYSQL
    'password' => '',                 // <<< TON MOT DE PASSE MYSQL
    'charset'  => 'utf8mb4',
    'collation'=> 'utf8mb4_unicode_ci',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ],
];

// Test de connexion
try {
    $dsn = sprintf(
        "%s:host=%s;port=%s;dbname=%s;charset=%s",
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['database'],
        $config['charset']
    );
    
    $pdo = new PDO(
        $dsn,
        $config['username'],
        $config['password'],
        $config['options']
    );
    
    // Si on arrive ici, la connexion est réussie
    error_log("Connexion à la base de données réussie");
} catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    die('Erreur de connexion à la base de données. Vérifiez la configuration.');
}

return $config;