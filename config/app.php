<?php // config/app.php
declare(strict_types=1);

// --- Configuration Cruciale de l'URL ---
// Détermine l'URL complète de la racine de ton application (le dossier public/).
// Adapte ceci EXACTEMENT à la façon dont tu accèdes à ton projet dans le navigateur.

// Option 1: Si tu accèdes via http://localhost/gestion_ecoles_v2/public/
// $baseUrl = 'http://localhost/gestion_ecoles_v2/public';

// Option 2: Si tu as configuré un Virtual Host (ex: http://gecoles.test/) qui pointe vers le dossier /public
$baseUrl = 'http://localhost/etat-d-avancement/public'; // URL complète incluant /public

// Option 3: Essai de détection automatique (peut être moins fiable selon les serveurs)
/*
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptFolder = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim(str_replace('\\', '/', $scriptFolder), '/');
if ($basePath === '.' || $basePath === '\\') { $basePath = ''; }
$baseUrl = $scheme . '://' . $host . $basePath;
*/
// -------- Fin Configuration URL --------


// --- Autres Configurations ---
$config = [
    'env' => 'development', // 'development' ou 'production'
    'base_url' => rtrim($baseUrl, '/'), // Assure qu'il n'y a pas de / à la fin

    // Ajouter d'autres paramètres ici si besoin (clé API, etc.)
];

// Définir les constantes globales
define('APP_ENV', $config['env']);
define('BASE_URL', $config['base_url']); // Ex: http://localhost/gestion_ecoles_v2/public

return $config; // Retourne aussi le tableau pour une utilisation éventuelle