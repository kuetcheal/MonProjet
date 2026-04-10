<?php

$apiKey = "AIzaSyDmFDvlGgo1AMnTIv57tiPTfaSfq_NMZwE";

$data = [
    "origin" => [
        "location" => [
            "latLng" => ["latitude" => 43.6119, "longitude" => 3.8772]
        ]
    ],
    "destination" => [
        "location" => [
            "latLng" => ["latitude" => 43.6047, "longitude" => 3.8796]
        ]
    ],
    "travelMode" => "DRIVE"
];

$ch = curl_init("https://routes.googleapis.com/directions/v2:computeRoutes");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "X-Goog-Api-Key: $apiKey",
    "X-Goog-FieldMask: routes.distanceMeters,routes.duration"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);

curl_close($ch);

echo "<pre>";
print_r(json_decode($response, true));