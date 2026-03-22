<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/seat_helpers.php';
require_once __DIR__ . '/Reservation/reservation_helpers.php';

use Mailjet\Client;
use Mailjet\Resources;

function postToDolibarr($url, $apiKey, array $data)
{
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\nDOLAPIKEY: $apiKey\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
            'ignore_errors' => true,
            'timeout' => 10
        ]
    ];

    $context = stream_context_create($options);
    $result = @file_get_contents($url, false, $context);

    if ($result === false) {
        return false;
    }

    return json_decode($result, true);
}

$messageTitle = '';
$messageLines = [];
$redirectUrl = 'Accueil.php';

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (
        !isset($_POST['nom'], $_POST['prenom'], $_POST['telephone'], $_POST['selectedSeat'], $_POST['reservationNumber'], $_POST['submit'])
    ) {
        throw new Exception('Erreur : données du formulaire manquantes.');
    }

    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = trim($_POST['telephone']);
    $numeroSiege = (int) trim($_POST['selectedSeat']);
    $reservationNumber = trim($_POST['reservationNumber']);
    $deliveryMethod = trim($_POST['deliveryMethod'] ?? 'email');

    if ($numeroSiege <= 0) {
        throw new Exception('Erreur : aucun siège sélectionné.');
    }

    $depart = trim($_POST['depart'] ?? $_SESSION['depart'] ?? '');
    $arrivee = trim($_POST['arrivee'] ?? $_SESSION['arrivee'] ?? '');
    $date = trim($_POST['dateVoyage'] ?? $_SESSION['date'] ?? '');
    $idVoyage = (int) ($_POST['idVoyage'] ?? $_SESSION['idVoyage'] ?? 0);
    $prix = isset($_POST['prixTotal']) ? (float) $_POST['prixTotal'] : (float) ($_SESSION['prix'] ?? 0);

    if ($idVoyage <= 0) {
        throw new Exception('Erreur : idVoyage manquant.');
    }

    $voyage = getVoyageById($bdd, $idVoyage);
    if (!$voyage) {
        throw new Exception('Voyage introuvable.');
    }

    $nombrePlaces = (int) ($voyage['nombrePlaces'] ?? 0);
    if ($numeroSiege > $nombrePlaces) {
        throw new Exception('Le siège sélectionné dépasse la capacité du bus.');
    }

    if (!isSeatAvailable($bdd, $idVoyage, $numeroSiege)) {
        throw new Exception('Cette place est déjà réservée. Veuillez revenir en arrière et en choisir une autre.');
    }

    $_SESSION['depart'] = $depart;
    $_SESSION['arrivee'] = $arrivee;
    $_SESSION['date'] = $date;
    $_SESSION['idVoyage'] = $idVoyage;
    $_SESSION['prix'] = $prix;

    $etat = 0;
    $qrToken = generateQrToken($reservationNumber, $telephone, $idVoyage, $numeroSiege);
    $qrUrl = buildTicketQrUrl($qrToken);

    $bdd->beginTransaction();

    $requete = '
        INSERT INTO reservation (
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
            qr_token,
            ticket_status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ';

    $stmt = $bdd->prepare($requete);
    $stmt->execute([
        $nom,
        $prenom,
        $telephone,
        $email,
        $idVoyage,
        $numeroSiege,
        $etat,
        $reservationNumber,
        $numeroSiege,
        $prix,
        $qrToken,
        'valid'
    ]);

    $bdd->commit();

    $pdfOutput = generateInvoicePdf(
        $nom,
        $prenom,
        $telephone,
        $email,
        $reservationNumber,
        $numeroSiege,
        $depart,
        $arrivee,
        $date,
        $idVoyage,
        $prix,
        $qrUrl
    );

    $emailMessage = 'Envoi email non demandé.';
    if ($deliveryMethod === 'email' && !empty($email)) {
        $mj = new Client(
            'f163a8d176afbcb29aae519bf6c5e181',
            'bf285777b4d59f84a43855ae1b40f96d',
            true,
            ['version' => 'v3.1']
        );

        $emailHtml = buildReservationEmailHtml(
            $nom,
            $prenom,
            $telephone,
            $reservationNumber,
            $numeroSiege,
            $depart,
            $arrivee,
            $date,
            $idVoyage,
            $prix
        );

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'akuetche55@gmail.com',
                        'Name' => 'Easy Travel',
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $nom . ' ' . $prenom,
                        ],
                    ],
                    'Subject' => 'Confirmation de réservation - Easy Travel',
                    'TextPart' => "Votre réservation a bien été confirmée. Référence : {$reservationNumber}.",
                    'HTMLPart' => $emailHtml,
                    'Attachments' => [
                        [
                            'ContentType' => 'application/pdf',
                            'Filename' => 'billet-reservation.pdf',
                            'Base64Content' => base64_encode($pdfOutput),
                        ],
                    ],
                ],
            ],
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);

        $emailMessage = $response->success()
            ? 'Email envoyé avec succès.'
            : 'Échec de l’envoi de l’email.';
    }

    $apiKey = '809d8187e33a2186b77a7b780ee5fe8219554e79';
    $dolibarrMessage = '';

    $dolibarr_url_tiers = 'http://localhost:100/dolibarr/api/index.php/thirdparties';
    $result_tiers = postToDolibarr($dolibarr_url_tiers, $apiKey, [
        'name' => $nom . ' ' . $prenom,
        'email' => $email,
        'phone' => $telephone,
        'client' => 1
    ]);

    if ($result_tiers !== false && isset($result_tiers['id'])) {
        $socid = $result_tiers['id'];

        $dolibarr_url_invoice = 'http://localhost:100/dolibarr/api/index.php/invoices';
        $result_invoice = postToDolibarr($dolibarr_url_invoice, $apiKey, [
            'socid' => $socid,
            'lines' => [
                [
                    'desc' => 'Réservation pour le voyage de ' . $depart . ' à ' . $arrivee,
                    'subprice' => $prix,
                    'qty' => 1,
                    'tva_tx' => 0,
                    'total_ht' => $prix,
                    'total_tva' => 0,
                    'total_ttc' => $prix,
                ],
            ],
            'date' => time(),
            'cond_reglement_id' => 1,
            'mode_reglement_id' => 1,
            'fk_account' => 1,
            'note_private' => 'Facture générée automatiquement après réservation',
        ]);

        $dolibarrMessage = $result_invoice !== false
            ? 'Intégration Dolibarr effectuée.'
            : 'Réservation enregistrée, mais facture Dolibarr non créée.';
    } else {
        $dolibarrMessage = 'Réservation enregistrée, mais Dolibarr est inaccessible.';
    }

    $messageTitle = 'Réservation finalisée';
    $messageLines[] = 'Votre réservation a bien été enregistrée.';
    $messageLines[] = $emailMessage;
    $messageLines[] = $dolibarrMessage;
    $messageLines[] = 'Redirection en cours vers l’accueil...';

} catch (Exception $e) {
    if (isset($bdd) && $bdd instanceof PDO && $bdd->inTransaction()) {
        $bdd->rollBack();
    }

    $messageTitle = 'Échec de traitement';
    $messageLines[] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="5;url=Accueil.php">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Finalisation</title>
</head>
<body class="min-h-screen bg-gradient-to-br from-green-50 to-slate-100 flex items-center justify-center px-4">
    <div class="max-w-2xl w-full bg-white border border-green-100 shadow-2xl rounded-3xl overflow-hidden">
        <div class="bg-green-600 text-white p-8 text-center">
            <h1 class="text-3xl font-bold mb-2"><?= htmlspecialchars($messageTitle) ?></h1>
            <p class="text-green-100">Traitement de votre réservation Easy Travel</p>
        </div>

        <div class="p-8">
            <div class="space-y-3 text-slate-700 text-lg">
                <?php foreach ($messageLines as $line): ?>
                    <div class="flex items-start gap-3 bg-slate-50 rounded-xl px-4 py-3 border border-slate-100">
                        <span class="text-green-600 font-bold">✓</span>
                        <p><?= htmlspecialchars($line) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-8 text-center">
                <a href="Accueil.php" class="inline-block bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded-xl transition">
                    Retour à l’accueil
                </a>
            </div>
        </div>
    </div>
</body>
</html>