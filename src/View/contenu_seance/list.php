<?php
// src/View/contenu_seance/list.php
// Vue pour afficher la liste des contenus de séance
?>

<div class="container mt-4">
    <!-- Search form -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un contenu..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
        </div>
    </form>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/contenus-seance/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un contenu
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
            <?php if (empty($contenus)): ?>
                <div class="alert alert-info">Aucun contenu de séance trouvé.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Contenu</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($contenus as $contenu): ?>
                                <tr>
                                    <td><?= htmlspecialchars($contenu['id']) ?></td>
                                    <td><?= htmlspecialchars($contenu['contenu']) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>/contenus-seance/edit/<?= $contenu['id'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#genericDeleteModal" data-url="<?= BASE_URL ?>/contenus-seance/delete/<?= $contenu['id'] ?>" data-item="<?= htmlspecialchars($contenu['contenu']) ?>" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <?php if (($totalPages ?? 1) > 1): ?>
                    <nav aria-label="Pagination">
                        <ul class="pagination justify-content-center">
                            <li class="page-item<?= ($currentPage <= 1) ? ' disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?><?= $search ? '&search='.urlencode($search) : '' ?>">Précédent</a>
                            </li>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item<?= ($i === $currentPage) ? ' active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?><?= $search ? '&search='.urlencode($search) : '' ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item<?= ($currentPage >= $totalPages) ? ' disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?><?= $search ? '&search='.urlencode($search) : '' ?>">Suivant</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
