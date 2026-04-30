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

<body class="bg-[#f6f7f9] overflow-x-hidden">
    <?php include 'includes/topbar.php'; ?>
    <?php include 'includes/header.php'; ?>

    <div class="pb-5"></div>

    <?php include 'includes/filtre_secondaire.php'; ?>

    <?php
    try {
        require_once __DIR__ . '/config.php';

        $bdd = $pdo;

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
        $transport = trim($_GET['transport'] ?? 'tout');
    } catch (Exception $e) {
        echo '<p class="text-red-600 text-center mt-8">Échec de connexion à la base de données.</p>';
        exit;
    }

    function buildFilterConditions(array &$params, string $prefix = '')
    {
        global $typeBusFilter, $trancheHoraire, $prixMax, $transport;

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

        if ($transport === 'bus') {
            $conditions[] = "modeTransport = :modeTransport{$prefix}";
            $params[":modeTransport{$prefix}"] = 'bus';
        } elseif ($transport === 'covoiturage') {
            $conditions[] = "modeTransport = :modeTransport{$prefix}";
            $params[":modeTransport{$prefix}"] = 'covoiturage';
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

        return (int)$stmt->fetchColumn();
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

    function getAmenities(string $bus, string $modeTransport = 'bus'): array
    {
        $bus = strtolower(trim($bus));
        $modeTransport = strtolower(trim($modeTransport));

        if ($modeTransport === 'covoiturage') {
            return [
                ['icon' => 'bi bi-person-badge', 'label' => 'Conducteur'],
                ['icon' => 'bi bi-people', 'label' => 'Places'],
                ['icon' => 'fa fa-snowflake-o', 'label' => 'Climatisation'],
            ];
        }

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
        $quartier = trim((string)$quartier);

        $alignClass = $align === 'center'
            ? 'text-center items-center'
            : ($align === 'right' ? 'text-right items-end' : 'text-left items-start');

        if (!empty($quartier)) {
            $quartierSafe = htmlspecialchars($quartier);

            return "
                <div class='flex flex-col {$alignClass} leading-tight min-w-0 max-w-full'>
                    <div class='text-[12px] sm:text-[16px] md:text-[20px] font-bold text-slate-700 break-words leading-tight'>{$ville}</div>
                    <div class='text-[9px] sm:text-[11px] md:text-[12px] text-gray-500 mt-0.5 break-words leading-tight'>{$quartierSafe}</div>
                </div>
            ";
        }

        return "
            <div class='flex flex-col {$alignClass} leading-tight min-w-0 max-w-full'>
                <div class='text-[12px] sm:text-[16px] md:text-[20px] font-bold text-slate-700 break-words leading-tight'>{$ville}</div>
            </div>
        ";
    }

    function renderAmenitiesHtml(string $bus, string $modeTransport = 'bus'): string
    {
        $amenities = getAmenities($bus, $modeTransport);

        $html = "<div class='flex flex-wrap items-center justify-center gap-1.5 sm:gap-3 text-gray-500 max-w-full overflow-hidden'>";

        foreach ($amenities as $item) {
            $icon = htmlspecialchars($item['icon']);
            $label = htmlspecialchars($item['label']);

            $html .= "
                <span class='inline-flex items-center gap-1 sm:gap-1.5' title='{$label}'>
                    <i class='{$icon} text-[10px] sm:text-[13px] md:text-[14px]'></i>
                    <span class='hidden md:inline text-[13px]'>{$label}</span>
                </span>
            ";
        }

        $html .= "</div>";

        return $html;
    }

    function renderTransportLabelHtml(string $modeTransport = 'bus'): string
    {
        $modeTransport = strtolower(trim($modeTransport));

        if ($modeTransport === 'covoiturage') {
            return "
                <div class='flex items-center gap-1.5 sm:gap-2 text-slate-600 font-semibold text-[10px] sm:text-[13px] md:text-[15px] whitespace-nowrap'>
                    <span>Personnel</span>
                    <i class='bi bi-car-front-fill text-[13px] sm:text-[18px] md:text-[20px]'></i>
                </div>
            ";
        }

        return "
            <div class='flex items-center gap-1.5 sm:gap-2 text-slate-600 font-semibold text-[10px] sm:text-[13px] md:text-[15px] whitespace-nowrap'>
                <span>Bus</span>
                <i class='fa fa-bus text-[13px] sm:text-[17px] md:text-[19px]'></i>
            </div>
        ";
    }

    function renderVoyageCard(array $voyage, string $type = 'simple')
    {
        $id = htmlspecialchars($voyage['idVoyage']);
        $heureDepart = htmlspecialchars(substr($voyage['heureDepart'], 0, 5));
        $heureArrivee = htmlspecialchars(substr($voyage['heureArrivee'], 0, 5));

        $villeDepart = $voyage['villeDepart'] ?? '';
        $quartierDepart = $voyage['quartierDepart'] ?? '';

        $villeArrivee = $voyage['villeArrivee'] ?? '';
        $quartierArrivee = $voyage['quartierArrivee'] ?? '';

        $modeTransport = strtolower(trim($voyage['modeTransport'] ?? 'bus'));
        $modeTransportSafe = htmlspecialchars($modeTransport);

        /*
            Pour le covoiturage, si prix_par_place existe, on l’utilise.
            Sinon on garde prix comme pour les bus.
        */
        if (
            $modeTransport === 'covoiturage'
            && isset($voyage['prix_par_place'])
            && $voyage['prix_par_place'] !== null
            && $voyage['prix_par_place'] !== ''
        ) {
            $prix = htmlspecialchars($voyage['prix_par_place']);
        } else {
            $prix = htmlspecialchars($voyage['prix']);
        }

        $bus = htmlspecialchars($voyage['typeBus'] ?? 'standard');

        $departHtml = formatLieuCompact($villeDepart, $quartierDepart, 'left');
        $arriveeHtml = formatLieuCompact($villeArrivee, $quartierArrivee, 'center');

        $amenitiesHtml = renderAmenitiesHtml($bus, $modeTransport);
        $transportLabelHtml = renderTransportLabelHtml($modeTransport);

        $busLower = strtolower(trim($bus));

        if ($modeTransport === 'covoiturage') {
            $typeHtml = "
                <div class='flex items-center justify-end gap-1 sm:gap-2 text-right min-w-0'>
                    <i class='bi bi-car-front-fill text-[12px] sm:text-[18px] md:text-[22px] shrink-0'></i>
                    <span class='text-[11px] sm:text-[16px] md:text-[20px] font-bold text-slate-700 truncate'>covoiturage</span>
                </div>
            ";
        } else {
            $typeHtml = "
                <div class='flex items-center justify-end gap-1 sm:gap-2 text-right min-w-0'>
                    <i class='fa fa-bus text-[12px] sm:text-[18px] md:text-[22px] shrink-0'></i>
                    <span class='text-[11px] sm:text-[16px] md:text-[20px] font-bold text-slate-700 truncate'>{$busLower}</span>
                </div>
            ";
        }

        $cardBody = "
        <div class='bg-white shadow-md border border-gray-100 px-2 py-3 sm:px-4 sm:py-4 md:px-5 transition hover:shadow-md w-full max-w-full overflow-hidden'>

            <div class='grid grid-cols-[0.72fr_1.35fr_0.75fr] sm:grid-cols-3 items-center gap-1 sm:gap-3 md:gap-4 mb-3 sm:mb-4'>
                <div class='text-left min-w-0'>
                    <span class='text-[12px] sm:text-[16px] md:text-[22px] font-extrabold text-slate-800 whitespace-nowrap'>{$heureDepart}</span>
                </div>

                <div class='flex items-center justify-center gap-1 sm:gap-2 md:gap-3 min-w-0 overflow-hidden'>
                    <span class='w-1.5 h-1.5 sm:w-2 sm:h-2 md:w-2.5 md:h-2.5 rounded-full bg-slate-700 shrink-0'></span>
                    <span class='h-[2px] w-4 sm:w-8 md:w-16 bg-slate-300 shrink'></span>
                    <span class='text-[12px] sm:text-[16px] md:text-[22px] font-extrabold text-slate-800 whitespace-nowrap shrink-0'>{$heureArrivee}</span>
                    <span class='h-[2px] w-4 sm:w-8 md:w-16 bg-slate-300 shrink'></span>
                    <span class='w-1.5 h-1.5 sm:w-2 sm:h-2 md:w-2.5 md:h-2.5 rounded-full bg-slate-700 shrink-0'></span>
                </div>

                <div class='text-right min-w-0'>
                    <p class='text-[11px] sm:text-[16px] md:text-[20px] leading-none font-extrabold text-black whitespace-nowrap'>
                        {$prix}<span class='text-[7px] sm:text-[10px] md:text-[14px] ml-0.5 sm:ml-1'>FCFA</span>
                    </p>
                </div>
            </div>

            <div class='grid grid-cols-[1fr_1fr_0.85fr] sm:grid-cols-3 items-start gap-1 sm:gap-3 md:gap-4 mb-3 sm:mb-4'>
                <div class='flex items-start gap-1 sm:gap-2 min-w-0 overflow-hidden'>
                    <i class='bi bi-geo-alt text-[13px] sm:text-[18px] md:text-[22px] mt-0.5 shrink-0'></i>
                    {$departHtml}
                </div>

                <div class='flex items-start justify-center gap-1 sm:gap-2 min-w-0 overflow-hidden'>
                    <i class='bi bi-geo-alt text-[13px] sm:text-[18px] md:text-[22px] mt-0.5 shrink-0'></i>
                    {$arriveeHtml}
                </div>

                <div class='flex justify-end min-w-0 overflow-hidden'>
                    {$typeHtml}
                </div>
            </div>

            <div class='grid grid-cols-[0.75fr_1fr_0.75fr] sm:grid-cols-3 items-center gap-1 sm:gap-3 md:gap-4'>
                <div class='text-left min-w-0 overflow-hidden'>
                    {$transportLabelHtml}
                </div>

                <div class='flex justify-center min-w-0 overflow-hidden'>
                    {$amenitiesHtml}
                </div>

                <div class='flex justify-end min-w-0'>
                    %s
                </div>
            </div>
        </div>
        ";

        if ($type === 'simple') {
            $action = "
                <button
                    class='continuer-btn-simple bg-green-600 hover:bg-green-700 text-white font-bold text-[9px] sm:text-[13px] md:text-[15px] px-2 sm:px-4 md:px-5 py-1.5 sm:py-2.5 transition min-w-[70px] sm:min-w-[120px] md:min-w-[130px]'
                    data-id='{$id}'
                    data-mode='{$modeTransportSafe}'
                    data-price='{$prix}'
                    data-depart='" . htmlspecialchars($villeDepart) . (!empty($quartierDepart) ? " - " . htmlspecialchars($quartierDepart) : "") . "'
                    data-arrive='" . htmlspecialchars($villeArrivee) . (!empty($quartierArrivee) ? " - " . htmlspecialchars($quartierArrivee) : "") . "'
                    data-time='{$heureDepart}'>
                    Continuer
                </button>
            ";

            echo "<div class='mb-4 sm:mb-5 w-full max-w-full overflow-hidden'>" . sprintf($cardBody, $action) . "</div>";
        } else {
            $action = "
                <button
                    class='continuer-btn bg-green-600 hover:bg-green-700 text-white font-bold text-[9px] sm:text-[13px] md:text-[15px] px-2 sm:px-4 md:px-5 py-1.5 sm:py-2.5 transition min-w-[70px] sm:min-w-[120px] md:min-w-[130px]'
                    data-id='{$id}'
                    data-type='{$type}'
                    data-mode='{$modeTransportSafe}'
                    data-price='{$prix}'
                    data-depart='" . htmlspecialchars($villeDepart) . (!empty($quartierDepart) ? " - " . htmlspecialchars($quartierDepart) : "") . "'
                    data-arrive='" . htmlspecialchars($villeArrivee) . (!empty($quartierArrivee) ? " - " . htmlspecialchars($quartierArrivee) : "") . "'
                    data-time='{$heureDepart}'>
                    Continuer
                </button>
            ";

            echo "<div id='conteneur-{$type}-{$id}' class='mb-4 sm:mb-5 voyage-card transition-all duration-200 w-full max-w-full overflow-hidden'>" . sprintf($cardBody, $action) . "</div>";
        }
    }
    ?>

    <div class="max-w-7xl mx-auto px-3 sm:px-4 py-5 sm:py-8 overflow-x-hidden">
        <div class="flex flex-col lg:flex-row gap-5 sm:gap-8 items-start">
            <main class="flex-1 min-w-0 order-1 w-full max-w-full overflow-hidden">
                <?php include 'includes/type_trajet.php'; ?>

                <?php if ($allerSimpleSelected): ?>
                    <?php
                    $voyagesDisponiblesAller = 0;
                    $voyagesAller = [];

                    if (!empty($Depart) && !empty($Arrivee) && !empty($date)) {
                        $voyagesDisponiblesAller = countVoyages($bdd, $Depart, $Arrivee, $date, 'allerCount');
                        $voyagesAller = fetchVoyages($bdd, $Depart, $Arrivee, $date, 'allerFetch');
                    }
                    ?>

                    <div class="flex flex-row items-center justify-between gap-2 mb-4 sm:mb-8 w-full max-w-full overflow-hidden">
                        <p class="text-[12px] min-[390px]:text-[13px] sm:text-[18px] md:text-xl font-bold text-green-600 whitespace-nowrap truncate">
                            Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                        </p>

                        <p class="text-[12px] min-[390px]:text-[13px] sm:text-[18px] md:text-xl font-bold text-slate-800 whitespace-nowrap text-right truncate">
                            <?php echo $voyagesDisponiblesAller; ?> voyages disponibles
                        </p>
                    </div>

                    <?php if (!empty($Depart) && !empty($Arrivee) && !empty($date)): ?>
                        <?php if (!empty($voyagesAller)): ?>
                            <?php foreach ($voyagesAller as $donne): ?>
                                <?php renderVoyageCard($donne, 'simple'); ?>
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

                    <div class="flex flex-row items-center justify-between gap-2 mb-4 sm:mb-8 w-full max-w-full overflow-hidden">
                        <h2 class="text-[12px] min-[390px]:text-[13px] sm:text-[18px] md:text-xl font-bold text-green-600 whitespace-nowrap truncate">
                            Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                        </h2>

                        <p class="text-[12px] min-[390px]:text-[13px] sm:text-[18px] md:text-xl font-bold text-slate-800 whitespace-nowrap text-right truncate">
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

                    <div class="mt-8 sm:mt-12 mb-4 sm:mb-8 flex flex-row items-center justify-between gap-2 w-full max-w-full overflow-hidden">
                        <h2 class="text-[12px] min-[390px]:text-[13px] sm:text-[18px] md:text-xl font-bold text-green-600 whitespace-nowrap truncate">
                            Retour : <?php echo !empty($dateRetour) ? date("d M Y", strtotime($dateRetour)) : ''; ?>
                        </h2>

                        <p class="text-[12px] min-[390px]:text-[13px] sm:text-[18px] md:text-xl font-bold text-slate-800 whitespace-nowrap text-right truncate">
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
        /*
            Aller simple :
            - Bus => recap.php
            - Covoiturage => covoiturage/recap-covoiturage.php
        */
        document.querySelectorAll('.continuer-btn-simple').forEach(button => {
            button.addEventListener('click', function() {
                const modeTransport = this.dataset.mode || 'bus';

                if (modeTransport === 'covoiturage') {
                    const params = new URLSearchParams({
                        idVoyage: this.dataset.id
                    });

                    window.location.href = 'covoiturage/recap-covoiturage.php?' + params.toString();
                    return;
                }

                const params = new URLSearchParams({
                    idVoyageAller: this.dataset.id,
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

        /*
            Aller-retour :
            - Bus => sélection aller + retour puis recap.php
            - Covoiturage => on redirige vers le processus covoiturage simple
        */
        document.addEventListener('DOMContentLoaded', function() {
            let selectedTrips = {
                aller: null,
                retour: null
            };

            document.querySelectorAll('.continuer-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    const type = this.dataset.type;
                    const modeTransport = this.dataset.mode || 'bus';

                    if (modeTransport === 'covoiturage') {
                        alert("Pour le covoiturage, veuillez réserver un trajet à la fois.");

                        const params = new URLSearchParams({
                            idVoyage: this.dataset.id
                        });

                        window.location.href = 'covoiturage/recap-covoiturage.php?' + params.toString();
                        return;
                    }

                    const parentCard = this.closest('[id^="conteneur-"]');

                    if (!parentCard) return;

                    const cardId = parentCard.id;
                    const selected = selectedTrips[type];

                    if (selected) {
                        const previousCard = document.getElementById(selected.cardId);

                        if (previousCard) {
                            const previousBox = previousCard.querySelector('div.bg-white');

                            if (previousBox) {
                                previousBox.classList.remove('border-green-500', 'bg-green-50');
                                previousBox.classList.add('border-transparent');
                            }
                        }
                    }

                    selectedTrips[type] = {
                        cardId: cardId,
                        voyageId: this.dataset.id,
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
                            idVoyageAller: selectedTrips.aller.voyageId,
                            idVoyageRetour: selectedTrips.retour.voyageId,
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