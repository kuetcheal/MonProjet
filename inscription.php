<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="CSS/inscription.css">
    <title>Document</title>
</head>

<body>

<?php include 'includes/header.php'; ?>


    <div class="container">
        <h2>Formulaire d'inscription</h2>
        <hr>
        <form action="verification.php" method="POST">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" placeholder="KUETCHE" required>

            <label for=" prenom">Prénom:</label>
            <input type="text" id="prenom" name="prenom" placeholder="ALEX" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="nom@gmail.com" required>

            <label for="email">Téléphone:</label>
            <input type="number" id="phone" name="phone" placeholder="655198412" required>

            <label for="password">Mot de passe:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">S'inscrire</button>
        </form>
    </div>

    <footer>
        <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>

    <style>
    body {
        background-color: aliceblue;
    }

    .container {
        background-color: green;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        margin: 50px auto;
        max-width: 500px;
        padding: 20px;
        margin-top: 70px;
    }

    form {
        display: flex;
        flex-direction: column;
        width: 300px;
        margin: auto;
    }

    label {
        margin-top: 10px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"] {
        padding: 5px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid black;
    }

    button[type="submit"] {
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    button[type="submit"]:hover {
        background-color: #3e8e41;
    }


    h2 {
        text-align: center;
        color: white;
    }



    header {
        width: 100%;
        background-color: green;
        height: 100px;
    }

    nav {
        width: 100%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    img {
        width: 120px;
        height: 80px;
        margin-top: 20px;
    }

    .items a {
        text-decoration: none;
        color: whitesmoke;
        font-size: 20px;
        margin-right: 40PX;
        padding: 0 15px;
    }


    .nav-bar ul {
        display: flex;

        list-style-type: none;
    }

    .header-picture {
        margin-left: 40px;

    }

    img {

        height: 60px;
        width: 100px;
    }

    footer {
        background-color: #6c757d;
        color: white;
        padding: 20px 0;
        text-align: center;
        width: 100%;
    }

    .nav-bar {
        margin-right: 30px;
    }
    </style>



</body>

</html>