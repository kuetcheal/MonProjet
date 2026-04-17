<?php

require_once __DIR__ . '/includes/whatsapp_helper.php';

echo "<h2>Test WhatsApp Message Texte</h2>";

$message = "Bonjour 👋 Ceci est un message test envoyé depuis mon projet PHP EasyTravel.";

$result = sendWhatsAppTextMessage(WHATSAPP_TEST_TO, $message);

echo '<pre>';
print_r($result);
echo '</pre>';

logWhatsAppResult('test_message_texte', $result);