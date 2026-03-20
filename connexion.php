<?php
session_start();

// Si l'utilisateur est déjà connecté
if (isset($_SESSION['Id_compte'])) {
    header('Location: Accueil.php');
    exit;
}

$erreurConnexion = "";

// Connexion base de données
$host = 'localhost';
$dbUser = 'root';
$dbPassword = '';
$database = 'bd_stock';

$conn = new mysqli($host, $dbUser, $dbPassword, $database);

if ($conn->connect_error) {
    die('Connexion échouée : ' . $conn->connect_error);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {

        // Cas ADMIN
        if ($username === 'GENERAL' && $password === '123general') {
            $_SESSION['admin_name'] = 'GENERAL';
            header('Location: Admins/listevoyage.php');
            exit;
        }

        // Recherche utilisateur
        $stmt = $conn->prepare('SELECT * FROM user WHERE user_name = ? LIMIT 1');

        if ($stmt) {

            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {

                $user = $result->fetch_assoc();

                // Vérification mot de passe hashé
                if (password_verify($password, $user['user_password'])) {

                    // Création session utilisateur
                    $_SESSION['Id_compte'] = $user['id'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['user_firstname'] = $user['user_firstname'];
                    $_SESSION['user_mail'] = $user['user_mail'];
                    $_SESSION['user_phone'] = $user['user_phone'];

                    header('Location: Accueil.php');
                    exit;
                } else {
                    $erreurConnexion = "Mot de passe incorrect.";
                }
            } else {
                $erreurConnexion = "Nom d'utilisateur incorrect.";
            }

            $stmt->close();
        } else {
            $erreurConnexion = "Erreur serveur.";
        }
    } else {
        $erreurConnexion = "Veuillez remplir tous les champs.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body style="background-color: aliceblue;">

    <?php include 'includes/header.php'; ?>

    <main class="px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="w-full max-w-xl mx-auto mt-6 sm:mt-10 mb-12 bg-[#008000] rounded-md shadow-lg p-5 sm:p-6 md:p-8">
            <h1 class="mb-6 text-center text-white text-3xl sm:text-4xl font-bold">
                Connexion
            </h1>

            <form method="post" action="connexion.php" class="flex flex-col">
                <label class="mb-2 text-white font-medium text-base">
                    Nom d'utilisateur :
                </label>

                <input
                    type="text"
                    name="username"
                    placeholder="ALEX"
                    required
                    class="w-full mb-5 rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                >

                <label class="mb-2 text-white font-medium text-base">
                    Mot de passe :
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="********"
                    required
                    class="w-full mb-5 rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                >

                <button
                    type="submit"
                    class="w-full bg-[#4CAF50] hover:bg-[#3e8e41] text-white py-3 rounded-md text-base font-medium transition duration-200"
                >
                    Se connecter
                </button>

                <?php if (!empty($erreurConnexion)) : ?>
                    <p class="mt-4 text-center text-red-200 font-semibold">
                        <?= htmlspecialchars($erreurConnexion) ?>
                    </p>
                <?php endif; ?>

                <p class="mt-5 text-white text-base leading-7">
                    Oupps, un problème ?
                    <a href="forgetpassword.php" class="text-white font-bold underline hover:text-green-200 transition">
                        Mot de passe oublié
                    </a>
                </p>

                <p class="mt-3 text-white text-base leading-7">
                    Pas encore de compte ?
                    <a href="inscription.php" class="text-white font-bold underline hover:text-green-200 transition">
                        S'inscrire
                    </a>
                </p>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>

</html>