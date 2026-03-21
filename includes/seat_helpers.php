<?php

function getVoyageById(PDO $bdd, int $idVoyage): ?array
{
    $stmt = $bdd->prepare("SELECT * FROM voyage WHERE idVoyage = :idVoyage");
    $stmt->execute(['idVoyage' => $idVoyage]);
    $voyage = $stmt->fetch(PDO::FETCH_ASSOC);

    return $voyage ?: null;
}

function getReservedSeats(PDO $bdd, int $idVoyage): array
{
    $stmt = $bdd->prepare("
        SELECT numeroPlace
        FROM reservation
        WHERE idVoyage = :idVoyage
          AND numeroPlace IS NOT NULL
    ");
    $stmt->execute(['idVoyage' => $idVoyage]);

    return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
}

function isSeatAvailable(PDO $bdd, int $idVoyage, int $numeroPlace): bool
{
    $stmt = $bdd->prepare("
        SELECT COUNT(*)
        FROM reservation
        WHERE idVoyage = :idVoyage
          AND numeroPlace = :numeroPlace
    ");
    $stmt->execute([
        'idVoyage' => $idVoyage,
        'numeroPlace' => $numeroPlace
    ]);

    return (int)$stmt->fetchColumn() === 0;
}