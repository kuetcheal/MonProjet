<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Mailjet\Resources;

// Fonction pour générer un code de confirmation aléatoire
function genererCodeVerification()
{
    return rand(100000, 999999);
}

// Fonction d'envoi d'email via Mailjet
function envoyerCodeVerificationMailjet(string $email, string $nom, string $code): bool
{
    $mj = new \Mailjet\Client(
        MAILJET_PUBLIC_KEY,
        MAILJET_PRIVATE_KEY,
        true,
        ['version' => 'v3.1']
    );

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => MAIL_FROM_EMAIL,
                    'Name' => MAIL_FROM_NAME,
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

    try {
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    } catch (Exception $e) {
        return false;
    }
}

$message = '';

// Traitement du formulaire d'inscription
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['phone'], $_POST['password'])
) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $veri = uniqid('', true);
    $code = substr($veri, -4);

    $_SESSION['nom'] = $nom;
    $_SESSION['prenom'] = $prenom;
    $_SESSION['phone'] = $phone;
    $_SESSION['email'] = $email;
    $_SESSION['password'] = $password;
    $_SESSION['code'] = $code;

    if (envoyerCodeVerificationMailjet($email, $nom, $code)) {
        $message = "Un code de confirmation a été envoyé à votre adresse email.";
    } else {
        $message = "Erreur lors de l'envoi de l'email de confirmation.";
    }
}

// Vérifier si l'utilisateur a cliqué sur le bouton "Renvoyer"
if (isset($_POST['renvoyer'])) {
    if (!isset($_SESSION['email'], $_SESSION['nom'])) {
        $message = "Session expirée. Veuillez recommencer l'inscription.";
    } else {
        $nouveauCode = genererCodeVerification();
        $_SESSION['code'] = $nouveauCode;

        if (envoyerCodeVerificationMailjet($_SESSION['email'], $_SESSION['nom'], (string) $nouveauCode)) {
            $message = "Un nouveau code de confirmation a été envoyé à votre adresse email.";
        } else {
            $message = "Erreur lors de l'envoi du nouveau code.";
        }
    }
}

// Vérifier si l'utilisateur a cliqué sur le bouton "Valider"
if (isset($_POST['valider'])) {
    if (!isset($_SESSION['code'], $_POST['verification'])) {
        $message = "Aucun code à vérifier.";
    } elseif ((string) $_SESSION['code'] === trim($_POST['verification'])) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $bdd = new PDO($dsn, DB_USER, DB_PASS);
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $nom = $_SESSION['nom'];
            $prenom = $_SESSION['prenom'];
            $email = $_SESSION['email'];
            $motpasse = $_SESSION['password'];
            $phone = $_SESSION['phone'];

            $requette = $bdd->prepare('
                INSERT INTO user (
                    user_name,
                    user_firstname,
                    user_password,
                    user_mail,
                    user_phone,
                    verification
                ) VALUES (
                    :nom,
                    :prenom,
                    :motpasse,
                    :email,
                    :phone,
                    :veri
                )
            ');

            $requette->bindParam(':nom', $nom);
            $requette->bindParam(':prenom', $prenom);
            $requette->bindParam(':motpasse', $motpasse);
            $requette->bindParam(':email', $email);
            $requette->bindParam(':phone', $phone);
            $requette->bindParam(':veri', $_SESSION['code']);

            if ($requette->execute()) {
                $data = [
                    'name' => $nom . ' ' . $prenom,
                    'address' => '',
                    'zip' => '',
                    'town' => '',
                    'email' => $email,
                    'client' => 1
                ];

                $options = [
                    'http' => [
                        'header'  => "Content-type: application/json\r\n" .
                                      "DOLAPIKEY: " . DOLIBARR_API_KEY . "\r\n",
                        'method'  => 'POST',
                        'content' => json_encode($data)
                    ]
                ];

                $context = stream_context_create($options);
                $result = file_get_contents(DOLIBARR_API_URL, false, $context);

                if ($result === false) {
                    $message = "Erreur lors de la création du tiers dans Dolibarr.";
                } else {
                    header('Location: Accueil.php');
                    exit;
                }
            } else {
                $message = "Erreur lors de l'insertion dans la base de données.";
            }
        } catch (PDOException $e) {
            $message = "Erreur de base de données.";
        }
    } else {
        $message = "Incompatibilité du code, veuillez insérer le bon code reçu.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <style>
        body {
            background-color: aliceblue;
            margin: 0;
            font-family: Arial, sans-serif;
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

        .header-picture {
            margin-left: 40px;
        }

        img {
            height: 60px;
            width: 100px;
            margin-top: 20px;
        }

        .nav-bar {
            margin-right: 30px;
        }

        .nav-bar ul {
            display: flex;
            list-style-type: none;
        }

        .items a {
            text-decoration: none;
            color: whitesmoke;
            font-size: 20px;
            margin-right: 40px;
            padding: 0 15px;
        }

        .container {
            border: 1px solid #ccc;
            padding: 20px;
            max-width: 500px;
            margin: 75px auto 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            background: #fff;
        }

        h3 {
            margin-top: 0;
        }

        input[type="text"] {
            padding: 8px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 50%;
        }

        button[type="submit"] {
            padding: 10px 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 8px;
        }

        .message {
            margin-top: 15px;
            padding: 10px;
            background: #f3f3f3;
            border-left: 4px solid green;
        }
    </style>
</head>

<body>

<header>
    <nav>
        <div class="header-picture">
            <img src="logo général.jpg" alt="logo site">
        </div>
        <div class="nav-bar">
            <ul>
                <li class="items">
                    <select id="select" name="select" aria-placeholder="2 places">
                        <option value="option1">Français</option>
                        <option value="option2">Anglais</option>
                    </select>
                </li>
                <li class="items">
                    <a href="#"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>

<div class="container">
    <h3>Un code de confirmation a été envoyé dans votre email, veuillez le saisir dans ce champ.</h3>

    <form method="post" action="#">
        <input type="text" name="verification" required><br><br>
        <button type="submit" name="valider">Valider</button>
        <button type="submit" name="renvoyer">Renvoyer le code</button>
    </form>

    <?php if (!empty($message)): ?>
        <div class="message">
            <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>
</div>

</body>
</html>