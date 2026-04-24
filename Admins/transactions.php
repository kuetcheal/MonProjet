<?php
require_once __DIR__ . '/../config.php';

$adminTitle = 'Transactions';
$adminWelcome = 'Suivi des transactions financières';
$adminNotificationCount = 0;

$search = trim($_GET['search'] ?? '');
$method = trim($_GET['method'] ?? '');
$status = trim($_GET['status'] ?? '');
$date = trim($_GET['date'] ?? '');

$where = [];
$params = [];

if ($search !== '') {
    $where[] = "(
        p.deposit_id LIKE :search
        OR p.phone LIKE :search
        OR r.nom LIKE :search
        OR r.prenom LIKE :search
        OR r.Numero_reservation LIKE :search
    )";
    $params[':search'] = '%' . $search . '%';
}

if ($method !== '') {
    $where[] = "p.provider = :provider";
    $params[':provider'] = $method;
}

if ($status !== '') {
    $where[] = "p.status = :status";
    $params[':status'] = $status;
}

if ($date !== '') {
    $where[] = "DATE(p.created_at) = :date_filter";
    $params[':date_filter'] = $date;
}

$sqlWhere = '';
if (!empty($where)) {
    $sqlWhere = 'WHERE ' . implode(' AND ', $where);
}

try {
    $sql = "
        SELECT
            p.deposit_id,
            p.amount,
            p.currency,
            p.status,
            p.phone,
            p.provider,
            p.provider_transaction_id,
            p.created_at,
            r.id_reservation,
            r.nom,
            r.prenom,
            r.email,
            r.Numero_reservation,
            r.statut_paiement,
            r.idVoyage
        FROM paiements p
        LEFT JOIN reservation r ON r.id_reservation = p.reservation_id
        $sqlWhere
        ORDER BY p.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmtCount = $pdo->query("
        SELECT
            COUNT(*) AS total,
            COALESCE(SUM(CASE WHEN status = 'COMPLETED' THEN amount ELSE 0 END), 0) AS total_completed,
            COALESCE(SUM(CASE WHEN status = 'PENDING' THEN amount ELSE 0 END), 0) AS total_pending
        FROM paiements
    ");
    $stats = $stmtCount->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    $errorMessage = $e->getMessage();
    $transactions = [];
    $stats = [
        'total' => 0,
        'total_completed' => 0,
        'total_pending' => 0
    ];
}

function formatFcfa($amount)
{
    return number_format((float)$amount, 0, ',', ' ') . ' FCFA';
}

function providerLabel($provider)
{
    if ($provider === 'ORANGE_CMR') return 'Orange Money';
    if ($provider === 'MTN_CMR') return 'MTN MoMo';
    if (!$provider) return 'Inconnu';
    return $provider;
}

ob_start();
?>

<div class="space-y-6">
    <?php if (!empty($errorMessage)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4">
            <strong>Erreur :</strong> <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500">Nombre total de transactions</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-2"><?= (int)$stats['total'] ?></h3>
        </div>

        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500">Montant complété</p>
            <h3 class="text-3xl font-bold text-green-600 mt-2"><?= htmlspecialchars(formatFcfa($stats['total_completed'])) ?></h3>
        </div>

        <div class="bg-white rounded-2xl shadow p-5">
            <p class="text-sm text-gray-500">Montant en attente</p>
            <h3 class="text-3xl font-bold text-yellow-600 mt-2"><?= htmlspecialchars(formatFcfa($stats['total_pending'])) ?></h3>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-5">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Historique des transactions</h2>
                <p class="text-gray-500 mt-1">Données réelles depuis la table <code>paiements</code></p>
            </div>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($search) ?>"
                placeholder="Référence, téléphone, client..."
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
            >

            <select name="method" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Tous les moyens</option>
                <option value="ORANGE_CMR" <?= $method === 'ORANGE_CMR' ? 'selected' : '' ?>>Orange Money</option>
                <option value="MTN_CMR" <?= $method === 'MTN_CMR' ? 'selected' : '' ?>>MTN MoMo</option>
            </select>

            <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Tous les statuts</option>
                <option value="COMPLETED" <?= $status === 'COMPLETED' ? 'selected' : '' ?>>COMPLETED</option>
                <option value="PENDING" <?= $status === 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                <option value="FAILED" <?= $status === 'FAILED' ? 'selected' : '' ?>>FAILED</option>
                <option value="REJECTED" <?= $status === 'REJECTED' ? 'selected' : '' ?>>REJECTED</option>
            </select>

            <input
                type="date"
                name="date"
                value="<?= htmlspecialchars($date) ?>"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"
            >

            <button type="submit" class="bg-green-600 text-white rounded-lg px-4 py-3 font-semibold hover:bg-green-700 transition">
                Rechercher
            </button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Date</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Référence paiement</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Réservation</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Client</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Téléphone</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Méthode</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Montant</th>
                        <th class="text-left px-6 py-4 font-semibold text-gray-600">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (!empty($transactions)): ?>
                        <?php foreach ($transactions as $transaction): ?>
                            <?php
                            $statusClass = 'bg-gray-100 text-gray-700';
                            if ($transaction['status'] === 'COMPLETED') {
                                $statusClass = 'bg-green-100 text-green-700';
                            } elseif ($transaction['status'] === 'PENDING') {
                                $statusClass = 'bg-yellow-100 text-yellow-700';
                            } elseif (in_array($transaction['status'], ['FAILED', 'REJECTED'])) {
                                $statusClass = 'bg-red-100 text-red-700';
                            }

                            $client = trim(($transaction['prenom'] ?? '') . ' ' . ($transaction['nom'] ?? ''));
                            if ($client === '') {
                                $client = 'Client inconnu';
                            }
                            ?>
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                    <?= htmlspecialchars($transaction['created_at'] ?? '-') ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800"><?= htmlspecialchars($transaction['deposit_id']) ?></div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        Provider TX: <?= htmlspecialchars($transaction['provider_transaction_id'] ?? '-') ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">
                                        <?= htmlspecialchars($transaction['Numero_reservation'] ?? '-') ?>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        ID réservation: <?= htmlspecialchars((string)($transaction['id_reservation'] ?? '-')) ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800"><?= htmlspecialchars($client) ?></div>
                                    <div class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($transaction['email'] ?? '-') ?></div>
                                </td>

                                <td class="px-6 py-4 text-gray-700">
                                    <?= htmlspecialchars($transaction['phone'] ?? '-') ?>
                                </td>

                                <td class="px-6 py-4 text-gray-700">
                                    <?= htmlspecialchars(providerLabel($transaction['provider'] ?? '')) ?>
                                </td>

                                <td class="px-6 py-4 font-bold text-gray-800">
                                    <?= htmlspecialchars(formatFcfa($transaction['amount'])) ?>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="mb-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?= $statusClass ?>">
                                            <?= htmlspecialchars($transaction['status']) ?>
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Réservation: <?= htmlspecialchars($transaction['statut_paiement'] ?? '-') ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                Aucune transaction trouvée.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$adminContent = ob_get_clean();
include __DIR__ . '/../includes/layoutadmin.php';