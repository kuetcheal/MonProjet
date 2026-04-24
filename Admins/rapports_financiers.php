<?php
$adminTitle = 'Rapports financiers';
$adminWelcome = 'Rapports et exports financiers';
$adminNotificationCount = 3;

ob_start();
?>
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-2xl font-bold text-gray-800">Rapports financiers</h2>
        <p class="text-gray-500 mt-2">Prépare, consulte et exporte les synthèses financières de l’activité.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500">Rapport journalier</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">Disponible</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500">Rapport hebdomadaire</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">Disponible</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500">Rapport mensuel</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">Disponible</p>
        </div>

        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500">Rapport annuel</p>
            <p class="text-2xl font-bold text-gray-800 mt-2">À consolider</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 bg-white rounded-2xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-5">Générer un rapport</h2>

            <form class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Type de rapport</label>
                    <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>Journalier</option>
                        <option>Hebdomadaire</option>
                        <option>Mensuel</option>
                        <option>Annuel</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date de début</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date de fin</label>
                    <input type="date" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Format</label>
                    <select class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option>PDF</option>
                        <option>CSV</option>
                        <option>Excel</option>
                    </select>
                </div>

                <button type="button" class="w-full bg-indigo-600 text-white py-3 rounded-lg font-bold hover:bg-indigo-700 transition">
                    Générer le rapport
                </button>
            </form>
        </div>

        <div class="xl:col-span-2 bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-800">Rapports récents</h2>
                <span class="text-sm text-gray-500">Historique d’exports</span>
            </div>

            <div class="space-y-4">
                <div class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="font-bold text-gray-800">Rapport mensuel - Avril 2026</p>
                        <p class="text-sm text-gray-500 mt-1">Généré le 24/04/2026 - Format PDF</p>
                    </div>
                    <a href="#" class="inline-flex items-center justify-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </a>
                </div>

                <div class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="font-bold text-gray-800">Rapport hebdomadaire - Semaine 16</p>
                        <p class="text-sm text-gray-500 mt-1">Généré le 23/04/2026 - Format Excel</p>
                    </div>
                    <a href="#" class="inline-flex items-center justify-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </a>
                </div>

                <div class="border border-gray-200 rounded-xl p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="font-bold text-gray-800">Rapport journalier - 22/04/2026</p>
                        <p class="text-sm text-gray-500 mt-1">Généré le 22/04/2026 - Format CSV</p>
                    </div>
                    <a href="#" class="inline-flex items-center justify-center bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-download mr-2"></i>Télécharger
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Contenu recommandé dans les rapports</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-50 rounded-xl p-4 border">
                <p class="font-semibold text-gray-800">Recettes</p>
                <p class="text-sm text-gray-500 mt-2">Total encaissé par période et par canal de paiement.</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 border">
                <p class="font-semibold text-gray-800">Dépenses</p>
                <p class="text-sm text-gray-500 mt-2">Charges classées par catégories et fréquence.</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 border">
                <p class="font-semibold text-gray-800">Résultat net</p>
                <p class="text-sm text-gray-500 mt-2">Différence entre les entrées et les sorties.</p>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 border">
                <p class="font-semibold text-gray-800">Anomalies</p>
                <p class="text-sm text-gray-500 mt-2">Paiements échoués, remboursements, écarts éventuels.</p>
            </div>
        </div>
    </div>
</div>
<?php
$adminContent = ob_get_clean();

include __DIR__ . '/../includes/layoutadmin.php';