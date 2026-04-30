<?php
session_start();

require_once __DIR__ . '/../config.php';

$userId = (int)($_SESSION['user_id'] ?? $_SESSION['Id_compte'] ?? 0);

if ($userId <= 0) {
    $_SESSION['redirect_after_login'] = '/MonProjet/covoiturage/recap-covoiturage.php';
    header('Location: ../connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../listevoyageretour.php?transport=covoiturage');
    exit;
}

$idVoyage = isset($_POST['idVoyage']) ? (int) $_POST['idVoyage'] : 0;
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$email = trim($_POST['email'] ?? '');
$prix = isset($_POST['prix']) ? (float) $_POST['prix'] : 0;

if ($idVoyage <= 0 || $nom === '' || $prenom === '' || $telephone === '' || $email === '') {
    die('Informations invalides.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Adresse email invalide.');
}

function generateReservationNumber($length = 8)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }

    return $randomString;
}

function generateUniqueReservationNumber(PDO $pdo, int $length = 8): string
{
    do {
        $number = generateReservationNumber($length);

        $stmt = $pdo->prepare("
            SELECT COUNT(*)
            FROM reservation
            WHERE Numero_reservation = :numero
        ");

        $stmt->execute([
            ':numero' => $number
        ]);

        $exists = (int) $stmt->fetchColumn() > 0;

    } while ($exists);

    return $number;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT *
        FROM voyage
        WHERE idVoyage = :idVoyage
        AND modeTransport = 'covoiturage'
        AND statut_trajet = 'valide'
        FOR UPDATE
    ");

    $stmt->execute([
        ':idVoyage' => $idVoyage
    ]);

    $voyage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voyage) {
        $pdo->rollBack();
        die('Trajet de covoiturage introuvable.');
    }

    if (empty($voyage['chauffeur_id'])) {
        $pdo->rollBack();
        die('Aucun chauffeur associé à ce trajet.');
    }

    if ((int)$voyage['chauffeur_id'] === $userId) {
        $pdo->rollBack();
        die('Vous ne pouvez pas réserver votre propre trajet.');
    }

    $placesDisponibles = isset($voyage['nombre_places_disponibles']) && $voyage['nombre_places_disponibles'] !== null
        ? (int) $voyage['nombre_places_disponibles']
        : (int) $voyage['nombrePlaces'];

    if ($placesDisponibles <= 0) {
        $pdo->rollBack();
        die('Ce trajet n’a plus de places disponibles.');
    }

    $prixFinal = !empty($voyage['prix_par_place'])
        ? (float) $voyage['prix_par_place']
        : (float) $voyage['prix'];

    if ($prixFinal <= 0 && $prix > 0) {
        $prixFinal = $prix;
    }

    /*
        On empêche le même client de faire plusieurs demandes actives
        pour le même trajet.
    */
    $check = $pdo->prepare("
        SELECT id_reservation
        FROM reservation
        WHERE user_id = :user_id
        AND idVoyage = :idVoyage
        AND statut_demande IN ('en_attente', 'acceptee')
        LIMIT 1
    ");

    $check->execute([
        ':user_id' => $userId,
        ':idVoyage' => $idVoyage
    ]);

    if ($check->fetch()) {
        $pdo->rollBack();
        die('Vous avez déjà une demande active pour ce trajet.');
    }

    $reservationNumber = generateUniqueReservationNumber($pdo);

    /*
        Pour le covoiturage :
        - Etat = 0 signifie en attente.
        - statut_demande = en_attente.
        - statut_paiement = en_attente.
        - Numero_siege = 0 car ce n’est pas une réservation de bus avec siège choisi.
    */
    $insert = $pdo->prepare("
        INSERT INTO reservation (
            user_id,
            nom,
            prenom,
            telephone,
            email,
            idVoyage,
            numeroPlace,
            Etat,
            Numero_reservation,
            Numero_siege,
            prix_reservation,
            statut_paiement,
            compte_fidelite,
            voyage_offert_utilise,
            ticket_status,
            statut_demande
        ) VALUES (
            :user_id,
            :nom,
            :prenom,
            :telephone,
            :email,
            :idVoyage,
            0,
            0,
            :numero_reservation,
            0,
            :prix_reservation,
            'en_attente',
            1,
            0,
            'valid',
            'en_attente'
        )
    ");

    $insert->execute([
        ':user_id' => $userId,
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':telephone' => $telephone,
        ':email' => $email,
        ':idVoyage' => $idVoyage,
        ':numero_reservation' => $reservationNumber,
        ':prix_reservation' => $prixFinal
    ]);

    $reservationId = (int) $pdo->lastInsertId();

    $pdo->commit();

    $_SESSION['success_message'] = "Votre demande de covoiturage a été envoyée au chauffeur. Vous serez notifié dès qu’il accepte ou refuse.";

header('Location: demande-envoyee.php?id_reservation=' . $reservationId);
exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die('Erreur : ' . $e->getMessage());
}