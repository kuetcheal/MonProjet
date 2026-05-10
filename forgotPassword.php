<?php
session_start();

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/vendor/autoload.php';

use Mailjet\Resources;

$message = '';
$messageType = '';
$step = $_SESSION['forgot_step'] ?? 'email';

/**
 * Génère un code de réinitialisation à 6 chiffres
 */
function genererCodeReset(): string
{
    return (string) rand(100000, 999999);
}

/**
 * Envoie le code de réinitialisation par Mailjet
 */
function envoyerCodeResetMailjet(string $email, string $code): bool
{
    try {
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
                            'Name' => $email,
                        ],
                    ],
                    'Subject' => 'Réinitialisation de votre mot de passe',
                    'TextPart' => "Voici votre code de réinitialisation : $code",
                    'HTMLPart' => "
                        <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #222;'>
                            <h2 style='color: #008000;'>Réinitialisation de mot de passe</h2>
                            <p>Bonjour,</p>
                            <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
                            <p>Voici votre code de vérification :</p>
                            <p style='font-size: 28px; font-weight: bold; color: #008000; letter-spacing: 3px;'>$code</p>
                            <p>Ce code est valable pendant 15 minutes.</p>
                            <p>Si vous n'êtes pas à l'origine de cette demande, vous pouvez ignorer cet email.</p>
                        </div>
                    ",
                ],
            ],
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);

        return $response->success();

    } catch (Throwable $e) {
        return false;
    }
}

/**
 * Recherche l'utilisateur par email.
 * Cette fonction est compatible avec tes deux structures possibles :
 * - table utilisateurs : email / password
 * - table user : user_mail / user_password
 */
