<?php
// session_start();
if (isset($_POST['deconnect_account'])) {
    session_unset();
    session_destroy();
    header('Location: connexion.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <title>Mon Compte</title>
</head>

<body>

    <!-- HEADER -->
    <header class="w-full flex justify-between items-center px-6 md:px-10 navigation">
        <div>
        <img src="pictures/logo-general.jpg" alt="rien" class="image">
        </div>
        <nav class="hidden md:flex space-x-6 text-white navbar">
            <a href="#">Accueil</a>
            <a href="reservations.php">Réservations</a>
            <a href="services.php">Services</a>
            <a href="contact.php">Nos contacts</a>
            <a href="../inscription.php">Inscription</a>
            <a href="connexion.php">Connexion</a>
        </nav>
        <!-- Icônes Notifications, Langue et Profil -->
        <div class="flex space-x-4 items-center">
            <h3 class=" text-white text-xl cursor-pointer">FR</h3>
            <img id="profil" src="pictures/OIP.jpg" class="h-10 w-10 rounded-full cursor-pointer"
                onclick="openModal('myModal')">

            <!-- Menu burger (visible à partir de 950px) -->
            <button id="burger-menu" class="lg:hidden text-white text-2xl focus:outline-none ml-4">
                <i class="fa fa-bars"></i>
            </button>
        </div>
    </header>

    <!-- MENU BURGER MOBILE -->
    <nav id="mobile-menu" class="hidden bg-green-700 text-white p-4 space-y-2 flex flex-col">
        <a href="#" class="hover:text-gray-300">Accueil</a>
        <a href="#" class="hover:text-gray-300">Réservations</a>
        <a href="#" class="hover:text-gray-300">Services</a>
        <a href="#" class="hover:text-gray-300">Nos contacts</a>
        <a href="#" class="hover:text-gray-300">Inscription</a>
        <a href="#" class="hover:text-gray-300">Connexion</a>
    </nav>

    <!-- POPUP UTILISATEUR (Toute la hauteur) -->
    <div id="myModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-end hidden">
        <div class="bg-white h-full w-96 shadow-lg p-6 overflow-y-auto relative">
            <!-- Bouton de fermeture -->
            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700 text-lg"
                onclick="closeModal('myModal')">
                <i class="fa fa-times-circle"></i>
            </button>

            <!-- Titre -->
            <h2 class="text-xl font-bold text-center text-green-600">Mon compte utilisateur</h2>
            <hr class="my-4 border-green-500">

            <!-- Image de profil -->
            <div class="flex flex-col items-center space-y-3">
                <img id="profil-pic" src="pictures/OIP.jpg" class="h-24 w-24 rounded-full border-2 border-green-500">
                <input type="file" id="input-file" accept="image/png, image/jpeg, image/jpg" class="hidden">
                <label for="input-file"
                    class="cursor-pointer bg-green-600 text-white px-4 py-1 rounded-md text-sm hover:bg-green-700">
                    Download image
                </label>
            </div>

            <hr class="my-4 border-green-500">

            <!-- Infos utilisateur -->
            <div class="text-center">
                <p class="text-lg font-semibold">Alex KUETCHE</p>
                <p class="text-green-500">alexkuetche@gmail.com</p>
            </div>

            <hr class="my-4 border-green-500">

            <!-- Boutons d'action -->
            <div class="space-y-4">
                <button class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    onclick="openModal('deleteAccountModal')">Supprimer</button>
                <button class="w-full py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                    onclick="openModal('logoutModal')">Se déconnecter</button>
                <button class="w-full py-2 bg-red-500 text-white rounded hover:bg-red-600"
                    onclick="openModal('editAccountModal')">Modifier</button>
            </div>

            <!-- Logo en bas -->
            <div class="mt-6 flex justify-center">
                <img src="../logo général.jpg" class="h-12">
            </div>
        </div>
    </div>


    <!-- POPUP suppression de compte -->
    <div id="deleteAccountModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-end hidden">
        <div class="bg-white h-full w-96 shadow-lg p-6 overflow-y-auto relative">
            <!-- Bouton de fermeture -->
            <button class="absolute top-4 left-4 text-gray-500 hover:text-gray-700 text-lg" onclick="closeModal('deleteAccountModal')">
                <i class="fa fa-arrow-left"></i>
            </button>
            <!-- Titre -->
            <h2 class="text-xl font-bold text-center text-green-600">Mon compte utilisateur</h2>
            <hr class="my-4 border-green-500">
            <!-- Image de profil -->
            <div class="flex flex-col items-center space-y-3">
                <img id="profil-pic" src="pictures/OIP.jpg" class="h-24 w-24 rounded-full border-2 border-green-500">
                <input type="file" id="input-file" accept="image/png, image/jpeg, image/jpg" class="hidden">
                <label for="input-file"
                    class="cursor-pointer bg-green-600 text-white px-4 py-1 rounded-md text-sm hover:bg-green-700">
                    Download image
                </label>
            </div>
            <hr class="my-4 border-green-500">
            <!-- Infos utilisateur -->
            <div class="text-center">
                <p class="text-lg font-semibold">Alex KUETCHE</p>
                <p class="text-green-500">alexkuetche@gmail.com</p>
            </div>
            <hr class="my-4 border-green-500">
            <!-- Boutons d'action -->
            <div class="space-y-4">
                <p class="text-lg font-semibold">Attention !!! cette action effacera définitivement votre compte de l'application.
                    Êtes-vous sûr de vouloir supprimer votre compte ?</p>
                <button class="w-full py-2 bg-green-600 text-white rounded hover:bg-green-700"
                    onclick="openModal('deleteAccountModal')">Supprimer</button>
            </div>
            <!-- Logo en bas -->
            <div class="mt-6 flex justify-center">
                <img src="logo général.jpg" class="h-12">
            </div>
        </div>
    </div>


    <!-- POPUP suppression de compte -->
    <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-end hidden">
        <div class="bg-white h-full w-96 shadow-lg p-6 overflow-y-auto relative">
            <!-- Bouton de fermeture -->
            <button class="absolute top-4 left-4 text-gray-500 hover:text-gray-700 text-lg" onclick="closeModal('logoutModal')">
                <i class="fa fa-arrow-left"></i>
            </button>
            <!-- Titre -->
            <h2 class="text-xl font-bold text-center text-green-600">Mon compte utilisateur</h2>
            <hr class="my-4 border-green-500">
            <!-- Image de profil -->
            <div class="flex flex-col items-center space-y-3">
                <img id="profil-pic" src="pictures/OIP.jpg" class="h-24 w-24 rounded-full border-2 border-green-500">
                <input type="file" id="input-file" accept="image/png, image/jpeg, image/jpg" class="hidden">
                <label for="input-file"
                    class="cursor-pointer bg-green-600 text-white px-4 py-1 rounded-md text-sm hover:bg-green-700">
                    Download image
                </label>
            </div>
            <hr class="my-4 border-green-500">
            <!-- Infos utilisateur -->
            <div class="text-center">
                <p class="text-lg font-semibold">Alex KUETCHE</p>
                <p class="text-green-500">alexkuetche@gmail.com</p>
            </div>
            <hr class="my-4 border-green-500">
            <!-- Boutons d'action -->
            <div class="space-y-4">
                <p class="text-lg font-semibold">Attention !!! cette action vous déconnectera du site.
                    Êtes-vous sûr de vouloir vous déconnecter votre compte ?</p>
                <button class="w-full py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                    onclick="openModal('logoutModal')">Se déconnecter</button>
            </div>
            <!-- Logo en bas -->
            <div class="mt-6 flex justify-center">
                <img src="logo général.jpg" class="h-12">
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        // Gestion du menu mobile
        document.getElementById("burger-menu").addEventListener("click", function() {
            document.getElementById("mobile-menu").classList.toggle("hidden");
        });

        // S'assurer que le menu principal disparaît en dessous de 950px
        function adjustMenu() {
            if (window.innerWidth < 950) {
                document.querySelector("nav.lg\\:flex").classList.add("hidden");
            } else {
                document.querySelector("nav.lg\\:flex").classList.remove("hidden");
                document.getElementById("mobile-menu").classList.add("hidden"); // Cacher le menu mobile si retour à grand écran
            }
        }

        // Appelle la fonction au chargement et lors du redimensionnement
        window.addEventListener("resize", adjustMenu);
        window.addEventListener("load", adjustMenu);



        // Gestion de l'image de profil
        document.addEventListener("DOMContentLoaded", function() {
            let profilPic = document.getElementById("profil-pic");
            let profilInput = document.getElementById("input-file");
            let profilOutils = document.getElementById("profil"); // Image en haut à droite
            const profilPics = document.querySelectorAll(".profil-pic-global");

            // Vérifier si une image est stockée
            const savedImage = localStorage.getItem("savedImage");
            if (savedImage) {
                profilPic.src = savedImage;
                profilPics.forEach(img => img.src = savedImage);
                profilOutils.src = savedImage;
            }

            // Mettre à jour l'image lorsqu'un fichier est sélectionné
            profilInput.addEventListener("change", function() {
                const file = profilInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        profilPic.src = e.target.result;
                        profilPics.forEach(img => img.src = e.target.result);
                        profilOutils.src = e.target.result;
                        localStorage.setItem("savedImage", e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

</body>

<style>

    .navigation{
        height : 125px;
        background-color: green;
    }
    .navbar{
        max-width: 790px;
        background-color: white ;
        height : 60px;
        border-radius : 30px;
        padding : 15px 15px ; 
    }

    .image{
        width: 120px;
        height: 100px;
    }
</style>

</html>