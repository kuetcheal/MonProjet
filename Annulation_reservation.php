<?php
// session_start();
// $host = "localhost";
// $user = "root";
// $password = "";
// $database = "bd_stock";

// $conn = mysqli_connect($host, $user, $password, $database);
// if (!$conn) {
//     die("Échec de la connexion à la base de données.");
// }

// if (isset($_GET['id_reservation'])) {
//     $id_reservation = $_GET['id_reservation'];

//     // Récupérer les détails de la réservation
//     $query = "SELECT reservation.Numero_reservation, reservation.nom, reservation.prenom, reservation.telephone, 
//                      voyage.villeDepart, voyage.villeArrivee, voyage.heureDepart, voyage.heureArrivee, voyage.jourDepart
//               FROM reservation
//               JOIN voyage ON reservation.idVoyage = voyage.idVoyage
//               WHERE reservation.id_reservation = ?";
//     $stmt = mysqli_prepare($conn, $query);
//     mysqli_stmt_bind_param($stmt, "i", $id_reservation);
//     mysqli_stmt_execute($stmt);
//     $result = mysqli_stmt_get_result($stmt);

//     if ($row = mysqli_fetch_assoc($result)) {
//         $numero_reservation = $row['Numero_reservation'];
//         $nom_passager = $row['nom'] . " " . $row['prenom'];
//         $telephone = $row['telephone'];
//         $trajet = $row['villeDepart'] . " - " . $row['villeArrivee'];
//         $heureDepart = $row['heureDepart'];
//         $heureArrivee = $row['heureArrivee'];
//         $jourDepart = $row['jourDepart'];
//     } else {
//         echo "Aucune réservation trouvée.";
//         exit;
//     }
// } else {
//     echo "ID de réservation non spécifié.";
//     exit;
// }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Document</title>
</head>

<body>
    <!-- Modal Container -->
    <div id="reservationForm" class="modalisation">
        <div class="modalité-content">
            <div class="ticket">
                <div class="ticket-header">
                    <h5>Jeu 5 Sept • Lille ➔ Bruxelles</h5>
                </div>
                <div class="ticket-body">
                    <h5>Jeudi 5 Septembre</h5>
                    <div class="info">
                        <div class="journey-line">
                            <div class="start-dot"></div>
                            <div class="line"></div>
                            <div class="end-dot"></div>
                        </div> <br>
                        <div class="arriver">
                            <i class="fas fa-clock"></i> 18h10 <br>
                            <i class="fas fa-map-marker-alt"></i> Lille, Lille-Europe
                        </div> <br>
                        <div class="arriver">
                            <i class="fas fa-clock"></i> 19h40 <br>
                            <i class="fas fa-map-marker-alt"></i> Bruxelles, Gare Bruxelles-Midi
                        </div>

                    </div>
                </div>
                <div class="ticket-footer">
                    <h6>Total pour 1 passager 6,99 €</h6>
                    <p>Frais d'annulation BlaBlaBus 1,80 €</p>
                    <p>Frais d'annulation Kombo 0,00 €</p>
                    <h6>Montant remboursé 5,19 €</h6>
                </div>
                <button class="cancel-button">Annuler le billet</button>
            </div>

        </div>
    </div>

    <style>
    .modalisation {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.7);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .modalité-content {
        background-color: #fefefe;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #888;
        width: 40%;
        max-width: 500px;
        margin: 0 auto;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        animation: fadeIn 0.3s ease-in-out;
    }



    .ticket {
        background-color: #f0f9f0;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0.2, 0.2), 0 1px 3px rgba(0, 0, 0, 0.08);

        width: 400px;
        padding: 20px;
        color: #333;
    }

    .ticket-header {
        background-color: #28a745;
        color: white;
        border-radius: 10px 10px 0 0;
        padding: 10px;
        text-align: center;
    }

    .ticket-body {
        padding: 20px;
        background-color: #f0f9f0;
    }

    .ticket-body h5 {
        margin-bottom: 15px;
    }

    .ticket-body .info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .ticket-body .info i {
        margin-right: 5px;
    }

    .ticket-footer {
        background-color: #e6f3e6;
        padding: 15px;
        border-radius: 0 0 10px 10px;
        text-align: center;
    }

    .ticket-footer h6 {
        margin: 0;
    }

    .cancel-button {
        background-color: #dc3545;
        color: white;
        border: none;
        padding: 10px;
        width: 100%;
        border-radius: 5px;
        margin-top: 20px;
    }

    .journey-line {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .start-dot,
    .end-dot {
        width: 15px;
        height: 15px;
        background-color: grey;
        border-radius: 50%;
    }

    .line {
        width: 2px;
        height: 100px;
        background-image: repeating-linear-gradient(to bottom,
                grey 0%,
                grey 20%,
                transparent 20%,
                transparent 40%);
        background-size: 2px 20px;
    }

    .infos {
        display: flex;
        gap: 20px;
    }
    </style>

</body>

</html>