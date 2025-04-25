<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? htmlspecialchars($title) : 'Gestion Écoles v2'; ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr pour les datepickers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
    <!-- Select2 pour les sélections multiples -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Styles personnalisés -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/css/style.css">
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="<?php echo BASE_URL; ?>">Gestion Pédagogique</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo BASE_URL; ?>">
                                <i class="fas fa-home me-1"></i> Accueil
                            </a>
                        </li>
                        
                        <?php if (isset($isLoggedIn) && $isLoggedIn === true): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?php echo BASE_URL; ?>/dashboard">
                                    <i class="fas fa-tachometer-alt me-1"></i> Tableau de Bord
                                </a>
                            </li>
                            
                            <!-- Menu Administration (Admin uniquement) -->
                            <?php if (isset($currentUser) && isset($currentUser['roles']) && in_array('admin', $currentUser['roles'], true)): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cogs me-1"></i> Administration
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/filieres">
                                                <i class="fas fa-sitemap me-1"></i> Filières
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/utilisateurs">
                                                <i class="fas fa-users-cog me-1"></i> Gérer Utilisateurs
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/formateurs">
                                                <i class="fas fa-chalkboard-teacher me-1"></i> Formateurs
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <h6 class="dropdown-header">Configuration système</h6>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/parametres">
                                                <i class="fas fa-sliders-h me-1"></i> Paramètres
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                
                                <!-- Menu Référentiels pédagogiques -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="referentielsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-book me-1"></i> Référentiels
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="referentielsDropdown">
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/objectifs-pedagogiques">
                                                <i class="fas fa-bullseye me-1"></i> Objectifs Pédagogiques
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/contenus-seance">
                                                <i class="fas fa-list-alt me-1"></i> Contenus de Séance
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/moyens-didactiques">
                                                <i class="fas fa-tools me-1"></i> Moyens Didactiques
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/strategies-evaluation">
                                                <i class="fas fa-chart-line me-1"></i> Stratégies d'Évaluation
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Menu Formation (Admin et Formateurs) -->
                            <?php if (isset($currentUser) && isset($currentUser['roles']) && (in_array('admin', $currentUser['roles'], true) || in_array('formateur', $currentUser['roles'], true))): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="formationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-graduation-cap me-1"></i> Formation
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="formationDropdown">
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/modules">
                                                <i class="fas fa-cubes me-1"></i> Modules
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/etats-avancement">
                                                <i class="fas fa-tasks me-1"></i> États d'Avancement
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="d-flex">
                        <?php if (isset($isLoggedIn) && $isLoggedIn === true): ?>
                            <span class="user-info me-2"><?php echo htmlspecialchars($currentUser['prenom'] ?? 'Utilisateur'); ?></span>
                            <a href="<?php echo BASE_URL; ?>/logout" class="btn btn-sm btn-danger">Déconnexion</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>/login" class="btn btn-sm btn-outline-light">Connexion</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <div class="container mt-4 main-content">
        <main>
            <!-- Affichage des Messages Flash -->
            <?php if (isset($_SESSION['_flash']) && !empty($_SESSION['_flash'])): ?>
                <?php foreach ($_SESSION['_flash'] as $key => $message): ?>
                    <?php 
                        $alertClass = 'alert-info';
                        if ($key === 'success') $alertClass = 'alert-success';
                        if ($key === 'error') $alertClass = 'alert-danger';
                        if ($key === 'warning') $alertClass = 'alert-warning';
                    ?>
                    <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['_flash']); // Très important ?>
            <?php endif; ?>
            
            <!-- Affichage des Messages Flash (ancien format) -->
            <?php if (isset($_SESSION['flash_message'])): ?>
                <div class="alert alert-<?= $_SESSION['flash_message']['type'] === 'error' ? 'danger' : $_SESSION['flash_message']['type'] ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_message']['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <!-- Contenu spécifique de la vue -->
            <?php echo $content ?? ''; ?>
        </main>
    </div>

    <footer class="bg-dark text-light py-3 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0"> 2023 Gestion Pédagogique</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Version 2.0</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- jQuery (nécessaire pour Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle avec Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Flatpickr pour les datepickers -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/fr.js"></script>
    <!-- Select2 pour les sélections multiples -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Application JS -->
    <script src="<?php echo BASE_URL; ?>/js/app.js"></script>

    <!-- Modal de confirmation générique -->
    <div class="modal fade" id="genericDeleteModal" tabindex="-1" aria-labelledby="genericDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="genericDeleteModalLabel">Confirmation de suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">
                    <p id="genericDeleteModalBody">Êtes-vous sûr ?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <form id="genericDeleteForm" method="post" action="">
                        <button type="submit" class="btn btn-danger">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      var genericModal = document.getElementById('genericDeleteModal');
      genericModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var url = button.getAttribute('data-url');
        var item = button.getAttribute('data-item');
        var body = genericModal.querySelector('#genericDeleteModalBody');
        body.textContent = 'Êtes-vous sûr de vouloir supprimer « ' + item + ' » ? Cette action est irréversible.';
        var form = genericModal.querySelector('#genericDeleteForm');
        form.action = url;
      });
    });
    </script>
</body>
</html>