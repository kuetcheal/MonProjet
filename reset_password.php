<?php
session_start();

require_once __DIR__ . '/config.php';

$message = '';

if (isset($_GET['token'])) {
    $token = trim($_GET['token']);

    $stmt = $pdo->prepare("SELECT * FROM user WHERE reset_token = :token LIMIT 1");
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (isset($_POST['password'], $_POST['confirm_password'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password === $confirm_password) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("
                    UPDATE user 
                    SET user_password = :password, reset_token = NULL 
                    WHERE reset_token = :token
                ");

                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':token', $token);
                $stmt->execute();

                $message = 'Votre mot de passe a été réinitialisé avec succès.';
            } else {
                $message = 'Les mots de passe ne correspondent pas.';
            }
        }
    } else {
        $message = 'Lien de réinitialisation invalide.';
    }
} else {
    $message = 'Token manquant.';
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe</title>
</head>

<body>
    <h2>Réinitialiser votre mot de passe</h2>
    <form action="" method="POST">
        <label for="password">Nouveau mot de passe:</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Ressaisir le mot de passe:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Valider</button>
    </form>
</body>

</html>