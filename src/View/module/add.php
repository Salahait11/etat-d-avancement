<?php
// Variables disponibles : 
// $title, $baseUrl, $isLoggedIn, $currentUser
// $filieres (tableau de toutes les filières)
// $formData (données pré-remplies)
// $errors (erreurs de validation)
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><?= htmlspecialchars($title ?? 'Ajouter un Module') ?></h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= BASE_URL ?>/modules" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <?php if (!empty($errors) && isset($errors['db'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($errors['db']) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= BASE_URL ?>/modules/store" method="POST">
                <div class="mb-3">
                    <label for="intitule" class="form-label">Intitulé du module *</label>
                    <input type="text" class="form-control <?= isset($errors['intitule']) ? 'is-invalid' : '' ?>" 
                           id="intitule" name="intitule" 
                           value="<?= htmlspecialchars($formData['intitule'] ?? '') ?>" required>
                    <?php if (isset($errors['intitule'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['intitule']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="objectif" class="form-label">Objectif</label>
                    <textarea class="form-control <?= isset($errors['objectif']) ? 'is-invalid' : '' ?>" 
                              id="objectif" name="objectif" rows="4"><?= htmlspecialchars($formData['objectif'] ?? '') ?></textarea>
                    <?php if (isset($errors['objectif'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['objectif']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="duree" class="form-label">Durée (en heures) *</label>
                    <input type="number" class="form-control <?= isset($errors['duree']) ? 'is-invalid' : '' ?>" 
                           id="duree" name="duree" min="1" 
                           value="<?= htmlspecialchars($formData['duree'] ?? '') ?>" required>
                    <?php if (isset($errors['duree'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['duree']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="id_filiere" class="form-label">Filière *</label>
                    <select class="form-select <?= isset($errors['id_filiere']) ? 'is-invalid' : '' ?>" 
                            id="id_filiere" name="id_filiere" required>
                        <option value="">-- Sélectionner une filière --</option>
                        <?php foreach ($filieres as $filiere): ?>
                            <option value="<?= htmlspecialchars($filiere['id']) ?>" 
                                <?= (isset($formData['id_filiere']) && $formData['id_filiere'] == $filiere['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($filiere['nom_filiere']) ?> (<?= htmlspecialchars($filiere['niveau']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['id_filiere'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($errors['id_filiere']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= BASE_URL ?>/modules" class="btn btn-secondary me-md-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>


