<?php

require_once __DIR__ . '/../config.php';

function getUserReservationsValides(PDO $pdo, int $userId): int
{
    $sql = "SELECT COUNT(*) 
            FROM reservation
            WHERE user_id = :user_id
              AND statut_paiement = 'payee'
              AND compte_fidelite = 1
              AND voyage_offert_utilise = 0";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);

    return (int) $stmt->fetchColumn();
}

function getVoyagesOffertsUtilises(PDO $pdo, int $userId): int
{
    $sql = "SELECT voyages_offerts_utilises
            FROM user
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);

    return (int) $stmt->fetchColumn();
}

function getFideliteInfos(PDO $pdo, int $userId): array
{
    $reservationsValides = getUserReservationsValides($pdo, $userId);
    $voyagesUtilises = getVoyagesOffertsUtilises($pdo, $userId);

    $voyagesGagnes = intdiv($reservationsValides, 12);
    $creditsDisponibles = max(0, $voyagesGagnes - $voyagesUtilises);
    $progression = $reservationsValides % 12;
    $restePourCadeau = 12 - $progression;

    if ($progression === 0 && $reservationsValides > 0) {
        $restePourCadeau = 12;
    }

    return [
        'reservations_valides' => $reservationsValides,
        'voyages_gagnes' => $voyagesGagnes,
        'voyages_utilises' => $voyagesUtilises,
        'credits_disponibles' => $creditsDisponibles,
        'progression' => $progression,
        'reste_pour_cadeau' => $restePourCadeau
    ];
}

function getUserInfos(PDO $pdo, int $userId): ?array
{
    $sql = "SELECT id, user_name, user_firstname, user_mail, user_phone
            FROM user
            WHERE id = :id
            LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $userId]);

    $user = $stmt->fetch();

    return $user ?: null;
}