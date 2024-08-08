<?php
$priceAller = $_GET['priceAller'];
$priceRetour = $_GET['priceRetour'];
$departAller = $_GET['departAller'];
$arriveAller = $_GET['arriveAller'];
$timeAller = $_GET['timeAller'];

$departRetour = $_GET['departRetour'] ?? 'N/A';
$arriveRetour = $_GET['arriveRetour'] ?? 'N/A';
$timeRetour = $_GET['timeRetour'] ?? 'N/A';

$prixTotal = $priceAller + $priceRetour;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résumé de votre voyage</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f4f4f4;
        color: #333;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        width: 100%;
    }

    h2 {
        color: #333;
    }

    ul {
        list-style-type: none;
        padding: 0;
    }

    ul li {
        margin: 5px 0;
    }

    h3 {
        color: #28a745;
    }

    .total-price {
        font-size: 1.2em;
        font-weight: bold;
        margin-top: 20px;
    }

    .continue-button {
        display: block;
        width: 100%;
        padding: 10px;
        background-color: #007bff;
        color: white;
        text-align: center;
        border-radius: 5px;
        text-decoration: none;
        font-size: 1em;
        font-weight: bold;
        margin-top: 20px;
        transition: background-color 0.3s ease;
    }

    .continue-button:hover {
        background-color: #0056b3;
    }


    p {
        font-weight: bold;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>Résumé de votre voyage</h2>
        <p>Trajet aller:</p>
        <ul>
            <li>De: <?= htmlspecialchars($departAller) ?> à <?= htmlspecialchars($arriveAller) ?></li>
            <li>Heure: <?= htmlspecialchars($timeAller) ?></li>
            <li>Prix: <?= htmlspecialchars($priceAller) ?> FCFA</li>
        </ul>

        <p>Trajet retour:</p>
        <ul>
            <li>De: <?= htmlspecialchars($departRetour) ?> à <?= htmlspecialchars($arriveRetour) ?></li>
            <li>Heure: <?= htmlspecialchars($timeRetour) ?></li>
            <li>Prix: <?= htmlspecialchars($priceRetour) ?> FCFA</li>
        </ul>

        <h3 class="total-price">Prix total: <?= htmlspecialchars($prixTotal) ?> FCFA</h3>

        <!-- Bouton pour continuer vers la page de paiement -->
        <a href="payment-double.php?totalPrice=<?= htmlspecialchars($prixTotal) ?>" class="continue-button">Continuer
            vers le
            paiement</a>
    </div>
</body>

</html>