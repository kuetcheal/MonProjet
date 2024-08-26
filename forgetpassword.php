<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
    <style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    .container {
        background-color: #ffffff;
        padding: 20px 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        width: 400px;
        text-align: center;
        margin: auto;
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

    .footer {
        background-color: #6c757d;
        color: white;
        padding: 20px 0;
        text-align: center;
        margin-top: auto;
        width: 100%;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Réinitialiser votre mot de passe</h2>
        <p>Si vous avez un compte sur ce site, un email contenant un lien de réinitialisation du mot de passe vous sera
            envoyé.
            Veuillez consulter votre boîte email.</p>
        <form action="send_reset_link.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Entrez votre adresse email" required>
            <button type="submit">Renvoyer le mot de passe</button>
        </form>
    </div>

    <footer class="footer">
        <p>&copy; 2024 EasyTravel: Alex K. Tous droits réservés.</p>
    </footer>

</body>