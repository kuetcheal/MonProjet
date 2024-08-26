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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
    $Depart = '';
    $Arrivee = '';
    $date = '';
    $dateRetour = '';
    
    if (isset($_POST['input1']) && isset($_POST['input2']) && isset($_POST['input3'])) {
        $Depart = $_POST['input1'];
        $Arrivee = $_POST['input2'];
        $date = $_POST['input3'];
        $dateRetour = $_POST['input4'] ?? ''; // Assigner à $dateRetour si disponible, sinon ''

        $_SESSION['depart'] = $Depart;
        $_SESSION['arrivee'] = $Arrivee;
        $_SESSION['date'] = $date;
        $_SESSION['dateretour'] = $dateRetour;

        // Requête pour compter le nombre de trajets disponibles
        $countQuery = $bdd->prepare('SELECT COUNT(*) as count FROM voyage WHERE villeDepart = :depart AND villeArrivee = :arrivee AND jourDepart = :date');
        $countQuery->execute([
            'depart' => $Depart,
            'arrivee' => $Arrivee,
            'date' => $date,
        ]);
        $countResult = $countQuery->fetch();
        $voyagesDisponibles = $countResult['count'];

        echo " <br><div id='conteneur1'>
      

<br> </div>

<div class='filtre'>
<div>
  <button type='submit' class='filt'>
    <i class='fa fa-sliders' aria-hidden='true'></i> filtre
  </button>
</div>
<div class='text'><h2>{$voyagesDisponibles} voyages disponibles</h2></div>
</div>  <br> ";
    }
} catch (Exception $e) {
    echo 'echec de connexion';
}
$allerSimpleSelected = isset($_POST['inlineRadioOptions']) && $_POST['inlineRadioOptions'] === 'option1';
$allerRetourSelected = isset($_POST['inlineRadioOptions']) && $_POST['inlineRadioOptions'] === 'option2';

