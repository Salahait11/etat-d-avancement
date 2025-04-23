<?php
$title = "Liste des évaluations";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-clipboard-list me-2"></i>Liste des évaluations</h1>
        <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'formateur'): ?>
            <a href="/evaluations/create" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle évaluation
            </a>
        <?php endif; ?>
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

    <div class="card mb-4">
        <div class="card-body">
            <form action="/evaluations" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Rechercher</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="<?= htmlspecialchars($search ?? '') ?>" 
                           placeholder="Apprenant ou module...">
                </div>
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Date début</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?= htmlspecialchars($start_date ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Date fin</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?= htmlspecialchars($end_date ?? '') ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Apprenant</th>
                            <th>Module</th>
                            <th>Compétences</th>
                            <th>Moyenne</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($evaluations)): ?>
                            <tr>
                                <td colspan="6" class="text-center">Aucune évaluation trouvée</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($evaluations as $evaluation): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($evaluation['date_evaluation'])) ?></td>
                                    <td>
                                        <a href="/apprenants/<?= $evaluation['id_apprenant'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($evaluation['apprenant_nom'] . ' ' . $evaluation['apprenant_prenom']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="/modules/<?= $evaluation['id_module'] ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($evaluation['module_code'] . ' - ' . $evaluation['module_nom']) ?>
                                        </a>
                                    </td>
                                    <td><?= $evaluation['nb_competences'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $evaluation['moyenne'] >= 10 ? 'success' : 'danger' ?>">
                                            <?= number_format($evaluation['moyenne'], 1) ?>/20
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/evaluations/<?= $evaluation['id'] ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'formateur'): ?>
                                                <a href="/evaluations/<?= $evaluation['id'] ?>/edit" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            onclick="confirmDelete(<?= $evaluation['id'] ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($total_pages > 1): ?>
                <nav aria-label="Navigation des pages" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search ?? '') ?>&start_date=<?= urlencode($start_date ?? '') ?>&end_date=<?= urlencode($end_date ?? '') ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&start_date=<?= urlencode($start_date ?? '') ?>&end_date=<?= urlencode($end_date ?? '') ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search ?? '') ?>&start_date=<?= urlencode($start_date ?? '') ?>&end_date=<?= urlencode($end_date ?? '') ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            <?php endif; ?>
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 