<?php
session_start();

require_once __DIR__ . '/../config.php';

$chauffeurId = (int)($_SESSION['user_id'] ?? $_SESSION['Id_compte'] ?? 0);

if ($chauffeurId <= 0) {
    header('Location: ../connexion.php');
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            r.id_reservation,
            r.Numero_reservation,
            r.nom,
            r.prenom,
            r.telephone,
            r.email,
            r.prix_reservation,
            r.statut_demande,

            v.idVoyage,
            v.villeDepart,
            v.quartierDepart,
            v.villeArrivee,
            v.quartierArrivee,
            v.jourDepart,
            v.heureDepart,
            v.heureArrivee

        FROM reservation r
        INNER JOIN voyage v ON v.idVoyage = r.idVoyage
        WHERE v.chauffeur_id = :chauffeur_id
        AND v.modeTransport = 'covoiturage'
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
                $statut = $demande['statut_demande'];

                $badgeClass = 'bg-yellow-100 text-yellow-700';
                $badgeText = 'En attente';

                if ($statut === 'acceptee') {
                    $badgeClass = 'bg-green-100 text-green-700';
                    $badgeText = 'Acceptée';
                } elseif ($statut === 'refusee') {
                    $badgeClass = 'bg-red-100 text-red-700';
                    $badgeText = 'Refusée';
                }
                ?>

                <div class="bg-white rounded-2xl shadow p-6 border border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5">

                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h2 class="text-xl font-bold text-slate-800">
                                    <?= htmlspecialchars($demande['prenom'] . ' ' . $demande['nom']) ?>
                                </h2>

                                <span class="px-3 py-1 rounded-full text-sm font-bold <?= $badgeClass ?>">
                                    <?= htmlspecialchars($badgeText) ?>
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

                            <p class="text-gray-600 mb-1">
                                <strong>Téléphone :</strong>
                                <?= htmlspecialchars($demande['telephone']) ?>
                            </p>

                            <p class="text-gray-600 mb-1">
                                <strong>Email :</strong>
                                <?= htmlspecialchars($demande['email']) ?>
                            </p>

                            <p class="text-green-600 font-bold mt-2">
                                <?= number_format((float)$demande['prix_reservation'], 0, ',', ' ') ?> FCFA
                            </p>
                        </div>

                        <?php if ($statut === 'en_attente'): ?>
                            <div class="flex flex-col gap-3 md:w-48">
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
                        <?php else: ?>
                            <div class="md:w-48 text-center text-gray-400 font-semibold">
                                Déjà traité
                            </div>
                        <?php endif; ?>

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

        const response = await fetch('../messagerie/api/accept-reservation.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);

            if (result.conversation_id) {
                window.location.href = '../messagerie/chat.php?conversation_id=' + result.conversation_id;
            } else {
                window.location.reload();
            }
        } else {
            alert(result.message);
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

        const response = await fetch('../messagerie/api/refuse-reservation.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            window.location.reload();
        } else {
            alert(result.message);
        }
    });
});
</script>

<?php
$content = ob_get_clean();
$title = "Demandes de covoiturage";
include __DIR__ . '/../layouts/default.php';
?>