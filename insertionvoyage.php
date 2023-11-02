<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Insertion voyage</title>
   
</head>
<body>
<header>
    <nav>
      <div class="header-picture">
        <img src="logo général.jpg" alt="logo site" />
      </div>
      <div class="nav-bar">
        <ul>
          <li class="items"> <a href="inventaire.php">Acceuil</a></li>
          <li class="items"><a href="ajoutarticle.php">liste de Reservations</a></li>
          <li class="items"><a href="ajoutclient.php">Nos services clients</a></li>
          <li class="items"><a href="achatarticle.php">liste des trajets</a></li> 
        </ul>
      </div>
      <div class="public">
        
          <div class="langue">
            <select id="select" name="select" aria-placeholder="2 places">
              <option value="option1">Français</option>
              <option value="option2">Anglais</option>

            </select>
          </div>
          <div class="outils"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true" ></i></div>
        
      </div>
    </nav>
  </header>
  <br>
    <br>
  <div id="box"> 
     <form action="#" method="POST">
        <h3>Veuillez insérer un trajet de voyage</h3>
          <br><hr><br>
        <div>
          <label>Depart</label>
          
          <select id="input1" name="depart" aria-placeholder="20 places" style="width: 250px; height: 40px;">
        <option value="Douala">Douala</option>
          <option value="Yaounde">Yaounde</option>
          <option value="Bafoussam">Bafoussam</option>
          <option value="Mbouda">Mbouda</option>
          <option value="Dschang">Dschang</option>
          <option value="Bafang">Bafang</option>
          <option value="Edea">Edea</option>
          <option value="Bamenda">Bamenda</option>
          <option value="Foumbot">Foumbot</option>
          <option value="Bagante">Bagante</option>
          <option value="Kribi">Kribi</option>
          <option value="Ngaoundere">Ngaoundere</option>
          <option value="Ebolowa">Ebolowa</option>
        </select>
       </div><br>
       <div>
          <label>Arrivée</label>
          <select id="input1" name="arrivee" aria-placeholder="20 places" style="width: 250px; height: 40px;">
        <option value="Douala">Douala</option>
          <option value="Yaounde">Yaounde</option>
          <option value="Bafoussam">Bafoussam</option>
          <option value="Mbouda">Mbouda</option>
          <option value="Dschang">Dschang</option>
          <option value="Bafang">Bafang</option>
          <option value="Edea">Edea</option>
          <option value="Bamenda">Bamenda</option>
          <option value="Foumbot">Foumbot</option>
          <option value="Bagante">Bagante</option>
          <option value="Kribi">Kribi</option>
          <option value="Ngaoundere">Ngaoundere</option>
          <option value="Ebolowa">Ebolowa</option>
        </select>
       </div> <br><br>
       <div class="form-group">
          <label for="select">type de bus:</label>
          <select id="select" name="select" aria-placeholder="2 places" style="width: 250px; height: 40px;">
          <option value=" classique">Bus classique</option>
          <option value=" VIP">Bus VIP</option></select>
       </div><br><br>
       <div>
          <label>heure départ</label>
          <input type="time" id="cni" name="partir" >
       </div><br>
       <div>
          <label>heure d'arrivée</label>
          <input type="time" id="cni" name="destination" >
       </div><br>
       <div>
          <label>jour Depart</label>
          <input type="date" class="date-input" name="date">
       </div><br>
       <div>
          <label>prix</label>
          <input type="text" id="cni" name="prix" class="class3">
        </div><br>
       <div class="bouton">
         <div><input type="submit" id="ins" value="insérer"></div>
         <div><input type="reset" id="annu" value="Annuler"></div>
       </div><br><br>
     </form>
 </div>
      <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
      <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>

 <form action="listevoyadmin.php" method="post">
   <div class="end">
       <div class="para"><p>consultez la liste des voyages disponibles en cliquant sur le bouton suivant:</p> </div>
       <div> <button type="submit" class="liste-voyage">liste voyages</button> </div>
   </div>
 </form>

<?php
try
{
$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
if(isset($_POST["depart"]) && isset($_POST["prix"]) && isset($_POST["destination"]) && isset($_POST["arrivee"]) && isset($_POST["select"]) && isset($_POST["partir"]) && isset($_POST["date"])){
echo("connexion reussie");
$depart=$_POST["depart"];
$arrive=$_POST["arrivee"];
$bus=$_POST["select"];
$heureDepart=$_POST["partir"];
$heureArrivee=$_POST["destination"];
$prix=$_POST["prix"];
$date=$_POST["date"];


$requette = "insert into voyage (villeDepart,	villeArrivee,	typeBus, prix, heureDepart, heureArrivee, jourDepart) values ('$depart', '$arrive', '$bus', '$prix' ,'$heureDepart', '$heureArrivee', '$date');";
$bdd->exec($requette);
echo("insertion reussie");
}

}
catch (Exception $e)
{
echo("echec de connexion");
}
?>
        
     <style>


/* PARTIE HEADER */
  /* PARTIE HEADER */

  header{
    width: 1400px;
    background-color: green;
    height: 120px;
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
}
.items a{
    text-decoration: none;
    color: whitesmoke;
    font-size: 20px;
   margin-right: 20px;
   padding: 0 15px; 
}

.public{
   margin-right: 90px;
   display: flex;
   align-items: center;  
}

.outils i {
  color: white;
  font-size: 37px;
  margin-left: 30px;
  cursor: pointer;
}
 
.nav-bar ul{
    display: flex;
    list-style-type: none;
}

.nav-bar{
    margin-left: 25px;
    margin-top: 70px;
}

a {
  color: white;
  text-decoration: none;
  position: relative;
}

.nav-bar .items a:hover {
      color: rosybrown;
    }

.header-picture{
  margin-left: 50px;
}
.fa{
  font-size: 15px;
}

.liste a{
    color: 'black';
}
/* PARTIE MAIN */

#box{
    width: 480px;
    height: 595px;
     background-color:green;
    color: aliceblue;
   position: absolute;
   left: 35%;
   align-items: center;
   justify-content: center;
   flex-direction: column;
   border-radius: 5px;
   top: 170px;
   padding: 15px;
}

input{
    justify-content: space-between;
    margin-left: 15px;
    width: 250px; 
    height: 35px; 
    border-radius: 7px 
}
button[type="submit"],
button[type="reset"]
{
    height: 30px;
    margin-left: 20px;
    width: 120px;
    display: inline;
    align-items: center;   
}


label{
    margin-left: 20px;
    font-size: 20px;
}
#ins, 
#annu
{
    width: 100px;
    color: green;
    margin-left: 60px;
}

h2{
    text-align: center;
    font-size: 30px;
}

.bouton{
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.date-input {
    appearance: none;
    padding: 8px;
    border: 1px solid green;
    border-radius: 4px;
    font-size: 14px;
    border-color: green;
    width: 200px;
    height: 30px;
    font-size: 20px;
  }
  .liste-voyage{
    color: white;
    background-color: green;
    border-color: 2px solid green;
    font-size: 20px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 24px;
  }
  .para{
    font-size: 24px;  
    font-weight: bold;
    margin-left: 205px; 
  }
  .end{
    display: flex;
    text-align: center;
  }
  .class1{
    margin-left: 120px;
  }
  .class2{
    margin-left: 110px;
  }
  .class3{
    margin-left: 130px;
  }
     </style>
     
</body>
</html>