<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><?= htmlspecialchars($title ?? 'Liste des Filières') ?></h1>
        <a href="<?= BASE_URL ?>/filieres/add" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter une filière
        </a>
    </div>

    <?php if (isset($_SESSION['_flash']['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['_flash']['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['_flash']['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($_SESSION['_flash']['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($filieres)): ?>
        <div class="alert alert-info">
            Aucune filière n'a été trouvée. Commencez par en ajouter une.
        </div>
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nom de la filière</th>
                                <th>Niveau</th>
                                <th>Durée totale (heures)</th>
                                <th>Date de création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($filieres as $filiere): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string)$filiere['id']) ?></td>
                                    <td><?= htmlspecialchars($filiere['nom_filiere']) ?></td>
                                    <td><?= htmlspecialchars($filiere['niveau']) ?></td>
                                    <td><?= htmlspecialchars((string)$filiere['duree_totale']) ?></td>
                                    <td><?= htmlspecialchars((new DateTime($filiere['created_at']))->format('d/m/Y H:i')) ?></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= BASE_URL ?>/filieres/show/<?= $filiere['id'] ?>" class="btn btn-sm btn-info" title="Détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?= BASE_URL ?>/filieres/edit/<?= $filiere['id'] ?>" class="btn btn-sm btn-warning" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#genericDeleteModal" data-url="<?= BASE_URL ?>/filieres/delete/<?= $filiere['id'] ?>" data-item="<?= htmlspecialchars($filiere['nom_filiere']) ?>" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
