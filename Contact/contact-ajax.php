<?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    if (isset($_POST['name']) && isset($_POST['gmail']) && isset($_POST['message']) && isset($_POST['telephone']) && isset($_POST['choix'])) {
        $a = $_POST['name'];
        $d = $_POST['telephone'];
        $b = $_POST['gmail'];
        $c = $_POST['messa'];
        $e = $_POST['choix'];

        $requete = $bdd->prepare("INSERT INTO admins (nom, email, messa, telephone, Nom_ville) VALUES (?, ?, ?, ?, ?)");
        
        // Exécution de la requête
        if ($requete->execute([$a, $b, $c, $d, $e])) {
            echo json_encode(['status' => 'success', 'redirect' => 'merci.php']);
        } else {
            $errorInfo = $requete->errorInfo();
            echo json_encode(['status' => 'error', 'message' => 'Erreur d\'insertion : ' . $errorInfo[2]]);
        }
    }
} catch (Exception $e) {
    // En cas d'erreur de connexion ou d'exécution de la requête
    echo json_encode(['status' => 'error', 'message' => 'Echec de connexion: ' . $e->getMessage()]);
}
?>