<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'bd_stock';

// Connexion à la base de données
$conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);

// Récupération des valeurs uniques pour les filtres
$villeDepartList = $conn->query("SELECT DISTINCT villeDepart FROM voyage")->fetchAll(PDO::FETCH_COLUMN);
$villeArriveeList = $conn->query("SELECT DISTINCT villeArrivee FROM voyage")->fetchAll(PDO::FETCH_COLUMN);

// Filtrage des résultats
$conditions = [];
$params = [];

if (!empty($_GET['villeDepart'])) {
    $conditions[] = "villeDepart = :villeDepart";
    $params[':villeDepart'] = $_GET['villeDepart'];
}
if (!empty($_GET['villeArrivee'])) {
    $conditions[] = "villeArrivee = :villeArrivee";
    $params[':villeArrivee'] = $_GET['villeArrivee'];
}
if (!empty($_GET['jourDepart'])) {
    $conditions[] = "jourDepart = :jourDepart";
    $params[':jourDepart'] = $_GET['jourDepart'];
}
if (!empty($_GET['prix'])) {
    $conditions[] = "prix <= :prix";
    $params[':prix'] = $_GET['prix'];
}
// Construire la requête SQL avec filtres
$whereClause = !empty($conditions) ? ' WHERE ' . implode(' AND ', $conditions) : '';
$query = "SELECT * FROM voyage $whereClause ORDER BY jourDepart DESC";



// PAGINATION - Limite à 5 voyages par page
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Récupération des voyages avec pagination
$resultat = $conn->prepare("SELECT * FROM voyage ORDER BY jourDepart DESC LIMIT :start, :limit");
$resultat->bindParam(':start', $start, PDO::PARAM_INT);
$resultat->bindParam(':limit', $limit, PDO::PARAM_INT);
$resultat->execute();
$voyages = $resultat->fetchAll(PDO::FETCH_ASSOC);

// Compter le nombre total de voyages
$total_query = "SELECT COUNT(*) FROM voyage";
$total_result = $conn->query($total_query);
$total_voyages = $total_result->fetchColumn();
$total_pages = ceil($total_voyages / $limit);



// Vérifier si le formulaire de suppression a été soumis
if (isset($_POST['delete_voyage'])) {
    $id = $_POST['id_voyage'];
    $query = "DELETE FROM voyage WHERE idVoyage = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<meta http-equiv='refresh' content='2;url=listevoyadmin.php'>";
        echo "<div class='fixed top-0 left-0 w-full bg-green-500 text-white text-center p-4'>Voyage supprimé avec succès.</div>";
    } else {
        echo "<div class='fixed top-0 left-0 w-full bg-red-500 text-white text-center p-4'>Erreur lors de la suppression.</div>";
    }
}



