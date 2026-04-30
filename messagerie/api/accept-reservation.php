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
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT 
            r.id_reservation,
            r.user_id AS client_id,
            r.idVoyage AS voyage_id,
            r.Etat,
            r.statut_demande,
            v.chauffeur_id
        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        WHERE r.id_reservation = :reservation_id
        FOR UPDATE
    ");

    $stmt->execute([
        ':reservation_id' => $reservationId
    ]);

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Réservation introuvable.'
        ]);
        exit;
    }

    if (empty($reservation['chauffeur_id'])) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Aucun chauffeur n’est associé à ce trajet.'
        ]);
        exit;
    }

    if ((int) $reservation['chauffeur_id'] !== $chauffeurId) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Vous n’êtes pas autorisé à accepter cette réservation.'
        ]);
        exit;
    }

    if ($reservation['statut_demande'] !== 'en_attente') {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Cette réservation a déjà été traitée.'
        ]);
        exit;
    }

    $clientId = (int) $reservation['client_id'];
    $voyageId = (int) $reservation['voyage_id'];

    $updateReservation = $pdo->prepare("
        UPDATE reservation
        SET statut_demande = 'acceptee'
        WHERE id_reservation = :reservation_id
    ");

    $updateReservation->execute([
        ':reservation_id' => $reservationId
    ]);

    $checkConversation = $pdo->prepare("
        SELECT id
        FROM conversations
        WHERE reservation_id = :reservation_id
        LIMIT 1
    ");

    $checkConversation->execute([
        ':reservation_id' => $reservationId
    ]);

    $existingConversation = $checkConversation->fetch(PDO::FETCH_ASSOC);

    if ($existingConversation) {
        $conversationId = (int) $existingConversation['id'];
    } else {
        $createConversation = $pdo->prepare("
            INSERT INTO conversations (
                reservation_id,
                voyage_id,
                client_id,
                chauffeur_id,
                statut,
                created_at,
                updated_at
            ) VALUES (
                :reservation_id,
                :voyage_id,
                :client_id,
                :chauffeur_id,
                'active',
                NOW(),
                NOW()
            )
        ");

        $createConversation->execute([
            ':reservation_id' => $reservationId,
            ':voyage_id' => $voyageId,
            ':client_id' => $clientId,
            ':chauffeur_id' => $chauffeurId
        ]);

        $conversationId = (int) $pdo->lastInsertId();
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Réservation acceptée. Conversation créée.',
        'conversation_id' => $conversationId
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}