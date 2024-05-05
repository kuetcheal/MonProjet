<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <title>Document</title>
</head>

<body>
    <header>
        <nav>
            <div class="header-picture">
                <img src="logo général.jpg" alt="logo site" />
            </div>
            <div class="nav-bar">
                <ul>
                    <li class="items">
                        <select id="select" name="select" aria-placeholder="2 places">
                            <option value="option1">Français</option>
                            <option value="option2">Anglais</option>

                        </select>
                    </li>
                    <li class="items"><a href="#"><i class="fa fa-user-circle-o fa-2x" aria-hidden="true"></i></a></li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="main">
        <div class="partie2">
            <img src="pictures/eleve.jpg" alt="image" class="ascillation">
        </div>

        <div class="formulaire-reservation">
            <h2>Gestion de votre réservation</h2>
            <div class="formu">
                <form action="#">
                    <div class="champ">
                        <label for="email">Adresse e-mail <span>*</span></label><br>
                        <input type="email" id="mail" name="mail" required>
                    </div>
                    <div class="champ">
                        <label for="numero-reservation">Numéro de réservation <span>*</span></label><br>
                        <input type="text" id="numero-reservation" name="numero-reservation" required>
                    </div>
                    <button type="submit">Confirmer</button>
                </form>
            </div>
        </div>

    </div>
    <h1>vous satifaire est notre priorité</h1><br>
    <?php include 'footer.php'; ?>

</body>
<style>
/* header body { */
body {
    background-color: aliceblue;
}

header {
    width: 100%;
    background-color: green;
    height: 100px;
}

.main {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    padding: 50px;
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
}

.header-picture {
    margin-left: 40px;

}

.partie2 img {
    height: 380px;
    width: 480px;
}

img {

    height: 60px;
    width: 100px;
}

.nav-bar {
    margin-right: 30px;
}

.formulaire-reservation {
    display: flex;
    flex-direction: column;
}

h2 {
    margin-bottom: 20px;
    font-size: 24px;
}

h1 {
    text-align: center;
    color: green;
}

.formu {
    border: 1px solid #ccc;
    height: 300px;
    border-radius: 5px;
    width: 400px;
    display: flex;
    flex-direction: column;
    padding: 15px;
}

.formu form {
    margin-bottom: 40px;
    /* Ajoute de l'espace en bas du formulaire */
}

.formu .champ {
    margin-bottom: 20px;
    /* Ajoute de l'espace entre chaque champ */
}

.formu button {
    margin-top: 20px;
    /* Ajoute de l'espace au-dessus du bouton */
}

label {
    display: block;
    font-size: 16px;
    font-weight: bold;
}

input {
    width: 300px;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;

}

button {
    background-color: #4CAF50;
    color: white;
    height: 40px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 200px;
    font-size: 16px;
    margin-left: 60px;
}

button:hover {
    background-color: #45a049;
}

.ascillation {
    animation: ascillation 3s infinite;
}

span {
    color: red;
}
</style>

</html>