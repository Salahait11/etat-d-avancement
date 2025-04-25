<?php
// Vérifier si les variables nécessaires sont définies
$title = $title ?? 'Gestion Écoles v2';
$baseUrl = $baseUrl ?? BASE_URL ?? '';
$isLoggedIn = $isLoggedIn ?? false;
$currentUser = $currentUser ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Utilisation de BASE_URL pour le CSS -->
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Styles basiques (à déplacer dans style.css) -->
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; line-height: 1.6; }
        .container { max-width: 1100px; margin: 20px auto; padding: 0 15px; }
        header { background-color: #343a40; color: #fff; padding: 1rem 0; margin-bottom: 20px; }
        header .container { display: flex; justify-content: space-between; align-items: center; }
        header h1 { margin: 0; font-size: 1.8em; }
        header a { color: #fff; text-decoration: none; }
        header nav ul { list-style: none; padding: 0; margin: 0; }
        header nav li { display: inline-block; margin-left: 20px; }
        header nav a { color: #f8f9fa; } header nav a:hover { color: #fff; }
        main { min-height: 60vh; }
        footer { background-color: #f8f9fa; text-align: center; padding: 15px 0; margin-top: 30px; border-top: 1px solid #e7e7e7; font-size: 0.9em; color: #6c757d; }
        .flash-messages { margin-bottom: 15px; padding: 0; list-style: none; }
        .flash-message { padding: 12px 18px; margin-bottom: 12px; border-radius: 5px; border: 1px solid transparent; }
        .flash-success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
        .flash-error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .flash-warning { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
        .flash-info { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
        .button { display: inline-block; margin-top: 5px; margin-right: 5px; text-decoration: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-size: 0.95em; border: none; color: white; background-color: #0d6efd; }
        button.button { font-family: inherit; }
        .button.delete-button { background-color: #dc3545; }
        .button.secondary-button { background-color: #6c757d; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="<?php echo htmlspecialchars($baseUrl); ?>">Gestion Pédagogique</a></h1>
            <nav>
                <ul>
                    <li><a href="<?php echo htmlspecialchars($baseUrl); ?>">Accueil</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li><a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard">Tableau de Bord</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl); ?>/filieres">Filières</a></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl); ?>/modules">Modules</a></li>
                        <li><span><?php echo htmlspecialchars($currentUser['prenom'] ?? 'Utilisateur'); ?></span></li>
                        <li><a href="<?php echo htmlspecialchars($baseUrl); ?>/logout" class="button delete-button" style="padding: 5px 10px; font-size:0.9em;">Déco</a></li>
                    <?php else: ?>
                        <li><a href="<?php echo htmlspecialchars($baseUrl); ?>/login">Connexion</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <main>
            <!-- Affichage des Messages Flash -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?>">
                    <?= $_SESSION['flash_message']['message'] ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['_flash']) && !empty($_SESSION['_flash'])): ?>
                <div class="flash-messages">
                    <?php foreach ($_SESSION['_flash'] as $key => $message): ?>
                        <div class="flash-message flash-<?php echo htmlspecialchars($key); ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['_flash']); // Très important ?>
                </div>
            <?php endif; ?>
