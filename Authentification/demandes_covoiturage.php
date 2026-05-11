<?php
session_start();

require_once __DIR__ . '/../config.php';

$chauffeurId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($chauffeurId <= 0) {
    header('Location: ../connexion.php');
    exit;
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

            v.idVoyage,
            v.villeDepart,
            v.quartierDepart,
            v.villeArrivee,
            v.quartierArrivee,
            v.jourDepart,
            v.heureDepart,
            v.heureArrivee,

            c.id AS conversation_id

        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        LEFT JOIN conversations c ON c.reservation_id = r.id_reservation
        WHERE v.chauffeur_id = :chauffeur_id
        AND v.modeTransport = 'covoiturage'
        AND r.type_reservation = 'covoiturage'
        ORDER BY r.id_reservation DESC
    ");

    $stmt->execute([
        ':chauffeur_id' => $chauffeurId
    ]);

    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

ob_start();
?>

<div class="max-w-6xl mx-auto px-4 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-slate-800">
            Demandes de covoiturage
        </h1>

        <p class="text-gray-500 mt-2">
            Acceptez ou refusez les demandes des clients pour vos trajets.
        </p>
    </div>

    <?php if (empty($demandes)): ?>

        <div class="bg-white rounded-2xl shadow p-8 text-center">
            <p class="text-gray-500">
                Aucune demande de covoiturage pour le moment.
            </p>
        </div>

    <?php else: ?>

        <div class="space-y-5">
            <?php foreach ($demandes as $demande): ?>
                <?php
                $statut = $demande['statut_demande'] ?? 'en_attente';
                $statutPaiement = $demande['statut_paiement'] ?? 'en_attente';

                $badgeClass = 'bg-yellow-100 text-yellow-700';
                $badgeText = 'En attente';

                if ($statut === 'acceptee') {
                    $badgeClass = 'bg-green-100 text-green-700';
                    $badgeText = 'Acceptée';
                } elseif ($statut === 'refusee') {
                    $badgeClass = 'bg-red-100 text-red-700';
                    $badgeText = 'Refusée';
                } elseif ($statut === 'expiree') {
                    $badgeClass = 'bg-gray-100 text-gray-700';
                    $badgeText = 'Expirée';
                } elseif ($statut === 'annulee_client') {
                    $badgeClass = 'bg-gray-100 text-gray-700';
                    $badgeText = 'Annulée par le client';
                } elseif ($statut === 'annulee_chauffeur') {
                    $badgeClass = 'bg-red-100 text-red-700';
                    $badgeText = 'Annulée par le chauffeur';
                } elseif ($statut === 'terminee') {
                    $badgeClass = 'bg-blue-100 text-blue-700';
                    $badgeText = 'Terminée';
                } elseif ($statut === 'litige') {
                    $badgeClass = 'bg-red-100 text-red-700';
                    $badgeText = 'Litige';
                }

                $paymentClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                $paymentText = 'Paiement en attente';

                if ($statutPaiement === 'payee') {
                    $paymentClass = 'bg-green-50 text-green-700 border-green-200';
                    $paymentText = 'Payé';
                } elseif ($statutPaiement === 'offerte') {
                    $paymentClass = 'bg-green-50 text-green-700 border-green-200';
                    $paymentText = 'Offert';
                } elseif ($statutPaiement === 'paiement_en_cours') {
                    $paymentClass = 'bg-yellow-50 text-yellow-700 border-yellow-200';
                    $paymentText = 'Paiement en cours';
                } elseif ($statutPaiement === 'paiement_echoue') {
                    $paymentClass = 'bg-red-50 text-red-700 border-red-200';
                    $paymentText = 'Paiement échoué';
                } elseif ($statutPaiement === 'annule') {
                    $paymentClass = 'bg-gray-50 text-gray-700 border-gray-200';
                    $paymentText = 'Paiement annulé';
                } elseif ($statutPaiement === 'remboursee') {
                    $paymentClass = 'bg-blue-50 text-blue-700 border-blue-200';
                    $paymentText = 'Remboursé';
                }

                $conversationId = (int)($demande['conversation_id'] ?? 0);

                $peutVoirChat = (
                    $statut === 'acceptee'
                    && in_array($statutPaiement, ['payee', 'offerte'], true)
                    && $conversationId > 0
                );

                $deadlineTexte = null;

                if (!empty($demande['payment_deadline_at']) && $statut === 'acceptee' && $statutPaiement !== 'payee') {
                    $deadlineTexte = date('d/m/Y à H:i', strtotime($demande['payment_deadline_at']));
                }
                ?>

                <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">

                        <div class="flex-1">
                            <div class="flex flex-wrap items-center gap-3 mb-3">
                                <h2 class="text-xl font-bold text-slate-800">
                                    <?= htmlspecialchars(trim($demande['prenom'] . ' ' . $demande['nom'])) ?>
                                </h2>

                                <span class="px-3 py-1 rounded-full text-sm font-bold <?= $badgeClass ?>">
                                    <?= htmlspecialchars($badgeText) ?>
                                </span>

                                <span class="px-3 py-1 rounded-full text-sm font-bold border <?= $paymentClass ?>">
                                    <?= htmlspecialchars($paymentText) ?>
                                </span>
                            </div>

                            <p class="text-gray-600 mb-1">
                                <strong>Trajet :</strong>
                                <?= htmlspecialchars($demande['villeDepart']) ?>
                                →
                                <?= htmlspecialchars($demande['villeArrivee']) ?>
                            </p>

                            <p class="text-gray-600 mb-1">
                                <strong>Date :</strong>
                                <?= htmlspecialchars($demande['jourDepart']) ?>
                                à
                                <?= htmlspecialchars(substr($demande['heureDepart'], 0, 5)) ?>
                            </p>

                            <?php if (!empty($demande['quartierDepart']) || !empty($demande['quartierArrivee'])): ?>
                                <p class="text-gray-600 mb-1">
                                    <strong>Quartiers :</strong>
                                    <?= htmlspecialchars($demande['quartierDepart'] ?? '') ?>
                                    →
                                    <?= htmlspecialchars($demande['quartierArrivee'] ?? '') ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-gray-600 mb-1">
                                <strong>Téléphone :</strong>
                                <?= htmlspecialchars($demande['telephone']) ?>
                            </p>

                            <p class="text-gray-600 mb-1">
                                <strong>Email :</strong>
                                <?= htmlspecialchars($demande['email']) ?>
                            </p>

                            <p class="text-gray-600 mb-1">
                                <strong>Numéro demande :</strong>
                                <?= htmlspecialchars($demande['Numero_reservation']) ?>
                            </p>

                            <?php if ($deadlineTexte): ?>
                                <p class="text-yellow-700 font-semibold mb-1">
                                    <strong>Délai de paiement :</strong>
                                    <?= htmlspecialchars($deadlineTexte) ?>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($demande['paid_at']) && $statutPaiement === 'payee'): ?>
                                <p class="text-green-700 font-semibold mb-1">
                                    <strong>Payé le :</strong>
                                    <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($demande['paid_at']))) ?>
                                </p>
                            <?php endif; ?>

                            <p class="text-green-600 font-bold mt-2">
                                <?= number_format((float)$demande['prix_reservation'], 0, ',', ' ') ?> FCFA
                            </p>
                        </div>

                        <div class="md:w-56">
                            <?php if ($statut === 'en_attente'): ?>

                                <div class="flex flex-col gap-3">
                                    <button
                                        class="accept-btn bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition"
                                        data-id="<?= (int)$demande['id_reservation'] ?>">
                                        Accepter
                                    </button>

                                    <button
                                        class="refuse-btn bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded-xl transition"
                                        data-id="<?= (int)$demande['id_reservation'] ?>">
                                        Refuser
                                    </button>
                                </div>

                            <?php elseif ($peutVoirChat): ?>

                                <a href="../messagerie/chat.php?conversation_id=<?= (int)$conversationId ?>"
                                   class="block text-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl transition">
                                    Ouvrir le chat
                                </a>

                            <?php elseif ($statut === 'acceptee' && $statutPaiement !== 'payee' && $statutPaiement !== 'offerte'): ?>

                                <div class="text-center bg-yellow-50 border border-yellow-200 text-yellow-700 font-semibold p-4 rounded-xl">
                                    En attente du paiement client
                                </div>

                            <?php else: ?>

                                <div class="text-center text-gray-400 font-semibold p-4">
                                    Déjà traité
                                </div>

                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>
