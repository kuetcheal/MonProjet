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
    $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
    unset($_SESSION['redirect_after_login']);

    $redirect = trim((string)$redirect);

    if ($redirect === '') {
        return 'index.php';
    }

    /*
        Sécurité : on évite les redirections externes.
        On autorise seulement les chemins internes du projet.
    */
    if (
        str_contains($redirect, "\r") ||
        str_contains($redirect, "\n") ||
        preg_match('#^https?://#i', $redirect)
    ) {
        return 'index.php';
    }

    return $redirect;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $messageError = "Veuillez remplir tous les champs.";
    } else {
        /*
            Connexion ADMIN depuis .env
        */
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_name'] = ADMIN_USERNAME;
            $_SESSION['admin_logged_in'] = true;

            header('Location: Admins/listevoyage.php');
            exit;
        }

        /*
            Connexion utilisateur classique depuis la base de données
        */
        try {
            $stmt = $pdo->prepare("
                SELECT *
                FROM utilisateurs
                WHERE username = :username OR email = :username
                LIMIT 1
            ");

            $stmt->execute([
                ':username' => $username
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $passwordFromDb = $user['password'] ?? '';

                if (password_verify($password, $passwordFromDb) || $password === $passwordFromDb) {
                    $userId = (int)($user['id'] ?? 0);

                    if ($userId <= 0) {
                        $messageError = "Compte utilisateur invalide.";
                    } else {
                        /*
                            Important :
                            Ton projet utilise parfois user_id,
                            et parfois Id_compte.
                            Donc on enregistre les deux pour éviter les bugs.
                        */
                        $_SESSION['user_id'] = $userId;
                        $_SESSION['Id_compte'] = $userId;

                        $_SESSION['username'] = $user['username'] ?? $username;
                        $_SESSION['user_email'] = $user['email'] ?? '';

                        /*
                            Si ta table utilisateurs possède une colonne role,
                            elle sera utilisée. Sinon, on met client par défaut.
                        */
                        $_SESSION['user_role'] = $user['role'] ?? 'client';

                        $redirectAfterLogin = getSafeRedirectUrl();

                        header('Location: ' . $redirectAfterLogin);
                        exit;
                    }
                }
            }

            if (empty($messageError)) {
                $messageError = "Identifiants incorrects.";
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

    <?php include 'includes/header.php'; ?>

    <main class="flex-1 flex items-center justify-center px-4 py-10 sm:py-14 md:py-16">
        <div class="w-full max-w-md bg-white shadow-xl rounded-2xl p-6 sm:p-8 md:p-9">
            <h1 class="text-2xl sm:text-3xl font-bold text-center text-gray-800 mb-6">
                Connexion
            </h1>

            <?php if (!empty($messageInfo)): ?>
                <div class="mb-5 bg-blue-50 border border-blue-200 text-blue-700 text-center px-4 py-3 rounded-lg text-sm sm:text-base">
                    <?= htmlspecialchars($messageInfo) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($messageError)): ?>
                <div class="mb-5 bg-red-500 text-white text-center px-4 py-3 rounded-lg text-sm sm:text-base">
                    <?= htmlspecialchars($messageError) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-5">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Nom d'utilisateur ou email
                    </label>
                    <input
                        type="text"
                        name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Votre identifiant">
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        Mot de passe
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="Votre mot de passe">
                </div>

                <button
                    type="submit"
                    class="w-full bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 transition duration-200">
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