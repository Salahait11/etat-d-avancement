<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title ?? 'Détails de la Filière') ?></h1>
        <div>
            <a href="<?= BASE_URL ?>/filieres" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
            <a href="<?= BASE_URL ?>/filieres/edit/<?= $filiere['id'] ?>" class="btn btn-warning">
                <i class="fas fa-edit"></i> Modifier
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0"><?= htmlspecialchars($filiere['nom_filiere']) ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">ID:</th>
                            <td><?= htmlspecialchars((string)$filiere['id']) ?></td>
                        </tr>
                        <tr>
                            <th>Nom:</th>
                            <td><?= htmlspecialchars($filiere['nom_filiere']) ?></td>
                        </tr>
                        <tr>
                            <th>Niveau:</th>
                            <td>
                                <span class="badge bg-info"><?= htmlspecialchars($filiere['niveau']) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>Durée totale:</th>
                            <td><?= htmlspecialchars((string)$filiere['duree_totale']) ?> heures</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">Créé le:</th>
                            <td><?= htmlspecialchars((new DateTime($filiere['created_at']))->format('d/m/Y H:i')) ?></td>
                        </tr>
                        <tr>
                            <th>Dernière mise à jour:</th>
                            <td><?= htmlspecialchars((new DateTime($filiere['updated_at']))->format('d/m/Y H:i')) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-3">
                <h5>Description</h5>
                <div class="p-3 bg-light rounded">
                    <?php if (!empty($filiere['description'])): ?>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($filiere['description'])) ?></p>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aucune description disponible.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Ici, vous pourriez afficher les modules associés à cette filière -->
            <div class="mt-4">
                <h5>Modules associés</h5>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> La liste des modules associés à cette filière sera implémentée ultérieurement.
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash"></i> Supprimer cette filière
            </button>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer la filière <strong><?= htmlspecialchars($filiere['nom_filiere']) ?></strong> ?
                <p class="text-danger mt-2">
                    <i class="fas fa-exclamation-triangle"></i> Cette action est irréversible et supprimera toutes les données associées.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <a href="<?= BASE_URL ?>/filieres/delete/<?= $filiere['id'] ?>" class="btn btn-danger">Supprimer</a>
            </div>
        </div>
    </div>
</div>
