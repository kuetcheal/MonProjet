<?php
session_start();

require_once __DIR__ . '/../config.php';

if (isset($_POST['id_voyage'])) {
    $id_voyage = (int) $_POST['id_voyage'];

    $requete_suppression = "DELETE FROM voyage WHERE idVoyage = :id_voyage";
    $statement = $pdo->prepare($requete_suppression);
    $statement->bindValue(':id_voyage', $id_voyage, PDO::PARAM_INT);
    $resultat = $statement->execute();

    if ($resultat) {
        header("Location: listevoyadmin.php?success=delete");
        exit;
    }

    header("Location: listevoyadmin.php?error=delete");
    exit;
}

header("Location: listevoyadmin.php?error=missing_id");
exit;