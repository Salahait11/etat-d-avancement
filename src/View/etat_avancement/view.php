<?php
/**
 * Vue pour afficher les détails d'un état d'avancement
 * 
 * @var array $data Données formatées de l'état d'avancement
 * @var bool $isAdmin Indique si l'utilisateur est administrateur
 * @var array $currentUser Données de l'utilisateur connecté
 */

// Vérification des données
if (!isset($data) || !isset($data['data']) || !isset($data['data']['etat'])) {
    echo '<div class="alert alert-danger">Aucune donnée disponible pour cet état d\'avancement.</div>';
    return;
}

$etat = $data['data']['etat'];
$objectifs = $data['data']['objectifs'] ?? [];
$moyens = $data['data']['moyens'] ?? [];
$strategies = $data['data']['strategies'] ?? [];
?>

<div class="container-fluid">
    <!-- En-tête avec titre et boutons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-clipboard-list me-2"></i>État d'Avancement
                    </h4>
                    <p class="text-muted mb-0">
                        Module : <?= htmlspecialchars($data['data']['etat']['module']) ?> | 
                        Formateur : <?= htmlspecialchars($data['data']['etat']['formateur']) ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= BASE_URL ?>/etats-avancement" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <a href="<?= BASE_URL ?>/etats-avancement/edit/<?= $data['data']['etat']['id'] ?>" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date de séance</label>
                                <p class="mb-0"><?= htmlspecialchars($data['data']['etat']['date']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Heure de séance</label>
                                <p class="mb-0"><?= htmlspecialchars($data['data']['etat']['heure']) ?></p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Durée réalisée</label>
                                <p class="mb-0"><?= htmlspecialchars($data['data']['etat']['nbr_heure']) ?> heures</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Heures cumulées</label>
                                <p class="mb-0"><?= htmlspecialchars($data['data']['etat']['nbr_heure_cumulee']) ?> heures</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation par onglets -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <ul class="nav nav-tabs mb-4" id="viewTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                                <i class="fas fa-info-circle me-2"></i>Informations générales
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="objectifs-tab" data-bs-toggle="tab" data-bs-target="#objectifs" type="button" role="tab">
                                <i class="fas fa-bullseye me-2"></i>Objectifs
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="moyens-tab" data-bs-toggle="tab" data-bs-target="#moyens" type="button" role="tab">
                                <i class="fas fa-tools me-2"></i>Moyens
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="strategies-tab" data-bs-toggle="tab" data-bs-target="#strategies" type="button" role="tab">
                                <i class="fas fa-chart-line me-2"></i>Stratégies
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="commentaires-tab" data-bs-toggle="tab" data-bs-target="#commentaires" type="button" role="tab">
                                <i class="fas fa-comments me-2"></i>Commentaires
                            </button>
                        </li>
                    </ul>

                    <!-- Contenu des onglets -->
                    <div class="tab-content" id="viewTabsContent">
                        <!-- Onglet Informations générales -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Module</label>
                                            <p class="mb-0"><?= htmlspecialchars($etat['module']) ?></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Formateur</label>
                                            <p class="mb-0"><?= htmlspecialchars($etat['formateur']) ?></p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Taux de réalisation</label>
                                            <div class="progress" style="height: 25px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: <?= htmlspecialchars($etat['taux_realisation']) ?>%"
                                                     aria-valuenow="<?= htmlspecialchars($etat['taux_realisation']) ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?= htmlspecialchars($etat['taux_realisation']) ?>%
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label fw-bold">Disposition</label>
                                            <p class="mb-0">
                                                <span class="badge bg-<?= $etat['disposition'] === 'Oui' ? 'success' : 'danger' ?>">
                                                    <?= htmlspecialchars($etat['disposition']) ?>
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Contenu de la séance -->
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <label class="form-label fw-bold">Contenu de la séance</label>
                                            <?php if (!empty($etat['contenu_seance'])): ?>
                                                <div class="p-3 bg-light rounded">
                                                    <?= nl2br(htmlspecialchars($etat['contenu_seance'])) ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-info mb-0">
                                                    <i class="fas fa-info-circle me-2"></i>Aucun contenu de séance renseigné
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Objectifs -->
                        <div class="tab-pane fade" id="objectifs" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <?php if (!empty($objectifs)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Objectif</th>
                                                        <th class="text-center" style="width: 150px;">Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($objectifs as $objectif): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($objectif['libelle']) ?></td>
                                                            <td class="text-center">
                                                                <span class="badge bg-<?= getStatusColor($objectif['statut']) ?>">
                                                                    <?= htmlspecialchars($objectif['statut']) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>Aucun objectif pédagogique associé
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Moyens -->
                        <div class="tab-pane fade" id="moyens" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <?php if (!empty($moyens)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Moyen</th>
                                                        <th class="text-center" style="width: 150px;">Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($moyens as $moyen): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($moyen['libelle']) ?></td>
                                                            <td class="text-center">
                                                                <span class="badge bg-<?= getStatusColor($moyen['statut']) ?>">
                                                                    <?= htmlspecialchars($moyen['statut']) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>Aucun moyen didactique associé
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Stratégies -->
                        <div class="tab-pane fade" id="strategies" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <?php if (!empty($strategies)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Stratégie</th>
                                                        <th class="text-center" style="width: 150px;">Statut</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($strategies as $strategie): ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($strategie['libelle']) ?></td>
                                                            <td class="text-center">
                                                                <span class="badge bg-<?= getStatusColor($strategie['statut']) ?>">
                                                                    <?= htmlspecialchars($strategie['statut']) ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>Aucune stratégie d'évaluation associée
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Commentaires -->
                        <div class="tab-pane fade" id="commentaires" role="tabpanel">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="h-100">
                                                <h4 class="h6 text-muted mb-3">Commentaire général</h4>
                                                <div class="p-3 bg-light rounded h-100">
                                                    <?= !empty($etat['commentaire']) ? nl2br(htmlspecialchars($etat['commentaire'])) : '<em class="text-muted">Aucun commentaire</em>' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="h-100">
                                                <h4 class="h6 text-muted mb-3">Difficultés rencontrées</h4>
                                                <div class="p-3 bg-light rounded h-100">
                                                    <?= !empty($etat['difficultes']) ? nl2br(htmlspecialchars($etat['difficultes'])) : '<em class="text-muted">Aucune difficulté signalée</em>' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="h-100">
                                                <h4 class="h6 text-muted mb-3">Solutions proposées</h4>
                                                <div class="p-3 bg-light rounded h-100">
                                                    <?= !empty($etat['solutions']) ? nl2br(htmlspecialchars($etat['solutions'])) : '<em class="text-muted">Aucune solution proposée</em>' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmation de suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet état d'avancement ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form action="<?= BASE_URL ?>/etats-avancement/delete/<?= htmlspecialchars($etat['id']) ?>" method="POST">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
/**
 * Retourne la couleur Bootstrap appropriée pour un statut
 */
function getStatusColor($statut) {
    $colors = [
        'Atteint' => 'success',
        'En cours' => 'warning',
        'Non atteint' => 'danger',
        'Réalisé' => 'success',
        'Utilisé' => 'success',
        'Non utilisé' => 'secondary',
        'Appliquée' => 'success',
        'Non appliquée' => 'secondary',
        'Utilisée' => 'success'
    ];
    
    return $colors[$statut] ?? 'secondary';
}
?>
