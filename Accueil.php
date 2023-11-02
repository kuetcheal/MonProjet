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
    <link rel="stylesheet" href="style.css">

    <title>Document</title>
</head>

<body>

    <!-- PARTIE HEADER -->
    <header>
        <nav>
            <div class="header-picture">
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

                <div class="langue">
                    <select id="select" name="select" aria-placeholder="2 places">
                        <option value="option1">Français</option>
                        <option value="option2">Anglais</option>

                    </select>
                </div>
                <div class="outils"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"
                        onclick="openModal('myModal')"></i></div>

            </div>
        </nav>
    </header>

    <!-- AFFICHAGE DE LA POPUP DU SETTINGS  -->
    <div id="myModal" class="modal1" style="display: none;">
        <div class="modal-content1">
            <h2 style=" text-align: center;">Réglages</h2>
            <span class="close1" onclick="closeModal('myModal')" style="position: fixed; right: 385px; top: 12px;"><i
                    class=" fa
                fa-arrow-left" aria-hidden="true">
                </i></span><br>
            <h2>Mon compte utilisateur</h2> <br><br>
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
            <hr style=" color: green;"><br><br>
            <h2 style=" color: green; text-align: center; "> Mes connexions</h2>
            <p style=" font-size: 16px; ">Veuilez accéder au contenu du settings de l'application </p><br>
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

            <!-- <form method="post" action="update.php">
                <h4 style="font-size: 20px; color: green;">Nom d'utilisateur</h4>
                <label for="username" style="font-size: 16px;">Veuillez saisir le nom d'utilisateur:</label>
                <input type="text" name="username" id="username" placeholder="ALEX" style="width: 370px;"
                    required><br><br>

                <h4 style="font-size: 20px; color: green;">Mot de passe actuel</h4>
                <label for="password" style="font-size: 16px;">Entrer votre mot de passe actuel:</label>
                <input type="password" name="password" id="password" placeholder="impact1999" style="width: 370px;"
                    required><br><br>

                <h4 style="font-size: 20px; color: green;">Nouveau mot de passe</h4>
                <label for="newPassword" style="font-size: 16px;">Saisir votre nouveau mot de passe:</label>
                <input type="password" name="newPassword" id="newPassword" placeholder="stephane2000"
                    style="width: 370px;" required><br><br><br>

                <h4 style="font-size: 20px; color: green;">Confirmer votre nouveau mot de passe</h4>
                <label for="confirmPassword" style="font-size: 16px;">Confirmer votre nouveau mot de passe:</label>
                <input type="password" name="confirmPassword" id="confirmPassword" placeholder="stephane2000"
                    style="width: 370px;" required><br><br><br>

                <hr style="color: green;"><br><br>

                <button name="delete_account" class="btn-modifier" style="margin-left: 105px;">Modifier</button>
            </form> -->

        </div>
    </div>


    <!-- CODE PHP POUR LA SUPPRESSION DU COMPTE -->
    <?php
