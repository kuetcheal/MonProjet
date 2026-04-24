<?php
session_start();

require_once __DIR__ . '/../config.php';

/* =========================
   SUPPRESSION
========================= */
if (isset($_POST['delete_voyage']) && !empty($_POST['id_voyage'])) {
    $id = (int) $_POST['id_voyage'];

    $stmt = $pdo->prepare("DELETE FROM voyage WHERE idVoyage = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: listevoyage.php?success=delete');
        exit;
    }

    header('Location: listevoyage.php?error=delete');
    exit;
}

/* =========================
   FILTRES
========================= */
$villeDepartList = $pdo->query("SELECT DISTINCT villeDepart FROM voyage ORDER BY villeDepart ASC")->fetchAll(PDO::FETCH_COLUMN);
$villeArriveeList = $pdo->query("SELECT DISTINCT villeArrivee FROM voyage ORDER BY villeArrivee ASC")->fetchAll(PDO::FETCH_COLUMN);

$conditions = [];
$params = [];

if (!empty($_GET['villeDepart'])) {
    $conditions[] = "v.villeDepart = :villeDepart";
    $params[':villeDepart'] = $_GET['villeDepart'];
}

if (!empty($_GET['villeArrivee'])) {
    $conditions[] = "v.villeArrivee = :villeArrivee";
    $params[':villeArrivee'] = $_GET['villeArrivee'];
}

if (!empty($_GET['jourDepart'])) {
    $conditions[] = "v.jourDepart = :jourDepart";
    $params[':jourDepart'] = $_GET['jourDepart'];
}

$whereClause = !empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';

/* =========================
   PAGINATION
========================= */
$limit = 10;
$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

/* =========================
   TOTAL
========================= */
$countQuery = "SELECT COUNT(*) FROM voyage v" . $whereClause;
$countStmt = $pdo->prepare($countQuery);

foreach ($params as $key => $value) {
    $countStmt->bindValue($key, $value);
}

$countStmt->execute();

$total_voyages = (int) $countStmt->fetchColumn();
$total_pages = max(1, (int) ceil($total_voyages / $limit));

if ($page > $total_pages) {
    $page = $total_pages;
    $start = ($page - 1) * $limit;
}

/* =========================
   LISTE DES VOYAGES
========================= */
$query = "
    SELECT 
        v.*,
        COUNT(r.id_reservation) AS total_reservations
    FROM voyage v
    LEFT JOIN reservation r ON r.idVoyage = v.idVoyage
    $whereClause
    GROUP BY v.idVoyage
    ORDER BY v.jourDepart DESC, v.heureDepart DESC
    LIMIT :start, :limit
";

$resultat = $pdo->prepare($query);

foreach ($params as $key => $value) {
    $resultat->bindValue($key, $value);
}

$resultat->bindValue(':start', $start, PDO::PARAM_INT);
$resultat->bindValue(':limit', $limit, PDO::PARAM_INT);
$resultat->execute();

$voyages = $resultat->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CONTENU DE LA PAGE
========================= */
ob_start();
?>

<h4 class="text-center text-xl font-bold mb-4">Liste des trajets disponibles</h4>

<?php if (isset($_GET['success']) && $_GET['success'] === 'delete'): ?>
    <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
        Voyage supprimé avec succès.
    </div>
<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'delete'): ?>
    <div class="mb-4 bg-red-500 text-white text-center p-3 rounded-lg shadow">
        Erreur lors de la suppression du voyage.
    </div>
<?php endif; ?>

<form method="GET" action="" id="filterForm" class="p-4 w-full mb-6">
    <div class="flex flex-wrap items-end gap-6">
        <!-- Bouton Ajouter -->
        <a href="insertionvoyage.php" class="bg-blue-500 text-white h-12 px-6 flex items-center justify-center rounded-lg transition duration-300 hover:bg-blue-600 hover:scale-105">
            <i class="fa fa-plus-circle mr-2"></i> Ajouter un voyage
        </a>

        <!-- Ville départ -->
        <div class="flex flex-col">
            <label class="block text-gray-700 font-bold mb-1">Ville Départ</label>
            <select
                name="villeDepart"
                onchange="submitFilterForm(this)"
                class="border p-2 rounded h-12 w-48 transition duration-300 ease-in-out hover:shadow-md focus:ring-2 focus:ring-blue-400 focus:outline-none"
            >
                <option value="">Toutes</option>
                <?php foreach ($villeDepartList as $ville): ?>
                    <option value="<?= htmlspecialchars($ville) ?>" <?= (isset($_GET['villeDepart']) && $_GET['villeDepart'] === $ville) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ville) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Ville arrivée -->
        <div class="flex flex-col">
            <label class="block text-gray-700 font-bold mb-1">Ville Arrivée</label>
            <select
                name="villeArrivee"
                onchange="submitFilterForm(this)"
                class="border p-2 rounded h-12 w-48 transition duration-300 ease-in-out hover:shadow-md focus:ring-2 focus:ring-blue-400 focus:outline-none"
            >
                <option value="">Toutes</option>
                <?php foreach ($villeArriveeList as $ville): ?>
                    <option value="<?= htmlspecialchars($ville) ?>" <?= (isset($_GET['villeArrivee']) && $_GET['villeArrivee'] === $ville) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ville) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Date -->
        <div class="flex flex-col">
            <label class="block text-gray-700 font-bold mb-1">Date</label>
            <input
                type="date"
                name="jourDepart"
                value="<?= htmlspecialchars($_GET['jourDepart'] ?? '') ?>"
                onchange="submitFilterForm(this)"
                class="border p-2 rounded h-12 w-48 transition duration-300 ease-in-out hover:shadow-md focus:ring-2 focus:ring-blue-400 focus:outline-none"
            >
        </div>

        <!-- Bouton reset -->
        <div class="flex gap-3">
            <a href="listevoyage.php" class="bg-red-500 text-white h-12 px-6 flex items-center justify-center rounded-lg transition duration-300 hover:bg-red-600 hover:scale-105">
                Réinitialiser
            </a>
        </div>
    </div>
