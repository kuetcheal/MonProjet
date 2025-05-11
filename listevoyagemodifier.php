<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trajets disponibles</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body class="bg-gray-50">

<?php
session_start();
try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
    $Depart = $_POST['input1'] ?? '';
    $Arrivee = $_POST['input2'] ?? '';
    $date = $_POST['input3'] ?? '';

    $isModification = isset($_POST['modification']) && $_POST['modification'] === 'true';
    $idReservation = $_POST['id_reservation'] ?? null;

    echo "<h2 class='text-center text-xl font-bold text-green-600 my-6'>Trajets disponibles le " . date("d M Y", strtotime($date)) . "</h2>";

    $query = $bdd->prepare("SELECT * FROM voyage WHERE villeDepart = ? AND villeArrivee = ? AND jourDepart = ?");
    $query->execute([$Depart, $Arrivee, $date]);

    while ($donne = $query->fetch()) {
        $heure = $donne['heureDepart'];
        $heure2 = $donne['heureArrivee'];
        $depart = $donne['villeDepart'];
        $arrive = $donne['villeArrivee'];
        $prix = $donne['prix'];
        $bus = $donne['typeBus'];
        $idvoyage = $donne['idVoyage'];

        echo "
        <div class='bg-white shadow-md rounded-lg p-6 mb-6 max-w-4xl mx-auto border border-gray-200'>
            <div class='flex justify-between items-center'>
                <!-- Heures -->
                <div class='flex items-center text-lg font-bold text-gray-800 space-x-2'>
                    <span>{$heure}</span>
                    <span>➔</span>
                    <span>{$heure2}</span>
                </div>

                <!-- Prix -->
                <div class='text-right'>
                    <p class='text-green-600 font-semibold text-sm'>{$prix} FCFA</p>
                    <div class='text-gray-500 text-xs flex items-center justify-end space-x-1'>
                        <i class='fa fa-bus'></i>
                        <span>{$bus}</span>
                    </div>
                </div>
            </div>

            <!-- Ville départ / arrivée -->
            <div class='flex justify-between text-sm text-gray-600 mt-4'>
                <div class='flex items-center space-x-2'>
                    <i class='bi bi-geo-alt'></i><span>{$depart}</span>
                </div>
                <div class='flex items-center space-x-2'>
                    <i class='bi bi-geo-alt'></i><span>{$arrive}</span>
                </div>
            </div>

            <!-- Détails & Bouton -->
            <div class='flex justify-between items-center mt-4'>
                <button class='text-blue-600 hover:underline text-sm'>Détails du trajet</button>
                <div class='text-gray-500 text-lg space-x-2'>
                    <i class='fa fa-wifi'></i>
                    <i class='fa fa-television'></i>
                    <i class='fa fa-beer'></i>
                </div>";

        if ($isModification && $idReservation) {
            echo "
                <form method='POST' action='Reservation/valider_modification.php'>
                    <input type='hidden' name='id_reservation' value='{$idReservation}'>
                    <input type='hidden' name='idVoyage' value='{$idvoyage}'>
                    <input type='hidden' name='nom' value='" . htmlspecialchars($_POST['nom']) . "'>
                    <input type='hidden' name='prenom' value='" . htmlspecialchars($_POST['prenom']) . "'>
                    <input type='hidden' name='telephone' value='" . htmlspecialchars($_POST['telephone']) . "'>
                    <input type='hidden' name='numero_siege' value='" . htmlspecialchars($_POST['numero_siege']) . "'>
                    <button type='submit' class='bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm font-semibold'>
                        Continuer
                    </button>
                </form>";
        }

        echo "</div></div>";
    }
} catch (Exception $e) {
    echo "<p class='text-red-600 text-center'>Erreur : " . $e->getMessage() . "</p>";
}
?>

</body>
</html>
