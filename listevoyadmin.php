<?php
session_start();
?>


<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <meta charset="UTF-8">
    <title>Tableau moderne</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>

<body>
    <header>
        <nav>
            <div class="header-picture">
                <img src="logo général.jpg" alt="logo site" />
            </div>
            <div class="nav-bar">
                <ul>

                    <li><a href="notification.php">
                            <i class="fa fa-bell fa-2x" aria-hidden="true" style="color: white;"></i>
                            <?php
       $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
$requeteCount = 'SELECT COUNT(*) as count FROM admins WHERE lu = 0';
$resultatCount = $bdd->query($requeteCount);
if ($resultatCount !== false) {
    // Vérifiez le résultat avant d'appeler fetchColumn()
    $count = $resultatCount->fetchColumn();

    if ($count !== false) {
        // Afficher le nombre s'il y en a
        if ($count > 0) {
            echo '<span class="badge">'.$count.'</span>';
        }
    } else {
        // Gérer l'erreur lors de l'appel de fetchColumn()
        echo 'Erreur lors de la récupération du nombre de messages non lus.';
    }
} else {
    // Gérer l'erreur lors de l'exécution de la requête SQL
    $errorInfo = $bdd->errorInfo();
    echo 'Erreur SQL : '.$errorInfo[2];
}
?>
                        </a></li>
                    <li class="outils"><a href="#"><i class="fa fa-user-circle-o fa-6x" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>
    <br>

    <!-- POPUP DE SUPPRESSION D'UN VOYAGE -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Suppression du trajet</h2>
            <p>Êtes-vous sûr de vouloir supprimer ce voyage ?</p>

            <form method="POST" action="">
                <input type="hidden" id="id_voyage" name="id_voyage" value="">
                <button type="submit" name="delete_voyage">Supprimer</button>
            </form>
        </div>
    </div>

    <!-- CODE PHP POUR LA SUPPRESSION D'UN VOYAGE' -->
    <?php
// Vérifier si le formulaire de suppression a été soumis
if (isset($_POST['delete_voyage'])) {
    $host = 'localhost'; // nom d'hôte
    $user = 'root'; // nom d'utilisateur
    $password = ''; // mot de passe
    $database = 'bd_stock'; // nom de la base de données

    // Connexion à la base de données MySQLi
    $conn = mysqli_connect($host, $user, $password, $database);
    $id = $_POST['id_voyage'];
    $query = "DELETE FROM voyage WHERE idVoyage =$id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<meta http-equiv='refresh' content='3;url=listevoyadmin.php'>";
        echo "<div style='height: 100px; width: 600px; background-color: green; color: white;
font-size: 25px; padding: 30px; text-align: center; margin: 0 auto; margin-top: 150px; '>
Voyage supprimé avec succès.
</div>";
    } else {
        echo 'Erreur lors de la suppression du voyage.';
    }
    header('Location: listevoyadmin.php');
    mysqli_close($conn);
    exit;
}
?>


    <?php
   try {
       $conn = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

       echo "
        <table class='my-table'>
        <caption>liste des trajets disponibles</caption>
        <tr>
            <th scope='col'>Id voyage</th>
            <th scope='col'>Ville départ</th>
            <th scope='col'>Ville arrivée</th>
            <th scope='col'>heure départ</th>
            <th scope='col'>heure arrivée</th> 
            <th scope='col'>type de bus</th>
            <th scope='col'>Prix</th>
            <th scope='col'>Actions</th>
        </tr>";
       $requette = 'select * From voyage';
       $resultat = $conn->query($requette);

       while ($donne = $resultat->fetch()) {
           $heure = $donne['heureDepart'];
           $depart = $donne['villeDepart'];
           $arrive = $donne['villeArrivee'];
           $price = $donne['prix'];
           $bus = $donne['typeBus'];
           $heure2 = $donne['heureArrivee'];
           $idvoyage = $donne['idVoyage'];

           echo "
    <tr>
        <td>$idvoyage</td>
        <td>$depart</td>
        <td>$arrive</td>
        <td>$heure</td>
        <td>$heure2</td> 
        <td>$bus</td>
        <td>$price</td>  
        <td rowspan=''>
            <div class='colonne-divisee'>
                <form method='post' action='modifier.php'>
                    <input type='hidden' name='id_voyage' value='$idvoyage'>
                    <button type='submit' style='background-color: green;  cursor: pointer; color:white;'>Modifier</button>
                </form>
                <form method='post' action=''>
                    <input type='hidden' name='id_voyage' value='$idvoyage'>
                    <button type='button' onclick='openModal()' style='background-color: red; margin-left: 35px; cursor: pointer;'>Supprimer</button>
                </form>
            </div>
        </td> 
    </tr>
";
       }

       echo '</table>';

       echo '
<style>
.my-table {
    border-collapse: collapse;
}

.my-table th, .my-table td {
    border: 1px solid black;
    padding: 8px;
}
</style>
';
   } catch (\Throwable $th) {
       echo 'Echec de connexion';
   }

