<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config.php';

try {
    if (
        isset($_POST['name'], $_POST['gmail'], $_POST['message'], $_POST['telephone'], $_POST['choix'])
    ) {
        $a = trim($_POST['name']);
        $d = trim($_POST['telephone']);
        $b = trim($_POST['gmail']);
        $c = trim($_POST['message']);
        $e = trim($_POST['choix']);

        $requete = $pdo->prepare("
            INSERT INTO admins (nom, email, messa, telephone, Nom_ville)
            VALUES (?, ?, ?, ?, ?)
        ");

        if ($requete->execute([$a, $b, $c, $d, $e])) {
            echo json_encode([
                'status' => 'success',
                'redirect' => 'merci.php'
            ]);
            exit;
        }

        echo json_encode([
            'status' => 'error',
            'message' => "Erreur lors de l'insertion."
        ]);
        exit;
    }

    echo json_encode([
        'status' => 'error',
        'message' => 'Veuillez remplir tous les champs.'
    ]);
    exit;

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Échec : ' . $e->getMessage()
    ]);
    exit;
}