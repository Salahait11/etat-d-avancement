<?php
// src/View/formateur/edit.php
?>
<div class="container mt-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <form method="post" action="<?= BASE_URL ?>/formateurs/update/<?= $f['id'] ?>">
        <div class="mb-3">
            <label for="specialite" class="form-label">Spécialité</label>
            <input type="text" id="specialite" name="specialite" class="form-control <?= isset($errors['specialite']) ? 'is-invalid' : '' ?>" value="<?= htmlspecialchars($formData['specialite']) ?>">
            <?php if (isset($errors['specialite'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['specialite']) ?></div>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="<?= BASE_URL ?>/formateurs" class="btn btn-secondary">Annuler</a>
    </form>
</div>
