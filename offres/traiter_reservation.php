<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/fidelite_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../Reservation/Reservation.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$telephone = trim($_POST['telephone'] ?? '');
$email = trim($_POST['email'] ?? '');
$idVoyage = (int) ($_POST['idVoyage'] ?? 0);
$numeroPlace = trim($_POST['numeroPlace'] ?? '');
$prixReservation = (float) ($_POST['prix_reservation'] ?? 0);
$utiliserOffre = isset($_POST['utiliser_offre']) ? 1 : 0;

$userId = null;
$statutPaiement = 'en_attente';
$voyageOffertUtilise = 0;

if (isset($_SESSION['Id_compte'])) {
    $userId = (int) $_SESSION['Id_compte'];
    $user = getUserInfos($pdo, $userId);

    if (!$user) {
        die("Utilisateur introuvable.");
    }

    // On force les infos du billet à celles du compte
    $nom = $user['user_name'];
    $prenom = $user['user_firstname'];
    $telephone = $user['user_phone'];
    $email = $user['user_mail'];

    $fidelite = getFideliteInfos($pdo, $userId);

    if ($utiliserOffre === 1) {
        if ($fidelite['credits_disponibles'] <= 0) {
            die("Aucun crédit fidélité disponible.");
        }

        $prixReservation = 0;
        $statutPaiement = 'offerte';
        $voyageOffertUtilise = 1;
    } else {
        // à remplacer plus tard par ton vrai système de paiement
        $statutPaiement = 'payee';
    }
} else {
    // si pas connecté, la réservation ne participe pas au programme fidélité
    $statutPaiement = 'payee';
}

$numeroReservation = strtoupper(substr(md5(uniqid('', true)), 0, 8));

try {
    $pdo->beginTransaction();

    $sql = "INSERT INTO reservation (
                user_id, nom, prenom, telephone, email,
                idVoyage, numeroPlace, Etat, Numero_reservation,
                Numero_siege, prix_reservation, qr_token,
                statut_paiement, compte_fidelite, voyage_offert_utilise
            ) VALUES (
                :user_id, :nom, :prenom, :telephone, :email,
                :idVoyage, :numeroPlace, 0, :numero_reservation,
                :numero_siege, :prix_reservation, NULL,
                :statut_paiement, :compte_fidelite, :voyage_offert_utilise
            )";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'user_id' => $userId,
        'nom' => $nom,
        'prenom' => $prenom,
        'telephone' => $telephone,
        'email' => $email,
        'idVoyage' => $idVoyage,
        'numeroPlace' => $numeroPlace,
        'numero_reservation' => $numeroReservation,
        'numero_siege' => $numeroPlace,
        'prix_reservation' => $prixReservation,
        'statut_paiement' => $statutPaiement,
        'compte_fidelite' => $userId ? 1 : 0,
        'voyage_offert_utilise' => $voyageOffertUtilise
    ]);

    if ($userId && $voyageOffertUtilise === 1) {
        $updateUser = $pdo->prepare("
            UPDATE user
            SET voyages_offerts_utilises = voyages_offerts_utilises + 1
            WHERE id = :id
        ");
        $updateUser->execute(['id' => $userId]);
    }

    $pdo->commit();

    header('Location: mes_offres.php?success=1');
    exit;
} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur lors de la réservation : " . $e->getMessage());
}