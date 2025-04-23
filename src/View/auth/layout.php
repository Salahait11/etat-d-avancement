<?php // src/View/layout.php ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Utilise la variable $title passée par le contrôleur via render() -->
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Gestion Écoles'; ?></title>
    <!-- Assure-toi que ce chemin est correct par rapport à ta racine web (public/) -->
    <link rel="stylesheet" href="/css/style.css">
    <!-- Autres balises meta, liens CSS ou JS pour le <head> -->
</head>
<body>
    <header>
        <h1><a href="/">Gestion Pédagogique</a></h1>
        <nav>
            <ul>
                <!-- **** CORRECTION ICI **** -->
                <!-- Utilise la variable $isLoggedIn qui est injectée par BaseController::render -->
                <?php if (isset($isLoggedIn) && $isLoggedIn === true): ?>
                    <li><a href="/dashboard">Tableau de Bord</a></li>
                    <li><a href="/filieres">Filières</a></li>
                    <li><a href="/modules">Modules</a></li>
                    <li><a href="/utilisateurs">Utilisateurs</a></li>
                    <li><a href="/etat-avancement">Suivi Avancement</a></li>
                    <!-- **** CORRECTION ICI **** -->
                    <!-- Utilise la variable $currentUser qui est injectée -->
                    <li>
                        Connecté:
                        <?php echo isset($currentUser['prenom']) ? htmlspecialchars($currentUser['prenom']) : 'Utilisateur'; ?>
                        (<?php echo isset($currentUser['email']) ? htmlspecialchars($currentUser['email']) : ''; ?>)
                    </li>
                    <li><a href="/logout">Déconnexion</a></li>
                <?php else: ?>
                    <li><a href="/login">Connexion</a></li>
                    <!-- Peut-être un lien d'inscription plus tard ? -->
                    <!-- <li><a href="/register">Inscription</a></li> -->
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <!-- Affichage des Messages Flash -->
        <?php
            // Accès direct à la session pour les messages flash (comme défini dans BaseController)
            // Une méthode helper dans BaseController serait plus propre, mais ceci fonctionne.
            if (isset($_SESSION['_flash']) && !empty($_SESSION['_flash'])):
        ?>
            <div class="flash-messages">
            <?php foreach ($_SESSION['_flash'] as $key => $message): ?>
                <div class="flash-message flash-<?php echo htmlspecialchars($key); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['_flash']); // Nettoyer les messages après affichage ?>
        <?php endif; ?>

        <!-- Injection du contenu spécifique de la vue ($content) -->
        <?php echo $content ?? ''; // La variable $content est définie dans BaseController::render ?>
    </main>

    <footer>
        <p>© <?php echo date('Y'); ?> Gestion Écoles. Tous droits réservés.</p>
    </footer>

    <!-- Lien vers le JS global (si nécessaire) -->
    <!-- Assure-toi que ce chemin est correct par rapport à ta racine web (public/) -->
    <script src="/js/script.js"></script>
    <!-- Autres scripts JS spécifiques à la page pourraient être ajoutés ici via une section dans le layout -->
</body>
</html>