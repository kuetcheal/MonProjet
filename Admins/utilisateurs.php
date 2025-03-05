<?php
session_start();
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'bd_stock';

try {
    // Connexion à la base de données
    $bdd = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Suppression d'un utilisateur
    if (isset($_POST['delete_user'])) {
        $id = $_POST['user_id'];
        $query = "DELETE FROM user WHERE id = :id";
        $stmt = $bdd->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Utilisateur supprimé avec succès.";
        } else {
            $_SESSION['message'] = "Erreur lors de la suppression de l'utilisateur.";
        }
        header("Location: users.php");
        exit();
    }

    // Pagination : 8 utilisateurs par page
    $limit = 8;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Récupération des utilisateurs avec pagination
    $requete = $bdd->prepare("SELECT * FROM user ORDER BY id DESC LIMIT :start, :limit");
    $requete->bindParam(':start', $start, PDO::PARAM_INT);
    $requete->bindParam(':limit', $limit, PDO::PARAM_INT);
    $requete->execute();
    $users = $requete->fetchAll(PDO::FETCH_ASSOC);

    // Compter le nombre total d'utilisateurs
    $total_query = $bdd->query("SELECT COUNT(*) FROM user");
    $total_users = $total_query->fetchColumn();
    $total_pages = ceil($total_users / $limit);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des utilisateurs</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <div class="container mx-auto p-6 flex-grow">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Liste des Utilisateurs</h2>

        <?php if (isset($_SESSION['message'])) : ?>
            <div class="bg-green-500 text-white p-3 mb-4 rounded-md text-center">
                <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4">ID</th>
                        <th class="py-2 px-4">Nom</th>
                        <th class="py-2 px-4">Prénom</th>
                        <th class="py-2 px-4">Email</th>
                        <th class="py-2 px-4">Téléphone</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr class="border-b">
                            <td class="py-2 px-4 text-center"><?= $user['id']; ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($user['user_name']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($user['user_firstname']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($user['user_mail']); ?></td>
                            <td class="py-2 px-4 text-center"><?= htmlspecialchars($user['user_phone']); ?></td>
                            <td class="py-2 px-4 text-center">
                                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                    <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-700">
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($users)) : ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">Aucun utilisateur trouvé.</td>
                        </tr>
                    <?php endif; ?>
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
    </div>

    <!-- Footer Fixe -->
    <footer class="bg-gray-700 text-white text-center py-4 w-full mt-auto">
        <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>
</body>
</html>
