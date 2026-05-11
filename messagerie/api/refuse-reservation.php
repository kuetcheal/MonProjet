<?php
session_start();

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

$chauffeurId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($chauffeurId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

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
        SET r.statut_demande = 'refusee',
            r.Etat = 2
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
            'message' => 'Impossible de refuser cette demande. Elle a peut-être déjà été traitée.'
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Demande refusée.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}



