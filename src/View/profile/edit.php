<?php
// Les variables $title, $user, $formateur, $errors, $isFormateur sont passées par le contrôleur
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Modifier mon profil</h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>/profile/update" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom</label>
                                <input type="text" class="form-control <?= isset($errors['nom']) ? 'is-invalid' : '' ?>" 
                                       id="nom" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required>
                                <?php if (isset($errors['nom'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['nom']) ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input type="text" class="form-control <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>" 
                                       id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required>
                                <?php if (isset($errors['prenom'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['prenom']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                                   id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                            <?php if (isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="ancien_mot_de_passe" class="form-label">Ancien mot de passe</label>
                            <input type="password" class="form-control <?= isset($errors['ancien_mot_de_passe']) ? 'is-invalid' : '' ?>" 
                                   id="ancien_mot_de_passe" name="ancien_mot_de_passe">
                            <?php if (isset($errors['ancien_mot_de_passe'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['ancien_mot_de_passe']) ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Requis uniquement si vous souhaitez changer votre mot de passe</small>
                        </div>

                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Nouveau mot de passe</label>
                            <input type="password" class="form-control <?= isset($errors['mot_de_passe']) ? 'is-invalid' : '' ?>" 
                                   id="mot_de_passe" name="mot_de_passe">
                            <?php if (isset($errors['mot_de_passe'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['mot_de_passe']) ?></div>
                            <?php endif; ?>
                            <small class="text-muted">Laissez vide pour ne pas changer le mot de passe</small>
                        </div>

                        <div class="mb-3">
                            <label for="confirmation_mot_de_passe" class="form-label">Confirmer le nouveau mot de passe</label>
                            <input type="password" class="form-control" 
                                   id="confirmation_mot_de_passe" name="confirmation_mot_de_passe">
                        </div>

                        <?php if ($isFormateur && $formateur): ?>
                        <hr>
                        <h5 class="mb-3">Informations Formateur</h5>
                        <div class="mb-3">
                            <label for="specialite" class="form-label">Spécialité</label>
                            <input type="text" class="form-control" id="specialite" name="specialite" 
                                   value="<?= htmlspecialchars($formateur['specialite'] ?? '') ?>">
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= BASE_URL ?>/profile" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 