<?php
session_start();

require_once __DIR__ . '/../config.php';

$userId = (int)($_SESSION['user_id'] ?? $_SESSION['Id_compte'] ?? 0);

if ($userId <= 0) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../connexion.php');
    exit;
}

$conversationId = isset($_GET['conversation_id']) ? (int) $_GET['conversation_id'] : 0;

if ($conversationId <= 0) {
    die('Conversation invalide.');
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.*,
            v.villeDepart,
            v.villeArrivee,
            v.jourDepart,
            v.heureDepart
        FROM conversations c
        LEFT JOIN voyage v ON v.idVoyage = c.voyage_id
        WHERE c.id = :conversation_id
        AND c.statut = 'active'
        AND (
            c.client_id = :user_id_client
            OR c.chauffeur_id = :user_id_chauffeur
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
        die('Accès non autorisé à cette conversation.');
    }

    $receiverId = ((int) $conversation['client_id'] === $userId)
        ? (int) $conversation['chauffeur_id']
        : (int) $conversation['client_id'];

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Discussion</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen">

<?php
// Tu peux garder ou retirer selon ton design
// include __DIR__ . '/../includes/header.php';
?>

<div class="max-w-4xl mx-auto px-4 py-6">

    <div class="mb-4">
        <a href="conversations.php" class="text-green-700 font-semibold hover:underline">
            ← Retour aux conversations
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

        <div class="p-5 border-b border-gray-200 bg-green-700 text-white">
            <h1 class="text-xl font-bold">
                <?= htmlspecialchars($conversation['villeDepart'] ?? 'Départ') ?>
                →
                <?= htmlspecialchars($conversation['villeArrivee'] ?? 'Arrivée') ?>
            </h1>

            <p class="text-sm text-green-100 mt-1">
                Départ :
                <?= htmlspecialchars($conversation['jourDepart'] ?? '') ?>
                à
                <?= htmlspecialchars(substr($conversation['heureDepart'] ?? '', 0, 5)) ?>
            </p>
        </div>

        <div id="messagesBox" class="h-[500px] overflow-y-auto p-5 bg-gray-50 space-y-3">
            <div class="text-center text-gray-400 text-sm">
                Chargement des messages...
            </div>
        </div>

        <form id="messageForm" class="p-4 border-t border-gray-200 bg-white flex gap-3">
            <input type="hidden" id="conversationId" value="<?= (int) $conversationId ?>">
            <input type="hidden" id="receiverId" value="<?= (int) $receiverId ?>">

            <input
                type="text"
                id="messageInput"
                class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-600"
                placeholder="Écrire un message..."
                maxlength="1000"
                autocomplete="off"
            >

            <button
                type="submit"
                class="bg-green-700 hover:bg-green-800 text-white font-semibold px-6 py-3 rounded-xl transition"
            >
                Envoyer
            </button>
        </form>

    </div>
</div>

<script>
const conversationId = document.getElementById('conversationId').value;
const receiverId = document.getElementById('receiverId').value;
const messagesBox = document.getElementById('messagesBox');
const messageForm = document.getElementById('messageForm');
const messageInput = document.getElementById('messageInput');

let lastMessagesHtml = '';

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

async function loadMessages() {
    try {
        const response = await fetch(`api/fetch-messages.php?conversation_id=${conversationId}`);
        const data = await response.json();

        if (!data.success) {
            messagesBox.innerHTML = `<p class="text-red-500 text-center">${escapeHtml(data.message)}</p>`;
            return;
        }

        let html = '';

        if (data.messages.length === 0) {
            html = '<p class="text-center text-gray-400 text-sm">Aucun message pour le moment. Commencez la discussion.</p>';
        } else {
            data.messages.forEach(msg => {
                const isMe = msg.is_me;

                html += `
                    <div class="flex ${isMe ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-[75%] rounded-2xl px-4 py-3 ${
                            isMe
                            ? 'bg-green-700 text-white rounded-br-none'
                            : 'bg-white border border-gray-200 text-gray-900 rounded-bl-none'
                        }">
                            <p class="text-sm whitespace-pre-wrap">${escapeHtml(msg.message)}</p>
                            <p class="text-[11px] mt-1 ${isMe ? 'text-green-100' : 'text-gray-400'}">
                                ${escapeHtml(msg.created_at)}
                            </p>
                        </div>
                    </div>
                `;
            });
        }

        if (html !== lastMessagesHtml) {
            messagesBox.innerHTML = html;
            messagesBox.scrollTop = messagesBox.scrollHeight;
            lastMessagesHtml = html;
        }

        await fetch('api/mark-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: conversationId
            })
        });

    } catch (error) {
        console.error(error);
    }
}

messageForm.addEventListener('submit', async function(e) {
    e.preventDefault();

    const message = messageInput.value.trim();

    if (message === '') {
        return;
    }

    messageInput.disabled = true;

    try {
        const response = await fetch('api/send-message.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                receiver_id: receiverId,
                message: message
            })
        });

        const data = await response.json();

        if (data.success) {
            messageInput.value = '';
            await loadMessages();
        } else {
            alert(data.message);
        }

    } catch (error) {
        alert('Erreur lors de l’envoi du message.');
        console.error(error);
    }

    messageInput.disabled = false;
    messageInput.focus();
});

loadMessages();
setInterval(loadMessages, 3000);
</script>

<?php
// include __DIR__ . '/../includes/footer.php';
?>

</body>
</html>