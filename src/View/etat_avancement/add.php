<?php
// src/View/etat_avancement/add.php
// Vue pour le formulaire d'ajout d'un état d'avancement
$title = 'Ajouter un État d\'Avancement';
?>

<!-- Ajout des dépendances Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Styles spécifiques à la page d'ajout -->
<style>
    /* Styles isolés pour éviter les conflits */
    .etat-avancement-form .tab-pane {
        transition: opacity 0.3s ease-in-out;
    }
    .etat-avancement-form .nav-link.completed {
        color: #198754;
    }
    .etat-avancement-form .nav-link.completed::before {
        content: '✓';
        margin-right: 5px;
    }
    .etat-avancement-form .alert-validation {
        animation: fadeIn 0.3s ease-in-out;
    }
    .etat-avancement-form .nav-link.disabled {
        pointer-events: none;
        opacity: 0.6;
    }
    .etat-avancement-form .nav-link.active {
        font-weight: bold;
    }
    /* Styles pour les boutons de navigation */
    .etat-avancement-form .btn {
        min-width: 120px;
        margin: 0 5px;
    }
    .etat-avancement-form .btn-navigation {
        display: none;
    }
    .etat-avancement-form .btn-navigation.active {
        display: inline-block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<!-- Formulaire d'ajout d'état d'avancement -->
<div class="container mt-4">
    <h2 class="mb-4">Ajouter un État d'Avancement</h2>
    
    <form id="etatAvancementForm" class="etat-avancement-form" method="POST" action="<?= BASE_URL ?>/etats-avancement/store">
        <!-- Onglets de navigation -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="informations-tab" data-bs-toggle="tab" data-bs-target="#informations" type="button" role="tab" aria-controls="informations" aria-selected="true">Informations</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="objectifs-tab" data-bs-toggle="tab" data-bs-target="#objectifs" type="button" role="tab" aria-controls="objectifs" aria-selected="false">Objectifs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contenus-tab" data-bs-toggle="tab" data-bs-target="#contenus" type="button" role="tab" aria-controls="contenus" aria-selected="false">Contenus</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="moyens-tab" data-bs-toggle="tab" data-bs-target="#moyens" type="button" role="tab" aria-controls="moyens" aria-selected="false">Moyens</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="strategies-tab" data-bs-toggle="tab" data-bs-target="#strategies" type="button" role="tab" aria-controls="strategies" aria-selected="false">Stratégies</button>
            </li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content">
            <!-- Onglet Informations -->
            <div class="tab-pane fade show active" id="informations" role="tabpanel">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="heure" class="form-label">Heure</label>
                        <input type="time" class="form-control" id="heure" name="heure" required value="<?= date('H:i') ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="id_module" class="form-label">Module</label>
                        <select class="form-select" id="id_module" name="id_module" required>
                            <option value="">Sélectionner un module</option>
                            <?php foreach ($modules as $module): ?>
                                <option value="<?= $module['id'] ?>"><?= htmlspecialchars($module['intitule']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="id_formateur" class="form-label">Formateur</label>
                        <select class="form-select" id="id_formateur" name="id_formateur" required>
                            <option value="">Sélectionner un formateur</option>
                            <?php foreach ($formateurs as $formateur): ?>
                                <option value="<?= $formateur['id'] ?>" <?= ($formateur['id'] == ($currentUser['id'] ?? '')) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($formateur['nom'] . ' ' . $formateur['prenom']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nbr_heure" class="form-label">Nombre d'heures</label>
                        <input type="number" class="form-control" id="nbr_heure" name="nbr_heure" required min="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="nbr_heure_cumulee" class="form-label">Heures cumulées</label>
                        <input type="number" class="form-control" id="nbr_heure_cumulee" name="nbr_heure_cumulee" min="0" value="0">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="taux_realisation" class="form-label">Taux de réalisation (%)</label>
                        <input type="number" class="form-control" id="taux_realisation" name="taux_realisation" min="0" max="100" step="0.01" value="0.00">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="disposition" class="form-label">Disposition</label>
                    <select class="form-select" id="disposition" name="disposition">
                        <option value="0">Non</option>
                        <option value="1">Oui</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="commentaire" class="form-label">Commentaire général</label>
                    <textarea class="form-control" id="commentaire" name="commentaire" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="difficultes" class="form-label">Difficultés rencontrées</label>
                    <textarea class="form-control" id="difficultes" name="difficultes" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label for="solutions" class="form-label">Solutions proposées</label>
                    <textarea class="form-control" id="solutions" name="solutions" rows="3"></textarea>
                </div>
            </div>

            <!-- Onglet Objectifs -->
            <div class="tab-pane fade" id="objectifs" role="tabpanel">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchObjectifs" placeholder="Rechercher un objectif...">
                </div>
                <div class="list-group">
                    <?php foreach ($objectifs as $objectif): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($objectif['objectif']) ?></h6>
                                </div>
                                <select class="form-select form-select-sm w-auto" name="objectifs[<?= $objectif['id'] ?>]">
                                    <option value="">Non réalisé</option>
                                    <option value="partiel">Partiellement réalisé</option>
                                    <option value="realise">Réalisé</option>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Onglet Contenus -->
            <div class="tab-pane fade" id="contenus" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i> Contenus de séance</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="contenu_seance" class="form-label">Description des contenus de la séance</label>
                            <textarea class="form-control" id="contenu_seance" name="contenu_seance" rows="5" 
                                    placeholder="Décrivez ici les contenus abordés pendant la séance..."><?= htmlspecialchars($formData['contenu_seance'] ?? '') ?></textarea>
                            <div class="form-text">
                                Indiquez les contenus abordés et leur statut (réalisé, partiellement réalisé, non réalisé)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Onglet Moyens -->
            <div class="tab-pane fade" id="moyens" role="tabpanel">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchMoyens" placeholder="Rechercher un moyen...">
                </div>
                <div class="list-group">
                    <?php foreach ($moyens as $moyen): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($moyen['moyen']) ?></h6>
                                </div>
                                <select class="form-select form-select-sm w-auto" name="moyens[<?= $moyen['id'] ?>]">
                                    <option value="">Non utilisé</option>
                                    <option value="partiel">Partiellement utilisé</option>
                                    <option value="utilise">Utilisé</option>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Onglet Stratégies -->
            <div class="tab-pane fade" id="strategies" role="tabpanel">
                <div class="mb-3">
                    <input type="text" class="form-control" id="searchStrategies" placeholder="Rechercher une stratégie...">
                </div>
                <div class="list-group">
                    <?php foreach ($strategies as $strategie): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($strategie['strategie']) ?></h6>
                                </div>
                                <select class="form-select form-select-sm w-auto" name="strategies[<?= $strategie['id'] ?>]">
                                    <option value="">Non utilisée</option>
                                    <option value="partiel">Partiellement utilisée</option>
                                    <option value="utilise">Utilisée</option>
                                </select>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Boutons de navigation -->
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary btn-navigation" id="prevBtn">Précédent</button>
            <div>
                <button type="button" class="btn btn-primary btn-navigation" id="nextBtn">Suivant</button>
                <button type="submit" class="btn btn-success btn-navigation" id="submitBtn">Enregistrer</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal pour ajouter un contenu -->
<div class="modal fade" id="addContenuModal" tabindex="-1" aria-labelledby="addContenuModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addContenuModalLabel">Ajouter un contenu de séance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addContenuForm">
                    <div class="mb-3">
                        <label for="newContenu" class="form-label">Description du contenu</label>
                        <textarea class="form-control" id="newContenu" name="contenu" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="saveContenu()">Enregistrer</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration des onglets
    const tabs = {
        informations: 0,
        objectifs: 1,
        contenus: 2,
        moyens: 3,
        strategies: 4
    };

    // Éléments du DOM
    const form = document.getElementById('etatAvancementForm');
    const tabButtons = document.querySelectorAll('.nav-link');
    const tabContents = document.querySelectorAll('.tab-pane');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    let currentTab = 'informations';

    // Fonction pour mettre à jour l'interface
    function updateInterface() {
        // Mettre à jour les onglets
        tabButtons.forEach(button => {
            const tabId = button.getAttribute('aria-controls');
            button.classList.remove('active');
            button.setAttribute('aria-selected', 'false');
            if (tabId === currentTab) {
                button.classList.add('active');
                button.setAttribute('aria-selected', 'true');
            }
        });

        // Mettre à jour le contenu des onglets
        tabContents.forEach(content => {
            content.classList.remove('show', 'active');
            if (content.id === currentTab) {
                content.classList.add('show', 'active');
            }
        });

        // Gérer les boutons de navigation
        const isFirstTab = currentTab === 'informations';
        const isLastTab = currentTab === 'strategies';

        // Bouton Précédent
        if (isFirstTab) {
            prevBtn.classList.remove('active');
        } else {
            prevBtn.classList.add('active');
        }

        // Boutons Suivant et Enregistrer
        if (isLastTab) {
            nextBtn.classList.remove('active');
            submitBtn.classList.add('active');
        } else {
            nextBtn.classList.add('active');
            submitBtn.classList.remove('active');
        }
    }

    // Gestionnaires d'événements
    prevBtn.addEventListener('click', () => {
        const tabOrder = Object.keys(tabs);
        const currentIndex = tabOrder.indexOf(currentTab);
        if (currentIndex > 0) {
            currentTab = tabOrder[currentIndex - 1];
            updateInterface();
        }
    });

    nextBtn.addEventListener('click', () => {
        const tabOrder = Object.keys(tabs);
        const currentIndex = tabOrder.indexOf(currentTab);
        if (currentIndex < tabOrder.length - 1) {
            currentTab = tabOrder[currentIndex + 1];
            updateInterface();
        }
    });

    // Gestionnaire pour les onglets
    tabButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            currentTab = button.getAttribute('aria-controls');
            updateInterface();
        });
    });

    // Validation du formulaire
    form.addEventListener('submit', (e) => {
        e.preventDefault();
        // Vérifier que nous sommes sur le dernier onglet
        if (currentTab !== 'strategies') {
            alert('Veuillez compléter tous les onglets avant de soumettre le formulaire.');
            return;
        }
        // Soumettre le formulaire
        form.submit();
    });

    // Initialisation
    updateInterface();
});

