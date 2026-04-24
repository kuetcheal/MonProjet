<?php
session_start();

require_once __DIR__ . '/../config.php';

// Suppression
if (isset($_POST['delete_voyage'])) {
    $id = (int) $_POST['id_voyage'];

    $query = "DELETE FROM voyage WHERE idVoyage = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header("Location: listevoyadmin.php?success=delete");
        exit;
    }

    header("Location: listevoyadmin.php?error=delete");
    exit;
}

// Pagination
$limit = 10;
$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Récupération des voyages
$query = "SELECT * FROM voyage ORDER BY jourDepart DESC LIMIT :start, :limit";
$stmt = $pdo->prepare($query);
$stmt->bindValue(':start', $start, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$voyages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total
$total_query = "SELECT COUNT(*) FROM voyage";
$total_result = $pdo->query($total_query);
$total_voyages = (int) $total_result->fetchColumn();
$total_pages = max(1, (int) ceil($total_voyages / $limit));
?>




 <!-- Pagination -->
 <div class="flex justify-center mt-4">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i; ?>" class="px-3 py-1 mx-1 bg-gray-200 rounded"><?= $i; ?></a>
                    <?php endfor; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-700 text-white text-center py-4 w-full">
        <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!</p>
    </footer>


    <body class="h-screen bg-gray-100 flex flex-col">
    <div class="flex flex-1">
    <aside class="w-64 bg-gray-900 text-white h-screen p-5 flex flex-col">
    </div>
    </div>