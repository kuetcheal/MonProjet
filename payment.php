<?php
session_start();

$prixTotal = isset($_GET['totalPrice']) ? (float) $_GET['totalPrice'] : 0;
$idvoyage = $_POST['idVoyage'] ?? $_SESSION['idVoyage'] ?? null;

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($idvoyage) {
        $_SESSION['idVoyage'] = $idvoyage;

        $requette = $bdd->prepare("SELECT * FROM voyage WHERE idVoyage = :idVoyage");
        $requette->execute(['idVoyage' => $idvoyage]);
        $donne = $requette->fetch(PDO::FETCH_ASSOC);

        if ($donne) {
            $prixTotal = (float) $donne['prix'];
        }
    }

    $_SESSION['prix'] = $prixTotal;
} catch (Exception $e) {
    $dbError = "Échec de connexion à la base de données.";
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

ob_start();
?>

<div class="pt-8 pb-12">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white shadow-lg rounded-2xl p-6 md:p-8">
            <?php if (!empty($dbError)): ?>
                <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($dbError) ?></p>
            <?php endif; ?>

            <h2 class="text-center text-2xl md:text-3xl font-bold text-gray-800 mb-8">
                Informations du Passager
            </h2>

            <form method="post" action="finalisation.php">
                <?php if ($idvoyage): ?>
                    <input type="hidden" name="idVoyage" value="<?= htmlspecialchars($idvoyage) ?>">
                <?php endif; ?>

                <input type="hidden" name="reservationNumber" value="<?= htmlspecialchars($reservationNumber) ?>">
                <input type="hidden" name="prixTotal" value="<?= htmlspecialchars($prixTotal) ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="prenom" placeholder="herve" required class="w-full p-3 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom" placeholder="Dupuis"  required class="w-full p-3 border rounded-lg">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-bold mb-2">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" placeholder="example@gmail.com" required class="w-full p-3 border rounded-lg">
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-bold mb-2">
                        Téléphone <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="telephone" placeholder="+237 6 55 19 62 54" required class="w-full p-3 border rounded-lg">
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-8 mb-4">
                    Sélectionnez votre siège <span class="text-red-500">*</span>
                </h3>

                <div class="grid grid-cols-6 gap-2 bg-gray-100 p-4 rounded-xl">
                    <div class="col-span-6 flex justify-center items-center bg-orange-500 text-white font-bold py-3 rounded-lg">
                        Conducteur
                    </div>

                    <?php
                    $seatNumber = 1;
                    for ($row = 0; $row < 12; $row++) {
                        for ($col = 0; $col < 6; $col++) {
                            if ($col === 2) {
                                echo '<div></div>';
                            } else {
                                echo '<button type="button"
                                    class="seat-btn bg-blue-500 text-white p-2 rounded hover:bg-blue-700 transition"
                                    id="seat' . $seatNumber . '"
                                    onclick="selectSeat(' . $seatNumber . ')">'
                                    . $seatNumber .
                                '</button>';
                                $seatNumber++;
                            }
                        }
                    }
                    ?>
                </div>

                <input type="hidden" name="selectedSeat" id="selectedSeat">

                <h3 class="text-xl font-bold text-gray-800 mt-8">
                    Total à payer :
                    <span class="text-green-600"><?= htmlspecialchars(number_format($prixTotal, 0, ',', ' ')) ?> FCFA</span>
                </h3>

                <div class="mt-6">
                    <button type="submit" name="submit" class="w-full bg-green-500 text-white py-3 rounded-lg text-lg font-bold hover:bg-green-700 transition">
                        Payer à l'agence
                    </button>
                </div>

                <div class="mt-4">
                    <button type="button" id="checkout-button" class="w-full bg-purple-500 text-white py-3 rounded-lg text-lg font-bold hover:bg-purple-700 transition">
                        Payer avec Stripe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('pk_test_51Phsz9DwEke97it4yD8lj7SGiuTeL7yqscNb3S8ZMj8CvzGmOZ6V64Bqgk6uW6vpO7mF24SdHdf9lN6n07V9JV7v00p8mrRvpS');
    const checkoutButton = document.getElementById('checkout-button');

    if (checkoutButton) {
        checkoutButton.addEventListener('click', function () {
            const form = document.querySelector('form');
            const formData = new FormData(form);

            fetch('create-checkout-session.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(session => stripe.redirectToCheckout({ sessionId: session.id }))
            .catch(error => console.error('Error:', error));
        });
    }

    function selectSeat(seatNumber) {
        const seats = document.querySelectorAll('.seat-btn');

        seats.forEach(seat => {
            seat.classList.remove('bg-green-500');
            seat.classList.add('bg-blue-500');
        });

        const selectedSeat = document.getElementById(`seat${seatNumber}`);
        if (selectedSeat) {
            selectedSeat.classList.remove('bg-blue-500');
            selectedSeat.classList.add('bg-green-500');
        }

        document.getElementById('selectedSeat').value = seatNumber;
    }
</script>

<?php
$content = ob_get_clean();
$title = "Paiement";
include __DIR__ . '/layouts/default.php';
?>