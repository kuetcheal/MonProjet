<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$password = "";
$database = "bd_stock";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die(json_encode(["status" => "error", "message" => "Erreur de connexion à la base de données."]));
}

// Lire les données JSON envoyées
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id_reservation'])) {
    $id_reservation = intval($data['id_reservation']);
    
    $query = "DELETE FROM reservation WHERE id_reservation = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_reservation);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(["status" => "success", "message" => "Réservation supprimée avec succès."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur lors de la suppression."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ID de réservation manquant."]);
}

mysqli_close($conn);