</form>

<div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-800 text-white">
                <th class="p-2">N° Voyage</th>
                <th class="p-2">Ville Départ</th>
                <th class="p-2">Quartier Départ</th>
                <th class="p-2">Ville Arrivée</th>
                <th class="p-2">Quartier Arrivée</th>
                <th class="p-2">Heure Départ</th>
                <th class="p-2">Heure Arrivée</th>
                <th class="p-2">Type de Bus</th>
                <th class="p-2">Prix</th>
                <th class="p-2">Date</th>
                <th class="p-2 text-center">Réservations</th>
                <th class="p-2 text-center">Passagers</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($voyages)): ?>
                <?php foreach ($voyages as $donne): ?>
                    <tr class="border-b">
                        <td class="p-2"><?= htmlspecialchars($donne['idVoyage']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['villeDepart']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['quartierDepart'] ?? '') ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['villeArrivee']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['quartierArrivee'] ?? '') ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['heureDepart']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['heureArrivee']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['typeBus']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['prix']) ?></td>
                        <td class="p-2"><?= htmlspecialchars($donne['jourDepart']) ?></td>

                        <td class="p-2 text-center">
                            <span class="inline-flex items-center justify-center min-w-[40px] px-3 py-1 rounded-full bg-blue-100 text-blue-700 font-bold">
                                <?= (int)$donne['total_reservations'] ?>
                            </span>
                        </td>

                        <td class="p-2 text-center">
                            <a href="voyage_reservations.php?idVoyage=<?= (int)$donne['idVoyage'] ?>"
                               class="bg-indigo-600 text-white px-3 py-2 rounded hover:bg-indigo-700 inline-block">
                                Voir passagers
                            </a>
                        </td>

                        <td class="p-2">
                            <div class="flex space-x-2">
                                <form method="post" action="modifier.php">
                                    <input type="hidden" name="id_voyage" value="<?= htmlspecialchars($donne['idVoyage']) ?>">
                                    <button type="submit" class="bg-green-500 text-white p-2 rounded hover:bg-green-600">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </form>

                                <button type="button" onclick="openModal(<?= (int)$donne['idVoyage'] ?>)" class="bg-red-500 text-white p-2 rounded hover:bg-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="13" class="text-center p-4 text-gray-500">Aucun voyage trouvé.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="flex justify-center mt-6 space-x-2 flex-wrap">
    <?php $queryParams = $_GET; ?>

    <?php if ($page > 1): ?>
        <?php $queryParams['page'] = $page - 1; ?>
        <a href="?<?= http_build_query($queryParams) ?>" class="px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300">
            <i class="fas fa-chevron-left"></i>
        </a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php $queryParams['page'] = $i; ?>

        <?php if ($i == $page): ?>
            <span class="px-3 py-1 rounded-full bg-gray-900 text-white"><?= $i ?></span>
        <?php elseif ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
            <a href="?<?= http_build_query($queryParams) ?>" class="px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300">
                <?= $i ?>
            </a>
        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
            <span class="px-3 py-1 text-gray-500">...</span>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <?php $queryParams['page'] = $page + 1; ?>
        <a href="?<?= http_build_query($queryParams) ?>" class="px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300">
            <i class="fas fa-chevron-right"></i>
        </a>
    <?php endif; ?>
</div>

<!-- MODAL SUPPRESSION -->
<div id="myModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Suppression du trajet</h2>
        <p class="mb-4">Êtes-vous sûr de vouloir supprimer ce voyage ?</p>

        <form method="POST" action="">
            <input type="hidden" id="id_voyage" name="id_voyage">
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Annuler
                </button>
                <button type="submit" name="delete_voyage" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Supprimer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function submitFilterForm(element) {
        element.classList.add('scale-105', 'ring-2', 'ring-blue-400');

        setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 180);
    }

    function openModal(id) {
        document.getElementById('id_voyage').value = id;
        document.getElementById('myModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('myModal').classList.add('hidden');
    }
</script>

<?php
$adminContent = ob_get_clean();

$adminTitle = 'Liste des voyages';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Bienvenu dans votre espace Administrateur ! ! !';
$baseUrl = './';

include __DIR__ . '/../includes/layoutadmin.php';
?>