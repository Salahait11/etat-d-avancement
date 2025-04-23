<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Modifier la Compétence</h1>
        <a href="/competences/<?php echo $competence['id']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="/competences/<?php echo $competence['id']; ?>/update" method="POST" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="code" class="form-label">Code *</label>
                        <input type="text" 
                               class="form-control" 
                               id="code" 
                               name="code" 
                               value="<?php echo htmlspecialchars($competence['code']); ?>"
                               required
                               pattern="[A-Z0-9]{2,10}"
                               title="Le code doit contenir entre 2 et 10 caractères (lettres majuscules et chiffres)">
                        <div class="invalid-feedback">
                            Veuillez entrer un code valide (2-10 caractères, lettres majuscules et chiffres).
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="niveau" class="form-label">Niveau *</label>
                        <select class="form-select" id="niveau" name="niveau" required>
                            <option value="">Sélectionnez un niveau</option>
                            <option value="débutant" <?php echo $competence['niveau'] === 'débutant' ? 'selected' : ''; ?>>
                                Débutant
                            </option>
                            <option value="intermédiaire" <?php echo $competence['niveau'] === 'intermédiaire' ? 'selected' : ''; ?>>
                                Intermédiaire
                            </option>
                            <option value="avancé" <?php echo $competence['niveau'] === 'avancé' ? 'selected' : ''; ?>>
                                Avancé
                            </option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un niveau.
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="intitule" class="form-label">Intitulé *</label>
                    <input type="text" 
                           class="form-control" 
                           id="intitule" 
                           name="intitule" 
                           value="<?php echo htmlspecialchars($competence['intitule']); ?>"
                           required
                           minlength="5"
                           maxlength="100">
                    <div class="invalid-feedback">
                        L'intitulé doit contenir entre 5 et 100 caractères.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              rows="5" 
                              required
                              minlength="20"
                              maxlength="1000"><?php echo htmlspecialchars($competence['description']); ?></textarea>
                    <div class="invalid-feedback">
                        La description doit contenir entre 20 et 1000 caractères.
                    </div>
                </div>

                <div class="mb-3">
                    <label for="id_module" class="form-label">Module *</label>
                    <select class="form-select" id="id_module" name="id_module" required>
                        <option value="">Sélectionnez un module</option>
                        <?php foreach ($modules as $module): ?>
                            <option value="<?php echo $module['id']; ?>" 
                                    <?php echo $competence['id_module'] === $module['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($module['titre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Veuillez sélectionner un module.
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Validation des formulaires Bootstrap
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?> 