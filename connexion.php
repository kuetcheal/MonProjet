<?php
session_start();

require_once __DIR__ . '/config.php';

$messageError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $messageError = "Veuillez remplir tous les champs.";
    } else {
        // Cas ADMIN depuis .env
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_name'] = ADMIN_USERNAME;
            $_SESSION['admin_logged_in'] = true;

            header('Location: Admins/listevoyage.php');
            exit;
        }

        // Cas utilisateur classique depuis la base de données
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
                $passwordFromDb = $user['password'];

                if (password_verify($password, $passwordFromDb) || $password === $passwordFromDb) {
                    $_SESSION['user_id'] = $user['id'] ?? null;
                    $_SESSION['username'] = $user['username'] ?? $username;
                    $_SESSION['user_email'] = $user['email'] ?? '';

                    header('Location: index.php');
                    exit;
                }
            }

            $messageError = "Identifiants incorrects.";
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