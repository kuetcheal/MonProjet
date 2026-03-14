<?php
session_start();

// Si l'utilisateur est déjà connecté
if (isset($_SESSION['Id_compte'])) {
    header('Location: Accueil.php');
    exit;
}

$erreurConnexion = "";

// Connexion base de données
$host = 'localhost';
$dbUser = 'root';
$dbPassword = '';
$database = 'bd_stock';

$conn = new mysqli($host, $dbUser, $dbPassword, $database);

if ($conn->connect_error) {
    die('Connexion échouée : ' . $conn->connect_error);
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {

        // Cas ADMIN
        if ($username === 'GENERAL' && $password === '123general') {
            $_SESSION['admin_name'] = 'GENERAL';
            header('Location: Accueiladmin.php');
            exit;
        }

        // Recherche utilisateur
        $stmt = $conn->prepare('SELECT * FROM user WHERE user_name = ? LIMIT 1');

        if ($stmt) {

            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {

                $user = $result->fetch_assoc();

                // Vérification mot de passe hashé
                if (password_verify($password, $user['user_password'])) {

                    // Création session utilisateur
                    $_SESSION['Id_compte'] = $user['id'];
                    $_SESSION['user_name'] = $user['user_name'];
                    $_SESSION['user_firstname'] = $user['user_firstname'];
                    $_SESSION['user_mail'] = $user['user_mail'];
                    $_SESSION['user_phone'] = $user['user_phone'];

                    header('Location: Accueil.php');
                    exit;

                } else {
                    $erreurConnexion = "Mot de passe incorrect.";
                }

            } else {
                $erreurConnexion = "Nom d'utilisateur incorrect.";
            }

            $stmt->close();

        } else {
            $erreurConnexion = "Erreur serveur.";
        }

    } else {
        $erreurConnexion = "Veuillez remplir tous les champs.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Connexion</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<link rel="stylesheet" href="connexion.css">

</head>

<body>

<header>

<nav>

<div class="header-picture">
<img src="pictures/logo-general.jpg" alt="logo site">
</div>

<div class="nav-bar">

<ul>

<li class="items">
<select>
<option>Français</option>
<option>Anglais</option>
</select>
</li>

<li class="items">
<a href="#">
<i class="fa fa-user-circle-o fa-2x"></i>
</a>
</li>

</ul>

</div>

</nav>

</header>

<div class="container">

<h1>Connexion</h1>

<form method="post" action="connexion.php">

<label style="color:white;">Nom d'utilisateur :</label>

<input type="text"
name="username"
placeholder="ALEX"
required>

<label style="color:white;">Mot de passe :</label>

<input type="password"
name="password"
placeholder="********"
required>

<button type="submit">Se connecter</button>

<?php
if (!empty($erreurConnexion)) {
    echo "<p class='para2'>$erreurConnexion</p>";
}
?>

<p class="para">
Oupps, un problème ?
<a href="forgetpassword.php">Mot de passe oublié</a>
</p>

<p class="para">
Pas encore de compte ?
<a href="inscription.php">S'inscrire</a>
</p>

</form>

</div>

<footer>
<p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
</footer>

<style>

body{
background-color:aliceblue;
}

header{
width:100%;
background-color:green;
height:100px;
}

nav{
display:flex;
justify-content:space-between;
align-items:center;
}

.header-picture{
margin-left:40px;
}

.header-picture img{
height:60px;
width:100px;
}

.nav-bar{
margin-right:30px;
}

.nav-bar ul{
display:flex;
list-style:none;
}

.items a{
color:white;
font-size:20px;
margin-right:40px;
}

.container{

background-color:green;

border-radius:5px;

box-shadow:0 0 10px rgba(0,0,0,0.3);

margin:50px auto;

max-width:500px;

padding:20px;

}

h1{
text-align:center;
color:white;
}

form{
display:flex;
flex-direction:column;
}

input{
padding:10px;
margin-bottom:20px;
border-radius:5px;
border:none;
}

button{
padding:10px;
background:#4CAF50;
color:white;
border:none;
border-radius:5px;
cursor:pointer;
}

button:hover{
background:#3e8e41;
}

.para{
color:white;
font-size:16px;
}

.para a{
color:white;
font-weight:bold;
}

.para2{
color:red;
text-align:center;
font-size:18px;
}

footer{
background:#6c757d;
color:white;
text-align:center;
padding:20px;
margin-top:30px;
}

</style>

</body>

</html>