<?php
session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <title>Tableau moderne</title>
    <link rel="stylesheet" href="listevoyadmin.css">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<body>
    <header>
        <nav>
            <div class="header-picture">
                <img src="logo général.jpg" alt="logo site" />
            </div>
            <div class="nav-bar">
                <ul>

                    <li><a href="notification.php">
                            <i class="fa fa-bell fa-2x" aria-hidden="true" style="color: white;"></i>
                            <?php
       $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
$requeteCount = 'SELECT COUNT(*) as count FROM admins WHERE lu = 0';
$resultatCount = $bdd->query($requeteCount);
if ($resultatCount !== false) {
    // Vérifiez le résultat avant d'appeler fetchColumn()
    $count = $resultatCount->fetchColumn();

    if ($count !== false) {
        // Afficher le nombre s'il y en a
        if ($count > 0) {
            echo '<span class="badge">'.$count.'</span>';
        }
    } else {
        // Gérer l'erreur lors de l'appel de fetchColumn()
        echo 'Erreur lors de la récupération du nombre de messages non lus.';
    }
} else {
    // Gérer l'erreur lors de l'exécution de la requête SQL
    $errorInfo = $bdd->errorInfo();
    echo 'Erreur SQL : '.$errorInfo[2];
}
?>
                        </a></li>
                    <li class="outils"><a href="#"><i class="fa fa-user-circle-o fa-6x" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="sidebar">
        <ul>
            <div class="nav-item">
                <li><a class="nav-link" href="#liste"><i class="fas fa-list"></i> Liste</a></li>
            </div>
            <div class="nav-item">
                <li><a class="nav-link" href="#destination"> Destination</a></li>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="/inscription">ajouter</a>
                    <a class="dropdown-item" href="/connexion">liste destination</a>

                </div>
            </div>
            <div class="nav-item">
                <li><a class="nav-link" href="#utilisateurs"><i class="fas fa-users"></i> Utilisateurs</a></li>
            </div>
            <div class="nav-item">
                <li><a class="nav-link" href="#voyages"><i class="fas fa-suitcase-rolling"></i> Mes voyages</a></li>
            </div>
        </ul>
    </div>
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
    $host = 'localhost'; // nom d'hôte
    $user = 'root'; // nom d'utilisateur
    $password = ''; // mot de passe
    $database = 'bd_stock'; // nom de la base de données

    // Connexion à la base de données MySQLi
    $conn = mysqli_connect($host, $user, $password, $database);
    $id = $_POST['id_voyage'];
    $query = "DELETE FROM voyage WHERE idVoyage =$id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<meta http-equiv='refresh' content='3;url=listevoyadmin.php'>";
        echo "<div style='height: 100px; width: 600px; background-color: green; color: white;
font-size: 25px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px; '>
Voyage supprimé avec succès.
</div>";
    } else {
        echo 'Erreur lors de la suppression du voyage.';
    }
    header('Location: listevoyadmin.php');
    mysqli_close($conn);
    exit;
}
?>
    <h4 style="margin-left: 700px;">liste des trajets disponibles</h4>
    <div class=" annonce" style="font-size: 24px">
        <div class="trajet"> <i class="fa fa-plus-circle" aria-hidden="true"></i>
            <a href=" insertionvoyage.php"> Ajouter un voyage</a>
        </div>
        <div>
            <button type=' submit' class='filt' style="font-size: 24px">
                <i class='fa fa-sliders' aria-hidden='true'></i> filtre
            </button>
        </div>
    </div>

    <div class=" my-table-container">

        <?php
   try {
       $conn = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

       echo "
      
        <table class='my-table'> 
        <tr>
            <th scope='col'>Id voyage</th>
            <th scope='col'>Ville départ</th>
            <th scope='col'>Ville arrivée</th>
            <th scope='col'>heure départ</th>
            <th scope='col'>heure arrivée</th> 
            <th scope='col'>type de bus</th>
            <th scope='col'>Prix</th>
            <th scope='col'>date</th>
            <th scope='col'>Actions</th>
        </tr>";
       $requette = 'select * From voyage order by jourDepart DESC';
       $resultat = $conn->query($requette);

       while ($donne = $resultat->fetch()) {
           $heure = $donne['heureDepart'];
           $depart = $donne['villeDepart'];
           $arrive = $donne['villeArrivee'];
           $price = $donne['prix'];
           $bus = $donne['typeBus'];
           $heure2 = $donne['heureArrivee'];
           $idvoyage = $donne['idVoyage'];
           $date = $donne['jourDepart'];

           echo "
    <tr>
        <td>$idvoyage</td>
        <td>$depart</td>
        <td>$arrive</td>
        <td>$heure</td>
        <td>$heure2</td> 
        <td>$bus</td>
        <td>$price</td>  
        <td>$date</td>
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
";
       }

       echo '</table>';

       echo '
<style>
.my-table {
    border-collapse: collapse;
    width: 80%; /* Ajustez la largeur selon vos besoins */
    margin-right: 20px; /* Ajoutez une marge à droite */
}

.my-table th, .my-table td {
    border: 1px solid black;
    padding: 8px;
    text-align: left; 
}

/* Aligner la table à droite */
.my-table-container {
    display: flex;
    justify-content: flex-end; /* Ceci aligne la table à droite */
    
}
</style>
';
   } catch (\Throwable $th) {
       echo 'Echec de connexion';
   }

?>
    </div>

    <script>
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

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