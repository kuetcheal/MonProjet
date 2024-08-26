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

// Calculer 15% du prix total
$montant15 = $prixTotal * 0.15;

// Ajouter 15% au prix total si le bouton est cliqué
if (isset($_POST['ajouter15'])) {
    $prixTotal += $montant15;
}
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
        flex-direction: column;
        min-height: 100vh;
    }

    .container {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        width: 100%;
        flex-grow: 1;
        /* Permet à la container de prendre le reste de l'espace disponible */
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

    h3,
    h4 {
        color: #28a745;
    }

    .total-price {
        font-size: 1.2em;
        font-weight: bold;
        margin-top: 20px;
    }

    .add-button,
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
        border: none;
        cursor: pointer;
    }

    .add-button:hover,
    .continue-button:hover {
        background-color: #0056b3;
    }

    footer {
        background-color: #6c757d;
        color: white;
        padding: 20px 0;
        text-align: center;
        width: 100%;
        position: relative;
        bottom: 0;
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

        <!-- Montant calculé pour 15% -->
        <h5>Réserver avec la possibilité d'annuler ou de modifier pour seulement: <?= htmlspecialchars($montant15) ?>
            FCFA</h5>

        <!-- Formulaire pour ajouter 15% -->
        <form method="post">
            <button type="submit" name="ajouter15" class="add-button">Ajouter</button>
        </form>

        <!-- Prix total avec ou sans les 15% ajoutés -->
        <h3 class="total-price">Prix total: <?= htmlspecialchars($prixTotal) ?> FCFA</h3>

        <!-- Bouton pour continuer vers la page de paiement -->
        <a href="payment.php?totalPrice=<?= htmlspecialchars($prixTotal) ?>" class="continue-button">Continuer vers le
            paiement</a>

    </div>

    <footer>
        <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>
</body>

</html>