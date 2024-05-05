<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOOTER</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
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
</body>


<style>
footer {
    background-color: rgb(247, 247, 247);
    padding: 40px;
    font-family: Arial, sans-serif;
    box-shadow: inset 5px 5px 10px -5px rgba(0, 0, 0, 0.5);
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

#outillage {
    /* display: flex; */
    position: fixed;
    top: 50px;
}
</style>

</html>