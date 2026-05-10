<?php
session_start();

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use Mailjet\Resources;

header('Content-Type: application/json');

/**
 * Envoie un email au client lorsque sa demande de covoiturage est acceptée.
 */
function envoyerMailReservationAccepteeMailjet(
    string $email,
    string $nomClient,
    string $villeDepart,
    string $villeArrivee,
    string $jourDepart,
    string $heureDepart
): bool {
    $mj = new \Mailjet\Client(
        MAILJET_PUBLIC_KEY,
        MAILJET_PRIVATE_KEY,
        true,
        ['version' => 'v3.1']
    );

    $nomClientSafe = htmlspecialchars($nomClient, ENT_QUOTES, 'UTF-8');
    $villeDepartSafe = htmlspecialchars($villeDepart, ENT_QUOTES, 'UTF-8');
    $villeArriveeSafe = htmlspecialchars($villeArrivee, ENT_QUOTES, 'UTF-8');
    $jourDepartSafe = htmlspecialchars($jourDepart, ENT_QUOTES, 'UTF-8');
    $heureDepartSafe = htmlspecialchars(substr($heureDepart, 0, 5), ENT_QUOTES, 'UTF-8');

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
                'TextPart' => "Bonjour $nomClient, votre demande de covoiturage $villeDepart → $villeArrivee du $jourDepart à " . substr($heureDepart, 0, 5) . " a été acceptée.",
                'HTMLPart' => "
                    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #222;'>
                        <h2 style='color: #15803d;'>Demande de covoiturage acceptée</h2>

                        <p>Bonjour <strong>$nomClientSafe</strong>,</p>

                        <p>Bonne nouvelle ! Votre demande de covoiturage a été acceptée par le chauffeur.</p>

                        <div style='background: #f0fdf4; border: 1px solid #bbf7d0; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                            <p style='margin: 0 0 8px 0;'><strong>Trajet :</strong> $villeDepartSafe → $villeArriveeSafe</p>
                            <p style='margin: 0;'><strong>Date :</strong> $jourDepartSafe à $heureDepartSafe</p>
                        </div>

                        <p>Vous pouvez maintenant échanger avec le chauffeur depuis votre messagerie EasyTravel.</p>

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

$chauffeurId = (int)($_SESSION['user_id'] ?? $_SESSION['Id_compte'] ?? 0);

if ($chauffeurId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

$reservationId = isset($_POST['reservation_id']) ? (int) $_POST['reservation_id'] : 0;

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
            r.user_id AS client_id,
            r.idVoyage AS voyage_id,
            r.statut_demande,
            r.Etat,
            r.nom,
            r.prenom,
            r.email,

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

    /*
        1. On accepte la demande.
    */
    $updateReservation = $pdo->prepare("
        UPDATE reservation
        SET statut_demande = 'acceptee',
            Etat = 1
        WHERE id_reservation = :reservation_id
    ");

    $updateReservation->execute([
        ':reservation_id' => $reservationId
    ]);

    /*
        2. On diminue le nombre de places disponibles.
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
        3. On vérifie si une conversation existe déjà.
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
    } else {
        /*
            4. On crée la conversation entre le client et le chauffeur.
        */
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
        5. Après validation en base, on envoie l'email au client.
        Même si l'email échoue, la réservation reste acceptée.
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
            $reservation['heureDepart'] ?? ''
        );
    }

    echo json_encode([
        'success' => true,
        'message' => $mailEnvoye
            ? 'Demande acceptée. La conversation a été créée et un email a été envoyé au client.'
            : 'Demande acceptée. La conversation a été créée, mais l’email n’a pas pu être envoyé.',
        'conversation_id' => $conversationId,
        'mail_sent' => $mailEnvoye
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