<?php
session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <title>Tableau moderne</title>
</head>

<body>
    <header>
        <nav>
            <div class="header-picture">
                <img src="logo général.jpg" alt="logo site" />
            </div>
            <div class="nav-bar">
                <ul>
                    <li class="items">
                        <select id="select" name="select" aria-placeholder="2 places">
                            <option value="option1">Français</option>
                            <option value="option2">Anglais</option>

                        </select>
                    </li>
                    <li class="outils"><a href="#"><i class="fa fa-user-circle-o fa-6x" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>
    <br>

    <!-- POPUP DE SUPPRESSION D'UN VOYAGE -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Suppression du trajet</h2>
            <p>Êtes-vous sûr de vouloir supprimer ce voyage ?</p>

            <form method="POST" action="">
                <input type="hidden" id="id_voyage" name="id_voyage" value="">
                <button type="submit" name="delete_voyage">Supprimer</button>
            </form>
        </div>
    </div>

    <!-- CODE PHP POUR LA SUPPRESSION D'UN VOYAGE' -->
    <?php
// Vérifier si le formulaire de suppression a été soumis
if (isset($_POST['delete_voyage'])) {
  
    $host = "localhost"; // nom d'hôte
    $user = "root"; // nom d'utilisateur
    $password = ""; // mot de passe
    $database = "bd_stock"; // nom de la base de données

    // Connexion à la base de données MySQLi
    $conn = mysqli_connect($host, $user, $password, $database);
    $id = $_POST['id_voyage'];
    $query = "DELETE FROM voyage WHERE idVoyage =$id";
    $result = mysqli_query($conn, $query);
 
    if ($result) {
      echo ("<meta http-equiv='refresh' content='3;url=listevoyadmin.php'>");
      echo("<div style='height: 100px; width: 600px; background-color: green; color: white;
font-size: 25px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px; '>
Voyage supprimé avec succès.
</div>");
  } else {
      echo("Erreur lors de la suppression du voyage.");
  }
    header('Location: listevoyadmin.php');
    mysqli_close($conn);
    exit;
}
?>


    <?php
   try {
    $conn = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    echo("
        <table class='my-table'>
        <caption>liste des trajets disponibles</caption>
        <tr>
            <th scope='col'>Id voyage</th>
            <th scope='col'>Ville départ</th>
            <th scope='col'>Ville arrivée</th>
            <th scope='col'>heure départ</th>
            <th scope='col'>heure arrivée</th> 
            <th scope='col'>type de bus</th>
            <th scope='col'>Prix</th>
            <th scope='col'>Actions</th>
        </tr>");
    $requette ="select * From voyage";
   $resultat = $conn->query($requette);


   while($donne=$resultat->fetch()){
   
    $heure=$donne["heureDepart"] ;
    $depart=$donne["villeDepart"];
    $arrive=$donne["villeArrivee"];
    $price=$donne["prix"];
    $bus=$donne["typeBus"];
    $heure2=$donne["heureArrivee"];
    $idvoyage=$donne["idVoyage"];

    echo("
    <tr>
        <td>$idvoyage</td>
        <td>$depart</td>
        <td>$arrive</td>
        <td>$heure</td>
        <td>$heure2</td> 
        <td>$bus</td>
        <td>$price</td>  
        <td rowspan=''>
            <div class='colonne-divisee'>
                <form method='post' action='modifier.php'>
                    <input type='hidden' name='id_voyage' value='$idvoyage'>
                    <button type='submit' style='background-color: green;  cursor: pointer; color:white;'>Modifier</button>
                </form>
                <form method='post' action=''>
                    <input type='hidden' name='id_voyage' value='$idvoyage'>
                    <button type='button' onclick='openModal()' style='background-color: red; margin-left: 35px; cursor: pointer;'>Supprimer</button>
                </form>
            </div>
        </td> 
    </tr>
");
}

echo("</table>");

echo("
<style>
.my-table {
    border-collapse: collapse;
}

.my-table th, .my-table td {
    border: 1px solid black;
    padding: 8px;
}
</style>
");

    
   } catch (\Throwable $th) {
    echo("Echec de connexion");
   } 
   
?>


    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
    }

    th {
        background-color: #f2f2f2;
    }

    .colonne-divisee {
        display: flex;
        margin: 5px;
    }

    .colonne-gauche,
    .colonne-droite {
        flex: 1;
        padding: 5px;
    }


    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* PARTIE HEADER  */
    body {
        background-color: aliceblue;
    }

    header {
        width: 100%;
        background-color: green;
        height: 95px;
    }

    nav {
        width: 100%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    img {
        width: 120px;
        height: 80px;
        margin-top: 20px;
    }

    .items a {
        text-decoration: none;
        color: whitesmoke;
        font-size: 20px;
        margin-right: 40PX;
        padding: 0 15px;
    }


    .nav-bar ul {
        display: flex;

        list-style-type: none;
    }

    .header-picture {
        margin-left: 40px;

    }

    img {

        height: 55px;
        width: 95px;
    }

    .nav-bar {
        margin-right: 30px;
    }

    .outils i {
        color: white;
        font-size: 37px;
        margin-left: 30px;
        cursor: pointer;
    }

    caption {
        font-size: 20px;
        font-weight: bold;

        margin: 10px;
    }
    </style>

    <script>
    // function openModal() {
    //     document.getElementById("myModal").style.display = "block";
    // }


    // function closeModal() {
    //     document.getElementById("myModal").style.display = "none";
    // }


    // function deleteItem() {

    //     alert("L'élément a été supprimé !");
    //     closeModal(); 
    // }

    function openModal() {
        var idvoyage = "<?php echo $idvoyage; ?>";
        document.getElementById("id_voyage").value = idvoyage;
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
    }

    function closeModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "none";
    }
    </script>

</body>

</html>



<!-- 
<form method='post' action=''>
            <input type='hidden' name='id_voyage' value='$idvoyage'>
            <button type='submit' style='background-color: green; margin-right: 5px;'>Modifier</button>
            <button type='submit'  name='delete_voyage'  style='background-color: red;'>Supprimer</button>
        </form> -->


<!-- $id= $_POST['id_voyage']; -->


<!-- <form method='post' action='supprimer.php'>
            <input type='hidden' name='id_voyage' value='$idvoyage'> -->
<!-- </form> -->