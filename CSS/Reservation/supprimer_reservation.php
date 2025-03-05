<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "bd_stock";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Échec de la connexion à la base de données."]));
}

if (isset($_POST['id_reservation'])) {
    $id_reservation = $_POST['id_reservation'];

    $query = "DELETE FROM reservation WHERE id_reservation = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_reservation);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Réservation supprimée avec succès."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur lors de la suppression."]);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>
