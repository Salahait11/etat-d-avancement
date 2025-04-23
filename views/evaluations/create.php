<?php
$title = "Nouvelle évaluation";
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-plus-circle me-2"></i>Nouvelle évaluation</h1>
        <a href="/evaluations" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
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
            <form action="/evaluations/store" method="POST" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="apprenant_id" class="form-label">Apprenant <span class="text-danger">*</span></label>
                        <select class="form-select" id="apprenant_id" name="apprenant_id" required>
                            <option value="">Sélectionner un apprenant</option>
                            <?php foreach ($apprenants as $apprenant): ?>
                                <option value="<?= $apprenant['id'] ?>" <?= isset($_SESSION['post_data']['apprenant_id']) && $_SESSION['post_data']['apprenant_id'] == $apprenant['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($apprenant['nom'] . ' ' . $apprenant['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner un apprenant.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="module_id" class="form-label">Module <span class="text-danger">*</span></label>
                        <select class="form-select" id="module_id" name="module_id" required>
                            <option value="">Sélectionner un module</option>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?= $module['id'] ?>" <?= isset($_SESSION['post_data']['module_id']) && $_SESSION['post_data']['module_id'] == $module['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($module['code'] . ' - ' . $module['nom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner un module.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date_evaluation" class="form-label">Date d'évaluation <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="date_evaluation" name="date_evaluation" 
                               value="<?= isset($_SESSION['post_data']['date_evaluation']) ? $_SESSION['post_data']['date_evaluation'] : date('Y-m-d') ?>" 
                               max="<?= date('Y-m-d') ?>" required>
                        <div class="invalid-feedback">Veuillez sélectionner une date d'évaluation.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="commentaire" class="form-label">Commentaire</label>
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3"><?= isset($_SESSION['post_data']['commentaire']) ? htmlspecialchars($_SESSION['post_data']['commentaire']) : '' ?></textarea>
                </div>

                <div class="mb-4">
                    <h4 class="mb-3">Notes des compétences</h4>
                    <div id="competences-container">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Veuillez sélectionner un module pour afficher les compétences associées.
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="/evaluations" class="btn btn-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer l'évaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleSelect = document.getElementById('module_id');
    const competencesContainer = document.getElementById('competences-container');
    const form = document.querySelector('form');

    // Charger les compétences lors de la sélection d'un module
    moduleSelect.addEventListener('change', function() {
        const moduleId = this.value;
        if (moduleId) {
            fetch(`/api/modules/${moduleId}/competences`)
                .then(response => response.json())
                .then(competences => {
                    if (competences.length > 0) {
                        let html = `
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
                        `;
                        
                        competences.forEach(competence => {
                            html += `
                                <tr>
                                    <td>${competence.code}</td>
                                    <td>${competence.titre}</td>
                                    <td>
                                        <span class="badge bg-${getNiveauColor(competence.niveau)}">
                                            ${competence.niveau}
                                        </span>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
                                               name="notes[${competence.id}]" 
                                               min="0" max="20" step="0.5" required>
                                    </td>
                                </tr>
                            `;
                        });

                        html += `
                                    </tbody>
                                </table>
                            </div>
                        `;
                        competencesContainer.innerHTML = html;
                    } else {
                        competencesContainer.innerHTML = `
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>Aucune compétence associée à ce module.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    competencesContainer.innerHTML = `
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>Une erreur est survenue lors du chargement des compétences.
                        </div>
                    `;
                });
        } else {
            competencesContainer.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>Veuillez sélectionner un module pour afficher les compétences associées.
                </div>
            `;
        }
    });

    // Validation du formulaire
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});

function getNiveauColor(niveau) {
    switch(niveau.toLowerCase()) {
        case 'débutant': return 'info';
        case 'intermédiaire': return 'primary';
        case 'avancé': return 'success';
        default: return 'secondary';
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 