function trouverUtilisateurParEmail(PDO $pdo, string $email): ?array
{
    try {
        $stmt = $pdo->prepare("
            SELECT id, email, password
            FROM utilisateurs
            WHERE email = :email
            LIMIT 1
        ");

        $stmt->execute([
            ':email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return [
                'table' => 'utilisateurs',
                'id' => $user['id'],
                'email' => $user['email'],
            ];
        }
    } catch (PDOException $e) {
        // On continue vers l'autre table si celle-ci n'existe pas
    }

    try {
        $stmt = $pdo->prepare("
            SELECT id, user_mail, user_password
            FROM user
            WHERE user_mail = :email
            LIMIT 1
        ");

        $stmt->execute([
            ':email' => $email
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            return [
                'table' => 'user',
                'id' => $user['id'],
                'email' => $user['user_mail'],
            ];
        }
    } catch (PDOException $e) {
        // Aucune table trouvée
    }

    return null;
}

/**
 * Met à jour le mot de passe selon la table trouvée
 */
function mettreAJourMotDePasse(PDO $pdo, string $table, int $userId, string $passwordHash): bool
{
    if ($table === 'utilisateurs') {
        $stmt = $pdo->prepare("
            UPDATE utilisateurs
            SET password = :password
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([
            ':password' => $passwordHash,
            ':id' => $userId
        ]);
    }

    if ($table === 'user') {
        $stmt = $pdo->prepare("
            UPDATE user
            SET user_password = :password
            WHERE id = :id
            LIMIT 1
        ");

        return $stmt->execute([
            ':password' => $passwordHash,
            ':id' => $userId
        ]);
    }

    return false;
}

/**
 * Étape 1 : envoi du code
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'send_code') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $message = "Veuillez saisir votre adresse email.";
        $messageType = 'error';
        $step = 'email';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Veuillez saisir une adresse email valide.";
        $messageType = 'error';
        $step = 'email';
    } else {
        $utilisateur = trouverUtilisateurParEmail($pdo, $email);

        if (!$utilisateur) {
            $message = "Aucun compte n'est associé à cette adresse email.";
            $messageType = 'error';
            $step = 'email';
        } else {
            $code = genererCodeReset();

            $_SESSION['forgot_email'] = $email;
            $_SESSION['forgot_code'] = $code;
            $_SESSION['forgot_expires_at'] = time() + (15 * 60);
            $_SESSION['forgot_step'] = 'reset';

            if (envoyerCodeResetMailjet($email, $code)) {
                $message = "Un code de réinitialisation a été envoyé à votre adresse email.";
                $messageType = 'success';
                $step = 'reset';
            } else {
                $message = "Erreur lors de l'envoi de l'email. Veuillez réessayer.";
                $messageType = 'error';
                $step = 'email';
                unset(
                    $_SESSION['forgot_email'],
                    $_SESSION['forgot_code'],
                    $_SESSION['forgot_expires_at'],
                    $_SESSION['forgot_step']
                );
            }
        }
    }
}

/**
 * Étape 2 : vérification du code + nouveau mot de passe
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset_password') {
    $codeSaisi = trim($_POST['code'] ?? '');
    $newPassword = trim($_POST['new_password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (
        empty($_SESSION['forgot_email']) ||
        empty($_SESSION['forgot_code']) ||
        empty($_SESSION['forgot_expires_at'])
    ) {
        $message = "Session expirée. Veuillez recommencer la procédure.";
        $messageType = 'error';
        $step = 'email';
        $_SESSION['forgot_step'] = 'email';
    } elseif (time() > (int) $_SESSION['forgot_expires_at']) {
        $message = "Le code a expiré. Veuillez demander un nouveau code.";
        $messageType = 'error';
        $step = 'email';

        unset(
            $_SESSION['forgot_email'],
            $_SESSION['forgot_code'],
            $_SESSION['forgot_expires_at'],
            $_SESSION['forgot_step']
        );
    } elseif ($codeSaisi === '') {
        $message = "Veuillez saisir le code reçu par email.";
        $messageType = 'error';
        $step = 'reset';
    } elseif ($codeSaisi !== (string) $_SESSION['forgot_code']) {
        $message = "Code incorrect. Veuillez vérifier le code reçu.";
        $messageType = 'error';
        $step = 'reset';
    } elseif ($newPassword === '' || $confirmPassword === '') {
        $message = "Veuillez remplir les deux champs de mot de passe.";
        $messageType = 'error';
        $step = 'reset';
    } elseif (strlen($newPassword) < 6) {
        $message = "Le mot de passe doit contenir au moins 6 caractères.";
        $messageType = 'error';
        $step = 'reset';
    } elseif ($newPassword !== $confirmPassword) {
        $message = "Les deux mots de passe ne correspondent pas.";
        $messageType = 'error';
        $step = 'reset';
    } else {
        $email = $_SESSION['forgot_email'];
        $utilisateur = trouverUtilisateurParEmail($pdo, $email);

        if (!$utilisateur) {
            $message = "Compte introuvable. Veuillez recommencer.";
            $messageType = 'error';
            $step = 'email';
        } else {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $updated = mettreAJourMotDePasse(
                $pdo,
                $utilisateur['table'],
                (int) $utilisateur['id'],
                $passwordHash
            );

            if ($updated) {
                unset(
                    $_SESSION['forgot_email'],
                    $_SESSION['forgot_code'],
                    $_SESSION['forgot_expires_at'],
                    $_SESSION['forgot_step']
                );

                $message = "Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
                $messageType = 'success';
                $step = 'done';
            } else {
                $message = "Erreur lors de la mise à jour du mot de passe.";
                $messageType = 'error';
                $step = 'reset';
            }
        }
    }
}

/**
 * Renvoi du code
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'resend_code') {
    if (empty($_SESSION['forgot_email'])) {
        $message = "Session expirée. Veuillez saisir votre email à nouveau.";
        $messageType = 'error';
        $step = 'email';
    } else {
        $email = $_SESSION['forgot_email'];
        $code = genererCodeReset();

        $_SESSION['forgot_code'] = $code;
        $_SESSION['forgot_expires_at'] = time() + (15 * 60);
        $_SESSION['forgot_step'] = 'reset';

        if (envoyerCodeResetMailjet($email, $code)) {
            $message = "Un nouveau code a été envoyé à votre adresse email.";
            $messageType = 'success';
            $step = 'reset';
        } else {
            $message = "Erreur lors du renvoi du code.";
            $messageType = 'error';
            $step = 'reset';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[aliceblue] min-h-screen">

    <?php include 'includes/header.php'; ?>

    <main class="px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="w-full max-w-xl mx-auto mt-6 sm:mt-10 mb-12 bg-[#008000] rounded-md shadow-lg p-5 sm:p-6 md:p-8">

            <h2 class="text-center text-white text-2xl sm:text-3xl font-bold mb-4">
                Mot de passe oublié
            </h2>

            <hr class="border-white/30 mb-6">

            <p class="text-white/90 text-center text-base leading-7 mb-6">
                Entrez votre adresse email. Nous vous enverrons un code pour réinitialiser votre mot de passe.
            </p>

            <?php if (!empty($message)): ?>
                <div class="mb-5 rounded-md px-4 py-3 text-sm sm:text-base
                    <?php echo $messageType === 'success'
                        ? 'bg-green-100 text-green-700 border border-green-200'
                        : 'bg-red-100 text-red-700 border border-red-200'; ?>">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($step === 'email'): ?>

                <form method="POST" action="" class="flex flex-col gap-4">
                    <input type="hidden" name="action" value="send_code">

                    <div>
                        <label for="email" class="block text-white font-medium mb-2">
                            Adresse email :
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="nom@gmail.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                            required
                            class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-[#4CAF50] hover:bg-[#3e8e41] text-white py-3 rounded-md text-base font-medium transition duration-200 mt-2"
                    >
                        Envoyer le code
                    </button>

                    <p class="mt-4 text-white text-base leading-7 text-center">
                        Vous vous souvenez de votre mot de passe ?
                        <a href="connexion.php" class="text-white font-bold underline hover:text-green-200 transition">
                            Se connecter
                        </a>
                    </p>
                </form>

            <?php elseif ($step === 'reset'): ?>

                <form method="POST" action="" class="flex flex-col gap-4">
                    <input type="hidden" name="action" value="reset_password">

                    <div>
                        <label for="code" class="block text-white font-medium mb-2">
                            Code reçu par email :
                        </label>

                        <input
                            type="text"
                            id="code"
                            name="code"
                            maxlength="6"
                            placeholder="Entrez le code"
                            required
                            class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                        >
                    </div>

                    <div>
                        <label for="new_password" class="block text-white font-medium mb-2">
                            Nouveau mot de passe :
                        </label>

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            placeholder="Nouveau mot de passe"
                            required
                            class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                        >
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-white font-medium mb-2">
                            Confirmer le mot de passe :
                        </label>

                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Confirmez le mot de passe"
                            required
                            class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-[#4CAF50] hover:bg-[#3e8e41] text-white py-3 rounded-md text-base font-medium transition duration-200 mt-2"
                    >
                        Réinitialiser le mot de passe
                    </button>
                </form>

                <form method="POST" action="" class="mt-4">
                    <input type="hidden" name="action" value="resend_code">

                    <button
                        type="submit"
                        class="w-full bg-gray-700 hover:bg-gray-800 text-white py-3 rounded-md text-base font-medium transition duration-200"
                    >
                        Renvoyer le code
                    </button>
                </form>

                <p class="mt-4 text-white text-base leading-7 text-center">
                    Retour à la connexion ?
                    <a href="connexion.php" class="text-white font-bold underline hover:text-green-200 transition">
                        Se connecter
                    </a>
                </p>

            <?php elseif ($step === 'done'): ?>

                <div class="text-center">
                    <a
                        href="connexion.php"
                        class="inline-block w-full bg-[#4CAF50] hover:bg-[#3e8e41] text-white py-3 rounded-md text-base font-medium transition duration-200"
                    >
                        Aller à la connexion
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>