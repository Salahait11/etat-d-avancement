<?php // src/View/dashboard/index.php
$title = "Tableau de Bord";
?>
<h2>Tableau de Bord</h2>

<?php if (isset($currentUser) && $currentUser): ?>
    <p>Bienvenue, <?php echo htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']); ?> !</p>
<?php endif; ?>

<p>Ceci est votre tableau de bord principal.</p>
<ul>
    <li><a href="/filieres">Gérer les Filières</a></li>
    <li><a href="/logout">Se déconnecter</a></li>
</ul>