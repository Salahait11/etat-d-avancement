<?php
// Variables disponibles : $title, $baseUrl, $isLoggedIn, $currentUser
// Spécifiques : $filiere (données originales), $errors (si validation échoue), $formData (données soumises si erreur)

// Utiliser les données soumises ($formData) si elles existent (en cas d'erreur de validation),
// sinon utiliser les données originales de la filière ($filiere).
$displayData = $formData ?? $filiere ?? [];
$formErrors = $errors ?? [];
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title ?? 'Modifier la Filière') ?></h1>
        <a href="<?= BASE_URL ?>/filieres" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (isset($formErrors['general'])): ?>
                <div class="alert alert-danger" role="alert">
                    <?= htmlspecialchars($formErrors['general']) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_message']['type'] ?>">
                    <?= $_SESSION['flash_message']['message'] ?>
                </div>
            <?php endif; ?>
            <form action="<?= BASE_URL ?>/filieres/edit/<?= $filiere['id'] ?>" method="POST">
                <div class="mb-3">
                    <label for="nom_filiere" class="form-label">Nom de la Filière <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($formErrors['nom_filiere']) ? 'is-invalid' : '' ?>" 
                           id="nom_filiere" name="nom_filiere" 
                           value="<?= htmlspecialchars($displayData['nom_filiere'] ?? '') ?>" required>
                    <?php if (isset($formErrors['nom_filiere'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['nom_filiere']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="niveau" class="form-label">Niveau <span class="text-danger">*</span></label>
                    <input type="text" class="form-control <?= isset($formErrors['niveau']) ? 'is-invalid' : '' ?>" 
                           id="niveau" name="niveau" placeholder="Ex: Bac+2, Licence..."
                           value="<?= htmlspecialchars($displayData['niveau'] ?? '') ?>" required>
                    <?php if (isset($formErrors['niveau'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['niveau']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="duree_totale" class="form-label">Durée totale (heures) <span class="text-danger">*</span></label>
                    <?php
                        // Gérer la valeur affichée pour la durée (vient de $formData si erreur, sinon de $filiere)
                        $dureeValue = $formData['duree_totale_str'] ?? $filiere['duree_totale'] ?? '';
                    ?>
                    <input type="number" class="form-control <?= isset($formErrors['duree_totale']) ? 'is-invalid' : '' ?>" 
                           id="duree_totale" name="duree_totale" min="1" step="1"
                           value="<?= htmlspecialchars((string)$dureeValue) ?>" required>
                    <?php if (isset($formErrors['duree_totale'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['duree_totale']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control <?= isset($formErrors['description']) ? 'is-invalid' : '' ?>" 
                              id="description" name="description" rows="4"><?= htmlspecialchars($displayData['description'] ?? '') ?></textarea>
                    <?php if (isset($formErrors['description'])): ?>
                        <div class="invalid-feedback">
                            <?= htmlspecialchars($formErrors['description']) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="<?= BASE_URL ?>/filieres" class="btn btn-secondary">Retour à la liste</a>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
