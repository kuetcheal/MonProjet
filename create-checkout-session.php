<?php
require 'vendor/autoload.php';

session_start();

\Stripe\Stripe::setApiKey('sk_test_51Phsz9DwEke97it4L0FgkWO1ns27esGI5Whl9sz4RuxnTEi6IVu1RJTwjvEC25kZyC8vBTIuVCWv0jTJv4DNaI9200yyi4dOSG'); // Remplacez par votre clé secrète Stripe

header('Content-Type: application/json');

$YOUR_DOMAIN = 'http://localhost/MonProjet';

$checkout_session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => 'Voyage Payment',
            ],
            'unit_amount' => $_SESSION["prix"] * 100, // Le montant doit être en cents
        ],
        'quantity' => 1,
    ]],
    'mode' => 'payment',
    'success_url' => $YOUR_DOMAIN . '/success.html',
    'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
]);

echo json_encode(['id' => $checkout_session->id]);