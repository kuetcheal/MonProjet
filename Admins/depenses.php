<?php
$adminTitle = 'Dépenses';
$adminWelcome = 'Suivi et gestion des dépenses';
$adminNotificationCount = 3;

$depenses = [
    ['date' => '2026-04-24', 'categorie' => 'Carburant', 'description' => 'Approvisionnement bus ligne Douala-Yaoundé', 'montant' => '95 000 FCFA', 'mode' => 'Espèces'],
    ['date' => '2026-04-23', 'categorie' => 'Maintenance', 'description' => 'Réparation frein arrière', 'montant' => '180 000 FCFA', 'mode' => 'Virement'],
    ['date' => '2026-04-22', 'categorie' => 'Commission paiement', 'description' => 'Frais agrégateur de paiement', 'montant' => '21 500 FCFA', 'mode' => 'Prélèvement'],
    ['date' => '2026-04-21', 'categorie' => 'Rémunération', 'description' => 'Prime chauffeur semaine', 'montant' => '50 000 FCFA', 'mode' => 'Espèces'],
];

ob_start();
?>
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-sm text-gray-500">Dépenses aujourd’hui</h2>
            <p class="text-3xl font-bold text-red-600 mt-2">95 000 FCFA</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-sm text-gray-500">Dépenses ce mois</h2>
            <p class="text-3xl font-bold text-red-600 mt-2">3 120 000 FCFA</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-sm text-gray-500">Catégorie dominante</h2>
            <p class="text-3xl font-bold text-gray-800 mt-2">Carburant</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 bg-white rounded-2xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-5">Ajouter une dépense</h2>

            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Catégorie</label>
                    <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option>Carburant</option>
                        <option>Maintenance</option>
                        <option>Commission paiement</option>
                        <option>Rémunération</option>
                        <option>Autre</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Détail de la dépense..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Montant</label>
                    <input type="number" placeholder="50000" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mode de paiement</label>
                    <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option>Espèces</option>
                        <option>Orange Money</option>
                        <option>MTN MoMo</option>
                        <option>Virement</option>
                        <option>Prélèvement</option>
                    </select>
                </div>

                <button type="button" class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 transition">
                    Enregistrer la dépense
                </button>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white rounded-2xl shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-5">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Historique des dépenses</h2>
                    <p class="text-gray-500 text-sm mt-1">Suivi des sorties d’argent</p>
                </div>

                <button class="bg-gray-800 text-white px-4 py-2 rounded-lg hover:bg-black transition font-semibold">
                    <i class="fas fa-download mr-2"></i>Exporter
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">Date</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">Catégorie</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">Description</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">Montant</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">Mode</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($depenses as $depense): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($depense['date']) ?></td>
                                <td class="px-4 py-4 font-semibold text-gray-800"><?= htmlspecialchars($depense['categorie']) ?></td>
                                <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($depense['description']) ?></td>
                                <td class="px-4 py-4 font-bold text-red-600"><?= htmlspecialchars($depense['montant']) ?></td>
                                <td class="px-4 py-4 text-gray-700"><?= htmlspecialchars($depense['mode']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php
$adminContent = ob_get_clean();

include __DIR__ . '/../includes/layoutadmin.php';