<?php
session_start();
require_once __DIR__ . '/../config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

/**
 * Suppression d'un utilisateur
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'], $_POST['user_id'])) {
    $id = (int) $_POST['user_id'];

    if ($id > 0) {
        $stmt = $bdd->prepare("DELETE FROM user WHERE id = :id");
        $success = $stmt->execute([':id' => $id]);

        $_SESSION['message'] = $success
            ? "Utilisateur supprimé avec succès."
            : "Erreur lors de la suppression de l'utilisateur.";
    }

    header("Location: utilisateurs.php");
    exit;
}

/**
 * Filtres
 */
$roleFilter = trim($_GET['role'] ?? '');
$search = trim($_GET['search'] ?? '');

/**
 * Pagination
 */
$limit = 8;
$page = isset($_GET['page']) && (int) $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

/**
 * Construction des conditions SQL
 */
$where = [];
$params = [];

if ($roleFilter !== '') {
    $where[] = "u.role = :role";
    $params[':role'] = $roleFilter;
}

if ($search !== '') {
    $where[] = "(
        u.user_name LIKE :search
        OR u.user_firstname LIKE :search
        OR u.user_mail LIKE :search
        OR u.user_phone LIKE :search
    )";
    $params[':search'] = '%' . $search . '%';
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

/**
 * Notifications admin non lues
 */
