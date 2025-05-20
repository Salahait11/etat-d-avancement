<?php
// src/View/etat_avancement/edit.php
// Vue pour le formulaire d'édition d'un état d'avancement
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-edit me-2"></i> <?= htmlspecialchars($title) ?></h1>
        <div>
            <a href="<?= BASE_URL ?>/etats-avancement/view/<?= $etat['id'] ?>" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> Voir les détails
            </a>
            <a href="<?= BASE_URL ?>/etats-avancement" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>
        </div>
    </div>

    <?php if (isset($errors['general'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= htmlspecialchars($errors['general']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/etats-avancement/update/<?= $etat['id'] ?>" method="POST" class="needs-validation" novalidate>
        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs mb-4" id="editTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="fas fa-info-circle me-2"></i> Informations générales
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="objectifs-tab" data-bs-toggle="tab" data-bs-target="#objectifs" type="button" role="tab">
                    <i class="fas fa-bullseye me-2"></i> Objectifs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="moyens-tab" data-bs-toggle="tab" data-bs-target="#moyens" type="button" role="tab">
                    <i class="fas fa-tools me-2"></i> Moyens
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="strategies-tab" data-bs-toggle="tab" data-bs-target="#strategies" type="button" role="tab">
                    <i class="fas fa-chart-line me-2"></i> Stratégies
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="commentaires-tab" data-bs-toggle="tab" data-bs-target="#commentaires" type="button" role="tab">
                    <i class="fas fa-comments me-2"></i> Commentaires
                </button>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content" id="editTabsContent">
            <!-- Onglet Informations générales -->
            <div class="tab-pane fade show active" id="general" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="id_module" class="form-label">Module <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['id_module']) ? 'is-invalid' : '' ?>" 
                                        id="id_module" name="id_module" required>
                                    <option value="">Sélectionner un module</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?= $module['id'] ?>" 
                                                <?= ($module['id'] == $formData['id_module']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($module['intitule']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_module'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['id_module']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_formateur" class="form-label">Formateur <span class="text-danger">*</span></label>
                                <select class="form-select <?= isset($errors['id_formateur']) ? 'is-invalid' : '' ?>" 
                                        id="id_formateur" name="id_formateur" required>
                                    <option value="">Sélectionner un formateur</option>
                                    <?php foreach ($formateurs as $formateur): ?>
                                        <option value="<?= $formateur['id'] ?>" 
                                                <?= ($formateur['id'] == $formData['id_formateur']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($formateur['nom'] . ' ' . $formateur['prenom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_formateur'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['id_formateur']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="date" class="form-label">Date de séance <span class="text-danger">*</span></label>
                                <input type="date" class="form-control <?= isset($errors['date']) ? 'is-invalid' : '' ?>" 
                                       id="date" name="date" value="<?= htmlspecialchars($formData['date'] ?? '') ?>" required>
                                <?php if (isset($errors['date'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['date']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="heure" class="form-label">Heure de séance <span class="text-danger">*</span></label>
                                <input type="time" class="form-control <?= isset($errors['heure']) ? 'is-invalid' : '' ?>" 
                                       id="heure" name="heure" value="<?= htmlspecialchars($formData['heure'] ?? '') ?>" required>
                                <?php if (isset($errors['heure'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['heure']) ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="nbr_heure" class="form-label">Durée réalisée (heures) <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" min="0.5" class="form-control <?= isset($errors['nbr_heure']) ? 'is-invalid' : '' ?>" 
                                       id="nbr_heure" name="nbr_heure" value="<?= htmlspecialchars($formData['nbr_heure'] ?? '') ?>" required>
                                <?php if (isset($errors['nbr_heure'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['nbr_heure']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="nbr_heure_cumulee" class="form-label">Heures cumulées</label>
                                <input type="number" class="form-control" id="nbr_heure_cumulee" name="nbr_heure_cumulee" 
                                       value="<?= htmlspecialchars($formData['nbr_heure_cumulee'] ?? 0) ?>" min="0">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="taux_realisation" class="form-label">Taux de réalisation (%)</label>
                                <input type="number" class="form-control" id="taux_realisation" name="taux_realisation" 
                                       value="<?= htmlspecialchars($formData['taux_realisation'] ?? 0) ?>" min="0" max="100" step="0.01">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="disposition" class="form-label">Disposition</label>
                                <select class="form-select" id="disposition" name="disposition">
                                    <option value="0" <?= ($formData['disposition'] ?? 0) == 0 ? 'selected' : '' ?>>Non</option>
                                    <option value="1" <?= ($formData['disposition'] ?? 0) == 1 ? 'selected' : '' ?>>Oui</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Objectifs -->
            <div class="tab-pane fade" id="objectifs" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (empty($objectifs)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                <p class="text-muted fst-italic">Aucun objectif pédagogique disponible</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchObjectifs" placeholder="Rechercher un objectif...">
                                </div>
                            </div>
                            <div class="list-group objectifs-list">
                                <?php foreach ($objectifs as $objectif): ?>
                                    <div class="list-group-item objectif-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-bold mb-1"><?= htmlspecialchars($objectif['objectif']) ?></div>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="objectifs[<?= $objectif['id'] ?>]" 
                                                       id="objectif_<?= $objectif['id'] ?>_atteint" value="atteint" 
                                                       <?= isset($formData['objectifs'][$objectif['id']]) && $formData['objectifs'][$objectif['id']] === 'atteint' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-success btn-sm" for="objectif_<?= $objectif['id'] ?>_atteint">Atteint</label>
                                                
                                                <input type="radio" class="btn-check" name="objectifs[<?= $objectif['id'] ?>]" 
                                                       id="objectif_<?= $objectif['id'] ?>_en_cours" value="en_cours" 
                                                       <?= isset($formData['objectifs'][$objectif['id']]) && $formData['objectifs'][$objectif['id']] === 'en_cours' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-warning btn-sm" for="objectif_<?= $objectif['id'] ?>_en_cours">En cours</label>
                                                
                                                <input type="radio" class="btn-check" name="objectifs[<?= $objectif['id'] ?>]" 
                                                       id="objectif_<?= $objectif['id'] ?>_non_atteint" value="non_atteint" 
                                                       <?= isset($formData['objectifs'][$objectif['id']]) && $formData['objectifs'][$objectif['id']] === 'non_atteint' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-danger btn-sm" for="objectif_<?= $objectif['id'] ?>_non_atteint">Non atteint</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Onglet Moyens -->
            <div class="tab-pane fade" id="moyens" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (empty($moyens)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <p class="text-muted fst-italic">Aucun moyen didactique disponible</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchMoyens" placeholder="Rechercher un moyen...">
                                </div>
                            </div>
                            <div class="list-group moyens-list">
                                <?php foreach ($moyens as $moyen): ?>
                                    <div class="list-group-item moyen-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-bold mb-1"><?= htmlspecialchars($moyen['moyen']) ?></div>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="moyens[<?= $moyen['id'] ?>]" 
                                                       id="moyen_<?= $moyen['id'] ?>_utilise" value="utilise" 
                                                       <?= isset($formData['moyens'][$moyen['id']]) && $formData['moyens'][$moyen['id']] === 'utilise' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-success btn-sm" for="moyen_<?= $moyen['id'] ?>_utilise">Utilisé</label>
                                                
                                                <input type="radio" class="btn-check" name="moyens[<?= $moyen['id'] ?>]" 
                                                       id="moyen_<?= $moyen['id'] ?>_non_utilise" value="non_utilise" 
                                                       <?= isset($formData['moyens'][$moyen['id']]) && $formData['moyens'][$moyen['id']] === 'non_utilise' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-danger btn-sm" for="moyen_<?= $moyen['id'] ?>_non_utilise">Non utilisé</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Onglet Stratégies -->
            <div class="tab-pane fade" id="strategies" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (empty($strategies)): ?>
                            <div class="text-center py-4">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted fst-italic">Aucune stratégie d'évaluation disponible</p>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchStrategies" placeholder="Rechercher une stratégie...">
                                </div>
                            </div>
                            <div class="list-group strategies-list">
                                <?php foreach ($strategies as $strategie): ?>
                                    <div class="list-group-item strategie-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-bold mb-1"><?= htmlspecialchars($strategie['strategie']) ?></div>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="strategies[<?= $strategie['id'] ?>]" 
                                                       id="strategie_<?= $strategie['id'] ?>_appliquee" value="appliquee" 
                                                       <?= isset($formData['strategies'][$strategie['id']]) && $formData['strategies'][$strategie['id']] === 'appliquee' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-success btn-sm" for="strategie_<?= $strategie['id'] ?>_appliquee">Appliquée</label>
                                                
                                                <input type="radio" class="btn-check" name="strategies[<?= $strategie['id'] ?>]" 
                                                       id="strategie_<?= $strategie['id'] ?>_non_appliquee" value="non_appliquee" 
                                                       <?= isset($formData['strategies'][$strategie['id']]) && $formData['strategies'][$strategie['id']] === 'non_appliquee' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-danger btn-sm" for="strategie_<?= $strategie['id'] ?>_non_appliquee">Non appliquée</label>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Onglet Commentaires -->
            <div class="tab-pane fade" id="commentaires" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="contenu_seance" class="form-label">Contenu de la séance</label>
                            <textarea class="form-control" id="contenu_seance" name="contenu_seance" rows="5" 
                                    placeholder="Décrivez ici les contenus abordés pendant la séance..."><?= htmlspecialchars($formData['contenu_seance'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="difficultes" class="form-label">Difficultés rencontrées</label>
                                <textarea class="form-control" id="difficultes" name="difficultes" rows="3" 
                                        placeholder="Décrivez ici les difficultés rencontrées..."><?= htmlspecialchars($formData['difficultes'] ?? '') ?></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="solutions" class="form-label">Solutions proposées</label>
                                <textarea class="form-control" id="solutions" name="solutions" rows="3" 
                                        placeholder="Décrivez ici les solutions proposées..."><?= htmlspecialchars($formData['solutions'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire général</label>
                            <textarea class="form-control" id="commentaire" name="commentaire" rows="3" 
                                    placeholder="Ajoutez ici vos commentaires généraux..."><?= htmlspecialchars($formData['commentaire'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons de soumission -->
        <div class="d-flex justify-content-end mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

<!-- Script pour la recherche et le filtrage -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction de recherche générique
    function setupSearch(inputId, listClass, itemClass) {
        const searchInput = document.getElementById(inputId);
        if (!searchInput) return;

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const items = document.querySelectorAll(`.${itemClass}`);

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }

    // Initialiser la recherche pour chaque section
    setupSearch('searchObjectifs', 'objectifs-list', 'objectif-item');
    setupSearch('searchMoyens', 'moyens-list', 'moyen-item');
    setupSearch('searchStrategies', 'strategies-list', 'strategie-item');

    // Validation du formulaire
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    });
});
</script>
