<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
$mail= new PHPMailer(true);
try
{
    
//Server settings
$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
$mail->isSMTP();                                            //Send using SMTP
// $mail->Host       = 'smtp.example.com';                     //Set the SMTP server to send through
$mail->SMTPAuth   = true;                                   //Enable SMTP authentication
$mail->Username   = 'kuetchealex99@gmail.com';                     //SMTP username
$mail->Password   = 'alex1995';                               //SMTP password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
$mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

//Recipients
$mail->setFrom('kuetchealex99@gmail.com', 'administrateur');
$mail->addAddress('kengnelilye@gmail.com', 'Joe User');     //Add a recipient
// $mail->addAddress('ellen@example.com');               
// $mail->addReplyTo('info@example.com', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');

//Attachments
// $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

//Content
$mail->isHTML(true);                                  //Set email format to HTML
$mail->Subject = 'coucou lilye';
$mail->Body    = 'alex le boss <b>cool</b>';
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

$mail->send();

echo "Inscription réussie. Un email de confirmation a été envoyé à l'adresse $mail.";

}

catch (Exception $e)
{
echo("echec de connexion");
}
?>