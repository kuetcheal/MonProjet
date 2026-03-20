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

    header("Location: destinations.php?success=delete");
    exit();
}

/* =========================
   AJOUT D'UNE DESTINATION
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nom_ville'])) {
    $nom_ville = trim($_POST['nom_ville']);

    if (!empty($nom_ville)) {
        $stmt = $bdd->prepare("INSERT INTO destination (Nom_ville) VALUES (?)");
        $stmt->execute([$nom_ville]);

        header("Location: destinations.php?success=add");
        exit();
    } else {
        header("Location: destinations.php?error=empty");
        exit();
    }
}

/* =========================
   RECUPERATION DES DESTINATIONS
========================= */
$stmt = $bdd->query("SELECT * FROM destination ORDER BY id_destination ASC");
$destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   CONTENU DE LA PAGE
========================= */
ob_start();
?>

<div class="max-w-5xl mx-auto">
    <?php if (isset($_GET['success']) && $_GET['success'] === 'add'): ?>
        <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            Destination ajoutée avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'delete'): ?>
        <div class="mb-4 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            Destination supprimée avec succès.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'empty'): ?>
        <div class="mb-4 bg-red-500 text-white text-center p-3 rounded-lg shadow">
            Le nom de la ville est obligatoire.
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-md rounded-lg p-6 w-full">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Liste des Destinations</h2>

        <button onclick="toggleModal(true)" class="mb-4 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none">
            + Ajouter une destination
        </button>

        <div class="overflow-x-auto">
            <table class="w-full bg-white border rounded-lg overflow-hidden">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Nom de la ville</th>
                        <th class="py-2 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($destinations)): ?>
                        <?php foreach ($destinations as $destination): ?>
                            <tr class="border-t">
                                <td class="py-2 px-4"><?= htmlspecialchars($destination['id_destination']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($destination['Nom_ville']) ?></td>
                                <td class="py-2 px-4 text-center">
                                    <button onclick="confirmDelete(<?= (int)$destination['id_destination'] ?>)" class="text-red-500 hover:text-red-700">
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
</div>

<!-- Modal Ajout Destination -->
<div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-lg font-bold mb-4">Ajouter une destination</h2>
        <form action="destinations.php" method="POST">
            <input type="text" name="nom_ville" placeholder="Nom de la ville" required class="w-full px-4 py-2 border rounded-lg mb-4">
            <div class="flex justify-between">
                <button type="button" onclick="toggleModal(false)" class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-600">
                    Annuler
                </button>
                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-700">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de confirmation de suppression -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
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

<script>
    function toggleModal(show) {
        document.getElementById('modal').style.display = show ? 'flex' : 'none';
    }

    function confirmDelete(id) {
        document.getElementById('deleteConfirmLink').href = "destinations.php?confirm_delete_id=" + id;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function toggleDeleteModal(show) {
        document.getElementById('deleteModal').style.display = show ? 'none' : 'flex';
    }
</script>

<?php
$adminContent = ob_get_clean();

$adminTitle = 'Gestion des destinations';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Bienvenu dans votre espace Administrateur ! ! !';
$baseUrl = '';

include __DIR__ . '/../includes/layoutadmin.php';
?>