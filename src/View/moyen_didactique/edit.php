<?php
// src/View/moyen_didactique/edit.php
// Vue pour le formulaire d'édition d'un moyen didactique
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/moyens-didactiques" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= BASE_URL ?>/moyens-didactiques/update/<?= $moyen['id'] ?>" method="POST" class="needs-validation" novalidate>
                <?php if (isset($errors['general'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>
                
                <div class="mb-3">
                    <label for="moyen" class="form-label">Moyen <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['moyen']) ? 'is-invalid' : '' ?>" 
                           id="moyen" name="moyen" value="<?= htmlspecialchars($formData['moyen'] ?? '') ?>" required>
                    <?php if (isset($errors['moyen'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['moyen']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                              id="description" name="description" rows="4"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['description']) ?></div>
                    <?php endif; ?>
                    <div class="form-text">Décrivez le moyen didactique en détail (optionnel).</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= BASE_URL ?>/moyens-didactiques" class="btn btn-outline-secondary me-md-2">Annuler</a>
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
