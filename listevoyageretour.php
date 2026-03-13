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

    <div class="max-w-6xl mx-auto px-4 py-8">
        <?php
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
            $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

            $voyagesDisponibles = 0;

            if (!empty($Depart) && !empty($Arrivee) && !empty($date)) {
                $countQuery = $bdd->prepare("
                    SELECT COUNT(*) as count
                    FROM voyage
                    WHERE villeDepart = :depart
                    AND villeArrivee = :arrivee
                    AND jourDepart = :date
                ");
                $countQuery->execute([
                    'depart' => $Depart,
                    'arrivee' => $Arrivee,
                    'date' => $date,
                ]);
                $voyagesDisponibles = $countQuery->fetch(PDO::FETCH_ASSOC)['count'] ?? 0;
            }
        } catch (Exception $e) {
            echo '<p class="text-red-600 text-center mt-8">Échec de connexion à la base de données.</p>';
            exit;
        }

        function renderVoyageCard($id, $heureDepart, $heureArrivee, $depart, $arrivee, $prix, $bus, $type = 'simple', $formAction = 'payment.php')
        {
            $busLabel = htmlspecialchars($bus);
            $departSafe = htmlspecialchars($depart);
            $arriveeSafe = htmlspecialchars($arrivee);
            $heureDepartSafe = htmlspecialchars($heureDepart);
            $heureArriveeSafe = htmlspecialchars($heureArrivee);
            $prixSafe = htmlspecialchars($prix);
            $idSafe = htmlspecialchars($id);

            if ($type === 'simple') {
                echo "
                <div class='max-w-5xl mx-auto mb-6'>
                    <div class='bg-white rounded-[22px] shadow-[0_8px_24px_rgba(0,0,0,0.08)] border border-gray-100 p-6 hover:shadow-[0_12px_32px_rgba(0,0,0,0.12)] transition'>
                        <div class='flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6'>
                            
                            <div class='flex-1'>
                                <div class='flex flex-wrap items-center gap-3 mb-6'>
                                    <span class='text-2xl font-extrabold text-slate-800'>{$heureDepartSafe}</span>
                                    <span class='text-gray-400 font-semibold'>—</span>
                                    <span class='text-2xl font-extrabold text-slate-800'>{$heureArriveeSafe}</span>
                                </div>

                                <div class='grid grid-cols-1 md:grid-cols-3 gap-6 mb-5'>
                                    <div>
                                        <p class='text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2'>Départ</p>
                                        <p class='text-[30px] font-bold text-slate-700 flex items-center gap-2'>
                                            <i class='bi bi-geo-alt text-green-600'></i> {$departSafe}
                                        </p>
                                    </div>

                                    <div>
                                        <p class='text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2'>Arrivée</p>
                                        <p class='text-[30px] font-bold text-slate-700 flex items-center gap-2'>
                                            <i class='bi bi-geo-alt text-green-600'></i> {$arriveeSafe}
                                        </p>
                                    </div>

                                    <div>
                                        <p class='text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2'>Type</p>
                                        <p class='text-[30px] font-bold text-slate-700 flex items-center gap-2'>
                                            <i class='fa fa-bus text-green-600'></i> {$busLabel}
                                        </p>
                                    </div>
                                </div>

                                <div class='flex flex-wrap items-center gap-4 text-gray-500'>
                                    <button type='button' class='text-blue-600 hover:underline font-medium'>Détails du trajet</button>
                                    <div class='flex items-center gap-4 text-lg'>
                                        <i class='fa fa-wifi'></i>
                                        <i class='fa fa-television'></i>
                                        <i class='fa fa-beer'></i>
                                    </div>
                                </div>
                            </div>

                            <div class='lg:w-[210px] flex flex-col items-end justify-between gap-4'>
                                <div class='text-right w-full'>
                                    <p class='text-sm text-gray-400 font-semibold'>Prix</p>
                                    <p class='text-3xl font-extrabold text-green-600'>{$prixSafe} <span class='text-lg'>FCFA</span></p>
                                </div>

                                <form method='post' action='{$formAction}' class='w-full'>
                                    <input type='hidden' name='idVoyage' value='{$idSafe}'>
                                    <input type='submit' value='Continuer' class='w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl cursor-pointer transition'>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>";
            } else {
                echo "
                <div id='conteneur-{$type}-{$idSafe}' class='max-w-5xl mx-auto mb-6 voyage-card transition-all duration-200'>
                    <div class='bg-white rounded-[22px] shadow-[0_8px_24px_rgba(0,0,0,0.08)] border-2 border-transparent p-6 hover:shadow-[0_12px_32px_rgba(0,0,0,0.12)] transition'>
                        <div class='flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6'>
                            
                            <div class='flex-1'>
                                <div class='flex flex-wrap items-center gap-3 mb-6'>
                                    <span class='text-2xl font-extrabold text-slate-800'>{$heureDepartSafe}</span>
                                    <span class='text-gray-400 font-semibold'>—</span>
                                    <span class='text-2xl font-extrabold text-slate-800'>{$heureArriveeSafe}</span>
                                </div>

                                <div class='grid grid-cols-1 md:grid-cols-3 gap-6 mb-5'>
                                    <div>
                                        <p class='text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2'>Départ</p>
                                        <p class='text-[30px] font-bold text-slate-700 flex items-center gap-2'>
                                            <i class='bi bi-geo-alt text-green-600'></i> {$departSafe}
                                        </p>
                                    </div>

                                    <div>
                                        <p class='text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2'>Arrivée</p>
                                        <p class='text-[30px] font-bold text-slate-700 flex items-center gap-2'>
                                            <i class='bi bi-geo-alt text-green-600'></i> {$arriveeSafe}
                                        </p>
                                    </div>

                                    <div>
                                        <p class='text-xs uppercase tracking-wide text-gray-400 font-semibold mb-2'>Type</p>
                                        <p class='text-[30px] font-bold text-slate-700 flex items-center gap-2'>
                                            <i class='fa fa-bus text-green-600'></i> {$busLabel}
                                        </p>
                                    </div>
                                </div>

                                <div class='flex flex-wrap items-center gap-4 text-gray-500'>
                                    <button type='button' class='text-blue-600 hover:underline font-medium'>Détails du trajet</button>
                                    <div class='flex items-center gap-4 text-lg'>
                                        <i class='fa fa-wifi'></i>
                                        <i class='fa fa-television'></i>
                                        <i class='fa fa-beer'></i>
                                    </div>
                                </div>
                            </div>

                            <div class='lg:w-[210px] flex flex-col items-end justify-between gap-4'>
                                <div class='text-right w-full'>
                                    <p class='text-sm text-gray-400 font-semibold'>Prix</p>
                                    <p class='text-3xl font-extrabold text-green-600'>{$prixSafe} <span class='text-lg'>FCFA</span></p>
                                </div>

                                <button 
                                    class='continuer-btn w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition'
                                    data-id='{$idSafe}'
                                    data-type='{$type}'
                                    data-price='{$prixSafe}'
                                    data-depart='{$departSafe}'
                                    data-arrive='{$arriveeSafe}'
                                    data-time='{$heureDepartSafe}'>
                                    Continuer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>";
            }
        }
        ?>

        <?php if ($allerSimpleSelected): ?>
            <div class="max-w-5xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8">
                <h2 class="text-3xl font-extrabold text-green-600">
                    Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                </h2>
                <p class="text-xl font-bold text-slate-800">
                    <?php echo $voyagesDisponibles; ?> voyages disponibles
                </p>
            </div>

            <?php
            $requette1 = $bdd->prepare("
                SELECT * 
                FROM voyage 
                WHERE villeDepart = :depart 
                AND villeArrivee = :arrivee 
                AND jourDepart = :date
                ORDER BY heureDepart ASC
            ");
            $requette1->execute([
                'depart' => $Depart,
                'arrivee' => $Arrivee,
                'date' => $date
            ]);

            if ($requette1->rowCount() > 0) {
                while ($donne = $requette1->fetch(PDO::FETCH_ASSOC)) {
                    renderVoyageCard(
                        $donne['idVoyage'],
                        $donne['heureDepart'],
                        $donne['heureArrivee'],
                        $donne['villeDepart'],
                        $donne['villeArrivee'],
                        $donne['prix'],
                        $donne['typeBus'],
                        'simple',
                        'payment.php'
                    );
                }
            } else {
                echo "<p class='text-center text-gray-500 text-lg mt-10'>Aucun voyage disponible pour cette recherche.</p>";
            }
            ?>
        <?php endif; ?>

        <?php if ($allerRetourSelected): ?>
            <div class="max-w-5xl mx-auto flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-8">
                <h2 class="text-3xl font-extrabold text-green-600">
                    Aller : <?php echo !empty($date) ? date("d M Y", strtotime($date)) : ''; ?>
                </h2>
                <p class="text-xl font-bold text-slate-800">
                    <?php echo $voyagesDisponibles; ?> voyages disponibles
                </p>
            </div>

            <?php
            $requetteAller = $bdd->prepare("
                SELECT * 
                FROM voyage 
                WHERE villeDepart = :depart 
                AND villeArrivee = :arrivee 
                AND jourDepart = :date
                ORDER BY heureDepart ASC
            ");
            $requetteAller->execute([
                'depart' => $Depart,
                'arrivee' => $Arrivee,
                'date' => $date
            ]);

            if ($requetteAller->rowCount() > 0) {
                while ($donne = $requetteAller->fetch(PDO::FETCH_ASSOC)) {
                    renderVoyageCard(
                        $donne['idVoyage'],
                        $donne['heureDepart'],
                        $donne['heureArrivee'],
                        $donne['villeDepart'],
                        $donne['villeArrivee'],
                        $donne['prix'],
                        $donne['typeBus'],
                        'aller'
                    );
                }
            } else {
                echo "<p class='text-center text-gray-500 text-lg mt-10 mb-10'>Aucun voyage aller disponible.</p>";
            }
            ?>

            <div class="max-w-5xl mx-auto mt-12 mb-8">
                <h2 class="text-3xl font-extrabold text-green-600">
                    Retour : <?php echo !empty($dateRetour) ? date("d M Y", strtotime($dateRetour)) : ''; ?>
                </h2>
            </div>

            <?php
            $requetteRetour = $bdd->prepare("
                SELECT * 
                FROM voyage 
                WHERE villeDepart = :departRetour
                AND villeArrivee = :arriveeRetour
                AND jourDepart = :dateRetour
                ORDER BY heureDepart ASC
            ");
            $requetteRetour->execute([
                'departRetour' => $Arrivee,
                'arriveeRetour' => $Depart,
                'dateRetour' => $dateRetour
            ]);

            if ($requetteRetour->rowCount() > 0) {
                while ($donne = $requetteRetour->fetch(PDO::FETCH_ASSOC)) {
                    renderVoyageCard(
                        $donne['idVoyage'],
                        $donne['heureDepart'],
                        $donne['heureArrivee'],
                        $donne['villeDepart'],
                        $donne['villeArrivee'],
                        $donne['prix'],
                        $donne['typeBus'],
                        'retour'
                    );
                }
            } else {
                echo "<p class='text-center text-gray-500 text-lg mt-10'>Aucun voyage retour disponible.</p>";
            }
            ?>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
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