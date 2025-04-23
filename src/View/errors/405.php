<?php // src/View/errors/405.php
http_response_code(405);
$title = "405 - Méthode Non Autorisée";
?>
<h1>Erreur 405</h1>
<p>La méthode HTTP utilisée pour accéder à cette ressource n'est pas permise.</p>
<p><a href="/">Retour à l'accueil</a></p>