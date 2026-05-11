<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/fidelite_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Reservation/Reservation.php');
    exit;
}

/*
    Pour le covoiturage, le client doit être connecté,
    car la demande, le paiement et la messagerie sont liés à son compte.
*/
$userId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    header('Location: ../connexion.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$email = trim($_POST['email'] ?? '');
$idVoyage = (int)($_POST['idVoyage'] ?? 0);
$numeroPlace = trim($_POST['numeroPlace'] ?? '');
$prixReservation = (float)($_POST['prix_reservation'] ?? 0);
$utiliserOffre = isset($_POST['utiliser_offre']) ? 1 : 0;

if ($idVoyage <= 0) {
    die("Trajet invalide.");
}

$statutPaiement = 'en_attente';
$voyageOffertUtilise = 0;

try {
    $pdo->beginTransaction();

    /*
        1. On vérifie que le voyage existe bien et que c'est un covoiturage.
    */
    $checkVoyage = $pdo->prepare("
        SELECT 
            idVoyage,
            modeTransport,
            nombrePlaces,
            nombre_places_disponibles
        FROM voyage
        WHERE idVoyage = :idVoyage
        AND modeTransport = 'covoiturage'
        LIMIT 1
        FOR UPDATE
    ");

    $checkVoyage->execute([
        ':idVoyage' => $idVoyage
    ]);

    $voyage = $checkVoyage->fetch(PDO::FETCH_ASSOC);

    if (!$voyage) {
        $pdo->rollBack();
        die("Ce trajet n'est pas un covoiturage valide.");
    }

    $placesDisponibles = $voyage['nombre_places_disponibles'] !== null
        ? (int)$voyage['nombre_places_disponibles']
        : (int)$voyage['nombrePlaces'];

    if ($placesDisponibles <= 0) {
        $pdo->rollBack();
        die("Il n'y a plus de places disponibles pour ce trajet.");
    }

    /*
        2. On récupère les infos du client connecté.
    */
    $user = getUserInfos($pdo, $userId);

    if (!$user) {
        $pdo->rollBack();
        die("Utilisateur introuvable.");
    }

    /*
        On force les infos de la demande à celles du compte connecté.
    */
    $nom = $user['user_name'];
    $prenom = $user['user_firstname'];
    $telephone = $user['user_phone'];
    $email = $user['user_mail'];

    $fidelite = getFideliteInfos($pdo, $userId);

    if ($utiliserOffre === 1) {
        $creditsDisponibles = (int)($fidelite['credits_disponibles'] ?? 0);

        if ($creditsDisponibles <= 0) {
            $pdo->rollBack();
            die("Aucun crédit fidélité disponible.");
        }

        $prixReservation = 0;
        $statutPaiement = 'offerte';
        $voyageOffertUtilise = 1;
    } else {
        /*
            Nouveau flux covoiturage :
            le client ne paie pas au moment de la demande.
            Il paiera seulement après acceptation du chauffeur.
        */
        $statutPaiement = 'en_attente';
    }

    $numeroReservation = strtoupper(substr(md5(uniqid('', true)), 0, 8));

    /*
        3. On crée la demande de covoiturage.
    */
    $sql = "INSERT INTO reservation (
                type_reservation,
                user_id,
                nom,
                prenom,
                telephone,
                email,
                idVoyage,
                numeroPlace,
                Etat,
                statut_demande,
                Numero_reservation,
                Numero_siege,
                prix_reservation,
                qr_token,
                statut_paiement,
                compte_fidelite,
                voyage_offert_utilise,
                statut_reversement_chauffeur
            ) VALUES (
                'covoiturage',
                :user_id,
                :nom,
                :prenom,
                :telephone,
                :email,
                :idVoyage,
                :numeroPlace,
                0,
                'en_attente',
                :numero_reservation,
                :numero_siege,
                :prix_reservation,
                NULL,
                :statut_paiement,
                :compte_fidelite,
                :voyage_offert_utilise,
                'non_eligible'
            )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':user_id' => $userId,
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':telephone' => $telephone,
        ':email' => $email,
        ':idVoyage' => $idVoyage,
        ':numeroPlace' => $numeroPlace,
        ':numero_reservation' => $numeroReservation,
        ':numero_siege' => $numeroPlace,
        ':prix_reservation' => $prixReservation,
        ':statut_paiement' => $statutPaiement,
        ':compte_fidelite' => 1,
        ':voyage_offert_utilise' => $voyageOffertUtilise
    ]);

    $reservationId = (int)$pdo->lastInsertId();

    /*
        4. Si le client utilise une offre fidélité, on la consomme.
    */
    if ($voyageOffertUtilise === 1) {
        $updateUser = $pdo->prepare("
            UPDATE user
            SET voyages_offerts_utilises = voyages_offerts_utilises + 1
            WHERE id = :id
        ");

        $updateUser->execute([
            ':id' => $userId
        ]);
    }

    $pdo->commit();

    /*
        Après soumission, le client arrive sur la page de suivi.
        Cette page affichera : en attente / payer / discuter selon le statut.
    */
    header('Location: ../covoiturage/demande-envoyee.php?id_reservation=' . $reservationId);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die("Erreur lors de la réservation : " . $e->getMessage());
}