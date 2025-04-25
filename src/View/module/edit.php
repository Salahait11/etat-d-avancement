<?php
// Variables disponibles : 
// $title, $baseUrl, $isLoggedIn, $currentUser
// $module (données du module à modifier)
// $filieres (tableau de toutes les filières)
// $formData (données pré-remplies ou soumises)
// $errors (erreurs de validation)

// Utiliser les données soumises ($formData) si elles existent (en cas d'erreur de validation),
// sinon utiliser les données originales du module ($module).
$displayData = $formData ?? $module ?? [];
$formErrors = $errors ?? [];
?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><?= htmlspecialchars($title ?? 'Modifier le Module') ?></h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= BASE_URL ?>/modules" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <?php if (!empty($formErrors) && isset($formErrors['db'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($formErrors['db']) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form action="<?= BASE_URL ?>/modules/update/<?= $module['id'] ?>" method="POST">
                <div class="mb-3">
                    <label for="intitule" class="form-label">Intitulé du module *</label>
                    <input type="text" class="form-control <?= isset($formErrors['intitule']) ? 'is-invalid' : '' ?>" 
                           id="intitule" name="intitule" 
                           value="<?= htmlspecialchars($displayData['intitule'] ?? '') ?>" required>
                    <?php if (isset($formErrors['intitule'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['intitule']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="objectif" class="form-label">Objectif</label>
                    <textarea class="form-control <?= isset($formErrors['objectif']) ? 'is-invalid' : '' ?>" 
                              id="objectif" name="objectif" rows="4"><?= htmlspecialchars($displayData['objectif'] ?? '') ?></textarea>
                    <?php if (isset($formErrors['objectif'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['objectif']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="duree" class="form-label">Durée (en heures) *</label>
                    <input type="number" class="form-control <?= isset($formErrors['duree']) ? 'is-invalid' : '' ?>" 
                           id="duree" name="duree" min="1" 
                           value="<?= htmlspecialchars($displayData['duree'] ?? '') ?>" required>
                    <?php if (isset($formErrors['duree'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['duree']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="id_filiere" class="form-label">Filière *</label>
                    <select class="form-select <?= isset($formErrors['id_filiere']) ? 'is-invalid' : '' ?>" 
                            id="id_filiere" name="id_filiere" required>
                        <option value="">-- Sélectionner une filière --</option>
                        <?php foreach ($filieres as $filiere): ?>
                            <option value="<?= htmlspecialchars($filiere['id']) ?>" 
                                <?= (isset($displayData['id_filiere']) && $displayData['id_filiere'] == $filiere['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($filiere['nom_filiere']) ?> (<?= htmlspecialchars($filiere['niveau']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($formErrors['id_filiere'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['id_filiere']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="<?= BASE_URL ?>/modules" class="btn btn-secondary me-md-2">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
