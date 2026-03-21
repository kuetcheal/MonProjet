<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('Échec de connexion : ' . $e->getMessage());
}

/* =========================
   SUPPRESSION D'UNE DESTINATION
========================= */
if (isset($_GET['confirm_delete_id']) && !empty($_GET['confirm_delete_id'])) {
    $delete_id = (int) $_GET['confirm_delete_id'];

    $stmt = $bdd->prepare("DELETE FROM destination WHERE id_destination = ?");
    $stmt->execute([$delete_id]);

    header("Location: destinations.php?success=delete_destination");
    exit();
}

/* =========================
   SUPPRESSION D'UN QUARTIER
========================= */
if (isset($_GET['confirm_delete_quartier_id']) && !empty($_GET['confirm_delete_quartier_id'])) {
    $delete_quartier_id = (int) $_GET['confirm_delete_quartier_id'];

    $stmt = $bdd->prepare("DELETE FROM quartier WHERE id_quartier = ?");
    $stmt->execute([$delete_quartier_id]);

    header("Location: destinations.php?success=delete_quartier");
    exit();
}

/* =========================
   AJOUT D'UNE DESTINATION
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_destination') {
    $nom_ville = trim($_POST['nom_ville']);

    if (!empty($nom_ville)) {
        $stmt = $bdd->prepare("INSERT INTO destination (Nom_ville) VALUES (?)");
        $stmt->execute([$nom_ville]);

        header("Location: destinations.php?success=add_destination");
        exit();
    } else {
        header("Location: destinations.php?error=empty_destination");
        exit();
    }
}

/* =========================
   AJOUT D'UN QUARTIER
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_quartier') {
    $nom_quartier = trim($_POST['nom_quartier']);
    $id_destination = isset($_POST['id_destination']) ? (int) $_POST['id_destination'] : 0;

    if (!empty($nom_quartier) && $id_destination > 0) {
        $stmt = $bdd->prepare("INSERT INTO quartier (nom_quartier, id_destination) VALUES (?, ?)");
        $stmt->execute([$nom_quartier, $id_destination]);

        header("Location: destinations.php?success=add_quartier");
        exit();
    } else {
        header("Location: destinations.php?error=empty_quartier");
        exit();
    }
}

/* =========================
   RECUPERATION DES DESTINATIONS
========================= */
$stmt = $bdd->query("SELECT * FROM destination ORDER BY Nom_ville ASC");
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   FILTRE + PAGINATION QUARTIERS
========================= */
$filtreVille = isset($_GET['ville']) ? trim($_GET['ville']) : '';
$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$parPage = 10;
$offset = ($page - 1) * $parPage;

/* =========================
   TOTAL GENERAL DES QUARTIERS
========================= */
$stmt = $bdd->query("SELECT COUNT(*) FROM quartier");
$totalQuartiersGeneral = (int) $stmt->fetchColumn();

/* =========================
   TOTAL FILTRE
========================= */
$nomVilleFiltre = '';

if (!empty($filtreVille)) {
    $stmt = $bdd->prepare("
        SELECT COUNT(*)
        FROM quartier q
        INNER JOIN destination d ON q.id_destination = d.id_destination
        WHERE d.id_destination = ?
    ");
    $stmt->execute([$filtreVille]);
    $totalQuartiersFiltres = (int) $stmt->fetchColumn();

    $stmtVille = $bdd->prepare("SELECT Nom_ville FROM destination WHERE id_destination = ?");
    $stmtVille->execute([$filtreVille]);
    $nomVilleFiltre = $stmtVille->fetchColumn();
} else {
    $totalQuartiersFiltres = $totalQuartiersGeneral;
}

/* =========================
   NOMBRE TOTAL DE PAGES
========================= */
$totalPages = max(1, ceil($totalQuartiersFiltres / $parPage));

if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $parPage;
}

