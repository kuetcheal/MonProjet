<?php
$typeBusFilter = $_GET['typeBus'] ?? '';
$trancheHoraire = $_GET['trancheHoraire'] ?? '';
$prixMax = $_GET['prixMax'] ?? '';
$tri = $_GET['tri'] ?? 'heure_asc';

$activeFilters = 0;
if (!empty($typeBusFilter)) $activeFilters++;
if (!empty($trancheHoraire)) $activeFilters++;
if ($prixMax !== '') $activeFilters++;
if (!empty($tri) && $tri !== 'heure_asc') $activeFilters++;
?>

<div class="bg-white   border border-gray-100 p-5 lg:sticky lg:top-6">
    <div class="flex items-center justify-between mb-5">
        <h3 class="text-xl font-bold text-slate-800">Filtrer</h3>
        <span class="inline-flex items-center justify-center min-w-[32px] h-8 px-2 rounded-full bg-green-100 text-green-700 font-bold text-sm">
            <?= $activeFilters ?>
        </span>
    </div>

    <form method="GET" action="" class="space-y-5">
        <!-- Type de bus -->
        <div>
            <label for="typeBus" class="block text-sm font-semibold text-gray-700 mb-2">Type de bus</label>
            <select
                id="typeBus"
                name="typeBus"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                <option value="">Tous</option>
                <option value="classique" <?= $typeBusFilter === 'classique' ? 'selected' : '' ?>>Classique</option>
                <option value="VIP" <?= $typeBusFilter === 'VIP' ? 'selected' : '' ?>>VIP</option>
            </select>
        </div>

        <!-- Tranche horaire -->
        <div>
            <label for="trancheHoraire" class="block text-sm font-semibold text-gray-700 mb-2">Heure de départ</label>
            <select
                id="trancheHoraire"
                name="trancheHoraire"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                <option value="">Toutes</option>
                <option value="matin" <?= $trancheHoraire === 'matin' ? 'selected' : '' ?>>Matin (00h - 11h59)</option>
                <option value="apresmidi" <?= $trancheHoraire === 'apresmidi' ? 'selected' : '' ?>>Après-midi (12h - 17h59)</option>
                <option value="soir" <?= $trancheHoraire === 'soir' ? 'selected' : '' ?>>Soir (18h - 23h59)</option>
            </select>
        </div>

        <!-- Prix maximum -->
        <div>
            <label for="prixMax" class="block text-sm font-semibold text-gray-700 mb-2">Prix maximum (FCFA)</label>
            <input
                type="number"
                id="prixMax"
                name="prixMax"
                min="0"
                value="<?= htmlspecialchars($prixMax) ?>"
                placeholder="Ex : 5000"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
        </div>

        <!-- Tri -->
        <div>
            <label for="tri" class="block text-sm font-semibold text-gray-700 mb-2">Trier par</label>
            <select
                id="tri"
                name="tri"
                class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
            >
                <option value="heure_asc" <?= $tri === 'heure_asc' ? 'selected' : '' ?>>Départ le plus tôt</option>
                <option value="heure_desc" <?= $tri === 'heure_desc' ? 'selected' : '' ?>>Départ le plus tard</option>
                <option value="prix_asc" <?= $tri === 'prix_asc' ? 'selected' : '' ?>>Prix le plus bas</option>
                <option value="prix_desc" <?= $tri === 'prix_desc' ? 'selected' : '' ?>>Prix le plus élevé</option>
            </select>
        </div>

        <div class="pt-2 flex flex-col gap-3">
            <button
                type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3  transition"
            >
                Appliquer les filtres
            </button>

            <a
                href="<?= strtok($_SERVER['REQUEST_URI'], '?') ?>"
                class="w-full text-center bg-gray-100 hover:bg-gray-200 text-slate-700 font-semibold py-3  transition"
            >
                Réinitialiser
            </a>
        </div>
    </form>

    <div class="mt-6 pt-5 border-t border-gray-200">
        <h4 class="text-sm font-bold text-slate-700 mb-3">Services possibles</h4>

        <div class="space-y-3 text-sm text-gray-600">
            <div class="flex items-center gap-3">
                <i class="fa fa-wifi text-green-600 w-4"></i>
                <span>Wi-Fi à bord</span>
            </div>
            <div class="flex items-center gap-3">
                <i class="fa fa-television text-green-600 w-4"></i>
                <span>Écran / divertissement</span>
            </div>
            <div class="flex items-center gap-3">
                <i class="fa fa-plug text-green-600 w-4"></i>
                <span>Prises électriques</span>
            </div>
            <div class="flex items-center gap-3">
                <i class="fa fa-coffee text-green-600 w-4"></i>
                <span>Boisson (souvent en VIP)</span>
            </div>
        </div>
    </div>
</div>