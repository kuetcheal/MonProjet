<?php
$priceAller = isset($_GET['priceAller']) ? (float) $_GET['priceAller'] : 0;
$priceRetour = isset($_GET['priceRetour']) ? (float) $_GET['priceRetour'] : 0;

$departAller = $_GET['departAller'] ?? 'N/A';
$arriveAller = $_GET['arriveAller'] ?? 'N/A';
$timeAller = $_GET['timeAller'] ?? 'N/A';

$departRetour = $_GET['departRetour'] ?? 'N/A';
$arriveRetour = $_GET['arriveRetour'] ?? 'N/A';
$timeRetour = $_GET['timeRetour'] ?? 'N/A';

$prixTotal = $priceAller + $priceRetour;
$montant15 = $prixTotal * 0.15;

if (isset($_POST['ajouter15'])) {
    $prixTotal += $montant15;
}

ob_start();
?>

<div class="max-w-3xl mx-auto px-4 py-10 ">
    <div class="bg-white rounded-2xl shadow-md p-6 md:p-8">
        <h2 class="text-3xl font-extrabold text-slate-800 mb-8">Résumé de votre voyage</h2>

        <div class="mb-8">
            <p class="text-xl font-bold text-slate-800 mb-3">Trajet aller :</p>
            <ul class="space-y-2 text-lg text-slate-700">
                <li><span class="font-semibold">De :</span> <?= htmlspecialchars($departAller) ?> à <?= htmlspecialchars($arriveAller) ?></li>
                <li><span class="font-semibold">Heure :</span> <?= htmlspecialchars($timeAller) ?></li>
                <li><span class="font-semibold">Prix :</span> <?= htmlspecialchars($priceAller) ?> FCFA</li>
            </ul>
        </div>

        <div class="mb-8">
            <p class="text-xl font-bold text-slate-800 mb-3">Trajet retour :</p>
            <ul class="space-y-2 text-lg text-slate-700">
                <li><span class="font-semibold">De :</span> <?= htmlspecialchars($departRetour) ?> à <?= htmlspecialchars($arriveRetour) ?></li>
                <li><span class="font-semibold">Heure :</span> <?= htmlspecialchars($timeRetour) ?></li>
                <li><span class="font-semibold">Prix :</span> <?= htmlspecialchars($priceRetour) ?> FCFA</li>
            </ul>
        </div>

        <div class="mb-6">
            <h5 class="text-lg font-semibold text-slate-800">
                Réserver avec la possibilité d'annuler ou de modifier pour seulement :
                <span class="text-green-600"><?= htmlspecialchars(number_format($montant15, 0, ',', ' ')) ?> FCFA</span>
            </h5>
        </div>

        <form method="post" class="mb-6">
            <button
                type="submit"
                name="ajouter15"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
                Ajouter
            </button>
        </form>

        <h3 class="text-3xl font-extrabold text-green-600 mb-6">
            Prix total : <?= htmlspecialchars(number_format($prixTotal, 0, ',', ' ')) ?> FCFA
        </h3>

        <a
            href="payment.php?totalPrice=<?= urlencode($prixTotal) ?>"
            class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition">
            Continuer vers le paiement
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Résumé de votre voyage";
include __DIR__ . '/layouts/default.php';
?>