<?php
// src/View/etat_avancement/list.php
// Vue pour afficher la liste des états d'avancement
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tasks me-2"></i> <?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/etats-avancement/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un état d'avancement
        </a>
    </div>

    <?php if (isset($flashMessages) && !empty($flashMessages)): ?>
        <?php foreach ($flashMessages as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Filtres et recherche -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filtres</h5>
        </div>
        <div class="card-body">
            <form method="get" action="<?= BASE_URL ?>/etats-avancement">
                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-4">
                        <label for="dateFilter" class="form-label">Date de séance</label>
                        <input type="date" id="dateFilter" name="date_seance" value="<?= htmlspecialchars(
                            $filters['date_seance'] ?? ''
                        ) ?>" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="moduleFilter" class="form-label">Module</label>
                        <select id="moduleFilter" name="module_id" class="form-select">
                            <option value="">Tous les modules</option>
                            <?php foreach ($modules as $mod): ?>
                                <option value="<?= $mod['id'] ?>" <?=
                                    ($filters['module_id'] == $mod['id'] ? 'selected' : '')
                                ?>><?= htmlspecialchars($mod['module_intitule'] ?? $mod['intitule']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <button type="submit" class="btn btn-primary">Appliquer</button>
                        <a href="<?= BASE_URL ?>/etats-avancement" class="btn btn-secondary">Réinitialiser</a>
                    </div>
                </div>
            </form>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchTable" class="form-control" placeholder="Rechercher..." aria-label="Rechercher">
                    </div>
                </div>
                <div class="col-md-8 text-md-end">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-secondary" id="btnToday">Aujourd'hui</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnWeek">Cette semaine</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnMonth">Ce mois</button>
                        <button type="button" class="btn btn-outline-secondary" id="btnAll">Tous</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des états d'avancement -->
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i> Liste des états d'avancement</h5>
            <span class="badge bg-primary" id="countResults"><?= count($etatsAvancement) ?> résultat(s)</span>
        </div>
        <div class="card-body">
            <?php if (empty($etatsAvancement)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> Aucun état d'avancement trouvé.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tableEtatsAvancement">
                        <thead class="table-light">
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i> ID</th>
                                <th><i class="fas fa-calendar-alt me-1"></i> Date</th>
                                <th><i class="fas fa-book me-1"></i> Module</th>
                                <th><i class="fas fa-user me-1"></i> Formateur</th>
                                <th><i class="fas fa-clock me-1"></i> Durée (h)</th>
                                <th><i class="fas fa-cogs me-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($etatsAvancement as $etat): ?>
                                <tr data-date="<?= $etat['date'] ?>">
                                    <td><?= htmlspecialchars($etat['id']) ?></td>
                                    <td class="text-nowrap"><?= (new DateTime($etat['date']))->format('d/m/Y') ?></td>
                                    <td><?= htmlspecialchars($etat['module_intitule']) ?></td>
                                    <td><?= htmlspecialchars($etat['formateur_nom']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($etat['nbr_heure']) ?></td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>/etats-avancement/view/<?= $etat['id'] ?>" class="btn btn-sm btn-outline-info" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($isAdmin || $etat['id_formateur'] == $currentUser['id']): ?>
                                                <a href="<?= BASE_URL ?>/etats-avancement/edit/<?= $etat['id'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-delete" title="Supprimer" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal" 
                                                        data-id="<?= $etat['id'] ?>" 
                                                        data-item-name="l'état d'avancement du <?= (new DateTime($etat['date']))->format('d/m/Y') ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
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

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-confirm">
        <div class="modal-content">
            <div class="modal-header">
                <div class="icon-box">
                    <i class="fas fa-times"></i>
                </div>
                <h4 class="modal-title" id="deleteModalLabel">Confirmation de suppression</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Voulez-vous vraiment supprimer cet état d'avancement ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" action="" method="POST">
                    <button type="submit" class="btn btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la recherche dans le tableau
    const searchInput = document.getElementById('searchTable');
    const table = document.getElementById('tableEtatsAvancement');
    const rows = table ? table.querySelectorAll('tbody tr') : [];
    const countResults = document.getElementById('countResults');
    
    // Fonction pour filtrer le tableau
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        let visibleCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });
        
        // Mettre à jour le compteur de résultats
        if (countResults) {
            countResults.textContent = `${visibleCount} résultat(s)`;
        }
    }
    
    // Écouteur d'événement pour la recherche
    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }
    
    // Filtres de date
    const btnToday = document.getElementById('btnToday');
    const btnWeek = document.getElementById('btnWeek');
    const btnMonth = document.getElementById('btnMonth');
    const btnAll = document.getElementById('btnAll');
    
    function filterByDate(filterFn) {
        let visibleCount = 0;
        
        rows.forEach(row => {
            const dateStr = row.getAttribute('data-date');
            const isVisible = filterFn(dateStr);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });
        
        if (countResults) {
            countResults.textContent = `${visibleCount} résultat(s)`;
        }
    }
    
    if (btnToday) {
        btnToday.addEventListener('click', function() {
            const today = new Date().toISOString().split('T')[0];
            filterByDate(dateStr => dateStr === today);
        });
    }
    
    if (btnWeek) {
        btnWeek.addEventListener('click', function() {
            const today = new Date();
            const firstDay = new Date(today.setDate(today.getDate() - today.getDay()));
            const lastDay = new Date(today.setDate(today.getDate() - today.getDay() + 6));
            
            filterByDate(dateStr => {
                const date = new Date(dateStr);
                return date >= firstDay && date <= lastDay;
            });
        });
    }
    
    if (btnMonth) {
        btnMonth.addEventListener('click', function() {
            const today = new Date();
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            
            filterByDate(dateStr => {
                const date = new Date(dateStr);
                return date >= firstDay && date <= lastDay;
            });
        });
    }
    
    if (btnAll) {
        btnAll.addEventListener('click', function() {
            rows.forEach(row => row.style.display = '');
            if (countResults) {
                countResults.textContent = `${rows.length} résultat(s)`;
            }
        });
    }
    
    // Gestion de la modale de suppression
    const deleteButtons = document.querySelectorAll('.btn-delete');
    const deleteForm = document.getElementById('deleteForm');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const itemName = this.getAttribute('data-item-name');
            
            // Mettre à jour le texte de la modale
            const modalBody = document.querySelector('#deleteModal .modal-body p');
            if (modalBody) {
                modalBody.textContent = `Voulez-vous vraiment supprimer ${itemName} ? Cette action est irréversible.`;
            }
            
            // Mettre à jour l'action du formulaire
            if (deleteForm) {
                deleteForm.action = `<?= BASE_URL ?>/etats-avancement/delete/${id}`;
            }
        });
    });
});
</script>
