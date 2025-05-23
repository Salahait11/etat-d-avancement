<?php
// src/View/strategie_evaluation/edit.php
// Vue pour le formulaire d'édition d'une stratégie d'évaluation
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/strategies-evaluation" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= BASE_URL ?>/strategies-evaluation/update/<?= $strategie['id'] ?>" method="POST" class="needs-validation" novalidate>
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="strategie" class="form-label">Stratégie <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['strategie']) ? 'is-invalid' : '' ?>" 
                           id="strategie" name="strategie" value="<?= htmlspecialchars($formData['strategie'] ?? '') ?>" required>
                    <?php if (isset($errors['strategie'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['strategie']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                              id="description" name="description" rows="4"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                    <?php endif; ?>
                    <div class="form-text">Décrivez la stratégie d'évaluation en détail (optionnel).</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= BASE_URL ?>/strategies-evaluation" class="btn btn-outline-secondary me-md-2">Annuler</a>
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
