<?php
session_start();

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['tracking_access']) || empty($_SESSION['tracking_reservation'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Accès au suivi non autorisé.'
    ]);
    exit;
}

$tracking = $_SESSION['tracking_reservation'];

$idReservation = isset($tracking['id_reservation']) ? (int)$tracking['id_reservation'] : 0;
$idVoyage = isset($tracking['idVoyage']) ? (int)$tracking['idVoyage'] : 0;

if ($idReservation <= 0 || $idVoyage <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Session de suivi invalide.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            r.id_reservation,
            r.type_reservation,
            r.statut_demande,
            r.statut_paiement,
            r.idVoyage,
            v.villeDepart,
            v.villeArrivee,
            v.chauffeur_id
        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        WHERE r.id_reservation = :idReservation
        AND r.idVoyage = :idVoyage
        LIMIT 1
    ");

    $stmt->execute([
        ':idReservation' => $idReservation,
        ':idVoyage' => $idVoyage
    ]);

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        echo json_encode([
            'success' => false,
            'message' => 'Réservation introuvable.'
        ]);
        exit;
    }

    $typeReservation = $reservation['type_reservation'] ?? 'bus';
    $statutDemande = $reservation['statut_demande'] ?? '';
    $statutPaiement = $reservation['statut_paiement'] ?? '';

    if ($typeReservation === 'covoiturage') {
        $autorise = (
            $statutDemande === 'acceptee'
            && in_array($statutPaiement, ['payee', 'offerte'], true)
        );

        if (!$autorise) {
            echo json_encode([
                'success' => false,
                'message' => 'Le suivi sera disponible après acceptation et paiement du covoiturage.'
            ]);
            exit;
        }
    } else {
        if (!in_array($statutPaiement, ['payee', 'offerte'], true)) {
            echo json_encode([
                'success' => false,
                'message' => 'Le suivi sera disponible après paiement.'
            ]);
            exit;
        }
    }

    $positionStmt = $pdo->prepare("
        SELECT 
            id_position,
            id_trajet,
            latitude,
            longitude,
            vitesse,
            date_position
        FROM vehicle_positions
        WHERE id_trajet = :idVoyage
        ORDER BY date_position DESC, id_position DESC
        LIMIT 1
    ");

    $positionStmt->execute([
        ':idVoyage' => $idVoyage
    ]);

    $position = $positionStmt->fetch(PDO::FETCH_ASSOC);

    if (!$position) {
        echo json_encode([
            'success' => false,
            'position_available' => false,
            'message' => 'Le chauffeur n’a pas encore partagé sa position.'
        ]);
        exit;
    }

    $updatedAt = strtotime($position['date_position']);
    $ageSeconds = time() - $updatedAt;

    echo json_encode([
        'success' => true,
        'position_available' => true,
        'latitude' => (float)$position['latitude'],
        'longitude' => (float)$position['longitude'],
        'vitesse' => $position['vitesse'] !== null ? (float)$position['vitesse'] : null,
        'updated_at' => $position['date_position'],
        'age_seconds' => $ageSeconds,
        'is_fresh' => $ageSeconds <= 120,
        'voyage' => [
            'idVoyage' => (int)$reservation['idVoyage'],
            'villeDepart' => $reservation['villeDepart'],
            'villeArrivee' => $reservation['villeArrivee']
        ]
    ], JSON_UNESCAPED_UNICODE);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}