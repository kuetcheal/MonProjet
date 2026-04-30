<?php
session_start();
require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$conversationId = isset($_GET['conversation_id']) ? (int) $_GET['conversation_id'] : 0;

if ($conversationId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Conversation invalide.'
    ]);
    exit;
}

try {
    $check = $pdo->prepare("
        SELECT id
        FROM conversations
        WHERE id = :conversation_id
        AND statut = 'active'
        AND (
            client_id = :user_id_client
            OR chauffeur_id = :user_id_chauffeur
        )
        LIMIT 1
    ");

    $check->execute([
        ':conversation_id' => $conversationId,
        ':user_id_client' => $userId,
        ':user_id_chauffeur' => $userId
    ]);

    if (!$check->fetch()) {
        echo json_encode([
            'success' => false,
            'message' => 'Accès non autorisé.'
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT 
            id,
            sender_id,
            receiver_id,
            message,
            is_read,
            DATE_FORMAT(created_at, '%d/%m/%Y %H:%i') AS created_at
        FROM messages
        WHERE conversation_id = :conversation_id
        ORDER BY created_at ASC
    ");

    $stmt->execute([
        ':conversation_id' => $conversationId
    ]);

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($messages as &$message) {
        $message['is_me'] = ((int) $message['sender_id'] === $userId);
    }

    echo json_encode([
        'success' => true,
        'messages' => $messages
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}