<?php
session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/fidelite_functions.php';

$user = null;
$fidelite = null;

if (isset($_SESSION['Id_compte'])) {
    $userId = (int) $_SESSION['Id_compte'];
    $user = getUserInfos($pdo, $userId);
    $fidelite = getFideliteInfos($pdo, $userId);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres fidélité - Easy Travel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body style="background-color: aliceblue;">

<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="relative overflow-hidden">
    <div class="absolute inset-0">
        <img
            src="/MonProjet/pictures/billet-offre.webp"
            alt="Offres fidélité"
            class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-black/60"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 lg:py-28">
        <div class="max-w-3xl">
            <span class="inline-block bg-white/15 text-white border border-white/20 px-4 py-2 text-sm font-semibold mb-5">
                Programme fidélité • Easy Travel
            </span>

            <h1 class="text-white text-3xl sm:text-5xl font-extrabold leading-tight mb-6">
                12 billets achetés = 1 voyage offert
            </h1>

            <p class="text-white/90 text-base sm:text-lg leading-8 mb-8">
                Réservez vos billets depuis votre compte personnel, cumulez vos trajets validés
                et bénéficiez d’un voyage offert dès votre 12e réservation payée.
            </p>

            <div class="flex flex-col sm:flex-row gap-4">
                <a href="../Reservation/Reservation.php"
                   class="inline-flex items-center justify-center bg-[#4CAF50] hover:bg-[#3e8e41] text-white font-semibold px-6 py-3 rounded-md transition">
                    Réserver maintenant
                </a>

                <a href="#fonctionnement"
                   class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold px-6 py-3 rounded-md transition">
                    Voir le fonctionnement
                </a>
            </div>
        </div>
    </div>
</section>

<section class="relative -mt-8 sm:-mt-12 z-10">
    <div class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white shadow-md p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center text-[#008000] text-2xl">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-800 mb-2">12 réservations validées</h3>
                <p class="text-gray-600 text-sm leading-6">
                    Chaque réservation payée depuis votre compte vous rapproche de votre bonus.
                </p>
            </div>

            <div class="bg-white shadow-md p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center text-[#008000] text-2xl">
                    <i class="fa-solid fa-gift"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-800 mb-2">1 voyage offert</h3>
                <p class="text-gray-600 text-sm leading-6">
                    Une fois le seuil atteint, vous pouvez réserver gratuitement un trajet.
                </p>
            </div>

            <div class="bg-white shadow-md p-6 text-center">
                <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center text-[#008000] text-2xl">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-800 mb-2">Compte obligatoire</h3>
                <p class="text-gray-600 text-sm leading-6">
                    Le billet doit être réservé avec les mêmes informations que votre compte.
                </p>
            </div>
        </div>
    </div>
</section>

<?php if ($user && $fidelite): ?>
<section class="py-16">
    <div class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-xl overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="p-8 sm:p-10">
                    <span class="text-[#008000] font-semibold uppercase tracking-wide text-sm">Votre progression</span>
                    <h2 class="text-3xl font-extrabold text-gray-900 mt-3 mb-4">
                        Bonjour <?= htmlspecialchars($user['user_firstname'] . ' ' . $user['user_name']) ?>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                        <div class="bg-green-50 p-5 rounded-lg border border-green-100">
                            <p class="text-sm text-gray-600">Réservations validées</p>
                            <h3 class="text-3xl font-extrabold text-[#008000]"><?= $fidelite['reservations_valides'] ?></h3>
                        </div>

                        <div class="bg-green-50 p-5 rounded-lg border border-green-100">
                            <p class="text-sm text-gray-600">Crédits disponibles</p>
                            <h3 class="text-3xl font-extrabold text-[#008000]"><?= $fidelite['credits_disponibles'] ?></h3>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="flex justify-between text-sm font-medium text-gray-700 mb-2">
                            <span>Progression actuelle</span>
                            <span><?= $fidelite['progression'] ?>/12</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                            <div class="bg-[#008000] h-4 rounded-full" style="width: <?= ($fidelite['progression'] / 12) * 100 ?>%;"></div>
                        </div>
                    </div>

                    <p class="text-gray-700 leading-7">
                        Il vous reste
                        <span class="font-bold text-[#008000]"><?= $fidelite['reste_pour_cadeau'] ?></span>
                        réservation(s) validée(s) pour obtenir un nouveau voyage offert.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4">
                        <a href="../Reservation/Reservation.php" class="bg-[#4CAF50] hover:bg-[#3e8e41] text-white font-semibold px-6 py-3 rounded-md transition text-center">
                            Faire une réservation
                        </a>
                        <a href="mes_offres.php" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold px-6 py-3 rounded-md transition text-center">
                            Voir mes avantages
                        </a>
                    </div>
                </div>

                <div class="relative min-h-[320px] lg:min-h-full">
                    <img
                        src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80"
                        alt="Voyage fidélité"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-black/30"></div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php else: ?>
<section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md p-8 text-center">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Connectez-vous pour suivre vos offres</h2>
            <p class="text-gray-600 leading-7 mb-6">
                Créez un compte ou connectez-vous pour accumuler vos réservations
                et profiter automatiquement de votre voyage offert.
            </p>
            <a href="../connexion.php" class="inline-flex items-center justify-center bg-[#008000] hover:bg-[#0d5d31] text-white font-semibold px-6 py-3 rounded-md transition">
                Se connecter
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<section id="fonctionnement" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-12">
            <span class="text-[#008000] font-semibold uppercase tracking-wide text-sm">Fonctionnement</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">
                Comment obtenir un billet offert ?
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-user-plus"></i></div>
                <h3 class="font-bold text-lg mb-2 text-gray-900">1. Créez un compte</h3>
                <p class="text-sm text-gray-600 leading-6">Votre compte sert à enregistrer vos réservations et votre progression.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-id-card"></i></div>
                <h3 class="font-bold text-lg mb-2 text-gray-900">2. Réservez avec vos infos</h3>
                <p class="text-sm text-gray-600 leading-6">Le nom, prénom, email et téléphone du billet doivent être ceux du compte.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-layer-group"></i></div>
                <h3 class="font-bold text-lg mb-2 text-gray-900">3. Cumulez 12 réservations</h3>
                <p class="text-sm text-gray-600 leading-6">Seules les réservations payées et validées sont prises en compte.</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-plane-departure"></i></div>
                <h3 class="font-bold text-lg mb-2 text-gray-900">4. Utilisez votre bonus</h3>
                <p class="text-sm text-gray-600 leading-6">Dès qu’un crédit est disponible, vous pouvez réserver gratuitement un trajet.</p>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>