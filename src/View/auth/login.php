<?php // src/View/auth/login.php
// $title est défini par le contrôleur
// $error (flash) est géré par le layout
?>

<h2>Connexion</h2>

<form action="/login" method="POST">
    <div class="form-group">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required autofocus
               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); /* Conserve l'email si erreur */ ?>">
    </div>
    <div class="form-group">
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button type="submit" class="button">Se connecter</button>
    </div>
</form>