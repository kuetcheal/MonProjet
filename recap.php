<?php
session_start();

$priceAller = isset($_GET['priceAller']) ? (float) $_GET['priceAller'] : 0;
$priceRetour = isset($_GET['priceRetour']) ? (float) $_GET['priceRetour'] : 0;

$departAller = $_GET['departAller'] ?? 'N/A';
$arriveAller = $_GET['arriveAller'] ?? 'N/A';
$timeAller = $_GET['timeAller'] ?? 'N/A';

$departRetour = $_GET['departRetour'] ?? '';
$arriveRetour = $_GET['arriveRetour'] ?? '';
$timeRetour = $_GET['timeRetour'] ?? '';

$prixBase = $priceAller + $priceRetour;
$montant15 = $prixBase * 0.15;

$prixTotal = $prixBase;

// Gestion choix utilisateur
if (isset($_POST['choixFlex'])) {
    if (isset($_POST['flexOption']) && $_POST['flexOption'] === 'yes') {
        $prixTotal += $montant15;
        $_SESSION['flex'] = true;
    } else {
        $_SESSION['flex'] = false;
    }
}

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">

        <h2 class="text-3xl font-extrabold text-slate-800 mb-8">
            Récapitulatif du voyage
        </h2>

        <!-- ALLER -->
        <div class="mb-6 border-l-4 border-green-500 pl-4">
            <h3 class="text-lg font-bold text-slate-700 mb-2">Trajet Aller</h3>

            <p class="text-slate-600">
                <span class="font-semibold"><?= htmlspecialchars($departAller) ?></span>
                →
                <span class="font-semibold"><?= htmlspecialchars($arriveAller) ?></span>
            </p>

            <p class="text-sm text-gray-500 mt-1">
                Heure : <?= htmlspecialchars($timeAller) ?>
            </p>

            <p class="text-green-600 font-bold mt-2">
                <?= number_format($priceAller, 0, ',', ' ') ?> FCFA
            </p>
        </div>

        <!-- RETOUR -->
        <?php if ($priceRetour > 0): ?>
        <div class="mb-6 border-l-4 border-blue-500 pl-4">
            <h3 class="text-lg font-bold text-slate-700 mb-2">Trajet Retour</h3>

            <p class="text-slate-600">
                <span class="font-semibold"><?= htmlspecialchars($departRetour) ?></span>
                →
                <span class="font-semibold"><?= htmlspecialchars($arriveRetour) ?></span>
            </p>

            <p class="text-sm text-gray-500 mt-1">
                Heure : <?= htmlspecialchars($timeRetour) ?>
            </p>

            <p class="text-blue-600 font-bold mt-2">
                <?= number_format($priceRetour, 0, ',', ' ') ?> FCFA
            </p>
        </div>
        <?php endif; ?>

        <!-- OPTIONS FLEXIBILITÉ -->
        <div class="bg-gray-50 p-5 rounded-xl mb-6">

            <p class="text-sm text-slate-700 mb-4 font-medium">
                Choisissez une option :
            </p>

            <form method="post" id="flexForm">

                <!-- OPTION 1 -->
                <label class="flex items-start gap-3 border rounded-xl p-4 mb-3 cursor-pointer hover:border-green-500 transition bg-white">
                    <input type="radio" name="flexOption" value="yes" class="mt-1 accent-green-600">

                    <div>
                        <p class="font-bold text-slate-800">
                            Ajouter la flexibilité (+ <?= number_format($montant15, 0, ',', ' ') ?> FCFA)
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            Annulez ou modifiez votre voyage sans frais supplémentaires.
                        </p>
                    </div>
                </label>

                <!-- OPTION 2 -->
                <label class="flex items-start gap-3 border rounded-xl p-4 cursor-pointer hover:border-red-400 transition bg-white">
                    <input type="radio" name="flexOption" value="no" class="mt-1 accent-red-500">

                    <div>
                        <p class="font-bold text-slate-800">
                            Ne pas ajouter l’option
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            Je me conforme aux conditions EasyTravel en cas d’annulation ou modification.
                        </p>
                    </div>
                </label>

                <!-- BOUTONS -->
                <div class="flex gap-3 mt-4">
                    <button type="submit" name="choixFlex"
                        class="flex-1 max-w-[180px] bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition">
                        Ajouter
                    </button>

                    <button type="submit" name="choixFlex"
                        class="flex-1 max-w-[180px] bg-gray-400 hover:bg-gray-500 text-white py-2 rounded-lg transition">
                        Ne pas ajouter
                    </button>
                </div>

            </form>
        </div>

        <!-- TOTAL -->
        <div class="flex justify-between items-center mb-6">
            <span class="text-xl font-bold text-slate-800">Total</span>
            <span class="text-2xl font-extrabold text-green-600">
                <?= number_format($prixTotal, 0, ',', ' ') ?> FCFA
            </span>
        </div>

        <!-- CONTINUER -->
        <a href="#"
           id="continueBtn"
           data-url="payment.php?totalPrice=<?= urlencode($prixTotal) ?>"
           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">
           Continuer vers le paiement
        </a>

    </div>
</div>

<script>
// Vérification choix obligatoire
document.getElementById('flexForm').addEventListener('submit', function(e) {
    const selected = document.querySelector('input[name="flexOption"]:checked');

    if (!selected) {
        e.preventDefault();
        alert("Veuillez choisir une option avant de continuer.");
    }
});

// Blocage bouton continuer
document.getElementById('continueBtn').addEventListener('click', function(e) {
    const selected = document.querySelector('input[name="flexOption"]:checked');

    if (!selected) {
        e.preventDefault();
        alert("Veuillez choisir une option avant de continuer.");
        return;
    }

    window.location.href = this.dataset.url;
});
</script>

<?php
$content = ob_get_clean();
$title = "Résumé de votre voyage";
include __DIR__ . '/layouts/default.php';
?>