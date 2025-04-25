<?php
// Variables disponibles : $title, $baseUrl, $isLoggedIn, $currentUser
// Spécifiques : $filieres (tableau de filières)
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><?= htmlspecialchars($title ?? 'Liste des Filières') ?></h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= BASE_URL ?>/filieres/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une filière
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?>">
            <?= $_SESSION['flash_message']['message'] ?>
        </div>
    <?php endif; ?>

    <?php if (empty($filieres)): ?>
        <div class="alert alert-info">
            Aucune filière n'est disponible pour le moment.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Niveau</th>
                        <th>Durée (h)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($filieres as $filiere): ?>
                        <tr>
                            <td><?= htmlspecialchars($filiere['id']) ?></td>
                            <td><?= htmlspecialchars($filiere['nom_filiere']) ?></td>
                            <td><?= htmlspecialchars($filiere['niveau']) ?></td>
                            <td><?= htmlspecialchars($filiere['duree_totale']) ?></td>
                            <td class="actions">
                                <!-- Lien GET pour afficher le formulaire d'édition -->
                                <a href="<?= BASE_URL ?>/filieres/edit/<?= $filiere['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                
                                <!-- Formulaire POST pour la suppression -->
                                <form action="<?= BASE_URL ?>/filieres/delete/<?= $filiere['id'] ?>" method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette filière ?\nATTENTION : Tous les modules liés seront également supprimés !');">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>