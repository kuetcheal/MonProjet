<?php
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Mailjet\Resources;

header('Content-Type: application/json');

function getApplicationBaseUrl(): string
{
    if (defined('APP_URL') && APP_URL !== '') {
        return rtrim(APP_URL, '/');
    }

    return 'https://www.easy-travel.app';
}

function envoyerMailReservationAccepteeMailjet(
    string $email,
    string $nomClient,
    string $villeDepart,
    string $villeArrivee,
    string $jourDepart,
    string $heureDepart,
    int $reservationId,
    bool $paiementRequis
): bool {
    $mj = new \Mailjet\Client(
        MAILJET_PUBLIC_KEY,
        MAILJET_PRIVATE_KEY,
        true,
        ['version' => 'v3.1']
    );

    $baseUrl = getApplicationBaseUrl();

    if ($paiementRequis) {
        $lienAction = $baseUrl . '/paiement/payer-covoiturage.php?id_reservation=' . $reservationId;
        $texteAction = "Votre demande a été acceptée. Pour confirmer votre place, veuillez effectuer le paiement.";
        $boutonAction = "Payer et confirmer ma place";
    } else {
        $lienAction = $baseUrl . '/covoiturage/demande-envoyee.php?id_reservation=' . $reservationId;
        $texteAction = "Votre demande a été acceptée. Votre place est confirmée.";
        $boutonAction = "Voir ma demande";
    }

    $nomClientSafe = htmlspecialchars($nomClient, ENT_QUOTES, 'UTF-8');
    $villeDepartSafe = htmlspecialchars($villeDepart, ENT_QUOTES, 'UTF-8');
    $villeArriveeSafe = htmlspecialchars($villeArrivee, ENT_QUOTES, 'UTF-8');
    $jourDepartSafe = htmlspecialchars($jourDepart, ENT_QUOTES, 'UTF-8');
    $heureDepartCourt = substr($heureDepart, 0, 5);
    $heureDepartSafe = htmlspecialchars($heureDepartCourt, ENT_QUOTES, 'UTF-8');
    $lienActionSafe = htmlspecialchars($lienAction, ENT_QUOTES, 'UTF-8');
    $texteActionSafe = htmlspecialchars($texteAction, ENT_QUOTES, 'UTF-8');
    $boutonActionSafe = htmlspecialchars($boutonAction, ENT_QUOTES, 'UTF-8');

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => MAIL_FROM_EMAIL,
                    'Name' => MAIL_FROM_NAME,
                ],
                'To' => [
                    [
                        'Email' => $email,
                        'Name' => $nomClient,
                    ],
                ],
                'Subject' => 'Votre demande de covoiturage a été acceptée',
                'TextPart' => "Bonjour $nomClient,\n\n"
                    . "Bonne nouvelle ! Votre demande de covoiturage $villeDepart → $villeArrivee du $jourDepart à $heureDepartCourt a été acceptée.\n\n"
                    . $texteAction . "\n\n"
                    . "Lien : $lienAction\n\n"
                    . "Merci d'utiliser EasyTravel.",
                'HTMLPart' => "
                    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #222;'>
                        <h2 style='color: #15803d;'>Demande de covoiturage acceptée</h2>

                        <p>Bonjour <strong>$nomClientSafe</strong>,</p>

                        <p>Bonne nouvelle ! Votre demande de covoiturage a été acceptée par le chauffeur.</p>

                        <div style='background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <p style='margin: 0 0 8px 0;'>
                                <strong>Trajet :</strong> $villeDepartSafe → $villeArriveeSafe
                            </p>
                            <p style='margin: 0;'>
                                <strong>Date :</strong> $jourDepartSafe à $heureDepartSafe
                            </p>
                        </div>

                        <p>$texteActionSafe</p>

                        <p style='margin-top: 25px;'>
                            <a href='$lienActionSafe'
                               style='display: inline-block; background: #15803d; color: #ffffff; padding: 12px 22px; border-radius: 8px; text-decoration: none; font-weight: bold;'>
                                $boutonActionSafe
                            </a>
                        </p>

                        <p style='margin-top: 25px;'>Merci d'utiliser EasyTravel.</p>
                    </div>
                ",
            ],
        ],
    ];

    try {
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    } catch (Exception $e) {
        return false;
    }
}

