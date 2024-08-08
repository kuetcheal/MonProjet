<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "bd_stock";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Échec de la connexion à la base de données.");
}

if (isset($_GET['id_reservation'])) {
    $id_reservation = $_GET['id_reservation'];

    // Récupérer les détails de la réservation
    $query = "SELECT reservation.Numero_reservation, reservation.nom, reservation.prenom, reservation.telephone, 
                     voyage.villeDepart, voyage.villeArrivee, voyage.heureDepart, voyage.heureArrivee, voyage.jourDepart
              FROM reservation
              JOIN voyage ON reservation.idVoyage = voyage.idVoyage
              WHERE reservation.id_reservation = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_reservation);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $numero_reservation = $row['Numero_reservation'];
        $nom_passager = $row['nom'] . " " . $row['prenom'];
        $telephone = $row['telephone'];
        $trajet = $row['villeDepart'] . " - " . $row['villeArrivee'];
        $heureDepart = $row['heureDepart'];
        $heureArrivee = $row['heureArrivee'];
        $jourDepart = $row['jourDepart'];
    } else {
        echo "Aucune réservation trouvée.";
        exit;
    }
} else {
    echo "ID de réservation non spécifié.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ma Réservation</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .container {
        width: 95%;
        margin: 20px auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        color: #333;
    }

    .reservation-details {
        margin-bottom: 20px;
    }

    .reservation-details p {
        margin: 10px 0;
    }

    .details {
        background-color: #e9e9e9;
        padding: 10px;
        border-radius: 5px;
    }

    .details p {
        margin: 5px 0;
    }

    .highlight {
        color: #d9534f;
        font-weight: bold;
    }

    .price-summary {
        text-align: right;
        margin-top: 20px;
        font-size: 18px;
    }

    .total-price {
        font-size: 24px;
        color: #5cb85c;
    }

    .button-container {
        margin-top: 30px;
        text-align: center;
    }

    .btn {
        display: inline-block;
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        font-size: 16px;
        border-radius: 5px;
        margin: 0 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        border: none;
    }

    .btn-danger {
        background-color: #d9534f;
    }

    .btn:hover {
        background-color: #0056b3;
    }

    .btn-danger:hover {
        background-color: #c9302c;
    }

    .footer {
        margin-top: 20px;
        text-align: center;
        font-size: 14px;
        color: #777;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>Réservation Confirmée</h1>
        <div class="reservation-details">
            <p><strong>Numéro de réservation :</strong> <?php echo $numero_reservation; ?></p>
            <p><strong>Nom du passager :</strong> <?php echo $nom_passager; ?></p>
            <p><strong>Téléphone :</strong> <?php echo $telephone; ?></p>
            <p><strong>Date d'émission :</strong> <?php echo date("d M Y"); ?></p>
        </div>

        <div class="details">
            <h2>Itinéraire</h2>
            <p><strong>Trajet :</strong> <?php echo $trajet; ?></p>
            <p><?php echo date("d M Y", strtotime($jourDepart)) . ", " . $heureDepart . " - " . $heureArrivee; ?></p>
        </div>

        <div class="button-container">
            <button class="btn btn-danger" id="resertForm">Annuler ma réservation</button>
            <button class=" btn">Modifier ma réservation</button>
        </div>
        <div id="modalContainaire"></div>
        <div class="footer">
            <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!</p>
        </div>
    </div>
</body>


<script>
// Code pour ouvrir le modal d'annulation
// Code pour ouvrir le modal d'annulation
document.getElementById('resertForm').addEventListener('click', function(event) {
    event.preventDefault(); // Prévenir le comportement par défaut du bouton

    $.ajax({
        url: './Annulation_reservation.php',
        success: function(response) {
            document.getElementById('modalContainaire').innerHTML = response;
            var modal = document.querySelector('#modalContainaire .modalisation');
            var closeButton = document.querySelector('#modalContainaire .close-button');

            modal.style.display = "flex"; // Affichez la modal en utilisant flexbox

            closeButton.onclick = function() {
                modal.style.display = "none";
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        }
    });
});
</script>

</html>