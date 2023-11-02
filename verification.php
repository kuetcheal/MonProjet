<?php 
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Document</title>
    <!-- <link rel="stylesheet" href="verification.css"> -->
</head>

<body>

    <?php
// Importation des librairies phpmailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
//  $mail= new PHPMailer(true);


if( !isset($_POST["verification"])&& isset($_POST["nom"]) && isset($_POST["password"]) && isset($_POST["email"])){

  $nom=$_POST["nom"];
  $_SESSION["nom"]=$nom;
  $motpasse=$_POST["password"];
  $_SESSION["motpasse"]=$motpasse;
  $mail1=$_POST["email"];
  $_SESSION["mail1"]=$mail1;
   $veri=uniqid();
   $code=(string)$veri;
   $code=substr($code, 0, 4);

   $_SESSION["code"]=$code;
   $mail= new PHPMailer();
//Server settings
 $mail->SMTPDebug = 0; 
// $mail = new PHPMailer();                     //Enable verbose debug output
$mail->isSMTP();                                            //Send using SMTP
 $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username   = 'kuetchealex99@gmail.com';                     //SMTP username
$mail->Password   = 'gboxarsdnbixrzxr';                               //SMTP password
$mail->SMTPSecure = 'TLS';            //Enable implicit TLS encryption
$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Recipients
$mail->setFrom('kuetchealex99@gmail.com', 'Easy travel');
$mail->addAddress($mail1, 'Joe User');     //Add a recipient

$mail->isHTML(true);                                  //Set email format to HTML
$mail->Subject = 'Confirmation du code';
$mail->Body    = '<p style="font-size: 15px; font-weight: bold;"> Bravoo '. $nom .'  votre compte est en voie de création, veuillez saisir ce code de confirmation <span style=" height: 80px; width: 160px; background-color:green; font: size 18px;">'. $code .'</span> dans le champs de votre interface utilisateur.</p>'; 

$mail->send();

}
?>


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
        <h3>un code de confirmation a été envoyé dans votre email,veuillez le saisir dans ce champ. </h3>
        <form method="post" action="#">
            <input type="text" name='verification'><br><br>
            <button type="submit" name='valider'>Valider</button>
        </form>
    </div>


    <?php

if(isset($_POST["verification"])){

if($_SESSION["code"]==$_POST["verification"]){
    
try
{
$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

$nom=$_SESSION["nom"];
$motpasse=$_SESSION["motpasse"];
$mail1=$_SESSION["mail1"];
$veri=$_SESSION["code"];
$requette = "insert into user (user_name, user_password, user_mail, verification) values ('$nom', '$motpasse', '$mail1', '$veri');";
$bdd->exec($requette);

header("Location: Accueil.php");
exit;
// echo ("Inscription réussie.");

}

catch (Exception $e)
{
echo("echec de connexion");
}
}

else{
  echo("incompatibilité du code,veuillez insérer le bon code reçu"); 
}
}
;
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
        border: 1px solid #ccc;
        /* bordure grise de 1 pixel */
        padding: 10px;
        /* marges intérieures de 10 pixels */
        max-width: 500px;
        /* largeur maximale de 500 pixels */
        margin: 0 auto;
        /* centrage horizontal */
        height: 200px;
        margin-top: 75px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    }

    h2 {
        font-size: 1.5rem;
        /* taille de police de 1,5 fois la taille par défaut */
        margin-bottom: 10px;
        /* espace de 10 pixels en dessous */
    }

    input[type="text"] {
        padding: 5px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 50%;
        /* largeur de 100% du conteneur */
    }

    button[type="submit"] {
        padding: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        width: '150px';
        font-size: '16px';
    }
    </style>

</body>

</html>