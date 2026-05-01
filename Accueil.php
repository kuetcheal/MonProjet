<?php
session_start();

require_once __DIR__ . '/config.php';

if (isset($_POST['deconnect_account'])) {
    session_unset();
    session_destroy();
    header('Location: connexion.php');
    exit;
}

$destinations = [];

try {
    $stmt = $pdo->query("SELECT Nom_ville FROM destination ORDER BY Nom_ville ASC");
    $destinations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors du chargement des destinations : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css" />
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Litepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>

    <link rel="stylesheet" href="CSS/Accueil.css">

    <title>Accueil</title>

    <style>
        .litepicker {
            font-family: 'Inter', Arial, sans-serif;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            min-width: auto !important;
            z-index: 9999 !important;
        }

        .litepicker .container__main {
            background: #ffffff !important;
            border: 1px solid #e5e7eb !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
            border-radius: 10px !important;
            padding: 18px 18px 14px !important;
            box-sizing: border-box !important;
        }

        .litepicker .container__months {
            gap: 18px !important;
        }

        .litepicker .month-item {
            padding: 0 !important;
        }

        .litepicker .month-item-header {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-height: 48px !important;
            margin-bottom: 10px !important;
            color: green !important;
            font-weight: 700 !important;
            font-size: 16px !important;
            border-bottom: 1px solid #f3f4f6;
        }

        .litepicker .month-item-name,
        .litepicker .month-item-year {
            color: green !important;
            font-weight: 700 !important;
            font-size: 16px !important;
        }

        .litepicker .button-previous-month,
        .litepicker .button-next-month {
            width: 36px !important;
            height: 36px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            color: #6b7280 !important;
            background: #f9fafb !important;
            transition: all 0.2s ease !important;
        }

        .litepicker .button-previous-month:hover,
        .litepicker .button-next-month:hover {
            background: #f3f4f6 !important;
            color: green !important;
        }

        .litepicker .month-item-weekdays-row {
            margin-bottom: 8px !important;
        }

        .litepicker .month-item-weekdays-row > div {
            color: #6b7280 !important;
            font-size: 13px !important;
            font-weight: 700 !important;
            text-transform: lowercase;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            height: 34px !important;
        }

        .litepicker .month-item-calendar {
            gap: 4px !important;
        }

        .litepicker .day-item {
            width: 42px !important;
            height: 42px !important;
            max-width: 42px !important;
            line-height: 42px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 15px !important;
            font-weight: bold !important;
            color: black !important;
            transition: all 0.18s ease !important;
            position: relative;
            z-index: 1;
        }

        .litepicker .day-item:hover {
            background: #f3f4f6 !important;
            color: #111827 !important;
        }

        .litepicker .day-item.is-today {
            border: 1.5px solid #9ca3af !important;
            color: #111827 !important;
            background: #fff !important;
        }

        .litepicker .day-item.is-start-date,
        .litepicker .day-item.is-end-date {
            background: green !important;
            color: #fff !important;
            font-weight: 700 !important;
        }

        .litepicker .day-item.is-in-range {
            background: #f3f4f6 !important;
            color: green !important;
            border-radius: 0 !important;
        }

        .litepicker .day-item.is-start-date.is-in-range {
            border-radius: 999px 0 0 999px !important;
        }

        .litepicker .day-item.is-end-date.is-in-range {
            border-radius: 0 999px 999px 0 !important;
        }

        .litepicker .day-item.is-start-date.is-end-date {
            border-radius: 999px !important;
        }

        .litepicker .day-item.is-locked {
            color: #d1d5db !important;
        }

        .litepicker .container__footer {
            border-top: 1px solid #f3f4f6 !important;
            margin-top: 12px !important;
            padding-top: 12px !important;
        }

        .litepicker .container__footer .button-cancel,
        .litepicker .container__footer .button-apply {
            font-weight: 600 !important;
            padding: 10px 16px !important;
        }

        .litepicker .container__footer .button-apply {
            background: #156f3e !important;
            border-color: #156f3e !important;
        }

        .litepicker .container__footer .button-cancel {
            color: #6b7280 !important;
        }

        .litepicker select {
            appearance: none !important;
            border: 1px solid #e5e7eb !important;
            padding: 6px 28px 6px 10px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            color: #374151 !important;
            background-color: #fff !important;
            background-image: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        /*
            Correction mobile :
            - calendrier contenu dans l’écran
            - un seul mois affiché
            - jours alignés en grille
            - suppression du débordement horizontal
        */
        @media (max-width: 768px) {
            .litepicker {
                width: calc(100vw - 32px) !important;
                max-width: calc(100vw - 32px) !important;
                min-width: 0 !important;
                left: 16px !important;
                right: 16px !important;
                box-sizing: border-box !important;
                overflow: hidden !important;
            }

            .litepicker .container__main {
                width: 100% !important;
                max-width: 100% !important;
                padding: 12px !important;
                border-radius: 18px !important;
                overflow: hidden !important;
                box-sizing: border-box !important;
            }

            .litepicker .container__months {
                display: flex !important;
                width: 100% !important;
                max-width: 100% !important;
                overflow: hidden !important;
                gap: 0 !important;
            }

            .litepicker .month-item {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 100% !important;
                padding: 0 !important;
                box-sizing: border-box !important;
            }

            .litepicker .month-item + .month-item {
                display: none !important;
            }

            .litepicker .month-item-header {
                min-height: 42px !important;
                margin-bottom: 8px !important;
                font-size: 14px !important;
            }

            .litepicker .month-item-name,
            .litepicker .month-item-year {
                font-size: 14px !important;
            }

            .litepicker .month-item-weekdays-row {
                display: grid !important;
                grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
                width: 100% !important;
                margin-bottom: 6px !important;
            }

            .litepicker .month-item-weekdays-row > div {
                width: auto !important;
                font-size: 12px !important;
                height: 30px !important;
            }

            .litepicker .month-item-calendar {
                display: grid !important;
                grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
                width: 100% !important;
                gap: 3px !important;
            }

            .litepicker .day-item {
                width: 34px !important;
                height: 34px !important;
                max-width: 34px !important;
                line-height: 34px !important;
                font-size: 13px !important;
                justify-self: center !important;
            }

            .litepicker .button-previous-month,
            .litepicker .button-next-month {
                width: 32px !important;
                height: 32px !important;
            }
        }

        @media (max-width: 390px) {
            .litepicker {
                width: calc(100vw - 20px) !important;
                max-width: calc(100vw - 20px) !important;
                left: 10px !important;
                right: 10px !important;
            }

            .litepicker .container__main {
                padding: 10px !important;
            }

            .litepicker .day-item {
                width: 30px !important;
                height: 30px !important;
                max-width: 30px !important;
                line-height: 30px !important;
                font-size: 12px !important;
            }

            .litepicker .month-item-weekdays-row > div {
                font-size: 11px !important;
            }
        }
    </style>
</head>

<body>
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <section class="hero-section">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <h1 class="hero-title">
                Profite jusqu'à -20% sur les billets de reservations !
            </h1>
            <p class="hero-subtitle">
                REMISES EXCLUSIVES POUR LES MEMBRES !
                <a href="connexion.php" class="hero-link">
                    CONNECTEZ-VOUS / INSCRIVEZ-VOUS ICI
                </a>
            </p>
        </div>

        <div class="search-card-wrapper hidden md:block shadow-md">
            <h3 class="hero-search-title">Reserver votre voyage</h3>

            <div class="search-card">
                <form action="listevoyageretour.php" method="post" class="search-form">

                    <div class="trip-options">
                        <label class="trip-option">
                            <input type="radio" id="desktop_trip_oneway" name="inlineRadioOptions" value="option1" checked>
                            <span>Aller simple</span>
                        </label>

                        <label class="trip-option">
                            <input type="radio" id="desktop_trip_roundtrip" name="inlineRadioOptions" value="option2">
                            <span>Aller-Retour</span>
                        </label>
                    </div>

                    <div class="search-fields">
                        <div class="field-group">
                            <label for="desktop_input1">
                                <i class="bi bi-geo-alt"></i> DE :
                            </label>
                            <select id="desktop_input1" name="input1" class="select2">
                                <?php foreach ($destinations as $donnee): ?>
                                    <option value="<?= htmlspecialchars($donnee['Nom_ville']) ?>">
                                        <?= htmlspecialchars($donnee['Nom_ville']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="desktop_input2">
                                <i class="bi bi-geo-alt"></i> A :
                            </label>
                            <select id="desktop_input2" name="input2" class="select2">
                                <?php foreach ($destinations as $donnee): ?>
                                    <option value="<?= htmlspecialchars($donnee['Nom_ville']) ?>">
                                        <?= htmlspecialchars($donnee['Nom_ville']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="desktop_depart_display">Date départ :</label>
                            <input
                                type="text"
                                id="desktop_depart_display"
                                placeholder="jj/mm/aaaa"
                                readonly
                                class="w-full cursor-pointer">
                            <input type="hidden" id="desktop_depart_date" name="input3">
                        </div>

                        <div class="field-group">
                            <label for="desktop_return_display">Date retour :</label>
                            <input
                                type="text"
                                id="desktop_return_display"
                                placeholder="jj/mm/aaaa"
                                readonly
                                disabled
                                class="w-full cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-100">
                            <input type="hidden" id="desktop_return_date" name="input4">
                        </div>

                        <div class="field-group submit-group">
                            <label class="fake-label">Valider</label>
                            <input type="submit" value="Valider" class="submit-btn">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <main class="home-main">

        <section class="block md:hidden px-4 py-5 bg-white">
            <div class="w-full max-w-md mx-auto relative z-[5]">
                <h3 class="text-center text-[20px] font-bold text-[#46e37b] pb-3 leading-tight">
                    Reserver votre voyage
                </h3>

                <div class="bg-white/95 shadow-md px-4 py-5">
                    <form action="listevoyageretour.php" method="post" class="flex flex-col gap-5">

                        <div class="flex flex-wrap items-center gap-6">
                            <label class="flex items-center gap-2 text-black text-[15px] font-bold">
                                <input
                                    type="radio"
                                    id="mobile_trip_oneway"
                                    name="inlineRadioOptions"
                                    value="option1"
                                    checked
                                    class="w-[18px] h-[18px] accent-[#18884c]">
                                <span>Aller simple</span>
                            </label>

                            <label class="flex items-center gap-2 text-black text-[15px] font-bold">
                                <input
                                    type="radio"
                                    id="mobile_trip_roundtrip"
                                    name="inlineRadioOptions"
                                    value="option2"
                                    class="w-[18px] h-[18px] accent-[#18884c]">
                                <span>Aller-Retour</span>
                            </label>
                        </div>

                        <div class="grid grid-cols-1 gap-4">

                            <div class="flex flex-col min-w-0">
                                <label for="mobile_input1" class="mb-2 text-[#156f3e] text-[15px] font-extrabold flex items-center gap-2">
                                    <i class="bi bi-geo-alt text-[#18884c]"></i> DE :
                                </label>
                                <select
                                    id="mobile_input1"
                                    name="input1"
                                    class="select2 w-full h-[50px] rounded-[12px] border border-[#d3d7dc] px-3 text-[15px] font-bold text-[#156f3e] bg-white outline-none">
                                    <?php foreach ($destinations as $donnee): ?>
                                        <option value="<?= htmlspecialchars($donnee['Nom_ville']) ?>">
                                            <?= htmlspecialchars($donnee['Nom_ville']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="flex flex-col min-w-0">
                                <label for="mobile_input2" class="mb-2 text-[#156f3e] text-[15px] font-extrabold flex items-center gap-2">
                                    <i class="bi bi-geo-alt text-[#18884c]"></i> A :
                                </label>
                                <select
                                    id="mobile_input2"
                                    name="input2"
                                    class="select2 w-full h-[50px] rounded-[12px] border border-[#d3d7dc] px-3 text-[15px] font-bold text-[#156f3e] bg-white outline-none">
                                    <?php foreach ($destinations as $donnee): ?>
                                        <option value="<?= htmlspecialchars($donnee['Nom_ville']) ?>">
                                            <?= htmlspecialchars($donnee['Nom_ville']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="flex flex-col min-w-0">
                                <label for="mobile_depart_display" class="mb-2 text-[#156f3e] text-[15px] font-extrabold">
                                    Date départ :
                                </label>
                                <input
                                    type="text"
                                    id="mobile_depart_display"
                                    placeholder="jj/mm/aaaa"
                                    readonly
                                    class="w-full h-[50px] rounded-[12px] border border-[#d3d7dc] px-3 text-[15px] font-bold text-[#156f3e] bg-white outline-none cursor-pointer">
                                <input type="hidden" id="mobile_depart_date" name="input3">
                            </div>

                            <div class="flex flex-col min-w-0">
                                <label for="mobile_return_display" class="mb-2 text-[#156f3e] text-[15px] font-extrabold">
                                    Date retour :
                                </label>
                                <input
                                    type="text"
                                    id="mobile_return_display"
                                    placeholder="jj/mm/aaaa"
                                    readonly
                                    disabled
                                    class="w-full h-[50px] rounded-[12px] border border-[#d3d7dc] px-3 text-[15px] font-bold text-[#156f3e] bg-white outline-none cursor-pointer disabled:bg-gray-100 disabled:text-gray-400 disabled:cursor-not-allowed">
                                <input type="hidden" id="mobile_return_date" name="input4">
                            </div>

                            <div class="flex flex-col min-w-0">
                                <input
                                    type="submit"
                                    value="Valider"
                                    class="w-full h-[50px] rounded-[12px] border-0 bg-[#156f3e] text-white text-[15px] font-extrabold cursor-pointer">
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </section>

        <section class="relative z-[2] max-w-[1350px] mx-auto px-3 md:px-5 pt-[80px] md:pt-[170px] pb-[25px]">

            <h2 class="mb-[30px] text-left text-black text-[20px] md:text-[24px] lg:text-[28px] font-bold leading-[1.3]">
                Gérer vos trajets et vos réservations sans soucis grâce à vos identifiants de réservation sur votre billet de voyage.
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-5">

                <button
                    id="openModalButton"
                    class="h-[60px] md:h-[72px] bg-[#018b01] hover:bg-[#3fa644] text-white text-[1rem] md:text-[1.15rem] lg:text-[1.25rem] font-bold transition duration-200 hover:-translate-y-[2px]">
                    Gérer ma réservation
                </button>

                <button
                    id="openTrackingModalButton"
                    type="button"
                    class="h-[60px] md:h-[72px] bg-[#018b01] hover:bg-[#3fa644] text-white text-[1rem] md:text-[1.15rem] lg:text-[1.25rem] font-bold transition duration-200 hover:-translate-y-[2px]">
                    Localiser mon trajet
                </button>

                <button
                    class="h-[60px] md:h-[72px] bg-[#018b01] hover:bg-[#3fa644] text-white text-[1rem] md:text-[1.15rem] lg:text-[1.25rem] font-bold transition duration-200 hover:-translate-y-[2px]">
                    Besoin d'aide
                </button>

            </div>
        </section>

        <div id="modalContainer"></div>

        <?php include 'geolocalisation/formulaire-localisation.php'; ?>

        <section>
            <?php include 'includes/service-bus-card.php'; ?>
        </section>

        <section>
            <?php include 'map.php'; ?>
        </section>

        <section class="bg-white py-[60px]">
            <div class="max-w-[1350px] mx-auto px-3 md:px-5">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 xl:gap-[38px] items-start">
                    <div class="w-full">
                        <img
                            src="https://img.freepik.com/photos-premium/bus-touristique-blanc-moderne-roule-long-autoroute-coucher-soleil-brillant_111726-2495.jpg"
                            alt="Bus EasyTravel"
                            class="w-full h-[260px] sm:h-[360px] md:h-[420px] lg:h-[520px] object-cover">
                    </div>

                    <div>
                        <h2 class="m-0 mb-[18px] font-bold text-[1.35rem] md:text-[1.55rem] lg:text-[1.7rem] font-bold leading-snug">
                            EasyTravel est le plus grand réseau camerounais de transport personnel inter-urbain !
                        </h2>

                        <p class="text-[#555] text-[0.98rem] md:text-[1.02rem] lg:text-[1.05rem] leading-[1.8] mb-4">
                            Depuis 2000, <strong class="text-black font-bold">Général Voyage</strong> agrandit continuellement son réseau
                            camerounais et dessert chaque jour plus de 100 destinations dont plus de 30 villes au Cameroun.
                            Notre objectif est de rendre le Cameroun vert !
                            Le réseau de Général Voyage s’étend du Sud à l'Est jusqu’au Grand Nord.
                            Découvrez notre
                            <a href="#" class="text-[#2e8b57] underline">carte interactive</a>
                            ou réservez dès maintenant pour <strong class="text-black font-bold">Yaoundé, Kribi, Bamenda, Edea, Banga</strong> et bien d’autres.
                        </p>

                        <h3 class="mt-[22px] mb-3 font-bold text-[1.15rem] md:text-[1.28rem] lg:text-[1.4rem] font-bold">
                            C’est simple et confortable
                        </h3>

                        <p class="text-[#555] text-[0.98rem] md:text-[1.02rem] lg:text-[1.05rem] leading-[1.8] mb-4">
                            Voyager n’a jamais été aussi simple avec Général Voyage.
                            Notre personnel serviable et notre site web détaillé vous accompagnent de la réservation jusqu’à l’arrivée.
                            Vous pouvez <a href="#" class="text-[#2e8b57] underline">acheter votre billet en ligne</a>
                            ou même au dernier moment auprès du conducteur.
                        </p>

                        <p class="text-[#555] text-[0.98rem] md:text-[1.02rem] lg:text-[1.05rem] leading-[1.8] mb-0">
                            Nos bus garantissent <strong class="text-black font-bold">une place assise avec espace pour vos jambes</strong>,
                            <strong class="text-black font-bold">Wi-fi gratuit</strong>,
                            <strong class="text-black font-bold">prises électriques</strong> et des snacks à petit prix !
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <?php include 'includes/actualites.php'; ?>
    </main>

    <div id="modalMessage"></div>

    <?php include 'includes/scrollToUp.php'; ?>
    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function setupBookingForm(formSelector, startDisplayId, endDisplayId, startHiddenId, endHiddenId) {
                const form = document.querySelector(formSelector);
                if (!form) return;

                const startDisplay = document.getElementById(startDisplayId);
                const endDisplay = document.getElementById(endDisplayId);
                const startHidden = document.getElementById(startHiddenId);
                const endHidden = document.getElementById(endHiddenId);

                if (!startDisplay || !endDisplay || !startHidden || !endHidden) return;

                const radios = form.querySelectorAll('input[name="inlineRadioOptions"]');
                let picker = null;

                function getTripMode() {
                    const checked = form.querySelector('input[name="inlineRadioOptions"]:checked');
                    return checked ? checked.value : 'option1';
                }

                function clearDates() {
                    startDisplay.value = '';
                    endDisplay.value = '';
                    startHidden.value = '';
                    endHidden.value = '';
                }

                function createPicker(singleMode = true) {
                    if (picker) {
                        picker.destroy();
                    }

                    const isMobile = window.matchMedia('(max-width: 768px)').matches;

                    picker = new Litepicker({
                        element: startDisplay,
                        elementEnd: singleMode ? null : endDisplay,
                        singleMode: singleMode,

                        numberOfMonths: isMobile ? 1 : 2,
                        numberOfColumns: isMobile ? 1 : 2,

                        autoApply: true,
                        minDate: new Date(),
                        lang: 'fr-FR',
                        format: 'DD/MM/YYYY',

                        dropdowns: {
                            minYear: new Date().getFullYear(),
                            maxYear: new Date().getFullYear() + 2,
                            months: true,
                            years: true
                        },

                        setup: (pickerInstance) => {
                            pickerInstance.on('selected', (date1, date2) => {
                                if (date1) {
                                    startDisplay.value = date1.format('DD/MM/YYYY');
                                    startHidden.value = date1.format('YYYY-MM-DD');
                                }

                                if (!singleMode && date2) {
                                    endDisplay.value = date2.format('DD/MM/YYYY');
                                    endHidden.value = date2.format('YYYY-MM-DD');
                                }

                                if (singleMode) {
                                    endDisplay.value = '';
                                    endHidden.value = '';
                                }
                            });
                        }
                    });
                }

                function updateMode(initialLoad = false) {
                    const isRoundTrip = getTripMode() === 'option2';

                    if (!initialLoad) {
                        clearDates();
                    }

                    if (isRoundTrip) {
                        endDisplay.disabled = false;
                        createPicker(false);
                    } else {
                        endDisplay.disabled = true;
                        endDisplay.value = '';
                        endHidden.value = '';
                        createPicker(true);
                    }
                }

                radios.forEach((radio) => {
                    radio.addEventListener('change', function() {
                        updateMode(false);
                    });
                });

                window.addEventListener('resize', function() {
                    updateMode(true);
                });

                form.addEventListener('submit', function(e) {
                    const isRoundTrip = getTripMode() === 'option2';
                    const depart = form.querySelector('select[name="input1"]');
                    const arrivee = form.querySelector('select[name="input2"]');

                    if (!depart || !depart.value) {
                        e.preventDefault();
                        alert('Veuillez sélectionner une ville de départ.');
                        return;
                    }

                    if (!arrivee || !arrivee.value) {
                        e.preventDefault();
                        alert('Veuillez sélectionner une ville d’arrivée.');
                        return;
                    }

                    if (depart.value === arrivee.value) {
                        e.preventDefault();
                        alert('La ville de départ et la ville d’arrivée doivent être différentes.');
                        return;
                    }

                    if (!startHidden.value) {
                        e.preventDefault();
                        alert('Veuillez sélectionner une date de départ.');
                        return;
                    }

                    if (isRoundTrip && !endHidden.value) {
                        e.preventDefault();
                        alert('Veuillez sélectionner une date de retour.');
                    }
                });

                updateMode(true);
            }

            setupBookingForm(
                '.search-card form',
                'desktop_depart_display',
                'desktop_return_display',
                'desktop_depart_date',
                'desktop_return_date'
            );

            setupBookingForm(
                'section.block.md\\:hidden form',
                'mobile_depart_display',
                'mobile_return_display',
                'mobile_depart_date',
                'mobile_return_date'
            );
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trackingModal = document.getElementById('trackingModal');
            const openTrackingModalButton = document.getElementById('openTrackingModalButton');
            const closeTrackingModal = document.getElementById('closeTrackingModal');

            if (openTrackingModalButton && trackingModal) {
                openTrackingModalButton.addEventListener('click', function() {
                    trackingModal.classList.remove('hidden');
                    trackingModal.classList.add('flex');
                });
            }

            if (closeTrackingModal && trackingModal) {
                closeTrackingModal.addEventListener('click', function() {
                    trackingModal.classList.remove('flex');
                    trackingModal.classList.add('hidden');
                });
            }

            if (trackingModal) {
                trackingModal.addEventListener('click', function(e) {
                    if (e.target === trackingModal) {
                        trackingModal.classList.remove('flex');
                        trackingModal.classList.add('hidden');
                    }
                });
            }
        });
    </script>

    <script src="Javascript/Accueil.js"></script>
</body>

</html>