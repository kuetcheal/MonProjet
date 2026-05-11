<?php
session_start();

require_once __DIR__ . '/../config.php';

$depositId = $_GET['depositId'] ?? $_GET['deposit_id'] ?? null;

if (!$depositId) {
    echo "Paiement en cours de vérification.";
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            p.*,
            r.id_reservation,
            r.type_reservation,
            r.statut_paiement,
            r.statut_demande
        FROM paiements p
        INNER JOIN reservation r ON r.id_reservation = p.reservation_id
        WHERE p.deposit_id = :deposit_id
        LIMIT 1
    ");

    $stmt->execute([
        ':deposit_id' => $depositId
    ]);

    $paiement = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paiement) {
        echo "Transaction introuvable.";
        exit;
    }

    $reservationId = (int)$paiement['id_reservation'];
    $typeReservation = $paiement['type_reservation'] ?? 'bus';

    if ($typeReservation === 'covoiturage') {
        header('Location: ../covoiturage/demande-envoyee.php?id_reservation=' . $reservationId);
        exit;
    }

} catch (Exception $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Retour paiement</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-xl mx-auto px-4 py-16">
    <div class="bg-white rounded-2xl shadow p-8 text-center">

        <h1 class="text-2xl font-extrabold text-slate-800 mb-3">
            Paiement lancé
        </h1>

        <p class="text-gray-600 mb-4">
            Statut actuel :
            <strong><?= htmlspecialchars($paiement['status'] ?? 'PENDING') ?></strong>
        </p>

        <p class="text-gray-500 text-sm mb-6">
            Si le paiement est validé, votre réservation sera confirmée automatiquement.
        </p>

        <a href="../Accueil.php"
           class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl transition">
            Retour à l’accueil
        </a>

    </div>
</div>

</body>
</html>