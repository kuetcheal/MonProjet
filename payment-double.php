<?php
session_start();

// Vérifie si le prix total est passé dans l'URL, sinon utilise celui en session
if (isset($_GET['totalPrice'])) {
    $totalPrice = $_GET['totalPrice'];
    $_SESSION["prix"] = $totalPrice;
} else {
    $totalPrice = $_SESSION["prix"] ?? 0; // Valeur par défaut à 0 si rien n'est défini
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
                        <input type="text" name="prenom">
                    </div>
                    <div class="name">
                        <label for="inputText">Nom </label><br><br>
                        <input type="text" name="nom">
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
                        <input type="text" name="email">
                    </div>
                    <div class="name">
                        <label for="inputText">Numéro téléphone (facultatif) </label><br>
                        <input type="text" name="telephone" placeholder="+237">
                    </div>
                    <input type="hidden" name="reservationNumber" value="<?php echo $reservationNumber; ?>">
                </div>
            </div>
            <div class="titre">
                <h2>Total à payer: <span><?php echo htmlspecialchars($totalPrice); ?> FCFA</span></h2>
            </div>
            <br>
            <h3>2) Mode de paiement</h3>
            <div> <input type="submit" name="submit" value="Payer à l'agence"> </div>
        </form>
        <br>
        <div id="stripe-button-container">
            <button id="checkout-button">Payer avec Stripe</button>
        </div>
    </main>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
    var stripe = Stripe(
        'pk_test_51Phsz9DwEke97it4yD8lj7SGiuTeL7yqscNb3S8ZMj8CvzGmOZ6V64Bqgk6uW6vpO7mF24SdHdf9lN6n07V9JV7v00p8mrRvpS'
    );

    var checkoutButton = document.getElementById('checkout-button');

    checkoutButton.addEventListener('click', function() {
        fetch('create-checkout-session.php', {
                method: 'POST',
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
    </style>
</body>

</html>