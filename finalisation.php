<?php
session_start();
require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/seat_helpers.php';

use Mailjet\Client;
use Mailjet\Resources;

function generateInvoice($nom, $prenom, $telephone, $email, $reservationNumber, $numeroSiege, $depart, $arrivee, $date, $idVoyage, $prix)
{
    $pdf = new \TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 12);

    $html = "
    <h1 style='text-align:center;color:#16a34a;'>Facture de Réservation</h1>
    <table border='1' cellpadding='8'>
        <tr><th><b>Nom</b></th><td>$nom $prenom</td></tr>
        <tr><th><b>Email</b></th><td>$email</td></tr>
        <tr><th><b>Téléphone</b></th><td>$telephone</td></tr>
        <tr><th><b>Départ</b></th><td>$depart</td></tr>
        <tr><th><b>Arrivée</b></th><td>$arrivee</td></tr>
        <tr><th><b>Date</b></th><td>$date</td></tr>
        <tr><th><b>ID Voyage</b></th><td>$idVoyage</td></tr>
        <tr><th><b>Réservation</b></th><td>$reservationNumber</td></tr>
        <tr><th><b>Siège</b></th><td>$numeroSiege</td></tr>
        <tr><th><b>Total</b></th><td>$prix FCFA</td></tr>
    </table>
    ";

    $pdf->writeHTML($html, true, false, true, false, '');
    return $pdf->Output('', 'S');
}

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

    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $numeroSiege = (int)trim($_POST['selectedSeat']);
    $reservationNumber = htmlspecialchars(trim($_POST['reservationNumber']));
    $deliveryMethod = trim($_POST['deliveryMethod'] ?? 'email');

    if ($numeroSiege <= 0) {
        throw new Exception('Erreur : aucun siège sélectionné.');
    }

    $depart = $_POST['depart'] ?? $_SESSION['depart'] ?? '';
    $arrivee = $_POST['arrivee'] ?? $_SESSION['arrivee'] ?? '';
    $date = $_POST['dateVoyage'] ?? $_SESSION['date'] ?? '';
    $idVoyage = (int)($_POST['idVoyage'] ?? $_SESSION['idVoyage'] ?? 0);
    $prix = isset($_POST['prixTotal']) ? (float) $_POST['prixTotal'] : (float) ($_SESSION['prix'] ?? 0);

    if ($idVoyage <= 0) {
        throw new Exception('Erreur : idVoyage manquant.');
    }

    $voyage = getVoyageById($bdd, $idVoyage);
    if (!$voyage) {
        throw new Exception('Voyage introuvable.');
    }

    $nombrePlaces = (int)($voyage['nombrePlaces'] ?? 0);
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
            prix_reservation
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
        $prix
    ]);

    $bdd->commit();

    $pdfOutput = generateInvoice(
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
        $prix
    );

    $emailMessage = 'Envoi email non demandé.';
    if ($deliveryMethod === 'email' && !empty($email)) {
        $mj = new Client(
            'f163a8d176afbcb29aae519bf6c5e181',
            'bf285777b4d59f84a43855ae1b40f96d',
            true,
            ['version' => 'v3.1']
        );

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'akuetche55@gmail.com',
                        'Name' => 'Easy travel',
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $nom,
                        ],
                    ],
                    'Subject' => 'Confirmation de Réservation',
                    'TextPart' => 'Votre facture est attachée à cet email.',
                    'HTMLPart' => "
                        <h1>Détails de la réservation</h1>
                        <p><strong>ID Voyage :</strong> $idVoyage</p>
                        <p><strong>Passager :</strong> $nom $prenom</p>
                        <p><strong>Téléphone :</strong> $telephone</p>
                        <p><strong>Numéro de Référence :</strong> $reservationNumber</p>
                        <p><strong>Numéro de siège :</strong> $numeroSiege</p>
                        <p><strong>Départ :</strong> $depart</p>
                        <p><strong>Arrivée :</strong> $arrivee</p>
                        <p><strong>Date :</strong> $date</p>
                    ",
                    'Attachments' => [
                        [
                            'ContentType' => 'application/pdf',
                            'Filename' => 'facture.pdf',
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
    $messageLines[] = $emailMessage;
    $messageLines[] = $dolibarrMessage;
    $messageLines[] = 'Redirection en cours vers l’accueil...';

} catch (Exception $e) {
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
<body class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
    <div class="max-w-2xl w-full bg-green-600 text-white p-8 rounded-2xl text-center shadow-xl">
        <h1 class="text-3xl font-bold mb-4"><?= htmlspecialchars($messageTitle) ?></h1>

        <div class="space-y-2 text-lg">
            <?php foreach ($messageLines as $line): ?>
                <p><?= htmlspecialchars($line) ?></p>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>