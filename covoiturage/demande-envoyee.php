<?php
session_start();

require_once __DIR__ . '/../config.php';

$userId = (int)($_SESSION['user_id'] ?? $_SESSION['Id_compte'] ?? 0);

if ($userId <= 0) {
    header('Location: ../connexion.php');
    exit;
}

$idReservation = isset($_GET['id_reservation']) ? (int) $_GET['id_reservation'] : 0;

if ($idReservation <= 0) {
    die('Demande de réservation invalide.');
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
            r.statut_paiement,
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

$statut = $reservation['statut_demande'] ?? 'en_attente';

$statutLabel = 'En attente';
$statutClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
$messageStatut = "Votre demande a bien été envoyée au chauffeur. Vous devez attendre sa réponse.";

if ($statut === 'acceptee') {
    $statutLabel = 'Acceptée';
    $statutClass = 'bg-green-100 text-green-700 border-green-200';
    $messageStatut = "Bonne nouvelle ! Le chauffeur a accepté votre demande. Vous pouvez maintenant discuter avec lui.";
} elseif ($statut === 'refusee') {
    $statutLabel = 'Refusée';
    $statutClass = 'bg-red-100 text-red-700 border-red-200';
    $messageStatut = "Le chauffeur a refusé votre demande. Vous pouvez chercher un autre trajet.";
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
                    <?= htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']) ?>
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
                        <?= number_format((float)$reservation['prix_reservation'], 0, ',', ' ') ?> FCFA
                    </p>
                </div>
            </div>
        </div>

        <?php if ($statut === 'en_attente'): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-xl mb-6">
                Votre demande est en attente. Le chauffeur doit encore accepter ou refuser.
            </div>
        <?php endif; ?>

        <?php if ($statut === 'acceptee' && !empty($reservation['conversation_id'])): ?>
            <a href="../messagerie/chat.php?conversation_id=<?= (int)$reservation['conversation_id'] ?>"
               class="block w-full text-center bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition">
                Discuter avec le chauffeur
            </a>
        <?php elseif ($statut === 'refusee'): ?>
            <a href="../listevoyageretour.php?transport=covoiturage"
               class="block w-full text-center bg-gray-800 hover:bg-gray-900 text-white font-bold py-4 rounded-xl transition">
                Chercher un autre covoiturage
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