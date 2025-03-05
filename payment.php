<?php
session_start();

// Récupérer le prix total depuis l'URL
$prixTotal = isset($_GET['totalPrice']) ? $_GET['totalPrice'] : 0;

// Stocker le prix total dans la session pour utilisation ultérieure
$_SESSION['prix'] = $prixTotal;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex flex-col min-h-screen">
    <div class="flex-1 flex justify-center items-center">
        <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-3xl">
            <?php
            try {
                $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
                $idvoyage = $_POST['idVoyage'];
                $_SESSION['idVoyage'] = $idvoyage;
                $requette = "SELECT * FROM voyage WHERE idVoyage='$idvoyage'";
                $resultat = $bdd->query($requette);
                while ($donne = $resultat->fetch()) {
                    $price = $donne["prix"];
                    $_SESSION["prix"] = $price;
                }
            } catch (Exception $e) {
                echo "<p class='text-red-500 text-center'>Échec de connexion</p>";
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

            <h2 class="text-center text-2xl font-bold text-gray-800 mb-6">Informations du Passager</h2>

            <form method="post" action="finalisation.php">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold">Prénom <span>*</span></label>
                        <input type="text" name="prenom" required class="w-full p-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-gray-700 font-bold">Nom <span>*</span></label>
                        <input type="text" name="nom" required class="w-full p-2 border rounded">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-bold">Email <span>*</span></label>
                    <input type="email" name="email" required class="w-full p-2 border rounded">
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-bold">Téléphone <span>*</span></label>
                    <input type="text" name="telephone" placeholder="+237" class="w-full p-2 border rounded">
                    <input type="hidden" name="reservationNumber" value="<?= $reservationNumber; ?>">
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-6">Sélectionnez votre siège <span>*</span> </h3>
                <div class="grid grid-cols-6 gap-2 mt-4 bg-gray-200 p-4 rounded">
                    <div class="col-span-6 flex justify-center items-center bg-orange-500 text-white font-bold py-2 rounded">
                        Conducteur
                    </div>
                    <script>
                        let seatNumber = 1;
                        for (let row = 0; row < 12; row++) {
                            for (let col = 0; col < 6; col++) {
                                if (col === 2) {
                                    document.write('<div class="bg-gray-100"></div>'); // Espacement central
                                } else {
                                    document.write(
                                        `<button type="button" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-700" id="seat${seatNumber}" onclick="selectSeat(${seatNumber})">${seatNumber}</button>`
                                    );
                                    seatNumber++;
                                }
                            }
                        }
                    </script>
                </div>
                <input type="hidden" name="selectedSeat" id="selectedSeat">

                <h3 class="text-xl font-bold text-gray-800 mt-6">Total à payer: <span class="text-green-600"><?= $_SESSION["prix"]; ?> FCFA</span></h3>

                <div class="mt-6">
                    <button type="submit" name="submit" class="w-full bg-green-500 text-white py-3 rounded text-lg font-bold hover:bg-green-700">
                        Payer à l'agence
                    </button>
                </div>

                <div class="mt-4">
                    <button type="button" id="checkout-button" class="w-full bg-purple-500 text-white py-3 rounded text-lg font-bold hover:bg-purple-700">
                        Payer avec Stripe
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FOOTER FIXE EN BAS -->
    <footer class="bg-gray-700 text-white text-center py-4 w-full mt-auto">
        <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!  </p>
           <p> @By Alex KUETCHE, 2024 EasyTech Website. </p>
      
    </footer>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var stripe = Stripe('pk_test_51Phsz9DwEke97it4yD8lj7SGiuTeL7yqscNb3S8ZMj8CvzGmOZ6V64Bqgk6uW6vpO7mF24SdHdf9lN6n07V9JV7v00p8mrRvpS');

        var checkoutButton = document.getElementById('checkout-button');

        checkoutButton.addEventListener('click', function() {
            var form = document.querySelector('form');
            var formData = new FormData(form);

            fetch('create-checkout-session.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(session => stripe.redirectToCheckout({ sessionId: session.id }))
                .catch(error => console.error('Error:', error));
        });

        function selectSeat(seatNumber) {
            const seats = document.querySelectorAll('.bg-blue-500, .bg-green-500');
            seats.forEach(seat => seat.classList.replace('bg-green-500', 'bg-blue-500'));

            const selectedSeat = document.getElementById(`seat${seatNumber}`);
            selectedSeat.classList.replace('bg-blue-500', 'bg-green-500');

            document.getElementById('selectedSeat').value = seatNumber;
        }
    </script>
</body>

</html>
