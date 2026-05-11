<?php
session_start();

require_once __DIR__ . '/../config.php';

function generateUuidV4(): string
{
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function cleanPhoneNumber(?string $phone): string
{
    $phone = preg_replace('/\D+/', '', (string)$phone);

    if (strlen($phone) === 9) {
        $phone = '237' . $phone;
    }

    return $phone;
}

function getApplicationBaseUrl(): string
{
    if (defined('APP_URL') && APP_URL !== '') {
        return rtrim(APP_URL, '/');
    }

    return 'https://www.easy-travel.app';
}

function getPawaPayToken(): string
{
    if (defined('PAYMENT_API_TOKEN') && PAYMENT_API_TOKEN !== '') {
        return PAYMENT_API_TOKEN;
    }

    if (defined('PAWAPAY_API_TOKEN') && PAWAPAY_API_TOKEN !== '') {
        return PAWAPAY_API_TOKEN;
    }

    throw new Exception("Token pawaPay introuvable dans config.php.");
}

$userId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../connexion.php');
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Méthode invalide.");
    }

    $reservationId = isset($_POST['id_reservation']) ? (int)$_POST['id_reservation'] : 0;
    $telephone = cleanPhoneNumber($_POST['phone'] ?? '');

    if ($reservationId <= 0) {
        throw new Exception("Réservation invalide.");
    }

    if ($telephone === '' || strlen($telephone) < 12) {
        throw new Exception("Numéro Mobile Money invalide. Exemple attendu : 2376XXXXXXXX.");
    }

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT
            r.*,
            v.villeDepart,
            v.villeArrivee,
            v.jourDepart,
            v.heureDepart
        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        WHERE r.id_reservation = :reservation_id
        AND r.user_id = :user_id
        AND r.type_reservation = 'covoiturage'
        LIMIT 1
        FOR UPDATE
    ");

    $stmt->execute([
        ':reservation_id' => $reservationId,
        ':user_id' => $userId
    ]);

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        throw new Exception("Réservation introuvable ou accès refusé.");
    }

    if (($reservation['statut_demande'] ?? '') !== 'acceptee') {
        throw new Exception("Cette demande n’est pas encore acceptée par le chauffeur.");
    }

    if (($reservation['statut_paiement'] ?? '') === 'payee') {
        $pdo->rollBack();
        header('Location: ../covoiturage/demande-envoyee.php?id_reservation=' . $reservationId);
        exit;
    }

    if (($reservation['statut_paiement'] ?? '') === 'offerte') {
        $pdo->rollBack();
        header('Location: ../covoiturage/demande-envoyee.php?id_reservation=' . $reservationId);
        exit;
    }

    if (!empty($reservation['payment_deadline_at']) && strtotime($reservation['payment_deadline_at']) < time()) {
        throw new Exception("Le délai de paiement est dépassé.");
    }

    $montant = (int)round((float)$reservation['prix_reservation']);

    if ($montant <= 0) {
        throw new Exception("Montant de paiement invalide.");
    }

    $depositId = generateUuidV4();

    $stmt = $pdo->prepare("
        INSERT INTO paiements (
            type_paiement,
            deposit_id,
            reservation_id,
            amount,
            currency,
            status,
            phone,
            created_at,
            updated_at
        ) VALUES (
            'deposit_client',
            :deposit_id,
            :reservation_id,
            :amount,
            'XAF',
            'PENDING',
            :phone,
            NOW(),
            NOW()
        )
    ");

    $stmt->execute([
        ':deposit_id' => $depositId,
        ':reservation_id' => $reservationId,
        ':amount' => $montant,
        ':phone' => $telephone
    ]);

    $stmt = $pdo->prepare("
        UPDATE reservation
        SET statut_paiement = 'paiement_en_cours'
        WHERE id_reservation = :reservation_id
    ");

    $stmt->execute([
        ':reservation_id' => $reservationId
    ]);

    $pdo->commit();

    $returnUrl = getApplicationBaseUrl() . '/paiement/pawapay-return.php';

    $payload = [
        "depositId" => $depositId,
        "returnUrl" => $returnUrl,
        "customerMessage" => "EasyTravel",
        "amountDetails" => [
            "amount" => (string)$montant,
            "currency" => "XAF"
        ],
        "phoneNumber" => $telephone,
        "language" => "FR",
        "country" => "CMR",
        "reason" => "Covoiturage",
        "metadata" => [
            ["reservation_id" => (string)$reservationId],
            ["type_reservation" => "covoiturage"],
            ["ville_depart" => (string)$reservation['villeDepart']],
            ["ville_arrivee" => (string)$reservation['villeArrivee']]
        ]
    ];

    $ch = curl_init(rtrim(PAWAPAY_BASE_URL, '/') . "/v2/paymentpage");

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . getPawaPayToken(),
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception("Erreur CURL : " . curl_error($ch));
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    file_put_contents(
        __DIR__ . '/pawapay-init-log.txt',
        date('Y-m-d H:i:s') . PHP_EOL .
        "TYPE: COVOITURAGE" . PHP_EOL .
        "HTTP CODE: " . $httpCode . PHP_EOL .
        "DEPOSIT ID: " . $depositId . PHP_EOL .
        "PAYLOAD: " . json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL .
        "RESPONSE: " . $response . PHP_EOL . PHP_EOL,
        FILE_APPEND
    );

    $result = json_decode($response, true);

    $stmt = $pdo->prepare("
        UPDATE paiements
        SET raw_response = :raw_response,
            status = :status,
            updated_at = NOW()
        WHERE deposit_id = :deposit_id
    ");

    $stmt->execute([
        ':raw_response' => $response,
        ':status' => $result['status'] ?? 'PENDING',
        ':deposit_id' => $depositId
    ]);

    if ($httpCode >= 400) {
        $stmt = $pdo->prepare("
            UPDATE reservation
            SET statut_paiement = 'paiement_echoue'
            WHERE id_reservation = :reservation_id
        ");
        $stmt->execute([':reservation_id' => $reservationId]);

        throw new Exception("Erreur pawaPay : " . $response);
    }

    if (empty($result['redirectUrl'])) {
        $stmt = $pdo->prepare("
            UPDATE reservation
            SET statut_paiement = 'paiement_echoue'
            WHERE id_reservation = :reservation_id
        ");
        $stmt->execute([':reservation_id' => $reservationId]);

        throw new Exception("Réponse pawaPay invalide : " . $response);
    }

    header("Location: " . $result['redirectUrl']);
    exit;

} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "<h2>Erreur lors de l'initialisation du paiement</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
    echo "<p><a href='../Accueil.php'>Retour à l'accueil</a></p>";
}