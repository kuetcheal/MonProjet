<?php
$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

// Suppression d'une destination
if (isset($_GET['confirm_delete_id'])) {
    $delete_id = $_GET['confirm_delete_id'];
    $stmt = $bdd->prepare("DELETE FROM destination WHERE id_destination = ?");
    $stmt->execute([$delete_id]);
    header("Location: destinations.php");
    exit();
}

// Ajout d'une nouvelle destination
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['nom_ville'])) {
    $nom_ville = htmlspecialchars($_POST['nom_ville']);
    $stmt = $bdd->prepare("INSERT INTO destination (Nom_ville) VALUES (?)");
    $stmt->execute([$nom_ville]);
    header("Location: destinations.php");
    exit();
}

// Récupération des destinations
$stmt = $bdd->query("SELECT * FROM destination ORDER BY id_destination ASC");
$destinations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Destinations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    <div class="flex-grow flex items-center justify-center">
        <div class="bg-white shadow-md rounded-lg p-6 w-full max-w-3xl">
            <h2 class="text-xl font-bold text-gray-700 text-center mb-4">Liste des Destinations</h2>

            <button onclick="toggleModal(true)" class="mb-4 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none">
                + Ajouter une destination
            </button>

            <table class="w-full bg-white border rounded-lg overflow-hidden">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="py-2 px-4 text-left">ID</th>
                        <th class="py-2 px-4 text-left">Nom de la ville</th>
                        <th class="py-2 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($destinations as $destination) : ?>
                        <tr class="border-t">
                            <td class="py-2 px-4"><?= $destination['id_destination'] ?></td>
                            <td class="py-2 px-4"><?= htmlspecialchars($destination['Nom_ville']) ?></td>
                            <td class="py-2 px-4 text-center">
                                <button onclick="confirmDelete(<?= $destination['id_destination'] ?>)" class="text-red-500 hover:text-red-700">
                                    <i class="fa fa-trash"></i> <!-- Icône de suppression -->
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Ajout Destination -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
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
    <div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center">
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
    <br>
    <!-- Footer Fixé en Bas -->
    <footer class="bg-gray-700 text-white text-center py-4 w-full fixed bottom-0">
        <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!</p>
        <p>@Alex KUETCHE. By EasyTech 2024.</p>
    </footer>

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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>
