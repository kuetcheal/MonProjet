<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="#" method="POST">
        <h3>Veuillez insérer une destination de voyage</h3>
        <br>
        <hr><br>

        <div>
            <label>Destination pour un trajet:</label>
            <input type="text" class="date-input" name="destination">
        </div><br>
        <div class="bouton">
            <div><input type="submit" id="ins" value="insérer" name="submit"></div>
            <div><input type="reset" id="annu" value="Annuler"></div>
        </div><br><br>
    </form>
    <br>
    <h1>Le plaisir de bien voyager à moindre coût</h1>
    <?php
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['submit']) && !empty($_POST['destination'])) {
            $destination = $_POST['destination'];

            // Préparer la requête pour éviter les injections SQL
            $requete = $bdd->prepare('INSERT INTO destination (Nom_ville) VALUES (:destination)');
            $requete->bindParam(':destination', $destination);

            // Exécuter la requête
            $requete->execute();

            echo ("<meta http-equiv='refresh' content='5;url=listevoyadmin.php'>");
            echo("<div style='height: 100px; width: 600px; background-color: green; color: white;
 font-size: 25px; padding: 30px; text-align: center; position: fixed; top: 150px; '>
 Voyage insérer avec succès.
</div>");
        }
    } catch (Exception $e) {
        echo 'Échec de connexion : '.$e->getMessage();
    }
    ?>


    <style>
    body {
        font-family: Arial, sans-serif;
        /* Police moderne */
        background-color: #f4f4f9;
        /* Couleur de fond légère */
        margin: 40px;
        color: #333;

    }

    form {
        background: #ffffff;
        /* Arrière-plan blanc pour le formulaire */
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        max-width: 500px;
        margin: auto;

    }

    h3 {
        text-align: center;
        color: green;
    }

    h1 {
        text-align: center;
        color: green;
    }

    label {
        display: block;
        /* Labels sur leur propre ligne */
        margin-bottom: 10px;
        /* Espace sous les labels */
        font-weight: bold;

    }

    input[type="text"],
    input[type="submit"],
    input[type="reset"] {
        width: 100%;
        /* Largeur complète */
        padding: 10px;
        /* Padding confortable */
        margin-top: 6px;
        /* Marge au-dessus du champ */
        margin-bottom: 16px;
        /* Marge en dessous du champ */
        border: 1px solid #ccc;
        /* Bordure subtile */
        border-radius: 4px;
        /* Bordures légèrement arrondies */
    }

    input[type="text"] {
        width: 95%;
        /* Largeur complète */
        padding: 10px;
        /* Padding confortable */
        margin-top: 6px;
        /* Marge au-dessus du champ */
        margin-bottom: 16px;
        /* Marge en dessous du champ */
        border: 1px solid #ccc;
        /* Bordure subtile */
        border-radius: 4px;
        /* Bordures légèrement arrondies */
    }

    input[type="submit"] {
        background-color: green;
        /* Couleur de fond des boutons */
        color: white;
        /* Texte blanc */
        font-size: 16px;
        /* Taille du texte */
        cursor: pointer;
        /* Curseur de pointage */
    }


    input[type="reset"] {
        background-color: red;
        /* Couleur de fond des boutons */
        color: white;
        /* Texte blanc */
        font-size: 16px;
        /* Taille du texte */
        cursor: pointer;
        /* Curseur de pointage */
    }

    input[type="submit"]:hover,
    input[type="reset"]:hover {
        background-color: #4a54e1;
        /* Couleur de fond au survol */
    }

    .bouton>div {
        margin-top: 10px;
        display: inline-block;
        width: 49%;
        /* Utilisation de presque la moitié de la largeur pour aligner les boutons côte à côte */
    }

    hr {
        border: none;
        /* Suppression de la bordure */
        height: 1px;
        /* Hauteur de la ligne */
        background-color: #ccc;
        /* Couleur de la ligne */
        margin-top: 0;
        /* Ajustement des marges */
        margin-bottom: 20px;
        /* Ajustement des marges */
    }
    </style>
</body>

</html>