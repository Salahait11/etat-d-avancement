<?php // src/View/auth/login.php
$title = "Connexion"; // Définit le titre pour le layout

// La variable $error est passée par AuthController::showLoginForm() et extraite par render()
?>

<h2>Connexion</h2>

<!-- Le message d'erreur flash est géré par layout.php -->
<!-- Si on voulait un message spécifique ici (en plus du flash), on utiliserait $error -->
<?php if (isset($error) && $error): ?>
    <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form action="/login" method="POST" class="login-form">
    <div>
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required autofocus
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); /* Conserve l'email si erreur */ ?>">
    </div>
    <div>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button type="submit">Se connecter</button>
    </div>
</form>
<!-- Optionnel: Lien vers une page "mot de passe oublié" ou "inscription" -->
<!--
<p>
    <a href="/forgot-password">Mot de passe oublié ?</a> |
    <a href="/register">Créer un compte</a>
</p>
-->