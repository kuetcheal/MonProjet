<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

try {

    if (
        isset($_POST['name'], $_POST['mail'], $_POST['phone'], $_POST['adresse'], $_POST['messager'], $_POST['ville'])
    ) {
        $nom = trim($_POST['name']);
        $email = trim($_POST['mail']);
        $telephone = trim($_POST['phone']);
        $adresse = trim($_POST['adresse']);
        $message = trim($_POST['messager']);
        $nomVille = trim($_POST['ville']);

        // INSERT BDD
        $requete = $pdo->prepare("
            INSERT INTO message (nom, email, tel, adresse, messager, nomVille)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        if ($requete->execute([$nom, $email, $telephone, $adresse, $message, $nomVille])) {

            /* =========================
               DOLIBARR API
            ========================= */

            $dolibarr_url = $_ENV['DOLIBARR_URL'] . '/thirdparties';
            $api_key = $_ENV['DOLIBARR_API_KEY'];

            $data = [
                'name' => $nom,
                'address' => $adresse,
                'zip' => '00000',
                'town' => $nomVille,
                'email' => $email,
                'phone' => $telephone,
                'client' => 1
            ];

            $options = [
                'http' => [
                    'header'  => "Content-type: application/json\r\nDOLAPIKEY: $api_key\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data),
                    'ignore_errors' => true
                ]
            ];

            $context = stream_context_create($options);
            $result = @file_get_contents($dolibarr_url, false, $context);

            if ($result === false) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Message enregistré mais Dolibarr inaccessible'
                ]);
                exit;
            }

            echo json_encode(['status' => 'success']);
            exit;
        }

        echo json_encode([
            'status' => 'error',
            'message' => "Erreur lors de l'insertion"
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Tous les champs sont obligatoires.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Erreur : ' . $e->getMessage()
    ]);
    exit;
}