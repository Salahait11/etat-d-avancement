<?php
// src/View/etat_avancement/view.php
// Vue pour afficher les détails d'un état d'avancement
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tasks me-2"></i> <?= htmlspecialchars($title) ?></h1>
        <div>
            <?php if ($isAdmin || $etat['id_formateur'] == $currentUser['id']): ?>
                <a href="<?= BASE_URL ?>/etats-avancement/edit/<?= $etat['id'] ?>" class="btn btn-primary me-2">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/etats-avancement" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
        <?php foreach ($flashMessages as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                    <i class="fas fa-<?= $type === 'success' ? 'check-circle' : ($type === 'error' || $type === 'danger' ? 'exclamation-circle' : 'info-circle') ?> me-2"></i>
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informations générales</h5>
                    <span class="badge bg-light text-primary"><?= (new DateTime($etat['date_seance']))->format('d/m/Y') ?></span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="fas fa-book fa-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Module</h6>
                                    <p class="mb-0 fw-bold"><?= htmlspecialchars($etat['module_intitule']) ?></p>
                                </div>
                            </div>
                            <div class="d-flex mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="fas fa-user fa-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Formateur</h6>
                                    <p class="mb-0 fw-bold"><?= htmlspecialchars($etat['formateur_nom']) ?></p>
                                </div>
                            </div>
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                        <i class="fas fa-clock fa-lg"></i>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-1">Durée réalisée</h6>
                                    <p class="mb-0 fw-bold"><?= htmlspecialchars($etat['duree_realisee']) ?> heures</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 border-start border-light">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Création</h6>
                                        <p class="timeline-date"><i class="fas fa-calendar-alt me-1"></i> <?= (new DateTime($etat['created_at']))->format('d/m/Y') ?></p>
                                        <p class="timeline-time"><i class="fas fa-clock me-1"></i> <?= (new DateTime($etat['created_at']))->format('H:i') ?></p>
                                    </div>
                                </div>
                                <?php if ($etat['updated_at'] && $etat['updated_at'] !== $etat['created_at']): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="timeline-title">Dernière mise à jour</h6>
                                        <p class="timeline-date"><i class="fas fa-calendar-alt me-1"></i> <?= (new DateTime($etat['updated_at']))->format('d/m/Y') ?></p>
                                        <p class="timeline-time"><i class="fas fa-clock me-1"></i> <?= (new DateTime($etat['updated_at']))->format('H:i') ?></p>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commentaires et difficultés -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-comment-dots me-2"></i> Commentaires et difficultés</h5>
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCommentaires" aria-expanded="true" aria-controls="collapseCommentaires">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="collapseCommentaires">
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-comment me-2"></i> Commentaire général</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($etat['commentaire'])): ?>
                                            <div class="comment-box">
                                                <?= nl2br(htmlspecialchars($etat['commentaire'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                                                <p class="text-muted fst-italic">Aucun commentaire</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Difficultés rencontrées</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($etat['difficultes'])): ?>
                                            <div class="comment-box bg-light-warning">
                                                <?= nl2br(htmlspecialchars($etat['difficultes'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                <p class="text-muted fst-italic">Aucune difficulté signalée</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0"><i class="fas fa-lightbulb me-2"></i> Solutions proposées</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($etat['solutions'])): ?>
                                            <div class="comment-box bg-light-success">
                                                <?= nl2br(htmlspecialchars($etat['solutions'])) ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-4">
                                                <i class="fas fa-lightbulb-slash fa-3x text-muted mb-3"></i>
                                                <p class="text-muted fst-italic">Aucune solution proposée</p>
                                            </div>
                                        <?php endif; ?>
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

    <div class="row">
        <!-- Objectifs pédagogiques -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-bullseye me-2"></i> Objectifs pédagogiques</h5>
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseObjectifs" aria-expanded="true" aria-controls="collapseObjectifs">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="collapseObjectifs">
                    <div class="card-body">
                        <?php if (empty($objectifs)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <p class="text-muted fst-italic">Aucun objectif pédagogique associé</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-sm" id="searchObjectifs" placeholder="Rechercher un objectif...">
                            </div>
                            <div class="progress mb-3" style="height: 20px;">
                                <?php 
                                $total = count($objectifs);
                                $atteints = 0;
                                $partiels = 0;
                                
                                foreach ($objectifs as $obj) {
                                    if ($obj['statut'] === 'atteint') $atteints++;
                                    else if ($obj['statut'] === 'partiel') $partiels++;
                                }
                                
                                $pctAtteints = $total > 0 ? ($atteints / $total) * 100 : 0;
                                $pctPartiels = $total > 0 ? ($partiels / $total) * 100 : 0;
                                $pctNonAtteints = 100 - $pctAtteints - $pctPartiels;
                                ?>
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctAtteints ?>%" 
                                     aria-valuenow="<?= $pctAtteints ?>" aria-valuemin="0" aria-valuemax="100" 
                                     data-bs-toggle="tooltip" title="<?= $atteints ?> objectif(s) atteint(s)">
                                    <?= round($pctAtteints) ?>%
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pctPartiels ?>%" 
                                     aria-valuenow="<?= $pctPartiels ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $partiels ?> objectif(s) partiellement atteint(s)">
                                    <?= round($pctPartiels) ?>%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $pctNonAtteints ?>%" 
                                     aria-valuenow="<?= $pctNonAtteints ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $total - $atteints - $partiels ?> objectif(s) non atteint(s)">
                                    <?= round($pctNonAtteints) ?>%
                                </div>
                            </div>
                            <ul class="list-group objectifs-list">
                                <?php foreach ($objectifs as $objectif): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="objectif-text"><?= htmlspecialchars($objectif['objectif']) ?></span>
                                        <span class="badge <?= $objectif['statut'] === 'atteint' ? 'bg-success' : ($objectif['statut'] === 'partiel' ? 'bg-warning' : 'bg-danger') ?> rounded-pill">
                                            <?= $objectif['statut'] === 'atteint' ? 'Atteint' : ($objectif['statut'] === 'partiel' ? 'Partiellement' : 'Non atteint') ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenus de séance -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i> Contenus de séance</h5>
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseContenus" aria-expanded="true" aria-controls="collapseContenus">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="collapseContenus">
                    <div class="card-body">
                        <?php if (empty($contenus)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted fst-italic">Aucun contenu de séance associé</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-sm" id="searchContenus" placeholder="Rechercher un contenu...">
                            </div>
                            <div class="progress mb-3" style="height: 20px;">
                                <?php 
                                $total = count($contenus);
                                $realises = 0;
                                $partiels = 0;
                                
                                foreach ($contenus as $cont) {
                                    if ($cont['statut'] === 'realise') $realises++;
                                    else if ($cont['statut'] === 'partiel') $partiels++;
                                }
                                
                                $pctRealises = $total > 0 ? ($realises / $total) * 100 : 0;
                                $pctPartiels = $total > 0 ? ($partiels / $total) * 100 : 0;
                                $pctNonRealises = 100 - $pctRealises - $pctPartiels;
                                ?>
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctRealises ?>%" 
                                     aria-valuenow="<?= $pctRealises ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $realises ?> contenu(s) réalisé(s)">
                                    <?= round($pctRealises) ?>%
                                </div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pctPartiels ?>%" 
                                     aria-valuenow="<?= $pctPartiels ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $partiels ?> contenu(s) partiellement réalisé(s)">
                                    <?= round($pctPartiels) ?>%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $pctNonRealises ?>%" 
                                     aria-valuenow="<?= $pctNonRealises ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $total - $realises - $partiels ?> contenu(s) non réalisé(s)">
                                    <?= round($pctNonRealises) ?>%
                                </div>
                            </div>
                            <ul class="list-group contenus-list">
                                <?php foreach ($contenus as $contenu): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="contenu-text"><?= htmlspecialchars($contenu['contenu']) ?></span>
                                        <span class="badge <?= $contenu['statut'] === 'realise' ? 'bg-success' : ($contenu['statut'] === 'partiel' ? 'bg-warning' : 'bg-danger') ?> rounded-pill">
                                            <?= $contenu['statut'] === 'realise' ? 'Réalisé' : ($contenu['statut'] === 'partiel' ? 'Partiellement' : 'Non réalisé') ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Moyens didactiques -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i> Moyens didactiques</h5>
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMoyens" aria-expanded="true" aria-controls="collapseMoyens">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="collapseMoyens">
                    <div class="card-body">
                        <?php if (empty($moyens)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-toolbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted fst-italic">Aucun moyen didactique associé</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-sm" id="searchMoyens" placeholder="Rechercher un moyen...">
                            </div>
                            <div class="progress mb-3" style="height: 20px;">
                                <?php 
                                $total = count($moyens);
                                $utilises = 0;
                                
                                foreach ($moyens as $moyen) {
                                    if ($moyen['statut'] === 'utilise') $utilises++;
                                }
                                
                                $pctUtilises = $total > 0 ? ($utilises / $total) * 100 : 0;
                                $pctNonUtilises = 100 - $pctUtilises;
                                ?>
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctUtilises ?>%" 
                                     aria-valuenow="<?= $pctUtilises ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $utilises ?> moyen(s) utilisé(s)">
                                    <?= round($pctUtilises) ?>%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $pctNonUtilises ?>%" 
                                     aria-valuenow="<?= $pctNonUtilises ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $total - $utilises ?> moyen(s) non utilisé(s)">
                                    <?= round($pctNonUtilises) ?>%
                                </div>
                            </div>
                            <ul class="list-group moyens-list">
                                <?php foreach ($moyens as $moyen): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="moyen-text"><?= htmlspecialchars($moyen['moyen']) ?></span>
                                        <span class="badge <?= $moyen['statut'] === 'utilise' ? 'bg-success' : 'bg-danger' ?> rounded-pill">
                                            <?= $moyen['statut'] === 'utilise' ? 'Utilisé' : 'Non utilisé' ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stratégies d'évaluation -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i> Stratégies d'évaluation</h5>
                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseStrategies" aria-expanded="true" aria-controls="collapseStrategies">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="collapse show" id="collapseStrategies">
                    <div class="card-body">
                        <?php if (empty($strategies)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                                <p class="text-muted fst-italic">Aucune stratégie d'évaluation associée</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <input type="text" class="form-control form-control-sm" id="searchStrategies" placeholder="Rechercher une stratégie...">
                            </div>
                            <div class="progress mb-3" style="height: 20px;">
                                <?php 
                                $total = count($strategies);
                                $appliquees = 0;
                                
                                foreach ($strategies as $strategie) {
                                    if ($strategie['statut'] === 'appliquee') $appliquees++;
                                }
                                
                                $pctAppliquees = $total > 0 ? ($appliquees / $total) * 100 : 0;
                                $pctNonAppliquees = 100 - $pctAppliquees;
                                ?>
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $pctAppliquees ?>%" 
                                     aria-valuenow="<?= $pctAppliquees ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $appliquees ?> stratégie(s) appliquée(s)">
                                    <?= round($pctAppliquees) ?>%
                                </div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $pctNonAppliquees ?>%" 
                                     aria-valuenow="<?= $pctNonAppliquees ?>" aria-valuemin="0" aria-valuemax="100"
                                     data-bs-toggle="tooltip" title="<?= $total - $appliquees ?> stratégie(s) non appliquée(s)">
                                    <?= round($pctNonAppliquees) ?>%
                                </div>
                            </div>
                            <ul class="list-group strategies-list">
                                <?php foreach ($strategies as $strategie): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span class="strategie-text"><?= htmlspecialchars($strategie['strategie']) ?></span>
                                        <span class="badge <?= $strategie['statut'] === 'appliquee' ? 'bg-success' : 'bg-danger' ?> rounded-pill">
                                            <?= $strategie['statut'] === 'appliquee' ? 'Appliquée' : 'Non appliquée' ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
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
                <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Confirmation de suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous vraiment supprimer cet état d'avancement ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i> Annuler</button>
                <form action="<?= BASE_URL ?>/etats-avancement/delete/<?= $etat['id'] ?>" method="POST">
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-2"></i> Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
    
    // Gestion des filtres de recherche
    function setupSearch(inputId, listClass, textClass) {
        const searchInput = document.getElementById(inputId);
        if (!searchInput) return;
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll(`.${listClass} li`);
            
            items.forEach(item => {
                const text = item.querySelector(`.${textClass}`).textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
    
    // Configuration des filtres pour chaque section
    setupSearch('searchObjectifs', 'objectifs-list', 'objectif-text');
    setupSearch('searchContenus', 'contenus-list', 'contenu-text');
    setupSearch('searchMoyens', 'moyens-list', 'moyen-text');
    setupSearch('searchStrategies', 'strategies-list', 'strategie-text');
    
    // Gestion des boutons de collapse
    document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon) {
                if (icon.classList.contains('fa-chevron-down')) {
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                } else {
                    icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                }
            }
        });
    });
});
</script>
