<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Formulaire d'inscription </h1>
    <form action="#" method="post">
        <div class="box">
            <div class="nom">
                <label for="nom"> Nom:</label>
                <input type="text" name="noun" id="">
            </div>
            <div class="nom">
                <label for="nom"> Email:</label>
                <input type="email" name="mail" id="">
            </div>
            <div class="nom">
                <label for="nom"> Mot de passe:</label>
                <input type="password" name="passe" id="">

            </div>
            <button type="submit">s'inscrire</button>
        </div>
    </form>

    <?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=projet_php', 'root', '');

    if (isset($_POST['noun']) && isset($_POST['mail']) && isset($_POST['passe'])) {
        $nom = $_POST['noun'];
        $email = $_POST['mail'];
        $motpasse = $_POST['passe'];
        $requete = "INSERT INTO users (Nom, Email, Password) VALUES ('$nom', '$email', '$motpasse');";
        $bdd->exec($requete);

        header('Location: nav.php');
        exit;
    }
}
 catch (Exception $e) 
{
    echo 'Échec de connexion';
}
    ?>


</body>

</html>