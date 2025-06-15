<?php
// src/View/objectif_pedagogique/list.php
// Vue pour afficher la liste des objectifs pédagogiques
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/objectifs-pedagogiques/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un objectif
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
            <?php if (empty($objectifs)): ?>
                <div class="alert alert-info">Aucun objectif pédagogique trouvé.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Objectif</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($objectifs as $objectif): ?>
                                <tr>
                                    <td><?= htmlspecialchars($objectif['id']) ?></td>
                                    <td><?= htmlspecialchars($objectif['objectif']) ?></td>
                                    <td>
                                        <?php if (!empty($objectif['description'])): ?>
                                            <?= nl2br(htmlspecialchars($objectif['description'])) ?>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">Aucune description</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="actions">
                                        <a href="<?= BASE_URL ?>/objectifs-pedagogiques/edit/<?= $objectif['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        
                                        <?php if (!isset($objectif['is_used']) || !$objectif['is_used']): ?>
                                        <form action="<?= BASE_URL ?>/objectifs-pedagogiques/delete/<?= $objectif['id'] ?>" method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet objectif pédagogique ?');">
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
    </div>
</div>
