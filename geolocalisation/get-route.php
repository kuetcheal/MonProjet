<?php
header('Content-Type: application/json; charset=utf-8');

if (
    !isset($_GET['originLat'], $_GET['originLng'], $_GET['destLat'], $_GET['destLng'])
) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Paramètres manquants.'
    ]);
    exit;
}

$originLat = (float) $_GET['originLat'];
$originLng = (float) $_GET['originLng'];
$destLat   = (float) $_GET['destLat'];
$destLng   = (float) $_GET['destLng'];

/**
 * Mets ici ta clé BACKEND, pas la clé front.
 * Idéalement plus tard : stocke-la dans un fichier de config non versionné.
 */
$apiKey = 'AIzaSyDmFDvlGgo1AMnTIv57tiPTfaSfq_NMZwE';

$url = 'https://routes.googleapis.com/directions/v2:computeRoutes';

$payload = [
    'origin' => [
        'location' => [
            'latLng' => [
                'latitude' => $originLat,
                'longitude' => $originLng
            ]
        ]
    ],
    'destination' => [
        'location' => [
            'latLng' => [
                'latitude' => $destLat,
                'longitude' => $destLng
            ]
        ]
    ],
    'travelMode' => 'DRIVE',
    'routingPreference' => 'TRAFFIC_AWARE',
    'computeAlternativeRoutes' => false,
    'languageCode' => 'fr-FR',
    'units' => 'METRIC'
];

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'X-Goog-Api-Key: ' . $apiKey,
        'X-Goog-FieldMask: routes.distanceMeters,routes.duration,routes.polyline.encodedPolyline'
    ],
    CURLOPT_POSTFIELDS => json_encode($payload),
]);

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($curlError) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur cURL : ' . $curlError
    ]);
    exit;
}

$data = json_decode($response, true);

if ($httpCode < 200 || $httpCode >= 300 || empty($data['routes'][0])) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur Routes API',
        'httpCode' => $httpCode,
        'googleResponse' => $data
    ]);
    exit;
}

$route = $data['routes'][0];

echo json_encode([
    'success' => true,
    'distanceMeters' => $route['distanceMeters'] ?? null,
    'duration' => $route['duration'] ?? null,
    'encodedPolyline' => $route['polyline']['encodedPolyline'] ?? null
], JSON_UNESCAPED_UNICODE);