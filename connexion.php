<?php
session_start();

require_once __DIR__ . '/config.php';

$messageError = '';
$messageInfo = '';

if (!empty($_SESSION['redirect_after_login'])) {
    $messageInfo = "Connectez-vous pour continuer votre demande de covoiturage.";
}

function getSafeRedirectUrl(): string
{
    $redirect = $_SESSION['redirect_after_login'] ?? 'Accueil.php';
    unset($_SESSION['redirect_after_login']);

    $redirect = trim((string) $redirect);

    if ($redirect === '') {
        return 'Accueil.php';
    }

    /*
        Sécurité : on évite les redirections externes.
    */
    if (
        strpos($redirect, "\r") !== false ||
        strpos($redirect, "\n") !== false ||
        preg_match('#^https?://#i', $redirect)
    ) {
        return 'Accueil.php';
    }

    return $redirect;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $messageError = "Veuillez saisir votre email et votre mot de passe.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageError = "Veuillez saisir une adresse email valide.";
    } else {

        /*
            Connexion ADMIN depuis .env

            Dans ton fichier .env, tu dois avoir :
            ADMIN_USERNAME=admins@gmail.com
            ADMIN_PASSWORD=123general
        */
        if ($email === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_name'] = ADMIN_USERNAME;
            $_SESSION['admin_email'] = ADMIN_USERNAME;
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_role'] = 'admin';

            header('Location: Admins/listevoyage.php');
            exit;
        }

        try {
            /*
                Connexion utilisateur classique
                Table utilisée par ton inscription : user
            */
            $stmt = $pdo->prepare("
                SELECT *
                FROM user
                WHERE user_mail = :email
                LIMIT 1
            ");

            $stmt->execute([
                ':email' => $email
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $passwordFromDb = $user['user_password'] ?? '';

                if (password_verify($password, $passwordFromDb) || $password === $passwordFromDb) {
                    $userId = (int) ($user['id'] ?? 0);

                    if ($userId <= 0) {
                        $messageError = "Compte utilisateur invalide.";
                    } elseif (($user['account_status'] ?? '') !== 'active') {
                        $messageError = "Votre compte n'est pas encore actif.";
                    } elseif ((int) ($user['verification'] ?? 0) !== 1) {
                        $messageError = "Votre compte n'est pas encore vérifié.";
                    } else {
                        /*
                            Sessions compatibles avec ton projet
                        */
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['Id_compte'] = $userId;

                        $_SESSION['user_name'] = $user['user_name'] ?? '';
                        $_SESSION['user_firstname'] = $user['user_firstname'] ?? '';
                        $_SESSION['user_mail'] = $user['user_mail'] ?? '';
                        $_SESSION['user_email'] = $user['user_mail'] ?? '';
                        $_SESSION['user_phone'] = $user['user_phone'] ?? '';
                        $_SESSION['user_role'] = $user['role'] ?? 'client';

                        $redirectAfterLogin = getSafeRedirectUrl();

                        header('Location: ' . $redirectAfterLogin);
                        exit;
                    }
                } else {
                    $messageError = "Email ou mot de passe incorrect.";
                }
            } else {
                $messageError = "Email ou mot de passe incorrect.";
            }
        } catch (PDOException $e) {
            $messageError = "Erreur lors de la connexion.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[aliceblue] min-h-screen flex flex-col">

    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <main class="flex-1 flex items-center justify-center px-4 py-10 sm:py-14 md:py-16">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-6 sm:p-8 md:p-9">
            <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6">
                Connexion
            </h1>

            <?php if (!empty($messageInfo)): ?>
                <div class="mb-5 bg-blue-50 border border-blue-200 text-blue-700 text-center px-4 py-3 rounded-lg text-sm sm:text-base">
                    <?= htmlspecialchars($messageInfo, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($messageError)): ?>
                <div class="mb-5 bg-red-500 text-white text-center px-4 py-3 rounded-lg text-sm sm:text-base">
                    <?= htmlspecialchars($messageError, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="nom@gmail.com"
                    >
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-semibold mb-2">
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Votre mot de passe"
                    >
                </div>

                <p class="text-right -mt-2">
                    <a href="forgotPassword.php" class="text-green-600 font-semibold text-sm hover:underline">
                        Mot de passe oublié ?
                    </a>
                </p>

                <button
                    type="submit"
                    class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition duration-200"
                >
                    Se connecter
                </button>
            </form>

            <p class="text-center text-sm text-gray-500 mt-6">
                Pas encore de compte ?
                <a href="inscription.php" class="text-green-600 font-semibold hover:underline">
                    Créer un compte
                </a>
            </p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>