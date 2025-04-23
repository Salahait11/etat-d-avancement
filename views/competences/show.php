<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Détails de la Compétence</h1>
        <div>
            <a href="/competences" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="/competences/<?php echo $competence['id']; ?>/edit" class="btn btn-warning">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Informations Générales</h5>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Code :</strong>
                            <p><?php echo htmlspecialchars($competence['code']); ?></p>
                        </div>
                        <div class="col-md-4">
                            <strong>Niveau :</strong>
                            <p>
                                <span class="badge bg-<?php 
                                    echo match($competence['niveau']) {
                                        'débutant' => 'success',
                                        'intermédiaire' => 'warning',
                                        'avancé' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo htmlspecialchars($competence['niveau']); ?>
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <strong>Module :</strong>
                            <p>
                                <a href="/modules/<?php echo $competence['id_module']; ?>">
                                    <?php echo htmlspecialchars($competence['module_titre']); ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Intitulé :</strong>
                        <p><?php echo htmlspecialchars($competence['intitule']); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong>Description :</strong>
                        <p><?php echo nl2br(htmlspecialchars($competence['description'])); ?></p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Évaluations Associées</h5>
                    <?php if (empty($evaluations)): ?>
                        <div class="alert alert-info">
                            Aucune évaluation associée à cette compétence.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evaluations as $evaluation): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($evaluation['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($evaluation['type']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($evaluation['date_evaluation'])); ?></td>
                                            <td>
                                                <a href="/evaluations/<?php echo $evaluation['id']; ?>" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Statistiques</h5>
                    <div class="mb-3">
                        <strong>Nombre d'évaluations :</strong>
                        <p><?php echo count($evaluations); ?></p>
                    </div>
                    <div class="mb-3">
                        <strong>Moyenne des notes :</strong>
                        <p><?php echo number_format($moyenne_notes, 2); ?>/20</p>
                    </div>
                    <div class="mb-3">
                        <strong>Taux de réussite :</strong>
                        <p><?php echo number_format($taux_reussite, 1); ?>%</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Apprenants</h5>
                    <?php if (empty($apprenants)): ?>
                        <div class="alert alert-info">
                            Aucun apprenant n'a été évalué sur cette compétence.
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($apprenants as $apprenant): ?>
                                <a href="/apprenants/<?php echo $apprenant['id']; ?>" 
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            <?php echo htmlspecialchars($apprenant['nom'] . ' ' . $apprenant['prenom']); ?>
                                        </h6>
                                        <small>
                                            <?php echo number_format($apprenant['moyenne'], 2); ?>/20
                                        </small>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($apprenant['filiere_titre']); ?>
                                    </small>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 