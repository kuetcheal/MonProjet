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

    <!-- PARIE HEADER -->
    <?php include 'header.php'; ?>

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

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
    if (isset($_POST['input1']) && isset($_POST['input2']) && isset($_POST['input3']) && isset($_POST['input4'])) {
        // echo("connexion reussie");
        $Depart = $_POST['input1'];
        $Arrivee = $_POST['input2'];
        $date = $_POST['input3'];
        $dateRetour = $_POST['input4'];
        $_SESSION['depart'] = $Depart;
        $_SESSION['arrivee'] = $Arrivee;
        $_SESSION['date'] = $date;
        $_SESSION['dateretour'] = $dateRetour;

        echo " <br><div id='conteneur1'>
      

<br> </div>
<br>
<div class='filtre'>
<div>
  <button type='submit' class='filt'>
    <i class='fa fa-sliders' aria-hidden='true'></i> filtre
  </button>
</div>
<div class='text'>15 voyages disponibles</div>
</div>  <br> ";
    }
} catch (Exception $e) {
    echo 'echec de connexion';
}
$allerSimpleSelected = isset($_POST['inlineRadioOptions']) && $_POST['inlineRadioOptions'] === 'option1';
$allerRetourSelected = isset($_POST['inlineRadioOptions']) && $_POST['inlineRadioOptions'] === 'option2';