</div>

<script>
document.querySelectorAll('.accept-btn').forEach(button => {
    button.addEventListener('click', async function () {
        if (!confirm("Voulez-vous accepter cette demande ?")) {
            return;
        }

        const reservationId = this.dataset.id;
        const formData = new FormData();
        formData.append('reservation_id', reservationId);

        this.disabled = true;
        this.textContent = 'Traitement...';
        this.classList.add('opacity-60', 'cursor-not-allowed');

        try {
            const response = await fetch('../messagerie/api/accept-reservation.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();

            let result;

            try {
                result = JSON.parse(text);
            } catch (error) {
                alert("Réponse serveur invalide : " + text);
                window.location.reload();
                return;
            }

            alert(result.message || 'Traitement terminé.');

            if (result.success) {
                /*
                    Nouveau flux :
                    après acceptation, le client doit payer.
                    Donc on ne redirige plus directement le chauffeur vers le chat.
                */
                window.location.reload();
            } else {
                window.location.reload();
            }

        } catch (error) {
            alert("Erreur réseau : " + error.message);
            window.location.reload();
        }
    });
});

document.querySelectorAll('.refuse-btn').forEach(button => {
    button.addEventListener('click', async function () {
        if (!confirm("Voulez-vous refuser cette demande ?")) {
            return;
        }

        const reservationId = this.dataset.id;
        const formData = new FormData();
        formData.append('reservation_id', reservationId);

        this.disabled = true;
        this.textContent = 'Traitement...';
        this.classList.add('opacity-60', 'cursor-not-allowed');

        try {
            const response = await fetch('../messagerie/api/refuse-reservation.php', {
                method: 'POST',
                body: formData
            });

            const text = await response.text();

            let result;

            try {
                result = JSON.parse(text);
            } catch (error) {
                alert("Réponse serveur invalide : " + text);
                window.location.reload();
                return;
            }

            alert(result.message || 'Traitement terminé.');
            window.location.reload();

        } catch (error) {
            alert("Erreur réseau : " + error.message);
            window.location.reload();
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$title = "Demandes de covoiturage";
include __DIR__ . '/../layouts/default.php';
?>