// Vérifier si le formulaire de suppression a été soumis
if (isset($_POST['delete_account'])) {
  
    $host = "localhost"; // nom d'hôte
    $user = "root"; // nom d'utilisateur
    $password = ""; // mot de passe
    $database = "bd_stock"; // nom de la base de données

    // Connexion à la base de données MySQLi
    $conn = mysqli_connect($host, $user, $password, $database);
  $id= $_SESSION['Id_compte'];
    $query = "DELETE FROM user WHERE id_name =$id";
    mysqli_query( $conn, $query);
 
    header('Location: connexion.php');
    exit;
}
?>





    <!-- PARTIE MAIN -->
    <div class="content">
        <div class="container">
            <p class="para"> le plaisir de bien voyager à un prix abordable à partir de 5.5euro seulement.
            </p>
        </div><br>
        <h1 class="titre"> Rechercher un trajet de voyage </h1><br>
    </div>
    <div class="box">
        <form action="listevoyage.php" method="post">
            <div class="radio-buttons">
                <input type="radio" id="radio1" name="radio" value="option1">
                <label for="radio1">Aller</label>

                <input type="radio" id="radio2" name="radio" value="option2">
                <label for="radio2">Aller-retour</label>
            </div>

            <div class="form-group">
                <label for="select">DE:</label>
                <select id="input1" name="input1" aria-placeholder="20 places" style="width: 250px; height: 50px;">
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

            <!-- <div class="form-group">
        <label for="input2">A:</label>
        <input type="text" id="input2" name="input2" placeholder="Yaoundé">
      </div> -->

            <div class="form-group">
                <label for="select">A:</label>
                <select id="input2" name="input2" aria-placeholder="20 places" style="width: 250px; height: 50px;">
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
                <label for="input3">Date départ:</label>
                <input type="date" class="date-input" name="input3">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value=" Valider"
                    style="background-color: green; color: white;  width: 200px; height: 50px; ">


            </div>
    </div>
    </form>


    </div>
    <div class="button-container">
        <button class="button">Gérer ma réservation</button>
        <button class="button">Localiser mon trajet</button>
        <button class="button">Besoin d'aide</button>
    </div>
    <br>
    <hr>
    <br><br>
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
    <br><br>
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
        <h2>C’est économique et bon pour l'environnement</h2><br><br>
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

    <!-- PARIE FOOTER -->

    <footer>
        <h2 class="slogan">Explorer d'autres façons de voyager au cameroun</h2>
        <br>
        <hr>
        <br>
        <div class="footer-content">

            <div class="social-media">
                <h3>Generales :</h3>
                <ul>
                    <li><a href="#"></i>A propos</a></li>
                    <li><a href="#">Conditions générales de reservations</a></li>
                    <li><a href="#">Conditions générales de transport</a></li>
                    <li><a href="#">Devenez conducteur à général voyage</a></li>
                </ul>
            </div>
            <div class="contact">
                <h3>Nous contacter :</h3>
                <ul>
                    <li>Email: agencegenerale@gmail.cm</li>
                    <li>Tel: (+237) 675051899</li>
                    <li>Adresse postal: 8 rue double-balle-Bepanda</li>
                    <li>Code postal: 4500</li>
                </ul>
            </div>
            <div class="privacy">
                <h3>Nos trajets :</h3>
                <ul>
                    <li><a href="#">Nos villes</a></li>
                    <li><a href="#">Connexions transport</a></li>
                    <li><a href="#">Nos arrêts bus</a></li>
                    <li><a href="#">Nos bus</a></li>
                </ul>
            </div>
        </div>
        <br>

        <div class="footer-picture">
            <div class="social-download">
                <p>Télécharger l'application sur :</p>
                <img src="pictures/Appstore.png" alt="logo site" id="apps" style=' font-size: 12px' />
                <img src="pictures/logo-playstore-ConvertImage.png" alt="logo site" class="playstore"
                    style=' font-size: 12px' />
            </div>

            <div class="social-icons">

                <ul>
                    <li class="liste">Rejoignez-nous sur:</li>
                    <li class="liste"><a
                            href="https://m.facebook.com/groups/835886833986349?group_view_referrer=search"><i
                                class="fa fa-facebook-official" aria-hidden="true"></i></a></li>
                    <li class="liste"><a href="#"><a href="#"><i class="fa fa-twitter fa-" aria-hidden="true"></i></a>
                    </li>
                    <li class="liste"><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </div>

        <div class="privacye">

            <a href="dossier html/privacy.html">Conditions d'utilisation</a>

        </div>
    </footer>


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

    .outils i {
        color: white;
        font-size: 37px;
        margin-left: 30px;
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
        height: 31px;
        width: 680px;
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




    /* PARTIE FOOTER */
    footer {
        background-color: rgb(247, 247, 247);
        padding: 40px;
        font-family: Arial, sans-serif;
        /* width: 1268px; */
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

    .box {
        border: 2px solid #ccc;
        padding: 20px;
        background-color: #ffffff;
        box-shadow: 0 1px 3px rgba(0.2, 0, 0.3, 0.3);
        width: 1050px;
        margin-left: 55px;
    }

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

    function openModal(arg) {
        document.getElementById(arg).style.display = "block";
    }

    // Fonction pour fermer la modal
    function closeModal(arg) {
        document.getElementById(arg).style.display = "none";
    }
    </script>


</body>

</html>