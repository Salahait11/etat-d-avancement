<?php
// src/View/utilisateur/list.php
// Vue pour afficher la liste des utilisateurs
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title) ?></h1>
        <a href="<?= BASE_URL ?>/utilisateurs/create" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter un utilisateur
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

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($utilisateurs)): ?>
                <div class="alert alert-info">Aucun utilisateur trouvé.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Statut</th>
                                <th>Rôles</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $utilisateur): ?>
                                <tr>
                                    <td><?= htmlspecialchars($utilisateur['id']) ?></td>
                                    <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
                                    <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
                                    <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                                    <td>
                                        <span class="badge <?= $utilisateur['statut'] === 'actif' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= htmlspecialchars($utilisateur['statut']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($utilisateur['roles'])): ?>
                                            <?php foreach ($utilisateur['roles'] as $role): ?>
                                                <span class="badge bg-info"><?= htmlspecialchars($role) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Aucun rôle</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>/utilisateurs/edit/<?= $utilisateur['id'] ?>" class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <?php if ((int)$utilisateur['id'] !== (int)($_SESSION['user']['id'] ?? 0)): ?>
                                                <form action="<?= BASE_URL ?>/utilisateurs/delete/<?= $utilisateur['id'] ?>" method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
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
