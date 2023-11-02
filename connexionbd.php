<?php
    $host = "localhost"; // nom d'hôte
    $user = "utilisateur"; // nom d'utilisateur
    $password = "motdepasse"; // mot de passe
    $database = "basededonnees"; // nom de la base de données

    // Connexion à la base de données MySQLi
    $conn = mysqli_connect($host, $user, $password, $database);

    // Vérifier la connexion
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    echo "Connected successfully";
?>