<?php

require_once __DIR__ . '/includes/whatsapp_helper.php';

echo "<h2>Test WhatsApp Message Texte avec nouveau token</h2>";

$message = "Bonjour 👋 Ceci est un test avec le nouveau token permanent EasyTravel.";

$result = sendWhatsAppTextMessage(WHATSAPP_TEST_TO, $message);

echo '<pre>';
print_r($result);
echo '</pre>';

logWhatsAppResult('test_nouveau_token_system_user', $result);