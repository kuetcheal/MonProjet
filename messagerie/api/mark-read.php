<?php
session_start();

require_once __DIR__ . '/../../config.php';

header('Content-Type: application/json');

$userId = (int)($_SESSION['user_id'] ?? $_SESSION['Id_compte'] ?? 0);

if ($userId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Utilisateur non connecté.'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!is_array($input)) {
    echo json_encode([
        'success' => false,
        'message' => 'Données JSON invalides.'
    ]);
    exit;
}

$conversationId = isset($input['conversation_id']) ? (int) $input['conversation_id'] : 0;

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

    if (!$check->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode([
            'success' => false,
            'message' => 'Accès refusé.'
        ]);
        exit;
    }

    $stmt = $pdo->prepare("
        UPDATE messages
        SET is_read = 1,
            read_at = NOW()
        WHERE conversation_id = :conversation_id
        AND receiver_id = :receiver_id
        AND is_read = 0
    ");

    $stmt->execute([
        ':conversation_id' => $conversationId,
        ':receiver_id' => $userId
    ]);

    echo json_encode([
        'success' => true
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur serveur : ' . $e->getMessage()
    ]);
    exit;
}