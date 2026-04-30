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

$input = json_decode(file_get_contents('php://input'), true);

$conversationId = isset($input['conversation_id']) ? (int) $input['conversation_id'] : 0;
$receiverId = isset($input['receiver_id']) ? (int) $input['receiver_id'] : 0;
$message = isset($input['message']) ? trim($input['message']) : '';

if ($conversationId <= 0 || $receiverId <= 0 || $message === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Données invalides.'
    ]);
    exit;
}

if (mb_strlen($message) > 1000) {
    echo json_encode([
        'success' => false,
        'message' => 'Le message est trop long.'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM conversations
        WHERE id = :conversation_id
        AND statut = 'active'
        AND (
            client_id = :user_id_client
            OR chauffeur_id = :user_id_chauffeur
        )
        LIMIT 1
    ");

    $stmt->execute([
        ':conversation_id' => $conversationId,
        ':user_id_client' => $userId,
        ':user_id_chauffeur' => $userId
    ]);

    $conversation = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$conversation) {
        echo json_encode([
            'success' => false,
            'message' => 'Conversation introuvable ou accès refusé.'
        ]);
        exit;
    }

    $validReceiverId = ((int) $conversation['client_id'] === $userId)
        ? (int) $conversation['chauffeur_id']
        : (int) $conversation['client_id'];

    if ($receiverId !== $validReceiverId) {
        echo json_encode([
            'success' => false,
            'message' => 'Destinataire invalide.'
        ]);
        exit;
    }

    $insert = $pdo->prepare("
        INSERT INTO messages (
            conversation_id,
            sender_id,
            receiver_id,
            message,
            type_message,
            is_read,
            created_at
        ) VALUES (
            :conversation_id,
            :sender_id,
            :receiver_id,
            :message,
            'texte',
            0,
            NOW()
        )
    ");

    $insert->execute([
        ':conversation_id' => $conversationId,
        ':sender_id' => $userId,
        ':receiver_id' => $receiverId,
        ':message' => $message
    ]);

    $updateConversation = $pdo->prepare("
        UPDATE conversations
        SET updated_at = NOW()
        WHERE id = :conversation_id
    ");

    $updateConversation->execute([
        ':conversation_id' => $conversationId
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Message envoyé.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}