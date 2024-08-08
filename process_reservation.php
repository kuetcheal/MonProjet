<?php
session_start();

$host = "localhost"; 
$user = "root"; 
$password = ""; 
$database = "bd_stock"; 

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    echo json_encode(["status" => "error", "message" => "Échec de la connexion à la base de données."]);
    exit;
}

if (isset($_POST["mail"], $_POST["Numero_reservation"])) {
    $mail = mysqli_real_escape_string($conn, $_POST['mail']);
    $reservation = mysqli_real_escape_string($conn, $_POST['Numero_reservation']);

    $query = "SELECT id_reservation FROM reservation WHERE email = ? AND Numero_reservation = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $mail, $reservation);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $valeur = mysqli_fetch_assoc($result);
        $_SESSION['id_reservation'] = $valeur['id_reservation'];
        $id_reservation = $valeur['id_reservation'];
        echo json_encode(["status" => "success", "redirect" => "./Ma_reservation.php?id_reservation=$id_reservation"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Aucune réservation trouvée pour cet email et numéro de réservation."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Veuillez fournir un email et un numéro de réservation."]);
}
?>