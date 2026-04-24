<?php
session_start();

require_once __DIR__ . '/config.php';

if (isset($_POST['password'], $_POST['newPassword'], $_POST['confirmPassword'])) {
    $password = trim($_POST['password']);
    $newPassword = trim($_POST['newPassword']);
    $confirmPassword = trim($_POST['confirmPassword']);

    if ($newPassword !== $confirmPassword) {
        header("Location: errorconnexion.php");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_password = :password LIMIT 1");
    $stmt->execute([
        ':password' => $password
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $update = $pdo->prepare("
            UPDATE user
            SET user_password = :newPassword
            WHERE id = :id
        ");

        $update->execute([
            ':newPassword' => $hashedPassword,
            ':id' => $user['id']
        ]);

        echo "Mot de passe modifié avec succès.";
        exit();
    }

    header("Location: errorconnexion.php");
    exit();
}
?>