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
require 'vendor/autoload.php';
use Mailjet\Resources;

// Traitement du formulaire d'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nom'], $_POST['email'], $_POST['password'])) {
    // Récupération des données du formulaire
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hachage du mot de passe

    // Génération d'un code de vérification
    $veri = uniqid('', true);
    $code = substr($veri, -4);

    // Initialisation de la session

    $_SESSION['nom'] = $nom;
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
    $_SESSION['code'] = $code;

    // Configuration de l'API Mailjet
    $mj = new \Mailjet\Client('f163a8d176afbcb29aae519bf6c5e181', 'bf285777b4d59f84a43855ae1b40f96d', true, ['version' => 'v3.1']);

    // Construction du corps de l'email
    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => 'akuetche55@gmail.com',
                    'Name' => 'Easy travel',
                ],
                'To' => [
                    [
                        'Email' => $email,
                        'Name' => $nom,
                    ],
                ],
                'Subject' => 'Confirmation de votre compte',
                'TextPart' => "Bonjour $nom, voici votre code de confirmation : $code",
                'HTMLPart' => "<h3>Bonjour $nom,</h3><p>Votre compte est en cours de création. Veuillez saisir ce code de confirmation <strong>$code</strong> sur le site.</p>",
            ],
        ],
    ];

    // Envoi de l'email
    try {
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            echo 'Email sent successfully.';
        } else {
            echo 'Failed to send email: '.$response->getData()['ErrorMessage'];
        }
    } catch (Exception $e) {
        echo 'Message could not be sent. Mailer Error: '.$e->getMessage();
    }
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
        <form method="post" action="Accueil.php">
            <input type="text" name='verification'><br><br>
            <button type="submit" name='valider'>Valider</button>
        </form>
    </div>


    <?php

if (isset($_POST['verification'])) {
    if ($_SESSION['code'] == $_POST['verification']) {
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

            $nom = $_SESSION['nom'];
            $motpasse = $_SESSION['motpasse'];
            $mail1 = $_SESSION['mail1'];
            $veri = $_SESSION['code'];
            $requette = "insert into user (user_name, user_password, user_mail, verification) values ('$nom', '$motpasse', '$mail1', '$veri');";
            $bdd->exec($requette);

            header('Location: Accueil.php');
            exit;
            // echo ("Inscription réussie.");
        } catch (Exception $e) {
            echo 'echec de connexion';
        }
    } else {
        echo 'incompatibilité du code,veuillez insérer le bon code reçu';
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