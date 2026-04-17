<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/fidelite_functions.php';

if (!isset($_SESSION['Id_compte'])) {
    header('Location: ../connexion.php');
    exit;
}

$userId = (int) $_SESSION['Id_compte'];
$user = getUserInfos($pdo, $userId);
$fidelite = getFideliteInfos($pdo, $userId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes offres - Easy Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="max-w-6xl mx-auto py-12 px-4">
    <div class="bg-white shadow-md p-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Mes offres fidélité</h1>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-green-50 p-5 border border-green-100">
                <p class="text-sm text-gray-600">Réservations validées</p>
                <h2 class="text-3xl font-bold text-[#008000]"><?= $fidelite['reservations_valides'] ?></h2>
            </div>

            <div class="bg-green-50 p-5 border border-green-100">
                <p class="text-sm text-gray-600">Voyages gagnés</p>
                <h2 class="text-3xl font-bold text-[#008000]"><?= $fidelite['voyages_gagnes'] ?></h2>
            </div>

            <div class="bg-green-50 p-5 border border-green-100">
                <p class="text-sm text-gray-600">Voyages utilisés</p>
                <h2 class="text-3xl font-bold text-[#008000]"><?= $fidelite['voyages_utilises'] ?></h2>
            </div>

            <div class="bg-green-50 p-5 border border-green-100">
                <p class="text-sm text-gray-600">Crédits disponibles</p>
                <h2 class="text-3xl font-bold text-[#008000]"><?= $fidelite['credits_disponibles'] ?></h2>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex justify-between mb-2">
                <span class="font-medium text-gray-700">Progression vers le prochain voyage offert</span>
                <span class="font-bold text-[#008000]"><?= $fidelite['progression'] ?>/12</span>
            </div>
            <div class="w-full bg-gray-200 h-4 rounded-full overflow-hidden">
                <div class="bg-[#008000] h-4 rounded-full" style="width: <?= ($fidelite['progression'] / 12) * 100 ?>%;"></div>
            </div>
        </div>

        <?php if ($fidelite['credits_disponibles'] > 0): ?>
            <div class="bg-green-100 border border-green-300 text-green-900 p-4 mb-6">
                Vous avez actuellement <strong><?= $fidelite['credits_disponibles'] ?></strong> voyage(s) offert(s) disponible(s).
            </div>
            <a href="../Reservation/Reservation.php?offre=1" class="inline-block bg-[#008000] hover:bg-[#0d5d31] text-white px-6 py-3 font-semibold rounded-md transition">
                Utiliser mon voyage offert
            </a>
        <?php else: ?>
            <div class="bg-yellow-100 border border-yellow-300 text-yellow-900 p-4">
                Aucun voyage offert disponible pour le moment.
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>