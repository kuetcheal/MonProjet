<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';


require __DIR__.'/vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;

?>


<?php

try
{
$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
if(isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["email"]) && isset($_POST["telephone"]) && isset($_POST['submit'])){
// echo("connexion reussie");


$noun=$_POST["nom"];
$prenoun=$_POST["prenom"];
$mail2=$_POST["email"];
$phone=$_POST["telephone"];
$reservationNumber = $_POST['reservationNumber'];

$etat=0;
$Depart=$_SESSION["depart"];
$Arrivee=$_SESSION["arrivee"];
$date=$_SESSION["date"];
$idvoyage=$_SESSION['idVoyage'];
$prix=$_SESSION["prix"];


$html2pdf = new Html2Pdf();
$html=' 
<h1> reçu de reservation </h1> </br>
</div class="voyageur">
  <div class="infos-voyageur">
     <p>Numéro reservation : '.$idvoyage.'</p>
     <p>Compagnie : Général voyage</p>
     <p>Passager : '.$noun.' '.$prenoun.'</p>
     <p>Téléphone : '.$phone.'</p>
     <p>Numero Ref : '.$reservationNumber.'</p>
  </div>
  <div class="header-picture">
     <img src="logo général.jpg" alt="logo site" />
  </div>
</div>
<p>
Hello, Votre voyage numéro '.$idvoyage.' allant de '.$Depart.' à '.$Arrivee.' a été effectué avec succès en date du 
'.$date.'  
</p></br>

';

$html2pdf->writeHTML('<h1>HelloWorld</h1>This is my first');
$html2pdf->output('C:/wamp64/www/MonProjet/Alex.pdf', 'F');


$requette = "insert into reservation (nom, prenom,	telephone, email, idVoyage, Etat) values ('$noun', '$prenoun', '$mail2', '$phone', '$idvoyage', '$etat');";
$bdd->exec($requette);
echo ("<meta http-equiv='refresh' content='10;url=Accueil.php'>");
echo("<div style='height: 100px; width: 600px; background-color: green; color: white;
 font-size: 20px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px; '>
 Hello $noun, vous avez éffectué avec succès votre reservation un code de confirmation comportant votre reçu de reservation  a été envoyé dans votre boîte email. 
</div>");

$mail= new PHPMailer();
//Server settings
 $mail->SMTPDebug = 2; 

$mail->isSMTP();                                            //Send using SMTP
 $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username = 'elockfrank4@gmail.com';                     // SMTP username
    $mail->Password = 'lidf nupy pnap hkbl';                             //SMTP password
$mail->SMTPSecure = 'TLS';           
$mail->Port       = 587;                                    

$mail->setFrom('kuetchealex99@gmail.com', 'Easy travel');
$mail->addAddress($mail2, 'Joe User');     //Add a recipient

$mail->isHTML(true);                                  //Set email format to HTML
$mail->Subject = 'Confirmation de reservation';
$mail->Body    = '<p style="font-size: 15px; font-weight: bold;"> Bravoo '. $noun .'  votre reservation a été effectuée avec succès. Notre agence vous souhaîtes un bon voyage. </p>'; 
$mail->addAttachment('C:/wamp64/www/MonProjet/reçu.pdf', 'reçu.pdf');
$mail->send();

exit;
}

}
catch (Exception $e)
{
echo("echec de connexion");
}
//  include("footer.php");
?>

<style>
.container {
    height: 100px;
    width: 600px;
    background-color: green;
    color: white;
    font-size: 16px;

}
</style>