<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1>Liste des Formateurs</h1>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="/formateurs/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Formateur
            </a>
        <?php endif; ?>
    </div>

    <div class="card mb-4 mt-4">
        <div class="card-header">
            <i class="fas fa-search me-1"></i>
            Recherche et Filtres
        </div>
        <div class="card-body">
            <form action="/formateurs" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($search ?? ''); ?>" 
                           placeholder="Nom, prénom, email...">
                </div>
                <div class="col-md-3">
                    <label for="specialite" class="form-label">Spécialité</label>
                    <select class="form-select" id="specialite" name="specialite">
                        <option value="">Toutes</option>
                        <?php foreach ($specialites as $spec): ?>
                            <option value="<?php echo htmlspecialchars($spec); ?>" 
                                    <?php echo (isset($specialite) && $specialite === $spec) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($spec); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="module" class="form-label">Module</label>
                    <select class="form-select" id="module" name="module">
                        <option value="">Tous</option>
                        <?php foreach ($modules as $mod): ?>
                            <option value="<?php echo $mod['id']; ?>" 
                                    <?php echo (isset($module) && $module == $mod['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($mod['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <?php if (empty($formateurs)): ?>
                <div class="alert alert-info">
                    Aucun formateur trouvé.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Spécialité</th>
                                <th>Modules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formateurs as $formateur): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($formateur['nom']); ?></td>
                                    <td><?php echo htmlspecialchars($formateur['prenom']); ?></td>
                                    <td><?php echo htmlspecialchars($formateur['email']); ?></td>
                                    <td><?php echo htmlspecialchars($formateur['telephone']); ?></td>
                                    <td><?php echo htmlspecialchars($formateur['specialite']); ?></td>
                                    <td>
                                        <?php 
                                        $modules = $formateurModel->getModulesByFormateur($formateur['id']);
                                        foreach ($modules as $module): 
                                        ?>
                                            <span class="badge bg-info me-1">
                                                <?php echo htmlspecialchars($module['nom']); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="/formateurs/show/<?php echo $formateur['id']; ?>" 
                                               class="btn btn-sm btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                                <a href="/formateurs/edit/<?php echo $formateur['id']; ?>" 
                                                   class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal<?php echo $formateur['id']; ?>"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                            <!-- Modal de confirmation de suppression -->
                                            <div class="modal fade" id="deleteModal<?php echo $formateur['id']; ?>" 
                                                 tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" class="btn-close" 
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer le formateur 
                                                            <?php echo htmlspecialchars($formateur['nom'] . ' ' . $formateur['prenom']); ?> ?
                                                            Cette action est irréversible.
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" 
                                                                    data-bs-dismiss="modal">Annuler</button>
                                                            <form action="/formateurs/delete/<?php echo $formateur['id']; ?>" 
                                                                  method="POST" class="d-inline">
                                                                <button type="submit" class="btn btn-danger">
                                                                    Supprimer
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Navigation des pages" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $query_string; ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo $query_string; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $query_string; ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?> 