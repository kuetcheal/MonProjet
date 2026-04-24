<?php
require_once __DIR__ . '/../config.php';

$rawData = file_get_contents('php://input');

file_put_contents(
    __DIR__ . '/pawapay-callback-log.txt',
    date('Y-m-d H:i:s') . PHP_EOL . $rawData . PHP_EOL . PHP_EOL,
    FILE_APPEND
);

$data = json_decode($rawData, true);

if (!$data || empty($data['depositId'])) {
    http_response_code(400);
    echo "Invalid callback";
    exit;
}

$depositId = $data['depositId'];
$status = $data['status'] ?? 'UNKNOWN';
$provider = $data['payer']['accountDetails']['provider'] ?? null;
$phone = $data['payer']['accountDetails']['phoneNumber'] ?? null;
$providerTransactionId = $data['providerTransactionId'] ?? null;

$stmt = $pdo->prepare("
    UPDATE paiements
    SET
        status = ?,
        phone = ?,
        provider = ?,
        provider_transaction_id = ?,
        updated_at = NOW()
    WHERE deposit_id = ?
");

$stmt->execute([
    $status,
    $phone,
    $provider,
    $providerTransactionId,
    $depositId
]);

if ($status === 'COMPLETED') {
    $stmt = $pdo->prepare("
        SELECT reservation_id
        FROM paiements
        WHERE deposit_id = ?
        LIMIT 1
    ");
    $stmt->execute([$depositId]);
    $paiement = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($paiement && !empty($paiement['reservation_id'])) {
        $stmt = $pdo->prepare("
            UPDATE reservation
            SET statut_paiement = 'paye'
            WHERE id_reservation = ?
        ");
        $stmt->execute([$paiement['reservation_id']]);
    }
}

http_response_code(200);
echo "OK";