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
        $alignClass = $align === 'center' ? 'text-center items-center' : ($align === 'right' ? 'text-right items-end' : 'text-left items-start');

        if (!empty($quartier)) {
            $quartierSafe = htmlspecialchars($quartier);
            return "
                <div class='flex flex-col {$alignClass} leading-tight'>
                    <div class='text-[18px] md:text-[20px] font-bold text-slate-700'>{$ville}</div>
                    <div class='text-[12px] text-gray-500 mt-1'>{$quartierSafe}</div>
                </div>
            ";
        }

        return "
            <div class='flex flex-col {$alignClass} leading-tight'>
                <div class='text-[18px] md:text-[20px] font-bold text-slate-700'>{$ville}</div>
            </div>
        ";
    }

    function renderAmenitiesHtml(string $bus): string
    {
        $amenities = getBusAmenities($bus);
        $html = "<div class='flex flex-wrap items-center gap-3 text-gray-500 text-sm'>";

        foreach ($amenities as $item) {
            $icon = htmlspecialchars($item['icon']);
            $label = htmlspecialchars($item['label']);
            $html .= "
                <span class='inline-flex items-center gap-1.5' title='{$label}'>
                    <i class='{$icon} text-[14px]'></i>
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
            <div class='flex items-center justify-end gap-2 text-right'>
                <i class='fa fa-bus text-green-600 text-[22px]'></i>
                <span class='text-[18px] md:text-[20px] font-bold text-slate-700'>{$busLower}</span>
            </div>
        ";

        if ($type === 'simple') {
            echo "
            <div class='mb-5'>
                <div class='bg-white rounded-[22px] shadow-[0_8px_24px_rgba(0,0,0,0.08)] border border-gray-100 px-5 py-4 hover:shadow-[0_12px_32px_rgba(0,0,0,0.12)] transition'>

                    <!-- Ligne 1 -->
                    <div class='grid grid-cols-3 items-center gap-4 mb-4'>
                        <div class='text-left'>
                            <span class='text-[20px] md:text-[22px] font-extrabold text-slate-800'>{$heureDepart}</span>
                        </div>

                        <div class='flex items-center justify-center gap-3'>
                            <span class='w-2.5 h-2.5 rounded-full bg-slate-700'></span>
                            <span class='h-[2px] w-12 md:w-20 bg-slate-300'></span>
                            <span class='text-[20px] md:text-[22px] font-extrabold text-slate-800'>{$heureArrivee}</span>
                            <span class='h-[2px] w-12 md:w-20 bg-slate-300'></span>
                            <span class='w-2.5 h-2.5 rounded-full bg-slate-700'></span>
                        </div>

                        <div class='text-right'>
                            <p class='text-[30px] leading-none font-extrabold text-green-600'>{$prix}<span class='text-[14px] ml-1'>FCFA</span></p>
                        </div>
                    </div>

                    <!-- Ligne 2 -->
                    <div class='grid grid-cols-1 md:grid-cols-3 items-start gap-4 mb-4'>
                        <div class='flex items-start gap-2'>
                            <i class='bi bi-geo-alt text-green-600 text-[22px] mt-0.5'></i>
                            {$departHtml}
                        </div>

                        <div class='flex items-start justify-center gap-2'>
                            <i class='bi bi-geo-alt text-green-600 text-[22px] mt-0.5'></i>
                            {$arriveeHtml}
                        </div>

                        <div class='flex justify-end'>
                            {$typeHtml}
                        </div>
                    </div>

                    <!-- Ligne 3 -->
                    <div class='grid grid-cols-1 md:grid-cols-3 items-center gap-4'>
                        <div class='text-left'>
                            <button
                                type='button'
                                class='text-blue-600 hover:underline font-medium text-[15px]'
                                onclick=\"toggleDetails('{$detailsId}')\">
                                Détails du trajet
                            </button>
                        </div>

                        <div class='flex justify-center'>
                            {$amenitiesHtml}
                        </div>

                        <div class='flex justify-end'>
                            <form method='post' action='{$formAction}' class='w-full md:w-auto'>
                                <input type='hidden' name='idVoyage' value='{$id}'>
                                <input type='submit' value='Continuer' class='bg-green-600 hover:bg-green-700 text-white font-bold text-[15px] px-4 py-2.5 cursor-pointer transition min-w-[180px]'>
                            </form>
                        </div>
                    </div>

                    <div id='{$detailsId}' class='hidden mt-4 pt-4 border-t border-gray-200'>
                        <div class='grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600'>
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
            </div>";
        } else {
            echo "
            <div id='conteneur-{$type}-{$id}' class='mb-5 voyage-card transition-all duration-200'>
                <div class='bg-white  shadow-md border-2 border-transparent px-5 py-4 hover:shadow-[0_12px_32px_rgba(0,0,0,0.12)] transition'>

                    <!-- Ligne 1 -->
                    <div class='grid grid-cols-3 items-center gap-4 mb-4'>
                        <div class='text-left'>
                            <span class='text-[20px] md:text-[22px] font-extrabold text-slate-800'>{$heureDepart}</span>
                        </div>

                        <div class='flex items-center justify-center gap-3'>
                            <span class='w-2.5 h-2.5 rounded-full bg-slate-700'></span>
                            <span class='h-[2px] w-12 md:w-20 bg-slate-300'></span>
                            <span class='text-[20px] md:text-[22px] font-extrabold text-slate-800'>{$heureArrivee}</span>
                            <span class='h-[2px] w-12 md:w-20 bg-slate-300'></span>
                            <span class='w-2.5 h-2.5 rounded-full bg-slate-700'></span>
                        </div>

                        <div class='text-right'>
                            <p class='text-[20px] leading-none font-extrabold text-black'>{$prix}<span class='text-[14px] ml-1'>FCFA</span></p>
                        </div>
                    </div>

                    <!-- Ligne 2 -->
                    <div class='grid grid-cols-1 md:grid-cols-3 items-start gap-4 mb-4'>
                        <div class='flex items-start gap-2'>
                            <i class='bi bi-geo-alt  text-[22px] mt-0.5'></i>
                            {$departHtml}
                        </div>

                        <div class='flex items-start justify-center gap-2'>
                            <i class='bi bi-geo-alt  text-[22px] mt-0.5'></i>
                            {$arriveeHtml}
                        </div>

                        <div class='flex justify-end'>
                            {$typeHtml}
                        </div>
                    </div>

                    <!-- Ligne 3 -->
                    <div class='grid grid-cols-1 md:grid-cols-3 items-center gap-4'>
                        <div class='text-left'>
                            <button
                                type='button'
                                class='text-blue-600 hover:underline font-medium text-[15px]'
                                onclick=\"toggleDetails('{$detailsId}')\">
                                Détails du trajet
                            </button>
                        </div>

                        <div class='flex justify-center'>
                            {$amenitiesHtml}
                        </div>

                        <div class='flex justify-end'>
                            <button
                                class='continuer-btn bg-green-600 hover:bg-green-700 text-white font-bold text-[15px] px-5 py-2.5  transition min-w-[130px]'
                                data-id='{$id}'
                                data-type='{$type}'
                                data-price='{$prix}'
                                data-depart='" . htmlspecialchars($villeDepart) . (!empty($quartierDepart) ? " - " . htmlspecialchars($quartierDepart) : "") . "'
                                data-arrive='" . htmlspecialchars($villeArrivee) . (!empty($quartierArrivee) ? " - " . htmlspecialchars($quartierArrivee) : "") . "'
                                data-time='{$heureDepart}'>
                                Continuer
                            </button>
                        </div>
                    </div>

                    <div id='{$detailsId}' class='hidden mt-4 pt-4 border-t border-gray-200'>
                        <div class='grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-slate-600'>
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
            </div>";
        }
    }
    ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            <aside class="w-full lg:w-[290px] shrink-0">
                <?php include 'includes/voyage_filtre.php'; ?>
            </aside>

            <main class="flex-1 min-w-0">
                <?php if ($allerSimpleSelected): ?>
                    <?php
                    $voyagesDisponiblesAller = 0;
                    $voyagesAller = [];

                    if (!empty($Depart) && !empty($Arrivee) && !empty($date)) {
                        $voyagesDisponiblesAller = countVoyages($bdd, $Depart, $Arrivee, $date, 'allerCount');
                        $voyagesAller = fetchVoyages($bdd, $Depart, $Arrivee, $date, 'allerFetch');
                    }
                    ?>

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8">
                        <p class="text-xl font-bold text-green-600">
                            Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                        </p>
                        <p class="text-xl font-bold text-slate-800">
                            <?php echo $voyagesDisponiblesAller; ?> voyages disponibles
                        </p>
                    </div>

                    <?php if (!empty($Depart) && !empty($Arrivee) && !empty($date)): ?>
                        <?php if (!empty($voyagesAller)): ?>
                            <?php foreach ($voyagesAller as $donne): ?>
                                <?php renderVoyageCard($donne, 'simple', 'payment.php'); ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500 text-lg mt-10">Aucun voyage disponible pour cette recherche.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 text-lg mt-10">Veuillez lancer une recherche de trajet.</p>
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

                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8">
                        <h2 class="text-xl font-bold ">
                            Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                        </h2>
                        <p class="text-xl font-bold text-slate-800">
                            <?php echo $voyagesDisponiblesAller; ?> voyages disponibles
                        </p>
                    </div>

                    <?php if (!empty($voyagesAller)): ?>
                        <?php foreach ($voyagesAller as $donne): ?>
                            <?php renderVoyageCard($donne, 'aller'); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 text-lg mt-10 mb-10">Aucun voyage aller disponible.</p>
                    <?php endif; ?>

                    <?php
                    $voyagesDisponiblesRetour = 0;
                    $voyagesRetour = [];

                    if (!empty($Depart) && !empty($Arrivee) && !empty($dateRetour)) {
                        $voyagesDisponiblesRetour = countVoyages($bdd, $Arrivee, $Depart, $dateRetour, 'retourCountRT');
                        $voyagesRetour = fetchVoyages($bdd, $Arrivee, $Depart, $dateRetour, 'retourFetchRT');
                    }
                    ?>

                    <div class="mt-12 mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <h2 class="text-xl font-bold ">
                            Retour : <?php echo !empty($dateRetour) ? date("d M Y", strtotime($dateRetour)) : ''; ?>
                        </h2>
                        <p class="text-xl font-bold text-slate-800">
                            <?php echo $voyagesDisponiblesRetour; ?> voyages disponibles
                        </p>
                    </div>

                    <?php if (!empty($voyagesRetour)): ?>
                        <?php foreach ($voyagesRetour as $donne): ?>
                            <?php renderVoyageCard($donne, 'retour'); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500 text-lg mt-10">Aucun voyage retour disponible.</p>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
        function toggleDetails(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.toggle('hidden');
        }

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