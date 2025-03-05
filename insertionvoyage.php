<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="CSS/admins/insertionvoyage.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Insertion voyage</title>

</head>

<body >
    <header>
        <nav>
            <div class="header-picture">
                <img src="logo général.jpg" alt="logo site" />
            </div>
            <!-- <div class="nav-bar">
                <ul>
                    <li class="items"> <a href="inventaire.php">Acceuil</a></li>
                    <li class="items"><a href="ajoutarticle.php">liste de Reservations</a></li>
                    <li class="items"><a href="ajoutclient.php">Nos services clients</a></li>
                    <li class="items"><a href="achatarticle.php">liste des trajets</a></li>
                </ul>
            </div> -->
            <div class="public">

                <div class="langue">
                    <select id="select" name="select" aria-placeholder="2 places">
                        <option value="option1">Français</option>
                        <option value="option2">Anglais</option>

                    </select>
                </div>
                <div class="outils"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"></i></div>

            </div>
        </nav>
    </header>
    <br>
    <br>
    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-2xl mx-auto">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Veuillez insérer un trajet de voyage</h2>

        <form action="#" method="POST" class="space-y-6">
            <!-- Ligne Départ et Arrivée -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Départ</label>
                    <select name="depart" class="w-full border rounded-lg px-3 py-2">
                        <option>Douala</option>
                        <option>Yaoundé</option>
                        <option>Bafoussam</option>
                        <option>Mbouda</option>
                        <option>Dschang</option>
                        <option>Bafang</option>
                        <option>Edea</option>
                        <option>Bamenda</option>
                        <option>Foumbot</option>
                        <option>Bagante</option>
                        <option>Kribi</option>
                        <option>Ngaoundéré</option>
                        <option>Ebolowa</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Arrivée</label>
                    <select name="arrivee" class="w-full border rounded-lg px-3 py-2">
                        <option>Douala</option>
                        <option>Yaoundé</option>
                        <option>Bafoussam</option>
                        <option>Mbouda</option>
                        <option>Dschang</option>
                        <option>Bafang</option>
                        <option>Edea</option>
                        <option>Bamenda</option>
                        <option>Foumbot</option>
                        <option>Bagante</option>
                        <option>Kribi</option>
                        <option>Ngaoundéré</option>
                        <option>Ebolowa</option>
                    </select>
                </div>
            </div>

            <!-- Type de bus -->
            <div>
                <label class="block text-gray-600 font-semibold mb-2">Type de bus</label>
                <select name="selectBus" class="w-full border rounded-lg px-3 py-2">
                    <option value="classique">Bus classique</option>
                    <option value="VIP">Bus VIP</option>
                </select>
            </div>

            <!-- Ligne Heure Départ et Heure Arrivée -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Heure de départ</label>
                    <input type="time" name="partir" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Heure d'arrivée</label>
                    <input type="time" name="destination" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>

            <!-- Ligne Date Départ et Prix -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Jour de départ</label>
                    <input type="date" name="date" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Prix</label>
                    <input type="text" name="prix" placeholder="Ex: 10000" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-center space-x-4">
                <button type="reset" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600">
                    Annuler
                </button>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    Insérer
                </button>
            </div>
        </form>
    </div>
    <br>
    

    <form action="listevoyadmin.php" method="post">
        <div class="end">
            <div class="para">
                <p>consultez la liste des voyages disponibles en cliquant sur le bouton suivant:</p>
            </div>
            <div> <button type="submit" class="liste-voyage">liste voyages</button> </div>
        </div>
    </form>

    <footer>
        <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>
    <?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

    if (isset($_POST['depart']) && isset($_POST['prix']) && isset($_POST['destination']) && isset($_POST['arrivee']) && isset($_POST['selectBus']) && isset($_POST['partir']) && isset($_POST['date'])) {
        $depart = $_POST['depart'];
        $arrive = $_POST['arrivee'];
        $bus = $_POST['selectBus'];
        $heureDepart = $_POST['partir'];
        $heureArrivee = $_POST['destination'];
        $prix = $_POST['prix'];
        $date = $_POST['date'];

        $requete = "INSERT INTO voyage (villeDepart, villeArrivee, typeBus, prix, heureDepart, heureArrivee, jourDepart) VALUES ('$depart', '$arrive', '$bus', '$prix', '$heureDepart', '$heureArrivee', '$date')";
        $bdd->exec($requete);
        echo 'Insertion réussie';
    }
} catch (Exception $e) {
    echo 'Échec de connexion : '.$e->getMessage();
}
    ?>


    <style>
   
    </style>

</body>

</html>