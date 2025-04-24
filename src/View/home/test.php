<?php // src/View/home/test.php ?>

<h1><?php echo htmlspecialchars($title ?? 'Page de Test'); ?></h1>
<p>Ceci est la page de test rendue par le contrôleur.</p>
<p><a href="<?php echo htmlspecialchars($baseUrl ?? '/'); ?>/">Retour à l'accueil</a></p>