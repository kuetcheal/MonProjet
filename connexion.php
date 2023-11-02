<?php
session_start();
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="connexion.css">
</head>

<body>

    <header>
        <nav>
            <div class="header-picture">
                <img src="logo général.jpg" alt="logo site" />
            </div>
            <div class="nav-bar">
                <ul>
                    <li class="items">
                        <select id="select" name="select" aria-placeholder="2 places">
                            <option value="option1">Français</option>
                            <option value="option2">Anglais</option>

                        </select>
                    </li>
                    <li class="items"><a href="#"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Connexion</h1>
        <form method="post" action="#">
            <label for="username" style="color: white;">Nom d'utilisateur :</label>
            <input type=" text" name="username" id="username" placeholder="ALEX" required
                style="height: 20px; border-radius: 5px; padding: 10px;"><br>
            <label for="password" style="color: white;">Mot de passe :</label>
            <input type="password" name="password" id="password" placeholder="1000jean" required><br>
            <button type="submit">se connecter</button>
            <p class="para">Si vous n'avez pas de compte, veuillez vous inscrire ici.<a
                    href="inscription.php">s'inscrire</a> </p>
        </form>
    </div>
    <div class="social-icons">
        <ul>
            <li class="liste">Rejoignez-nous sur:</li>
            <li class="liste"><a href="#"><i class="fa fa-facebook-official fa-2x" aria-hidden="true"></i></a></li>
            <li class="liste"><a href="#"><a href="#"><i class="fa fa-twitter fa-2x" aria-hidden="true"></i></a></li>
            <li class="liste"><a href="#"><i class="fa fa-instagram fa-2x" aria-hidden="true"></i></a></li>
            <li class="liste"><a href="#"><i class="fa fa-snapchat-ghost fa-2x" aria-hidden="true"></i></a></li>
        </ul>
    </div>
    <hr id="ligne_bas">
    </div>


    <?php

    $host = "localhost"; // nom d'hôte
    $user = "root"; // nom d'utilisateur
    $password = ""; // mot de passe
    $database = "bd_stock"; // nom de la base de données

    // Connexion à la base de données MySQLi
    $conn = mysqli_connect($host, $user, $password, $database);
    if(isset($_POST["username"]) && isset($_POST["password"])){
    // Vérifier la connexion
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $password = $_POST['password'];
    

    // Exécuter une requête SELECT pour récupérer les informations d'identification de l'utilisateur
    $query = "SELECT * FROM user WHERE user_name = '$username' AND user_password = '$password'";
    $result = mysqli_query($conn, $query);
  
    // Vérifier si l'utilisateur existe dans la base de données
    if (mysqli_num_rows($result) == 1) {
      while($valeur=mysqli_fetch_assoc($result)){
        echo($valeur['id_name']); 
        $_SESSION['Id_compte']=$valeur['id_name'];
      }

     header("Location: Accueil.php");
    } elseif ($username == "GENERAL" && $password == "123general") {
      header("Location: Accueiladmin.php");
    } else {
      header("Location: errorconnexion.php");
    }
  }
?>


    <style>
    body {
        background-color: aliceblue;
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

    .nav-bar {
        margin-right: 30px;
    }





    .container {
        background-color: green;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        margin: 50px auto;
        max-width: 500px;
        padding: 20px;
        margin-top: 20px;

    }




    h1 {
        margin-bottom: 20px;
        text-align: center;
        color: white;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 10px;
    }

    input[type="text"],
    input[type="password"] {
        border: black;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 20px;
    }

    button[type="submit"] {
        background-color: white;
        border: none;
        border-radius: 5px;
        color: white;
        padding: 10px;
        font-size: 16px;
        cursor: pointer;
    }

    button[type="submit"] {
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .liste ul {
        display: flex;
        font-size: 15px;
        list-style-type: none;
    }

    button:hover {
        background-color: #3e8e41;
    }

    .para {
        font-size: 16px;
    }

    hr {
        color: aqua;

    }

    #ligne {
        margin-top: 50px;
        color: aqua;
        height: 2px;
    }

    #ligne_bas {
        margin-bottom: 70px;
        height: 2px;
        color: aqua;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        margin: '25px';
        flex-basis: 30%;
        margin-bottom: 20px;
        margin-left: 50px;
    }

    .social-icons ul {
        display: flex;
        margin-top: 10px;
        margin-bottom: 10px;
        list-style-type: none;
    }

    .social-icons a {
        display: flex;

        margin: 0 10px;
    }

    .social-icons li {
        /* Ajoute un espace de 10 pixels à gauche et à droite de chaque élément de la liste */
        padding-left: 15px;
        padding-right: 15px;
    }


    .social-icons i {
        font-size: 24px;
        color: black;
    }

    .social-icons li.liste {
        font-size: 20px;
    }

    .social-icons li a i {
        font-size: 32px;
    }



    a {
        color: white;
    }

    /* ECHEC AUTHENTIFICATION */
    .para2 {
        text-align: center;
        color: red;
        font-size: 18px;
    }

    .containere {
        background-color: green;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
        margin: 50px auto;
        max-width: 500px;
        padding: 20px;
        margin-top: 20px;
        border-color: 1px red;

    }
    </style>


</body>

</html>