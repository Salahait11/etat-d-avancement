<?php
// src/View/formateur/add.php
?>
<div class="container mt-4">
    <h1><?= htmlspecialchars($title) ?></h1>
    <form method="post" action="<?= BASE_URL ?>/formateurs/store">
        <div class="mb-3">
            <label for="userSelect" class="form-label">Utilisateur</label>
            <select id="userSelect" name="id_utilisateur" class="form-select <?= isset($errors['id_utilisateur']) ? 'is-invalid' : '' ?>">
                <option value="">-- Sélectionnez un utilisateur --</option>
                <?php foreach ($users as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $formData['id_utilisateur'] == $u['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['prenom'] . ' ' . $u['nom'] . ' (' . $u['email'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['id_utilisateur'])): ?>
                <div class="invalid-feedback"><?= htmlspecialchars($errors['id_utilisateur']) ?></div>
            <?php endif; ?>
        </div>
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
