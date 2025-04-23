<?php // src/View/errors/500.php
http_response_code(500);
$title = "500 - Erreur Serveur";
?>
<h1>Erreur 500</h1>
<p>Une erreur interne est survenue sur le serveur. Veuillez réessayer plus tard ou contacter l'administrateur.</p>
<p><a href="/">Retour à l'accueil</a></p>