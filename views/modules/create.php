<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Nouveau Module</h1>
    
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
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-plus me-1"></i>
            Créer un nouveau module
        </div>
        <div class="card-body">
            <form action="/modules/store" method="POST" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required
                               pattern="[A-Z0-9]{2,10}" title="2 à 10 caractères alphanumériques majuscules"
                               value="<?= htmlspecialchars($_POST['code'] ?? '') ?>">
                        <div class="invalid-feedback">
                            Le code est requis et doit contenir entre 2 et 10 caractères alphanumériques majuscules.
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nom" name="nom" required
                               minlength="3" maxlength="100"
                               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
                        <div class="invalid-feedback">
                            Le nom est requis et doit contenir entre 3 et 100 caractères.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                              maxlength="500"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                    <div class="form-text">
                        Maximum 500 caractères.
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="duree" class="form-label">Durée (heures) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="duree" name="duree" required
                               min="1" max="1000" step="0.5"
                               value="<?= htmlspecialchars($_POST['duree'] ?? '') ?>">
                        <div class="invalid-feedback">
                            La durée est requise et doit être comprise entre 1 et 1000 heures.
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Compétences associées</label>
                    <div class="row">
                        <?php foreach ($competences as $competence): ?>
                            <div class="col-md-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="competences[]" value="<?= $competence['id'] ?>"
                                           id="competence_<?= $competence['id'] ?>"
                                           <?= in_array($competence['id'], $_POST['competences'] ?? []) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="competence_<?= $competence['id'] ?>">
                                        <?= htmlspecialchars($competence['code'] . ' - ' . $competence['intitule']) ?>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="/modules" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation côté client
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 