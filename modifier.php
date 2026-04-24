<?php
require_once __DIR__ . '/config.php';

if (isset($_POST['id_voyage'])) {
    $id_voyage = (int) $_POST['id_voyage'];

    $requete_recuperation = "SELECT * FROM voyage WHERE idVoyage = :id_voyage LIMIT 1";
    $statement = $pdo->prepare($requete_recuperation);
    $statement->bindValue(':id_voyage', $id_voyage, PDO::PARAM_INT);
    $statement->execute();

    $voyage = $statement->fetch(PDO::FETCH_ASSOC);

    if ($voyage) {
        echo "
        <div class='travel-modif'>
            <h2>Modification du trajet</h2>
            <hr style='color: green;'><br>

            <form method='POST' action='modification.php'>
                <input type='hidden' name='id_voyage' value='" . htmlspecialchars($voyage['idVoyage']) . "'>

                Ville départ:
                <input type='text' name='ville_depart' value='" . htmlspecialchars($voyage['villeDepart']) . "'><br><br>

                Ville arrivée:
                <input type='text' name='ville_arrivee' value='" . htmlspecialchars($voyage['villeArrivee']) . "'><br><br>

                Heure départ:
                <input type='text' name='heure_depart' value='" . htmlspecialchars($voyage['heureDepart']) . "'><br><br>

                Heure arrivée:
                <input type='text' name='heure_arrivee' value='" . htmlspecialchars($voyage['heureArrivee']) . "'><br><br>

                Type de bus:
                <input type='text' name='type_bus' value='" . htmlspecialchars($voyage['typeBus']) . "'><br><br>

                Prix:
                <input type='text' name='prix' value='" . htmlspecialchars($voyage['prix']) . "'><br><br>

                <button type='submit' class='btn'>Enregistrer</button>
            </form>
        </div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <style>
    .travel-modif {
        margin: 0 auto;
        background-color: green;
        padding: 10px;
        height: 500px;
        width: 400px;
    }

    h2 {
        text-align: center;
        color: white;
    }

    input {
        justify-content: space-between;
        margin-left: 15px;
        width: 250px;
        height: 35px;
        border-radius: 7px
    }

    .btn {
        background-color: rgb(128, 128, 128);
        color: white;
        height: 30px;
        width: 180px;
        border: 1px solid rgb(128, 128, 128);
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-left: 105px;
    }

    .btn:hover {
        background-color: rgb(150, 150, 150);
    }
    </style>
</body>

</html>