<?php
// src/View/contenu_seance/edit.php
// Vue pour le formulaire d'édition d'un contenu de séance
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/contenus-seance" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= BASE_URL ?>/contenus-seance/update/<?= $contenu['id'] ?>" method="POST" class="needs-validation" novalidate>
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="contenu" class="form-label">Contenu <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['contenu']) ? 'is-invalid' : '' ?>" 
                           id="contenu" name="contenu" value="<?= htmlspecialchars($formData['contenu'] ?? '') ?>" required>
                    <?php if (isset($errors['contenu'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['contenu']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= BASE_URL ?>/contenus-seance" class="btn btn-outline-secondary me-md-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation côté client
(function() {
    'use strict';
    
    // Fetch all forms we want to apply custom validation styles to
    var forms = document.querySelectorAll('.needs-validation');
    
    // Loop over them and prevent submission
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
