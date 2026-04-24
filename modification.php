<?php
require_once __DIR__ . '/config.php';

try {

    if (
        isset($_POST["type_bus"], $_POST["heure_arrivee"], $_POST["heure_depart"],
              $_POST["ville_depart"], $_POST["ville_arrivee"], $_POST["prix"], $_POST["id_voyage"])
    ) {

        $id_voyage = (int) $_POST["id_voyage"];
        $bus = trim($_POST["type_bus"]);
        $arrivee = trim($_POST["heure_arrivee"]);
        $heureDepart = trim($_POST["heure_depart"]);
        $Depart = trim($_POST["ville_depart"]);
        $destination = trim($_POST["ville_arrivee"]);
        $prix = (float) $_POST["prix"];

        $requete = "
            UPDATE voyage 
            SET 
                villeDepart = :ville_depart,
                villeArrivee = :ville_arrivee,
                typeBus = :type_bus,
                prix = :prix,
                heureDepart = :heure_depart,
                heureArrivee = :heure_arrivee
            WHERE idVoyage = :id_voyage
        ";

        $statement = $pdo->prepare($requete);
        $statement->bindValue(':ville_depart', $Depart);
        $statement->bindValue(':ville_arrivee', $destination);
        $statement->bindValue(':type_bus', $bus);
        $statement->bindValue(':prix', $prix);
        $statement->bindValue(':heure_depart', $heureDepart);
        $statement->bindValue(':heure_arrivee', $arrivee);
        $statement->bindValue(':id_voyage', $id_voyage, PDO::PARAM_INT);

        if ($statement->execute()) {

            echo "<meta http-equiv='refresh' content='2;url=listevoyadmin.php'>";
            echo "<div style='height: 100px; width: 600px; background-color: green; color: white;
                font-size: 25px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px;'>
                Modification effectuée avec succès.
            </div>";
        } else {
            echo "Erreur lors de la modification.";
        }

        exit;
    } else {
        echo "Données manquantes.";
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>