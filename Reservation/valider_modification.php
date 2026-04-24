<?php
session_start();

require_once __DIR__ . '/../config.php';

if (
    isset(
        $_POST['id_reservation'],
        $_POST['nom'],
        $_POST['prenom'],
        $_POST['telephone'],
        $_POST['numero_siege'],
        $_POST['idVoyage']
    )
) {
    $id = (int) $_POST['id_reservation'];
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $telephone = trim($_POST['telephone']);
    $numero_siege = (int) $_POST['numero_siege'];
    $idVoyage = (int) $_POST['idVoyage'];

    $sql = "
        UPDATE reservation 
        SET nom = ?, 
            prenom = ?, 
            telephone = ?, 
            Numero_siege = ?, 
            idVoyage = ?
        WHERE id_reservation = ?
    ";

    $stmt = $pdo->prepare($sql);
    $result = $stmt->execute([
        $nom,
        $prenom,
        $telephone,
        $numero_siege,
        $idVoyage,
        $id
    ]);

    if ($result) {
        header("Location: ../Ma_reservation.php?id_reservation=" . $id);
        exit;
    }

    echo "Erreur lors de la mise à jour.";
    exit;
}

echo "Données manquantes.";
exit;