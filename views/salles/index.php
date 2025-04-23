<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Salles</h1>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="/?route=salles&action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Salle
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="/?route=salles" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" 
                           placeholder="Nom, description...">
                </div>
                <div class="col-md-3">
                    <label for="capacite_min" class="form-label">Capacité minimum</label>
                    <input type="number" class="form-control" id="capacite_min" name="capacite_min" 
                           value="<?php echo htmlspecialchars($_GET['capacite_min'] ?? ''); ?>" 
                           min="1">
                </div>
                <div class="col-md-3">
                    <label for="capacite_max" class="form-label">Capacité maximum</label>
                    <input type="number" class="form-control" id="capacite_max" name="capacite_max" 
                           value="<?php echo htmlspecialchars($_GET['capacite_max'] ?? ''); ?>" 
                           min="1">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php if (empty($salles)): ?>
        <div class="alert alert-info">
            Aucune salle ne correspond à vos critères de recherche.
        </div>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            <?php foreach ($salles as $salle): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($salle['nom']); ?></h5>
                            <p class="card-text">
                                <i class="fas fa-users"></i> <?php echo htmlspecialchars($salle['capacite']); ?> places
                            </p>
                            <?php if ($salle['description']): ?>
                                <p class="card-text text-muted">
                                    <?php echo nl2br(htmlspecialchars($salle['description'])); ?>
                                </p>
                            <?php endif; ?>
                            <div class="mt-3">
                                <div class="progress mb-2">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo $salle['taux_occupation']; ?>%"
                                         aria-valuenow="<?php echo $salle['taux_occupation']; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo $salle['taux_occupation']; ?>%
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <?php echo $salle['seances_mois']; ?> séances ce mois
                                </small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="/?route=salles&action=show&id=<?php echo $salle['id']; ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                    <div>
                                        <a href="/?route=salles&action=edit&id=<?php echo $salle['id']; ?>" 
                                           class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal<?php echo $salle['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de confirmation de suppression -->
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <div class="modal fade" id="deleteModal<?php echo $salle['id']; ?>" tabindex="-1" 
                         aria-labelledby="deleteModalLabel<?php echo $salle['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $salle['id']; ?>">
                                        Confirmer la suppression
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Êtes-vous sûr de vouloir supprimer la salle "<?php echo htmlspecialchars($salle['nom']); ?>" ? 
                                    Cette action est irréversible.
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <form action="/?route=salles&action=delete&id=<?php echo $salle['id']; ?>" 
                                          method="POST" class="d-inline">
                                        <button type="submit" class="btn btn-danger">Supprimer</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Navigation des pages" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?route=salles&page=<?php echo $page - 1; ?><?php echo $query_string; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?route=salles&page=<?php echo $i; ?><?php echo $query_string; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?route=salles&page=<?php echo $page + 1; ?><?php echo $query_string; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 