if ($allerSimpleSelected) {

   

    
    // Afficher le titre pour les trajets aller
    echo "<h2 style='text-align: center; color: green;'>Aller: " . date("d M Y", strtotime($date)) . "</h2>";
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
        <div id='conteneur' class='conteneur'>       
            <div class='bloc1'>
              <div class='depart'>$heure</div>
               <hr class='ligne-horizontale'>
              <div class='arrivée'>  $heure2 </div>
               <hr class='ligne-horizontale'>
              <div class='prix'>$prix FCFA</div>
            </div> <br>
            <div class='bloc2'>
              <div class='lieu1'><i class='bi bi-geo-alt'></i>$depart </div>
              <div class='lieu2' style='margin-right: 50px;'><i class='bi bi-geo-alt'></i>$arrive </div>
              <div class='vip'>
                <button type='submit' class='bus'>
                  <i class='fa fa-bus' aria-hidden='true'></i>$bus
                </button>
              </div>
            </div><br>
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
    $heure3 = $heure4 = $prixretour = $busretour = $idvoyageretour = '';
    // Afficher le titre pour les trajets aller
    echo "<h2 style='text-align: center; color: green;'> Trajets Aller: " . date("d M Y", strtotime($date)) . "</h2>";


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
            echo "Erreur lors de l'exécution de la requête aller : ".$bdd->errorInfo()[2];
        } else {
            echo " 
      <div id='conteneur2_$idvoyage' class='conteneur2'>
            <div class='bloc1'>
              <div class='depart'>$heure</div>
               <hr class='ligne-horizontale'>
              <div class='arrivée'>  $heure2 </div>
               <hr class='ligne-horizontale'>
              <div class='prix'>$prixaller FCFA</div>
            </div> <br>
            <div class='bloc2'>
              <div class='lieu1'><i class='bi bi-geo-alt'></i>$depart </div>
              <div class='lieu2' style='margin-right: 50px;'><i class='bi bi-geo-alt'></i>$arrive </div>
              <div class='vip'>
                <button type='submit' class='bus'>
                  <i class='fa fa-bus' aria-hidden='true'></i>$bus
                </button>
              </div>
            </div>
        <br>    
        <div class='bloc3'>
            <div class='Infos'>
                <button id='ouvrirPopup'>Détails du trajet</button>
            </div>
            <div class='icone'>
                <i class='fa fa-wifi' aria-hidden='true'></i>
                <i class='fa fa-television' aria-hidden='true'></i>
                <i class='fa fa-beer' aria-hidden='true'></i>
            </div>
            <div class='form-group'>  
               <button class='continuer-btn' 
                        data-id='$idvoyage' 
                        data-type='aller' 
                        data-price='$prixaller' 
                        data-depart='$depart' 
                        data-arrive='$arrive' 
                        data-time='$heure'>
                    Continuer
                </button>
            </div>
        </div>
       </hr>
      </div><br>
        ";
        }
    }
   
    // Afficher le titre pour les trajets retour
  echo "<h2 style='text-align: center; color: green;'>Trajets Retour : " . date("d M Y", strtotime($dateRetour)) . "</h2>";
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
            if ($date == $dateRetour && $heure3 <= $heure2) {
                echo "<div class='error-message'>Erreur: L'heure d'arrivée du trajet retour ($heure3) ne peut pas être avant ou égale à l'heure de départ ($heure2) du trajet aller.</div>";
            }  else {
                echo "
      <div id='conteneur3_$idvoyageretour' class='conteneur3'>
        <div class='bloc1'>
            <div class='depart'>$heure3</div>
            <hr class='ligne-horizontale'>
            <div class='arrivée'>$heure4</div>
            <hr class='ligne-horizontale'>
            <div class='prix'> $prixretour FCFA</div>
        </div>
        <br>
        <div class='bloc2'>
            <div class='lieu1'><i class='bi bi-geo-alt'></i>$arrive2</div>
            <div class='lieu2' style='margin-right: 50px;'><i class='bi bi-geo-alt'></i>$depart1</div>
            <div class='vip'>
                <button type='submit' class='bus'>
                    <i class='fa fa-bus' aria-hidden='true'></i>$busretour
                </button>
            </div>
        </div>
        <br>
        <div class='bloc3'>
            <div class='Infos'>
                <button id='ouvrirPopup'>Détails du trajet</button>
            </div>
            <div class='icone'>
                <i class='fa fa-wifi' aria-hidden='true'></i>
                <i class='fa fa-television' aria-hidden='true'></i>
                <i class='fa fa-beer' aria-hidden='true'></i>
            </div>
           <div class='form-group' >
                        <button class='continuer-btn'
                                data-id='$idvoyageretour'
                                data-type='retour'
                                data-price='$prixretour'
                                data-depart='$depart1'
                                data-arrive='$arrive2'
                                data-time='$heure3'>
                               
                            Continuer
                        </button>
             </div>
        </div>
      </div>
         "

                ;
            }
        }
    }

    echo '</div>';
}
?>

    <footer>
        <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>
    <style>
    .ligne-horizontale {
        border-top: 1px solid #ccc;
        width: 250px;
        margin: 3px auto;
    }

    footer {
        background-color: #6c757d;
        color: white;
        padding: 20px 0;
        text-align: center;
        width: 100%;
    }

    .conteneur2 {
        background-color: #ffffff;
        padding: 16px;
        margin-bottom: 30px;
        border: none;
        width: 810px;
        margin-left: 250px;
        height: 130px;
        box-shadow: 0 1px 3px rgba(0.2, 0, 0.3, 0.3);
    }

    .conteneur {
        background-color: #ffffff;
        padding: 16px;
        margin-bottom: 30px;
        border: none;
        width: 810px;
        margin-left: 250px;
        height: 130px;
        box-shadow: 0 1px 3px rgba(0.2, 0, 0.3, 0.3);
    }

    .conteneur3 {
        background-color: #ffffff;
        padding: 16px;
        margin-bottom: 30px;
        border: none;
        width: 810px;
        margin-left: 250px;
        height: 130px;
        box-shadow: 0 1px 3px rgba(0.2, 0, 0.3, 0.3);
    }

    .container {
        height: 100px;
        width: 600px;
        background-color: green;
        color: white;
        font-size: 16px;
    }

    .popup {
        display: none;
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        border: 1px solid #888;
        background-color: #fff;
        z-index: 1000;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .popup-content {
        margin: 15px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .selected-trip {
        border: 4px solid #90EE90;
        /* Couleur vert clair */
        padding: 10px;
        /* box-shadow: 0 0 10px rgba(144, 238, 144, 0.5); */
        /* Optionnel : ajoute une ombre légère */
        background-color: #f0fff0;
        /* Optionnel : un fond légèrement vert clair */
        border-radius: 5px;
        /* Optionnel : pour arrondir les angles */
    }
    </style>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        let selectedTrips = {
            aller: null,
            retour: null
        };

        document.querySelectorAll('.continuer-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();

                const tripType = this.getAttribute('data-type');
                const containerSelector = tripType === 'aller' ? 'div[id^="conteneur2_"]' :
                    'div[id^="conteneur3_"]';
                const tripContainer = this.closest(containerSelector);
                const tripId = tripContainer.id;
                const tripPrice = this.getAttribute('data-price');
                const tripDepart = this.getAttribute('data-depart');
                const tripArrive = this.getAttribute('data-arrive');
                const tripTime = this.getAttribute('data-time');

                // Désélectionne le précédent trajet de ce type (aller ou retour)
                if (selectedTrips[tripType]) {
                    document.getElementById(selectedTrips[tripType].id).classList.remove(
                        'selected-trip');
                }

                // Sélectionne le nouveau trajet
                selectedTrips[tripType] = {
                    id: tripId,
                    price: tripPrice,
                    depart: tripDepart,
                    arrive: tripArrive,
                    time: tripTime
                };
                tripContainer.classList.add('selected-trip');

                // Vérifie si les deux trajets (aller et retour) sont sélectionnés
                if (selectedTrips.aller && selectedTrips.retour) {
                    // Redirige vers la page recap.php avec les informations
                    const params = new URLSearchParams({
                        priceAller: selectedTrips.aller.price,
                        priceRetour: selectedTrips.retour.price,
                        departAller: selectedTrips.aller.depart,
                        arriveAller: selectedTrips.aller.arrive,
                        timeAller: selectedTrips.aller.time,
                        departRetour: selectedTrips.retour.depart,
                        arriveRetour: selectedTrips.retour.arrive,
                        timeRetour: selectedTrips.retour.time
                    });

                    window.location.href = 'recap.php?' + params.toString();
                }
            });
        });
    });
    </script>

</body>

</html>