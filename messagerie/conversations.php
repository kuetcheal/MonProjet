<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../connexion.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id AS conversation_id,
            c.reservation_id,
            c.voyage_id,
            c.client_id,
            c.chauffeur_id,
            c.statut,
            c.updated_at,

            v.villeDepart,
            v.villeArrivee,
            v.jourDepart,
            v.heureDepart,

            (
                SELECT m.message
                FROM messages m
                WHERE m.conversation_id = c.id
                ORDER BY m.created_at DESC
                LIMIT 1
            ) AS last_message,

            (
                SELECT m.created_at
                FROM messages m
                WHERE m.conversation_id = c.id
                ORDER BY m.created_at DESC
                LIMIT 1
            ) AS last_message_date,

            (
                SELECT COUNT(*)
                FROM messages m
                WHERE m.conversation_id = c.id
                AND m.receiver_id = :user_id_unread
                AND m.is_read = 0
            ) AS unread_count

        FROM conversations c
        LEFT JOIN voyage v ON v.idVoyage = c.voyage_id
        WHERE 
            (c.client_id = :user_id_client OR c.chauffeur_id = :user_id_chauffeur)
            AND c.statut = 'active'
        ORDER BY c.updated_at DESC
    ");

    $stmt->execute([
        ':user_id_unread' => $userId,
        ':user_id_client' => $userId,
        ':user_id_chauffeur' => $userId
    ]);

    $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes conversations</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Mes conversations</h1>
        <p class="text-gray-500 mt-1">Retrouvez vos discussions avec les chauffeurs ou les clients.</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <?php if (empty($conversations)): ?>
            <div class="p-8 text-center">
                <p class="text-gray-500">Aucune conversation disponible pour le moment.</p>
            </div>
        <?php else: ?>
            <?php foreach ($conversations as $conversation): ?>
                <?php
                    $isClient = ((int) $conversation['client_id'] === $userId);
                    $roleText = $isClient ? 'Chauffeur' : 'Client';
                    $lastMessage = $conversation['last_message'] ?: 'Conversation créée. Vous pouvez commencer à discuter.';
                    $unreadCount = (int) $conversation['unread_count'];
                ?>

                <a href="chat.php?conversation_id=<?= (int) $conversation['conversation_id'] ?>"
                   class="block border-b border-gray-100 hover:bg-gray-50 transition">

                    <div class="p-5 flex items-center justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h2 class="font-semibold text-gray-900">
                                    <?= htmlspecialchars($conversation['villeDepart'] ?? 'Départ') ?>
                                    →
                                    <?= htmlspecialchars($conversation['villeArrivee'] ?? 'Arrivée') ?>
                                </h2>

                                <?php if ($unreadCount > 0): ?>
                                    <span class="bg-green-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        <?= $unreadCount ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <p class="text-sm text-gray-500">
                                <?= htmlspecialchars($roleText) ?> •
                                <?= htmlspecialchars($conversation['jourDepart'] ?? '') ?>
                                à
                                <?= htmlspecialchars($conversation['heureDepart'] ?? '') ?>
                            </p>

                            <p class="text-sm text-gray-700 mt-2 line-clamp-1">
                                <?= htmlspecialchars($lastMessage) ?>
                            </p>
                        </div>

                        <div class="text-right">
                            <span class="text-sm text-green-700 font-semibold">
                                Ouvrir
                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>