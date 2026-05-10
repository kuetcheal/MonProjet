<?php
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Mailjet\Resources;

function genererCodeVerification(): string
{
    return (string) rand(100000, 999999);
}

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
                'HTMLPart' => "
                    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #222;'>
                        <h3>Bonjour " . htmlspecialchars($nom) . ",</h3>
                        <p>Votre compte est en cours de création.</p>
                        <p>Veuillez saisir ce code de confirmation sur le site :</p>
                        <p style='font-size: 24px; font-weight: bold; color: green;'>$code</p>
                    </div>
                ",
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
$messageType = 'info';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données.');
}

/**
 * ÉTAPE 1 : formulaire d'inscription envoyé depuis inscription.php
 */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['phone'], $_POST['password']) &&
    !isset($_POST['valider']) &&
    !isset($_POST['renvoyer'])
) {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $passwordBrut = $_POST['password'];

    if ($nom === '' || $prenom === '' || $email === '' || $phone === '' || $passwordBrut === '') {
        $message = "Tous les champs sont obligatoires.";
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Adresse email invalide.";
        $messageType = 'error';
    } else {
        $checkUser = $bdd->prepare("SELECT id FROM user WHERE user_mail = :email LIMIT 1");
        $checkUser->execute([':email' => $email]);

        if ($checkUser->fetch()) {
            $message = "Cet email est déjà utilisé.";
            $messageType = 'error';
        } else {
            $passwordHash = password_hash($passwordBrut, PASSWORD_DEFAULT);
            $code = genererCodeVerification();

            $_SESSION['register_nom'] = $nom;
            $_SESSION['register_prenom'] = $prenom;
            $_SESSION['register_phone'] = $phone;
            $_SESSION['register_email'] = $email;
            $_SESSION['register_password'] = $passwordHash;
            $_SESSION['register_code'] = $code;

            if (envoyerCodeVerificationMailjet($email, $nom, $code)) {
                $message = "Un code de confirmation a été envoyé à votre adresse email.";
                $messageType = 'success';
            } else {
                $message = "Erreur lors de l'envoi de l'email de confirmation.";
                $messageType = 'error';
            }
        }
    }
}

/**
 * ÉTAPE 2 : renvoi du code
 */
if (isset($_POST['renvoyer'])) {
    if (!isset($_SESSION['register_email'], $_SESSION['register_nom'])) {
        $message = "Session expirée. Veuillez recommencer l'inscription.";
        $messageType = 'error';
    } else {
        $nouveauCode = genererCodeVerification();
        $_SESSION['register_code'] = $nouveauCode;

        if (envoyerCodeVerificationMailjet($_SESSION['register_email'], $_SESSION['register_nom'], $nouveauCode)) {
            $message = "Un nouveau code de confirmation a été envoyé à votre adresse email.";
            $messageType = 'success';
        } else {
            $message = "Erreur lors de l'envoi du nouveau code.";
            $messageType = 'error';
        }
    }
}

/**
 * ÉTAPE 3 : validation du code
 */
if (isset($_POST['valider'])) {
    $codeSaisi = trim($_POST['verification'] ?? '');

    if (
        !isset(
            $_SESSION['register_nom'],
            $_SESSION['register_prenom'],
            $_SESSION['register_phone'],
            $_SESSION['register_email'],
            $_SESSION['register_password'],
            $_SESSION['register_code']
        )
    ) {
        $message = "Session expirée. Veuillez recommencer l'inscription.";
        $messageType = 'error';
    } elseif ($codeSaisi === '') {
        $message = "Veuillez saisir le code de vérification.";
        $messageType = 'error';
    } elseif ((string) $_SESSION['register_code'] !== $codeSaisi) {
        $message = "Code incorrect. Veuillez saisir le bon code reçu.";
        $messageType = 'error';
    } else {
        $nom = $_SESSION['register_nom'];
        $prenom = $_SESSION['register_prenom'];
        $email = $_SESSION['register_email'];
        $phone = $_SESSION['register_phone'];
        $motpasse = $_SESSION['register_password'];

        $checkUser = $bdd->prepare("SELECT id FROM user WHERE user_mail = :email LIMIT 1");
        $checkUser->execute([':email' => $email]);

        if ($checkUser->fetch()) {
            $message = "Cet email est déjà utilisé.";
            $messageType = 'error';
        } else {
            $requete = $bdd->prepare("
                INSERT INTO user (
                    user_name,
                    user_firstname,
                    user_password,
                    user_mail,
                    user_phone,
                    verification,
                    voyages_offerts_utilises,
                    role,
                    account_status
                ) VALUES (
                    :nom,
                    :prenom,
                    :motpasse,
                    :email,
                    :phone,
                    :verification,
                    :voyages_offerts_utilises,
                    :role,
                    :account_status
                )
            ");

            $success = $requete->execute([
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':motpasse' => $motpasse,
                ':email' => $email,
                ':phone' => $phone,
                ':verification' => 1,
                ':voyages_offerts_utilises' => 0,
                ':role' => 'client',
                ':account_status' => 'active'
            ]);

            if ($success) {
                $userId = $bdd->lastInsertId();

                // sessions compatibles avec ton header.php
                $_SESSION['Id_compte'] = $userId;
                $_SESSION['user_name'] = $nom;
                $_SESSION['user_firstname'] = $prenom;
                $_SESSION['user_mail'] = $email;
                $_SESSION['user_phone'] = $phone;
                $_SESSION['user_role'] = 'client';

                unset(
                    $_SESSION['register_nom'],
                    $_SESSION['register_prenom'],
                    $_SESSION['register_phone'],
                    $_SESSION['register_email'],
                    $_SESSION['register_password'],
                    $_SESSION['register_code']
                );

                header('Location: Accueil.php');
                exit;
            } else {
                $message = "Erreur lors de la création du compte.";
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[aliceblue] min-h-screen">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <main class="px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="w-full max-w-xl mx-auto mt-6 sm:mt-10 mb-12 bg-white rounded-xl shadow-lg p-6 sm:p-8 border border-gray-100">
            <h1 class="text-2xl sm:text-3xl font-bold text-center text-green-700 mb-3">
                Vérification du compte
            </h1>

            <p class="text-gray-700 text-center leading-7 mb-6">
                Un code de confirmation a été envoyé à votre adresse email.
                Veuillez le saisir dans le champ ci-dessous.
            </p>

            <?php if (!empty($message)): ?>
                <div class="mb-5 rounded-lg px-4 py-3 text-sm sm:text-base
                    <?php echo $messageType === 'success'
                        ? 'bg-green-100 text-green-700 border border-green-200'
                        : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="" class="space-y-4">
                <div>
                    <label for="verification" class="block text-gray-800 font-medium mb-2">
                        Code de vérification
                    </label>
                    <input
                        type="text"
                        id="verification"
                        name="verification"
                        required
                        maxlength="6"
                        placeholder="Entrez votre code"
                        class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                    >
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button
                        type="submit"
                        name="valider"
                        class="w-full sm:w-1/2 bg-green-600 hover:bg-green-700 text-white py-3 rounded-md font-medium transition"
                    >
                        Valider
                    </button>

                    <button
                        type="submit"
                        name="renvoyer"
                        class="w-full sm:w-1/2 bg-gray-600 hover:bg-gray-700 text-white py-3 rounded-md font-medium transition"
                    >
                        Renvoyer le code
                    </button>
                </div>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>