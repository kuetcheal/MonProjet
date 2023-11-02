<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php
    if (isset($_POST['id_voyage'])) {
        $id_voyage = $_POST['id_voyage'];

        try {
            $conn = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
        } catch (\Throwable $th) {
            echo("Echec de connexion");
            exit();
        }

        $requete_suppression = "DELETE FROM voyage WHERE idVoyage = :id_voyage";
        $statement = $conn->prepare($requete_suppression);
        $statement->bindValue(':id_voyage', $id_voyage);
        $resultat = $statement->execute();

        if ($resultat) {
            echo ("<meta http-equiv='refresh' content='5;url=listevoyadmin.php'>");
            echo("<div style='height: 100px; width: 600px; background-color: green; color: white;
 font-size: 25px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px; '>
 Voyage supprimé avec succès.
</div>");
        } else {
            echo("Erreur lors de la suppression du voyage.");
        }
        
        $conn = null;
        

        
    } else {
        echo("ID du voyage non spécifié.");
    }
    exit; 
?>


</body>
</html>









