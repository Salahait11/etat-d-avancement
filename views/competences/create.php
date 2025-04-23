<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Nouvelle Compétence</h1>
        <a href="/competences" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="/competences/store" method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="code" 
                               name="code" 
                               required>
                        <div class="form-text">
                            Code unique identifiant la compétence
                        </div>
                        <div class="invalid-feedback">
                            Le code est requis.
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="intitule" class="form-label">Intitulé <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control" 
                               id="intitule" 
                               name="intitule" 
                               required>
                        <div class="invalid-feedback">
                            L'intitulé est requis.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="niveau" class="form-label">Niveau <span class="text-danger">*</span></label>
                        <select class="form-select" id="niveau" name="niveau" required>
                            <option value="">Sélectionnez un niveau</option>
                            <option value="débutant">Débutant</option>
                            <option value="intermédiaire">Intermédiaire</option>
                            <option value="avancé">Avancé</option>
                        </select>
                        <div class="invalid-feedback">
                            Le niveau est requis.
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="id_module" class="form-label">Module <span class="text-danger">*</span></label>
                        <select class="form-select" id="id_module" name="id_module" required>
                            <option value="">Sélectionnez un module</option>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?php echo $module['id']; ?>">
                                    <?php echo htmlspecialchars($module['titre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">
                            Le module est requis.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              rows="5" 
                              required></textarea>
                    <div class="invalid-feedback">
                        La description est requise.
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer la compétence
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation des formulaires Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
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