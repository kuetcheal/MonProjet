<?php
require_once __DIR__ . '/../config.php';

$adminTitle = 'Ressources financières';
$adminWelcome = 'Vue d’ensemble des ressources financières';
$adminNotificationCount = 0;

function formatFcfa($amount)
{
    return number_format((float)$amount, 0, ',', ' ') . ' FCFA';
}

try {
    // Recettes du jour
    $stmt = $pdo->query("
        SELECT COALESCE(SUM(amount), 0) AS total
        FROM paiements
        WHERE status = 'COMPLETED'
          AND DATE(created_at) = CURDATE()
    ");
    $recettesJour = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Recettes du mois
    $stmt = $pdo->query("
        SELECT COALESCE(SUM(amount), 0) AS total
        FROM paiements
        WHERE status = 'COMPLETED'
          AND YEAR(created_at) = YEAR(CURDATE())
          AND MONTH(created_at) = MONTH(CURDATE())
    ");
    $recettesMois = (float)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Paiements en attente
    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM paiements
        WHERE status = 'PENDING'
    ");
    $paiementsEnAttente = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Paiements échoués
    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM paiements
        WHERE status IN ('FAILED', 'REJECTED')
    ");
    $paiementsEchoues = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Réservations payées
    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM reservation
        WHERE statut_paiement = 'paye'
    ");
    $reservationsPayees = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Réservations en attente
    $stmt = $pdo->query("
        SELECT COUNT(*) AS total
        FROM reservation
        WHERE statut_paiement = 'en_attente'
    ");
    $reservationsEnAttente = (int)($stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    // Répartition par provider
    $stmt = $pdo->query("
        SELECT 
            COALESCE(provider, 'INCONNU') AS provider,
            COUNT(*) AS nb,
            COALESCE(SUM(amount), 0) AS total
        FROM paiements
        WHERE status = 'COMPLETED'
        GROUP BY provider
        ORDER BY total DESC
    ");
    $providers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalProviders = array_sum(array_map(fn($row) => (float)$row['total'], $providers));

    // Dernières transactions
    $stmt = $pdo->query("
        SELECT 
            p.deposit_id,
            p.amount,
            p.status,
            p.provider,
            p.phone,
            p.created_at,
            r.nom,
            r.prenom,
            r.Numero_reservation
        FROM paiements p
        LEFT JOIN reservation r ON r.id_reservation = p.reservation_id
        ORDER BY p.created_at DESC
        LIMIT 8
    ");
    $dernieresTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $recettesJour = 0;
    $recettesMois = 0;
    $paiementsEnAttente = 0;
    $paiementsEchoues = 0;
    $reservationsPayees = 0;
    $reservationsEnAttente = 0;
    $providers = [];
    $totalProviders = 0;
    $dernieresTransactions = [];
}

ob_start();
?>

<div class="space-y-6">
    <?php if (!empty($errorMessage)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
            <strong>Erreur :</strong> <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Recettes du jour</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= htmlspecialchars(formatFcfa($recettesJour)) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-coins text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Recettes du mois</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= htmlspecialchars(formatFcfa($recettesMois)) ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Paiements en attente</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= (int)$paiementsEnAttente ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-5 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Réservations payées</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= (int)$reservationsPayees ?></h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-ticket-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-2 bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-xl font-bold text-gray-800">Répartition réelle des encaissements</h2>
                <span class="text-sm text-gray-500">Basé sur la table paiements</span>
            </div>

            <div class="space-y-5">
                <?php if (!empty($providers)): ?>
                    <?php foreach ($providers as $provider): ?>
                        <?php
                        $total = (float)$provider['total'];
                        $percent = $totalProviders > 0 ? round(($total / $totalProviders) * 100, 1) : 0;

                        $label = $provider['provider'];
                        $barColor = 'bg-indigo-500';

                        if ($label === 'ORANGE_CMR') {
                            $label = 'Orange Money';
                            $barColor = 'bg-orange-500';
                        } elseif ($label === 'MTN_CMR') {
                            $label = 'MTN MoMo';
                            $barColor = 'bg-yellow-400';
                        } elseif ($label === 'INCONNU') {
                            $label = 'Autres / Inconnu';
                            $barColor = 'bg-gray-500';
                        }
                        ?>
                        <div>
                            <div class="flex justify-between mb-1 text-sm">
                                <span class="font-medium text-gray-700">
                                    <?= htmlspecialchars($label) ?> — <?= (int)$provider['nb'] ?> transaction(s)
                                </span>
                                <span class="text-gray-500"><?= htmlspecialchars((string)$percent) ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="<?= $barColor ?> h-3 rounded-full" style="width: <?= $percent ?>%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars(formatFcfa($total)) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500">Aucune donnée d’encaissement disponible pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-5">Indicateurs rapides</h2>

            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-yellow-50 border border-yellow-200">
                    <p class="font-semibold text-yellow-700"><?= (int)$paiementsEnAttente ?> paiement(s) en attente</p>
                    <p class="text-sm text-yellow-600 mt-1">À vérifier côté callbacks ou paiements non finalisés.</p>
                </div>

                <div class="p-4 rounded-xl bg-red-50 border border-red-200">
                    <p class="font-semibold text-red-700"><?= (int)$paiementsEchoues ?> paiement(s) échoué(s)</p>
                    <p class="text-sm text-red-600 mt-1">Transactions refusées ou non abouties.</p>
                </div>

                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
                    <p class="font-semibold text-blue-700"><?= (int)$reservationsEnAttente ?> réservation(s) en attente</p>
                    <p class="text-sm text-blue-600 mt-1">Réservations non encore réglées.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold text-gray-800">Dernières transactions</h2>
                <a href="transactions.php" class="text-green-600 font-semibold hover:text-green-800">Voir tout</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-3 text-gray-600">Client</th>
                            <th class="text-left py-3 text-gray-600">Montant</th>
                            <th class="text-left py-3 text-gray-600">Méthode</th>
                            <th class="text-left py-3 text-gray-600">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php if (!empty($dernieresTransactions)): ?>
                            <?php foreach ($dernieresTransactions as $row): ?>
                                <?php
                                $client = trim(($row['prenom'] ?? '') . ' ' . ($row['nom'] ?? ''));
                                if ($client === '') {
                                    $client = 'Client inconnu';
                                }

                                $providerLabel = $row['provider'] ?: 'Inconnu';
                                if ($providerLabel === 'ORANGE_CMR') $providerLabel = 'Orange Money';
                                if ($providerLabel === 'MTN_CMR') $providerLabel = 'MTN MoMo';

                                $statusClass = 'text-gray-700 bg-gray-100';
                                if ($row['status'] === 'COMPLETED') $statusClass = 'text-green-700 bg-green-100';
                                if ($row['status'] === 'PENDING') $statusClass = 'text-yellow-700 bg-yellow-100';
                                if (in_array($row['status'], ['FAILED', 'REJECTED'])) $statusClass = 'text-red-700 bg-red-100';
                                ?>
                                <tr>
                                    <td class="py-3">
                                        <div class="font-medium text-gray-800"><?= htmlspecialchars($client) ?></div>
                                        <div class="text-xs text-gray-500"><?= htmlspecialchars($row['Numero_reservation'] ?? '-') ?></div>
                                    </td>
                                    <td class="py-3 font-semibold text-gray-800"><?= htmlspecialchars(formatFcfa($row['amount'])) ?></td>
                                    <td class="py-3 text-gray-700"><?= htmlspecialchars($providerLabel) ?></td>
                                    <td class="py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $statusClass ?>">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">Aucune transaction pour le moment.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Actions rapides</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <a href="transactions.php" class="rounded-xl bg-green-600 text-white p-4 hover:bg-green-700 transition">
                    <i class="fas fa-money-check-dollar text-lg"></i>
                    <p class="font-bold mt-2">Transactions</p>
                    <p class="text-sm text-green-100 mt-1">Voir les paiements détaillés</p>
                </a>

                <a href="reservations.php" class="rounded-xl bg-blue-600 text-white p-4 hover:bg-blue-700 transition">
                    <i class="fas fa-folder text-lg"></i>
                    <p class="font-bold mt-2">Réservations</p>
                    <p class="text-sm text-blue-100 mt-1">Contrôler les réservations payées</p>
                </a>

                <a href="depenses.php" class="rounded-xl bg-red-600 text-white p-4 hover:bg-red-700 transition">
                    <i class="fas fa-wallet text-lg"></i>
                    <p class="font-bold mt-2">Dépenses</p>
                    <p class="text-sm text-red-100 mt-1">Ajouter tes charges réelles</p>
                </a>

                <a href="rapports_financiers.php" class="rounded-xl bg-indigo-600 text-white p-4 hover:bg-indigo-700 transition">
                    <i class="fas fa-file-invoice-dollar text-lg"></i>
                    <p class="font-bold mt-2">Rapports</p>
                    <p class="text-sm text-indigo-100 mt-1">Préparer les exports</p>
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$adminContent = ob_get_clean();
include __DIR__ . '/../includes/layoutadmin.php';