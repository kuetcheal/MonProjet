<?php
    if (isset($_POST['id_voyage'])) {
        $id_voyage = $_POST['id_voyage'];

        // Connectez-vous à la base de données

        try {
            $conn = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
        } catch (\Throwable $th) {
            echo("Echec de connexion");
            exit();
        }

        // Effectuez la requête de récupération du voyage

        $requete_recuperation = "SELECT * FROM voyage WHERE idVoyage = :id_voyage";
        $statement = $conn->prepare($requete_recuperation);
        $statement->bindValue(':id_voyage', $id_voyage);
        $resultat = $statement->execute();

        // Vérifiez si la récupération a réussi

        if ($resultat) {
            $voyage = $statement->fetch();

            // Affichez le formulaire de modification avec les données pré-remplies

            echo("
            <div class='travel-modif'>
             <h2>Modification du trajet</h2>
             <hr style=' color: green;'><br>
                 <form method='POST' action='modification.php'>
                    <input type='hidden' name='id_voyage' value='{$voyage['idVoyage']}'>
                    Ville départ: <input type='text' name='ville_depart' value='{$voyage['villeDepart']}'><br><br>
                    Ville arrivée: <input type='text' name='ville_arrivee' value='{$voyage['villeArrivee']}'><br><br>
                    Heure départ: <input type='text' name='heure_depart' value='{$voyage['heureDepart']}'><br><br>
                    Heure arrivée: <input type='text' name='heure_arrivee' value='{$voyage['heureArrivee']}'><br><br>
                    Type de bus: <input type='text' name='type_bus' value='{$voyage['typeBus']}'><br><br>
                    Prix: <input type='text' name='prix' value='{$voyage['prix']}'><br><br>
                    <button type='submit' class='btn'>Enregistrer</button>
                 </form>
            </div>     
            ");
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