<?php
session_start();
require __DIR__.'/vendor/autoload.php';

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf();

$idvoyage = isset($_SESSION['idVoyage']) ? $_SESSION['idVoyage'] : '';
$noun = isset($_SESSION["nom"]) ? $_SESSION["nom"] : '';
$prenoun = isset($_SESSION["prenom"]) ? $_SESSION["prenom"] : '';
$phone = isset($_SESSION["telephone"]) ? $_SESSION["telephone"] : '';
$Depart = isset($_SESSION["depart"]) ? $_SESSION["depart"] : '';
$Arrivee = isset($_SESSION["arrivee"]) ? $_SESSION["arrivee"] : '';
$date = isset($_SESSION["date"]) ? $_SESSION["date"] : '';
$prix = isset($_SESSION["prix"]) ? $_SESSION["prix"] : '';

$html2pdf->writeHTML('<h1 style= " text-align: center; "> reçu de reservation </h1> 
<div style="display: flex; align-items: center;">
  <div class="infos-voyageur" >
     <p>Numéro reservation : '.$idvoyage.'</p>
     <p>Compagnie : Général voyage</p>
     <p>Passager : '.$noun.' '.$prenoun.'</p>
     <p>Prix : '.$prix.' euros </p>
   </div>
    <div class="header-picture">
     <img src="logo général.jpg" alt="logo site" style= " height: 90px; width: 130px; left: 400px; bottom: 300px; position: fixed; "/>
     </div>
</div>
<p>
Hello, Votre voyage numéro '.$idvoyage.' allant de '.$Depart.' à '.$Arrivee.' a été effectué avec succès en date du 
'.$date.'  
</p>');
$html2pdf->output('C:/wamp64/www/MonProjet/reçu.pdf', 'F');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>

<body>

    <?php
    // print_r($_SESSION);
     ?>
    <style>
    h1 {
        text-align: center;
    }

    .voyageur {
        display: flex;
        justify-content: space-between;
    }
    </style>
</body>

</html>