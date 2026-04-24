<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

$bdd = $pdo;

$Depart = trim($_POST['input1'] ?? $_SESSION['depart'] ?? '');
$Arrivee = trim($_POST['input2'] ?? $_SESSION['arrivee'] ?? '');
$date = trim($_POST['input3'] ?? $_SESSION['date'] ?? '');
$transport = trim($_GET['transport'] ?? 'tout');

$typeBusFilter = trim($_GET['typeBus'] ?? '');
$trancheHoraire = trim($_GET['trancheHoraire'] ?? '');
$prixMax = trim($_GET['prixMax'] ?? '');
$tri = trim($_GET['tri'] ?? 'heure_asc');

function buildTransportFilterConditionsForTabs(array &$params, string $prefix = 'tab')
{
    global $typeBusFilter, $trancheHoraire, $prixMax;

    $conditions = [];

    if (!empty($typeBusFilter)) {
        $conditions[] = "typeBus = :typeBus{$prefix}";
        $params[":typeBus{$prefix}"] = $typeBusFilter;
    }

    if ($prixMax !== '' && is_numeric($prixMax)) {
        $conditions[] = "prix <= :prixMax{$prefix}";
        $params[":prixMax{$prefix}"] = (float) $prixMax;
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

function countByTransport(PDO $bdd, string $depart, string $arrivee, string $date, ?string $modeTransport = null, string $prefix = 'tab')
{
    if (empty($depart) || empty($arrivee) || empty($date)) {
        return 0;
    }

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

    if ($modeTransport !== null) {
        $conditions[] = "modeTransport = :modeTransport{$prefix}";
        $params[":modeTransport{$prefix}"] = $modeTransport;
    }

    $conditions = array_merge($conditions, buildTransportFilterConditionsForTabs($params, $prefix));

    $sql = "SELECT COUNT(*) FROM voyage WHERE " . implode(' AND ', $conditions);
    $stmt = $bdd->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

$totalCount = countByTransport($bdd, $Depart, $Arrivee, $date, null, 'all');
$covoiturageCount = countByTransport($bdd, $Depart, $Arrivee, $date, 'covoiturage', 'carpool');
$busCount = countByTransport($bdd, $Depart, $Arrivee, $date, 'bus', 'bus');

$queryParams = $_GET;
?>

<style>
    .trip-type-tabs-wrapper {
        margin-bottom: 22px;
    }

    .trip-type-tabs-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
    }

    .trip-type-tabs {
        display: flex;
        align-items: stretch;
        width: 100%;
        overflow-x: auto;
        scrollbar-width: none;
    }

    .trip-type-tabs::-webkit-scrollbar {
        display: none;
    }

    .trip-type-tab {
        position: relative;
        flex: 1 1 0;
        min-width: 180px;
        text-decoration: none;
        color: #475569;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        font-weight: 700;
        font-size: 17px;
        transition: all 0.2s ease;
        background: #fff;
        border-right: 1px solid #f1f5f9;
    }

    .trip-type-tab:last-child {
        border-right: none;
    }

    .trip-type-tab:hover {
        background: #f8fafc;
        color: #0f172a;
    }

    .trip-type-tab.active {
        color: #0f172a;
        background: #ffffff;
    }

    .trip-type-tab.active::after {
        content: "";
        position: absolute;
        left: 18px;
        right: 18px;
        bottom: 0;
        height: 3px;
        border-radius: 999px;
        background: #156f3e;
    }

    .trip-type-tab i {
        font-size: 24px;
        line-height: 1;
        color: #475569;
    }

    .trip-type-tab.active i {
        color: #156f3e;
    }

    .trip-type-count {
        color: #64748b;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .trip-type-tab {
            min-width: 150px;
            padding: 16px 18px;
            font-size: 15px;
        }

        .trip-type-tab i {
            font-size: 20px;
        }
    }
</style>

<div class="trip-type-tabs-wrapper">
    <div class="trip-type-tabs-card">
        <div class="trip-type-tabs">
            <?php
            $tabs = [
                [
                    'key' => 'tout',
                    'label' => 'Tout',
                    'icon' => 'bi bi-grid-1x2-fill',
                    'count' => $totalCount,
                ],
                [
                    'key' => 'covoiturage',
                    'label' => 'Covoiturage',
                    'icon' => 'bi bi-car-front-fill',
                    'count' => $covoiturageCount,
                ],
                [
                    'key' => 'bus',
                    'label' => 'Bus',
                    'icon' => 'bi bi-bus-front-fill',
                    'count' => $busCount,
                ],
            ];

            foreach ($tabs as $tab) {
                $params = $queryParams;
                $params['transport'] = $tab['key'];
                $url = '?' . http_build_query($params);
                $isActive = ($transport === $tab['key']) || ($transport === '' && $tab['key'] === 'tout');
                ?>
                <a
                    href="<?php echo htmlspecialchars($url); ?>"
                    class="trip-type-tab <?php echo $isActive ? 'active' : ''; ?>">
                    <i class="<?php echo htmlspecialchars($tab['icon']); ?>"></i>
                    <span>
                        <?php echo htmlspecialchars($tab['label']); ?>
                        <span class="trip-type-count">· <?php echo (int) $tab['count']; ?></span>
                    </span>
                </a>
            <?php } ?>
        </div>
    </div>
</div>