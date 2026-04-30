<?php
session_start();

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

$chauffeurId = (int)($_SESSION['user_id'] ?? $_SESSION['Id_compte'] ?? 0);

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
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT 
            r.id_reservation,
            r.user_id AS client_id,
            r.idVoyage AS voyage_id,
            r.statut_demande,
            r.Etat,
            v.chauffeur_id,
            v.nombrePlaces,
            v.nombre_places_disponibles
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

    if ((int)$reservation['chauffeur_id'] !== $chauffeurId) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Vous n’êtes pas autorisé à accepter cette demande.'
        ]);
        exit;
    }

    if (($reservation['statut_demande'] ?? '') !== 'en_attente') {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Cette demande a déjà été traitée.'
        ]);
        exit;
    }

    $placesDisponibles = $reservation['nombre_places_disponibles'] !== null
        ? (int)$reservation['nombre_places_disponibles']
        : (int)$reservation['nombrePlaces'];

    if ($placesDisponibles <= 0) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Il n’y a plus de places disponibles pour ce trajet.'
        ]);
        exit;
    }

    $clientId = (int)$reservation['client_id'];
    $voyageId = (int)$reservation['voyage_id'];

    /*
        1. On accepte la demande.
        Etat = 1 peut signifier acceptée dans ton ancien système.
    */
    $updateReservation = $pdo->prepare("
        UPDATE reservation
        SET statut_demande = 'acceptee',
            Etat = 1
        WHERE id_reservation = :reservation_id
    ");

    $updateReservation->execute([
        ':reservation_id' => $reservationId
    ]);

    /*
        2. On diminue le nombre de places disponibles.
    */
    $updatePlaces = $pdo->prepare("
        UPDATE voyage
        SET nombre_places_disponibles = GREATEST(
            COALESCE(nombre_places_disponibles, nombrePlaces) - 1,
            0
        )
        WHERE idVoyage = :voyage_id
    ");

    $updatePlaces->execute([
        ':voyage_id' => $voyageId
    ]);

    /*
        3. On vérifie si une conversation existe déjà.
    */
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
        $conversationId = (int)$existingConversation['id'];
    } else {
        /*
            4. On crée la conversation entre le client et le chauffeur.
        */
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

        $conversationId = (int)$pdo->lastInsertId();
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Demande acceptée. La conversation a été créée.',
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