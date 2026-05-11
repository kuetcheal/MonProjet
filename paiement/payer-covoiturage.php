<?php
session_start();

require_once __DIR__ . '/../config.php';

$userId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../connexion.php');
    exit;
}

$reservationId = isset($_GET['id_reservation']) ? (int)$_GET['id_reservation'] : 0;

if ($reservationId <= 0) {
    die('Réservation invalide.');
}

try {
    $stmt = $pdo->prepare("
        SELECT
            r.*,
            v.villeDepart,
            v.quartierDepart,
            v.villeArrivee,
            v.quartierArrivee,
            v.jourDepart,
            v.heureDepart,
            v.heureArrivee
        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        WHERE r.id_reservation = :reservation_id
        AND r.user_id = :user_id
        AND r.type_reservation = 'covoiturage'
        LIMIT 1
    ");

    $stmt->execute([
        ':reservation_id' => $reservationId,
        ':user_id' => $userId
    ]);

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        die('Réservation introuvable ou accès refusé.');
    }

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$statutDemande = $reservation['statut_demande'] ?? '';
$statutPaiement = $reservation['statut_paiement'] ?? '';
$prix = (int)round((float)($reservation['prix_reservation'] ?? 0));
$telephone = preg_replace('/\D+/', '', (string)($reservation['telephone'] ?? ''));

if (strlen($telephone) === 9) {
    $telephone = '237' . $telephone;
}

if ($statutDemande !== 'acceptee') {
    header('Location: ../covoiturage/demande-envoyee.php?id_reservation=' . $reservationId);
    exit;
}

if ($statutPaiement === 'payee' || $statutPaiement === 'offerte') {
    header('Location: ../covoiturage/demande-envoyee.php?id_reservation=' . $reservationId);
    exit;
}

$deadlineDepassee = false;

if (!empty($reservation['payment_deadline_at'])) {
    $deadlineDepassee = strtotime($reservation['payment_deadline_at']) < time();
}

if ($deadlineDepassee) {
    die("Le délai de paiement est dépassé. Veuillez contacter le support ou refaire une demande.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Paiement covoiturage</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<div class="max-w-3xl mx-auto px-4 py-10">

    <div class="bg-white rounded-2xl shadow p-8 border border-gray-100">

        <div class="mb-6">
            <span class="inline-block bg-green-100 text-green-700 font-bold px-4 py-2 rounded-full text-sm">
                Demande acceptée
            </span>

            <h1 class="text-3xl font-extrabold text-slate-800 mt-4">
                Payer et confirmer ma place
            </h1>

            <p class="text-gray-500 mt-2">
                Votre demande a été acceptée par le chauffeur. Finalisez le paiement pour confirmer votre place.
            </p>
        </div>

        <div class="border border-gray-200 rounded-xl p-5 mb-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">
                Détails du trajet
            </h2>

            <div class="grid md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <p class="text-sm text-gray-500">Départ</p>
                    <p class="font-bold">
                        <?= htmlspecialchars($reservation['villeDepart']) ?>
                    </p>
                    <p>
                        <?= htmlspecialchars($reservation['quartierDepart'] ?? '') ?>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Arrivée</p>
                    <p class="font-bold">
                        <?= htmlspecialchars($reservation['villeArrivee']) ?>
                    </p>
                    <p>
                        <?= htmlspecialchars($reservation['quartierArrivee'] ?? '') ?>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Date</p>
                    <p class="font-bold">
                        <?= htmlspecialchars($reservation['jourDepart']) ?>
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Heure</p>
                    <p class="font-bold">
                        <?= htmlspecialchars(substr($reservation['heureDepart'], 0, 5)) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-200 rounded-xl p-5 mb-6">
            <p class="text-sm text-green-700 font-semibold">Montant à payer</p>
            <p class="text-3xl font-extrabold text-green-700 mt-1">
                <?= number_format($prix, 0, ',', ' ') ?> FCFA
            </p>
        </div>

        <?php if ($statutPaiement === 'paiement_en_cours'): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl p-4 mb-6">
                Un paiement est déjà en cours. Si vous n’avez pas terminé, vous pouvez relancer le paiement.
            </div>
        <?php elseif ($statutPaiement === 'paiement_echoue'): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 mb-6">
                Le dernier paiement a échoué. Vous pouvez réessayer.
            </div>
        <?php endif; ?>

        <form method="POST" action="pawapay-init-covoiturage.php" class="space-y-5">
            <input type="hidden" name="id_reservation" value="<?= (int)$reservationId ?>">

            <div>
                <label class="block text-gray-700 font-bold mb-2">
                    Numéro Mobile Money
                </label>

                <input
                    type="text"
                    name="phone"
                    value="<?= htmlspecialchars($telephone) ?>"
                    placeholder="2376XXXXXXXX"
                    required
                    class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
                >

                <p class="text-sm text-gray-500 mt-2">
                    Utilisez un numéro Orange Money ou MTN Mobile Money du Cameroun.
                </p>
            </div>

            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition">
                Payer avec Mobile Money
            </button>
        </form>

        <div class="mt-5 text-center">
            <a href="../covoiturage/demande-envoyee.php?id_reservation=<?= (int)$reservationId ?>"
               class="text-green-700 font-semibold hover:underline">
                Retour à ma demande
            </a>
        </div>

    </div>

</div>

</body>
</html>