?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Voyages</title>

    <!-- Tailwind & Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="min-h-screen flex flex-col">

    <div class="flex flex-1">

        <aside class="w-60 bg-gray-900 text-white min-h-screen flex flex-col justify-between">

            <div class="flex items-center mb-6 pl-2">
                <span class="text-indigo-400 text-2xl font-bold">⚡ Général </span>
            </div>

            <nav class="flex-1">
                <ul class="space-y-1">
                    <li class="bg-gray-800 rounded-lg">
                        <a href="admin.php" class="flex items-center px-3 py-2">
                            <i class="fas fa-home w-5"></i> <span class="ml-3">Trajets</span>
                        </a>
                    </li>
                    <li>
                        <a href="listevoyadmin.php" class="flex items-center px-3 py-2">
                            <i class="fas fa-users w-5"></i> <span class="ml-3">Voyages</span>
                        </a>
                    </li>
                    <li>
                        <a href="Admins/reservations.php" class="flex items-center px-3 py-2">
                            <i class="fas fa-folder w-5"></i> <span class="ml-3">Reservations</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2">
                            <i class="fas fa-calendar w-5"></i> <span class="ml-3">Calendar</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center px-3 py-2">
                            <i class="fas fa-file w-5"></i> <span class="ml-3">Paiement</span>
                        </a>
                    </li>
                    <li>
                        <a href="Admins/utilisateurs.php" class="flex items-center px-3 py-2">
                            <i class="fas fa-chart-bar w-5"></i> <span class="ml-3">Abonnés</span>
                        </a>
                    </li>
                    <li>
                        <a href="Admins/destinations.php" class="flex items-center px-3 py-2">
                            <i class="fas fa-chart-bar w-5"></i> <span class="ml-3">Destinations</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="mt-auto">
                <h2 class="text-gray-400 text-xs uppercase pl-3 mb-2">CONCERNANT AGENCE</h2>
                <ul class="space-y-1">
                    <li class="flex items-center px-3 py-2">
                        <span class="bg-gray-700 p-2 rounded-full text-xs w-6 h-6 flex items-center justify-center">H</span>
                        <span class="ml-3"> destinations</span>
                    </li>
                    <li class="flex items-center px-3 py-2">
                        <span class="bg-gray-700 p-2 rounded-full text-xs w-6 h-6 flex items-center justify-center">T</span>
                        <span class="ml-3">Tailwind</span>
                    </li>
                    <li class="flex items-center px-3 py-2">
                        <span class="bg-gray-700 p-2 rounded-full text-xs w-6 h-6 flex items-center justify-center">W</span>
                        <span class="ml-3">Workcation</span>
                    </li>
                </ul>

                <a href="#" class="flex items-center px-3 py-2 mt-4">
                    <i class="fas fa-cog w-5"></i> <span class="ml-3">Settings</span>
                </a>
            </div>
        </aside>


        <!-- Contenu Principal -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white p-4 flex items-center justify-between shadow">
                <div class="relative w-1/3">
                    <input type="text" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring focus:ring-indigo-300" placeholder="Search">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                </div>

                <div class="flex items-center space-x-4">
                    <i class="fas fa-bell text-gray-500"></i>
                    <div class="flex items-center space-x-2">
                        <img src="pictures/OIP.jpg" class="rounded-full w-8 h-8" alt="User">
                        <span class="text-gray-700 font-bold text-lg">Tom Cook</span>
                    </div>

                </div>
            </header>

            <!-- Contenu -->
            <main class="flex-1 p-6">
                <h4 class="text-center text-xl font-bold mb-4">Liste des trajets disponibles</h4>


                <div class="flex items-center gap-4 bg-white p-4 rounded-lg shadow-md w-full">
                    <!-- Bouton Ajouter un voyage -->
                    <a href="insertionvoyage.php" class="bg-blue-500 text-white h-12 w-48 mt-6 flex items-center justify-center rounded-lg transition duration-300 hover:bg-blue-600">
                        <i class="fa fa-plus-circle mr-2"></i> Ajouter un voyage
                    </a>

                    <!-- Sélecteur Ville Départ -->
                    <div class="flex flex-col">
                        <label class="block text-gray-700 font-bold">Ville Départ</label>
                        <select name="villeDepart" class="border p-2 rounded h-12 w-48">
                            <option value="">Toutes</option>
                            <?php foreach ($villeDepartList as $ville) : ?>
                                <option value="<?= $ville; ?>" <?= (isset($_GET['villeDepart']) && $_GET['villeDepart'] == $ville) ? 'selected' : ''; ?>>
                                    <?= $ville; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sélecteur Ville Arrivée -->
                    <div class="flex flex-col">
                        <label class="block text-gray-700 font-bold">Ville Arrivée</label>
                        <select name="villeArrivee" class="border p-2 rounded h-12 w-48">
                            <option value="">Toutes</option>
                            <?php foreach ($villeArriveeList as $ville) : ?>
                                <option value="<?= $ville; ?>" <?= (isset($_GET['villeArrivee']) && $_GET['villeArrivee'] == $ville) ? 'selected' : ''; ?>>
                                    <?= $ville; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Champ Date -->
                    <div class="flex flex-col">
                        <label class="block text-gray-700 font-bold">Date</label>
                        <input type="date" name="jourDepart" value="<?= $_GET['jourDepart'] ?? ''; ?>" class="border p-2 rounded h-12 w-48">
                    </div>

                    <!-- Bouton Filtrer -->
                    <div class="flex flex-col">
                        <label class="invisible">Filtrer</label>
                        <button type="submit" class="bg-gray-500 text-white h-12 w-48 flex items-center justify-center rounded-lg transition duration-300 hover:bg-gray-700">
                            <i class="fa fa-sliders mr-2"></i> Filtrer
                        </button>
                    </div>
                </div>



                <!-- CODE PHP POUR LA SUPPRESSION D'UN VOYAGE' -->
                <?php
                // Vérifier si le formulaire de suppression a été soumis
                if (isset($_POST['delete_voyage'])) {
                    $host = 'localhost'; // nom d'hôte
                    $user = 'root'; // nom d'utilisateur
                    $password = ''; // mot de passe
                    $database = 'bd_stock'; // nom de la base de données

                    // Connexion à la base de données MySQLi
                    $conn = mysqli_connect($host, $user, $password, $database);
                    $id = $_POST['id_voyage'];
                    $query = "DELETE FROM voyage WHERE idVoyage =$id";
                    $result = mysqli_query($conn, $query);

                    if ($result) {
                        echo "<meta http-equiv='refresh' content='3;url=listevoyadmin.php'>";
                        echo "<div style='height: 100px; width: 600px; background-color: green; color: white;
                    font-size: 25px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px; '>
                    Voyage supprimé avec succès.
                    </div>";
                    } else {
                        echo 'Erreur lors de la suppression du voyage.';
                    }
                    header('Location: listevoyadmin.php');
                    mysqli_close($conn);
                    exit;
                }
                ?>

                <!-- Tableau -->
                <div class="overflow-x-auto bg-white shadow-md rounded-lg p-4">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-800 text-white">
                                <th class="p-2">ID Voyage</th>
                                <th class="p-2">Ville Départ</th>
                                <th class="p-2">Ville Arrivée</th>
                                <th class="p-2">Heure Départ</th>
                                <th class="p-2">Heure Arrivée</th>
                                <th class="p-2">Type de Bus</th>
                                <th class="p-2">Prix</th>
                                <th class="p-2">Date</th>
                                <th class="p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($voyages as $donne) {
                                echo "
        <tr class='border-b'>
            <td class='p-2'>{$donne['idVoyage']}</td>
            <td class='p-2'>{$donne['villeDepart']}</td>
            <td class='p-2'>{$donne['villeArrivee']}</td>
            <td class='p-2'>{$donne['heureDepart']}</td>
            <td class='p-2'>{$donne['heureArrivee']}</td>
            <td class='p-2'>{$donne['typeBus']}</td>
            <td class='p-2'>{$donne['prix']}</td>
            <td class='p-2'>{$donne['jourDepart']}</td>
            <td class='p-2 flex space-x-2'>
                <form method='post' action='modifier.php'>
                    <input type='hidden' name='id_voyage' value='{$donne['idVoyage']}'>
                    <button type='submit' class='bg-green-500 text-white p-2 rounded'>
                        <i class='fas fa-edit'></i>
                    </button>
                </form>
                <button type='button' onclick='openModal({$donne['idVoyage']})' class='bg-red-500 text-white p-2 rounded'>
                    <i class='fas fa-trash'></i>
                </button>
            </td>
        </tr>";
                            }
                            ?>
                        </tbody>

                    </table>
                </div>

                <!-- Pagination -->
                <div class="flex justify-center mt-6 space-x-2">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1; ?>" class="px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <?php if ($i == $page): ?>
                            <span class="px-3 py-1 rounded-full bg-gray-900 text-white"><?= $i; ?></span>
                        <?php elseif ($i == 1 || $i == $total_pages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="?page=<?= $i; ?>" class="px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300">
                                <?= $i; ?>
                            </a>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span class="px-3 py-1 text-gray-500">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>


                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?= $page + 1; ?>" class="px-3 py-1 rounded border bg-gray-200 hover:bg-gray-300">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>


    <!-- POPUP DE SUPPRESSION -->
    <div id="myModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-xl font-bold mb-4">Suppression du trajet</h2>
            <p class="mb-4">Êtes-vous sûr de vouloir supprimer ce voyage ?</p>
            <form method="POST" action="">
                <input type="hidden" id="id_voyage" name="id_voyage">
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-500 text-white rounded">Annuler</button>
                    <button type="submit" name="delete_voyage" class="px-4 py-2 bg-red-500 text-white rounded">Supprimer</button>
                </div>
            </form>
        </div>
    </div>


    <footer class="bg-gray-700 text-white text-center py-4 w-full">
        <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!</p>
    </footer>


    <script>
        function openModal(id) {
            document.getElementById("id_voyage").value = id;
            document.getElementById("myModal").classList.remove("hidden");
        }

        function closeModal() {
            document.getElementById("myModal").classList.add("hidden");
        }
    </script>
</body>

</html>