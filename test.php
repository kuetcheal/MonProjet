<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'bd_stock';

// Connexion à la base de données
$conn = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);

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

// PAGINATION
$limit = 10; // Nombre de voyages par page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Récupération des voyages avec pagination
$query = "SELECT * FROM voyage ORDER BY jourDepart DESC LIMIT $start, $limit";
$voyages = $conn->query($query)->fetchAll();

// Compter le nombre total de voyages
$total_query = "SELECT COUNT(*) FROM voyage";
$total_result = $conn->query($total_query);
$total_voyages = $total_result->fetchColumn();
$total_pages = ceil($total_voyages / $limit);
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