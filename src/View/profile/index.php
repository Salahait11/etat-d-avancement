<?php
// Les variables $title, $user, $formateur, $isAdmin, $isFormateur sont passées par le contrôleur
?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Mon Profil</h4>
                    <a href="<?= BASE_URL ?>/profile/edit" class="btn btn-light btn-sm">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4 text-center">
                            <div class="avatar-circle mb-3">
                                <i class="fas fa-user fa-3x"></i>
                            </div>
                            <h5><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></h5>
                            <span class="badge bg-<?= $isAdmin ? 'danger' : ($isFormateur ? 'primary' : 'secondary') ?>">
                                <?= $isAdmin ? 'Administrateur' : ($isFormateur ? 'Formateur' : 'Utilisateur') ?>
                            </span>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="text-muted">Email</label>
                                <p class="mb-0"><?= htmlspecialchars($user['email']) ?></p>
                            </div>
                            <?php if (!empty($user['telephone'])): ?>
                            <div class="mb-3">
                                <label class="text-muted">Téléphone</label>
                                <p class="mb-0"><?= htmlspecialchars($user['telephone']) ?></p>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($user['adresse'])): ?>
                            <div class="mb-3">
                                <label class="text-muted">Adresse</label>
                                <p class="mb-0"><?= htmlspecialchars($user['adresse']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($isFormateur && $formateur): ?>
                    <hr>
                    <h5 class="mb-3">Informations Formateur</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Spécialité</label>
                                <p class="mb-0"><?= htmlspecialchars($formateur['specialite'] ?? 'Non spécifiée') ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Années d'expérience</label>
                                <p class="mb-0"><?= htmlspecialchars($formateur['annees_experience'] ?? 'Non spécifiées') ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 100px;
    height: 100px;
    background-color: #e9ecef;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: #6c757d;
}
</style> 