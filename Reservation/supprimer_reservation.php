<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['id_reservation'])) {
    $id_reservation = (int) $data['id_reservation'];

    $query = "DELETE FROM reservation WHERE id_reservation = ?";
    $stmt = $pdo->prepare($query);

    if ($stmt->execute([$id_reservation])) {
        echo json_encode([
            "status" => "success",
            "message" => "Réservation supprimée avec succès."
        ]);
        exit;
    }

    echo json_encode([
        "status" => "error",
        "message" => "Erreur lors de la suppression."
    ]);
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "ID de réservation manquant."
]);
exit;