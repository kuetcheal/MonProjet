<?php
session_start();

require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Accueil.php');
    exit;
}

$numeroReservation = trim($_POST['Numero_reservation'] ?? '');
$email = trim($_POST['email'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');

if ($numeroReservation === '') {
    $_SESSION['tracking_error'] = "Veuillez renseigner le numéro de réservation.";
    header('Location: ../Accueil.php');
    exit;
}

if ($email === '' && $telephone === '') {
    $_SESSION['tracking_error'] = "Veuillez renseigner soit l'adresse mail, soit le numéro de téléphone.";
    header('Location: ../Accueil.php');
    exit;
}

$sql = "
    SELECT *
    FROM reservation
    WHERE Numero_reservation = :numeroReservation
      AND (
            (:email <> '' AND email = :email)
         OR (:telephone <> '' AND telephone = :telephone)
      )
    LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'numeroReservation' => $numeroReservation,
    'email' => $email,
    'telephone' => $telephone
]);

$reservation = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reservation) {
    $_SESSION['tracking_error'] = "Aucune réservation trouvée avec ces informations.";
    header('Location: ../Accueil.php');
    exit;
}

$_SESSION['tracking_access'] = true;
$_SESSION['tracking_reservation'] = [
    'id_reservation' => $reservation['id_reservation'],
    'Numero_reservation' => $reservation['Numero_reservation'],
    'idVoyage' => $reservation['idVoyage'],
    'nom' => $reservation['nom'],
    'prenom' => $reservation['prenom'],
    'email' => $reservation['email'],
    'telephone' => $reservation['telephone']
];

header('Location: ../localiser-trajet.php');
exit;