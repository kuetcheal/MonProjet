<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "bd_stock");

$id = $_POST['id_reservation'];
$nom = $_POST['nom'];
$prenom = $_POST['prenom'];
$telephone = $_POST['telephone'];
$numero_siege = $_POST['numero_siege'];
$idVoyage = $_POST['idVoyage'];

$sql = "UPDATE reservation SET nom = ?, prenom = ?, telephone = ?, numero_siege = ?, idVoyage = ? WHERE id_reservation = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssiii", $nom, $prenom, $telephone, $numero_siege, $idVoyage, $id);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../Ma_reservation.php?id_reservation=" . $id);
    exit;
} else {
    echo "Erreur lors de la mise à jour.";
}
?>