?>




    <body class="bg-gray-100 dark:bg-gray-900">
        <aside
            class="fixed top-0 z-10 ml-[-100%] flex h-screen w-full flex-col justify-between border-r bg-white px-6 pb-3 transition duration-300 md:w-4/12 lg:ml-0 lg:w-[25%] xl:w-[20%] 2xl:w-[15%] dark:bg-gray-800 dark:border-gray-700">
            <div>
                <div class="-mx-6 px-6 py-4">
                    <a href="#" title="home">
                        <img src="images/logo.svg" class="w-32" alt="tailus logo" />
                    </a>
                </div>

                <div class="mt-8 text-center">
                    <img src="images/second_user.webp" alt=""
                        class="m-auto h-10 w-10 rounded-full object-cover lg:h-28 lg:w-28" />
                    <h5 class="mt-4 hidden text-xl font-semibold text-gray-600 lg:block dark:text-gray-300">Cynthia J.
                        Watts</h5>
                    <span class="hidden text-gray-400 lg:block">Admin</span>
                </div>

                <ul class="mt-8 space-y-2 tracking-wide">
                    <li>
                        <a href="#" aria-label="dashboard"
                            class="relative flex items-center space-x-4 rounded-xl bg-gradient-to-r from-sky-600 to-cyan-400 px-4 py-3 text-white">
                            <svg class="-ml-1 h-6 w-6" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M6 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V8ZM6 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2v-1Z"
                                    class="dark:fill-slate-600 fill-current text-cyan-400"></path>
                                <path d="M13 8a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2V8Z"
                                    class="fill-current text-cyan-200 group-hover:text-cyan-300"></path>
                                <path d="M13 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v1a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-1Z"
                                    class="fill-current group-hover:text-sky-300"></path>
                            </svg>
                            <span class="-mr-1 font-medium">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="group flex items-center space-x-4 rounded-md px-4 py-3 text-gray-600 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path class="fill-current text-gray-300 group-hover:text-cyan-300" fill-rule="evenodd"
                                    d="M2 6a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1H8a3 3 0 00-3 3v1.5a1.5 1.5 0 01-3 0V6z"
                                    clip-rule="evenodd" />
                                <path
                                    class="fill-current text-gray-600 group-hover:text-cyan-600 dark:group-hover:text-sky-400"
                                    d="M6 12a2 2 0 012-2h8a2 2 0 012 2v2a2 2 0 01-2 2H2h2a2 2 0 002-2v-2z" />
                            </svg>
                            <span class="group-hover:text-gray-700 dark:group-hover:text-gray-50">Categories</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="group flex items-center space-x-4 rounded-md px-4 py-3 text-gray-600 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    class="fill-current text-gray-600 group-hover:text-cyan-600 dark:group-hover:text-sky-400"
                                    fill-rule="evenodd"
                                    d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z"
                                    clip-rule="evenodd" />
                                <path class="fill-current text-gray-300 group-hover:text-cyan-300"
                                    d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z" />
                            </svg>
                            <span class="group-hover:text-gray-700 dark:group-hover:text-gray-50">Reports</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="group flex items-center space-x-4 rounded-md px-4 py-3 text-gray-600 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    class="fill-current text-gray-600 group-hover:text-cyan-600 dark:group-hover:text-cyan-400"
                                    d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z" />
                                <path class="fill-current text-gray-300 group-hover:text-cyan-300"
                                    d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z" />
                            </svg>
                            <span class="group-hover:text-gray-700 dark:group-hover:text-gray-50">Other data</span>
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="group flex items-center space-x-4 rounded-md px-4 py-3 text-gray-600 dark:text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path class="fill-current text-gray-300 group-hover:text-cyan-300"
                                    d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                                <path
                                    class="fill-current text-gray-600 group-hover:text-cyan-600 dark:group-hover:text-sky-400"
                                    fill-rule="evenodd"
                                    d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="group-hover:text-gray-700 dark:group-hover:text-white">Finance</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="-mx-6 flex items-center justify-between border-t px-6 pt-4 dark:border-gray-700">
                <button class="group flex items-center space-x-4 rounded-md px-4 py-3 text-gray-600 dark:text-gray-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="group-hover:text-gray-700 dark:group-hover:text-white">Logout</span>
                </button>
            </div>
        </aside>
        <div class="ml-auto mb-6 lg:w-[75%] xl:w-[80%] 2xl:w-[85%]">
            <div class="sticky top-0 h-16 border-b bg-white dark:bg-gray-800 dark:border-gray-700 lg:py-2.5">
                <div class="flex items-center justify-between space-x-4 px-6 2xl:container">
                    <h5 hidden class="text-2xl font-medium text-gray-600 lg:block dark:text-white">Dashboard</h5>
                    <button class="-mr-2 h-16 w-12 border-r lg:hidden dark:border-gray-700 dark:text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="my-auto h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="flex space-x-4">
                        <!--search bar -->
                        <div hidden class="md:block">
                            <div class="relative flex items-center text-gray-400 focus-within:text-cyan-400">
                                <span
                                    class="absolute left-4 flex h-6 items-center border-r border-gray-300 pr-3 dark:border-gray-700">
                                    <svg xmlns="http://ww50w3.org/2000/svg" class="w-4 fill-current"
                                        viewBox="0 0 35.997 36.004">
                                        <path id="Icon_awesome-search" data-name="search"
                                            d="M35.508,31.127l-7.01-7.01a1.686,1.686,0,0,0-1.2-.492H26.156a14.618,14.618,0,1,0-2.531,2.531V27.3a1.686,1.686,0,0,0,.492,1.2l7.01,7.01a1.681,1.681,0,0,0,2.384,0l1.99-1.99a1.7,1.7,0,0,0,.007-2.391Zm-20.883-7.5a9,9,0,1,1,9-9A8.995,8.995,0,0,1,14.625,23.625Z">
                                        </path>
                                    </svg>
                                </span>
                                <input type="search" name="leadingIcon" id="leadingIcon" placeholder="Search here"
                                    class="outline-none w-full rounded-xl border border-gray-300 py-2.5 pl-14 pr-4 text-sm text-gray-600 transition focus:border-cyan-300 dark:bg-gray-900 dark:border-gray-700" />
                            </div>
                        </div>
                        <!--/search bar -->
                        <button aria-label="search"
                            class="h-10 w-10 rounded-xl border bg-gray-100 active:bg-gray-200 md:hidden dark:bg-gray-700 dark:border-gray-600 dark:active:bg-gray-800">
                            <svg xmlns="http://ww50w3.org/2000/svg"
                                class="mx-auto w-4 fill-current text-gray-600 dark:text-gray-300"
                                viewBox="0 0 35.997 36.004">
                                <path id="Icon_awesome-search" data-name="search"
                                    d="M35.508,31.127l-7.01-7.01a1.686,1.686,0,0,0-1.2-.492H26.156a14.618,14.618,0,1,0-2.531,2.531V27.3a1.686,1.686,0,0,0,.492,1.2l7.01,7.01a1.681,1.681,0,0,0,2.384,0l1.99-1.99a1.7,1.7,0,0,0,.007-2.391Zm-20.883-7.5a9,9,0,1,1,9-9A8.995,8.995,0,0,1,14.625,23.625Z">
                                </path>
                            </svg>
                        </button>
                        <button aria-label="chat"
                            class="h-10 w-10 rounded-xl border bg-gray-100 active:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:active:bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="m-auto h-5 w-5 text-gray-600 dark:text-gray-300" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                        </button>
                        <button aria-label="notification"
                            class="h-10 w-10 rounded-xl border bg-gray-100 active:bg-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:active:bg-gray-800">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="m-auto h-5 w-5 text-gray-600 dark:text-gray-300" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="px-6 pt-6 2xl:container">
                <div
                    class="flex h-[80vh] items-center justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <span class="dark:text-white">Content</span>
                </div>
            </div>
        </div>
    </body>



    <style>
    .badge {
        background-color: red;
        color: white;
        padding: 2px 6px;
        border-radius: 50%;
        font-size: 12px;
        position: absolute;
        top: 36px;
        right: 163px;
    }


    body {

        font-family: Arial, sans-serif;
        background-color: #f5f5f5;
    }

    table {
        border-collapse: collapse;
        width: 100%;
    }

    th,
    td {
        border: 1px solid black;
        padding: 8px;
    }

    th {
        background-color: #f2f2f2;
    }

    .colonne-divisee {
        display: flex;
        margin: 5px;
    }

    .colonne-gauche,
    .colonne-droite {
        flex: 1;
        padding: 5px;
    }


    .modal {
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

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* PARTIE HEADER  */
    body {
        background-color: aliceblue;
    }

    header {
        width: 100%;
        background-color: green;
        height: 95px;
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
        margin-right: 40PX;
        padding: 0 15px;
    }


    .nav-bar ul {
        display: flex;
        list-style-type: none;
        gap: 30px;
    }

    .header-picture {
        margin-left: 40px;

    }

    img {

        height: 55px;
        width: 95px;
    }

    .nav-bar {
        margin-right: 30px;
    }

    .outils i {
        color: white;
        font-size: 37px;
        margin-left: 30px;
        cursor: pointer;
    }

    caption {
        font-size: 20px;
        font-weight: bold;

        margin: 10px;
    }
    </style>

    <script>
    // function openModal() {
    //     document.getElementById("myModal").style.display = "block";
    // }


    // function closeModal() {
    //     document.getElementById("myModal").style.display = "none";
    // }


    // function deleteItem() {

    //     alert("L'élément a été supprimé !");
    //     closeModal(); 
    // }

    function openModal() {
        var idvoyage = "<?php echo $idvoyage; ?>";
        document.getElementById("id_voyage").value = idvoyage;
        var modal = document.getElementById("myModal");
        modal.style.display = "block";
    }

    function closeModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "none";
    }
    </script>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>



<!-- 
<form method='post' action=''>
            <input type='hidden' name='id_voyage' value='$idvoyage'>
            <button type='submit' style='background-color: green; margin-right: 5px;'>Modifier</button>
            <button type='submit'  name='delete_voyage'  style='background-color: red;'>Supprimer</button>
        </form> -->


<!-- $id= $_POST['id_voyage']; -->


<!-- <form method='post' action='supprimer.php'>
            <input type='hidden' name='id_voyage' value='$idvoyage'> -->
<!-- </form> -->