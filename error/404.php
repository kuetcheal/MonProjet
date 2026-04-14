<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>404 - Page introuvable</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    body {
      background: #f8f9fb;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      padding: 20px;
    }

    .container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 1100px;
      width: 100%;
      gap: 40px;
    }

    .left {
      flex: 1;
    }

    .left h1 {
      font-size: 80px;
      color: green;
      margin-bottom: 10px;
    }

    .left p {
      font-size: 18px;
      color: #333;
      margin-bottom: 10px;
    }

    .code {
      color: #666;
      margin-bottom: 20px;
    }

    .btn {
      display: inline-block;
      margin-top: 10px;
      padding: 12px 24px;
      background: green;
      color: #fff;
      text-decoration: none;
      font-weight: 600;
      border-radius: 4px;
      transition: 0.3s;
    }

    .btn:hover {
      background: green;
    }

    .right {
      flex: 1;
      text-align: center;
    }

    .right img {
      max-width: 100%;
      height: auto;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
        text-align: center;
      }

      .left h1 {
        font-size: 60px;
      }
    }
  </style>
</head>

<body>

  <div class="container">
    <!-- Texte -->
    <div class="left">
      <h1>Oups !</h1>

      <p><strong>Oops cette page n'existe pas (ou plus)</strong></p>
      <p class="code">Code d’erreur : 404</p>

      <a href="/MonProjet/Accueil.php" class="btn">
        Retour à l'accueil
      </a>
    </div>

    <!-- Image -->
    <div class="right">
      <img src="/MonProjet/pictures/error-pnj.webp" alt="Erreur 404">
    </div>
  </div>

</body>
</html>