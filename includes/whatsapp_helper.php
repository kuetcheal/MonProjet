<?php

require_once __DIR__ . '/../config.php';

/**
 * Nettoie un numéro pour WhatsApp API.
 * Exemple :
 * +237 6 55 19 62 54 => 237655196254
 */
function normalizeWhatsAppNumber(string $number): string
{
    return preg_replace('/\D+/', '', trim($number));
}

/**
 * Exécute une requête POST vers l'API WhatsApp Cloud.
 */
function sendWhatsAppRequest(array $payload): array
{
    if (empty(WHATSAPP_TOKEN) || empty(WHATSAPP_PHONE_NUMBER_ID)) {
        return [
            'success' => false,
            'http_code' => 0,
            'error' => 'Configuration WhatsApp manquante.',
            'response' => null
        ];
    }

    $url = 'https://graph.facebook.com/' . WHATSAPP_API_VERSION . '/' . WHATSAPP_PHONE_NUMBER_ID . '/messages';

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . WHATSAPP_TOKEN,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($curlError) {
        return [
            'success' => false,
            'http_code' => $httpCode,
            'error' => $curlError,
            'response' => null
        ];
    }

    $decodedResponse = json_decode($response, true);

    return [
        'success' => $httpCode >= 200 && $httpCode < 300,
        'http_code' => $httpCode,
        'error' => null,
        'response' => $decodedResponse
    ];
}

/**
 * Envoie le template hello_world de test.
 */
function sendWhatsAppHelloWorld(string $to): array
{
    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => normalizeWhatsAppNumber($to),
        'type' => 'template',
        'template' => [
            'name' => 'hello_world',
            'language' => [
                'code' => 'en_US'
            ]
        ]
    ];

    return sendWhatsAppRequest($payload);
}

/**
 * Envoie un message texte simple.
 * Attention : en production, hors fenêtre de conversation,
 * WhatsApp exige souvent un template approuvé.
 */
function sendWhatsAppTextMessage(string $to, string $message): array
{
    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => normalizeWhatsAppNumber($to),
        'type' => 'text',
        'text' => [
            'preview_url' => false,
            'body' => $message
        ]
    ];

    return sendWhatsAppRequest($payload);
}

/**
 * Envoie un template personnalisé.
 */
function sendWhatsAppTemplateMessage(string $to, string $templateName, string $languageCode = 'fr', array $components = []): array
{
    $template = [
        'name' => $templateName,
        'language' => [
            'code' => $languageCode
        ]
    ];

    if (!empty($components)) {
        $template['components'] = $components;
    }

    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => normalizeWhatsAppNumber($to),
        'type' => 'template',
        'template' => $template
    ];

    return sendWhatsAppRequest($payload);
}

/**
 * Envoie un document PDF accessible via une URL publique.
 */
function sendWhatsAppDocumentMessage(string $to, string $documentUrl, string $filename = 'billet.pdf', string $caption = ''): array
{
    $payload = [
        'messaging_product' => 'whatsapp',
        'to' => normalizeWhatsAppNumber($to),
        'type' => 'document',
        'document' => [
            'link' => $documentUrl,
            'filename' => $filename
        ]
    ];

    if (!empty($caption)) {
        $payload['document']['caption'] = $caption;
    }

    return sendWhatsAppRequest($payload);
}

/**
 * Petit logger simple.
 */
function logWhatsAppResult(string $context, array $result): void
{
    $logDir = __DIR__ . '/../logs';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0777, true);
    }

    $line = '[' . date('Y-m-d H:i:s') . '] ' . $context . ' - ' . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;

    file_put_contents($logDir . '/whatsapp.log', $line, FILE_APPEND);
}