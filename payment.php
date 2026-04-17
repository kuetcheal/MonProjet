<?php
session_start();
require_once __DIR__ . '/includes/seat_helpers.php';

$prixTotal = isset($_GET['totalPrice']) ? (float) $_GET['totalPrice'] : 0;
$idVoyage = $_POST['idVoyage'] ?? $_GET['idVoyage'] ?? $_SESSION['idVoyage'] ?? null;
$flexOption = $_POST['flexOption'] ?? $_GET['flexOption'] ?? null;

$dbError = '';
$depart = '';
$arrivee = '';
$dateVoyage = '';
$nombrePlaces = 0;
$reservedSeats = [];

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($idVoyage) {
        $_SESSION['idVoyage'] = $idVoyage;

        $voyage = getVoyageById($bdd, (int)$idVoyage);

        if ($voyage) {
            // IMPORTANT :
            // on ne réécrit plus $prixTotal avec le prix du voyage aller,
            // sinon on perd le cumul aller + retour transmis depuis recap.php

            $depart = trim(($voyage['villeDepart'] ?? '') . (!empty($voyage['quartierDepart']) ? ' - ' . $voyage['quartierDepart'] : ''));
            $arrivee = trim(($voyage['villeArrivee'] ?? '') . (!empty($voyage['quartierArrivee']) ? ' - ' . $voyage['quartierArrivee'] : ''));
            $dateVoyage = $voyage['jourDepart'] ?? '';
            $nombrePlaces = (int)($voyage['nombrePlaces'] ?? 0);
            $reservedSeats = getReservedSeats($bdd, (int)$idVoyage);
        }
    }

    $_SESSION['prix'] = $prixTotal;
    $_SESSION['depart'] = $depart;
    $_SESSION['arrivee'] = $arrivee;
    $_SESSION['date'] = $dateVoyage;
    $_SESSION['flexOption'] = $flexOption;
} catch (Exception $e) {
    $dbError = "Échec de connexion à la base de données : " . $e->getMessage();
}

