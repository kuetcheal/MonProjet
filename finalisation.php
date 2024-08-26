<?php
session_start();
require __DIR__.'/vendor/autoload.php';

use Mailjet\Client;
use Mailjet\Resources;
use PHPMailer\PHPMailer\Exception;
use TCPDF;

// Fonction pour générer la facture PDF
function generateInvoice($nom, $prenom, $telephone, $email, $reservationNumber, $depart, $arrivee, $date, $idVoyage, $prix)
{
    $pdf = new TCPDF();
    $pdf->AddPage();
    $pdf->SetFont('dejavusans', '', 12);
    $html = "
    <h1>Facture de réservation</h1>
    <p><strong>Nom du passager:</strong> $nom $prenom</p>
    <p><strong>Date de départ:</strong> $date</p>
    <p><strong>Ville de départ:</strong> $depart</p>
    <p><strong>Ville d'arrivée:</strong> $arrivee</p>
    <p><strong>Numéro de réservation:</strong> $reservationNumber</p>
    <p><strong>Prix:</strong> $prix FCFA</p>
    <h1>Merci de nous avoir fait confiance et à très bientôt </h1>
    ";
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdfOutput = $pdf->Output('', 'S'); // S pour retourner le document sous forme de chaîne

    return $pdfOutput;
}

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $_POST['selectedSeat'], $_POST['reservationNumber'], $_POST['submit'])) {
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $telephone = htmlspecialchars($_POST['telephone']);
        $numeroSiege = htmlspecialchars($_POST['selectedSeat']);
        $reservationNumber = htmlspecialchars($_POST['reservationNumber']);

        $etat = 0;
        $depart = $_SESSION['depart'];
        $arrivee = $_SESSION['arrivee'];
        $date = $_SESSION['date'];
        $idVoyage = $_SESSION['idVoyage'];
        $prix = $_SESSION['prix'];



        
         //  Créer le tiers dans Dolibarr
         $dolibarr_url_tiers = 'http://localhost:100/dolibarr/api/index.php/thirdparties';
         $api_key = '809d8187e33a2186b77a7b780ee5fe8219554e79';
 
         $data_tiers = array(
             'name' => $nom . ' ' . $prenom,
             'email' => $email,
             'phone' => $telephone,
             'client' => 1 // Marque le tiers comme un client
         );
 
         $options_tiers = array(
             'http' => array(
                 'header'  => "Content-type: application/json\r\n" .
                              "DOLAPIKEY: $api_key\r\n",
                 'method'  => 'POST',
                 'content' => json_encode($data_tiers)
             )
         );
 
         $context_tiers  = stream_context_create($options_tiers);
         $result_tiers = file_get_contents($dolibarr_url_tiers, false, $context_tiers);
 
         if ($result_tiers === FALSE) {
             echo 'Erreur lors de la création du tiers dans Dolibarr.';
             exit;
         }
         $result_tiers = json_decode($result_tiers, true);
         $socid = $result_tiers['id']; // Récupérer l'ID du tiers
 
         
         
       // Insertion dans la base de données
        $requete = 'INSERT INTO reservation (nom, prenom, telephone, email, idVoyage, Etat, Numero_reservation, Numero_siege) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $bdd->prepare($requete);
        $stmt->execute([$nom, $prenom, $telephone, $email, $idVoyage, $etat, $reservationNumber, $numeroSiege]);

        
        // Générer la facture PDF
        $pdfOutput = generateInvoice($nom, $prenom, $telephone, $email, $reservationNumber, $depart, $arrivee, $date, $idVoyage, $prix);

        
        // Configuration de l'API Mailjet
        $mj = new Client('f163a8d176afbcb29aae519bf6c5e181', 'bf285777b4d59f84a43855ae1b40f96d', true, ['version' => 'v3.1']);
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
                    'HTMLPart' => "<h1>Details de la réservation</h1>
                    <div style='font-family: Arial, sans-serif; padding: 20px; background-color: #f4f4f4;'>
        <div style='background-color: white; padding: 20px; border-radius: 10px;'>
            <h1 style='text-align: center; color: #333;'>Détails de la réservation</h1>
            <div style='display: flex; justify-content: space-between; align-items: center;'>
                <div style='width: 60%;'>
                    <h2 style='color: #555;'>Merci pour votre réservation</h2>
                    <p style='color: #777;'>Vous trouverez votre reçu de réservation ci-joint.</p>
                    <p style='color: #333;'><strong>Numéro de réservation :</strong> $idVoyage</p>
                    <p style='color: #333;'><strong>Compagnie :</strong> Général Voyage</p>
                    <p style='color: #333;'><strong>Passager :</strong> $nom $prenom</p>
                    <p style='color: #333;'><strong>Téléphone :</strong> $telephone</p>
                    <p style='color: #333;'><strong>Numéro de Référence :</strong> $reservationNumber</p>
                     <p style='color: #333;'><strong>Numéro de siège :</strong> $numeroSiege</p>
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

        // Envoi de l'email
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            echo 'Email sent successfully.';
        } else {
            echo 'Failed to send email: '.$response->getData()['ErrorMessage'];
        }


         // 2. Générer une facture dans Dolibarr via l'API REST
         $dolibarr_url_invoice = 'http://localhost:100/dolibarr/api/index.php/invoices';

         
      // Générer une facture dans Dolibarr via l'API REST
      $dolibarr_url = 'http://localhost:100/dolibarr/api/index.php/invoices';
      $api_key = '809d8187e33a2186b77a7b780ee5fe8219554e79';
      
     // Préparer les données de la facture à envoyer à l'API
     $data_invoice = array(
        'socid' => $socid, // Utiliser l'ID du tiers créé
        'lines' => array(
            array(
                'desc' => 'Réservation pour le voyage de ' . $depart . ' à ' . $arrivee,
                'subprice' => $prix,
                'qty' => 1,
                'tva_tx' => 0,
                'total_ht' => $prix,
                'total_tva' => 0,
                'total_ttc' => $prix,
            ),
        ),
        'date' => time(),
        'cond_reglement_id' => 1, // ID de la condition de règlement (par ex., 1 pour paiement comptant)
        'mode_reglement_id' => 1, // ID du mode de règlement (par ex., 1 pour chèque)
        'fk_account' => 1, // ID du compte bancaire
        'note_private' => 'Facture générée automatiquement après réservation',
    );

    $options_invoice = array(
        'http' => array(
            'header'  => "Content-type: application/json\r\n" .
                         "DOLAPIKEY: $api_key\r\n",
            'method'  => 'POST',
            'content' => json_encode($data_invoice)
        )
    );

    $context_invoice  = stream_context_create($options_invoice);
    $result_invoice = file_get_contents($dolibarr_url_invoice, false, $context_invoice);

    if ($result_invoice === FALSE) {
        echo 'Erreur lors de la création de la facture dans Dolibarr.';
    } else {
        echo 'Facture créée avec succès dans Dolibarr.';
    }

    echo "<meta http-equiv='refresh' content='10;url=Accueil.php'>";
    exit;
}
} catch (Exception $e) {
    echo 'Échec de connexion : '.$e->getMessage();
}
?>

<style>
.container {
    height: 100px;
    width: 600px;
    background-color: green;
    color: white;
    font-size: 16px;
}

h1 {
    color: green;
}
</style>