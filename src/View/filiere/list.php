<?php // src/View/filiere/list.php
$title = $title ?? 'Liste des Filières'; // Utilise le titre passé ou un défaut
?>

<h2>Liste des Filières</h2>

<p>
    <a href="/filieres/add" class="button add-button">Ajouter une nouvelle filière</a>
</p>

<?php if (!empty($filieres)): ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom de la Filière</th>
                <th>Niveau</th>
                <th>Durée (h)</th>
                <th>Description</th>
                <th>Créé le</th>
                <th>Modifié le</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($filieres as $filiere): ?>
                <tr>
                    <td><?php echo htmlspecialchars((string)$filiere['id']); ?></td>
                    <td><?php echo htmlspecialchars($filiere['nom_filiere']); ?></td>
                    <td><?php echo htmlspecialchars($filiere['niveau']); ?></td>
                    <td><?php echo htmlspecialchars((string)$filiere['duree_totale']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($filiere['description'] ?? 'N/A')); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($filiere['created_at']))); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($filiere['updated_at']))); ?></td>
                    <td class="actions">
                        <!-- Liens pour Modifier et Supprimer (à implémenter) -->
                        <a href="/filieres/edit/<?php echo $filiere['id']; ?>" class="button edit-button">Modifier</a>
                        <form action="/filieres/delete/<?php echo $filiere['id']; ?>" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ? Les modules associés seront aussi supprimés.');">
                            <button type="submit" class="button delete-button">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>Aucune filière n'a été trouvée.</p>
<?php endif; ?>

<style>
    /* Styles rapides pour la table et les boutons (à mettre dans style.css idéalement) */
    .data-table { width: 100%; border-collapse: collapse; margin-top: 1em; }
    .data-table th, .data-table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    .data-table th { background-color: #f2f2f2; }
    .data-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
    .actions a, .actions button { margin-right: 5px; text-decoration: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; font-size: 0.9em;}
    .button { border: none; color: white; }
    .add-button { background-color: #4CAF50; /* Green */ }
    .edit-button { background-color: #008CBA; /* Blue */ }
    .delete-button { background-color: #f44336; /* Red */ }
    .error { color: red; font-size: 0.9em; }
    .form-group { margin-bottom: 1em; }
    .form-group label { display: block; margin-bottom: 0.3em; }
    .form-group input[type=text], .form-group input[type=number], .form-group textarea { width: 90%; max-width: 400px; padding: 8px; border: 1px solid #ccc; }
    .form-group textarea { min-height: 80px; }
</style>