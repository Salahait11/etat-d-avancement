<?php // config/database.php

declare(strict_types=1);

$config = [
    'db_host'    => '127.0.0.1',        // Ou 'localhost'
    'db_port'    => '3306',
    'db_name'    => 'gestion_ecoles',   // <<< TON NOM DE BASE DE DONNÉES
    'db_user'    => 'root',             // <<< TON UTILISATEUR MYSQL
    'db_pass'    => '',                 // <<< TON MOT DE PASSE MYSQL (souvent vide en local)
    'db_charset' => 'utf8mb4'
];

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset={$config['db_charset']}";

try {
     $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
} catch (\PDOException $e) {
     // Gestion d'erreur basique pour le démarrage
     // En production, logguer l'erreur et afficher un message générique.
     error_log("ERREUR PDO : " . $e->getMessage());
     die("Erreur de connexion à la base de données. Vérifiez config/database.php et l'état du serveur MySQL. Message: " . $e->getMessage());
}
?>