<?php
// src/View/formateur/list.php
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/formateurs/add" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Ajouter un Formateur
        </a>
    </div>

    <?php if (empty($formateurs)): ?>
        <div class="alert alert-info">
            Aucun formateur trouvé.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Spécialité</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($formateurs as $f): ?>
                        <tr>
                            <td><?= htmlspecialchars($f['id']) ?></td>
                            <td><?= htmlspecialchars($f['prenom']) ?></td>
                            <td><?= htmlspecialchars($f['nom']) ?></td>
                            <td><?= htmlspecialchars($f['email']) ?></td>
                            <td><?= htmlspecialchars($f['specialite']) ?></td>
                            <td class="actions">
                                <a href="<?= BASE_URL ?>/formateurs/edit/<?= $f['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                
                                <?php if (!isset($f['is_used']) || !$f['is_used']): ?>
                                <form action="<?= BASE_URL ?>/formateurs/delete/<?= $f['id'] ?>" method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce formateur ?');">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
