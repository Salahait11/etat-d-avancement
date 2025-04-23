<?php // config/database.php

// --- Configuration de la Base de Données ---
$config = [
    'db_host'    => '127.0.0.1',        // Souvent localhost ou 127.0.0.1
    'db_port'    => '3306',             // Port MySQL par défaut
    'db_name'    => 'gestion_ecoles',   // Le nom de ta base de données
    'db_user'    => 'root',             // Ton utilisateur MySQL (souvent 'root' en local)
    'db_pass'    => '',                 // Ton mot de passe MySQL (souvent vide en local)
    'db_charset' => 'utf8mb4'
];

// --- Options PDO ---
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lève des exceptions en cas d'erreur SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Récupère les résultats en tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Utilise les vraies requêtes préparées
];

// --- Création de l'instance PDO ---
$dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset={$config['db_charset']}";

try {
     $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);
     // Si tu arrives ici, la connexion est réussie !
     // echo "Connexion à la base de données réussie !"; // Décommente pour tester rapidement

} catch (\PDOException $e) {
     // En cas d'échec de la connexion
     // En développement, afficher l'erreur est utile :
     error_log("ERREUR PDO : " . $e->getMessage()); // Log l'erreur
     die("Erreur de connexion à la base de données. Vérifiez les logs ou la configuration. Détails : " . $e->getMessage()); // Affiche un message et arrête le script

     // En production, il vaut mieux logger l'erreur et afficher un message générique à l'utilisateur :
     // error_log("Erreur de connexion DB : " . $e->getMessage());
     // die("Une erreur technique est survenue. Veuillez réessayer plus tard.");
}

// On ne retourne rien ici, le script qui inclura ce fichier aura accès à la variable $pdo
?>