<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
          <li  class="outils"><a href="#"><i class="fa fa-user-circle-o fa-6x" aria-hidden="true"></i></a></li>
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

try
{
$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
if(isset($_POST["input1"]) && isset($_POST["input2"]) && isset($_POST["input3"])){
    // echo("connexion reussie");
$Depart=$_POST["input1"];
$Arrivee=$_POST["input2"];
$date=$_POST["input3"];
$_SESSION["depart"]=$Depart;
$_SESSION["arrivee"]=$Arrivee;
$_SESSION["date"]=$date;

echo(" <br><div id='conteneur1'>
      
$date
<br> </div>
<br>
<div class='filtre'>
<div>
  <button type='submit' class='filt'>
    <i class='fa fa-sliders' aria-hidden='true'></i> filtre
  </button>
</div>
<div class='text'>15 voyages disponibles</div>
</div>  <br> ");


}

}
catch (Exception $e)
{
echo("echec de connexion");
}

$requette ="select * From voyage where villeDepart='$Depart' and villeArrivee='$Arrivee' and jourDepart='$date'";
$resultat = $bdd->query($requette);
while($donne=$resultat->fetch()){
    $heure=$donne["heureDepart"] ;
    $depart=$donne["villeDepart"];
    $arrive=$donne["villeArrivee"];
    $price=$donne["prix"];
    $bus=$donne["typeBus"];
    $heure2=$donne["heureArrivee"];
    $idvoyage=$donne["idVoyage"];
 echo("

 
 <div id='conteneur2'>
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
     
 
 
 
 ");
 
} 



?>
<style>
 /* PARTIE HEADER  */
body{
    background-color: aliceblue;
}

header{
    width: 100%;
    background-color: green;
    height: 95px;
}
nav{
    width: 100%;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
img{
    width: 120px;
    height: 80px;
    margin-top: 20px;
}
.items a{
    text-decoration: none;
    color: whitesmoke;
    font-size: 20px;
   margin-right: 40PX;
   padding: 0 15px; 
}

 
.nav-bar ul{
    display: flex;
   
    list-style-type: none;
}
.header-picture{
    margin-left: 40px;
  
  }
  img{
    
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

.box {
    border: 2px solid #ccc;
    padding: 20px;
    background-color: #ffffff;
    box-shadow: 0 1px 3px rgba(0.2, 0, 0.3, 0.3);
    width: 1307px; 
    margin-right: 2px;
      
  }
  
  .form-group {
    margin-right: 20px;
    display: inline-block;
    vertical-align: top;
  }
  
  label {
    display: block;
    margin-bottom: 5px;
  }
  
  input[type="text"],
  select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
  }
  
  .radio-buttons label {
    margin-right: 10px;
  }
  
  .radio-buttons input[type="radio"] {
    display: none;
    height: 20px;
    width: 20px;
    border: black;
  }
  
  .radio-buttons input[type="radio"] + label:before {
    content: "";
    display: inline-block;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    border: 2px solid #ccc;
    margin-right: 10px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
  }
  
  .radio-buttons input[type="radio"]:checked + label:before {
    background-color: #2196f3;
    border-color: #2196f3;
  }
  
  .radio-buttons label {
    display: inline-block;
    margin-bottom: 10px;
    font-weight: bold;
  }
  
  input[type="submit"] {
    background-color: #2196f3;
    border: 2px;
    color: white;
    padding: 8px 16px;
    text-align: center;
    text-decoration: none;
    display: inline;
    width: 100px;
    height: 40px;
    cursor: pointer;
  }

  #conteneur1 {
    background-color: #ffffff;
    margin-left: 550px;
    padding: 10px;
   height: 20px;
   width: 200px;
    border:  3px solid #666;
    
  }

  .filtre {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .filt{
    font-size: 22px;
    margin-left: 250px;
   border-radius: 5px;
   cursor: pointer;
  }
  .text{
    font-size: 17px;
    margin-right: 260px;
    font-weight: bold;
  }

  #conteneur2 {
    background-color: #ffffff;
    padding: 16px;
    margin-bottom: 30px;
    border: none;
    width: 810px;
    margin-left: 250px;
    height: 120px;
    box-shadow: 0 1px 3px rgba(0.2, 0, 0.3, 0.3);
  }

.bloc1{
  display: flex;
    justify-content: space-between;
    align-items: center; 
}

.depart, .arrivée, .prix{
  font-size: 20px;
  font-weight: bold;
}
.prix{
  font-size: 20px;
  font-weight: bold;
  margin-right: 20px;
}
.vip{
  margin-right: 20px;
}


.bloc2{
  display: flex;
    justify-content: space-between;
    align-items: center; 
}
.lieu1{
  font-size: 20px;
 
}
.lieu2{
  font-size: 20px;
 
 margin-left: 80px;
}

.bloc3{
  display: flex;
  justify-content: space-between;
  align-items: center;  
}


.bus{
  font-size: 20px;
  background-color: #ffffff;
 border-radius: 5px;
}
.icone{
 margin-right: 8px;
}
.fa{
  margin-right: 10px;
}


input[type="submit"]{
  font-size: 20px;
  background-color: green;
  border: none;
  cursor: pointer;
  border-radius: 3px;
  padding: 8px;
  color: white;
}
#ouvrirPopup{
  font-size: 20px;
  background-color: green;
  border: none;
  cursor: pointer;
  border-radius: 3px;
  color: white;
}  
.trajet{
  font-size: 20px;
  background-color: green;
  border: none;
  cursor: pointer;
  border-radius: 3px;
}  

</style>

</body>
</html>


<!-- lundi, le 15 Juillet 2023 -->