$notifCountStmt = $bdd->query("
    SELECT COUNT(*)
    FROM notifications
    WHERE cible_role = 'admin' AND is_read = 0
");
$adminNotificationCount = (int) $notifCountStmt->fetchColumn();

/**
 * Récupération des utilisateurs avec infos chauffeur
 */
$sqlUsers = "
    SELECT
        u.id,
        u.user_name,
        u.user_firstname,
        u.user_mail,
        u.user_phone,
        u.role,
        u.account_status,
        cp.statut_validation
    FROM user u
    LEFT JOIN chauffeur_profile cp ON cp.user_id = u.id
    $whereSql
    ORDER BY u.id DESC
    LIMIT :start, :limit
";

$requete = $bdd->prepare($sqlUsers);

foreach ($params as $key => $value) {
    $requete->bindValue($key, $value);
}

$requete->bindValue(':start', $start, PDO::PARAM_INT);
$requete->bindValue(':limit', $limit, PDO::PARAM_INT);
$requete->execute();

$users = $requete->fetchAll(PDO::FETCH_ASSOC);

/**
 * Total utilisateurs pour pagination
 */
$sqlTotal = "
    SELECT COUNT(*)
    FROM user u
    LEFT JOIN chauffeur_profile cp ON cp.user_id = u.id
    $whereSql
";

$totalQuery = $bdd->prepare($sqlTotal);
foreach ($params as $key => $value) {
    $totalQuery->bindValue($key, $value);
}
$totalQuery->execute();

$totalUsers = (int) $totalQuery->fetchColumn();
$totalPages = max(1, (int) ceil($totalUsers / $limit));

function roleBadge(string $role = ''): string
{
    $role = trim($role);

    switch ($role) {
        case 'admin':
            return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-100 text-purple-700">Admin</span>';
        case 'chauffeur':
            return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">Chauffeur</span>';
        case 'client_chauffeur':
            return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">Client / Chauffeur</span>';
        case 'client':
        default:
            return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Client</span>';
    }
}

function chauffeurStatusBadge(?string $status): string
{
    $status = trim((string) $status);

    if ($status === 'valide') {
        return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">Validé</span>';
    }

    if ($status === 'refuse') {
        return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">Refusé</span>';
    }

    if ($status === 'en_attente') {
        return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">En attente</span>';
    }

    return '<span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600">Non chauffeur</span>';
}

ob_start();
?>

<div class="max-w-7xl mx-auto">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <h2 class="text-3xl font-bold text-slate-800">Liste des utilisateurs</h2>

        <div class="text-sm text-gray-500">
            Total : <span class="font-semibold text-slate-700"><?= (int) $totalUsers ?></span> utilisateur(s)
        </div>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-100 text-green-700 border border-green-200 px-4 py-3 mb-5 rounded-lg">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form method="GET" class="bg-white rounded-2xl shadow p-5 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Recherche</label>
                <input
                    type="text"
                    name="search"
                    value="<?= htmlspecialchars($search) ?>"
                    placeholder="Nom, prénom, email, téléphone"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300"
                >
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Rôle</label>
                <select
                    name="role"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300"
                >
                    <option value="">Tous les rôles</option>
                    <option value="client" <?= $roleFilter === 'client' ? 'selected' : '' ?>>Client</option>
                    <option value="client_chauffeur" <?= $roleFilter === 'client_chauffeur' ? 'selected' : '' ?>>Client / Chauffeur</option>
                    <option value="chauffeur" <?= $roleFilter === 'chauffeur' ? 'selected' : '' ?>>Chauffeur</option>
                    <option value="admin" <?= $roleFilter === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-lg font-medium transition"
                >
                    Filtrer
                </button>

                <a
                    href="utilisateurs.php"
                    class="bg-red-500 hover:bg-red-600 text-white px-5 py-3 rounded-lg font-medium transition"
                >
                    Réinitialiser
                </a>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="py-4 px-4 text-left">ID</th>
                        <th class="py-4 px-4 text-left">Nom</th>
                        <th class="py-4 px-4 text-left">Prénom</th>
                        <th class="py-4 px-4 text-left">Email</th>
                        <th class="py-4 px-4 text-left">Téléphone</th>
                        <th class="py-4 px-4 text-left">Rôle</th>
                        <th class="py-4 px-4 text-left">Statut chauffeur</th>
                        <th class="py-4 px-4 text-left">Statut compte</th>
                        <th class="py-4 px-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $userItem): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-4 px-4"><?= (int) $userItem['id'] ?></td>
                            <td class="py-4 px-4 font-medium text-slate-800">
                                <?= htmlspecialchars($userItem['user_name'] ?? '') ?>
                            </td>
                            <td class="py-4 px-4">
                                <?= htmlspecialchars($userItem['user_firstname'] ?? '') ?>
                            </td>
                            <td class="py-4 px-4">
                                <?= htmlspecialchars($userItem['user_mail'] ?? '') ?>
                            </td>
                            <td class="py-4 px-4">
                                <?= htmlspecialchars((string) ($userItem['user_phone'] ?? '')) ?>
                            </td>
                            <td class="py-4 px-4">
                                <?= roleBadge($userItem['role'] ?? 'client') ?>
                            </td>
                            <td class="py-4 px-4">
                                <?= chauffeurStatusBadge($userItem['statut_validation'] ?? null) ?>
                            </td>
                            <td class="py-4 px-4">
                                <?php
                                $accountStatus = $userItem['account_status'] ?? 'active';
                                $accountClass = $accountStatus === 'active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-gray-100 text-gray-600';
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $accountClass ?>">
                                    <?= htmlspecialchars($accountStatus) ?>
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                    <input type="hidden" name="user_id" value="<?= (int) $userItem['id'] ?>">
                                    <button
                                        type="submit"
                                        name="delete_user"
                                        class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition"
                                    >
                                        Supprimer
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center mt-6 flex-wrap gap-2">
            <?php if ($page > 1): ?>
                <a
                    href="?page=<?= $page - 1 ?>&role=<?= urlencode($roleFilter) ?>&search=<?= urlencode($search) ?>"
                    class="px-4 py-2 rounded border bg-gray-200 hover:bg-gray-300"
                >
                    <i class="fas fa-chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="px-4 py-2 rounded-full bg-gray-900 text-white"><?= $i ?></span>
                <?php elseif ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <a
                        href="?page=<?= $i ?>&role=<?= urlencode($roleFilter) ?>&search=<?= urlencode($search) ?>"
                        class="px-4 py-2 rounded border bg-gray-200 hover:bg-gray-300"
                    >
                        <?= $i ?>
                    </a>
                <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                    <span class="px-4 py-2 text-gray-500">...</span>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a
                    href="?page=<?= $page + 1 ?>&role=<?= urlencode($roleFilter) ?>&search=<?= urlencode($search) ?>"
                    class="px-4 py-2 rounded border bg-gray-200 hover:bg-gray-300"
                >
                    <i class="fas fa-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$adminContent = ob_get_clean();

$adminTitle = 'Liste des utilisateurs';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Gestion des utilisateurs';
$baseUrl = '/MonProjet/Admins/';

include __DIR__ . '/../includes/layoutadmin.php';
?>