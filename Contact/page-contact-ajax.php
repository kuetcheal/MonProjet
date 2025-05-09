<?php

header('Content-Type: application/json');

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    if (
        isset($_POST['name']) && 
        isset($_POST['mail']) && 
        isset($_POST['phone']) && 
        isset($_POST['adresse']) &&  
        isset($_POST['messager']) && 
        isset($_POST['ville'])
    ) {
        $nom = $_POST['name'];
        $email = $_POST['mail'];
        $telephone = $_POST['phone'];
        $adresse = $_POST['adresse'];
        $message = $_POST['messager'];
        $nomVille = $_POST['ville'];

        $requete = $bdd->prepare("INSERT INTO message (nom, email, tel, adresse, messager, nomVille) VALUES (?, ?, ?, ?, ?, ?)");

        if ($requete->execute([$nom, $email, $telephone, $adresse, $message, $nomVille])) {
            // Créer un tiers dans Dolibarr via l'API REST
            $dolibarr_url = 'http://localhost:100/dolibarr/api/index.php/thirdparties';
            $api_key = '809d8187e33a2186b77a7b780ee5fe8219554e79';
            
            // Préparer les données du tiers à envoyer à l'API
            $data = array(
                'name' => $nom,
                'address' => $adresse,
                'zip' => '95200', // Ajoutez un code postal si disponible
                'town' => $nomVille,
                'email' => $email,
                'phone' => $telephone,
                'client' => 1 // 1 si c'est un client, 0 sinon
            );

            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/json\r\n" .
                                 "DOLAPIKEY: $api_key\r\n",
                    'method'  => 'POST',
                    'content' => json_encode($data)
                )
            );

            $context  = stream_context_create($options);
            $result = file_get_contents($dolibarr_url, false, $context);

            if ($result === FALSE) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du tiers dans Dolibarr.']);
            } else {
                echo json_encode(['status' => 'success']);
            }
        } else {
            $errorInfo = $requete->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'Erreur d\'insertion : ' . $errorInfo[2]]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Tous les champs sont obligatoires.']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Echec de connexion: ' . $e->getMessage()]);
}
?>