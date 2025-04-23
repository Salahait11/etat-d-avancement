<?php // _scripts/hash_password.php

// Définit le mot de passe en clair que tu souhaites utiliser pour ton test
$plainPassword = 'monSuperMotDePasse123!'; // <-- CHANGE CECI pour un mot de passe sécurisé

// Utilise la fonction de hachage recommandée par PHP
$hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

// Vérifie si le hachage a réussi
if ($hashedPassword === false) {
    echo "Erreur lors du hachage du mot de passe.";
} else {
    echo "Mot de passe en clair : " . htmlspecialchars($plainPassword) . "<br>";
    echo "--------------------------------------------------------<br>";
    echo "Hash à utiliser (copie cette ligne entière) :<br>";
    echo "<strong>" . htmlspecialchars($hashedPassword) . "</strong><br>";
    echo "--------------------------------------------------------<br>";
    echo "<p>Tu peux maintenant utiliser ce hash dans ta requête SQL INSERT.</p>";
    echo "<p>Exemple SQL :</p>";
    echo "<pre>INSERT INTO utilisateur (nom, prenom, mot_de_passe, email, statut) VALUES ('Admin', 'Test', '" . htmlspecialchars($hashedPassword) . "', 'admin@exemple.com', 'actif');</pre>";
}
?>