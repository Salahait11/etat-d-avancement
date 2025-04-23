<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Nouveau Formateur</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-plus me-1"></i>
            Ajouter un Formateur
        </div>
        <div class="card-body">
            <form action="/formateurs/store" method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nom" class="form-label">Nom *</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                        <div class="invalid-feedback">
                            Veuillez entrer le nom du formateur.
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="prenom" class="form-label">Prénom *</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                        <div class="invalid-feedback">
                            Veuillez entrer le prénom du formateur.
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Veuillez entrer une adresse email valide.
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="specialite" class="form-label">Spécialité *</label>
                    <input type="text" class="form-control" id="specialite" name="specialite" required>
                    <div class="invalid-feedback">
                        Veuillez entrer la spécialité du formateur.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="modules" class="form-label">Modules *</label>
                    <select class="form-select" id="modules" name="modules[]" multiple required>
                        <?php foreach ($modules as $module): ?>
                            <option value="<?php echo $module['id']; ?>">
                                <?php echo htmlspecialchars($module['nom']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Veuillez sélectionner au moins un module.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="commentaires" class="form-label">Commentaires</label>
                    <textarea class="form-control" id="commentaires" name="commentaires" rows="3"></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/formateurs" class="btn btn-secondary">
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

<?php
require_once __DIR__ . '/../layouts/footer.php';
?> 