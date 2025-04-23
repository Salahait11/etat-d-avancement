<?php // config/database.php

// Activer le typage strict (recommandé)
declare(strict_types=1);

// --- Configuration de la Base de Données ---
// Stocke les paramètres dans un tableau pour une meilleure organisation.
$config = [
    'db_host'    => '127.0.0.1',        // Hôte de la base de données. 'localhost' ou '127.0.0.1' fonctionne souvent.
                                       // Si MySQL tourne dans Docker, cela pourrait être le nom du service (ex: 'mysql_db').
    'db_port'    => '3306',             // Port standard de MySQL/MariaDB. Changez si vous utilisez un port différent.
    'db_name'    => 'gestion_ecoles',   // !!! IMPORTANT: Remplacez par le nom EXACT de votre base de données !!!
    'db_user'    => 'root',             // !!! IMPORTANT: Remplacez par votre nom d'utilisateur MySQL/MariaDB !!!
                                       // (souvent 'root' en local)
    'db_pass'    => '',                 // !!! IMPORTANT: Remplacez par votre mot de passe MySQL/MariaDB !!!
                                       // (souvent vide en local pour WAMP/MAMP/XAMPP par défaut, mais PAS recommandé)
    'db_charset' => 'utf8mb4'          // Jeu de caractères recommandé pour supporter les emojis et caractères spéciaux.
];

// --- Options PDO pour la connexion ---
// Ces options configurent le comportement de PDO.
$options = [
    // Mode de rapport d'erreurs : Lève des exceptions PDOException en cas d'erreur SQL.
    // C'est essentiel pour attraper les erreurs avec try/catch.
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,

    // Mode de récupération par défaut : Récupère les lignes sous forme de tableaux associatifs (nom_colonne => valeur).
    // Plus pratique que les tableaux indexés numériquement par défaut (PDO::FETCH_NUM) ou les deux (PDO::FETCH_BOTH).
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,

    // Désactive l'émulation des requêtes préparées par PDO.
    // Force l'utilisation des requêtes préparées natives du SGBD (si supporté), ce qui est généralement plus sûr.
    PDO::ATTR_EMULATE_PREPARES   => false,

    // Optionnel: Spécifier le jeu de caractères dans les options aussi (en plus du DSN) peut aider dans certains cas.
    // PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $config['db_charset'] // Alternative au charset dans le DSN
];

// --- Création du DSN (Data Source Name) ---
// Chaîne de caractères formatée spécifiquement pour PDO indiquant comment se connecter.
$dsn = "mysql:host={$config['db_host']};port={$config['db_port']};dbname={$config['db_name']};charset={$config['db_charset']}";

// --- Tentative de Connexion ---
// Utilise un bloc try/catch pour gérer les erreurs de connexion potentielles.
try {
    // Crée une nouvelle instance de l'objet PDO. C'est notre connexion à la base de données.
    // L'objet $pdo sera disponible dans le scope où ce fichier est inclus (ici, public/index.php).
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], $options);

    // Si on arrive ici sans exception, la connexion est établie avec succès !
    // Il n'est généralement pas nécessaire d'afficher un message ici, sauf pour un test rapide.
    // echo "Connexion à la base de données '{$config['db_name']}' réussie !"; // Décommentez pour tester si besoin

} catch (\PDOException $e) {
    // Une exception PDOException a été levée, ce qui signifie que la connexion a échoué.

    // 1. Loguer l'erreur détaillée côté serveur (important pour le débogage, NE PAS l'afficher à l'utilisateur final).
    // Assurez-vous que le serveur web a les permissions d'écrire dans le fichier/répertoire de log.
    error_log("ERREUR DE CONNEXION PDO : " . $e->getMessage());

    // 2. Arrêter l'exécution du script et afficher un message d'erreur.
    //    Le message affiché dépend de l'environnement (développement vs production).

    // Récupère l'environnement défini dans public/index.php (ou définir une valeur par défaut ici)
    $appEnv = defined('APP_ENV') ? APP_ENV : 'production'; // Par défaut, on suppose la production

    if ($appEnv === 'development') {
        // En DÉVELOPPEMENT: Afficher des détails techniques peut être utile.
        // ATTENTION : Contient potentiellement des informations sensibles (nom d'utilisateur, nom de la base...).
        die("<h1>Erreur de Connexion à la Base de Données (Dev)</h1>"
            . "<p>Impossible de se connecter à la base de données '{$config['db_name']}'. Vérifiez config/database.php et l'état de votre serveur MySQL.</p>"
            . "<p><strong>Erreur PDO :</strong> " . htmlspecialchars($e->getMessage()) . "</p>"
            . "<p><strong>DSN utilisé :</strong> " . htmlspecialchars($dsn) . "</p>"
            . "<p><strong>Utilisateur :</strong> " . htmlspecialchars($config['db_user']) . "</p>"
            // Ne jamais afficher le mot de passe, même en dev !
        );
    } else {
        // En PRODUCTION: Afficher un message générique et non technique à l'utilisateur.
        die("<h1>Erreur Technique</h1>"
            . "<p>Une erreur est survenue lors de la connexion au service de données. Notre équipe technique a été informée. Veuillez réessayer plus tard.</p>");
        // Dans un vrai scénario de production, vous auriez un système de monitoring
        // qui serait alerté par l'erreur logguée via error_log().
    }
}

// Si tout s'est bien passé, la variable $pdo contient maintenant l'objet de connexion PDO
// et est prête à être utilisée par les modèles ou les contrôleurs qui en auront besoin.
// Ce script ne retourne rien explicitement, il rend juste $pdo disponible.
?>