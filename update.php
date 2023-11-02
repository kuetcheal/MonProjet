<?php
    if (isset($_POST['id_voyage']) && isset($_POST['confirmPassword']) && isset($_POST['newPassword'])) {
        $password = $_POST['password'];
        $newPassword = $_POST['newPassword'];
        $confirmPassword = $_POST['confirmPassword'];

        // Connectez-vous à la base de données

        try {
            $conn = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
        } catch (\Throwable $th) {
            echo("Echec de connexion");
            exit();
        }

        // Effectuez la requête de récupération du voyage

        $requete_recuperation = "SELECT * FROM user WHERE user_password = :password";
        $statement = $conn->prepare($requete_recuperation);
        $statement->bindValue(':password', $password);
        $resultat = $statement->execute();

        if ($resultat && password_verify($newPassword, $resultat['user_password'])) {
            // Le nouveau mot de passe correspond au mot de passe stocké
            echo("Connexion réussie");
        } else {
            // Le nouveau mot de passe ne correspond pas ou l'utilisateur n'existe pas
            header("Location: errorconnexion.php");
            exit();
        }
       
    }
?>