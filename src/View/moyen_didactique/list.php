<?php
// src/View/moyen_didactique/list.php
// Vue pour afficher la liste des moyens didactiques
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/moyens-didactiques/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un moyen
        </a>
    </div>

    <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
        <?php foreach ($flashMessages as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($moyens)): ?>
                <div class="alert alert-info">Aucun moyen didactique trouvé.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Moyen</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($moyens as $moyen): ?>
                                <tr>
                                    <td><?= htmlspecialchars($moyen['id']) ?></td>
                                    <td><?= htmlspecialchars($moyen['moyen']) ?></td>
                                    <td>
                                        <?php if (!empty($moyen['description'])): ?>
                                            <?= nl2br(htmlspecialchars($moyen['description'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Aucune description</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>/moyens-didactiques/edit/<?= $moyen['id'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#genericDeleteModal" data-url="<?= BASE_URL ?>/moyens-didactiques/delete/<?= $moyen['id'] ?>" data-item="<?= htmlspecialchars($moyen['moyen']) ?>" title="Supprimer">
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
    </div>
</div>
