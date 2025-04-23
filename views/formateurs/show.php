<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4">
        <h1>Détails du Formateur</h1>
        <div>
            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <a href="/formateurs/edit/<?php echo $formateur['id']; ?>" class="btn btn-primary me-2">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-user me-1"></i>
                    Informations Générales
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Nom :</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($formateur['nom']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Prénom :</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($formateur['prenom']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email :</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($formateur['email']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Téléphone :</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($formateur['telephone']); ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Spécialité :</div>
                        <div class="col-md-8"><?php echo htmlspecialchars($formateur['specialite']); ?></div>
                    </div>
                    <?php if (!empty($formateur['commentaires'])): ?>
                        <div class="row mb-3">
                            <div class="col-md-4 fw-bold">Commentaires :</div>
                            <div class="col-md-8"><?php echo nl2br(htmlspecialchars($formateur['commentaires'])); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-book me-1"></i>
                    Modules Assignés
                </div>
                <div class="card-body">
                    <?php if (!empty($formateur['modules'])): ?>
                        <div class="list-group">
                            <?php foreach ($formateur['modules'] as $module): ?>
                                <div class="list-group-item">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($module['nom']); ?></h6>
                                    <small class="text-muted"><?php echo htmlspecialchars($module['description']); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Aucun module assigné à ce formateur.
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Statistiques
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Séances du mois</h5>
                                    <p class="card-text display-6"><?php echo $stats['seances_mois']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Heures de formation</h5>
                                    <p class="card-text display-6"><?php echo $stats['heures_formation']; ?>h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($_SESSION['user_role'] === 'admin'): ?>
<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce formateur ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="/formateurs/delete/<?php echo $formateur['id']; ?>" method="POST" class="d-inline">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?> 