<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Gestion Écoles'; ?></title>
    <link rel="stylesheet" href="/css/style.css"> <!-- Ajuster le chemin si nécessaire -->
     <style> /* Styles basiques pour commencer (à mettre dans style.css) */
        body { font-family: sans-serif; margin: 0; padding: 0; }
        header { background-color: #333; color: white; padding: 10px 20px; }
        header h1 { margin: 0; font-size: 1.5em; }
        header a { color: white; text-decoration: none; }
        header nav ul { list-style: none; padding: 0; margin: 0; text-align: right; }
        header nav li { display: inline-block; margin-left: 15px; }
        main { padding: 20px; }
        footer { background-color: #f2f2f2; text-align: center; padding: 10px; margin-top: 20px; border-top: 1px solid #ccc; }
        .flash-messages { margin-bottom: 15px; padding: 0; list-style: none; }
        .flash-message { padding: 10px 15px; margin-bottom: 10px; border-radius: 4px; }
        .flash-success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .flash-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .flash-info { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .form-group { margin-bottom: 1em; }
        .form-group label { display: block; margin-bottom: .3em; font-weight: bold; }
        .form-group input[type=text], .form-group input[type=email], .form-group input[type=password], .form-group input[type=number], .form-group textarea {
            width: 90%; max-width: 400px; padding: 8px; border: 1px solid #ccc; border-radius: 3px;
        }
         .button { display: inline-block; margin-top: 10px; margin-right: 10px; text-decoration: none; padding: 10px 15px; border-radius: 3px; cursor: pointer; font-size: 1em; border: none; color: white; background-color: #007bff; }
         button.button { font-family: inherit; }
         .button.delete-button { background-color: #dc3545; }
    </style>
</head>
<body>
    <header>
        <h1><a href="/">Gestion Pédagogique</a></h1>
        <nav>
            <ul>
                <?php if (isset($isLoggedIn) && $isLoggedIn === true): ?>
                    <li><a href="/dashboard">Tableau de Bord</a></li>
                    <li><a href="/filieres">Filières</a></li>
                    <!-- Ajouter d'autres liens si connecté -->
                    <li>Connecté: <?php echo isset($currentUser['prenom']) ? htmlspecialchars($currentUser['prenom']) : 'Utilisateur'; ?></li>
                    <li><a href="/logout" class="button delete-button" style="padding: 5px 10px; font-size:0.9em;">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="/login">Connexion</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Affichage des Messages Flash -->
        <?php if (isset($_SESSION['_flash']) && !empty($_SESSION['_flash'])): ?>
        <div class="flash-messages">
            <?php foreach ($_SESSION['_flash'] as $key => $message): ?>
                <div class="flash-message flash-<?php echo htmlspecialchars($key); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endforeach; ?>
            <?php unset($_SESSION['_flash']); ?>
        </div>
        <?php endif; ?>

        <!-- Contenu spécifique de la vue -->
        <?php echo $content ?? ''; ?>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> Gestion Écoles.</p>
    </footer>

    <script src="/js/script.js"></script> <!-- Ajuster le chemin si nécessaire -->
</body>
</html>