<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script> -->
    <title>Document</title>
</head>

<body>
    <!-- Modal Container -->
    <div id="exampleModal" class="modal">
        <div class="modal-content">
            <div class="infos">
                <h1>Gérez votre reservation</h1>
                <span class="close-button">×</span>
            </div>
            <form method="post" action="#">
                <label for="exampleInputEmail1">Votre adresse mail</label>
                <input type="email" id="exampleInputEmail1" name="mail" placeholder="alex99@gmail.com">
                <br>
                <label for="exampleFormControlInput1">Numéro réservation</label>
                <input type="text" id="exampleFormControlInput1" name="reservation" placeholder="8I4P5SPD">
                <br>
                <button type="submit">Vérifier</button>
            </form>
        </div>
    </div>

    <?php
session_start();  // S'assurer de démarrer la session en haut du script

$host = "localhost"; 
$user = "root"; 
$password = ""; 
$database = "bd_stock"; 

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Échec de la connexion: " . mysqli_connect_error());
}

if (isset($_POST["mail"], $_POST["reservation"])) {
    $mail = mysqli_real_escape_string($conn, $_POST['mail']);
    $reservation = mysqli_real_escape_string($conn, $_POST['reservation']);

    $query = "SELECT idReservation FROM reservation WHERE email = ? AND Numero_reservation = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ss", $mail, $reservation);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $valeur = mysqli_fetch_assoc($result);
        $_SESSION['idReservation'] = $valeur['idReservation'];
        header("Location: Reservation/Ma_reservation.php");
        exit;
    } else {
        header("Location: Reservation/error.php");
        exit;
    }
} else {
    echo "Veuillez fournir un email et un numéro de réservation.";
}
?>



    <style>
    /* The Modal (background) */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1000;
        /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4);
        /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        height: 270px;
        padding: 20px;
        border: 1px solid #888;
        width: 440px;
        left: 30px;
        top: 10px;
    }

    /* The Close Button */
    .close-button {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close-button:hover,
    .close-button:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    h1 {
        color: green;
    }

    /* Style pour le titre et le bouton de fermeture */
    .infos {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    h1 {
        color: green;
        margin-left: 25px;
    }

    .close-button {
        font-size: 1.4em;
        color: #aaa;
        cursor: pointer;
    }

    .close-button:hover {
        color: #f00;
    }

    /* Styles des formulaires pour améliorer la lisibilité et l'interaction */
    form {
        margin-top: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        color: black;
    }

    input[type="email"],
    input[type="text"] {
        width: 100%;
        padding: 8px;
        margin-bottom: 20px;
        border: 1px solid #ddd;
        border-radius: 4px;
        box-sizing: border-box;
        /* Ajoute le padding et la bordure dans la largeur de l'élément */
    }

    input[type="email"]:focus,
    input[type="text"]:focus {
        border-color: #0056b3;
        outline: none;
    }

    button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #0056b3;
    }
    </style>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script> -->
</body>

</html>