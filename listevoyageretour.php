<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>Document</title>
</head>

<body class="bg-white-100">
  <?php include 'includes/header.php'; ?>
    <div class="max-w-7xl mx-auto px-4 py-6">
        <?php include 'filtre_secondaire.php'; ?>

        <?php
        try {
            $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
            $Depart = $_POST['input1'] ?? '';
            $Arrivee = $_POST['input2'] ?? '';
            $date = $_POST['input3'] ?? '';
            $dateRetour = $_POST['input4'] ?? '';

            $_SESSION['depart'] = $Depart;
            $_SESSION['arrivee'] = $Arrivee;
            $_SESSION['date'] = $date;
            $_SESSION['dateretour'] = $dateRetour;

            $countQuery = $bdd->prepare('SELECT COUNT(*) as count FROM voyage WHERE villeDepart = :depart AND villeArrivee = :arrivee AND jourDepart = :date');
            $countQuery->execute([
                'depart' => $Depart,
                'arrivee' => $Arrivee,
                'date' => $date,
            ]);
            $voyagesDisponibles = $countQuery->fetch()['count'];

            echo "<div class='flex flex-col sm:flex-row justify-between items-center gap-4 my-6'>
                    <button class='flex items-center gap-2 bg-gray-200 px-4 py-2 rounded text-gray-800'><i class='fa fa-sliders'></i> Filtre</button>
                    <h2 class='text-xl font-semibold'>{$voyagesDisponibles} voyages disponibles</h2>
                  </div>";
        } catch (Exception $e) {
            echo '<p class="text-red-600">Échec de connexion à la base de données.</p>';
        }

        $allerSimpleSelected = ($_POST['inlineRadioOptions'] ?? '') === 'option1';
        $allerRetourSelected = ($_POST['inlineRadioOptions'] ?? '') === 'option2';

        if ($allerSimpleSelected) {
            echo "<h2 class='text-center text-green-600 text-2xl font-bold my-6'>Aller: " . date("d M Y", strtotime($date)) . "</h2>";
            $requette1 = "SELECT * FROM voyage WHERE villeDepart='$Depart' AND villeArrivee='$Arrivee' AND jourDepart='$date'";
            $query = $bdd->query($requette1);

            while ($donne = $query->fetch()) {
                $heure = $donne['heureDepart'];
                $depart = $donne['villeDepart'];
                $arrive = $donne['villeArrivee'];
                $prix = $donne['prix'];
                $bus = $donne['typeBus'];
                $heure2 = $donne['heureArrivee'];
                $idvoyage = $donne['idVoyage'];

                echo "<div class='bg-white shadow-md rounded p-4 mb-6'>
                    <div class='flex justify-between items-center'>
                        <div class='text-lg font-bold text-gray-800'>{$heure} - {$heure2}</div>
                        <div class='text-green-600 font-semibold'>{$prix} FCFA</div>
                    </div>
                    <div class='flex justify-between items-center text-sm text-gray-600 my-2'>
                        <div><i class='bi bi-geo-alt'></i> $depart</div>
                        <div><i class='bi bi-geo-alt'></i> $arrive</div>
                        <div><i class='fa fa-bus'></i> $bus</div>
                    </div>
                    <div class='flex justify-between items-center'>
                        <button class='text-blue-600 hover:underline'>Détails du trajet</button>
                        <div class='space-x-2 text-gray-500'>
                            <i class='fa fa-wifi'></i>
                            <i class='fa fa-television'></i>
                            <i class='fa fa-beer'></i>
                        </div>
                        <form method='post' action='payment.php'>
                            <input type='hidden' name='idVoyage' value='$idvoyage'>
                            <input type='submit' value='Continuer' class='bg-green-600 text-white px-4 py-1 rounded'>
                        </form>
                    </div>
                </div>";
            }
        }

        if ($allerRetourSelected) {
            echo "<h2 class='text-center text-green-600 text-2xl font-bold my-6'>Aller: " . date("d M Y", strtotime($date)) . "</h2>";
            $requetteAller = "SELECT * FROM voyage WHERE villeDepart='$Depart' AND villeArrivee='$Arrivee' AND jourDepart='$date'";
            $query = $bdd->query($requetteAller);

            while ($donne = $query->fetch()) {
                $heure = $donne['heureDepart'];
                $depart = $donne['villeDepart'];
                $arrive = $donne['villeArrivee'];
                $prixaller = $donne['prix'];
                $bus = $donne['typeBus'];
                $heure2 = $donne['heureArrivee'];
                $idvoyage = $donne['idVoyage'];

                echo "<div class='bg-white shadow-md rounded p-4 mb-6'>
                    <div class='flex justify-between items-center'>
                        <div class='text-lg font-bold text-gray-800'>{$heure} - {$heure2}</div>
                        <div class='text-green-600 font-semibold'>{$prixaller} FCFA</div>
                    </div>
                    <div class='flex justify-between items-center text-sm text-gray-600 my-2'>
                        <div><i class='bi bi-geo-alt'></i> $depart</div>
                        <div><i class='bi bi-geo-alt'></i> $arrive</div>
                        <div><i class='fa fa-bus'></i> $bus</div>
                    </div>
                    <div class='flex justify-between items-center'>
                        <button class='text-blue-600 hover:underline'>Détails du trajet</button>
                        <div class='space-x-2 text-gray-500'>
                            <i class='fa fa-wifi'></i>
                            <i class='fa fa-television'></i>
                            <i class='fa fa-beer'></i>
                        </div>
                        <button class='continuer-btn bg-green-600 text-white px-4 py-1 rounded'
                                data-id='$idvoyage'
                                data-type='aller'
                                data-price='$prixaller'
                                data-depart='$depart'
                                data-arrive='$arrive'
                                data-time='$heure'>
                            Continuer
                        </button>
                    </div>
                </div>";
            }

            echo "<h2 class='text-center text-green-600 text-2xl font-bold my-6'>Retour: " . date("d M Y", strtotime($dateRetour)) . "</h2>";
            $requetteRetour = "SELECT * FROM voyageretour WHERE villeRetour='$Depart' AND arriver='$Arrivee' AND jourPartir='$dateRetour'";
            $queryRetour = $bdd->query($requetteRetour);

            if ($queryRetour && $queryRetour->rowCount() > 0) {
                while ($donne = $queryRetour->fetch()) {
                    $depart1 = $donne['villeRetour'];
                    $arrive2 = $donne['arriver'];
                    $heure3 = $donne['heurePartir'];
                    $heure4 = $donne['heurearrive'];
                    $prixretour = $donne['prixBillet'];
                    $busretour = $donne['typebus'];
                    $idvoyageretour = $donne['idVoyageRetour'];

                    echo "<div class='bg-white shadow-md rounded p-4 mb-6'>
                        <div class='flex justify-between items-center'>
                            <div class='text-lg font-bold text-gray-800'>{$heure3} - {$heure4}</div>
                            <div class='text-green-600 font-semibold'>{$prixretour} FCFA</div>
                        </div>
                        <div class='flex justify-between items-center text-sm text-gray-600 my-2'>
                            <div><i class='bi bi-geo-alt'></i> $arrive2</div>
                            <div><i class='bi bi-geo-alt'></i> $depart1</div>
                            <div><i class='fa fa-bus'></i> $busretour</div>
                        </div>
                        <div class='flex justify-between items-center'>
                            <button class='text-blue-600 hover:underline'>Détails du trajet</button>
                            <div class='space-x-2 text-gray-500'>
                                <i class='fa fa-wifi'></i>
                                <i class='fa fa-television'></i>
                                <i class='fa fa-beer'></i>
                            </div>
                            <button class='continuer-btn bg-green-600 text-white px-4 py-1 rounded'
                                    data-id='$idvoyageretour'
                                    data-type='retour'
                                    data-price='$prixretour'
                                    data-depart='$depart1'
                                    data-arrive='$arrive2'
                                    data-time='$heure3'>
                                Continuer
                            </button>
                        </div>
                    </div>";
                }
            }
        }
        ?>
    </div>
    <?php include 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let selectedTrips = { aller: null, retour: null };
            document.querySelectorAll('.continuer-btn').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const type = this.dataset.type;
                    const id = this.closest('div[id^="conteneur"]').id;
                    const selected = selectedTrips[type];
                    if (selected) document.getElementById(selected.id).classList.remove('border-green-400', 'bg-green-50');
                    selectedTrips[type] = {
                        id,
                        price: this.dataset.price,
                        depart: this.dataset.depart,
                        arrive: this.dataset.arrive,
                        time: this.dataset.time
                    };
                    document.getElementById(id).classList.add('border-green-400', 'bg-green-50');

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
