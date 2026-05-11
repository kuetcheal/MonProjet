<?php
session_start();

require_once __DIR__ . '/../config.php';

$userId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    header('Location: ../connexion.php');
    exit;
}

$idReservation = isset($_GET['id_reservation']) ? (int)$_GET['id_reservation'] : 0;

if ($idReservation <= 0) {
    die('Demande de réservation invalide.');
}

/*
    Si le chauffeur a accepté, mais que le client n’a pas payé avant la limite,
    on expire la demande et on remet la place disponible.
*/
try {
    $pdo->beginTransaction();

    $checkExpiration = $pdo->prepare("
        SELECT 
            r.id_reservation,
            r.idVoyage,
            r.statut_demande,
            r.statut_paiement,
            r.payment_deadline_at
        FROM reservation r
        WHERE r.id_reservation = :id_reservation
        AND r.user_id = :user_id
        AND r.type_reservation = 'covoiturage'
        LIMIT 1
        FOR UPDATE
    ");

    $checkExpiration->execute([
        ':id_reservation' => $idReservation,
        ':user_id' => $userId
    ]);

    $reservationExpiration = $checkExpiration->fetch(PDO::FETCH_ASSOC);

    if ($reservationExpiration) {
        $statutDemandeExpiration = $reservationExpiration['statut_demande'] ?? '';
        $statutPaiementExpiration = $reservationExpiration['statut_paiement'] ?? '';
        $deadline = $reservationExpiration['payment_deadline_at'] ?? null;

        $paiementExpirable = in_array($statutPaiementExpiration, [
            'en_attente',
            'paiement_echoue'
        ], true);

        if (
            $statutDemandeExpiration === 'acceptee'
            && $paiementExpirable
            && !empty($deadline)
            && strtotime($deadline) < time()
        ) {
            $expireReservation = $pdo->prepare("
                UPDATE reservation
                SET statut_demande = 'expiree',
                    statut_paiement = 'annule'
                WHERE id_reservation = :id_reservation
                AND statut_demande = 'acceptee'
                AND statut_paiement IN ('en_attente', 'paiement_echoue')
            ");

            $expireReservation->execute([
                ':id_reservation' => $idReservation
            ]);

            if ($expireReservation->rowCount() > 0) {
                $restorePlace = $pdo->prepare("
                    UPDATE voyage
                    SET nombre_places_disponibles = COALESCE(nombre_places_disponibles, 0) + 1
                    WHERE idVoyage = :idVoyage
                ");

                $restorePlace->execute([
                    ':idVoyage' => (int)$reservationExpiration['idVoyage']
                ]);
            }
        }
    }

    $pdo->commit();

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    die('Erreur lors de la vérification de la demande : ' . $e->getMessage());
}

try {
    $stmt = $pdo->prepare("
        SELECT
            r.id_reservation,
            r.type_reservation,
            r.Numero_reservation,
            r.nom,
            r.prenom,
            r.telephone,
            r.email,
            r.prix_reservation,
            r.statut_demande,
            r.statut_paiement,
            r.payment_deadline_at,
            r.paid_at,
            r.idVoyage,

            v.villeDepart,
            v.quartierDepart,
            v.villeArrivee,
            v.quartierArrivee,
            v.jourDepart,
            v.heureDepart,
            v.heureArrivee,
            v.chauffeur_id,

            c.id AS conversation_id
        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        LEFT JOIN conversations c ON c.reservation_id = r.id_reservation
        WHERE r.id_reservation = :id_reservation
        AND r.user_id = :user_id
        AND r.type_reservation = 'covoiturage'
        LIMIT 1
    ");

    $stmt->execute([
        ':id_reservation' => $idReservation,
        ':user_id' => $userId
    ]);

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reservation) {
        die('Demande introuvable ou accès refusé.');
    }

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

$statutDemande = $reservation['statut_demande'] ?? 'en_attente';
$statutPaiement = $reservation['statut_paiement'] ?? 'en_attente';

$statutLabel = 'En attente';
$statutClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
$messageStatut = "Votre demande a bien été envoyée au chauffeur. Vous devez attendre sa réponse.";

$paiementLabel = 'En attente';
$paiementClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
$messagePaiement = "Le paiement sera disponible après acceptation du chauffeur.";

if ($statutDemande === 'acceptee') {
    $statutLabel = 'Acceptée';
    $statutClass = 'bg-green-100 text-green-700 border-green-200';

    if ($statutPaiement === 'payee') {
        $messageStatut = "Bonne nouvelle ! Votre demande est acceptée et votre paiement est confirmé. Vous pouvez discuter avec le chauffeur.";
    } elseif ($statutPaiement === 'offerte') {
        $messageStatut = "Bonne nouvelle ! Votre demande est acceptée. Votre place est confirmée grâce à votre offre fidélité.";
    } elseif ($statutPaiement === 'paiement_en_cours') {
        $messageStatut = "Votre demande est acceptée. Votre paiement est en cours de vérification.";
    } elseif ($statutPaiement === 'paiement_echoue') {
        $messageStatut = "Votre demande est acceptée, mais le paiement a échoué. Vous pouvez réessayer.";
    } else {
        $messageStatut = "Bonne nouvelle ! Le chauffeur a accepté votre demande. Vous devez maintenant payer pour confirmer votre place.";
    }
} elseif ($statutDemande === 'refusee') {
    $statutLabel = 'Refusée';
    $statutClass = 'bg-red-100 text-red-700 border-red-200';
    $messageStatut = "Le chauffeur a refusé votre demande. Vous pouvez chercher un autre trajet.";
} elseif ($statutDemande === 'expiree') {
    $statutLabel = 'Expirée';
    $statutClass = 'bg-gray-100 text-gray-700 border-gray-200';
    $messageStatut = "Votre délai de paiement est dépassé. La demande a expiré.";
} elseif ($statutDemande === 'annulee_client') {
    $statutLabel = 'Annulée';
    $statutClass = 'bg-gray-100 text-gray-700 border-gray-200';
    $messageStatut = "Vous avez annulé cette demande.";
} elseif ($statutDemande === 'annulee_chauffeur') {
    $statutLabel = 'Annulée par le chauffeur';
    $statutClass = 'bg-red-100 text-red-700 border-red-200';
    $messageStatut = "Le chauffeur a annulé cette demande.";
} elseif ($statutDemande === 'terminee') {
    $statutLabel = 'Terminée';
    $statutClass = 'bg-blue-100 text-blue-700 border-blue-200';
    $messageStatut = "Ce covoiturage est terminé.";
} elseif ($statutDemande === 'litige') {
    $statutLabel = 'Litige';
    $statutClass = 'bg-red-100 text-red-700 border-red-200';
    $messageStatut = "Cette demande est en litige. Veuillez contacter le support.";
}

if ($statutPaiement === 'payee') {
    $paiementLabel = 'Payé';
    $paiementClass = 'bg-green-50 text-green-700 border-green-200';
    $messagePaiement = "Votre paiement a été confirmé.";
} elseif ($statutPaiement === 'offerte') {
    $paiementLabel = 'Offert';
    $paiementClass = 'bg-green-50 text-green-700 border-green-200';
    $messagePaiement = "Votre place est confirmée grâce à votre offre fidélité.";
} elseif ($statutPaiement === 'paiement_en_cours') {
    $paiementLabel = 'Paiement en cours';
    $paiementClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
    $messagePaiement = "Le paiement est en cours de vérification. Actualisez cette page dans quelques instants.";
} elseif ($statutPaiement === 'paiement_echoue') {
    $paiementLabel = 'Paiement échoué';
    $paiementClass = 'bg-red-50 text-red-700 border-red-200';
    $messagePaiement = "Le paiement a échoué. Vous pouvez réessayer.";
} elseif ($statutPaiement === 'annule') {
    $paiementLabel = 'Annulé';
    $paiementClass = 'bg-gray-50 text-gray-700 border-gray-200';
    $messagePaiement = "Le paiement a été annulé.";
} elseif ($statutPaiement === 'remboursee') {
    $paiementLabel = 'Remboursé';
    $paiementClass = 'bg-blue-50 text-blue-700 border-blue-200';
    $messagePaiement = "Le paiement a été remboursé.";
}

$prix = (float)($reservation['prix_reservation'] ?? 0);
$conversationId = (int)($reservation['conversation_id'] ?? 0);

$peutPayer = (
    $statutDemande === 'acceptee'
    && in_array($statutPaiement, ['en_attente', 'paiement_echoue'], true)
);

$paiementEnCours = (
    $statutDemande === 'acceptee'
    && $statutPaiement === 'paiement_en_cours'
);

$peutDiscuter = (
    $statutDemande === 'acceptee'
    && in_array($statutPaiement, ['payee', 'offerte'], true)
    && $conversationId > 0
);

$deadlineTexte = null;

if (!empty($reservation['payment_deadline_at']) && $peutPayer) {
    $deadlineTexte = date('d/m/Y à H:i', strtotime($reservation['payment_deadline_at']));
}

ob_start();
?>

<div class="max-w-4xl mx-auto px-4 py-10">

    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">

        <div class="mb-8">
            <p class="inline-flex items-center px-4 py-2 rounded-full border font-bold <?= $statutClass ?>">
                Statut : <?= htmlspecialchars($statutLabel) ?>
            </p>

            <h1 class="text-3xl font-extrabold text-slate-800 mt-4">
                Demande de covoiturage
            </h1>

            <p class="text-gray-500 mt-2">
                <?= htmlspecialchars($messageStatut) ?>
            </p>
        </div>

        <div class="border rounded-2xl p-5 mb-6 bg-gray-50">
            <h2 class="text-xl font-bold text-slate-800 mb-4">
                Informations de la demande
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <p>
                    <strong>Numéro :</strong>
                    <?= htmlspecialchars($reservation['Numero_reservation']) ?>
                </p>

                <p>
                    <strong>Passager :</strong>
                    <?= htmlspecialchars(trim($reservation['prenom'] . ' ' . $reservation['nom'])) ?>
                </p>

                <p>
                    <strong>Téléphone :</strong>
                    <?= htmlspecialchars($reservation['telephone']) ?>
                </p>

                <p>
                    <strong>Email :</strong>
                    <?= htmlspecialchars($reservation['email']) ?>
                </p>
            </div>
        </div>

        <div class="border rounded-2xl p-5 mb-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">
                Trajet demandé
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <p class="text-gray-500 text-sm">Départ</p>

                    <p class="font-bold text-slate-800">
                        <?= htmlspecialchars($reservation['villeDepart']) ?>
                    </p>

                    <p class="text-gray-500">
                        <?= htmlspecialchars($reservation['quartierDepart'] ?? '') ?>
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 text-sm">Arrivée</p>

                    <p class="font-bold text-slate-800">
                        <?= htmlspecialchars($reservation['villeArrivee']) ?>
                    </p>

                    <p class="text-gray-500">
                        <?= htmlspecialchars($reservation['quartierArrivee'] ?? '') ?>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mt-6">
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-gray-500 text-sm">Date</p>

                    <p class="font-bold">
                        <?= htmlspecialchars($reservation['jourDepart']) ?>
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-gray-500 text-sm">Heure</p>

                    <p class="font-bold">
                        <?= htmlspecialchars(substr($reservation['heureDepart'], 0, 5)) ?>
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-gray-500 text-sm">Prix</p>

                    <p class="font-bold text-green-600">
                        <?= number_format($prix, 0, ',', ' ') ?> FCFA
                    </p>
                </div>
            </div>
        </div>

        <div class="border rounded-2xl p-5 mb-6 <?= $paiementClass ?>">
            <h2 class="text-xl font-bold mb-2">
                Paiement : <?= htmlspecialchars($paiementLabel) ?>
            </h2>

            <p>
                <?= htmlspecialchars($messagePaiement) ?>
            </p>

            <?php if ($deadlineTexte): ?>
                <p class="mt-2 font-semibold">
                    Délai de paiement : <?= htmlspecialchars($deadlineTexte) ?>
                </p>
            <?php endif; ?>

            <?php if (!empty($reservation['paid_at']) && $statutPaiement === 'payee'): ?>
                <p class="mt-2 text-sm">
                    Payé le <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($reservation['paid_at']))) ?>
                </p>
            <?php endif; ?>
        </div>

        <?php if ($statutDemande === 'en_attente'): ?>

            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-xl mb-6">
                Votre demande est en attente. Le chauffeur doit encore accepter ou refuser.
            </div>

            <a href="../listevoyageretour.php?transport=covoiturage"
               class="block w-full text-center bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition">
                Retour aux trajets
            </a>

        <?php elseif ($peutPayer): ?>

            <a href="../paiement/payer-covoiturage.php?id_reservation=<?= (int)$reservation['id_reservation'] ?>"
               class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition">
                Payer et confirmer ma place
            </a>

            <a href="../listevoyageretour.php?transport=covoiturage"
               class="block w-full text-center bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-4 rounded-xl transition mt-3">
                Retour aux trajets
            </a>

        <?php elseif ($paiementEnCours): ?>

            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-xl mb-6">
                Votre paiement est en cours de vérification par pawaPay. Cette opération peut prendre quelques instants.
            </div>

            <a href="demande-envoyee.php?id_reservation=<?= (int)$reservation['id_reservation'] ?>"
               class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition">
                Actualiser le statut
            </a>

        <?php elseif ($peutDiscuter): ?>

            <a href="../messagerie/chat.php?conversation_id=<?= (int)$conversationId ?>"
               class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition">
                Discuter avec le chauffeur
            </a>

        <?php elseif ($statutDemande === 'refusee'): ?>

            <a href="../listevoyageretour.php?transport=covoiturage"
               class="block w-full text-center bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition">
                Chercher un autre covoiturage
            </a>

        <?php elseif ($statutDemande === 'expiree'): ?>

            <a href="../listevoyageretour.php?transport=covoiturage"
               class="block w-full text-center bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition">
                Faire une nouvelle demande
            </a>

        <?php else: ?>

            <a href="../listevoyageretour.php?transport=covoiturage"
               class="block w-full text-center bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition">
                Retour aux trajets
            </a>

        <?php endif; ?>

    </div>

</div>

<?php
$content = ob_get_clean();
$title = "Demande de covoiturage";
include __DIR__ . '/../layouts/default.php';
?>