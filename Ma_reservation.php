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
                 voyage.villeDepart, voyage.villeArrivee, voyage.heureDepart, voyage.heureArrivee, voyage.jourDepart, reservation.prix_reservation
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
        $heureDepart = $row['heureDepart'];
        $heureArrivee = $row['heureArrivee'];
        $prix = $row['prix_reservation'];
        $jourDepart = $row['jourDepart'];
        $villeDepart = $row['villeDepart'];
        $villeArrivee = $row['villeArrivee'];

        // Calcul du remboursement
        $perte = $prix * 0.3;
        $remboursement = $prix - $perte;
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
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
<?php include 'includes/header.php'; ?>

    <div class="container mx-auto p-8 bg-white shadow-lg rounded-lg w-3/4 mt-8">
        <h1 class="text-2xl font-bold text-center text-gray-700 mb-6">Réservation Confirmée</h1>
        <div class="border p-4 rounded-lg shadow-md">
            <p><strong>Numéro de réservation :</strong> <?php echo $numero_reservation; ?></p>
            <p><strong>Nom du passager :</strong> <?php echo $nom_passager; ?></p>
            <p><strong>Téléphone :</strong> <?php echo $telephone; ?></p>
            <p><strong>Date d'émission :</strong> <?php echo date("d M Y"); ?></p>
        </div>

        <div class="mt-6 p-4 bg-blue-100 border-l-4 border-blue-500">
            <h2 class="text-xl font-semibold">Itinéraire</h2>
            <p><strong>Trajet :</strong> <?php echo "$villeDepart ➔ $villeArrivee"; ?></p>
            <p><?php echo date("d M Y", strtotime($jourDepart)) . " | " . $heureDepart . " ➔ " . $heureArrivee; ?></p>
        </div>

        <div class="flex justify-center mt-6 space-x-4">
            <button class="bg-red-500 text-white px-6 py-2 rounded shadow-md hover:bg-red-600 transition"
                onclick="openPopup()">Annuler ma réservation</button>

            <button class="bg-blue-500 text-white px-6 py-2 rounded shadow-md hover:bg-blue-600 transition">
                Modifier ma réservation
            </button>
        </div>
    </div>

    <!-- Popup d'annulation -->
    <div id="reservationForm" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 relative">
            <!-- Bouton de fermeture -->
            <button class="absolute top-2 right-2 text-gray-600 hover:text-gray-800 text-xl" onclick="closePopup()">
                &times;
            </button>

            <div class="bg-green-600 text-white text-center py-3 rounded-t-lg">
                <h3 class="text-lg font-bold"><?php echo date("d M Y", strtotime($jourDepart)) . " • $villeDepart ➔ $villeArrivee"; ?></h3>
            </div>
            <div class="p-4 text-center">
                <p class="text-xl font-bold text-gray-800"><?php echo date("d M Y"); ?></p>

                <div class="flex justify-between items-center mt-4">
                    <div>
                        <i class="fas fa-map-marker-alt text-gray-500"></i> <?php echo $villeDepart; ?>
                        <br><i class="fas fa-clock text-gray-500"></i> <?php echo $heureDepart; ?>

                    </div>
                    <div class="h-12 border-l-2 border-dashed mx-4"></div>
                    <div>
                        <i class="fas fa-map-marker-alt text-gray-500"></i> <?php echo $villeArrivee; ?>
                        <br> <i class="fas fa-clock text-gray-500"></i> <?php echo $heureArrivee; ?>

                    </div>
                </div>

                <div class="bg-gray-100 p-4 mt-4 rounded">
                    <p><strong>Total pour 1 passager :</strong> <?php echo $prix; ?> FCFA</p>
                    <p>Frais d'annulation : 30%</p>
                    <p><strong>Montant remboursé :</strong> <?php echo number_format($remboursement, 2); ?> FCFA</p>
                </div>

                <button class="w-full mt-4 bg-red-500 text-white py-2 rounded shadow-md hover:bg-red-600 transition"
                    onclick="supprimerReservation(<?php echo $id_reservation; ?>)">Annuler le billet</button>
            </div>
        </div>
    </div>

    <footer class="mt-auto bg-gray-700 text-white text-center py-4">
        <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!</p>
    </footer>

    <script>
        function openPopup() {
            document.getElementById('reservationForm').classList.remove('hidden');
        }

        function closePopup() {
            document.getElementById('reservationForm').classList.add('hidden');
        }



        function supprimerReservation(id) {
            if (confirm("Voulez-vous vraiment annuler cette réservation ?")) {
                fetch("./Reservation/supprimer_reservation.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            id_reservation: id
                        }) // Envoyer en JSON
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "success") {
                            alert(data.message);
                            window.location.href = "Accueil.php"; // Rediriger après suppression
                        } else {
                            alert("Erreur : " + data.message);
                        }
                    })
                    .catch(error => console.error("Erreur :", error));

            }
        }
    </script>
    </script>
</body>

</html>