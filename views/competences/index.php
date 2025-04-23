<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Compétences</h1>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="/competences/create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Compétence
            </a>
        <?php endif; ?>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="/competences" method="GET" class="d-flex">
                        <input type="text" 
                               name="search" 
                               class="form-control me-2" 
                               placeholder="Rechercher une compétence..."
                               value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <div class="btn-group">
                            <button type="button" 
                                    class="btn btn-outline-secondary dropdown-toggle" 
                                    data-bs-toggle="dropdown">
                                Filtrer par niveau
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/competences">Tous</a></li>
                                <li><a class="dropdown-item" href="/competences?niveau=débutant">Débutant</a></li>
                                <li><a class="dropdown-item" href="/competences?niveau=intermédiaire">Intermédiaire</a></li>
                                <li><a class="dropdown-item" href="/competences?niveau=avancé">Avancé</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (empty($competences)): ?>
                <div class="alert alert-info">
                    Aucune compétence trouvée.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Intitulé</th>
                                <th>Niveau</th>
                                <th>Module</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($competences as $competence): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($competence['code']); ?></td>
                                    <td><?php echo htmlspecialchars($competence['intitule']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php 
                                            echo match($competence['niveau']) {
                                                'débutant' => 'success',
                                                'intermédiaire' => 'warning',
                                                'avancé' => 'danger',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo htmlspecialchars($competence['niveau']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/modules/<?php echo $competence['id_module']; ?>">
                                            <?php echo htmlspecialchars($competence['module_titre']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/competences/<?php echo $competence['id']; ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                                <a href="/competences/<?php echo $competence['id']; ?>/edit" 
                                                   class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal<?php echo $competence['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($_SESSION['role'] === 'admin'): ?>
                                            <!-- Modal de confirmation de suppression -->
                                            <div class="modal fade" 
                                                 id="deleteModal<?php echo $competence['id']; ?>" 
                                                 tabindex="-1">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirmer la suppression</h5>
                                                            <button type="button" 
                                                                    class="btn-close" 
                                                                    data-bs-dismiss="modal">
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Êtes-vous sûr de vouloir supprimer la compétence 
                                                            "<?php echo htmlspecialchars($competence['intitule']); ?>" ?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" 
                                                                    class="btn btn-secondary" 
                                                                    data-bs-dismiss="modal">
                                                                Annuler
                                                            </button>
                                                            <form action="/competences/<?php echo $competence['id']; ?>/delete" 
                                                                  method="POST" 
                                                                  class="d-inline">
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
                                    <a class="page-link" 
                                       href="/competences?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        Précédent
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" 
                                       href="/competences?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" 
                                       href="/competences?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">
                                        Suivant
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 