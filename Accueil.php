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
    <!-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">

    <title>Document</title>
</head>

<body>
    <!-- PARIE HEADER -->
    <?php include 'includes/header.php'; ?>

    <?php include_once 'Cookies/cookies.php'; ?>

    <section class="relative bg-cover bg-center h-[500px] md:h-[550px] text-white flex flex-col justify-center items-center"
        style="background-image: url('https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg');">

        <!-- Overlay -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>

        <!-- Texte principal -->
        <div class="relative z-10 text-center px-4">
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold leading-tight mb-4">
                Profite jusqu'à -20% sur les billets de reservations !
            </h1>
            <p class="text-lg font-bold text-center pt-2">
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
                            <input type="radio" id="inlineRadio1" name="inlineRadioOptions" value="option1" checked
                                class="text-green-600 focus:ring-green-500 w-5 h-5">
                            <span>Aller</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" id="inlineRadio2" name="inlineRadioOptions" value="option2"
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

    <div class="reste">
        <div class="reserv">


        </div>

        <div class="button-container max-w-7xl align-items-center ">
            <div class="titre">
                <h1 class="text-green-700  text-center font-bold text-2xl mb-4">
                    Gérer vos trajets et vos reservations sans soucis grâce à vos identfiants de reservation sur votre billet de voyage.
                </h1>
            </div>

            <button class="button" id="openModalButton">Gérer ma réservation</button>
            <button class="button">Localiser mon trajet</button>
            <button class="button">Besoin d'aide</button>

        </div>

        <div id="modalContainer"></div>

        <h1 class="text-green-700 text-center font-bold text-2xl mb-4">
            Voyagez sur le plus grand réseau camerounais de bus longue distance !
        </h1><br>
        <div class="map">
            <?php include 'map.php'; ?>
        </div>
        <br>

        <section class="py-12 bg-white">
            <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-8 items-start">


                <div>
                    <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg"
                        alt="Bus EasyTravel"
                        class="rounded-lg shadow-md w-full h-[500px] object-cover">
                </div>


                <div>
                    <h1 class="text-green-700 font-bold text-2xl mb-4">
                        Voyagez sur le plus grand réseau camerounais de bus longue distance !
                    </h1>
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

        <section class="text-white text-center py-12 bus-location mt-4">
            <h1 class="text-green-700  text-center font-bold text-2xl mb-4">
                Gérer vos trajets et vos reservations sans soucis grâce à vos identfiants de reservation sur votre billet de voyage.
            </h1>
            <div class="carousel mx-auto max-w-7xl" data-flickity='{ "cellAlign": "left", "contain": true, "wrapAround": false, "pageDots": true }'>
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
                        <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad" alt="Tokyo" />
                        <div class="px-6 py-4">
                            <div class="font-bold text-xl mb-2">Tokyo</div>
                            <p class="text-gray-700 text-base">A blend of modern technology and traditional culture.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>




        <!-- Icône de chat -->
        <!-- Icône flottante du chatbot -->
        <div class="chat">
            <i class="fa fa-comments fa-5x" id="openChat"
                style="color: green; position: fixed; bottom: 30px; right: 30px; cursor: pointer; font-size: 61px;">
            </i>
        </div>

        <!-- Fenêtre de chat en popup -->
        <div id="chatModal"
            style="display:none; position: fixed; bottom: 100px; right: 30px; width: 400px; height: 600px; background: white; box-shadow: 0 0 10px rgba(0,0,0,0.3); z-index: 9999; border-radius: 10px; overflow: hidden;">

            <!-- En-tête -->
            <div style="background-color: #A93D87; color: white; padding: 10px; font-weight: bold; display: flex; justify-content: space-between; align-items: center;">
                <span>Assistance Mobiliis</span>
                <span id="closeChat" style="cursor: pointer;"><i class="fas fa-times"></i></span>
            </div>

            <!-- Contenu du chat (chargé dynamiquement) -->
            <iframe src="Cookies/chat.php" width="100%" height="100%" frameborder="0"></iframe>
        </div>




        <div id="modalMessage"></div>
    </div>
    <!-- PARIE FOOTER -->
    <?php include 'includes/footer.php'; ?>



    <style>
        .reste {
            background-color: white !important;
        }

        nav {
            width: 100%;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .map {
            background-color: #F8F7F7 !important;
            width: 100%;
            height: 850px;
        }

        .button-container {
            align-items: center !important;
            /* display : flex;
            flex-direction: column;
            justify-content : center; */
        }

        .bus-location {
            background-color: #F8F7F7 !important;
            margin-top: 25px !important;
            padding-top: 25px !important;
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
                `linear-gradient(rgba(39, 39, 39, 0.6), rgba(0, 0, 0, 0.6)), url(${images[currentIndex]})`;
        }
        setInterval(changeBackground, 4000);


        // popup de reservations
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
                url: './Cookies/chat.php',
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


        // popup pour l'oucerture du chat
        document.getElementById('openChat').addEventListener('click', function() {
            document.getElementById('chatModal').style.display = 'block';
        });

        document.getElementById('closeChat').addEventListener('click', function() {
            document.getElementById('chatModal').style.display = 'none';
        });






        document.addEventListener("DOMContentLoaded", function() {
            const radioAller = document.getElementById("inlineRadio1");
            const radioAllerRetour = document.getElementById("inlineRadio2");
            const dateRetour = document.getElementById("input4");

            function toggleDateRetour() {
                console.log("Changement détecté");
                if (radioAllerRetour.checked) {
                    dateRetour.disabled = false;
                    dateRetour.required = true;
                } else {
                    dateRetour.disabled = true;
                    dateRetour.required = false;
                    dateRetour.value = "";
                }
            }

            // Déclencheur au changement de sélection
            radioAller.addEventListener("change", toggleDateRetour);
            radioAllerRetour.addEventListener("change", toggleDateRetour);

            // État initial
            toggleDateRetour();
        });
    </script>

    <script src="Accueil.js"></script>
</body>

</html>