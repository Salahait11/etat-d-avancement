<?php // src/View/home/index.php
// Les variables $title, $welcomeMessage, $baseUrl, $isLoggedIn, $currentUser
// sont injectées par BaseController::render() et extract()
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center">
                        <h1 class="display-4">Système de Gestion des Écoles</h1>
                        <p class="lead mt-4">Une solution complète pour la gestion des formations et des étudiants</p>
                        
                        <?php if ($isLoggedIn ?? false): ?>
                            <div class="mt-4">
                                <p class="text-muted">Bienvenue <?php echo htmlspecialchars($currentUser['prenom'] ?? 'Utilisateur'); ?>!</p>
                                <a href="<?php echo htmlspecialchars($baseUrl); ?>/dashboard" class="btn btn-primary btn-lg">Accéder au Tableau de Bord</a>
                            </div>
                        <?php else: ?>
                            <div class="mt-4">
                                <a href="<?php echo htmlspecialchars($baseUrl); ?>/login" class="btn btn-primary btn-lg">Se connecter</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>