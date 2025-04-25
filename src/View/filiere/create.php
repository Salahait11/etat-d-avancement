<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title ?? 'Ajouter une Filière') ?></h1>
        <a href="<?= BASE_URL ?>/filieres" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($errors['general'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/filieres/store" method="post">
                <div class="mb-3">
                    <label for="nom_filiere" class="form-label">Nom de la filière <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($errors['nom_filiere']) ? 'is-invalid' : '' ?>" 
                           id="nom_filiere" name="nom_filiere" 
                           value="<?= htmlspecialchars($formData['nom_filiere'] ?? '') ?>" required>
                    <?php if (isset($errors['nom_filiere'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['nom_filiere']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" 
                              id="description" name="description" rows="3"><?= htmlspecialchars($formData['description'] ?? '') ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['description']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="niveau" class="form-label">Niveau <span class="text-danger">*</span></label>
                    <select class="form-select <?= isset($errors['niveau']) ? 'is-invalid' : '' ?>" 
                            id="niveau" name="niveau" required>
                        <option value="" <?= empty($formData['niveau']) ? 'selected' : '' ?>>Sélectionnez un niveau</option>
                        <option value="Débutant" <?= ($formData['niveau'] ?? '') === 'Débutant' ? 'selected' : '' ?>>Débutant</option>
                        <option value="Intermédiaire" <?= ($formData['niveau'] ?? '') === 'Intermédiaire' ? 'selected' : '' ?>>Intermédiaire</option>
                        <option value="Avancé" <?= ($formData['niveau'] ?? '') === 'Avancé' ? 'selected' : '' ?>>Avancé</option>
                        <option value="Expert" <?= ($formData['niveau'] ?? '') === 'Expert' ? 'selected' : '' ?>>Expert</option>
                    </select>
                    <?php if (isset($errors['niveau'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['niveau']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="duree_totale" class="form-label">Durée totale (heures) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control <?= isset($errors['duree_totale']) ? 'is-invalid' : '' ?>" 
                           id="duree_totale" name="duree_totale" min="1" 
                           value="<?= htmlspecialchars($formData['duree_totale'] ?? '') ?>" required>
                    <?php if (isset($errors['duree_totale'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['duree_totale']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Réinitialiser</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
