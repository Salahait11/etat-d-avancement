<?php
// src/View/etat_avancement/edit.php
// Vue pour le formulaire d'édition d'un état d'avancement
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
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
            <?= htmlspecialchars($errors['general']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/etats-avancement/update/<?= $etat['id'] ?>" method="POST" class="needs-validation" novalidate>
        <!-- Navigation par onglets -->
        <ul class="nav nav-tabs mb-4" id="etatAvancementTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="infos-tab" data-bs-target="#infos" type="button" role="tab" aria-controls="infos" aria-selected="true">
                    <i class="fas fa-info-circle"></i> Informations générales
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="objectifs-tab" data-bs-target="#objectifs" type="button" role="tab" aria-controls="objectifs" aria-selected="false">
                    <i class="fas fa-bullseye"></i> Objectifs pédagogiques
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contenus-tab" data-bs-target="#contenus" type="button" role="tab" aria-controls="contenus" aria-selected="false">
                    <i class="fas fa-list-alt"></i> Contenus de séance
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="moyens-tab" data-bs-target="#moyens" type="button" role="tab" aria-controls="moyens" aria-selected="false">
                    <i class="fas fa-tools"></i> Moyens didactiques
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="strategies-tab" data-bs-target="#strategies" type="button" role="tab" aria-controls="strategies" aria-selected="false">
                    <i class="fas fa-chart-line"></i> Stratégies d'évaluation
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="commentaires-tab" data-bs-target="#commentaires" type="button" role="tab" aria-controls="commentaires" aria-selected="false">
                    <i class="fas fa-comment"></i> Commentaires
                </button>
            </li>
        </ul>
        
        <!-- Contenu des onglets -->
        <div class="tab-content" id="etatAvancementTabContent">
            <!-- Onglet 1: Informations générales -->
            <div class="tab-pane fade show active" id="infos" role="tabpanel" aria-labelledby="infos-tab">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row">
                            <!-- Module -->
                            <div class="col-md-6 mb-3">
                                <label for="id_module" class="form-label">Module <span class="text-danger">*</span></label>
                                <select class="form-select select2 <?= isset($errors['id_module']) ? 'is-invalid' : '' ?>" 
                                        id="id_module" name="id_module" required>
                                    <option value="">Sélectionnez un module</option>
                                    <?php foreach ($modules as $module): ?>
                                        <option value="<?= $module['id'] ?>" <?= $formData['id_module'] == $module['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($module['intitule']) ?> 
                                            (<?= htmlspecialchars($module['filiere_nom'] ?? 'Aucune filière') ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_module'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['id_module']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Formateur -->
                            <div class="col-md-6 mb-3">
                                <label for="id_formateur" class="form-label">Formateur <span class="text-danger">*</span></label>
                                <select class="form-select select2 <?= isset($errors['id_formateur']) ? 'is-invalid' : '' ?>" 
                                        id="id_formateur" name="id_formateur" required>
                                    <option value="">Sélectionnez un formateur</option>
                                    <?php foreach ($formateurs as $formateur): ?>
                                        <option value="<?= $formateur['id'] ?>" <?= $formData['id_formateur'] == $formateur['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($formateur['prenom'] . ' ' . $formateur['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['id_formateur'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['id_formateur']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Date de séance -->
                            <div class="col-md-6 mb-3">
                                <label for="date_seance" class="form-label">Date de séance <span class="text-danger">*</span></label>
                                <input type="text" class="form-control datepicker <?= isset($errors['date_seance']) ? 'is-invalid' : '' ?>" 
                                       id="date_seance" name="date_seance" 
                                       value="<?= htmlspecialchars($formData['date_seance']) ?>" required>
                                <?php if (isset($errors['date_seance'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['date_seance']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Durée réalisée -->
                            <div class="col-md-6 mb-3">
                                <label for="duree_realisee" class="form-label">Durée réalisée (heures) <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" min="0.5" class="form-control <?= isset($errors['duree_realisee']) ? 'is-invalid' : '' ?>" 
                                       id="duree_realisee" name="duree_realisee" value="<?= htmlspecialchars($formData['duree_realisee']) ?>" required>
                                <?php if (isset($errors['duree_realisee'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['duree_realisee']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Objectifs pédagogiques -->
            <div class="tab-pane fade" id="objectifs" role="tabpanel" aria-labelledby="objectifs-tab">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Objectifs pédagogiques <span class="text-white">*</span></h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors['objectifs'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($errors['objectifs']) ?></div>
                        <?php endif; ?>
                        
                        <?php if (empty($objectifs)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i> Aucun objectif pédagogique disponible. 
                                <a href="<?= BASE_URL ?>/objectifs-pedagogiques/add" class="alert-link">Ajouter un objectif</a>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="form-text mb-3">Sélectionnez le statut de chaque objectif pédagogique</div>
                                
                                <!-- Barre de recherche pour filtrer les objectifs -->
                                <div class="input-group mb-3">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" class="form-control" id="searchObjectifs" placeholder="Rechercher un objectif..." aria-label="Rechercher">
                                </div>
                                
                                <div class="list-group" id="objectifsList">
                                    <?php foreach ($objectifs as $objectif): ?>
                                        <div class="list-group-item reference-item">
                                            <div class="fw-bold mb-2"><?= htmlspecialchars($objectif['objectif']) ?></div>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" 
                                                           name="objectifs[<?= $objectif['id'] ?>]" 
                                                           id="objectif_<?= $objectif['id'] ?>_atteint" 
                                                           value="atteint" 
                                                           <?= (isset($formData['objectifs'][$objectif['id']]) && $formData['objectifs'][$objectif['id']] === 'atteint') ? 'checked' : '' ?>>
                                                    <label class="form-check-label status-atteint" for="objectif_<?= $objectif['id'] ?>_atteint">
                                                        <i class="fas fa-check-circle me-1"></i> Atteint
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" 
                                                           name="objectifs[<?= $objectif['id'] ?>]" 
                                                           id="objectif_<?= $objectif['id'] ?>_en_cours" 
                                                           value="en_cours" 
                                                           <?= (isset($formData['objectifs'][$objectif['id']]) && $formData['objectifs'][$objectif['id']] === 'en_cours') ? 'checked' : '' ?>>
                                                    <label class="form-check-label status-en-cours" for="objectif_<?= $objectif['id'] ?>_en_cours">
                                                        <i class="fas fa-spinner me-1"></i> En cours
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" 
                                                           name="objectifs[<?= $objectif['id'] ?>]" 
                                                           id="objectif_<?= $objectif['id'] ?>_non_atteint" 
                                                           value="non_atteint" 
                                                           <?= (isset($formData['objectifs'][$objectif['id']]) && $formData['objectifs'][$objectif['id']] === 'non_atteint') ? 'checked' : '' ?>>
                                                    <label class="form-check-label status-non-atteint" for="objectif_<?= $objectif['id'] ?>_non_atteint">
                                                        <i class="fas fa-times-circle me-1"></i> Non atteint
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Contenus de séance -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Contenus de séance <span class="text-white">*</span></h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($errors['contenus'])): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($errors['contenus']) ?></div>
                        <?php endif; ?>
                        
                        <?php if (empty($contenus)): ?>
                            <div class="alert alert-warning">
                                Aucun contenu de séance disponible. 
                                <a href="<?= BASE_URL ?>/contenus-seance/add" class="alert-link">Ajouter un contenu</a>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="form-text mb-2">Sélectionnez le statut de chaque contenu de séance</div>
                                <div class="list-group">
                                    <?php foreach ($contenus as $contenu): ?>
                                        <div class="list-group-item">
                                            <div class="fw-bold mb-1"><?= htmlspecialchars($contenu['contenu']) ?></div>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="contenu[<?= $contenu['id'] ?>]" 
                                                       id="contenu_<?= $contenu['id'] ?>_realise" value="realise" 
                                                       <?= isset($formData['contenus'][$contenu['id']]) && $formData['contenus'][$contenu['id']] === 'realise' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-success btn-sm" for="contenu_<?= $contenu['id'] ?>_realise">Réalisé</label>
                                                
                                                <input type="radio" class="btn-check" name="contenu[<?= $contenu['id'] ?>]" 
                                                       id="contenu_<?= $contenu['id'] ?>_partiel" value="partiel" 
                                                       <?= isset($formData['contenus'][$contenu['id']]) && $formData['contenus'][$contenu['id']] === 'partiel' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-warning btn-sm" for="contenu_<?= $contenu['id'] ?>_partiel">Partiellement</label>
                                                
                                                <input type="radio" class="btn-check" name="contenu[<?= $contenu['id'] ?>]" 
                                                       id="contenu_<?= $contenu['id'] ?>_non_realise" value="non_realise" 
                                                       <?= isset($formData['contenus'][$contenu['id']]) && $formData['contenus'][$contenu['id']] === 'non_realise' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-danger btn-sm" for="contenu_<?= $contenu['id'] ?>_non_realise">Non réalisé</label>
                                                
                                                <input type="radio" class="btn-check" name="contenu[<?= $contenu['id'] ?>]" 
                                                       id="contenu_<?= $contenu['id'] ?>_none" value="" 
                                                       <?= !isset($formData['contenus'][$contenu['id']]) || $formData['contenus'][$contenu['id']] === '' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-secondary btn-sm" for="contenu_<?= $contenu['id'] ?>_none">N/A</label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Moyens didactiques -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Moyens didactiques</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($moyens)): ?>
                            <div class="alert alert-warning">
                                Aucun moyen didactique disponible. 
                                <a href="<?= BASE_URL ?>/moyens-didactiques/add" class="alert-link">Ajouter un moyen</a>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="form-text mb-2">Sélectionnez les moyens didactiques utilisés</div>
                                <div class="list-group">
                                    <?php foreach ($moyens as $moyen): ?>
                                        <div class="list-group-item">
                                            <div class="fw-bold mb-1"><?= htmlspecialchars($moyen['moyen']) ?></div>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="moyen[<?= $moyen['id'] ?>]" 
                                                       id="moyen_<?= $moyen['id'] ?>_utilise" value="utilise" 
                                                       <?= isset($formData['moyens'][$moyen['id']]) && $formData['moyens'][$moyen['id']] === 'utilise' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-success btn-sm" for="moyen_<?= $moyen['id'] ?>_utilise">Utilisé</label>
                                                
                                                <input type="radio" class="btn-check" name="moyen[<?= $moyen['id'] ?>]" 
                                                       id="moyen_<?= $moyen['id'] ?>_non_utilise" value="non_utilise" 
                                                       <?= isset($formData['moyens'][$moyen['id']]) && $formData['moyens'][$moyen['id']] === 'non_utilise' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-danger btn-sm" for="moyen_<?= $moyen['id'] ?>_non_utilise">Non utilisé</label>
                                                
                                                <input type="radio" class="btn-check" name="moyen[<?= $moyen['id'] ?>]" 
                                                       id="moyen_<?= $moyen['id'] ?>_none" value="" 
                                                       <?= !isset($formData['moyens'][$moyen['id']]) || $formData['moyens'][$moyen['id']] === '' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-secondary btn-sm" for="moyen_<?= $moyen['id'] ?>_none">N/A</label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Stratégies d'évaluation -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">Stratégies d'évaluation</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($strategies)): ?>
                            <div class="alert alert-warning">
                                Aucune stratégie d'évaluation disponible. 
                                <a href="<?= BASE_URL ?>/strategies-evaluation/add" class="alert-link">Ajouter une stratégie</a>
                            </div>
                        <?php else: ?>
                            <div class="mb-3">
                                <div class="form-text mb-2">Sélectionnez les stratégies d'évaluation appliquées</div>
                                <div class="list-group">
                                    <?php foreach ($strategies as $strategie): ?>
                                        <div class="list-group-item">
                                            <div class="fw-bold mb-1"><?= htmlspecialchars($strategie['strategie']) ?></div>
                                            <div class="btn-group" role="group">
                                                <input type="radio" class="btn-check" name="strategie[<?= $strategie['id'] ?>]" 
                                                       id="strategie_<?= $strategie['id'] ?>_appliquee" value="appliquee" 
                                                       <?= isset($formData['strategies'][$strategie['id']]) && $formData['strategies'][$strategie['id']] === 'appliquee' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-success btn-sm" for="strategie_<?= $strategie['id'] ?>_appliquee">Appliquée</label>
                                                
                                                <input type="radio" class="btn-check" name="strategie[<?= $strategie['id'] ?>]" 
                                                       id="strategie_<?= $strategie['id'] ?>_non_appliquee" value="non_appliquee" 
                                                       <?= isset($formData['strategies'][$strategie['id']]) && $formData['strategies'][$strategie['id']] === 'non_appliquee' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-danger btn-sm" for="strategie_<?= $strategie['id'] ?>_non_appliquee">Non appliquée</label>
                                                
                                                <input type="radio" class="btn-check" name="strategie[<?= $strategie['id'] ?>]" 
                                                       id="strategie_<?= $strategie['id'] ?>_none" value="" 
                                                       <?= !isset($formData['strategies'][$strategie['id']]) || $formData['strategies'][$strategie['id']] === '' ? 'checked' : '' ?>>
                                                <label class="btn btn-outline-secondary btn-sm" for="strategie_<?= $strategie['id'] ?>_none">N/A</label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Onglet 6: Commentaires et difficultés -->
            <div class="tab-pane fade" id="commentaires" role="tabpanel" aria-labelledby="commentaires-tab">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Commentaires et difficultés</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire général</label>
                            <textarea class="form-control <?= isset($errors['commentaire']) ? 'is-invalid' : '' ?>" 
                                      id="commentaire" name="commentaire" rows="4"><?= htmlspecialchars($formData['commentaire'] ?? '') ?></textarea>
                            <?php if (isset($errors['commentaire'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['commentaire']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">Commentaires généraux sur le déroulement de la séance</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="difficultes" class="form-label">Difficultés rencontrées</label>
                            <textarea class="form-control <?= isset($errors['difficultes']) ? 'is-invalid' : '' ?>" 
                                      id="difficultes" name="difficultes" rows="4"><?= htmlspecialchars($formData['difficultes'] ?? '') ?></textarea>
                            <?php if (isset($errors['difficultes'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['difficultes']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">Difficultés rencontrées pendant la séance</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="solutions" class="form-label">Solutions proposées</label>
                            <textarea class="form-control <?= isset($errors['solutions']) ? 'is-invalid' : '' ?>" 
                                      id="solutions" name="solutions" rows="4"><?= htmlspecialchars($formData['solutions'] ?? '') ?></textarea>
                            <?php if (isset($errors['solutions'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['solutions']) ?></div>
                            <?php endif; ?>
                            <div class="form-text">Solutions proposées pour résoudre les difficultés</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Boutons de navigation -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-outline-secondary btn-prev-tab" id="prevTab">
                <i class="fas fa-arrow-left"></i> Précédent
            </button>
            
            <button type="submit" class="btn btn-success btn-submit">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            
            <button type="button" class="btn btn-outline-primary btn-next-tab" id="nextTab">
                Suivant <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<script>
// Le code JavaScript pour la navigation entre onglets et la validation a été déplacé dans app.js
document.addEventListener('DOMContentLoaded', function() {
    'use strict';
    
    // Afficher le bouton d'enregistrement uniquement sur le dernier onglet
    const submitButton = document.querySelector('.btn-submit');
    const tabContents = document.querySelectorAll('.tab-pane');
    
    // Cacher le bouton d'enregistrement sur tous les onglets sauf le dernier
    if (submitButton) {
        submitButton.style.display = 'none';
        
        // Observer les changements d'onglets pour afficher/masquer le bouton d'enregistrement
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const lastTabContent = tabContents[tabContents.length - 1];
                    if (lastTabContent && lastTabContent.classList.contains('active')) {
                        submitButton.style.display = 'block';
                    } else {
                        submitButton.style.display = 'none';
                    }
                }
            });
        });
        
        // Observer chaque onglet pour les changements de classe
        tabContents.forEach(function(tabContent) {
            observer.observe(tabContent, { attributes: true });
        });
    }
    
    // Validation du formulaire
    const form = document.querySelector('.needs-validation');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Trouver le premier champ invalide et activer son onglet
                const invalidFields = form.querySelectorAll(':invalid');
                if (invalidFields.length > 0) {
                    const invalidField = invalidFields[0];
                    const tabPane = invalidField.closest('.tab-pane');
                    if (tabPane) {
                        // Afficher une alerte dans l'onglet contenant le champ invalide
                        let alertDiv = tabPane.querySelector('.alert-validation');
                        if (!alertDiv) {
                            alertDiv = document.createElement('div');
                            alertDiv.className = 'alert alert-danger alert-validation mt-3';
                            alertDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> Veuillez remplir tous les champs obligatoires dans cette section.`;
                            tabPane.insertBefore(alertDiv, tabPane.firstChild);
                        }
                        
                        // Activer l'onglet contenant le champ invalide
                        const tabs = document.querySelectorAll('#etatAvancementTab button');
                        const tabId = tabPane.id;
                        const tabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
                        if (tabButton) {
                            // Désactiver tous les onglets
                            tabs.forEach(tab => {
                                tab.classList.remove('active');
                                tab.setAttribute('aria-selected', 'false');
                            });
                            
                            // Cacher tous les contenus d'onglets
                            tabContents.forEach(content => {
                                content.classList.remove('show', 'active');
                            });
                            
                            // Activer l'onglet contenant le champ invalide
                            tabButton.classList.add('active');
                            tabButton.setAttribute('aria-selected', 'true');
                            tabPane.classList.add('show', 'active');
                            
                            // Focus sur le champ invalide
                            invalidField.focus();
                        }
                    }
                }
            }
        
        form.classList.add('was-validated');
    }, false);
    
    // Filtrage des listes
    function setupFilter(inputId, listId) {
        const input = document.getElementById(inputId);
        const list = document.getElementById(listId);
        
        if (input && list) {
            input.addEventListener('input', function() {
                const filter = this.value.toLowerCase();
                const items = list.querySelectorAll('.list-group-item');
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(filter)) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
    }
    
    // Initialiser les filtres pour chaque liste
    setupFilter('searchObjectifs', 'objectifsList');
    setupFilter('searchContenus', 'contenusList');
    setupFilter('searchMoyens', 'moyensList');
    setupFilter('searchStrategies', 'strategiesList');
});
</script>