function generateReservationNumber($length = 8)
{
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

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css"/>

<style>
    .iti { width: 100%; }
    .iti__tel-input {
        width: 100% !important;
        padding-top: 12px !important;
        padding-bottom: 12px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.5rem !important;
        font-size: 1rem !important;
        line-height: 1.5rem !important;
    }
    .iti__country-list {
        z-index: 9999 !important;
    }
</style>

<div class="pt-8 pb-12">
    <div class="max-w-3xl mx-auto px-4">
        <div class="bg-white shadow-lg rounded-2xl p-6 md:p-8">
            <?php if (!empty($dbError)): ?>
                <p class="text-red-500 text-center mb-4"><?= htmlspecialchars($dbError) ?></p>
            <?php endif; ?>

            <h2 class="text-center text-2xl md:text-3xl font-bold text-gray-800 mb-8">
                Informations du Passager
            </h2>

            <form method="post" action="finalisation.php" id="passengerForm">
                <?php if ($idVoyage): ?>
                    <input type="hidden" name="idVoyage" value="<?= htmlspecialchars($idVoyage) ?>">
                <?php endif; ?>

                <input type="hidden" name="depart" value="<?= htmlspecialchars($depart) ?>">
                <input type="hidden" name="arrivee" value="<?= htmlspecialchars($arrivee) ?>">
                <input type="hidden" name="dateVoyage" value="<?= htmlspecialchars($dateVoyage) ?>">
                <input type="hidden" name="reservationNumber" value="<?= htmlspecialchars($reservationNumber) ?>">
                <input type="hidden" name="prixTotal" value="<?= htmlspecialchars($prixTotal) ?>">
                <input type="hidden" name="flexOption" value="<?= htmlspecialchars($flexOption ?? '') ?>">
                <input type="hidden" name="telephone" id="telephone">
                <input type="hidden" name="deliveryMethod" id="deliveryMethod" value="email">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            Prénom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="prenom" placeholder="Hervé" required class="w-full p-3 border rounded-lg">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-2">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nom" placeholder="Dupuis" required class="w-full p-3 border rounded-lg">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-bold mb-2">
                        Email <span class="text-red-500" id="emailRequiredMark">*</span>
                    </label>
                    <input type="email" name="email" id="email" placeholder="example@gmail.com" required class="w-full p-3 border rounded-lg">
                    <p id="emailHint" class="text-sm text-gray-500 mt-2">
                        Utilisé si vous choisissez de recevoir votre billet par email.
                    </p>
                </div>

                <div class="mt-4">
                    <label class="block text-gray-700 font-bold mb-2">
                        Téléphone <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" id="telephoneVisible" placeholder="6 55 19 62 54" required class="w-full p-3 border rounded-lg">
                    <p id="telephoneError" class="text-red-500 text-sm mt-2 hidden">
                        Veuillez saisir un numéro de téléphone valide.
                    </p>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-3">
                        Mode de réception du billet <span class="text-red-500">*</span>
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <label class="border rounded-xl p-4 cursor-pointer hover:border-green-500 transition bg-gray-50">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="deliveryMethodChoice" value="email" checked class="mt-1 accent-green-600">
                                <div>
                                    <div class="font-bold text-gray-800">Je veux le recevoir par email</div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        La confirmation et le billet seront envoyés à votre adresse email.
                                    </p>
                                </div>
                            </div>
                        </label>

                        <label class="border rounded-xl p-4 cursor-pointer hover:border-green-500 transition bg-gray-50">
                            <div class="flex items-start gap-3">
                                <input type="radio" name="deliveryMethodChoice" value="whatsapp" class="mt-1 accent-green-600">
                                <div>
                                    <div class="font-bold text-gray-800">
                                        J’accepte de recevoir ma confirmation et mon billet sur WhatsApp si ce numéro a un compte WhatsApp
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">
                                        Le billet sera envoyé sur le numéro renseigné, si WhatsApp y est actif.
                                    </p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-800 mt-8 mb-4">
                    Sélectionnez votre siège <span class="text-red-500">*</span>
                </h3>

                <?php include __DIR__ . '/includes/seat_grid.php'; ?>

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

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js"></script>
<script src="https://js.stripe.com/v3/"></script>

<script>
    const stripe = Stripe('pk_test_51Phsz9DwEke97it4yD8lj7SGiuTeL7yqscNb3S8ZMj8CvzGmOZ6V64Bqgk6uW6vpO7mF24SdHdf9lN6n07V9JV7v00p8mrRvpS');
    const checkoutButton = document.getElementById('checkout-button');
    const form = document.getElementById('passengerForm');

    const phoneInput = document.getElementById('telephoneVisible');
    const phoneHiddenInput = document.getElementById('telephone');
    const phoneError = document.getElementById('telephoneError');

    const emailInput = document.getElementById('email');
    const emailRequiredMark = document.getElementById('emailRequiredMark');
    const deliveryMethodHidden = document.getElementById('deliveryMethod');
    const deliveryMethodRadios = document.querySelectorAll('input[name="deliveryMethodChoice"]');

    const iti = window.intlTelInput(phoneInput, {
        initialCountry: 'cm',
        preferredCountries: ['cm', 'fr', 'be', 'ca'],
        separateDialCode: true,
        nationalMode: true,
        autoPlaceholder: 'polite',
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js'
    });

    function getDeliveryMethod() {
        const checked = document.querySelector('input[name="deliveryMethodChoice"]:checked');
        return checked ? checked.value : 'email';
    }

    function syncDeliveryMethod() {
        const method = getDeliveryMethod();
        deliveryMethodHidden.value = method;

        if (method === 'email') {
            emailInput.required = true;
            emailRequiredMark.classList.remove('hidden');
        } else {
            emailInput.required = false;
            emailRequiredMark.classList.add('hidden');
        }
    }

    function validateAndSetPhone() {
        const rawValue = phoneInput.value.trim();

        if (!rawValue) {
            phoneHiddenInput.value = '';
            phoneError.classList.add('hidden');
            return false;
        }

        if (iti.isValidNumber()) {
            phoneHiddenInput.value = iti.getNumber();
            phoneError.classList.add('hidden');
            phoneInput.classList.remove('border-red-500');
            return true;
        } else {
            phoneHiddenInput.value = '';
            phoneError.classList.remove('hidden');
            phoneInput.classList.add('border-red-500');
            return false;
        }
    }

    function validateFormBeforeSubmit(showAlert = true) {
        const selectedSeat = document.getElementById('selectedSeat').value;
        const deliveryMethod = getDeliveryMethod();
        const phoneOk = validateAndSetPhone();

        syncDeliveryMethod();

        if (!selectedSeat) {
            if (showAlert) alert('Veuillez sélectionner un siège avant de continuer.');
            return false;
        }

        if (deliveryMethod === 'email' && !emailInput.value.trim()) {
            if (showAlert) alert('Veuillez renseigner votre adresse email.');
            emailInput.focus();
            return false;
        }

        if (deliveryMethod === 'whatsapp' && !phoneOk) {
            if (showAlert) alert('Veuillez saisir un numéro valide pour WhatsApp.');
            phoneInput.focus();
            return false;
        }

        if (deliveryMethod === 'email' && phoneInput.value.trim() && !phoneOk) {
            if (showAlert) alert('Le numéro de téléphone saisi n’est pas valide.');
            phoneInput.focus();
            return false;
        }

        return true;
    }

    function selectSeat(seatNumber) {
        const seats = document.querySelectorAll('.seat-btn');

        seats.forEach(seat => {
            seat.classList.remove('bg-green-500');
            seat.classList.add('bg-blue-500');
        });

        const selectedSeat = document.getElementById(`seat${seatNumber}`);
        if (selectedSeat && !selectedSeat.disabled) {
            selectedSeat.classList.remove('bg-blue-500');
            selectedSeat.classList.add('bg-green-500');
        }

        document.getElementById('selectedSeat').value = seatNumber;
    }

    deliveryMethodRadios.forEach(radio => {
        radio.addEventListener('change', syncDeliveryMethod);
    });

    phoneInput.addEventListener('blur', validateAndSetPhone);
    phoneInput.addEventListener('countrychange', validateAndSetPhone);
    phoneInput.addEventListener('input', function () {
        phoneError.classList.add('hidden');
        phoneInput.classList.remove('border-red-500');
    });

    if (form) {
        form.addEventListener('submit', function (e) {
            if (!validateFormBeforeSubmit(true)) {
                e.preventDefault();
            }
        });
    }

    if (checkoutButton) {
        checkoutButton.addEventListener('click', function () {
            if (!validateFormBeforeSubmit(true)) {
                return;
            }

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

    syncDeliveryMethod();
</script>

<?php
$content = ob_get_clean();
$title = "Paiement";
include __DIR__ . '/layouts/default.php';
?>