<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

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
    </style>


    <script>
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
    </script>
</body>

</html>