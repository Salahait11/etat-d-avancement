<?php // src/View/dashboard/index.php

// Définit le titre pour la balise <title> dans le layout.
// La variable $title est utilisée par `layout.php`.
$title = "Tableau de Bord";

// Les variables $isLoggedIn et $currentUser sont automatiquement disponibles ici
// car elles sont injectées par BaseController::render et extraites.
// Pas besoin de les redéfinir ou d'appeler $this->... ici.
?>

<h2>Tableau de Bord</h2>

<!-- **** CORRECTION ICI **** -->
<!-- Utilise directement la variable $currentUser injectée -->
<?php if (isset($currentUser) && $currentUser): ?>
    <p>Bienvenue sur votre tableau de bord, <?php echo htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']); ?> !</p>
    <p>Votre email enregistré : <?php echo htmlspecialchars($currentUser['email']); ?></p>
<?php else: ?>
    <p>Bienvenue sur le tableau de bord.</p>
    <!-- Ce cas ne devrait normalement pas arriver si la route est bien protégée par requireLogin() -->
<?php endif; ?>

<p>C'est ici que vous trouverez les résumés et les accès rapides aux différentes sections.</p>
<ul>
    <li><a href="/filieres">Gérer les Filières</a></li>
    <li><a href="/modules">Gérer les Modules</a></li>
    <li><a href="/etat-avancement">Consulter/Ajouter Suivi Pédagogique</a></li>
    <!-- Ajouter d'autres liens utiles -->
</ul>

<p style="margin-top: 20px;"><a href="/logout">Se déconnecter</a></p>