<?php
session_start();

$idVoyageAller = $_GET['idVoyageAller'] ?? $_POST['idVoyageAller'] ?? null;
$idVoyageRetour = $_GET['idVoyageRetour'] ?? $_POST['idVoyageRetour'] ?? null;

$priceAller = isset($_GET['priceAller']) ? (float) $_GET['priceAller'] : (isset($_POST['priceAller']) ? (float) $_POST['priceAller'] : 0);
$priceRetour = isset($_GET['priceRetour']) ? (float) $_GET['priceRetour'] : (isset($_POST['priceRetour']) ? (float) $_POST['priceRetour'] : 0);

$departAller = $_GET['departAller'] ?? $_POST['departAller'] ?? 'N/A';
$arriveAller = $_GET['arriveAller'] ?? $_POST['arriveAller'] ?? 'N/A';
$timeAller = $_GET['timeAller'] ?? $_POST['timeAller'] ?? 'N/A';

$departRetour = $_GET['departRetour'] ?? $_POST['departRetour'] ?? '';
$arriveRetour = $_GET['arriveRetour'] ?? $_POST['arriveRetour'] ?? '';
$timeRetour = $_GET['timeRetour'] ?? $_POST['timeRetour'] ?? '';

if ($idVoyageAller) {
    $_SESSION['idVoyage'] = $idVoyageAller;
}

$prixBase = $priceAller + $priceRetour;
$montant15 = $prixBase * 0.15;

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">

        <h2 class="text-3xl font-extrabold text-slate-800 mb-8">
            Récapitulatif du voyage
        </h2>

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

        <div class="bg-gray-50 p-5 rounded-xl mb-6">
            <p class="text-sm text-slate-700 mb-4 font-medium">
                Choisissez une option :
            </p>

            <div class="space-y-3">
                <label class="flex items-start gap-3 border rounded-xl p-4 cursor-pointer hover:border-green-500 transition bg-white">
                    <input
                        type="radio"
                        name="flexOption"
                        value="yes"
                        class="mt-1 accent-green-600"
                    >
                    <div>
                        <p class="font-bold text-slate-800">
                            Ajouter la flexibilité (+ <?= number_format($montant15, 0, ',', ' ') ?> FCFA)
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            Annulez ou modifiez votre voyage sans frais supplémentaires.
                        </p>
                    </div>
                </label>

                <label class="flex items-start gap-3 border rounded-xl p-4 cursor-pointer hover:border-red-400 transition bg-white">
                    <input
                        type="radio"
                        name="flexOption"
                        value="no"
                        class="mt-1 accent-red-500"
                    >
                    <div>
                        <p class="font-bold text-slate-800">
                            Ne pas ajouter l’option
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            Je me conforme aux conditions EasyTravel en cas d’annulation ou modification.
                        </p>
                    </div>
                </label>
            </div>
        </div>

        <div class="flex justify-between items-center mb-6">
            <span class="text-xl font-bold text-slate-800">Total</span>
            <span id="totalDisplay" class="text-2xl font-extrabold text-green-600">
                <?= number_format($prixBase, 0, ',', ' ') ?> FCFA
            </span>
        </div>

        <a href="#"
           id="continueBtn"
           class="block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition">
           Continuer vers le paiement
        </a>

    </div>
</div>

<script>
    const prixBase = <?= json_encode((float)$prixBase) ?>;
    const montant15 = <?= json_encode((float)$montant15) ?>;
    const idVoyageAller = <?= json_encode($idVoyageAller ?? '') ?>;

    const continueBtn = document.getElementById('continueBtn');
    const totalDisplay = document.getElementById('totalDisplay');
    const flexRadios = document.querySelectorAll('input[name="flexOption"]');

    function formatFcfa(value) {
        return new Intl.NumberFormat('fr-FR').format(value) + ' FCFA';
    }

    function getSelectedFlexOption() {
        const checked = document.querySelector('input[name="flexOption"]:checked');
        return checked ? checked.value : null;
    }

    function getCurrentTotal() {
        const option = getSelectedFlexOption();

        if (option === 'yes') {
            return prixBase + montant15;
        }

        return prixBase;
    }

    function updateDisplayedTotal() {
        const selected = getSelectedFlexOption();

        if (!selected) {
            totalDisplay.textContent = formatFcfa(prixBase);
            return;
        }

        totalDisplay.textContent = formatFcfa(getCurrentTotal());
    }

    flexRadios.forEach(radio => {
        radio.addEventListener('change', updateDisplayedTotal);
    });

    continueBtn.addEventListener('click', function(e) {
        e.preventDefault();

        const selected = getSelectedFlexOption();

        if (!selected) {
            alert("Veuillez choisir une option avant de continuer.");
            return;
        }

        if (!idVoyageAller) {
            alert("Aucun voyage sélectionné. Merci de revenir à la liste des trajets.");
            return;
        }

        const totalPrice = getCurrentTotal();

        const params = new URLSearchParams({
            idVoyage: idVoyageAller,
            totalPrice: totalPrice,
            flexOption: selected
        });

        window.location.href = 'payment.php?' + params.toString();
    });

    updateDisplayedTotal();
</script>

<?php
$content = ob_get_clean();
$title = "Résumé de votre voyage";
include __DIR__ . '/layouts/default.php';
?>