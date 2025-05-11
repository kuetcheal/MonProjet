<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'bd_stock';

try {
    $bdd = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Suppression d'une réservation
    if (isset($_POST['delete_reservation'])) {
        $id = $_POST['reservation_id'];
        $query = "DELETE FROM reservation WHERE id_reservation = :id";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Réservation supprimée avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression de la réservation.";
        }
        header("Location: reservations.php");
        exit();
    }

    // Filtres
    $filters = [];
    $conditions = [];
    $destinations = [];
    try {
        $pdoTemp = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
        $queryDest = $pdoTemp->query("SELECT Nom_ville FROM destination ORDER BY Nom_ville ASC");
        $destinations = $queryDest->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        die("Erreur chargement destinations : " . $e->getMessage());
    }


    if (!empty($_GET['villeDepart'])) {
        $conditions[] = "v.villeDepart = :villeDepart";
        $filters['villeDepart'] = $_GET['villeDepart'];
    }
    if (!empty($_GET['villeArrivee'])) {
        $conditions[] = "v.villeArrivee = :villeArrivee";
        $filters['villeArrivee'] = $_GET['villeArrivee'];
    }
    if (!empty($_GET['jourDepart'])) {
        $conditions[] = "v.jourDepart = :jourDepart";
        $filters['jourDepart'] = $_GET['jourDepart'];
    }
    if (!empty($_GET['heureDepart'])) {
        $conditions[] = "v.heureDepart = :heureDepart";
        $filters['heureDepart'] = $_GET['heureDepart'];
    }

    // Pagination
    $limit = 8;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    $whereClause = $conditions ? "WHERE " . implode(" AND ", $conditions) : "";

    // Récupération des réservations + jointure voyage
    $sql = "SELECT r.*, v.villeDepart, v.villeArrivee, v.jourDepart, v.heureDepart FROM reservation r 
            JOIN voyage v ON r.idVoyage = v.idVoyage 
            $whereClause
            ORDER BY r.id_reservation DESC 
            LIMIT :start, :limit";

    $stmt = $bdd->prepare($sql);
    foreach ($filters as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Compte total pour pagination
    $countSql = "SELECT COUNT(*) FROM reservation r JOIN voyage v ON r.idVoyage = v.idVoyage " . ($whereClause ? $whereClause : "");
    $countStmt = $bdd->prepare($countSql);
    foreach ($filters as $key => $value) {
        $countStmt->bindValue(":$key", $value);
    }
    $countStmt->execute();
    $total_reservations = $countStmt->fetchColumn();
    $total_pages = ceil($total_reservations / $limit);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des réservations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <div class="w-[98%] mx-auto p-6 flex-grow">

        <h1 class="text-2xl font-bold text-center text-gray-800 mb-6">Liste des Réservations</h1>


        <!-- Filtres -->
        <form method="GET" id="filterForm"
            class="bg-white p-5 mb-6 rounded shadow-md flex flex-wrap items-center justify-between gap-6">

            <!-- Ville de départ -->
            <select name="villeDepart"
                onchange="document.getElementById('filterForm').submit()"
                class="w-48 h-12 border rounded px-3 text-green-700 font-semibold text-base">
                <option value="">Ville Départ</option>
                <?php foreach ($destinations as $ville): ?>
                    <option value="<?= htmlspecialchars($ville) ?>"
                        <?= (isset($_GET['villeDepart']) && $_GET['villeDepart'] === $ville) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ville) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Ville d'arrivée -->
            <select name="villeArrivee"
                onchange="document.getElementById('filterForm').submit()"
                class="w-48 h-12 border rounded px-3 text-green-700 font-semibold text-base">
                <option value=""> Ville Arrivée</option>
                <?php foreach ($destinations as $ville): ?>
                    <option value="<?= htmlspecialchars($ville) ?>"
                        <?= (isset($_GET['villeArrivee']) && $_GET['villeArrivee'] === $ville) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($ville) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Date de réservation -->
            <input type="date"
                name="jourDepart"
                value="<?= $_GET['jourDepart'] ?? '' ?>"
                onchange="document.getElementById('filterForm').submit()"
                class="w-52 h-12 border rounded px-3 text-base text-gray-700">

            <!-- Bouton de reset -->
            <a href="reservations.php"
                class="h-12 px-6 bg-gray-600 text-white rounded hover:bg-gray-700 text-base flex items-center justify-center">
                Réinitialiser les filtres
            </a>
        </form>


        <?php if (isset($_SESSION['message'])) : ?>
            <div class="bg-green-500 text-white p-3 mb-4 rounded-md text-center">
                <?= $_SESSION['message'];
                unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4">ID</th>
                        <th class="py-2 px-4">Nom</th>
                        <th class="py-2 px-4">Prénom</th>
                        <th class="py-2 px-4">Téléphone</th>
                        <th class="py-2 px-4">Email</th>
                        <th class="py-2 px-4">ID Voyage</th>
                        <th class="py-2 px-4">Ville départ</th>
                        <th class="py-2 px-4">Ville arrivée</th>
                        <th class="py-2 px-4">Date</th>
                        <th class="py-2 px-4">Heure</th>
                        <th class="py-2 px-4">N° réservation</th>
                        <!-- <th class="py-2 px-4">N° siège</th> -->
                        <th class="py-2 px-4">Prix</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation) : ?>
                        <tr class="border-b">
                            <td class="py-2 px-4 text-center"><?= $reservation['id_reservation']; ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['nom']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['prenom']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['telephone']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['email']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['idVoyage']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['villeDepart']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['villeArrivee']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['jourDepart']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['heureDepart']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['Numero_reservation']); ?></td>
                            <!-- <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['Numero_siege']); ?></td> -->
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($reservation['prix_reservation']); ?> FCFA</td>
                            <td class="py-2 px-4 text-center">
                                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette réservation ?');">
                                    <input type="hidden" name="reservation_id" value="<?= $reservation['id_reservation']; ?>">
                                    <button type="submit" name="delete_reservation" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-700">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($reservations)) : ?>
                        <tr>
                            <td colspan="14" class="text-center py-4 text-gray-500">Aucune réservation trouvée.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-6 space-x-2">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                    class="px-3 py-1 rounded border <?= $i == $page ? 'bg-gray-900 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
                    <?= $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <footer class="bg-gray-700 text-white text-center py-4 w-full mt-auto">
        <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>
</body>

</html>