<?php // src/View/home/index.php
// Les variables $title, $welcomeMessage, $baseUrl, $isLoggedIn, $currentUser
// sont injectées par BaseController::render() et extract()
?>

<h1><?php echo htmlspecialchars($title ?? 'Accueil'); ?></h1>
<p><?php echo htmlspecialchars($welcomeMessage ?? 'Bienvenue !'); ?></p>

<p>URL de base : <?php echo htmlspecialchars($baseUrl ?? 'N/A'); ?></p>

<p>
    <a href="<?php echo htmlspecialchars($baseUrl); ?>/test-route">Lien vers la page de test</a>
</p>

<?php if ($isLoggedIn ?? false): ?>
    <p>Vous êtes connecté en tant que <?php echo htmlspecialchars($currentUser['prenom'] ?? 'Utilisateur'); ?>.</p>
    <p><a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard">Aller au Tableau de Bord</a></p>
    <p><a href="<?php echo htmlspecialchars($baseUrl); ?>/logout">Se déconnecter</a></p>
<?php else: ?>
    <p>Vous n'êtes pas connecté.</p>
    <p><a href="<?php echo htmlspecialchars($baseUrl); ?>/login">Se connecter</a></p>
<?php endif; ?>