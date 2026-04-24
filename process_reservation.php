<?php
session_start();

require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');

if (isset($_POST["mail"], $_POST["Numero_reservation"])) {
    $mail = trim($_POST['mail']);
    $reservation = trim($_POST['Numero_reservation']);

    $query = "SELECT id_reservation FROM reservation WHERE email = ? AND Numero_reservation = ? LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$mail, $reservation]);

    $valeur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($valeur) {
        $_SESSION['id_reservation'] = $valeur['id_reservation'];
        $id_reservation = (int) $valeur['id_reservation'];

        echo json_encode([
            "status" => "success",
            "redirect" => "./Ma_reservation.php?id_reservation=$id_reservation"
        ]);
        exit;
    }

    echo json_encode([
        "status" => "error",
        "message" => "Aucune réservation trouvée pour cet email et numéro de réservation."
    ]);
    exit;
}

echo json_encode([
    "status" => "error",
    "message" => "Veuillez fournir un email et un numéro de réservation."
]);
exit;
?>