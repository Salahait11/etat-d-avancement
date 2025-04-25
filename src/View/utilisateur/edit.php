<?php
// src/View/utilisateur/edit.php
// Vue pour le formulaire d'édition d'un utilisateur
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/utilisateurs" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="<?= BASE_URL ?>/utilisateurs/update/<?= $utilisateur['id'] ?>" method="POST" class="needs-validation" novalidate>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" 
                               id="nom" name="nom" value="<?= htmlspecialchars($formData['nom'] ?? '') ?>" required>
                        <?php if (isset($errors['nom'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['nom']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="prenom" class="form-label">Prénom <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" 
                               id="prenom" name="prenom" value="<?= htmlspecialchars($formData['prenom'] ?? '') ?>" required>
                        <?php if (isset($errors['prenom'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['prenom']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                           id="email" name="email" value="<?= htmlspecialchars($formData['email'] ?? '') ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="mot_de_passe" class="form-label">Mot de passe <small class="text-muted">(laisser vide pour conserver l'actuel)</small></label>
                        <input type="password" class="form-control <?= isset($errors['mot_de_passe']) ? 'is-invalid' : '' ?>" 
                               id="mot_de_passe" name="mot_de_passe">
                        <?php if (isset($errors['mot_de_passe'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['mot_de_passe']) ?></div>
                        <?php endif; ?>
                        <div class="form-text">Si renseigné, le mot de passe doit contenir au moins 6 caractères.</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="confirmation_mot_de_passe" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control <?= isset($errors['confirmation_mot_de_passe']) ? 'is-invalid' : '' ?>" 
                               id="confirmation_mot_de_passe" name="confirmation_mot_de_passe">
                        <?php if (isset($errors['confirmation_mot_de_passe'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['confirmation_mot_de_passe']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['statut']) ? 'is-invalid' : '' ?>" 
                            id="statut" name="statut" required>
                        <option value="actif" <?= ($formData['statut'] ?? '') === 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="inactif" <?= ($formData['statut'] ?? '') === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                    </select>
                    <?php if (isset($errors['statut'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['statut']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label class="form-label">Rôles</label>
                    <div class="card">
                        <div class="card-body">
                            <?php if (empty($roles)): ?>
                                <div class="alert alert-info">Aucun rôle disponible.</div>
                            <?php else: ?>
                                <?php foreach ($roles as $role): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="roles[]" value="<?= $role['id'] ?>" id="role_<?= $role['id'] ?>"
                                               <?= in_array($role['id'], $formData['roles'] ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                            <?= htmlspecialchars($role['nom']) ?> 
                                            <?php if (!empty($role['description'])): ?>
                                                <small class="text-muted">(<?= htmlspecialchars($role['description']) ?>)</small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= BASE_URL ?>/utilisateurs" class="btn btn-outline-secondary me-md-2">Annuler</a>
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
