<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Informations générales -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Détails de la Salle</h2>
                    <div>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="/?route=salles&action=edit&id=<?php echo $salle['id']; ?>" class="btn btn-primary me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        <?php endif; ?>
                        <a href="/?route=salles" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nom :</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($salle['nom']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Capacité :</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($salle['capacite']); ?> places</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Description :</div>
                        <div class="col-md-8"><?php echo nl2br(htmlspecialchars($salle['description'] ?? 'Aucune description disponible')); ?></div>
                    </div>
                </div>
            </div>

            <!-- Séances planifiées -->
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Séances Planifiées</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($seances)): ?>
                        <div class="alert alert-info">
                            Aucune séance n'est planifiée dans cette salle.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Heure</th>
                                        <th>Module</th>
                                        <th>Formateur</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($seances as $seance): ?>
                                        <tr>
                                            <td><?php echo date('d/m/Y', strtotime($seance['date'])); ?></td>
                                            <td><?php echo substr($seance['heure_debut'], 0, 5) . ' - ' . substr($seance['heure_fin'], 0, 5); ?></td>
                                            <td><?php echo htmlspecialchars($seance['module_titre']); ?></td>
                                            <td><?php echo htmlspecialchars($seance['formateur_nom']); ?></td>
                                            <td>
                                                <?php
                                                $statutClass = match($seance['statut']) {
                                                    'planifiee' => 'warning',
                                                    'en_cours' => 'success',
                                                    'terminee' => 'info',
                                                    'annulee' => 'danger',
                                                    default => 'secondary'
                                                };
                                                $statutLabel = match($seance['statut']) {
                                                    'planifiee' => 'Planifiée',
                                                    'en_cours' => 'En cours',
                                                    'terminee' => 'Terminée',
                                                    'annulee' => 'Annulée',
                                                    default => 'Inconnu'
                                                };
                                                ?>
                                                <span class="badge bg-<?php echo $statutClass; ?>">
                                                    <?php echo $statutLabel; ?>
                                                </span>
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

        <!-- Statistiques -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Statistiques</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5>Séances du mois</h5>
                        <div class="display-4"><?php echo $stats['seances_mois']; ?></div>
                    </div>
                    <div class="mb-3">
                        <h5>Taux d'occupation</h5>
                        <div class="progress">
                            <div class="progress-bar" role="progressbar" 
                                 style="width: <?php echo $stats['taux_occupation']; ?>%"
                                 aria-valuenow="<?php echo $stats['taux_occupation']; ?>" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                <?php echo $stats['taux_occupation']; ?>%
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<?php if ($_SESSION['user_role'] === 'admin'): ?>
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette salle ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="/?route=salles&action=delete&id=<?php echo $salle['id']; ?>" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 