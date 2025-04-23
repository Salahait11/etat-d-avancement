<?php
$title = "Modifier l'évaluation";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2"></i>Modifier l'évaluation</h1>
        <a href="/evaluations/<?= $evaluation['id'] ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour aux détails
        </a>
    </div>

    <?php if (isset($_SESSION['errors'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['errors']); ?>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="/evaluations/<?= $evaluation['id'] ?>/update" method="POST" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Apprenant</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($evaluation['apprenant_nom'] . ' ' . $evaluation['apprenant_prenom']) ?>" readonly>
                        <input type="hidden" name="apprenant_id" value="<?= $evaluation['id_apprenant'] ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Module</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($evaluation['module_code'] . ' - ' . $evaluation['module_nom']) ?>" readonly>
                        <input type="hidden" name="module_id" value="<?= $evaluation['id_module'] ?>">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date_evaluation" class="form-label">Date d'évaluation <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_evaluation" name="date_evaluation" 
                               value="<?= isset($_SESSION['post_data']['date_evaluation']) ? $_SESSION['post_data']['date_evaluation'] : $evaluation['date_evaluation'] ?>" 
                               max="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Veuillez sélectionner une date d'évaluation.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="commentaire" class="form-label">Commentaire</label>
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3"><?= isset($_SESSION['post_data']['commentaire']) ? htmlspecialchars($_SESSION['post_data']['commentaire']) : htmlspecialchars($evaluation['commentaire']) ?></textarea>
                </div>

                <div class="mb-4">
                    <h4 class="mb-3">Notes des compétences</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Compétence</th>
                                    <th>Niveau</th>
                                    <th>Note /20</th>
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
                                            <input type="number" class="form-control form-control-sm" 
                                                   name="notes[<?= $note['id_competence'] ?>]" 
                                                   value="<?= isset($_SESSION['post_data']['notes'][$note['id_competence']]) ? $_SESSION['post_data']['notes'][$note['id_competence']] : $note['note'] ?>"
                                                   min="0" max="20" step="0.5" required>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/evaluations/<?= $evaluation['id'] ?>" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>

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