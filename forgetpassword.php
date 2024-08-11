<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    .container {
        background-color: #ffffff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 400px;
        text-align: center;
    }

    h2 {
        color: green;
        margin-bottom: 20px;
    }

    p {
        color: #333;
        font-size: 14px;
        margin-bottom: 20px;
    }

    input[type="email"] {
        width: 100%;
        padding: 12px;
        margin: 10px 0 20px 0;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    button {
        background-color: green;
        color: white;
        border: none;
        padding: 12px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        border-radius: 4px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
    }

    button:hover {
        background-color: #006400;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Réinitialiser votre mot de passe</h2>
        <p>Si vous avez un compte sur ce site, un email contenant un lien de réinitialisation du mot de passe vous sera
            envoyé.
            Veuillez
            consulter votre boîte email.</p>
        <form action="send_reset_link.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Entrez votre adresse email" required>
            <button type="submit">Renvoyer le mot de passe</button>
        </form>
    </div>
</body>

</html>