
<?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    if (isset($_POST["type_bus"]) && isset($_POST["heure_arrivee"]) && isset($_POST["heure_depart"]) && isset($_POST["ville_depart"]) && isset($_POST["ville_arrivee"]) && isset($_POST["prix"]) && isset($_POST["id_voyage"])) {
      
        $id_voyage = $_POST["id_voyage"];
        $bus = $_POST["type_bus"];
        $arrivee = $_POST["heure_arrivee"];
        $heureDepart = $_POST["heure_depart"];
        $Depart = $_POST["ville_depart"];
        $destination = $_POST["ville_arrivee"];
        $prix = $_POST["prix"];

        
        $requete = "UPDATE voyage SET villeDepart = :ville_depart, villeArrivee = :ville_arrivee, typeBus = :type_bus, prix = :prix, heureDepart = :heure_depart, heureArrivee = :heure_arrivee WHERE idVoyage = :id_voyage";
        $statement = $bdd->prepare($requete);
        $statement->bindValue(':ville_depart', $Depart);
        $statement->bindValue(':ville_arrivee', $destination);
        $statement->bindValue(':type_bus', $bus);
        $statement->bindValue(':prix', $prix);
        $statement->bindValue(':heure_depart', $heureDepart);
        $statement->bindValue(':heure_arrivee', $arrivee);
        $statement->bindValue(':id_voyage', $id_voyage);
        $resultat = $statement->execute();

       
        echo ("<meta http-equiv='refresh' content='2;url=listevoyadmin.php'>");
        echo("<div style='height: 100px; width: 600px; background-color: green; color: white;
            font-size: 25px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px; '>
            modification effectuée avec succès.
        </div>");
        exit;
    }
} catch (Exception $e) {
    echo("echec de connexion");
}
?>
