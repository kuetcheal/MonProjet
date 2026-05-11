<?php
require_once __DIR__ . '/../config.php';

header('Content-Type: text/plain; charset=utf-8');

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

$provider = $data['payer']['accountDetails']['provider']
    ?? $data['customer']['accountDetails']['provider']
    ?? null;

$phone = $data['payer']['accountDetails']['phoneNumber']
    ?? $data['customer']['accountDetails']['phoneNumber']
    ?? null;

$providerTransactionId = $data['providerTransactionId'] ?? null;

$failureCode = $data['failureReason']['failureCode']
    ?? $data['failureReason']['code']
    ?? null;

$failureMessage = $data['failureReason']['failureMessage']
    ?? $data['failureReason']['message']
    ?? null;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT
            p.*,
            r.type_reservation,
            r.statut_paiement,
            r.id_reservation
        FROM paiements p
        INNER JOIN reservation r ON r.id_reservation = p.reservation_id
        WHERE p.deposit_id = :deposit_id
        LIMIT 1
        FOR UPDATE
    ");

    $stmt->execute([
        ':deposit_id' => $depositId
    ]);

    $paiement = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$paiement) {
        $pdo->rollBack();
        http_response_code(404);
        echo "Payment not found";
        exit;
    }

    $reservationId = (int)$paiement['reservation_id'];
    $typeReservation = $paiement['type_reservation'] ?? 'bus';

    $isFinalStatus = in_array($status, [
        'COMPLETED',
        'FAILED',
        'REJECTED',
        'CANCELLED',
        'DUPLICATE_IGNORED'
    ], true);

    if ($isFinalStatus) {
        $stmt = $pdo->prepare("
            UPDATE paiements
            SET
                status = :status,
                phone = COALESCE(:phone, phone),
                provider = COALESCE(:provider, provider),
                provider_transaction_id = :provider_transaction_id,
                failure_code = :failure_code,
                failure_message = :failure_message,
                raw_callback = :raw_callback,
                completed_at = COALESCE(completed_at, NOW()),
                updated_at = NOW()
            WHERE deposit_id = :deposit_id
        ");
    } else {
        $stmt = $pdo->prepare("
            UPDATE paiements
            SET
                status = :status,
                phone = COALESCE(:phone, phone),
                provider = COALESCE(:provider, provider),
                provider_transaction_id = :provider_transaction_id,
                failure_code = :failure_code,
                failure_message = :failure_message,
                raw_callback = :raw_callback,
                updated_at = NOW()
            WHERE deposit_id = :deposit_id
        ");
    }

    $stmt->execute([
        ':status' => $status,
        ':phone' => $phone,
        ':provider' => $provider,
        ':provider_transaction_id' => $providerTransactionId,
        ':failure_code' => $failureCode,
        ':failure_message' => $failureMessage,
        ':raw_callback' => $rawData,
        ':deposit_id' => $depositId
    ]);

    if ($status === 'COMPLETED') {
        if ($typeReservation === 'covoiturage') {
            $stmt = $pdo->prepare("
                UPDATE reservation
                SET statut_paiement = 'payee',
                    paid_at = COALESCE(paid_at, NOW()),
                    statut_reversement_chauffeur = 'en_attente'
                WHERE id_reservation = :reservation_id
            ");
        } else {
            $stmt = $pdo->prepare("
                UPDATE reservation
                SET statut_paiement = 'payee',
                    paid_at = COALESCE(paid_at, NOW())
                WHERE id_reservation = :reservation_id
            ");
        }

        $stmt->execute([
            ':reservation_id' => $reservationId
        ]);
    }

    if (in_array($status, ['FAILED', 'REJECTED', 'CANCELLED'], true)) {
        $stmt = $pdo->prepare("
            UPDATE reservation
            SET statut_paiement = 'paiement_echoue'
            WHERE id_reservation = :reservation_id
            AND statut_paiement <> 'payee'
        ");

        $stmt->execute([
            ':reservation_id' => $reservationId
        ]);
    }

    $pdo->commit();

    http_response_code(200);
    echo "OK";
    exit;

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    file_put_contents(
        __DIR__ . '/pawapay-callback-log.txt',
        date('Y-m-d H:i:s') . PHP_EOL .
        "ERREUR CALLBACK: " . $e->getMessage() . PHP_EOL . PHP_EOL,
        FILE_APPEND
    );

    http_response_code(500);
    echo "Server error";
    exit;
}