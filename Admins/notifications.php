<?php
session_start();
require_once __DIR__ . '/../config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base.');
}

$bdd->exec("UPDATE notifications SET is_read = 1 WHERE cible_role = 'admin' AND is_read = 0");

$notifCountStmt = $bdd->query("SELECT COUNT(*) FROM notifications WHERE cible_role = 'admin' AND is_read = 0");
$adminNotificationCount = (int) $notifCountStmt->fetchColumn();

$stmt = $bdd->query("
    SELECT *
    FROM notifications
    WHERE cible_role = 'admin'
    ORDER BY created_at DESC
");
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<div class="max-w-5xl mx-auto">
    <h1 class="text-3xl font-bold text-slate-800 mb-6">Notifications administrateur</h1>

    <div class="space-y-4">
        <?php foreach ($notifications as $notif): ?>
            <a href="<?= htmlspecialchars($notif['lien'] ?: '#') ?>" class="block bg-white rounded-xl shadow p-5 hover:bg-slate-50 transition">
                <h2 class="text-lg font-semibold text-slate-800 mb-1">
                    <?= htmlspecialchars($notif['titre']) ?>
                </h2>
                <p class="text-gray-600 mb-2"><?= htmlspecialchars($notif['message']) ?></p>
                <p class="text-xs text-gray-400"><?= htmlspecialchars($notif['created_at']) ?></p>
            </a>
        <?php endforeach; ?>

        <?php if (empty($notifications)): ?>
            <div class="bg-white rounded-xl shadow p-6 text-center text-gray-500">
                Aucune notification pour le moment.
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
$adminContent = ob_get_clean();

$adminTitle = 'Notifications';
$adminWelcome = 'Centre de notifications';
$adminUserName = 'Alex Stephane';
$baseUrl = '/MonProjet/Admins/';

include __DIR__ . '/../includes/layoutadmin.php';