<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="CSS/filtre_secondaire.css">
    <title>Liste voyage</title>
</head>

<body class="bg-[#f6f7f9]">
    <?php include 'includes/header.php'; ?>

    <div class="pb-5"></div>

    <?php include 'filtre_secondaire.php'; ?>

    <?php
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $Depart = trim($_POST['input1'] ?? $_SESSION['depart'] ?? '');
        $Arrivee = trim($_POST['input2'] ?? $_SESSION['arrivee'] ?? '');
        $date = trim($_POST['input3'] ?? $_SESSION['date'] ?? '');
        $dateRetour = trim($_POST['input4'] ?? $_SESSION['dateretour'] ?? '');
        $tripType = $_POST['inlineRadioOptions'] ?? $_SESSION['tripType'] ?? 'option1';

        $_SESSION['depart'] = $Depart;
        $_SESSION['arrivee'] = $Arrivee;
        $_SESSION['date'] = $date;
        $_SESSION['dateretour'] = $dateRetour;
        $_SESSION['tripType'] = $tripType;

        $allerSimpleSelected = $tripType === 'option1';
        $allerRetourSelected = $tripType === 'option2';

        $typeBusFilter = trim($_GET['typeBus'] ?? '');
        $trancheHoraire = trim($_GET['trancheHoraire'] ?? '');
        $prixMax = trim($_GET['prixMax'] ?? '');
        $tri = trim($_GET['tri'] ?? 'heure_asc');
    } catch (Exception $e) {
        echo '<p class="text-red-600 text-center mt-8">Échec de connexion à la base de données.</p>';
        exit;
    }

    function buildFilterConditions(array &$params, string $prefix = '')
    {
        global $typeBusFilter, $trancheHoraire, $prixMax;

        $conditions = [];

        if (!empty($typeBusFilter)) {
            $conditions[] = "typeBus = :typeBus{$prefix}";
            $params[":typeBus{$prefix}"] = $typeBusFilter;
        }

        if ($prixMax !== '' && is_numeric($prixMax)) {
            $conditions[] = "prix <= :prixMax{$prefix}";
            $params[":prixMax{$prefix}"] = (float)$prixMax;
        }

        if (!empty($trancheHoraire)) {
            switch ($trancheHoraire) {
                case 'matin':
                    $conditions[] = "heureDepart BETWEEN :heureStart{$prefix} AND :heureEnd{$prefix}";
                    $params[":heureStart{$prefix}"] = '00:00:00';
                    $params[":heureEnd{$prefix}"] = '11:59:59';
                    break;

                case 'apresmidi':
                    $conditions[] = "heureDepart BETWEEN :heureStart{$prefix} AND :heureEnd{$prefix}";
                    $params[":heureStart{$prefix}"] = '12:00:00';
                    $params[":heureEnd{$prefix}"] = '17:59:59';
                    break;

                case 'soir':
                    $conditions[] = "heureDepart BETWEEN :heureStart{$prefix} AND :heureEnd{$prefix}";
                    $params[":heureStart{$prefix}"] = '18:00:00';
                    $params[":heureEnd{$prefix}"] = '23:59:59';
                    break;
            }
        }

        return $conditions;
    }

    function getOrderByClause()
    {
        global $tri;

        switch ($tri) {
            case 'prix_asc':
                return 'prix ASC, heureDepart ASC';
            case 'prix_desc':
                return 'prix DESC, heureDepart ASC';
            case 'heure_desc':
                return 'heureDepart DESC';
            case 'heure_asc':
            default:
                return 'heureDepart ASC';
        }
    }

    function countVoyages(PDO $bdd, string $depart, string $arrivee, string $date, string $prefix = 'c')
    {
        $params = [
            ":depart{$prefix}" => $depart,
            ":arrivee{$prefix}" => $arrivee,
            ":date{$prefix}" => $date,
        ];

        $conditions = [
            "villeDepart = :depart{$prefix}",
            "villeArrivee = :arrivee{$prefix}",
            "jourDepart = :date{$prefix}",
        ];

        $conditions = array_merge($conditions, buildFilterConditions($params, $prefix));

        $sql = "SELECT COUNT(*) FROM voyage WHERE " . implode(' AND ', $conditions);
        $stmt = $bdd->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    function fetchVoyages(PDO $bdd, string $depart, string $arrivee, string $date, string $prefix = 'f')
    {
        $params = [
            ":depart{$prefix}" => $depart,
            ":arrivee{$prefix}" => $arrivee,
            ":date{$prefix}" => $date,
        ];

        $conditions = [
            "villeDepart = :depart{$prefix}",
            "villeArrivee = :arrivee{$prefix}",
            "jourDepart = :date{$prefix}",
        ];

        $conditions = array_merge($conditions, buildFilterConditions($params, $prefix));

        $orderBy = getOrderByClause();

        $sql = "SELECT * FROM voyage WHERE " . implode(' AND ', $conditions) . " ORDER BY {$orderBy}";
        $stmt = $bdd->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getBusAmenities(string $bus): array
    {
        $bus = strtolower(trim($bus));

        if ($bus === 'vip') {
            return [
                ['icon' => 'fa fa-wifi', 'label' => 'Wi-Fi'],
                ['icon' => 'fa fa-television', 'label' => 'Écran'],
                ['icon' => 'fa fa-plug', 'label' => 'Prise'],
                ['icon' => 'fa fa-coffee', 'label' => 'Boisson'],
            ];
        }

        return [
            ['icon' => 'fa fa-wifi', 'label' => 'Wi-Fi'],
            ['icon' => 'fa fa-television', 'label' => 'Écran'],
            ['icon' => 'fa fa-plug', 'label' => 'Prise'],
        ];
    }

    function formatLieuCompact(string $ville = '', string $quartier = '', string $align = 'left'): string
    {
        $ville = htmlspecialchars($ville);
        $quartier = trim((string) $quartier);

        $alignClass = $align === 'center'
            ? 'text-center items-center'
            : ($align === 'right' ? 'text-right items-end' : 'text-left items-start');

        if (!empty($quartier)) {
            $quartierSafe = htmlspecialchars($quartier);
            return "
                <div class='flex flex-col {$alignClass} leading-tight min-w-0'>
                    <div class='text-[15px] sm:text-[16px] md:text-[20px] font-bold text-slate-700 break-words'>{$ville}</div>
                    <div class='text-[10px] sm:text-[11px] md:text-[12px] text-gray-500 mt-0.5 break-words'>{$quartierSafe}</div>
                </div>
            ";
        }

        return "
            <div class='flex flex-col {$alignClass} leading-tight min-w-0'>
                <div class='text-[15px] sm:text-[16px] md:text-[20px] font-bold text-slate-700 break-words'>{$ville}</div>
            </div>
        ";
    }

    function renderAmenitiesHtml(string $bus): string
    {
        $amenities = getBusAmenities($bus);
        $html = "<div class='flex flex-wrap items-center justify-center gap-2 sm:gap-3 text-gray-500'>";

        foreach ($amenities as $item) {
            $icon = htmlspecialchars($item['icon']);
            $label = htmlspecialchars($item['label']);
            $html .= "
                <span class='inline-flex items-center gap-1 sm:gap-1.5' title='{$label}'>
                    <i class='{$icon} text-[12px] sm:text-[13px] md:text-[14px]'></i>
                    <span class='hidden md:inline text-[13px]'>{$label}</span>
                </span>
            ";
        }

        $html .= "</div>";
        return $html;
    }

    function renderVoyageCard(array $voyage, string $type = 'simple', string $formAction = 'payment.php')
    {
        $id = htmlspecialchars($voyage['idVoyage']);
        $heureDepart = htmlspecialchars(substr($voyage['heureDepart'], 0, 5));
        $heureArrivee = htmlspecialchars(substr($voyage['heureArrivee'], 0, 5));
        $villeDepart = $voyage['villeDepart'] ?? '';
        $quartierDepart = $voyage['quartierDepart'] ?? '';
        $villeArrivee = $voyage['villeArrivee'] ?? '';
        $quartierArrivee = $voyage['quartierArrivee'] ?? '';
        $prix = htmlspecialchars($voyage['prix']);
        $bus = htmlspecialchars($voyage['typeBus']);

        $departHtml = formatLieuCompact($villeDepart, $quartierDepart, 'left');
        $arriveeHtml = formatLieuCompact($villeArrivee, $quartierArrivee, 'center');
        $amenitiesHtml = renderAmenitiesHtml($bus);
        $detailsId = "details-{$type}-{$id}";
        $busLower = strtolower(trim($bus));

        $typeHtml = "
            <div class='flex items-center justify-end gap-1.5 sm:gap-2 text-right'>
                <i class='fa fa-bus text-[16px] sm:text-[18px] md:text-[22px]'></i>
                <span class='text-[15px] sm:text-[16px] md:text-[20px] font-bold text-slate-700'>{$busLower}</span>
            </div>
        ";

        $cardBody = "
    <div class='bg-white shadow-md border border-gray-100 px-2 py-3 sm:px-4 sm:py-4 md:px-5 transition hover:shadow-md w-full'>

        <!-- Ligne 1 -->
        <div class='grid grid-cols-3 items-center gap-2 sm:gap-3 md:gap-4 mb-3 sm:mb-4'>
            <div class='text-left'>
                <span class='text-[13px] sm:text-[16px] md:text-[22px] font-extrabold text-slate-800'>{$heureDepart}</span>
            </div>

            <div class='flex items-center justify-center gap-1 sm:gap-2 md:gap-3'>
                <span class='w-1.5 h-1.5 sm:w-2 sm:h-2 md:w-2.5 md:h-2.5 rounded-full bg-slate-700'></span>
                <span class='h-[2px] w-5 sm:w-8 md:w-16 bg-slate-300'></span>
                <span class='text-[13px] sm:text-[16px] md:text-[22px] font-extrabold text-slate-800'>{$heureArrivee}</span>
                <span class='h-[2px] w-5 sm:w-8 md:w-16 bg-slate-300'></span>
                <span class='w-1.5 h-1.5 sm:w-2 sm:h-2 md:w-2.5 md:h-2.5 rounded-full bg-slate-700'></span>
            </div>

            <div class='text-right'>
                <p class='text-[13px] sm:text-[16px] md:text-[20px] leading-none font-extrabold text-black'>
                    {$prix}<span class='text-[9px] sm:text-[10px] md:text-[14px] ml-1'>FCFA</span>
                </p>
            </div>
        </div>

        <!-- Ligne 2 -->
        <div class='grid grid-cols-3 items-start gap-2 sm:gap-3 md:gap-4 mb-3 sm:mb-4'>
            <div class='flex items-start gap-1.5 sm:gap-2 min-w-0'>
                <i class='bi bi-geo-alt text-[16px] sm:text-[18px] md:text-[22px] mt-0.5 shrink-0'></i>
                {$departHtml}
            </div>

            <div class='flex items-start justify-center gap-1.5 sm:gap-2 min-w-0'>
                <i class='bi bi-geo-alt text-[16px] sm:text-[18px] md:text-[22px] mt-0.5 shrink-0'></i>
                {$arriveeHtml}
            </div>

            <div class='flex justify-end min-w-0'>
                {$typeHtml}
            </div>
        </div>

        <!-- Ligne 3 -->
        <div class='grid grid-cols-3 items-center gap-2 sm:gap-3 md:gap-4'>
            <div class='text-left min-w-0'>
                <button
                    type='button'
                    class='text-blue-600 hover:underline font-medium text-[11px] sm:text-[13px] md:text-[15px]'
                    onclick=\"toggleDetails('{$detailsId}')\">
                    Détails du trajet
                </button>
            </div>

            <div class='flex justify-center min-w-0'>
                {$amenitiesHtml}
            </div>

            <div class='flex justify-end min-w-0'>
                %s
            </div>
        </div>

        <div id='{$detailsId}' class='hidden mt-3 sm:mt-4 pt-3 sm:pt-4 border-t border-gray-200'>
            <div class='grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-4 text-[12px] sm:text-sm text-slate-600'>
                <div>
                    <p><strong>Départ :</strong> " . htmlspecialchars($villeDepart) . (!empty($quartierDepart) ? " - " . htmlspecialchars($quartierDepart) : "") . "</p>
                    <p><strong>Heure départ :</strong> {$heureDepart}</p>
                </div>
                <div>
                    <p><strong>Arrivée :</strong> " . htmlspecialchars($villeArrivee) . (!empty($quartierArrivee) ? " - " . htmlspecialchars($quartierArrivee) : "") . "</p>
                    <p><strong>Heure arrivée :</strong> {$heureArrivee}</p>
                </div>
            </div>
        </div>
    </div>
      ";

       if ($type === 'simple') {
    $action = "
        <button
            class='continuer-btn-simple bg-green-600 hover:bg-green-700 text-white font-bold text-[11px] sm:text-[13px] md:text-[15px] px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 transition min-w-[92px] sm:min-w-[120px] md:min-w-[130px]'
            data-id='{$id}'
            data-price='{$prix}'
            data-depart='" . htmlspecialchars($villeDepart) . (!empty($quartierDepart) ? " - " . htmlspecialchars($quartierDepart) : "") . "'
            data-arrive='" . htmlspecialchars($villeArrivee) . (!empty($quartierArrivee) ? " - " . htmlspecialchars($quartierArrivee) : "") . "'
            data-time='{$heureDepart}'>
            Continuer
        </button>
    ";
            echo "<div class='mb-4 sm:mb-5'>" . sprintf($cardBody, $action) . "</div>";
        } else {
            $action = "
                <button
                    class='continuer-btn bg-green-600 hover:bg-green-700 text-white font-bold text-[11px] sm:text-[13px] md:text-[15px] px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 transition min-w-[92px] sm:min-w-[120px] md:min-w-[130px]'
                    data-id='{$id}'
                    data-type='{$type}'
                    data-price='{$prix}'
                    data-depart='" . htmlspecialchars($villeDepart) . (!empty($quartierDepart) ? " - " . htmlspecialchars($quartierDepart) : "") . "'
                    data-arrive='" . htmlspecialchars($villeArrivee) . (!empty($quartierArrivee) ? " - " . htmlspecialchars($quartierArrivee) : "") . "'
                    data-time='{$heureDepart}'>
                    Continuer
                </button>
            ";
            echo "<div id='conteneur-{$type}-{$id}' class='mb-4 sm:mb-5 voyage-card transition-all duration-200'>" . sprintf($cardBody, $action) . "</div>";
        }
    }
    ?>

    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-5 sm:py-8">
        <div class="flex flex-col lg:flex-row gap-5 sm:gap-8 items-start">
            <main class="flex-1 min-w-0 order-1">
                <?php if ($allerSimpleSelected): ?>
                    <?php
                    $voyagesDisponiblesAller = 0;
                    $voyagesAller = [];

                    if (!empty($Depart) && !empty($Arrivee) && !empty($date)) {
                        $voyagesDisponiblesAller = countVoyages($bdd, $Depart, $Arrivee, $date, 'allerCount');
                        $voyagesAller = fetchVoyages($bdd, $Depart, $Arrivee, $date, 'allerFetch');
                    }
                    ?>

                    <div class="flex flex-col gap-2 sm:gap-3 md:flex-row md:items-center md:justify-between mb-5 sm:mb-8">
                        <p class="text-[16px] sm:text-[18px] md:text-xl font-bold text-green-600">
                            Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                        </p>
                        <p class="text-[16px] sm:text-[18px] md:text-xl font-bold text-slate-800">
                            <?php echo $voyagesDisponiblesAller; ?> voyages disponibles
                        </p>
                    </div>

                    <?php if (!empty($Depart) && !empty($Arrivee) && !empty($date)): ?>
                        <?php if (!empty($voyagesAller)): ?>
                            <?php foreach ($voyagesAller as $donne): ?>
                                <?php renderVoyageCard($donne, 'simple', 'payment.php'); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500 text-base sm:text-lg mt-10">Aucun voyage disponible pour cette recherche.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 text-base sm:text-lg mt-10">Veuillez lancer une recherche de trajet.</p>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if ($allerRetourSelected): ?>
                    <?php
                    $voyagesDisponiblesAller = 0;
                    $voyagesAller = [];

                    if (!empty($Depart) && !empty($Arrivee) && !empty($date)) {
                        $voyagesDisponiblesAller = countVoyages($bdd, $Depart, $Arrivee, $date, 'allerCountRT');
                        $voyagesAller = fetchVoyages($bdd, $Depart, $Arrivee, $date, 'allerFetchRT');
                    }
                    ?>

                    <div class="flex flex-col gap-2 sm:gap-3 md:flex-row md:items-center md:justify-between mb-5 sm:mb-8">
                        <h2 class="text-[16px] sm:text-[18px] md:text-xl font-bold">
                            Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                        </h2>
                        <p class="text-[16px] sm:text-[18px] md:text-xl font-bold text-slate-800">
                            <?php echo $voyagesDisponiblesAller; ?> voyages disponibles
                        </p>
                    </div>

                    <?php if (!empty($voyagesAller)): ?>
                        <?php foreach ($voyagesAller as $donne): ?>
                            <?php renderVoyageCard($donne, 'aller'); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 text-base sm:text-lg mt-10 mb-10">Aucun voyage aller disponible.</p>
                    <?php endif; ?>

                    <?php
                    $voyagesDisponiblesRetour = 0;
                    $voyagesRetour = [];

                    if (!empty($Depart) && !empty($Arrivee) && !empty($dateRetour)) {
                        $voyagesDisponiblesRetour = countVoyages($bdd, $Arrivee, $Depart, $dateRetour, 'retourCountRT');
                        $voyagesRetour = fetchVoyages($bdd, $Arrivee, $Depart, $dateRetour, 'retourFetchRT');
                    }
                    ?>

                    <div class="mt-8 sm:mt-12 mb-5 sm:mb-8 flex flex-col gap-2 sm:gap-3 md:flex-row md:items-center md:justify-between">
                        <h2 class="text-[16px] sm:text-[18px] md:text-xl font-bold">
                            Retour : <?php echo !empty($dateRetour) ? date("d M Y", strtotime($dateRetour)) : ''; ?>
                        </h2>
                        <p class="text-[16px] sm:text-[18px] md:text-xl font-bold text-slate-800">
                            <?php echo $voyagesDisponiblesRetour; ?> voyages disponibles
                        </p>
                    </div>

                    <?php if (!empty($voyagesRetour)): ?>
                        <?php foreach ($voyagesRetour as $donne): ?>
                            <?php renderVoyageCard($donne, 'retour'); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 text-base sm:text-lg mt-10">Aucun voyage retour disponible.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </main>

            <aside class="w-full lg:w-[290px] shrink-0 order-2 lg:order-none">
                <?php include 'includes/voyage_filtre.php'; ?>
            </aside>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleDetails(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden');
        }

        document.querySelectorAll('.continuer-btn-simple').forEach(button => {
         button.addEventListener('click', function () {

        const params = new URLSearchParams({
            priceAller: this.dataset.price,
            priceRetour: 0,
            departAller: this.dataset.depart,
            arriveAller: this.dataset.arrive,
            timeAller: this.dataset.time,
            departRetour: '',
            arriveRetour: '',
            timeRetour: ''
        });

        window.location.href = 'recap.php?' + params.toString();
        });
        });

        document.addEventListener('DOMContentLoaded', function () {
            let selectedTrips = { aller: null, retour: null };

            document.querySelectorAll('.continuer-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();

                    const type = this.dataset.type;
                    const parentCard = this.closest('[id^="conteneur-"]');
                    if (!parentCard) return;

                    const id = parentCard.id;
                    const selected = selectedTrips[type];

                    if (selected) {
                        const previousCard = document.getElementById(selected.id);
                        if (previousCard) {
                            const previousBox = previousCard.querySelector('div.bg-white');
                            if (previousBox) {
                                previousBox.classList.remove('border-green-500', 'bg-green-50');
                                previousBox.classList.add('border-transparent');
                            }
                        }
                    }

                    selectedTrips[type] = {
                        id,
                        price: this.dataset.price,
                        depart: this.dataset.depart,
                        arrive: this.dataset.arrive,
                        time: this.dataset.time
                    };

                    const currentBox = parentCard.querySelector('div.bg-white');
                    if (currentBox) {
                        currentBox.classList.remove('border-transparent');
                        currentBox.classList.add('border-green-500', 'bg-green-50');
                    }

                    if (selectedTrips.aller && selectedTrips.retour) {
                        const params = new URLSearchParams({
                            priceAller: selectedTrips.aller.price,
                            priceRetour: selectedTrips.retour.price,
                            departAller: selectedTrips.aller.depart,
                            arriveAller: selectedTrips.aller.arrive,
                            timeAller: selectedTrips.aller.time,
                            departRetour: selectedTrips.retour.depart,
                            arriveRetour: selectedTrips.retour.arrive,
                            timeRetour: selectedTrips.retour.time
                        });

                        window.location.href = 'recap.php?' + params.toString();
                    }
                });
            });
        });
    </script>
</body>
</html>