<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'État d\'Avancement' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?= BASE_URL ?>/public/css/style.css" rel="stylesheet">
    
    <!-- Styles basiques -->
    <style>
        body { 
            font-family: sans-serif; 
            margin: 0; 
            padding: 0; 
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        main { 
            flex: 1;
            padding: 2rem 0;
        }
        .flash-messages { 
            margin-bottom: 15px; 
            padding: 0; 
            list-style: none; 
        }
        .flash-message { 
            padding: 12px 18px; 
            margin-bottom: 12px; 
            border-radius: 5px; 
            border: 1px solid transparent; 
        }
        .flash-success { 
            background-color: #d4edda; 
            color: #155724; 
            border-color: #c3e6cb; 
        }
        .flash-error { 
            background-color: #f8d7da; 
            color: #721c24; 
            border-color: #f5c6cb; 
        }
        .flash-warning { 
            background-color: #fff3cd; 
            color: #856404; 
            border-color: #ffeeba; 
        }
        .flash-info { 
            background-color: #d1ecf1; 
            color: #0c5460; 
            border-color: #bee5eb; 
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="<?= BASE_URL ?>">
                <i class="fas fa-graduation-cap me-2"></i>État d'Avancement
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/etats-avancement">
                                <i class="fas fa-clipboard-list me-1"></i>États d'Avancement
                            </a>
                        </li>
                        <?php if ($this->isAdmin()): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog me-1"></i>Administration
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/modules"><i class="fas fa-book me-2"></i>Modules</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/filieres"><i class="fas fa-sitemap me-2"></i>Filières</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/formateurs"><i class="fas fa-chalkboard-teacher me-2"></i>Formateurs</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/objectifs-pedagogiques"><i class="fas fa-bullseye me-2"></i>Objectifs Pédagogiques</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/moyens-didactiques"><i class="fas fa-tools me-2"></i>Moyens Didactiques</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/strategies-evaluation"><i class="fas fa-chart-line me-2"></i>Stratégies d'Évaluation</a></li>
                                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/utilisateurs"><i class="fas fa-users me-2"></i>Utilisateurs</a></li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i><?= htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/profile"><i class="fas fa-id-card me-2"></i>Mon Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= BASE_URL ?>/login">
                                <i class="fas fa-sign-in-alt me-1"></i>Connexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container">
        <?php if (isset($_SESSION['flash_messages'])): ?>
            <?php foreach ($_SESSION['flash_messages'] as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show">
                        <?= htmlspecialchars($message) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
            <?php unset($_SESSION['flash_messages']); ?>
        <?php endif; ?>

        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">
                <i class="fas fa-copyright me-1"></i><?= date('Y') ?> État d'Avancement. Tous droits réservés.
            </span>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JavaScript -->
    <script src="<?= BASE_URL ?>/public/js/app.js"></script>
    <script src="<?= BASE_URL ?>/public/js/etat-avancement.js"></script>
</body>
</html>