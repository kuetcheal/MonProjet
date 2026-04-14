<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>403 - Accès interdit</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 60px;
    }
    h1 {
      font-size: 60px;
      color: #261e48;
    }
    a {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: white;
      background: #261e48;
      padding: 12px 20px;
    }
  </style>
</head>
<body>
  <h1>403</h1>
  <p>vous n'êtes pas autorisé à consulter la page dont vous voulez.</p>
  <a href="/MonProjet/Accueil.php">Retour à l’accueil</a>
</body>
</html>