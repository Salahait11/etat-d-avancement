<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Détails du Module</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle me-1"></i>
                Informations générales
            </div>
            <div>
                <?php if ($this->checkPermission('module', 'edit')): ?>
                    <a href="/modules/edit/<?= $module['id'] ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                <?php endif; ?>
                <?php if ($this->checkPermission('module', 'delete')): ?>
                    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $module['id'] ?>)">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                <?php endif; ?>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Code</th>
                            <td><?= htmlspecialchars($module['code']) ?></td>
                        </tr>
                        <tr>
                            <th>Nom</th>
                            <td><?= htmlspecialchars($module['nom']) ?></td>
                        </tr>
                        <tr>
                            <th>Durée</th>
                            <td><?= htmlspecialchars($module['duree']) ?> heures</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td><?= nl2br(htmlspecialchars($module['description'])) ?: '<span class="text-muted">Aucune description</span>' ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-bar me-1"></i>
                            Statistiques
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6 mb-3">
                                    <h5><?= $stats['seances_mois'] ?></h5>
                                    <small class="text-muted">Séances ce mois</small>
                                </div>
                                <div class="col-6 mb-3">
                                    <h5><?= $stats['heures_formation'] ?></h5>
                                    <small class="text-muted">Heures de formation</small>
                                </div>
                                <div class="col-6">
                                    <h5><?= $stats['formateurs_count'] ?></h5>
                                    <small class="text-muted">Formateurs</small>
                                </div>
                                <div class="col-6">
                                    <h5><?= $stats['competences_count'] ?></h5>
                                    <small class="text-muted">Compétences</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Compétences -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-tasks me-1"></i>
            Compétences associées
        </div>
        <div class="card-body">
            <?php if (empty($competences)): ?>
                <div class="alert alert-info">
                    Aucune compétence associée à ce module.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Intitulé</th>
                                <th>Niveau</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($competences as $competence): ?>
                                <tr>
                                    <td><?= htmlspecialchars($competence['code']) ?></td>
                                    <td><?= htmlspecialchars($competence['intitule']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= match($competence['niveau']) {
                                            'Débutant' => 'success',
                                            'Intermédiaire' => 'warning',
                                            'Avancé' => 'danger',
                                            default => 'secondary'
                                        } ?>">
                                            <?= htmlspecialchars($competence['niveau']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($competence['description']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Formateurs -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-chalkboard-teacher me-1"></i>
            Formateurs assignés
        </div>
        <div class="card-body">
            <?php if (empty($formateurs)): ?>
                <div class="alert alert-info">
                    Aucun formateur assigné à ce module.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Spécialité</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($formateurs as $formateur): ?>
                                <tr>
                                    <td><?= htmlspecialchars($formateur['nom'] . ' ' . $formateur['prenom']) ?></td>
                                    <td><?= htmlspecialchars($formateur['email']) ?></td>
                                    <td><?= htmlspecialchars($formateur['specialite']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Séances -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-calendar-alt me-1"></i>
            Séances planifiées
        </div>
        <div class="card-body">
            <?php if (empty($seances)): ?>
                <div class="alert alert-info">
                    Aucune séance planifiée pour ce module.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Horaire</th>
                                <th>Formateur</th>
                                <th>Salle</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seances as $seance): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($seance['date'])) ?></td>
                                    <td><?= $seance['heure_debut'] . ' - ' . $seance['heure_fin'] ?></td>
                                    <td><?= htmlspecialchars($seance['formateur_nom'] . ' ' . $seance['formateur_prenom']) ?></td>
                                    <td><?= htmlspecialchars($seance['salle']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= match($seance['statut']) {
                                            'planifiée' => 'primary',
                                            'en cours' => 'warning',
                                            'terminée' => 'success',
                                            'annulée' => 'danger',
                                            default => 'secondary'
                                        } ?>">
                                            <?= htmlspecialchars($seance['statut']) ?>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer ce module ? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    const modal = document.getElementById('deleteModal');
    const form = document.getElementById('deleteForm');
    form.action = `/modules/delete/${id}`;
    new bootstrap.Modal(modal).show();
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 