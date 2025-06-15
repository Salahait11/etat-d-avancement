<?php
// Variables disponibles : $title, $modules, $baseUrl, $isLoggedIn, $currentUser, $search, $currentPage, $totalPages
?>

<div class="container mt-4">
    <!-- Search form -->
    <form method="GET" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Rechercher un module ou filière..." value="<?= htmlspecialchars($search ?? '') ?>">
            <button class="btn btn-outline-secondary" type="submit">Rechercher</button>
        </div>
    </form>
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><?= htmlspecialchars($title ?? 'Liste des Modules') ?></h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= BASE_URL ?>/modules/add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un nouveau module
            </a>
        </div>
    </div>

    <?php if (empty($modules)): ?>
        <div class="alert alert-info">
            Aucun module n'a été trouvé.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Intitulé</th>
                        <th>Filière</th>
                        <th>Durée (h)</th>
                        <th>Objectif</th>
                        <th>Créé le</th>
                        <th>Modifié le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($modules as $module): ?>
                        <tr>
                            <td><?= htmlspecialchars((string)$module['id']) ?></td>
                            <td><?= htmlspecialchars($module['intitule']) ?></td>
                            <td><?= htmlspecialchars($module['nom_filiere']) ?> (ID: <?= htmlspecialchars((string)$module['id_filiere']) ?>)</td>
                            <td><?= htmlspecialchars((string)$module['duree']) ?></td>
                            <td><?= nl2br(htmlspecialchars($module['objectif'] ?? 'N/A')) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($module['created_at']))) ?></td>
                            <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($module['updated_at']))) ?></td>
                            <td class="actions">
                                <a href="<?= BASE_URL ?>/modules/edit/<?= $module['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                                
                                <?php if (!isset($module['is_used']) || !$module['is_used']): ?>
                                <form action="<?= BASE_URL ?>/modules/delete/<?= $module['id'] ?>" method="POST" style="display: inline;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce module ?');">
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
