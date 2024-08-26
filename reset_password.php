<?php
session_start();
require __DIR__.'/vendor/autoload.php';
use PDO;

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Connexion à la base de données
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    // Vérification du jeton
    $stmt = $bdd->prepare("SELECT * FROM user WHERE reset_token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (isset($_POST['password']) && isset($_POST['confirm_password'])) {
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];

            if ($password === $confirm_password) {
                // Hash du nouveau mot de passe
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Mise à jour du mot de passe dans la base de données
                $stmt = $bdd->prepare("UPDATE user SET user_password = :password, reset_token = NULL WHERE reset_token = :token");
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':token', $token);
                $stmt->execute();

                echo 'Votre mot de passe a été réinitialisé avec succès.';
            } else {
                echo 'Les mots de passe ne correspondent pas.';
            }
        }
    } else {
        echo 'Lien de réinitialisation invalide.';
    }
} else {
    echo 'Token manquant.';
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