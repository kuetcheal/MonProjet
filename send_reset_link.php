<?php
session_start();

require_once __DIR__ . '/config.php';

use Mailjet\Client;
use Mailjet\Resources;

$message = '';

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT * FROM user WHERE user_mail = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(50));

        $stmt = $pdo->prepare("UPDATE user SET reset_token = :token WHERE user_mail = :email");
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $resetLink = "http://localhost/MonProjet/reset_password.php?token=" . urlencode($token);

        $mj = new Client(
            MAILJET_PUBLIC_KEY,
            MAILJET_PRIVATE_KEY,
            true,
            ['version' => 'v3.1']
        );

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => MAIL_FROM_EMAIL,
                        'Name' => MAIL_FROM_NAME,
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $user['user_name'],
                        ],
                    ],
                    'Subject' => 'Réinitialisation de votre mot de passe',
                    'TextPart' => 'Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe : ' . $resetLink,
                    'HTMLPart' => "
                        <p>Veuillez cliquer sur le lien suivant pour réinitialiser votre mot de passe :</p>
                        <p><a href='" . htmlspecialchars($resetLink, ENT_QUOTES) . "'>$resetLink</a></p>
                    ",
                ],
            ],
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);

        $message = $response->success()
            ? 'Un email de réinitialisation a été envoyé.'
            : "Erreur lors de l'envoi de l'email.";
    } else {
        $message = "Cet email n'existe pas dans notre base de données.";
    }
}
?>