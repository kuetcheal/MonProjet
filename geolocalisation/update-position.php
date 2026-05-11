<?php
session_start();

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');

$chauffeurId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($chauffeurId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    echo json_encode([
        'success' => false,
        'message' => 'Données JSON invalides.'
    ]);
    exit;
}

$idVoyage = isset($input['idVoyage']) ? (int)$input['idVoyage'] : 0;
$latitude = isset($input['latitude']) ? (float)$input['latitude'] : null;
$longitude = isset($input['longitude']) ? (float)$input['longitude'] : null;
$speed = isset($input['speed']) && $input['speed'] !== null ? (float)$input['speed'] : null;

if ($idVoyage <= 0 || $latitude === null || $longitude === null) {
    echo json_encode([
        'success' => false,
        'message' => 'Paramètres manquants.'
    ]);
    exit;
}

if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
    echo json_encode([
        'success' => false,
        'message' => 'Coordonnées invalides.'
    ]);
    exit;
}

try {
    $check = $pdo->prepare("
        SELECT idVoyage
        FROM voyage
        WHERE idVoyage = :idVoyage
        AND chauffeur_id = :chauffeurId
        LIMIT 1
    ");

    $check->execute([
        ':idVoyage' => $idVoyage,
        ':chauffeurId' => $chauffeurId
    ]);

    if (!$check->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode([
            'success' => false,
            'message' => 'Accès refusé à ce trajet.'
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO vehicle_positions (
            id_trajet,
            latitude,
            longitude,
            vitesse,
            date_position
        ) VALUES (
            :id_trajet,
            :latitude,
            :longitude,
            :vitesse,
            NOW()
        )
    ");

    $stmt->execute([
        ':id_trajet' => $idVoyage,
        ':latitude' => $latitude,
        ':longitude' => $longitude,
        ':vitesse' => $speed
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Position enregistrée.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}