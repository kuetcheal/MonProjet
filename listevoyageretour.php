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
    echo "<h2>Aller: $date</h2>";
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
    echo "<h2> Trajets Aller: $date</h2>";

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
      <div id='conteneur2'>
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
                <button class='continuer-btn' data-id='$idvoyage' data-type='aller'>Continuer</button>
            </div>
        </div>
       </hr>
      </div>
        ";
        }
    }

    // Afficher le titre pour les trajets retour
    echo "<h2>Trajets Retour: $dateRetour</h2>";
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
            if (!$queryRetour) {
                echo "Erreur lors de l'exécution de la requête aller : ".$bdd->errorInfo()[2];
            } else {
                echo "
      <div id='conteneur2'>
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
            <div class='form-group'>     
                 <button class='continuer-btn' data-id='$idvoyageretour' data-type='retour'>Continuer</button>                            
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

    <!-- Conteneur pour la popup -->
    <div id="popup" class="popup">
        <h2>Détails du Trajet</h2>
        <span class="popup-close" onclick="document.getElementById('popup').style.display='none';">Fermer</span>
        <p><strong>Prix Aller:</strong> <span id="popup-price-aller"></span></p>
        <p><strong>Départ Aller:</strong> <span id="popup-depart-aller"></span></p>
        <p><strong>Arrivée Aller:</strong> <span id="popup-arrive-aller"></span></p>
        <p><strong>Escale(s) Aller:</strong> <span id="popup-escales-aller">Loum, Penja, Edea</span></p>
        <hr>
        <p><strong>Prix Retour:</strong> <span id="popup-price-retour"></span></p>
        <p><strong>Départ Retour:</strong> <span id="popup-depart-retour"></span></p>
        <p><strong>Arrivée Retour:</strong> <span id="popup-arrive-retour"></span></p>
        <p><strong>Escale(s) Retour:</strong> <span id="popup-escales-retour">Loum, Penja, Edea</span></p>
        <hr>
        <p><strong>Total à payer:</strong> <span id="popup-total"></span></p>
        <button id="popup-continue" class="popup-continue">Continuer</button>
    </div>

    <script>
    let priceAller = 0;
    let priceRetour = 0;

    document.querySelectorAll('.continuer-btn').forEach(button => {
        button.addEventListener('click', function() {
            const type = this.getAttribute('data-type');
            const id = this.getAttribute('data-id');
            fetch(`getTrajetDetails.php?id=${id}&type=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (type === 'aller') {
                        priceAller = data.prix;
                        document.getElementById('popup-price-aller').innerText = data.prix;
                        document.getElementById('popup-depart-aller').innerText = data.villeDepart;
                        document.getElementById('popup-arrive-aller').innerText = data.villeArrivee;
                    } else {
                        priceRetour = data.prix;
                        document.getElementById('popup-price-retour').innerText = data.prix;
                        document.getElementById('popup-depart-retour').innerText = data.villeDepart;
                        document.getElementById('popup-arrive-retour').innerText = data
                            .villeArrivee;
                    }
                    document.getElementById('popup-total').innerText = priceAller + priceRetour +
                        ' FCFA';
                    document.getElementById('popup').style.display = 'block';
                });
        });
    });

    document.getElementById('popup-continue').addEventListener('click', function() {
        window.location.href = 'payment.php';
    });
    </script>

    <style>
    .ligne-horizontale {
        border-top: 1px solid #ccc;
        width: 250px;
        margin: 3px auto;
    }

    #conteneur2 {
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
    </style>


    <script>
    // function showPopup(depart, arrivee, prix) {
    //     document.getElementById('popupDepart').textContent = depart;
    //     document.getElementById('popupArrivee').textContent = arrivee;
    //     document.getElementById('popupPrix').textContent = prix;
    //     document.getElementById('popup').style.display = 'block';
    // }

    // function closePopup() {
    //     document.getElementById('popup').style.display = 'none';
    // }
    </script>
</body>

</html>