// Fonction pour sauvegarder un nouveau contenu
function saveContenu() {
    const contenu = document.getElementById('newContenu').value.trim();
    if (!contenu) {
        alert('Veuillez saisir une description du contenu');
        return;
    }

    fetch('<?= BASE_URL ?>/contenus-seance/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ contenu: contenu })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Ajouter le nouveau contenu à la liste
            const contenuList = document.querySelector('#contenus .list-group');
            const newContenu = createContenuElement(data.contenu);
            contenuList.appendChild(newContenu);
            
            // Fermer la modal et réinitialiser le formulaire
            const modal = bootstrap.Modal.getInstance(document.getElementById('addContenuModal'));
            modal.hide();
            document.getElementById('newContenu').value = '';
            
            // Afficher un message de succès
            alert('Contenu ajouté avec succès');
        } else {
            alert(data.message || 'Erreur lors de l\'ajout du contenu');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de l\'ajout du contenu');
    });
}

// Fonction pour créer un élément de contenu
function createContenuElement(contenu) {
    const div = document.createElement('div');
    div.className = 'list-group-item';
    div.setAttribute('data-contenu-id', contenu.id);
    div.innerHTML = `
        <div class="d-flex justify-content-between align-items-start">
            <div class="fw-bold mb-1">${contenu.contenu}</div>
            <button type="button" class="btn btn-sm btn-outline-danger" 
                    onclick="deleteContenu(${contenu.id})">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="btn-group" role="group">
            <input type="radio" class="btn-check" name="contenus[${contenu.id}]" 
                   id="contenu_${contenu.id}_realise" value="realise">
            <label class="btn btn-outline-success btn-sm" for="contenu_${contenu.id}_realise">Réalisé</label>
            
            <input type="radio" class="btn-check" name="contenus[${contenu.id}]" 
                   id="contenu_${contenu.id}_partiel" value="partiel">
            <label class="btn btn-outline-warning btn-sm" for="contenu_${contenu.id}_partiel">Partiellement</label>
            
            <input type="radio" class="btn-check" name="contenus[${contenu.id}]" 
                   id="contenu_${contenu.id}_non_realise" value="non_realise">
            <label class="btn btn-outline-danger btn-sm" for="contenu_${contenu.id}_non_realise">Non réalisé</label>
        </div>
    `;
    return div;
}

// Fonction pour supprimer un contenu
function deleteContenu(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce contenu ?')) {
        return;
    }

    fetch('<?= BASE_URL ?>/contenus-seance/delete/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Supprimer l'élément de la liste
            const element = document.querySelector(`[data-contenu-id="${id}"]`);
            if (element) {
                element.remove();
            }
            alert('Contenu supprimé avec succès');
        } else {
            alert(data.message || 'Erreur lors de la suppression du contenu');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression du contenu');
    });
}

// Configuration des filtres de recherche
document.addEventListener('DOMContentLoaded', function() {
    setupSearch('searchContenus', '#contenus .list-group');
});

function setupSearch(inputId, listGroupSelector) {
    const searchInput = document.getElementById(inputId);
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const items = document.querySelectorAll(`${listGroupSelector} .list-group-item`);
        
        items.forEach(item => {
            const text = item.querySelector('.fw-bold').textContent.toLowerCase();
            if (text.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
}
</script>