if ($allerSimpleSelected) {
    $requette1 = "select * from voyage  WHERE voyage.villeDepart='$Depart' AND voyage.villeArrivee='$Arrivee' AND voyage.jourDepart='$date'";
    $query = $bdd->query($requette1);
    while ($donne = $query->fetch()) {
        $heure = $donne['heureDepart'];
        $depart = $donne['villeDepart'];
        $arrive = $donne['villeArrivee'];
        $prix = $donne['prix'];
        $bus = $donne['typeBus'];
        $heure2 = $donne['heureArrivee'];
        $idvoyage = $donne['idVoyage'];

        echo "
        <div id='conteneur2'>
        <h1> $date</h1> 
            <div class='bloc1'>
              <div class='depart'>$heure</div>
              <div class='arrivée'>  $heure2 </div>
              <div class='prix'>$prix</div>
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

    // Ajoutez ici le code supplémentaire que vous souhaitez exécuter pour l'aller simple
} elseif ($allerRetourSelected) {
   
    echo "<div id='conteneur2'>";
    
      $heure3 = $heure4 = $prixretour = $busretour = $idvoyageretour = '';

    $requetteAller = "select * from voyage  WHERE voyage.villeDepart='$Depart' AND voyage.villeArrivee='$Arrivee' AND voyage.jourDepart='$date'";
    $query = $bdd->query($requetteAller);
    while ($donne = $query->fetch()) {
        $heure = $donne['heureDepart'];
        $depart = $donne['villeDepart'];
        $arrive = $donne['villeArrivee'];
        $prixaller = $donne['prix'];
        $bus = $donne['typeBus'];
        $heure2 = $donne['heureArrivee'];
        $idvoyage = $donne['idVoyage'];

        if (!$query) {
          echo "Erreur lors de l'exécution de la requête aller : " . $bdd->errorInfo()[2];
      } else {
        
        echo "
        
        <h3>Aller: $date</h3> 
            <div class='bloc1'>
              <div class='depart'>$heure</div>
              <div class='arrivée'>  $heure2 </div>
              <div class='prix'>$prixaller</div>
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
       </hr>
      
        ";
      }
    }

   

    $requetteRetour = "select * FROM voyageretour  WHERE voyageretour.villeRetour='$Depart' AND voyageretour.arriver='$Arrivee' AND voyageretour.jourPartir='$dateRetour'";
  $queryRetour = $bdd->query($requetteRetour);
  if ($queryRetour && $queryRetour->rowCount() > 0) {
    while ($donne = $queryRetour->fetch()) {
        $depart1 = $donne['villeRetour'];
        $arrive2 = $donne['arriver'];
        $heure3 = $donne['heurePartir'];
        $heure4 = $donne['heurearrive'];
        $prixretour = $donne['prixBillet'];
        $busretour = $donne['typebus'];
        $idvoyageretour = $donne['idVoyageRetour'];
        if (! $queryRetour) {
          echo "Erreur lors de l'exécution de la requête aller : " . $bdd->errorInfo()[2];
      } else {
        echo '
        <br>
        <h3>Retour: ' . $dateRetour . '</h3>
        <div class="bloc1">
            <div class="depart">' . $heure3 . '</div>
            <hr class="ligne-horizontale">
            <div class="arrivée">' . $heure4 . '</div>
            <hr class="ligne-horizontale">
            <div class="prix">' . $prixretour . ' FCFA</div>
        </div>
        <br>
        <div class="bloc2">
            <div class="lieu1">' . $arrive2 . '</div>
            <div class="lieu2" style="margin-right: 90px;">' . $depart1 . '</div>
            <div class="vip">
                <button type="submit" class="bus">
                    <i class="fa fa-bus" aria-hidden="true"></i>' . $busretour . '
                </button>
            </div>
        </div>
        <br>
        <div class="bloc3">
            <div class="Infos">
                <button id="ouvrirPopup">Détails du trajet</button>
            </div>
            <div class="icone">
                <i class="fa fa-wifi" aria-hidden="true"></i>
                <i class="fa fa-television" aria-hidden="true"></i>
                <i class="fa fa-beer" aria-hidden="true"></i>
            </div>
            <div class="form-group">
                <form method="post" action="payment.php">
                    <input type="hidden" value="' . $idvoyageretour . '" name="idVoyage">
                    <input type="submit" value="continuer">
                </form>
            </div>
        </div>
        ';
        

        
       }
    }
  } 

  echo "</div>"; 
 }



// } elseif ($allerRetourSelected) {
//   echo "<div id='conteneur2'>"; // Conteneur principal pour les trajets aller et retour

//   // Requête pour le trajet aller
//   $requetteAller = "SELECT * FROM voyage WHERE villeDepart='$Depart' AND villeArrivee='$Arrivee' AND jourDepart='$date'";
//   $queryAller = $bdd->query($requetteAller);

//   if ($queryAller && $queryAller->rowCount() > 0) {
//       while ($donneAller = $queryAller->fetch()) {
//           // Afficher les informations du trajet aller
//           echo "
//               <h3>Aller: {$date}</h3> 
//               <div class='bloc1'>
//                   <div class='depart'>{$donneAller['heureDepart']}</div>
//                   <div class='arrivée'>{$donneAller['heureArrivee']}</div>
//                   <div class='prix'>{$donneAller['prix']}</div>
//               </div>
//               <div class='bloc2'>
//                   <div class='lieu1'>{$donneAller['villeDepart']}</div>
//                   <div class='lieu2'>{$donneAller['villeArrivee']}</div>
//                   <button type='submit' class='bus'>
//                       <i class='fa fa-bus' aria-hidden='true'></i>{$donneAller['typeBus']}
//                   </button>
//               </div>
//               <br><hr>
//           ";

//           // Requête pour les trajets retour correspondants
//           $requetteRetour = "SELECT * FROM voyageretour WHERE villeRetour='$Arrivee' AND arriver='$Depart' AND jourPartir='$dateRetour'";
//           $queryRetour = $bdd->query($requetteRetour);

//           if ($queryRetour && $queryRetour->rowCount() > 0) {
//               while ($donneRetour = $queryRetour->fetch()) {
//                   // Afficher les informations du trajet retour
//                   echo '
//                       <h3>Retour: ' . $dateRetour . '</h3>
//                       <div class="bloc1">
//                           <div class="depart">' . $donneRetour['heurePartir'] . '</div>
//                           <hr class="ligne-horizontale">
//                           <div class="arrivée">' . $donneRetour['heurearrive'] . '</div>
//                           <hr class="ligne-horizontale">
//                           <div class="prix">' . $donneRetour['prixBillet'] . ' FCFA</div>
//                       </div>
//                       <div class="bloc2">
//                           <div class="lieu1">' . $donneRetour['arriver'] . '</div>
//                           <div class="lieu2" style="margin-right: 90px;">' . $donneRetour['villeRetour'] . '</div>
//                           <button type="submit" class="bus">
//                               <i class="fa fa-bus" aria-hidden="true"></i>' . $donneRetour['typebus'] . '
//                           </button>
//                       </div>
//                       <br>
//                   ';
//               }
//           } else {
//               echo "Aucun trajet retour trouvé pour la date de retour sélectionnée.";
//           }
//           echo "<hr><br>"; // Séparateur après chaque association aller-retour
//       }
//   } else {
//       echo "Erreur lors de l'exécution de la requête aller : " . ($bdd->errorInfo()[2] ?? 'Pas de détails');
//   }

//   echo "</div>"; // Fin du conteneur principal
// }
?>



    <style>


    </style>

    <!-- PARIE FOOTER -->



    <style>
    .ligne-horizontale {
        border-top: 1px solid #ccc;

        /* Couleur noire pour la ligne */
        width: 250px;
        /* Largeur de la ligne à 80% de son conteneur */
        margin: 3px auto;
        /* Centrer la ligne horizontalement et ajouter une marge */
    }
    </style>
</body>

</html>