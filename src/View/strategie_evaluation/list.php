<?php
// src/View/strategie_evaluation/list.php
// Vue pour afficher la liste des stratégies d'évaluation
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/strategies-evaluation/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter une stratégie
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
            <?php if (empty($strategies)): ?>
                <div class="alert alert-info">Aucune stratégie d'évaluation trouvée.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Stratégie</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($strategies as $strategie): ?>
                                <tr>
                                    <td><?= htmlspecialchars($strategie['id']) ?></td>
                                    <td><?= htmlspecialchars($strategie['strategie']) ?></td>
                                    <td>
                                        <?php if (!empty($strategie['description'])): ?>
                                            <?= nl2br(htmlspecialchars($strategie['description'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Aucune description</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>/strategies-evaluation/edit/<?= $strategie['id'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#genericDeleteModal" data-url="<?= BASE_URL ?>/strategies-evaluation/delete/<?= $strategie['id'] ?>" data-item="<?= htmlspecialchars($strategie['strategie']) ?>" title="Supprimer">
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
