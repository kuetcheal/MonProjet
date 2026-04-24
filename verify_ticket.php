<?php
session_start();

require_once __DIR__ . '/config.php';

$message = '';
$status = 'error';

try {
    $token = trim($_GET['token'] ?? '');

    if ($token === '') {
        throw new Exception('Ticket invalide : token manquant.');
    }

    $stmt = $pdo->prepare("
        SELECT id_reservation, nom, prenom, telephone, idVoyage, Numero_reservation, Numero_siege, ticket_status, scanned_at
        FROM reservation
        WHERE qr_token = ?
        LIMIT 1
    ");

    $stmt->execute([$token]);
    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        throw new Exception('Billet introuvable.');
    }

    if ($reservation['ticket_status'] === 'used') {
        $status = 'used';
        $message = 'Ce billet a déjà été utilisé.';
    } elseif ($reservation['ticket_status'] === 'cancelled') {
        $status = 'cancelled';
        $message = 'Ce billet a été annulé.';
    } else {
        $update = $pdo->prepare("
            UPDATE reservation
            SET ticket_status = 'used', scanned_at = NOW()
            WHERE id_reservation = ?
        ");

        $update->execute([$reservation['id_reservation']]);

        $status = 'success';
        $message = 'Billet valide. Accès autorisé.';
    }
} catch (Exception $e) {
    $message = $e->getMessage();
}

$bgClass = 'bg-red-600';
if ($status === 'success') $bgClass = 'bg-green-600';
if ($status === 'used') $bgClass = 'bg-yellow-500';
if ($status === 'cancelled') $bgClass = 'bg-gray-700';
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Vérification du billet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
    <div class="max-w-xl w-full <?= $bgClass ?> text-white p-8 rounded-2xl shadow-2xl text-center">
        <h1 class="text-3xl font-bold mb-4">Contrôle du billet</h1>
        <p class="text-xl"><?= htmlspecialchars($message) ?></p>
    </div>
</body>
</html>