<?php
session_start(); // Démarrer la session (si ce n'est pas déjà fait)

// Vérifier si le formulaire de déconnexion a été soumis
// git test unitaires
if (isset($_POST['deconnect_account'])) {
    // Supprimer toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();

    // Rediriger vers la page de connexion
    header('Location: connexion.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"> -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <link rel="stylesheet" href="style.css">

    <title>Document</title>
</head>

<body>

    <!-- PARTIE HEADER -->
    <header class="container-fluid">
        <nav class="container-fluid">
            <div class=" header-picture">
                <img src="logo général.jpg" alt="logo site" />
            </div>
            <div class="nav-bar">
                <ul>
                    <li class="items"> <a href="#">Acceuil</a></li>
                    <li class="items"><a href="ajoutarticle.php">Reservations</a></li>
                    <li class="items"><a href="ajoutclient.php">Nos services clients</a></li>
                    <li class="items"><a href="achatarticle.php">nos achats</a></li>
                </ul>
            </div>
            <div class="public">
                <div class="notif">
                    <i class="fa fa-bell-o" aria-hidden="true"></i>
                </div>
                <div class="langue">
                    <!-- <select id="select" name="select" aria-placeholder="2 places">
                        <option value="option1">Français</option>
                        <option value="option2">Anglais</option>
                    </select> -->
                    <i class="fa fa-globe" aria-hidden="true"></i>
                </div>
                <div class="outils">
                    <img src="pictures/OIP.jpg" id="profil" onclick="openModal('myModal')" alt="image user" />
                </div>

            </div>
        </nav>
    </header>

    <!-- AFFICHAGE DE LA POPUP DU SETTINGS  -->
    <div id="myModal" class="modal1" style="display: none;">
        <div class="modal-content1">
            <span class="close1" onclick="closeModal('myModal')" style="position: fixed; right: 365px; top: 12px;"><i
                    class=" fa
                fa-arrow-left" aria-hidden="true">
                </i></span>
            <h2>Mon compte utilisateur</h2> <br>
            <hr style=" color: green;"><br><br>
            <div class="profiler">
                <div>
                    <img src=" pictures/OIP.jpg" alt="image user" id="profil-pic" style=" position: fixed; top: 75px" />

                    <input type="file" accept="image/png, image/jpeg, image/jpg" id="input-file" style="display: none">
                    <label for="input-file"
                        style=" position: fixed; top: 164px; height: 20px; width:110px; background-color: green; color: white; font-size: 12px; padding 8px:">Download
                        image</label>
                </div>

                <div class="profil-infos" style=" margin-left: 145px; ">
                    <p>Alex KUETCHE</p>
                    <P style=" color: green; ">alexkuetche@gmail.com</P>
                </div>
            </div> <br><br>
            <hr style=" color: green;"><br><br>
            <h2 style=" color: green; text-align: center; "> Mes connexions</h2><br>
            <p style=" font-size: 16px; ">Veuilez accéder au contenu settings de l'application </p><br>
            <div style=" display: flex; align-items: center; flex-direction: column; gap: 23px; ">
                <button type=" submit" class="btn-supprimer" style=" width: 350px;" onclick="openModal('myModal1')">
                    Supprimer</button>
                <button type="submit" class="btn-deconnecter" style=" width: 350px;" onclick="openModal('myModal2')">Se
                    déconnecter</button>
                <button type="submit" class="btn-modifier" style=" width: 350px;"
                    onclick="openModal('myModal3')">Modifier</button>
            </div><br><br>
            <div>
                <img src="logo général.jpg" alt="logo site" style=" height: 60px; width: 110px; margin-left: 140px;" />
            </div>
        </div>
    </div>

    <!-- AFFICHAGE DU POPUP DE MESSAGE DE SUPPRESSION -->
    <div id="myModal1" class="modal1">
        <div class="modal-content1">
            <h2 style=" text-align: center;">Réglages</h2>
            <span class="close1" onclick="closeModal('myModal1')" style="position: fixed; right: 385px; top: 12px;"><i
                    class=" fa
                fa-arrow-left" aria-hidden="true">
                </i></span><br>
            <h2>Suppression du compte</h2> <br><br>
            <hr style=" color: green;"><br><br>
            <div class="profil" style=" display: flex; ">
                <div class="outils"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"
                        style=" color: rgb(128, 128, 128); font-size: 60px; position: fixed; right: 335px; "></i>
                </div>
                <div class="profil-infos" style=" margin-left: 80px; ">
                    <p>Alex KUETCHE</p>
                    <P style=" color: green; ">alexkuetche@gmail.com</P>
                </div>
            </div> <br><br>
            <hr style=" color: green;"><br><br><br>
            <p style=" text-align: center; ">Attention !!! cette action effacera définitivement votre compte
                de
                l'application.
                êtes-vous sûr de vouloir supprimer votre compte ? </p> <br><br><br>
            <form method="post" action="">
                <button name="delete_account" class="btn-supprimer"
                    style=" background-color: green; color: white; border: none; border-radius: 5px;
       border-color: 2px solid green; font-size: 20px; height: 30px; width: 120px; margin-left: 135px;">Supprimer</button>
                <br><br><br><br><br><br>
                <div>
                    <img src="logo général.jpg" alt="logo site"
                        style=" height: 60px; width: 110px; margin-left: 140px;" />
                </div>
            </form>
        </div>
    </div>

    <!-- CODE PHP POUR IMPORTER LES IMAGES DEPUIS LA GALERIE -->
    <?php

?>


    <!-- AFFICHAGE DU POPUP DE MESSAGE DE DECONNECTION -->
    <div id="myModal2" class="modal1">
        <div class="modal-content1">
            <h2 style=" text-align: center;">Réglages</h2>
            <span class="close1" onclick="closeModal('myModal2')" style="position: fixed; right: 385px; top: 12px;"><i
                    class=" fa
                fa-arrow-left" aria-hidden="true">
                </i></span><br>
            <h2>Deconnexion du compte</h2> <br><br>
            <hr style=" color: green;"><br><br>
            <div class="profil" style=" display: flex; ">
                <div class="outils"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"
                        style=" color: rgb(128, 128, 128); font-size: 60px; position: fixed; right: 335px; "></i>
                </div>
                <div class="profil-infos" style=" margin-left: 80px; ">
                    <p>Alex KUETCHE</p>
                    <P style=" color: green; ">alexkuetche@gmail.com</P>
                </div>
            </div> <br><br>
            <hr style=" color: green;"><br><br><br>
            <p style=" text-align: center; ">Attention !!! cette action déconnectera votre compte de
                l'application.
                êtes-vous sûr de vouloir vous déconnecter votre compte ? </p> <br><br><br>
            <form method="post" action="">
                <button name="delete_account" class="btn-deconnecter" style="margin-left: 105px;">Se
                    déconnecter</button>
                <br><br><br><br>
                <div>
                    <img src=" logo général.jpg" alt="logo site"
                        style=" height: 60px; width: 110px; margin-left: 140px;" />
                </div>
            </form>
        </div>
    </div>


    <!-- AFFICHAGE DU POPUP DE MESSAGE DE MODIFICATION -->


    <div id="myModal3" class="modal1">
        <div class="modal-content1">
            <h2 style=" text-align: center;">Réglages</h2>
            <span class="close1" onclick="closeModal('myModal3')" style="position: fixed; right: 385px; top: 12px;"><i
                    class=" fa
                fa-arrow-left" aria-hidden="true">
                </i></span><br>
            <h2>Modification du compte</h2> <br>
            <hr style=" color: green;"><br>
            <form method="post" action="">
                <h4 style="font-size: 20px; color: green;">Nom d'utilisateur</h4>
                <label for="username" style="font-size: 16px; ">Veuillez saisir le nom
                    d'utilisateur:</label>
                <input type="text" name="username" id="username" placeholder="ALEX" style="width: 370px; "
                    required><br><br>
                <h4 style="font-size: 20px; color: green;">Mot de passe actuel</h4>
                <label for="username" style="font-size: 16px; ">Entrer votre mot de passe actuel:</label>
                <input type="text" name="password" id="username" placeholder="impact1999" style="width: 370px; "
                    required><br><br>
                <h4 style="font-size: 20px; color: green;">Nouveau mot de passe</h4>
                <label for="password" style="font-size: 16px;">Saisir votre nouveau mot de passe:</label>
                <input type="text" name="newPassword" id="username" placeholder="stephane2000" style="width: 370px; "
                    required><br><br><br>
                <h4 style="font-size: 20px; color: green;">Mot de passe actuel</h4>
                <label for="username" style="font-size: 16px; ">Confirmer votre nouveu mot de
                    passe:</label>
                <input type="text" name="confirmPassword" id="username" placeholder="stephane2000"
                    style="width: 370px; " required><br><br><br>
                <hr style=" color: green;"><br><br>
                <button name="delete_account" class="btn-modifier" style="margin-left: 105px;">
                    Modifier</button>
            </form>


        </div>
    </div>


    <!-- CODE PHP POUR LA SUPPRESSION DU COMPTE -->
    <?php
// Vérifier si le formulaire de suppression a été soumis
if (isset($_POST['delete_account'])) {
    $host = 'localhost'; // nom d'hôte
    $user = 'root'; // nom d'utilisateur
    $password = ''; // mot de passe
    $database = 'bd_stock'; // nom de la base de données

    // Connexion à la base de données MySQLi
    $conn = mysqli_connect($host, $user, $password, $database);
    $id = $_SESSION['Id_compte'];
    $query = "DELETE FROM user WHERE id_name =$id";
    mysqli_query($conn, $query);

    header('Location: connexion.php');
    exit;
}
?>
    <!-- PARTIE MAIN -->
    <div class="content">
        <div class="container">
            <p class="para"> le plaisir de bien voyager à un prix abordable à partir de 5.5euro seulement.</p>
        </div><br>
        <h1 class="titre"> Rechercher un trajet de voyage </h1><br>
    </div>
    <div class="box" style=" gap: 48px;">
        <form action="listevoyageretour.php" method="post">
            <div class="Voyage" style="display: flex; gap: 25px;">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio1"
                        value="option1" checked>
                    <label class="form-check-label" for="inlineRadio1">Aller</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="inlineRadioOptions" id="inlineRadio2"
                        value="option2">
                    <label class="form-check-label" for="inlineRadio2">Aller-Retour</label>
                </div>
            </div>

            <div class="form-group">
                <label for="select">DE :</label>
                <select id="input1" name="input1" aria-placeholder="20 places" style="width: 150px; height: 40px;">
                    <option value="Douala">Douala</option>
                    <option value="Yaounde">Yaounde</option>
                    <option value="Bafoussam">Bafoussam</option>
                    <option value="Mbouda">Mbouda</option>
                    <option value="Dschang">Dschang</option>
                    <option value="Bafang">Bafang</option>
                    <option value="Edea">Edea</option>
                    <option value="Bamenda">Bamenda</option>
                    <option value="Foumbot">Foumbot</option>
                    <option value="Bagante">Bagante</option>
                    <option value="Kribi">Kribi</option>
                    <option value="Ngaoundere">Ngaoundere</option>
                    <option value="Ebolowa">Ebolowa</option>
                </select>
            </div>

            <div class="form-group">
                <label for="select">A :</label>
                <select id="input2" name="input2" aria-placeholder="20 places" style="width: 150px; height: 40px;">
                    <option value="Douala">Douala</option>
                    <option value="Yaounde">Yaounde</option>
                    <option value="Bafoussam">Bafoussam</option>
                    <option value="Mbouda">Mbouda</option>
                    <option value="Dschang">Dschang</option>
                    <option value="Bafang">Bafang</option>
                    <option value="Edea">Edea</option>
                    <option value="Bamenda">Bamenda</option>
                    <option value="Foumbot">Foumbot</option>
                    <option value="Bagante">Bagante</option>
                    <option value="Kribi">Kribi</option>
                    <option value="Ngaoundere">Ngaoundere</option>
                    <option value="Ebolowa">Ebolowa</option>
                </select>
            </div>
            <div class="form-group">
                <label for="input3">Date départ :</label>
                <input type="date" class="date-input" name="input3" style="width: 150px; height: 35px;">
            </div>
            <div class="form-groupe" style="display: none; position: relative; left: 550px; bottom: 76px">
                <label for="input3">Date retour :</label>
                <input type="date" class="date-input" name="input4" style="width: 150px; height: 35px;">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value=" Valider"
                    style="background-color: green; color: white;  width: 150px; height: 40px;">


            </div>
    </div>
    </form>


    </div>
    <div class="button-container">
        <button class="button" id="openModalButton">Gérer ma réservation</button>
        <button class="button">Localiser mon trajet</button>
        <button class="button">Besoin d'aide</button>
    </div>

    <hr>

    <div class="map-box">
        <div class="map-content">
            <div class="map-image">
                <img src="pictures/carte.jpg" alt="Carte du Cameroun" id="carte">
                <div class="city-marker douala">
                    <span>Douala</span>
                </div>
                <div class="city-marker yaounde">
                    <span>Yaoundé</span>
                </div>
                <div class="city-marker bamenda">
                    <span>Bamenda</span>
                </div>
            </div>
            <br>
            <hr><br>
            <div class="map-infos">
                <a
                    href="https://www.google.com/maps/d/viewer?mid=1EYecAk1HDlJM2-XAEl5KQzJgsbQ&hl=en&ll=3.871870776159668%2C11.490017499999983&z=12">
                    <h2 class="indication">Découvrez toutes nos destinations</h2>
                </a>
                <p>Notre agence possède plus de 30 destinations dans toutes les grandes villes en allant du sud
                    vers
                    le
                    nord en
                    passant par l'Est et l'ouest du pays.</p>
            </div>
        </div>
    </div>
    <br>
    <hr><br>
    <div class="rectangle">
        <p> <span> Voyagez sur le plus grand réseau camerounais de bus longue distance !</span> Depuis 2000,
            général
            voyage
            agrandit continuellement son réseaucamerounais et dessert désormais chaque jour plus de 100
            destinations
            dont plus
            de 30 villes au cameroun.
            Notre objectif est de rendre le Cameroun verte ! Le réseau de général voyage s’étend déjà du Sud à
            l'EST-Cameroun ainsi que vers le grand Nord Cameroun.Vous pouvez découvrir l'intégralité de notre
            réseau
            de
            bus
            longue distance sur notre carte interactive. Réservez dès maintenant votre voyage en bus pour
            Yaounde,
            Kribi,
            ABamenda, Edea, Banga et bien d’autres ! Si vous manquez d'inspiration pour votre prochain voyage,
            consultez notre page idées de voyages ; pour choisir une destination originale ou consultez la liste
            complète de
            nos destinations.</p>
        <br>
        <h2>C’est simple et confortable</h2><br>
        <p>
            Voyager n’a jamais été aussi simple avec gGénéral voyage car nous vous accompagnons de la première à
            la
            dernière étape.
            Grâce à des informations détaillées sur notre site web et à notre personnel serviable, vous pouvez
            parfaitement
            planifier votre voyage et embarquer sereinement. Vous pouvez<span> acheter votre billet sur notre
                site
                internet</span> ou bien même au dernier moment auprès du conducteur. Votre billet Général voyage
            vous
            offre <span>la
                garantie d’une place assise</span> avec beaucoup<span> d’espace pour vos jambes</span>.
            Si lors du trajet vous avez une petite faim, pas de problème ! Nos conducteurs proposent des snacks
            et
            boissons à
            petit prix. A bord de nos bus, vous aurez <span>accès à notre Wi-fi gratuit</span> afin de lire vos
            mails,
            écouter
            de la musique ou poster des photos sur vos réseaux sociaux. De plus, des prises électriques sont
            présentes à
            coté
            de chaque siège et elles vous seront bien utiles lors des longs trajets !</p>
        <br>
        <h2>C’est économique et bon pour l'environnement</h2><br>
        <p style='text-align: center;'>
            Avec général voyage, il est facile d'économiser : les prix défiant toute concurrence de nos billets
            soulagent
            votre porte-monnaie tandis que le confort de nos bus épargne vos nerfs, des pauses à repetition et
            aux
            demandes
            des passagers pour satisfaire leur besoin. De plus,
            voyager en bus longue distance est le mode de transport le plus respectueux de l'environnement, avec
            un
            bilan CO2
            exemplaire. « Roulez vert » est notre devise !</p>


    </div>


    <div class="chat">
        <i class="fa fa-comments fa-5x" onclick="openModal('myModal5')"
            style="color: green; position: fixed; top: 566px; right: 18px; cursor: pointer; font-size: 61px;"></i>
    </div>

    <form action="" method="POST">
        <div class=" modal1" id="myModal5" style="display: none;">
            <div class="contente">
                <div class="modal-header">
                    <h2 class="modal-title">Contactez l'agence EasyTravel</h2>
                    <span class="close1" onclick="closeModal('myModal5')"
                        style="position: fixed; right: 365px; top: 77px;">
                        <i class="fa fa-times" aria-hidden="true" style="font-size: 25px"></i></span>
                </div>
                <br>
                <span>
                    <hr>
                </span> <br>
                <div class="modal-body">
                    <div class="mbx">
                        <label for="">Votre nom</label>
                        <input type="text" class="form-control" placeholder="Alex KUETCHE" name="name">
                    </div>
                    <div class="mbx">
                        <label for="">Numéro téléphone</label>
                        <input type="text" class="form-control" placeholder="655196254" name="telephone">
                    </div>
                    <div class=" mbx">
                        <label>Email address</label>
                        <input type="email" class="form-control" id="exampleFormControlInput1" name="gmail" placeholder="
                            name@example.com">
                    </div>
                    <div class="mbz">
                        <label for="exampleFormControlTextarea1" class="form-label">Rediger votre
                            message</label>
                        <textarea class=" form-control" id="exampleFormControlTextarea1" rows="3"
                            name="message"></textarea>
                    </div>
                </div>
                <br>
                <span>
                    <hr>
                </span> <br>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" onclick="openModal('myModal6')"
                        style="background-color: green; border-color: green;">Envoyer</button>
                </div>
            </div>

        </div>
    </form>

    <div id="myModal6" class="modale" style="display: none;">
        <div class="modale-contente">

            <h2>Insertion réussie</h2> <br>
            <p>Votre message a été inséré avec succès.</p> <br>
            <button onclick="closeModal('myModal6')">Fermer</button>
        </div>
    </div>';

    <?php
 $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

if (isset($_POST['name']) && isset($_POST['gmail']) && isset($_POST['message']) && isset($_POST['telephone'])) {
    try {
        $a = $_POST['name'];
        $d = $_POST['telephone'];
        $b = $_POST['gmail'];
        // $_SESSION['gmail']=$b;
        $c = $_POST['message'];
        // $_SESSION['message']=$c;

        $requete = "insert into admins (nom, email, message, telephone) values ('$a', '$b', '$c', '$d')";
        $bdd->exec($requete);
    } catch (Exception $e) {
        echo 'echec de connexion';
    }
}
?>

    <!-- PARIE FOOTER -->
    <?php include 'footer.php'; ?>



    <style>
    /* PARTIE HEADER */
    .date-input {
        appearance: none;
        padding: 8px;
        border: 1px solid green;
        border-radius: 4px;
        font-size: 14px;
        border-color: green;
        width: 200px;
        height: 30px;
        font-size: 20px;
    }

    header {
        width: 1400px;
        background-color: green;
        height: 120px;
    }

    nav {
        width: 100%;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    img {
        width: 120px;
        height: 80px;
        margin-top: 20px;

    }

    .items a {
        text-decoration: none;
        color: whitesmoke;
        font-size: 20px;
        margin-right: 20px;
        padding: 0 15px;
    }

    .public {
        margin-right: 90px;
        display: flex;
        align-items: center;

    }

    .outils i {
        color: white;
        font-size: 37px;
        margin-left: 30px;
        cursor: pointer;
    }

    /* Affichage de la popup */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        position: fixed;
        right: 5px;
        bottom: 197px;
        gap: 15px;
        display: flex;
        flex-direction: column;
        text-align: center;
        height: 120px;
        width: 190px;
        border-radius: 11px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        position: fixed;
        top: 80px;
        right: 15px;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .modal-title {
        display: flex;
        text-align: center;
    }

    .btn-supprimer {
        background-color: rgb(0, 128, 0);
        color: white;
        height: 30px;
        width: 180px;
        border: 1px solid rgb(0, 128, 0);
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-supprimer:hover {
        background-color: rgb(46, 184, 46);
    }

    .btn-deconnecter {
        background-color: rgb(128, 128, 128);
        color: white;
        height: 30px;
        width: 180px;
        border: 1px solid gray;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-deconnecter:hover {
        background-color: rgb(150, 150, 150);
    }

    .btn-modifier {
        background-color: rgb(188, 143, 143);
        color: white;
        height: 30px;
        width: 180px;
        border: 1px solid rosybrown;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-modifier:hover {
        background-color: rgb(167, 112, 112);
    }


    /* Affichage de la popup de suppression */
    .modal1 {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content1 {
        /* background-color: #fefefe; */
        background-color: aliceblue;
        /* margin: 15% auto; */
        padding: 20px;
        border: 1px solid #888;
        height: 600px;
        width: 400px;
        position: fixed;
        bottom: 41px;
        top: 1px;
        left: 930px;
    }

    .contente {
        background-color: white;
        padding: 20px;
        border: 2px solid white;
        border-radius: 5px;
        height: 450px;
        width: 600px;
        position: fixed;
        bottom: 71px;
        left: 340px;

    }

    .btn {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close1 {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close1:hover,
    .close1:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Affichage de la popup de déconnexion */
    .modal2 {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content2 {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
    }

    .close2 {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close2:hover,
    .close2:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .items a {
        text-decoration: none;
        color: whitesmoke;
        font-size: 20px;
        margin-right: 20px;
        padding: 0 15px;
    }

    .public {
        margin-right: 90px;
        display: flex;
        align-items: center;
    }

    #profil {

        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
    }

    .nav-bar ul {
        display: flex;
        list-style-type: none;
    }

    .nav-bar {
        margin-left: 25px;
        margin-top: 65px;
        background-color: #3e8e41;
        height: 43px;
        width: 705px;
        padding: 7px;
        border-radius: 20px;
    }

    a {
        color: white;
        text-decoration: none;
        position: relative;
    }

    .header-picture {
        margin-left: 50px;
        cursor: pointer;
    }

    .fa {
        font-size: 15px;
    }

    .liste a {
        color: 'black';
    }


    .outils {
        text-align: center;

        /* Centre le contenu horizontalement */
    }

    #profil-pic {
        position: fixed;
        top: 145px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        /* Donne une forme circulaire à l'image */
    }

    #input-file {
        display: none;
    }

    .import {
        cursor: pointer;
        position: fixed;
        top: 240px;
        /* Change le curseur au survol pour indiquer que le label est cliquable */
        color: green;
        /* Couleur du texte du label (vous pouvez ajuster selon votre style) */
        text-decoration: none;
        /* Souligne le texte du label */
    }

    /* Style supplémentaire pour améliorer l'aspect du label au survol */
    .import :hover {
        color: #0056b3;
        /* Changement de couleur au survol */
    }

    .fa-bell-o {
        color: white;
        font-size: 21px;
        cursor: pointer;
    }

    .fa-globe {
        color: white;
        font-size: 21px;
        cursor: pointer;
    }

    .modale {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modale-contente {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        height: 150px;
        text-align: center;
        border-radius: 8px;
    }



    /* PARTIE FOOTER */
    footer {
        background-color: rgb(247, 247, 247);
        padding: 40px;
        font-family: Arial, sans-serif;
        box-shadow: inset 5px 5px 10px -5px rgba(0, 0, 0, 0.5);
        width: 100%;
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }


    .social-media,
    .contact,
    .privacy {
        flex-basis: 30%;
        margin-bottom: 20px;
    }

    .footer-picture {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .social-icons {
        display: flex;
        justify-content: center;
        flex-basis: 30%;
        margin-bottom: 70px;
        margin-right: 50px;
    }

    .social-icons ul {
        display: flex;
        list-style-type: none;
    }

    .social-icons a {
        display: flex;
        justify-content: center;
        margin: 0 10px;
    }

    .social-icons i {
        font-size: 24px;
        color: black;

    }

    .social-icons i:hover {
        color: rosybrown;
    }

    .liste a {
        color: 'red';
    }

    h3 {
        margin-bottom: 10px;
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }

    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    li {
        margin-bottom: 10px;
    }

    h3 {
        color: red;
    }

    a {
        color: green;
        text-decoration: none;
        font-size: 14px;
    }

    a:hover {
        color: rosybrown;
    }

    li a {
        margin-left: 5px;
    }

    #apps {
        margin-right: 30px;
    }

    .social-download img {
        height: 50px;
        width: 90px;
        cursor: pointer;
    }

    #outillage {
        /* display: flex; */
        position: fixed;
        top: 50px;
    }


    /* MAIN */
    .container {
        padding: 20px;
        background-image: linear-gradient(rgba(39, 39, 39, 0.6), rgba(0, 0, 0, 0.6)), url("https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg");
        background-size: cover;
        height: 500px;
        color: aliceblue;
        flex-grow: 1;
        width: 100%;
        background-repeat: no-repeat;
    }

    /* Style de base pour les boutons radio personnalisés */
    .form-check-input {
        appearance: none;
        /* Supprime le style par défaut du navigateur */
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: transparent;
        border: 2px solid #ccc;
        border-radius: 50%;
        width: 25px;
        height: 25px;
        cursor: pointer;
        /* Change le curseur pour indiquer un élément cliquable */
        position: relative;
        /* Nécessaire pour positionner le pseudo-élément */
        transition: border-color 0.3s;
        /* Transition douce pour la couleur de la bordure */
    }

    /* Style lorsque le radio est sélectionné */
    .form-check-input:checked {
        border-color: green;
        /* Change la couleur de la bordure en vert lorsqu'il est sélectionné */
    }

    .form-check-input:checked::before {
        content: '';
        /* Nécessaire pour générer un cercle à l'intérieur */
        position: absolute;
        top: 50%;
        /* Centre le cercle verticalement */
        left: 50%;
        /* Centre le cercle horizontalement */
        width: 14px;
        /* Largeur du cercle intérieur */
        height: 14px;
        /* Hauteur du cercle intérieur */
        background-color: green;
        /* Fond vert pour l'état sélectionné */
        border-radius: 50%;
        /* Rend le pseudo-élément circulaire */
        transform: translate(-50%, -50%);
        /* Assure que le pseudo-élément est parfaitement centré */
    }

    /* Amélioration de l'interactivité au survol */
    .form-check-input:hover:not(:checked) {
        border-color: #aaa;
        /* Assombrit légèrement la bordure au survol si non sélectionné */
    }

    /* Améliorations pour l'accessibilité lors du focus */
    .form-check-input:focus-visible {
        outline: 2px solid #5b9dd9;
        /* Ajoute un contour bleu lors du focus par le clavier */
        outline-offset: 2px;
    }



    .reservation1 {
        position: relative;
        text-align: center;
    }

    .reservation1 img {
        width: 300px;
        height: 300px;
        /* ajustez la hauteur selon vos besoins */
        display: block;
        /* pour résoudre l'espace réservé sous l'image */
        margin: 0 auto;
        /* centrer l'image horizontalement */
    }

    .buttonerser {
        position: absolute;
        bottom: 10px;
        /* ajustez la marge inférieure selon vos besoins */
        left: 50%;
        transform: translateX(-50%);
        background-color: #4CAF50;
        color: white;
        padding: 15px 30px;
        font-size: 16px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        z-index: 1;
        /* pour être en avant-plan */
        width: 150px;
        height: 50px;
        transition: background-color 0.3s;
        /* effet hover */
    }

    .buttonerser:hover {
        background-color: #45a049;
        /* couleur différente au survol */
    }

    .box {
        border: 2px solid #ccc;
        padding: 20px;
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0.2, 0, 0.3, 0.3);
        width: 1000px;
        margin-left: 55px;
        gap: 10px;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
    }




    /* 
    .box {
        display: flex;
        flex-direction: row;
        gap: 10px;

    } */



    .rectangle {
        text-align: center;
    }
    </style>


    <script>
    // Fonction pour ouvrir la modal
    let modalId = document.getElementById("myModal");
    let modalId1 = document.getElementById("myModal1");
    let modalId2 = document.getElementById("myModal2");
    let modalId3 = document.getElementById("myModal3");
    let modalId5 = document.getElementById("myModal5");
    let modalId6 = document.getElementById("myModal6");


    function openModal(arg) {
        document.getElementById(arg).style.display = "block";
    }

    // Fonction pour fermer la modal
    function closeModal(arg) {
        document.getElementById(arg).style.display = "none";
    }

    let profilPic = document.getElementById("profil-pic");
    let profilInput = document.getElementById("input-file");


    // Vérifier s'il y a une image dans le localStorage
    const savedImage = localStorage.getItem("savedImage");
    if (savedImage) {
        profilPic.src = savedImage; // Si une image est enregistrée, l'afficher
    }

    profilInput.onchange = function() {
        profilPic.src = URL.createObjectURL(profilInput.files[0]);

        localStorage.setItem("savedImage", profilPic.src);
    }

    //fontion d 'affichage de aller-retour
    $(document).ready(function() {
        $("input[name='inlineRadioOptions']").change(function() {
            if ($(this).val() === 'option2') {
                $("div.form-groupe").show();
            } else {
                $("div.form-groupe").hide();
            }
        });
    });


    //gestion d'ouverture de la popup de gestion de reservation
    document.getElementById('openModalButton').addEventListener('click', function() {
        $.ajax({
            url: 'Reservation/formulaire.php', // Assurez-vous que cela renvoie seulement le code de la modal
            success: function(response) {
                document.body.insertAdjacentHTML('beforeend', response);
                var modal = document.getElementById('exampleModal');
                var closeButton = document.querySelector('.close-button');

                modal.style.display = "block";

                closeButton.onclick = function() {
                    modal.style.display = "none";
                    modal.parentNode.removeChild(modal);
                }

                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                        modal.parentNode.removeChild(modal);
                    }
                }
            }
        });
    });
    </script>



    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</body>

</html>