<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

$villeDepart = trim($_POST['villeDepart'] ?? '');
$villeArrivee = trim($_POST['villeArrivee'] ?? '');
$jourDepart = trim($_POST['jourDepart'] ?? '');

if ($villeDepart && $villeArrivee && $jourDepart) {
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM voyage 
        WHERE villeDepart = ? 
        AND villeArrivee = ? 
        AND jourDepart = ?
    ");

    $stmt->execute([$villeDepart, $villeArrivee, $jourDepart]);
    $count = (int) $stmt->fetchColumn();

    if ($count > 0) {
        echo json_encode(['success' => true, 'count' => $count]);
        exit;
    }

    echo json_encode([
        'success' => false,
        'message' => 'Aucun trajet trouvé pour cette combinaison.'
    ]);
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Données incomplètes.'
]);
exit;
?>