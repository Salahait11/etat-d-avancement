<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">Modifier la Salle</h2>
                    <a href="/?route=salles&action=show&id=<?php echo $salle['id']; ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <form action="/?route=salles&action=update&id=<?php echo $salle['id']; ?>" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom de la salle *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nom" 
                                   name="nom" 
                                   value="<?php echo htmlspecialchars($salle['nom']); ?>"
                                   required>
                            <div class="invalid-feedback">
                                Veuillez saisir le nom de la salle.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="capacite" class="form-label">Capacité *</label>
                            <input type="number" 
                                   class="form-control" 
                                   id="capacite" 
                                   name="capacite" 
                                   value="<?php echo htmlspecialchars($salle['capacite']); ?>"
                                   min="1"
                                   required>
                            <div class="invalid-feedback">
                                Veuillez saisir une capacité valide (minimum 1).
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"><?php echo htmlspecialchars($salle['description']); ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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