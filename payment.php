<?php
session_start();

// Récupérer le prix total depuis l'URL
$prixTotal = isset($_GET['totalPrice']) ? $_GET['totalPrice'] : 0;

// Stocker le prix total dans la session pour utilisation ultérieure
$_SESSION['prix'] = $prixTotal;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <!-- <script src="https://js.stripe.com/v3/"></script> -->
</head>

<body>
    <?php
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
        $idvoyage = $_POST['idVoyage'];
        $_SESSION['idVoyage'] = $idvoyage;
        $requette = "select * From voyage where idVoyage='$idvoyage'";
        $resultat = $bdd->query($requette);
        while ($donne = $resultat->fetch()) {
            $price = $donne["prix"];
            $_SESSION["prix"] = $price;
        }
    } catch (Exception $e) {
        echo("echec de connexion");
    }

    function generateReservationNumber($length = 8) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    $reservationNumber = generateReservationNumber();
    ?>

    <main>
        <form method="post" action="finalisation.php">
            <h3>1) Informations du passager</h3>
            <div class="info-personnel">
                <div class="titre">
                    <p>1</p>
                    <h3 class="mode">Passagers</h3>
                </div>
                <div class="contenaire1">
                    <div class="fisrtName">
                        <label for="inputText">Prénom </label><br><br>
                        <input type="text" name="prenom" required>
                    </div>
                    <div class="name">
                        <label for="inputText">Nom </label><br><br>
                        <input type="text" name="nom" required>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="info-personnel">
                <div class="titre">
                    <p>2</p>
                    <h3 class="mode">Contact</h3>
                </div>
                <div class="contenaire1">
                    <div class="fisrtName">
                        <label for="inputText">Email</label><br>
                        <input type="text" name="email" required>
                    </div>
                    <div class="name">
                        <label for="inputText">Numéro téléphone (facultatif) </label><br>
                        <input type="text" name="telephone" placeholder="+237">
                    </div>
                    <input type="hidden" name="reservationNumber" value="<?php echo $reservationNumber; ?>">
                </div>
            </div><br>
            <h3>2) selectionnez votre chaise</h3><br>
            <div class="bus">
                <div class="driver-row">
                    <div class="driver-seat">Conducteur</div>
                    <div class="droite">
                        <div class="seat" id="seat1" onclick="selectSeat(1)">1</div>
                        <div class="seat" id="seat2" onclick="selectSeat(2)">2</div>
                    </div>
                </div>
                <div class="seats">
                    <!-- Generating 68 seats for the grid (2 front seats + 68 = 70 total seats) -->
                    <script>
                    let seatNumber = 3;
                    for (let row = 0; row < 13; row++) {
                        for (let col = 0; col < 6; col++) {
                            if (col === 3) {
                                document.write('<div class="spacer"></div>');
                            } else {
                                document.write(
                                    `<div class="seat" id="seat${seatNumber}" onclick="selectSeat(${seatNumber})">${seatNumber}</div>`
                                );
                                seatNumber++;
                            }
                        }
                    }
                    </script>
                </div>
            </div>
            <!-- Hidden input to store the selected seat number -->
            <input type="hidden" name="selectedSeat" id="selectedSeat" value="">

            <div class="titre">
                <h2>Total à payer: <span><?php echo($_SESSION["prix"]); ?> FCFA</span></h2>
            </div>
            <br>
            <h3>3) Mode de paiement</h3>
            <div> <input type="submit" name="submit" value="Payer à l'agence"> </div>
            <div id="stripe-button-container"><br>
                <button type="button" id="checkout-button">Payer avec Stripe</button>
            </div>

        </form>
    </main>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
    var stripe = Stripe(
        'pk_test_51Phsz9DwEke97it4yD8lj7SGiuTeL7yqscNb3S8ZMj8CvzGmOZ6V64Bqgk6uW6vpO7mF24SdHdf9lN6n07V9JV7v00p8mrRvpS'
    );

    var checkoutButton = document.getElementById('checkout-button');

    checkoutButton.addEventListener('click', function() {
        // Récupérer les données du formulaire
        var form = document.querySelector('form');
        var formData = new FormData(form);

        // Envoyer les données du formulaire à create-checkout-session.php
        fetch('create-checkout-session.php', {
                method: 'POST',
                body: formData // Inclure les données du formulaire dans la requête
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(session) {
                return stripe.redirectToCheckout({
                    sessionId: session.id
                });
            })
            .then(function(result) {
                if (result.error) {
                    alert(result.error.message);
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
    });

    // fonction de selection de la chaise du client
    function selectSeat(seatNumber) {
        const seats = document.getElementsByClassName('seat');
        for (let seat of seats) {
            seat.classList.remove('selected');
        }

        const selectedSeat = document.getElementById(`seat${seatNumber}`);
        selectedSeat.classList.add('selected');

        // Mettre à jour le champ caché avec le numéro du siège sélectionné
        document.getElementById('selectedSeat').value = seatNumber;

        console.log(`Seat ${seatNumber} selected`);
    }
    </script>



    <style>
    body {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .main {
        display: flex;
        flex-direction: column;
    }

    .info-personnel {
        background-color: aliceblue;
        width: 720px;
        height: 130px;
        display: flex;
        justify-content: space-between;
        padding: 15px;
        border: 1px solid black;
    }

    .contenaire1 {
        display: flex;
        justify-content: space-between;
        padding: 15px;
    }

    .name {
        margin-left: 90px;
    }

    h3 {
        margin-left: 9px;
    }

    input[type="text"] {
        padding: 5px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 5px;
        width: 200px;
    }

    input[type="submit"] {
        padding: 5px;
        font-size: 19px;
        border: 1px solid #ccc;
        color: white;
        border-radius: 5px;
        width: 750px;
        background-color: green;
        height: 60px;
        cursor: pointer;
    }

    #checkout-button {
        padding: 10px;
        font-size: 19px;
        border: 1px solid #ccc;
        color: white;
        border-radius: 5px;
        width: 750px;
        background-color: #6772e5;
        height: 60px;
        cursor: pointer;
        text-align: center;
    }


    .bus {
        display: grid;
        grid-template-columns: repeat(6, 50px);
        gap: 10px;
        background-color: #ccc;
        padding: 20px;
        border-radius: 10px;
        width: 370px;
    }

    .driver-row {
        grid-column: 1 / span 6;
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .driver-seat {
        background-color: orange;
        text-align: center;
        line-height: 50px;
        font-weight: bold;
        color: white;
        border-radius: 5px;
        width: 120px;
    }

    .seats {
        grid-column: 1 / span 6;
        display: grid;
        grid-template-columns: repeat(6, 50px);
        gap: 10px;
    }

    .seat {
        background-color: #007bff;
        color: white;
        text-align: center;
        line-height: 30px;
        border-radius: 5px;
        cursor: pointer;
        width: 40px;
        height: 35px;
    }

    .seat.selected {
        background-color: #28a745;
    }

    .seat:hover {
        background-color: #0056b3;
    }

    .spacer {
        width: 30px;
        height: 30px;
    }

    .droite {
        display: flex;
        margin-left: 110px;
        gap: 20px;
    }
    </style>
</body>

</html>