<?php
session_start(); // Démarrer la session (si ce n'est pas déjà fait)
if (isset($_POST['deconnect_account'])) {
    // Supprimer toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css" />
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="style.css">

    <title>Document</title>
</head>

<body>
    <!-- PARIE HEADER -->

    <?php include_once 'Cookies/cookies.php'; ?>


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
                    <li class="items"><a href="ajoutclient.php">Services</a></li>
                    <li class="items"><a href="Contact/page-contact.php">Nos contacts</a></li>
                    <li class="items"><a href="inscription.php">inscription</a></li>
                    <li class="items"><a href="connexion.php">connexion</a></li>
                </ul>
            </div>
            <div class="public">
                <div class="notif">
                    <i class="fa fa-bell-o" aria-hidden="true"></i>
                </div>
                <div class="langue">
                    <i class="fa fa-globe" aria-hidden="true"></i>
                </div>
                <div class="outils">
                    <img src="pictures/OIP.jpg" id="profil" onclick="openModal('myModal')" alt="image user" />
                </div>

            </div>
        </nav>
    </header>

    <!-- Popup de settings -->
    <div id="myModal" class="modal1" style="display: none;">
        <div class="modal-content1">
            <span class="close1" onclick="closeModal('myModal')" style="position: fixed; right: 373px; top: 16px;">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </span>
            <h2 style="margin-left: 60px;">Mon compte utilisateur</h2><br>
            <hr style="color: green;"><br><br>
            <div class="profiler">
                <div>
                    <img src="pictures/OIP.jpg" alt="image user" id="profil-pic" style="position: fixed; top: 75px" />
                    <input type="file" accept="image/png, image/jpeg, image/jpg" id="input-file" style="display: none">
                    <label for="input-file"
                        style="position: fixed; top: 164px; height: 20px; width:110px; background-color: green; color: white; font-size: 12px; padding: 8px">Download
                        image</label>
                </div>
                <div class="profil-infos" style="margin-left: 145px;">
                    <p>Alex KUETCHE</p>
                    <p style="color: green;">alexkuetche@gmail.com</p>
                </div>
            </div><br><br>
            <hr style="color: green;"><br><br>
            <h2 style="color: green; text-align: center;">Mes connexions</h2><br>
            <p style="font-size: 16px;">Veuillez accéder au contenu settings de l'application</p><br>
            <div style="display: flex; align-items: center; flex-direction: column; gap: 23px;">
                <button type="submit" class="btn-supprimer" style="width: 350px;"
                    onclick="openModal('myModal1')">Supprimer</button>
                <button type="submit" class="btn-deconnecter" style="width: 350px;" onclick="openModal('myModal2')">Se
                    déconnecter</button>
                <button type="submit" class="btn-modifier" style="width: 350px;"
                    onclick="openModal('myModal3')">Modifier</button>
            </div><br><br>
            <div>
                <img src="logo général.jpg" alt="logo site" style="height: 60px; width: 110px; margin-left: 140px;" />
            </div>
        </div>
    </div>

    <!-- Popup de suppression -->
    <div id="myModal1" class="modal1">
        <div class="modal-content1">
            <h2 style="text-align: center;">Réglages</h2>
            <span class="close1" onclick="closeModal('myModal1')" style="position: fixed; right: 385px; top: 12px;">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </span><br>
            <h2>Suppression du compte</h2><br><br>
            <hr style="color: green;"><br><br>
            <div class="profil" style="display: flex;">
                <img src="pictures/OIP.jpg" alt="image user" class="profil-pic-global"
                    style="position: fixed; top: 148px; height: 60px; width: 60px; border-radius: 50%; object-fit: cover;" />
                <div class="profil-infos" style="margin-left: 80px;">
                    <p>Alex KUETCHE</p>
                    <p style="color: green;">alexkuetche@gmail.com</p>
                </div>
            </div><br><br>
            <hr style="color: green;"><br><br><br>
            <p style="text-align: center;">Attention !!! cette action effacera définitivement votre compte de
                l'application. Êtes-vous sûr de vouloir supprimer votre compte ?</p><br><br><br>
            <form method="post" action="">
                <button name="delete_account" class="btn-supprimer"
                    style="background-color: green; color: white; border: none; border-radius: 5px; border-color: 2px solid green; font-size: 20px; height: 30px; width: 120px; margin-left: 135px;">Supprimer</button><br><br><br><br><br><br>
                <div>
                    <img src="logo général.jpg" alt="logo site"
                        style="height: 60px; width: 110px; margin-left: 140px; margin-bottom: 40px" />
                </div>
            </form>
        </div>
    </div>

    <!-- Popup de déconnexion -->
    <div id="myModal2" class="modal1">
        <div class="modal-content1">
            <h2 style="text-align: center;">Réglages</h2>
            <span class="close1" onclick="closeModal('myModal2')" style="position: fixed; right: 385px; top: 12px;">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
            </span><br>
            <h2>Déconnexion du compte</h2><br><br>
            <hr style="color: green;"><br><br>
            <div class="profil" style="display: flex;">
                <img src="pictures/OIP.jpg" alt="image user" class="profil-pic-global"
                    style="position: fixed; top: 148px; height: 60px; width: 60px; border-radius: 50%; object-fit: cover;" />
                <div class="profil-infos" style="margin-left: 80px;">
                    <p>Alex KUETCHE</p>
                    <p style="color: green;">alexkuetche@gmail.com</p>
                </div>
            </div><br><br>
            <hr style="color: green;"><br><br><br>
            <p style="text-align: center;">Attention !!! cette action déconnectera votre compte de l'application.
                Êtes-vous sûr de vouloir vous déconnecter votre compte ?</p><br><br><br>
            <form method="post" action="">
                <button name="delete_account" class="btn-deconnecter" style="margin-left: 105px;">Se
                    déconnecter</button><br><br><br><br>
                <div>
                    <img src="logo général.jpg" alt="logo site"
                        style="height: 60px; width: 110px; margin-left: 140px;" />
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
    <!-- SECTION HERO -->
    <section class="relative bg-cover bg-center h-[500px] md:h-[550px] text-white flex flex-col justify-center items-center"
        style="background-image: url('https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg');">

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Texte principal -->
        <div class="relative z-10 text-center px-4">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold leading-tight mb-4">
                Profite de tes voyages! jusqu'à -20% sur les billets<br>
                ainsi que des billets gratuits
            </h1>
            <p class="text-lg font-bold">
                REMISES EXCLUSIVES POUR LES MEMBRES !
                <a href="connexion.php" class="text-green-400 underline hover:text-green-300">
                    CONNECTEZ-VOUS / INSCRIVEZ-VOUS ICI
                </a>
            </p>
        </div>

        <!-- Titre secondaire bien visible -->
        <h3 class="relative z-10 text-2xl md:text-3xl font-bold text-green-400 mt-10 mb-4">
            Rechercher votre trajet
        </h3>

        <!-- FORMULAIRE intégré en bas de l’image -->
        <div class="relative z-10 w-[90%] max-w-5xl mt-4 ">
            <div class="bg-white bg-opacity-95 backdrop-blur-md shadow-xl rounded px-6 py-6 text-green-800 text-base font-bold">
                <form action="listevoyageretour.php" method="post" class="flex flex-col space-y-6">

                    <!-- Radios -->
                    <div class="flex space-x-6 items-center text-lg">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="inlineRadioOptions" value="option1" checked
                                class="text-green-600 focus:ring-green-500 w-5 h-5">
                            <span>Aller</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="inlineRadioOptions" value="option2"
                                class="text-green-600 focus:ring-green-500 w-5 h-5">
                            <span>Aller-Retour</span>
                        </label>
                    </div>

                    <!-- Inputs -->
                    <div class="flex flex-col md:flex-row md:items-end md:space-x-4 space-y-4 md:space-y-0 text-lg font-bold">

                        <!-- DE -->
                        <div>
                            <label for="input1" class="block mb-1">
                                <i class="bi bi-geo-alt text-green-600 mr-1"></i> DE :
                            </label>
                            <select id="input1" name="input1" class="w-44 h-11 border border-gray-300 rounded px-2 text-green-700 font-bold">
                                <?php
                                $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
                                $query = 'select * from destination order by Nom_ville ASC';
                                $response = $bdd->query($query);
                                while ($donnee = $response->fetch()) {
                                    $destination = $donnee['Nom_ville'];
                                    echo '<option value="' . htmlspecialchars($destination) . '">' . htmlspecialchars($destination) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- A -->
                        <div>
                            <label for="input2" class="block mb-1">
                                <i class="bi bi-geo-alt text-green-600 mr-1"></i> A :
                            </label>
                            <select id="input2" name="input2" class="w-44 h-11 border border-gray-300 rounded px-2 text-green-700 font-bold">
                                <?php
                                $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
                                $query = 'select * from destination order by Nom_ville ASC';
                                $response = $bdd->query($query);
                                while ($donnee = $response->fetch()) {
                                    $destination = $donnee['Nom_ville'];
                                    echo '<option value="' . htmlspecialchars($destination) . '">' . htmlspecialchars($destination) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <!-- Date départ -->
                        <div>
                            <label for="input3" class="block mb-1">Date départ :</label>
                            <input type="date" name="input3"
                                class="w-44 h-11 border border-gray-300 rounded px-2 text-green-700 font-bold">
                        </div>

                        <!-- Date retour -->
                        <div>
                            <label for="input4" class="block mb-1">Date retour :</label>
                            <input type="date" name="input4" id="input4" disabled
                                class="w-44 h-11 border border-gray-300 rounded px-2 text-green-700 font-bold">
                        </div>

                        <!-- Bouton -->
                        <div>
                            <label class="invisible block h-5">Valider</label>
                            <input type="submit" value="Valider"
                                class="w-44 h-11 bg-green-600 hover:bg-green-700 text-white font-bold rounded cursor-pointer">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>




    <div class="button-container">
        <button class="button" id="openModalButton">Gérer ma réservation</button>
        <button class="button">Localiser mon trajet</button>
        <button class="button">Besoin d'aide</button>
    </div>

    <div id="modalContainer"></div>

    <h1 style="color: green; text-align: center;">Profitez de toutes nos destinations pour vos besoins de deplacement
    </h1><br>
    <!-- PARIE MAP -->
    <?php include 'map.php'; ?>

    <br>

    <section class="py-12 bg-white">
        <div class="max-w-6xl mx-auto px-4 grid md:grid-cols-2 gap-8 items-start">


            <div>
                <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg"
                    alt="Bus EasyTravel"
                    class="rounded-lg shadow-md w-full h-[500px] object-cover">
            </div>


            <div>
                <h2 class="text-green-700 font-bold text-2xl mb-4">
                    Voyagez sur le plus grand réseau camerounais de bus longue distance !
                </h2>
                <p class="text-gray-700 mb-4">
                    Depuis 2000, <strong class="text-green-800">Général Voyage</strong> agrandit continuellement son réseau
                    camerounais et dessert chaque jour plus de 100 destinations dont plus de 30 villes au Cameroun.
                    Notre objectif est de rendre le Cameroun vert !
                    Le réseau de Général Voyage s’étend du Sud à l'Est jusqu’au Grand Nord.
                    Découvrez notre <a href="#" class="text-green-600 font-medium hover:underline">carte interactive</a>
                    ou réservez dès maintenant pour <strong>Yaoundé, Kribi, Bamenda, Edea, Banga</strong> et bien d’autres.
                </p>

                <h3 class="text-green-600 font-semibold text-xl mb-2">C’est simple et confortable</h3>
                <p class="text-gray-700 mb-4">
                    Voyager n’a jamais été aussi simple avec Général Voyage.
                    Notre personnel serviable et notre site web détaillé vous accompagnent de la réservation jusqu’à l’arrivée.
                    Vous pouvez <a href="#" class="text-green-600 font-medium hover:underline">acheter votre billet en ligne</a>
                    ou même au dernier moment auprès du conducteur.
                </p>

                <p class="text-gray-700">
                    Nos bus garantissent <strong class="font-semibold text-green-700">une place assise avec espace pour vos jambes</strong>,
                    <strong class="font-semibold text-green-700">Wi-fi gratuit</strong>,
                    <strong class="font-semibold text-green-700">prises électriques</strong> et des snacks à petit prix !
                </p>
            </div>

        </div>
    </section>

    <section class="text-white text-center py-12">
        <div class="carousel mx-auto" data-flickity='{ "cellAlign": "left", "contain": true, "wrapAround": false, "pageDots": true }'>
            <div class="carousel-cell">
                <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b" alt="City" />
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">New York City</div>
                        <p class="text-gray-700 text-base">The Big Apple, featuring stunning architecture and vibrant culture.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-cell">
                <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad" alt="Tokyo" />
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">Tokyo</div>
                        <p class="text-gray-700 text-base">A blend of modern technology and traditional culture.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-cell">
                <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e" alt="Paris" />
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">Paris</div>
                        <p class="text-gray-700 text-base">The City of Light, known for its romance and iconic landmarks.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-cell">
                <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b" alt="City" />
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">New York City</div>
                        <p class="text-gray-700 text-base">The Big Apple, featuring stunning architecture and vibrant culture.</p>
                    </div>
                </div>
            </div>
            <div class="carousel-cell">
                <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
                    <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad" alt="Tokyo" />
                    <div class="px-6 py-4">
                        <div class="font-bold text-xl mb-2">Tokyo</div>
                        <p class="text-gray-700 text-base">A blend of modern technology and traditional culture.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <div class=" chat">
        <i class="fa fa-comments fa-5x" id="openModalMessage"
            style=" color: green; position: fixed; top: 566px; right: 18px; cursor: pointer; font-size: 61px;"></i>
    </div>
    <div id="modalMessage"></div>







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
            height: 140px;
        }

        nav {
            width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-bar {
            margin-left: 25px;
            margin-top: 65px;
            background-color: #3e8e41;
            height: 40px;
            width: 900px;
            padding: 7px;
            border-radius: 20px;
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
            margin-top: 10px;
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


        .carousel {
            margin-left: auto;
            margin-right: auto;
            padding-left: 0;
        }

        .carousel-cell {
            width: 300px;
            margin-right: 20px;
        }


        .flickity-page-dots {
            bottom: -40px;
            text-align: center;
        }

        .flickity-page-dots .dot {
            background: #333;
            width: 10px;
            height: 10px;
            margin: 0 5px;
            border-radius: 50%;
            opacity: 0.5;
            transition: opacity 0.3s ease;
        }

        .flickity-page-dots .dot.is-selected {
            opacity: 1;
        }




        /* PARTIE FOOTER */



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


        #outillage {
            /* display: flex; */
            position: fixed;
            top: 50px;
        }

        .container {
            padding: 20px;
            background-size: cover;
            height: 500px;
            color: aliceblue;
            flex-grow: 1;
            width: 100%;
            background-repeat: no-repeat;
            position: relative;
            transition: background-image 0.1s ease-in-out;
        }

        .para {
            position: absolute;
            bottom: 300px;
            left: 200px;
            font-size: 30px;
        }

        .custom-icon {
            font-weight: bold;
            color: #000000;
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
            width: 910px;
            margin-left: 205px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
        }

        .promo-text h1 {
            font-size: 48px;
            margin: 0;
            font-weight: bold;
        }

        .promo-text p {
            font-size: 18px;
            margin-top: 10px;
            font-weight: bold;
        }
    </style>


    <script>
        // Fonction pour ouvrir la modal
        let modalId = document.getElementById("myModal");
        let modalId1 = document.getElementById("myModal1");
        let modalId2 = document.getElementById("myModal2");
        let modalId3 = document.getElementById("myModal3");
        // let modalId5 = document.getElementById("myModal5");
        // let modalId6 = document.getElementById("myModal6");


        function openModal(arg) {
            document.getElementById(arg).style.display = "block";
        }

        // Fonction pour fermer la modal
        function closeModal(arg) {
            document.getElementById(arg).style.display = "none";
        }




        // Vérifier s'il y a une image dans le localStorage
        let profilPic = document.getElementById("profil-pic");
        let profilInput = document.getElementById("input-file");
        const profilPics = document.querySelectorAll(".profil-pic-global");
        const profilOutils = document.getElementById("profil");


        // Vérifier s'il y a une image dans le localStorage
        const savedImage = localStorage.getItem("savedImage");
        if (savedImage) {
            profilPic.src = savedImage; // Affiche l'image dans la popup de settings
            profilPics.forEach(img => img.src = savedImage); // Affiche l'image dans les autres popups
            profilOutils.src = savedImage; // Affiche l'image dans la section "outils"
        }
        // Gérer le changement d'image
        profilInput.onchange = function() {
            const newImageUrl = URL.createObjectURL(profilInput.files[0]);
            profilPic.src = newImageUrl;


            // Mettre à jour toutes les images dans les popups
            profilPics.forEach(img => img.src = newImageUrl);

            // Mettre à jour l'image dans la section "outils"
            profilOutils.src = newImageUrl;

            // Stocker l'image dans le localStorage
            localStorage.setItem("savedImage", newImageUrl);
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

        //gestion des animations de la page d'acceuil
        const images = [
            "https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg",
            "https://www.sustainable-bus.com/wp-content/uploads/2021/06/CrosswayCNG_Flixbus_inside-1688x1080.jpg",
            "https://content.presspage.com/uploads/2327/1920_flixbus-treyratcliff-paris-02.jpg?10000"
        ];

        let currentIndex = 0;
        const container = document.querySelector('.container');

        function changeBackground() {
            currentIndex = (currentIndex + 1) % images.length;
            container.style.backgroundImage =
                linear-gradient(rgba(39, 39, 39, 0.6), rgba(0, 0, 0, 0.6)), url(${images[currentIndex]});
        }
        setInterval(changeBackground, 4000);

        // // gestion du choix du trajet retour ou non
        // document.getElementById('inlineRadio1').addEventListener('change', function() {
        //     document.getElementById('input4').disabled = true;
        // });
        // document.getElementById('inlineRadio2').addEventListener('change', function() {
        //     document.getElementById('input4').disabled = false;
        // });



        // Code pour ouvrir le modal de reservation  et vérifier sa reservation
        document.getElementById('openModalButton').addEventListener('click', function() {
            $.ajax({
                url: './formulaire.php',
                success: function(response) {
                    document.getElementById('modalContainer').innerHTML = response;

                    var modal = document.querySelector('#modalContainer .modalisation');
                    var closeButton = document.querySelector('#modalContainer .close-button');

                    if (modal) {
                        modal.style.display = "flex"; // Assurez-vous qu'il s'affiche correctement

                        // Gestion de la fermeture avec le bouton "×"
                        if (closeButton) {
                            closeButton.addEventListener('click', function() {
                                modal.style.display = "none";
                                document.getElementById('modalContainer').innerHTML = ''; // Nettoyage du contenu
                            });
                        }

                        // Gestion de la fermeture en cliquant à l'extérieur de la modale
                        window.addEventListener('click', function(event) {
                            if (event.target === modal) {
                                modal.style.display = "none";
                                document.getElementById('modalContainer').innerHTML = ''; // Nettoyage du contenu
                            }
                        });
                    }
                }
            });
        });




        // Code pour gérer la soumission du formulaire de réservation
        $(document).on('submit', '#reservationForm', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: './process_reservation.php', // Mettez à jour l'URL ici
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.location.href = response.redirect;
                    } else if (response.status === 'error') {
                        console.error(response.message ||
                            'Erreur lors de la vérification de la réservation.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erreur: ", xhr.responseText);
                    alert('Une erreur est survenue.');
                }
            });
        });


        // Code pour ouvrir le modal d'envoi de message
        document.getElementById('openModalMessage').addEventListener('click', function() {
            $.ajax({
                url: './Contact/contact.php',
                success: function(response) {
                    document.getElementById('modalMessage').innerHTML = response;
                    var modal = document.querySelector('#modalMessage .modalitisation');
                    var closeButton = document.querySelector('.close1');

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
                },
                error: function(xhr, status, error) {
                    console.error("Error loading modal: ", status, error);
                }
            });
        });


        // Code pour gérer la soumission du formulaire de message
        $(document).on('submit', '#contactForm', function(e) {
            e.preventDefault();

            $.ajax({
                type: 'POST',
                url: './Contact/contact-ajax.php',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    if (response.status === 'success') {
                        window.location.href = response.redirect;
                    } else if (response.status === 'error') {
                        console.error(response.message ||
                            'Erreur lors de la soumission du formulaire.');
                        alert(response.message || 'Erreur lors de la soumission du formulaire.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Erreur: ", xhr.responseText);
                    alert('Une erreur est survenue.');
                }
            });
        });






        document.addEventListener("DOMContentLoaded", function() {
            const radioAller = document.getElementById("inlineRadio1");
            const radioAllerRetour = document.getElementById("inlineRadio2");
            const dateRetour = document.getElementById("input4");

            function toggleDateRetour() {
                if (radioAllerRetour.checked) {
                    dateRetour.removeAttribute("disabled"); // Active le champ
                    dateRetour.setAttribute("required", "true"); // Rend obligatoire
                } else {
                    dateRetour.setAttribute("disabled", "true"); // Désactive le champ
                    dateRetour.removeAttribute("required"); // Enlève l'obligation de remplissage
                    dateRetour.value = ""; // Réinitialise la valeur
                }
            }

            radioAller.addEventListener("change", toggleDateRetour);
            radioAllerRetour.addEventListener("change", toggleDateRetour);

            // Appeler la fonction au chargement pour s'assurer que l'état est correct
            toggleDateRetour();
        });







        // selectionne de la destination
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Sélectionnez une destination',
                allowClear: true
            });
        });
    </script>

    <script src="Accueil.js"></script>
</body>

</html>