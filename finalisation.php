<?php
session_start();
require __DIR__ . '/vendor/autoload.php';

use Mailjet\Client;
use Mailjet\Resources;

function generateInvoice($nom, $prenom, $telephone, $email, $reservationNumber, $numeroSiege, $depart, $arrivee, $date, $idVoyage, $prix)
{
    $pdf = new \TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 12);

    $html = "
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .facture-container {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
            font-size: 24px;
        }
        .facture-header {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .facture-details {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .facture-details th, .facture-details td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .facture-details th {
            background-color: #4CAF50;
            color: white;
        }
        .total-section {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 14px;
            color: #777;
        }
    </style>

    <div class='facture-container'>
        <h1>Facture de Réservation</h1>

        <p class='facture-header'>Informations du Passager</p>
        <table class='facture-details'>
            <tr><th>Nom</th><td>$nom $prenom</td></tr>
            <tr><th>Email</th><td>$email</td></tr>
            <tr><th>Téléphone</th><td>$telephone</td></tr>
        </table>

        <p class='facture-header'>Détails du Voyage</p>
        <table class='facture-details'>
            <tr><th>Départ</th><td>$depart</td></tr>
            <tr><th>Arrivée</th><td>$arrivee</td></tr>
            <tr><th>Date</th><td>$date</td></tr>
            <tr><th>ID Voyage</th><td>$idVoyage</td></tr>
            <tr><th>Numéro de Réservation</th><td>$reservationNumber</td></tr>
            <tr><th>Numéro de Siège</th><td>$numeroSiege</td></tr>
        </table>

        <p class='total-section'>Total à Payer : $prix FCFA</p>
        <p class='footer'>Merci de nous avoir fait confiance ! Nous vous souhaitons un excellent voyage.</p>
    </div>
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

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (
        !isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $_POST['selectedSeat'], $_POST['reservationNumber'], $_POST['submit'])
    ) {
        exit('Erreur : données du formulaire manquantes.');
    }

    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $telephone = htmlspecialchars(trim($_POST['telephone']));
    $numeroSiege = htmlspecialchars(trim($_POST['selectedSeat']));
    $reservationNumber = htmlspecialchars(trim($_POST['reservationNumber']));

    if ($numeroSiege === '') {
        exit('Erreur : aucun siège sélectionné.');
    }

    $depart = $_POST['depart'] ?? $_SESSION['depart'] ?? '';
    $arrivee = $_POST['arrivee'] ?? $_SESSION['arrivee'] ?? '';
    $date = $_POST['dateVoyage'] ?? $_SESSION['date'] ?? '';
    $idVoyage = $_POST['idVoyage'] ?? $_SESSION['idVoyage'] ?? null;
    $prix = isset($_POST['prixTotal']) ? (float) $_POST['prixTotal'] : (float) ($_SESSION['prix'] ?? 0);

    if (!$idVoyage) {
        exit('Erreur : idVoyage manquant. Veuillez reprendre la réservation depuis la page de paiement.');
    }

    $_SESSION['depart'] = $depart;
    $_SESSION['arrivee'] = $arrivee;
    $_SESSION['date'] = $date;
    $_SESSION['idVoyage'] = $idVoyage;
    $_SESSION['prix'] = $prix;

    $etat = 0;

    $requete = 'INSERT INTO reservation (nom, prenom, telephone, email, idVoyage, Etat, Numero_reservation, Numero_siege, prix_reservation) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';
    $stmt = $bdd->prepare($requete);
    $stmt->execute([$nom, $prenom, $telephone, $email, $idVoyage, $etat, $reservationNumber, $numeroSiege, $prix]);

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
                    <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
                        <div style='background-color: white; padding: 20px; border-radius: 10px;'>
                            <h1 style='text-align: center; color: #333;'>Détails de la réservation</h1>
                            <div style='display: flex; justify-content: space-between; align-items: center;'>
                                <div style='width: 60%;'>
                                    <h2 style='color: #555;'>Merci pour votre réservation</h2>
                                    <p style='color: #777;'>Vous trouverez votre reçu de réservation ci-joint.</p>
                                    <p style='color: #333;'><strong>ID Voyage :</strong> $idVoyage</p>
                                    <p style='color: #333;'><strong>Compagnie :</strong> Général Voyage</p>
                                    <p style='color: #333;'><strong>Passager :</strong> $nom $prenom</p>
                                    <p style='color: #333;'><strong>Téléphone :</strong> $telephone</p>
                                    <p style='color: #333;'><strong>Numéro de Référence :</strong> $reservationNumber</p>
                                    <p style='color: #333;'><strong>Numéro de siège :</strong> $numeroSiege</p>
                                    <p style='color: #333;'><strong>Départ :</strong> $depart</p>
                                    <p style='color: #333;'><strong>Arrivée :</strong> $arrivee</p>
                                    <p style='color: #333;'><strong>Date :</strong> $date</p>
                                </div>
                                <div style='width: 30%; text-align: right;'>
                                    <img src='logo_general.jpg' alt='logo site' style='width: 100px; height: auto;'/>
                                </div>
                            </div>
                            <hr style='margin: 20px 0; border: 1px solid #ddd;'/>
                            <p style='color: #777;'>Cordialement,</p>
                            <p style='color: #333; font-weight: bold;'>L'équipe de Général Voyage</p>
                        </div>
                    </div>",
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

    // Intégration Dolibarr : on tente, mais on ne bloque pas la réservation si ça échoue
    $apiKey = '809d8187e33a2186b77a7b780ee5fe8219554e79';
    $socid = null;
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

        if ($result_invoice !== false) {
            $dolibarrMessage = 'Intégration Dolibarr effectuée.';
        } else {
            $dolibarrMessage = 'Réservation enregistrée, mais facture Dolibarr non créée.';
        }
    } else {
        $dolibarrMessage = 'Réservation enregistrée, mais Dolibarr est inaccessible.';
    }

    echo "<div class='container'>";
    echo "<h1>Réservation finalisée</h1>";
    echo "<p>$emailMessage</p>";
    echo "<p>$dolibarrMessage</p>";
    echo "<p>Redirection en cours vers l'accueil...</p>";
    echo "</div>";

    echo "<meta http-equiv='refresh' content='5;url=Accueil.php'>";
    exit;

} catch (\Exception $e) {
    echo 'Échec de traitement : ' . $e->getMessage();
}
?>

<style>
.container {
    max-width: 700px;
    margin: 60px auto;
    padding: 24px;
    background-color: #16a34a;
    color: white;
    border-radius: 12px;
    text-align: center;
    font-size: 18px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

h1 {
    color: white;
    margin-bottom: 16px;
}
</style>