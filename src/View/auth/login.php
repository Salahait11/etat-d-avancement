<?php // src/View/auth/login.php
// Les variables globales comme $baseUrl, $isLoggedIn, $currentUser
// sont injectées par BaseController::render.
// La variable $title est passée en $data par AuthController::showLoginForm.
// Les messages flash sont gérés par le layout.php.
?>

<div class="login-container" style="max-width: 400px; margin: 30px auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; background-color: #f9f9f9;">

    <h2><?php echo htmlspecialchars($title ?? 'Connexion'); ?></h2>

    <form action="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/login" method="POST">
        <div class="form-group">
            <label for="email">Adresse Email :</label>
            <input type="email" id="email" name="email" required
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); /* Conserve l'email si erreur */ ?>"
                   aria-describedby="emailHelp">
             <small id="emailHelp" style="font-size: 0.8em; color: #6c757d;">Entrez votre adresse email enregistrée.</small>
             <?php // Affichage d'erreur spécifique au champ (si on l'implémente dans le contrôleur)
                 /* if (isset($errors['email'])): ?>
                     <p style="color: red; font-size: 0.9em; margin-top: 5px;"><?php echo htmlspecialchars($errors['email']); ?></p>
                 <?php endif; */ ?>
        </div>

        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required aria-describedby="passwordHelp">
             <small id="passwordHelp" style="font-size: 0.8em; color: #6c757d;">Entrez votre mot de passe.</small>
              <?php /* if (isset($errors['password'])): ?>
                     <p style="color: red; font-size: 0.9em; margin-top: 5px;"><?php echo htmlspecialchars($errors['password']); ?></p>
                 <?php endif; */ ?>
        </div>

        <div>
            <button type="submit" class="button">Se connecter</button>
        </div>

         <!-- Optionnel : Lien mot de passe oublié -->
         <!-- <p style="margin-top: 15px; font-size: 0.9em;">
             <a href="<?php echo htmlspecialchars($baseUrl ?? ''); ?>/forgot-password">Mot de passe oublié ?</a>
         </p> -->
    </form>

</div>

<?php
// Styles spécifiques à cette vue (mieux dans un CSS séparé)
// Juste pour l'exemple, on garde les styles de formulaire ici pour l'instant
?>
<style>
     .form-group { margin-bottom: 1.2em; }
     .form-group label { display: block; margin-bottom: 0.5em; font-weight: bold;}
     .form-group input[type=email], .form-group input[type=password] {
         display: block;
         width: calc(100% - 18px); /* Prend en compte padding+border */
         padding: 8px;
         border: 1px solid #ccc;
         border-radius: 4px;
         font-size: 1em;
     }
     .login-container .button {
         padding: 10px 20px;
         font-size: 1em;
     }
</style>