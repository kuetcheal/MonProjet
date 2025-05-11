<?php
header('Content-Type: application/json');
$host = "localhost";
$user = "root";
$password = "";
$database = "bd_stock";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    echo json_encode(['success' => false, 'message' => "Erreur de connexion"]);
    exit;
}

$villeDepart = $_POST['villeDepart'] ?? '';
$villeArrivee = $_POST['villeArrivee'] ?? '';
$jourDepart = $_POST['jourDepart'] ?? '';

if ($villeDepart && $villeArrivee && $jourDepart) {
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM voyage WHERE villeDepart = ? AND villeArrivee = ? AND jourDepart = ?");
    mysqli_stmt_bind_param($stmt, "sss", $villeDepart, $villeArrivee, $jourDepart);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($count > 0) {
        echo json_encode(['success' => true, 'count' => $count]);
    } else {
        echo json_encode(['success' => false, 'message' => "Aucun trajet trouvé pour cette combinaison."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Données incomplètes."]);
}
?>
