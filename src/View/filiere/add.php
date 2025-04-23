<?php // src/View/filiere/add.php
$title = $title ?? 'Ajouter une Filière';

// Récupérer les erreurs et les données précédemment soumises (si elles existent)
$formErrors = $errors ?? [];
$formValues = $formData ?? [];
?>

<h2>Ajouter une Nouvelle Filière</h2>

<form action="/filieres/add" method="POST">
    <div class="form-group">
        <label for="nom_filiere">Nom de la Filière <span style="color:red;">*</span>:</label>
        <input type="text" id="nom_filiere" name="nom_filiere" required
               value="<?php echo htmlspecialchars($formValues['nom_filiere'] ?? ''); ?>">
        <?php if (isset($formErrors['nom_filiere'])): ?>
            <p class="error"><?php echo htmlspecialchars($formErrors['nom_filiere']); ?></p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="niveau">Niveau <span style="color:red;">*</span>:</label>
        <input type="text" id="niveau" name="niveau" required placeholder="Ex: Bac+2, Licence, Master..."
               value="<?php echo htmlspecialchars($formValues['niveau'] ?? ''); ?>">
         <?php if (isset($formErrors['niveau'])): ?>
            <p class="error"><?php echo htmlspecialchars($formErrors['niveau']); ?></p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="duree_totale">Durée Totale (en heures) <span style="color:red;">*</span>:</label>
        <input type="number" id="duree_totale" name="duree_totale" required min="1" step="1"
               value="<?php echo htmlspecialchars((string)($formValues['duree_totale'] ?? '')); ?>">
         <?php if (isset($formErrors['duree_totale'])): ?>
            <p class="error"><?php echo htmlspecialchars($formErrors['duree_totale']); ?></p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="description">Description :</label>
        <textarea id="description" name="description"><?php echo htmlspecialchars($formValues['description'] ?? ''); ?></textarea>
         <?php if (isset($formErrors['description'])): ?>
            <p class="error"><?php echo htmlspecialchars($formErrors['description']); ?></p>
        <?php endif; ?>
    </div>

    <?php if (isset($db_error) && $db_error): ?>
         <p class="error">Une erreur est survenue lors de l'enregistrement. Vérifiez si le nom de filière n'existe pas déjà.</p>
    <?php endif; ?>

    <div>
        <button type="submit" class="button add-button">Ajouter la Filière</button>
        <a href="/filieres" class="button">Annuler</a>
    </div>
</form>

<!-- Copier les styles du fichier list.php ou les mettre dans style.css -->
<style>
     .error { color: red; font-size: 0.9em; margin-top: 0.2em; }
     .form-group { margin-bottom: 1em; }
     .form-group label { display: block; margin-bottom: 0.3em; font-weight: bold;}
     .form-group input[type=text], .form-group input[type=number], .form-group textarea { width: 90%; max-width: 400px; padding: 8px; border: 1px solid #ccc; border-radius: 3px;}
     .form-group textarea { min-height: 80px; }
     .button { display: inline-block; margin-top: 10px; margin-right: 10px; text-decoration: none; padding: 10px 15px; border-radius: 3px; cursor: pointer; font-size: 1em; border: none; color: white; }
     .add-button { background-color: #4CAF50; /* Green */ }
     .button[href="/filieres"] { background-color: #aaa; } /* Grey for cancel */
</style>