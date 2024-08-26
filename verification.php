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
        <h3>Un code de confirmation a été envoyé dans votre email, veuillez le saisir dans ce champ.</h3>
        <form method="post" action="#">
            <input type="text" name="verification"><br><br>
            <button type="submit" name="valider">Valider</button>
            <button type="submit" name="renvoyer">Renvoyer le code</button>
        </form>
    </div>



    <?php


// Fonction pour générer un code de confirmation aléatoire
function genererCodeVerification() {
    return rand(100000, 999999); // Génère un code à 6 chiffres
}

// Vérifier si l'utilisateur a cliqué sur le bouton "Renvoyer"
if (isset($_POST['renvoyer'])) {
    // Générer un nouveau code de confirmation
    $nouveauCode = genererCodeVerification();
    $_SESSION['code'] = $nouveauCode;

    // Envoyer le nouveau code à l'email de l'utilisateur
    $to = $_SESSION['email'];
    $subject = "Votre nouveau code de confirmation";
    $message = "Votre nouveau code de confirmation est : $nouveauCode";
    $headers = "From: noreply@votresite.com"; // Modifier l'adresse email de l'expéditeur

    if (mail($to, $subject, $message, $headers)) {
        echo "Un nouveau code de confirmation a été envoyé à votre adresse email.";
    } else {
        echo "Erreur lors de l'envoi du nouveau code.";
    }
}

// Vérifier si l'utilisateur a cliqué sur le bouton "Valider"
if (isset($_POST['valider'])) {
    if ($_SESSION['code'] == $_POST['verification']) {
        try {
            // Connexion à la base de données locale
            $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
            $email = $_SESSION['email'];
            $motpasse = $_SESSION['password'];

            // Insérer les informations dans votre base de données locale
            $requette = $bdd->prepare('INSERT INTO user (user_name, user_password, user_mail, verification) VALUES (:nom, :motpasse, :email, :veri)');
            $requette->bindParam(':nom', $nom);
            $requette->bindParam(':motpasse', $motpasse);
            $requette->bindParam(':email', $email);
            $requette->bindParam(':veri', $_SESSION['code']);

            if ($requette->execute()) {
                // Créer un tiers dans Dolibarr via l'API REST
                $dolibarr_url = 'http://localhost:100/dolibarr/api/index.php/thirdparties';
                $api_key = '809d8187e33a2186b77a7b780ee5fe8219554e79';
                
                // Préparer les données du tiers à envoyer à l'API
                $data = array(
                    'name' => $nom . ' ' . $prenom,
                    'address' => '', // Ajoutez une adresse si disponible
                    'zip' => '', // Ajoutez un code postal si disponible
                    'town' => '', // Ajoutez une ville si disponible
                    'email' => $email,
                    'client' => 1 // 1 si c'est un client, 0 sinon
                );

                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n" .
                                     "DOLAPIKEY: $api_key\r\n",
                        'method'  => 'POST',
                        'content' => json_encode($data)
                    )
                );

                $context  = stream_context_create($options);
                $result = file_get_contents($dolibarr_url, false, $context);

                if ($result === FALSE) {
                    echo 'Erreur lors de la création du tiers dans Dolibarr.';
                } else {
                    // Redirection vers Accueil.php en cas de succès
                    header('Location: Accueil.php');
                    exit;
                }
            } else {
                echo 'Erreur lors de l\'insertion dans la base de données.';
            }
        } catch (PDOException $e) {
            echo 'Erreur : '.$e->getMessage();
        }
    } else {
        echo 'Incompatibilité du code, veuillez insérer le bon code reçu.';
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