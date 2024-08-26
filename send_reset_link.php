<?php
session_start();
require __DIR__.'/vendor/autoload.php';
use Mailjet\Client;
use Mailjet\Resources;
use PDO;

if (isset($_POST['email'])) {
    $email = $_POST['email'];

    // Connexion à la base de données
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    // Vérification si l'email existe dans la base de données
    $stmt = $bdd->prepare("SELECT * FROM user WHERE user_mail = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // L'email existe, créer un jeton unique
        $token = bin2hex(random_bytes(50));
        
        // Stocker le jeton dans la base de données
        $stmt = $bdd->prepare("UPDATE user SET reset_token = :token WHERE user_mail = :email");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Lien de réinitialisation
        $resetLink = "http://localhost:100/dolibarr/reset_password.php?token=$token";

        // Envoi de l'email avec le lien de réinitialisation
        $mj = new Client('f163a8d176afbcb29aae519bf6c5e181', 'bf285777b4d59f84a43855ae1b40f96d', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'akuetche55@gmail.com',
                        'Name' => 'Easy travel',
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $user['user_name'],
                        ],
                    ],
                    'Subject' => 'Réinitialisation de votre mot de passe',
                    'TextPart' => 'Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe :',
                    'HTMLPart' => "<p>Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe :</p>
                                   <p><a href='$resetLink'>$resetLink</a></p>",
                ],
            ],
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            echo 'Un email de réinitialisation a été envoyé.';
        } else {
            echo 'Erreur lors de l\'envoi de l\'email.';
        }
    } else {
        // L'email n'existe pas
        echo 'Cet email n\'existe pas dans notre base de données.';
    }
}
?>