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
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= BASE_URL ?>/formateurs/edit/<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete" 
                                            data-bs-toggle="modal" data-bs-target="#genericDeleteModal" 
                                            data-url="<?= BASE_URL ?>/formateurs/delete/<?= $f['id'] ?>" 
                                            data-item="<?= htmlspecialchars($f['prenom'] . ' ' . $f['nom']) ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