$chauffeurId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($chauffeurId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

$reservationId = isset($_POST['reservation_id']) ? (int)$_POST['reservation_id'] : 0;

if ($reservationId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Réservation invalide.'
    ]);
    exit;
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        SELECT 
            r.id_reservation,
            r.type_reservation,
            r.user_id AS client_id,
            r.idVoyage AS voyage_id,
            r.statut_demande,
            r.statut_paiement,
            r.Etat,
            r.nom,
            r.prenom,
            r.email,
            r.prix_reservation,

            v.chauffeur_id,
            v.nombrePlaces,
            v.nombre_places_disponibles,
            v.villeDepart,
            v.villeArrivee,
            v.jourDepart,
            v.heureDepart
        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        WHERE r.id_reservation = :reservation_id
        FOR UPDATE
    ");

    $stmt->execute([
        ':reservation_id' => $reservationId
    ]);

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Réservation introuvable.'
        ]);
        exit;
    }

    if (($reservation['type_reservation'] ?? '') !== 'covoiturage') {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Cette réservation n’est pas une demande de covoiturage.'
        ]);
        exit;
    }

    if ((int)$reservation['chauffeur_id'] !== $chauffeurId) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Vous n’êtes pas autorisé à accepter cette demande.'
        ]);
        exit;
    }

    if (($reservation['statut_demande'] ?? '') !== 'en_attente') {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Cette demande a déjà été traitée.'
        ]);
        exit;
    }

    $placesDisponibles = $reservation['nombre_places_disponibles'] !== null
        ? (int)$reservation['nombre_places_disponibles']
        : (int)$reservation['nombrePlaces'];

    if ($placesDisponibles <= 0) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Il n’y a plus de places disponibles pour ce trajet.'
        ]);
        exit;
    }

    $clientId = (int)$reservation['client_id'];
    $voyageId = (int)$reservation['voyage_id'];

    if ($clientId <= 0) {
        $pdo->rollBack();

        echo json_encode([
            'success' => false,
            'message' => 'Impossible de créer la conversation : client introuvable.'
        ]);
        exit;
    }

    $prixReservation = (int)round((float)$reservation['prix_reservation']);

    /*
        Si le trajet est offert ou à 0 FCFA, le client ne paie pas.
        Sinon, il devra payer après acceptation.
    */
    $paiementRequis = !(
        $prixReservation <= 0
        || ($reservation['statut_paiement'] ?? '') === 'offerte'
    );

    $statutPaiementApresAcceptation = $paiementRequis ? 'en_attente' : 'offerte';

    /*
        Pour l’instant, commission à 0.
        Tu pourras modifier plus tard :
        exemple : commission 10%, montant chauffeur = prix - commission.
    */
    $commissionPlateforme = 0;
    $montantChauffeur = max($prixReservation - $commissionPlateforme, 0);

    /*
        1. On accepte la demande.
        Si paiement requis, le client aura 30 minutes pour payer.
    */
    $updateReservation = $pdo->prepare("
        UPDATE reservation
        SET statut_demande = 'acceptee',
            Etat = 1,
            statut_paiement = :statut_paiement,
            payment_deadline_at = CASE 
                WHEN :paiement_requis = 1 THEN DATE_ADD(NOW(), INTERVAL 30 MINUTE)
                ELSE NULL
            END,
            montant_chauffeur = :montant_chauffeur,
            commission_plateforme = :commission_plateforme,
            statut_reversement_chauffeur = 'non_eligible'
        WHERE id_reservation = :reservation_id
    ");

    $updateReservation->execute([
        ':statut_paiement' => $statutPaiementApresAcceptation,
        ':paiement_requis' => $paiementRequis ? 1 : 0,
        ':montant_chauffeur' => $montantChauffeur,
        ':commission_plateforme' => $commissionPlateforme,
        ':reservation_id' => $reservationId
    ]);

    /*
        2. On bloque une place dès l’acceptation.
        Si le client ne paie pas avant expiration, on remettra la place plus tard.
    */
    $updatePlaces = $pdo->prepare("
        UPDATE voyage
        SET nombre_places_disponibles = GREATEST(
            COALESCE(nombre_places_disponibles, nombrePlaces) - 1,
            0
        )
        WHERE idVoyage = :voyage_id
    ");

    $updatePlaces->execute([
        ':voyage_id' => $voyageId
    ]);

    /*
        3. On crée ou récupère la conversation.
        Même si le paiement n’est pas encore fait, la conversation existe.
        Sur tes pages, on affichera le bouton discuter seulement après paiement confirmé.
    */
    $checkConversation = $pdo->prepare("
        SELECT id
        FROM conversations
        WHERE reservation_id = :reservation_id
        LIMIT 1
    ");

    $checkConversation->execute([
        ':reservation_id' => $reservationId
    ]);

    $existingConversation = $checkConversation->fetch(PDO::FETCH_ASSOC);

    if ($existingConversation) {
        $conversationId = (int)$existingConversation['id'];

        $updateConversation = $pdo->prepare("
            UPDATE conversations
            SET statut = 'active',
                updated_at = NOW()
            WHERE id = :conversation_id
        ");

        $updateConversation->execute([
            ':conversation_id' => $conversationId
        ]);
    } else {
        $createConversation = $pdo->prepare("
            INSERT INTO conversations (
                reservation_id,
                voyage_id,
                client_id,
                chauffeur_id,
                statut,
                created_at,
                updated_at
            ) VALUES (
                :reservation_id,
                :voyage_id,
                :client_id,
                :chauffeur_id,
                'active',
                NOW(),
                NOW()
            )
        ");

        $createConversation->execute([
            ':reservation_id' => $reservationId,
            ':voyage_id' => $voyageId,
            ':client_id' => $clientId,
            ':chauffeur_id' => $chauffeurId
        ]);

        $conversationId = (int)$pdo->lastInsertId();
    }

    $pdo->commit();

    /*
        4. Après validation en base, on envoie l’email au client.
        Même si l’email échoue, la demande reste acceptée.
    */
    $nomClient = trim(($reservation['prenom'] ?? '') . ' ' . ($reservation['nom'] ?? ''));
    $emailClient = trim($reservation['email'] ?? '');

    $mailEnvoye = false;

    if ($emailClient !== '' && filter_var($emailClient, FILTER_VALIDATE_EMAIL)) {
        $mailEnvoye = envoyerMailReservationAccepteeMailjet(
            $emailClient,
            $nomClient !== '' ? $nomClient : 'Client',
            $reservation['villeDepart'] ?? '',
            $reservation['villeArrivee'] ?? '',
            $reservation['jourDepart'] ?? '',
            $reservation['heureDepart'] ?? '',
            $reservationId,
            $paiementRequis
        );
    }

    $paymentUrl = getApplicationBaseUrl() . '/paiement/payer-covoiturage.php?id_reservation=' . $reservationId;
    $demandeUrl = getApplicationBaseUrl() . '/covoiturage/demande-envoyee.php?id_reservation=' . $reservationId;

    echo json_encode([
        'success' => true,
        'message' => $mailEnvoye
            ? 'Demande acceptée. Un email a été envoyé au client.'
            : 'Demande acceptée, mais l’email n’a pas pu être envoyé.',
        'conversation_id' => $conversationId,
        'mail_sent' => $mailEnvoye,
        'paiement_requis' => $paiementRequis,
        'payment_url' => $paymentUrl,
        'demande_url' => $demandeUrl
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}