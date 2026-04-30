<?php
session_start();
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

$chauffeurId = (int) $_SESSION['user_id'];
$reservationId = isset($_POST['reservation_id']) ? (int) $_POST['reservation_id'] : 0;

if ($reservationId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Réservation invalide.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        SET r.statut_demande = 'refusee'
        WHERE r.id_reservation = :reservation_id
        AND v.chauffeur_id = :chauffeur_id
        AND r.statut_demande = 'en_attente'
    ");

    $stmt->execute([
        ':reservation_id' => $reservationId,
        ':chauffeur_id' => $chauffeurId
    ]);

    if ($stmt->rowCount() === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Impossible de refuser cette réservation.'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Réservation refusée.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}