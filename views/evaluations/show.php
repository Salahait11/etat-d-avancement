<?php
$title = "Détails de l'évaluation";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-clipboard-check me-2"></i>Détails de l'évaluation</h1>
        <div class="btn-group">
            <a href="/evaluations" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
            <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'formateur'): ?>
                <a href="/evaluations/<?= $evaluation['id'] ?>/edit" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $evaluation['id'] ?>)">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations générales
                    </h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Date</dt>
                        <dd class="col-sm-8"><?= date('d/m/Y', strtotime($evaluation['date_evaluation'])) ?></dd>

                        <dt class="col-sm-4">Apprenant</dt>
                        <dd class="col-sm-8">
                            <a href="/apprenants/<?= $evaluation['id_apprenant'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($evaluation['apprenant_nom'] . ' ' . $evaluation['apprenant_prenom']) ?>
                            </a>
                        </dd>

                        <dt class="col-sm-4">Module</dt>
                        <dd class="col-sm-8">
                            <a href="/modules/<?= $evaluation['id_module'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($evaluation['module_code'] . ' - ' . $evaluation['module_nom']) ?>
                            </a>
                        </dd>

                        <?php if (!empty($evaluation['commentaire'])): ?>
                            <dt class="col-sm-4">Commentaire</dt>
                            <dd class="col-sm-8"><?= nl2br(htmlspecialchars($evaluation['commentaire'])) ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="mb-0"><?= number_format($moyenne, 1) ?>/20</h3>
                        <div class="progress mt-2" style="height: 10px;">
                            <div class="progress-bar bg-<?= $moyenne >= 10 ? 'success' : 'danger' ?>" 
                                 role="progressbar" 
                                 style="width: <?= ($moyenne / 20) * 100 ?>%">
                            </div>
                        </div>
                    </div>
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Nombre de compétences</dt>
                        <dd class="col-sm-6"><?= count($notes) ?></dd>

                        <dt class="col-sm-6">Note minimale</dt>
                        <dd class="col-sm-6"><?= number_format(min(array_column($notes, 'note')), 1) ?>/20</dd>

                        <dt class="col-sm-6">Note maximale</dt>
                        <dd class="col-sm-6"><?= number_format(max(array_column($notes, 'note')), 1) ?>/20</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list-check me-2"></i>Notes des compétences
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Compétence</th>
                                    <th>Niveau</th>
                                    <th>Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($notes as $note): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($note['competence_code']) ?></td>
                                        <td><?= htmlspecialchars($note['competence_titre']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= getNiveauColor($note['competence_niveau']) ?>">
                                                <?= htmlspecialchars($note['competence_niveau']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $note['note'] >= 10 ? 'success' : 'danger' ?>">
                                                <?= number_format($note['note'], 1) ?>/20
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($_SESSION['user']['role'] === 'admin'): ?>
    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Êtes-vous sûr de vouloir supprimer cette évaluation ? Cette action est irréversible.
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
            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            const form = document.getElementById('deleteForm');
            form.action = `/evaluations/${id}/delete`;
            modal.show();
        }
    </script>
<?php endif; ?>

<?php
function getNiveauColor($niveau) {
    switch(strtolower($niveau)) {
        case 'débutant': return 'info';
        case 'intermédiaire': return 'primary';
        case 'avancé': return 'success';
        default: return 'secondary';
    }
}
?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 