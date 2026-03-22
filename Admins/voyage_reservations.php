<?php
session_start();
require_once __DIR__ . '/../includes/admin_db.php';

if (!isset($_GET['idVoyage']) || (int)$_GET['idVoyage'] <= 0) {
    die("Voyage introuvable.");
}

$idVoyage = (int) $_GET['idVoyage'];

/* =========================
   SUPPRESSION RÉSERVATION
========================= */
if (isset($_POST['delete_reservation']) && !empty($_POST['reservation_id'])) {
    $reservationId = (int) $_POST['reservation_id'];

    $deleteStmt = $bdd->prepare("DELETE FROM reservation WHERE id_reservation = :id");
    $deleteStmt->bindValue(':id', $reservationId, PDO::PARAM_INT);

    if ($deleteStmt->execute()) {
        $_SESSION['message'] = "Réservation supprimée avec succès.";
    } else {
        $_SESSION['message'] = "Erreur lors de la suppression.";
    }

    header("Location: voyage_reservations.php?idVoyage=" . $idVoyage);
    exit();
}

/* =========================
   INFOS VOYAGE
========================= */
$voyageStmt = $bdd->prepare("
    SELECT *
    FROM voyage
    WHERE idVoyage = :idVoyage
    LIMIT 1
");
$voyageStmt->bindValue(':idVoyage', $idVoyage, PDO::PARAM_INT);
$voyageStmt->execute();
$voyage = $voyageStmt->fetch(PDO::FETCH_ASSOC);

if (!$voyage) {
    die("Voyage introuvable.");
}

/* =========================
   FILTRES
========================= */
$conditions = ["r.idVoyage = :idVoyage"];
$params = [':idVoyage' => $idVoyage];

if (!empty($_GET['search'])) {
    $conditions[] = "(r.nom LIKE :search OR r.prenom LIKE :search OR r.telephone LIKE :search OR r.Numero_reservation LIKE :search)";
    $params[':search'] = '%' . trim($_GET['search']) . '%';
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

/* =========================
   PAGINATION
========================= */
$limit = 10;
$page = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

/* =========================
   TOTAL RÉSERVATIONS
========================= */
$countStmt = $bdd->prepare("
    SELECT COUNT(*)
    FROM reservation r
    $whereClause
");
foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}
$countStmt->execute();
$totalReservations = (int) $countStmt->fetchColumn();
$totalPages = max(1, ceil($totalReservations / $limit));

/* =========================
   LISTE DES PASSAGERS
========================= */
$listStmt = $bdd->prepare("
    SELECT r.*
    FROM reservation r
    $whereClause
    ORDER BY r.id_reservation DESC
    LIMIT :start, :limit
");

foreach ($params as $key => $value) {
    $listStmt->bindValue($key, $value);
}
$listStmt->bindValue(':start', $start, PDO::PARAM_INT);
$listStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$listStmt->execute();
$reservations = $listStmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   STATS
========================= */
$capacite = (int) ($voyage['nombrePlaces'] ?? 0);
$placesRestantes = max(0, $capacite - $totalReservations);

ob_start();
?>

<div class="max-w-[98%] mx-auto">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Passagers du voyage #<?= htmlspecialchars($voyage['idVoyage']) ?></h1>
            <p class="text-gray-600 mt-1">
                <?= htmlspecialchars($voyage['villeDepart']) ?> → <?= htmlspecialchars($voyage['villeArrivee']) ?>
                | <?= htmlspecialchars($voyage['jourDepart']) ?>
                | <?= htmlspecialchars($voyage['heureDepart']) ?>
            </p>
        </div>

        <div class="flex gap-3">
            <a href="listevoyage.php" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Retour aux voyages
            </a>
            <a href="reservations.php?idVoyage=<?= (int)$voyage['idVoyage'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Vue globale filtrée
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-600">
            <p class="text-sm text-gray-500">ID Voyage</p>
            <p class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($voyage['idVoyage']) ?></p>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-600">
            <p class="text-sm text-gray-500">Réservations</p>
            <p class="text-2xl font-bold text-gray-800"><?= $totalReservations ?></p>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500">Capacité bus</p>
            <p class="text-2xl font-bold text-gray-800"><?= $capacite ?></p>
        </div>

        <div class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-600">
            <p class="text-sm text-gray-500">Places restantes</p>
            <p class="text-2xl font-bold text-gray-800"><?= $placesRestantes ?></p>
        </div>
    </div>

    <form method="GET" class="bg-white p-4 rounded-xl shadow mb-6 flex flex-col md:flex-row gap-4 md:items-center md:justify-between">
        <input type="hidden" name="idVoyage" value="<?= (int)$idVoyage ?>">

        <div class="flex-1">
            <input
                type="text"
                name="search"
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                placeholder="Rechercher par nom, prénom, téléphone ou n° réservation"
                class="w-full border rounded-lg px-4 py-3"
            >
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-green-600 text-white px-5 py-3 rounded-lg hover:bg-green-700">
                Rechercher
            </button>
            <a href="voyage_reservations.php?idVoyage=<?= (int)$idVoyage ?>" class="bg-gray-500 text-white px-5 py-3 rounded-lg hover:bg-gray-600">
                Réinitialiser
            </a>
        </div>
    </form>

    <div class="overflow-x-auto bg-white shadow rounded-xl">
        <table class="min-w-full">
            <thead>
                <tr class="bg-gray-800 text-white">
                    <th class="py-3 px-4 text-center">ID</th>
                    <th class="py-3 px-4 text-center">Nom</th>
                    <th class="py-3 px-4 text-center">Prénom</th>
                    <th class="py-3 px-4 text-center">Téléphone</th>
                    <th class="py-3 px-4 text-center">Email</th>
                    <th class="py-3 px-4 text-center">Siège</th>
                    <th class="py-3 px-4 text-center">N° réservation</th>
                    <th class="py-3 px-4 text-center">Prix</th>
                    <th class="py-3 px-4 text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($reservations)): ?>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['id_reservation']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['nom']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['prenom']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['telephone']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['email']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['Numero_siege']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['Numero_reservation']) ?></td>
                            <td class="py-3 px-4 text-center"><?= htmlspecialchars($reservation['prix_reservation']) ?> FCFA</td>
                            <td class="py-3 px-4 text-center">
                                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette réservation ?');">
                                    <input type="hidden" name="reservation_id" value="<?= (int)$reservation['id_reservation'] ?>">
                                    <button type="submit" name="delete_reservation" class="bg-red-500 text-white px-3 py-2 rounded hover:bg-red-700">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center py-6 text-gray-500">Aucune réservation trouvée pour ce voyage.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="flex justify-center mt-6 space-x-2 flex-wrap">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
               class="px-3 py-1 rounded border <?= $i == $page ? 'bg-gray-900 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<?php
$adminContent = ob_get_clean();
$adminTitle = 'Passagers du voyage';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Gestion des passagers par voyage';
$baseUrl = './';

include __DIR__ . '/../includes/layoutadmin.php';