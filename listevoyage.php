<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="listevoyage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Document</title>
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

    <main>
        <div class="box">
            <form>
                <div class="radio-buttons">
                    <input type="radio" id="radio1" name="radio" value="option1">
                    <label for="radio1">Aller</label>

                    <input type="radio" id="radio2" name="radio" value="option2">
                    <label for="radio2">Aller-retour</label>
                </div>

                <div class="form-group">
                    <label for="input1">DE:</label>
                    <input type="text" id="input1" name="input1" placeholder="Douala">
                </div>

                <div class="form-group">
                    <label for="input2">A:</label>
                    <input type="text" id="input2" name="input2" placeholder="Yaoundé">
                </div>

                <div class="form-group">
                    <label for="input3">Départ:</label>
                    <input type="text" id="input3" name="input3" placeholder=" lu,20 Avril.">
                </div>

                <div class="form-group">
                    <label for="input4">Passage:</label>
                    <input type="text" id="input4" name="input4" placeholder="1 adulte">
                </div>

                <div class="form-group">
                    <label for="select">Select:</label>
                    <select id="select" name="select" aria-placeholder="2 places">
                        <option value="option1">Adultes</option>
                        <option value="option2">Enfants</option>

                    </select>
                </div>

                <div class="form-group">
                    <imput type="submit" value="Rechercher" class="butt 
            ">Rechercher</i>
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Valider
                        </button>
                </div>

            </form>
        </div>
    </main>


    <?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    // Vérification et assignation des variables POST
    $Depart = isset($_POST['Depart']) ? $_POST['Depart'] : null;
    $Arrivee = isset($_POST['Arrivee']) ? $_POST['Arrivee'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $dateRetour = isset($_POST['dateRetour']) ? $_POST['dateRetour'] : null;
    $allerRetourSelected = isset($_POST['allerRetour']) && $_POST['allerRetour']; // Utilisation conditionnelle

    if ($Depart && $Arrivee && $date) {
        // Seulement si $Depart, $Arrivee, et $date sont non-null
        $_SESSION['depart'] = $Depart;
        $_SESSION['arrivee'] = $Arrivee;
        $_SESSION['date'] = $date;
        $_SESSION['dateretour'] = $dateRetour;

        echo " <br><div id='conteneur1'><br> </div><br>
        <div class='filtre'>
            <div>
                <button type='submit' class='filt'>
                    <i class='fa fa-sliders' aria-hidden='true'></i> filtre
                </button>
            </div>
            <div class='text'>15 voyages disponibles</div>
        </div><br>";
    }

    // Paramètres de recherche fournis par l'utilisateur
    // $Depart = $_POST['Depart'];
    // $Arrivee = $_POST['Arrivee'];
    // $date = $_POST['date'];
    $allerSimpleSelected = isset($_POST['inlineRadioOptions']) && $_POST['inlineRadioOptions'] === 'option1';

    if ($allerSimpleSelected) {
        // Utilisation de requêtes préparées pour améliorer la sécurité
        $requete = 'SELECT * FROM voyage WHERE villeDepart = :depart AND villeArrivee = :arrivee AND jourDepart = :date';
        $stmt = $bdd->prepare($requete);
        $stmt->execute([
            ':depart' => $Depart,
            ':arrivee' => $Arrivee,
            ':date' => $date,
        ]);

        // Traitement des résultats
        while ($donne = $stmt->fetch()) {
            $heure = $donne['heureDepart'];
            $depart = $donne['villeDepart'];
            $arrive = $donne['villeArrivee'];
            $price = $donne['prix'];
            $bus = $donne['typeBus'];
            $heure2 = $donne['heureArrivee'];
            $idvoyage = $donne['idVoyage'];
            
            // Affichage des résultats en HTML
            echo "
        <div id='conteneur2'>
        <h1> $date</h1>
            <div class='bloc1'>
                <div class='depart'>$heure</div>
                <div class='arrivée'> $heure2 </div>
                <div class='prix'>$price</div>
            </div> <br>
            <div class='bloc2'>
                <div class='lieu1'>$depart </div>
                <div class='lieu2'> $arrive </div>
                <div class='vip'>
                    <button type='submit' class='bus'>
                        <i class='fa fa-bus' aria-hidden='true'></i>$bus
                    </button>
                </div>
            </div>
            <br>
            <div class='bloc3'>
                <div class='Infos'>
                    <button id='ouvrirPopup'> Détails du trajet </button>
                </div>
                <div class='icone'>
                    <i class='fa fa-wifi' aria-hidden='true'></i>
                    <i class='fa fa-television' aria-hidden='true'></i>
                    <i class='fa fa-beer' aria-hidden='true'></i>
                </div>
                <div class='form-group'>
                    <form method='post' action='payment.php'>
                        <input type='hidden' value='$idvoyage' name='idVoyage'>
                        <input type='submit' value='continuer'>
                    </form>
                </div>
            </div>
        </div>    
        ";
        }
    } else {
        $allerRetourSelected = isset($_POST['inlineRadioOptions']) && $_POST['inlineRadioOptions'] === 'option2';
    }

    // Paramètres de recherche fournis par l'utilisateur
    // $Depart = $_POST['Depart'];
    // $Arrivee = $_POST['Arrivee'];
    // $date = $_POST['date'];
    // $dateRetour = $_POST['dateRetour'];
    // $allerRetourSelected = $_POST['allerRetour']; // Supposons que c'est un booléen

    // Début de la requête SQL
    $requete = 'SELECT voyage.*, 
                   voyageretour.heurePartir, 
                   voyageretour.heurearrive AS heure2Retour, 
                   voyageretour.typebus AS typeBusRetour, 
                   voyageretour.prixBillet AS prixBilletRetour
            FROM voyage
            LEFT JOIN voyageretour ON voyage.idVoyageretour = voyageretour.idVoyageretour
            WHERE (voyage.villeDepart = :depart AND voyage.villeArrivee = :arrivee AND voyage.jourDepart = :date)
            OR (voyageretour.villeRetour = :arrivee AND voyageretour.arriver = :depart AND voyageretour.jourPartir = :dateRetour)
            OR (voyageretour.idVoyageretour IS NOT NULL)';

    // Préparation et exécution de la requête
    $stmt = $bdd->prepare($requete);
    $stmt->execute([
        ':depart' => $Depart,
        ':arrivee' => $Arrivee,
        ':date' => $date,
        ':dateRetour' => $dateRetour,
    ]);

    // Traitement des résultats
    if ($stmt) {
        while ($donne = $stmt->fetch()) {
            $heure = $donne['heureDepart'];
            $depart = $donne['villeDepart'];
            $arrive = $donne['villeArrivee'];
            $price = $donne['prix'];
            $bus = $donne['typeBus'];
            $heure2 = $donne['heureArrivee'];
            $idvoyage = $donne['idVoyage'];
            $heureRetour = isset($donne['heurePartir']) ? $donne['heurePartir'] : '';
            $heure2Retour = isset($donne['heure2Retour']) ? $donne['heure2Retour'] : '';
            $busRetour = isset($donne['typeBusRetour']) ? $donne['typeBusRetour'] : '';
            $priceBilletRetour = isset($donne['prixBilletRetour']) ? $donne['prixBilletRetour'] : '';

            echo "
    <div id='conteneur2'>
     <h1> $date</h1> 
         <div class='bloc1'>
           <div class='depart'>$heure</div>
           <div class='arrivée'>  $heure2 </div>
           <div class='prix'>$price</div>
         </div> <br>
         <div class='bloc2'>
           <div class='lieu1'>$depart </div>
           <div class='lieu2'> $arrive </div>
           <div class='vip'>
             <button type='submit' class='bus'>
               <i class='fa fa-bus' aria-hidden='true'></i>$bus
             </button>
           </div>
         </div>
         <br><hr>
         <h1> $dateRetour</h1> 
         <div class='bloc1'>
         <div class='depart'> $heureRetour</div>
         <div class='arrivée'>   $heure2Retour </div>
         <div class='prix'>$priceBilletRetour</div>
       </div> <br>
       <div class='bloc2'>
       <div class='lieu1'>$arrive </div>
       <div class='lieu2'>$depart </div>
       <div class='vip'>
         <button type='submit' class='bus'>
           <i class='fa fa-bus' aria-hidden='true'></i>$busRetour
         </button>
       </div>
     </div>
     <br>
         <div class='bloc3'>
           <div class='Infos'>
             <button   id='ouvrirPopup'>  Détails du trajet </button> 
           </div>
           <div class='icone'>
             <i class='fa fa-wifi' aria-hidden='true'></i>
             <i class='fa fa-television' aria-hidden='true'></i>
             <i class='fa fa-beer' aria-hidden='true'></i>
           </div>
           <div class='form-group'>
           <form method='post' action='payment.php'> 
           <input type='hidden' value='$idvoyage' name='idVoyage'>
             <input type='submit'  value='continuer'>
             
             </form>
           </div>
         </div>
    </div>     
    ";
        }
    } else {
        // Gérer l'erreur ici
        echo "Erreur lors de l'exécution de la requête : ".$bdd->errorInfo()[2];
    }
} catch (Exception $e) {
    echo 'Échec de la connexion : '.$e->getMessage();
}
?>

</body>

</html>