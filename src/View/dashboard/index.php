<?php // src/View/dashboard/index.php

// La variable $title est passée par le contrôleur via la méthode render()
// Les variables $baseUrl, $isLoggedIn, $currentUser sont automatiquement injectées par render()

?>

<h2><?php echo htmlspecialchars($title ?? 'Tableau de Bord'); ?></h2>

<?php if (isset($currentUser) && $currentUser): ?>
    <p>
        Bienvenue sur votre tableau de bord,
        <strong><?php echo htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']); ?></strong> !
    </p>
    <p style="font-size: 0.9em; color: #555;">
        Connecté avec l'email : <?php echo htmlspecialchars($currentUser['email']); ?>
        (ID: <?php echo htmlspecialchars((string)$currentUser['id']); ?>)
    </p>
<?php else: ?>
    <p>Bienvenue sur le tableau de bord.</p>
    <p style="color: orange;">(Note: Si vous voyez ceci, il y a un problème car cette page devrait être protégée et $currentUser devrait être défini).</p>
<?php endif; ?>

<hr style="margin: 20px 0;">

<h3>Accès Rapides</h3>
<p>Voici quelques actions courantes que vous pouvez effectuer :</p>
<ul>
    <li>
        <a href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/filieres" class="button">Gérer les Filières</a>
        <span style="font-size: 0.9em; color: #6c757d;">(Voir la liste, ajouter, modifier, supprimer)</span>
    </li>
    <li>
        <a href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/modules" class="button secondary-button">Gérer les Modules</a>
        <span style="font-size: 0.9em; color: #6c757d;">(Implémentation future)</span>
    </li>
    <li>
        <a href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/etat-avancement" class="button secondary-button">Suivi Pédagogique</a>
        <span style="font-size: 0.9em; color: #6c757d;">(Implémentation future)</span>
    </li>
    <?php // Ajouter d'autres liens pertinents selon les rôles plus tard ?>
    <!-- Exemple:
    <li>
        <a href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/utilisateurs" class="button secondary-button">Gérer les Utilisateurs</a>
        <span style="font-size: 0.9em; color: #6c757d;">(Réservé Admin - Implémentation future)</span>
    </li>
    -->
</ul>

<?php if (!empty($stats)): ?>
<h3>Statistiques</h3>
<ul>
    <li>Filières : <?php echo htmlspecialchars($stats['filieres']); ?></li>
    <li>Modules : <?php echo htmlspecialchars($stats['modules']); ?></li>
    <li>États d'avancement : <?php echo htmlspecialchars($stats['etats']); ?></li>
    <li>Utilisateurs : <?php echo htmlspecialchars($stats['utilisateurs']); ?></li>
</ul>
<?php endif; ?>

<?php if (!empty($latestEtats)): ?>
    <h3>Derniers suivis</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Date de séance</th>
                <th>Module</th>
                <th>Formateur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($latestEtats as $etat): ?>
                <tr>
                    <td><?php echo htmlspecialchars((new DateTime($etat['date_seance']))->format('d/m/Y')); ?></td>
                    <td><?php echo htmlspecialchars($etat['module_intitule']); ?></td>
                    <td><?php echo htmlspecialchars($etat['formateur_nom']); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/etat-avancement/edit/<?php echo $etat['id']; ?>" class="btn btn-sm btn-outline-primary">Voir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucun suivi récent.</p>
<?php endif; ?>

<p style="margin-top: 30px;">
    <a href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/logout" class="button delete-button">Se déconnecter</a>
</p>

<?php
// On pourrait ajouter ici des widgets, des statistiques, etc. pour un vrai tableau de bord.
// Par exemple : Nombre de filières, derniers modules ajoutés, etc.
// nécessiterait de passer ces données supplémentaires depuis le contrôleur.
?>