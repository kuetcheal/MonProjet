<?php
require_once __DIR__ . '/../config.php';

function generateUuidV4()
{
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function cleanPhoneNumber(?string $phone): string
{
    return preg_replace('/\D+/', '', (string) $phone);
}

try {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom = trim($_POST['nom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = cleanPhoneNumber($_POST['telephone'] ?? '');
    $selectedSeat = isset($_POST['selectedSeat']) ? (int) $_POST['selectedSeat'] : 0;

    $idVoyageAller = isset($_POST['idVoyageAller']) && $_POST['idVoyageAller'] !== ''
        ? (int) $_POST['idVoyageAller']
        : null;

    $idVoyageRetour = isset($_POST['idVoyageRetour']) && $_POST['idVoyageRetour'] !== ''
        ? (int) $_POST['idVoyageRetour']
        : null;

    $prixTotal = isset($_POST['prixTotal']) ? (int) round((float) $_POST['prixTotal']) : 0;
    $reservationNumber = trim($_POST['reservationNumber'] ?? '');
    $deliveryMethod = trim($_POST['deliveryMethod'] ?? 'email');

    if ($prenom === '' || $nom === '') {
        throw new Exception("Le nom et le prénom sont obligatoires.");
    }

    if ($telephone === '') {
        throw new Exception("Le numéro de téléphone est obligatoire.");
    }

    if ($selectedSeat <= 0) {
        throw new Exception("Veuillez sélectionner un siège.");
    }

    if ($prixTotal <= 0) {
        throw new Exception("Le montant du paiement est invalide.");
    }

    if (!$idVoyageAller) {
        throw new Exception("Le voyage aller est introuvable.");
    }

    if ($reservationNumber === '') {
        throw new Exception("Le numéro de réservation est introuvable.");
    }

    $depositId = generateUuidV4();

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO reservation (
            user_id,
            nom,
            prenom,
            telephone,
            email,
            idVoyage,
            numeroPlace,
            Etat,
            Numero_reservation,
            Numero_siege,
            prix_reservation,
            statut_paiement
        ) VALUES (
            NULL,
            :nom,
            :prenom,
            :telephone,
            :email,
            :idVoyage,
            :numeroPlace,
            0,
            :numero_reservation,
            :numero_siege,
            :prix_reservation,
            'en_attente'
        )
    ");

    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':telephone' => $telephone,
        ':email' => $email !== '' ? $email : null,
        ':idVoyage' => $idVoyageAller,
        ':numeroPlace' => $selectedSeat,
        ':numero_reservation' => $reservationNumber,
        ':numero_siege' => $selectedSeat,
        ':prix_reservation' => $prixTotal,
    ]);

    $reservationId = (int) $pdo->lastInsertId();

    $stmt = $pdo->prepare("
        INSERT INTO paiements (
            deposit_id,
            reservation_id,
            amount,
            currency,
            status,
            phone
        ) VALUES (
            :deposit_id,
            :reservation_id,
            :amount,
            'XAF',
            'PENDING',
            :phone
        )
    ");

    $stmt->execute([
        ':deposit_id' => $depositId,
        ':reservation_id' => $reservationId,
        ':amount' => $prixTotal,
        ':phone' => $telephone
    ]);

    $payload = [
        "depositId" => $depositId,
        "returnUrl" => PAWAPAY_RETURN_URL,
        "customerMessage" => "EasyTravel",
        "amountDetails" => [
            "amount" => (string) $prixTotal,
            "currency" => "XAF"
        ],
        "phoneNumber" => $telephone,
        "language" => "FR",
        "country" => "CMR",
        "reason" => "Reservation voyage",
        "metadata" => [
            ["reservation_id" => (string) $reservationId],
            ["reservation_number" => $reservationNumber],
            ["delivery_method" => $deliveryMethod],
            ["id_voyage_aller" => (string) $idVoyageAller],
            ["id_voyage_retour" => $idVoyageRetour ? (string) $idVoyageRetour : ""]
        ]
    ];

    $ch = curl_init(PAWAPAY_BASE_URL . "/v2/paymentpage");

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . PAYMENT_API_TOKEN,
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
        "HTTP CODE: " . $httpCode . PHP_EOL .
        "PAYLOAD: " . json_encode($payload, JSON_UNESCAPED_UNICODE) . PHP_EOL .
        "RESPONSE: " . $response . PHP_EOL . PHP_EOL,
        FILE_APPEND
    );

    $result = json_decode($response, true);

    if ($httpCode >= 400) {
        throw new Exception("Erreur pawaPay : " . $response);
    }

    if (!isset($result['redirectUrl'])) {
        throw new Exception("Réponse pawaPay invalide : " . $response);
    }

    $pdo->commit();

    header("Location: " . $result['redirectUrl']);
    exit;
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "<h2>Erreur lors de l'initialisation du paiement</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}