/* =========================
   RECUPERATION DES QUARTIERS PAGINES
========================= */
if (!empty($filtreVille)) {
    $stmt = $bdd->prepare("
        SELECT q.id_quartier, q.nom_quartier, d.Nom_ville, d.id_destination
        FROM quartier q
        INNER JOIN destination d ON q.id_destination = d.id_destination
        WHERE d.id_destination = ?
        ORDER BY q.nom_quartier ASC
        LIMIT $parPage OFFSET $offset
    ");
    $stmt->execute([$filtreVille]);
    $quartiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $bdd->query("
        SELECT q.id_quartier, q.nom_quartier, d.Nom_ville, d.id_destination
        FROM quartier q
        INNER JOIN destination d ON q.id_destination = d.id_destination
        ORDER BY d.Nom_ville ASC, q.nom_quartier ASC
        LIMIT $parPage OFFSET $offset
    ");
    $quartiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* =========================
   CONTENU DE LA PAGE
========================= */
ob_start();
?>

<div class="max-w-6xl mx-auto space-y-8">

    <?php if (isset($_GET['success']) && $_GET['success'] === 'add_destination'): ?>
        <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            Destination ajoutée avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'delete_destination'): ?>
        <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            Destination supprimée avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'add_quartier'): ?>
        <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            Quartier ajouté avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'delete_quartier'): ?>
        <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            Quartier supprimé avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'empty_destination'): ?>
        <div class="mb-4 bg-red-500 text-white text-center p-3 rounded-lg shadow">
            Le nom de la ville est obligatoire.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'empty_quartier'): ?>
        <div class="mb-4 bg-red-500 text-white text-center p-3 rounded-lg shadow">
            Le nom du quartier et la ville sont obligatoires.
        </div>
    <?php endif; ?>

    <!-- Bloc villes -->
    <div class="bg-white shadow-md rounded-lg p-6 w-full">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Liste des destinations</h2>

        <button onclick="toggleModal(true)" class="mb-4 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition duration-300">
            + Ajouter une destination
        </button>

        <div class="overflow-x-auto">
            <table class="w-full bg-white border rounded-lg overflow-hidden">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">ID</th>
                        <th class="py-3 px-4 text-left">Nom de la ville</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($destinations)): ?>
                        <?php foreach ($destinations as $destination): ?>
                            <tr class="border-t hover:bg-gray-50 transition duration-200">
                                <td class="py-3 px-4"><?= htmlspecialchars($destination['id_destination']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($destination['Nom_ville']) ?></td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="confirmDelete(<?= (int)$destination['id_destination'] ?>)" class="text-red-500 hover:text-red-700 transition duration-200">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="py-4 px-4 text-center text-gray-500">Aucune destination enregistrée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bloc quartiers -->
    <div class="bg-white shadow-md rounded-lg p-6 w-full">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">
            Liste des quartiers / points de prise en charge
        </h2>

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <button onclick="toggleQuartierModal(true)" class="px-4 py-2 bg-green-500 text-white font-semibold  shadow-md hover:bg-green-700 transition duration-300 w-fit">
                + Ajouter un quartier
            </button>

            <form method="GET" action="destinations.php" class="flex flex-col md:flex-row items-center gap-3">
                <select
                    name="ville"
                    id="ville"
                    onchange="this.form.submit()"
                    class="min-w-[240px] px-4 py-2 border border-gray-300  shadow-sm focus:outline-none focus:ring-2 focus:ring-green-400 transition duration-300"
                >
                    <option value="">Toutes les villes</option>
                    <?php foreach ($destinations as $destination): ?>
                        <option value="<?= (int)$destination['id_destination'] ?>" <?= ($filtreVille == $destination['id_destination']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($destination['Nom_ville']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php if (!empty($filtreVille)): ?>
                    <a href="destinations.php" class="px-3 py-2 bg-gray-100 text-gray-700  hover:bg-gray-200 transition">
                        Réinitialiser
                    </a>
                <?php endif; ?>
            </form>

            <div class="px-5 py-2  bg-green-50  text-center">
                <p class="text-xl font-bold text-green-700">
                   <span class="text-sm text-gray-600">Nbre quartiers :</span> <?= !empty($filtreVille) ? $totalQuartiersFiltres : $totalQuartiersGeneral ?>
                </p>
            </div>
        </div>

        <?php if (!empty($filtreVille) && !empty($nomVilleFiltre)): ?>
            <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                Filtre actif sur la ville :
                <strong><?= htmlspecialchars($nomVilleFiltre) ?></strong>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="w-full bg-white border rounded-lg overflow-hidden">
                <thead class="bg-green-500 text-white">
                    <tr>
                        <th class="py-3 px-4 text-left">ID</th>
                        <th class="py-3 px-4 text-left">Quartier</th>
                        <th class="py-3 px-4 text-left">Ville</th>
                        <th class="py-3 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($quartiers)): ?>
                        <?php foreach ($quartiers as $quartier): ?>
                            <tr class="border-t hover:bg-gray-50 transition duration-300">
                                <td class="py-3 px-4"><?= htmlspecialchars($quartier['id_quartier']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($quartier['nom_quartier']) ?></td>
                                <td class="py-3 px-4"><?= htmlspecialchars($quartier['Nom_ville']) ?></td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="confirmDeleteQuartier(<?= (int)$quartier['id_quartier'] ?>)" class="text-red-500 hover:text-red-700 transition duration-200">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="py-4 px-4 text-center text-gray-500">
                                Aucun quartier trouvé.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-6 flex flex-wrap justify-center items-center gap-2">
                <?php
                $basePaginationUrl = 'destinations.php';
                if (!empty($filtreVille)) {
                    $basePaginationUrl .= '?ville=' . urlencode($filtreVille) . '&page=';
                } else {
                    $basePaginationUrl .= '?page=';
                }
                ?>

                <?php if ($page > 1): ?>
                    <a href="<?= $basePaginationUrl . ($page - 1) ?>"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Précédent
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="<?= $basePaginationUrl . $i ?>"
                       class="px-4 py-2 rounded-lg transition <?= $i == $page ? 'bg-green-500 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="<?= $basePaginationUrl . ($page + 1) ?>"
                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Suivant
                    </a>
                <?php endif; ?>
            </div>

            <div class="mt-3 text-center text-sm text-gray-500">
                Page <?= $page ?> sur <?= $totalPages ?> —
                <?= !empty($filtreVille) ? $totalQuartiersFiltres : $totalQuartiersGeneral ?>
                quartier<?= ((!empty($filtreVille) ? $totalQuartiersFiltres : $totalQuartiersGeneral) > 1) ? 's' : '' ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Ajout Destination -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50" style="display:none;">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4">Ajouter une destination</h2>
        <form action="destinations.php" method="POST">
            <input type="hidden" name="action" value="add_destination">
            <input type="text" name="nom_ville" placeholder="Nom de la ville" required class="w-full px-4 py-2 border rounded-lg mb-4">
            <div class="flex justify-between">
                <button type="button" onclick="toggleModal(false)" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-600">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-700">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Ajout Quartier -->
<div id="quartierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50" style="display:none;">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4">Ajouter un quartier</h2>
        <form action="destinations.php" method="POST">
            <input type="hidden" name="action" value="add_quartier">

            <select name="id_destination" required class="w-full px-4 py-2 border rounded-lg mb-4">
                <option value="">Choisir une ville</option>
                <?php foreach ($destinations as $destination): ?>
                    <option value="<?= (int)$destination['id_destination'] ?>">
                        <?= htmlspecialchars($destination['Nom_ville']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="text" name="nom_quartier" placeholder="Nom du quartier" required class="w-full px-4 py-2 border rounded-lg mb-4">

            <div class="flex justify-between">
                <button type="button" onclick="toggleQuartierModal(false)" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-600">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal suppression destination -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50" style="display:none;">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4 text-red-600">Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer cette destination ?</p>
        <div class="flex justify-between mt-4">
            <button type="button" onclick="toggleDeleteModal(false)" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-600">
                Annuler
            </button>
            <a id="deleteConfirmLink" href="#" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-700">
                Supprimer
            </a>
        </div>
    </div>
</div>

<!-- Modal suppression quartier -->
<div id="deleteQuartierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50" style="display:none;">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4 text-red-600">Confirmer la suppression</h2>
        <p>Êtes-vous sûr de vouloir supprimer ce quartier ?</p>
        <div class="flex justify-between mt-4">
            <button type="button" onclick="toggleDeleteQuartierModal(false)" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-600">
                Annuler
            </button>
            <a id="deleteQuartierConfirmLink" href="#" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-700">
                Supprimer
            </a>
        </div>
    </div>
</div>

<script>
    function toggleModal(show) {
        document.getElementById('modal').style.display = show ? 'flex' : 'none';
    }

    function toggleQuartierModal(show) {
        document.getElementById('quartierModal').style.display = show ? 'flex' : 'none';
    }

    function confirmDelete(id) {
        document.getElementById('deleteConfirmLink').href = "destinations.php?confirm_delete_id=" + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function confirmDeleteQuartier(id) {
        document.getElementById('deleteQuartierConfirmLink').href = "destinations.php?confirm_delete_quartier_id=" + id;
        document.getElementById('deleteQuartierModal').style.display = 'flex';
    }

    function toggleDeleteModal(show) {
        document.getElementById('deleteModal').style.display = show ? 'none' : 'flex';
    }

    function toggleDeleteQuartierModal(show) {
        document.getElementById('deleteQuartierModal').style.display = show ? 'none' : 'flex';
    }
</script>

<?php
$adminContent = ob_get_clean();

$adminTitle = 'Gestion des destinations et quartiers';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Bienvenu dans votre espace Administrateur ! ! !';
$baseUrl = '';

include __DIR__ . '/../includes/layoutadmin.php';
?>