<?php
require_once __DIR__ . '/../config.php';

$depositId = $_GET['depositId'] ?? null;

if (!$depositId) {
    echo "Paiement en cours de vérification.";
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM paiements WHERE deposit_id = ?");
$stmt->execute([$depositId]);
$paiement = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paiement) {
    echo "Transaction introuvable.";
    exit;
}

echo "Paiement lancé. Statut actuel : " . htmlspecialchars($paiement['status']);