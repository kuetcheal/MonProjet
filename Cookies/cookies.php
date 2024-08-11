<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cookies</title>
    <style>
    /* Style général pour le fond semi-transparent */
    .cookie-banner {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        /* Fond semi-transparent */
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    /* Style de la popup elle-même */
    .cookie-popup {
        background-color: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        width: 550px;
        text-align: center;
    }

    .cookie-popup h2 {
        font-size: 1.5em;
        margin-bottom: 15px;
    }

    .cookie-popup p {
        margin-bottom: 20px;
    }

    .cookie-popup button {
        padding: 10px 20px;
        margin: 5px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .cookie-popup .accept-btn {
        background-color: #007bff;
        color: #fff;
    }

    .cookie-popup .decline-btn {
        background-color: #ccc;
        color: #000;
    }
    </style>

</head>

<body>
    <div class="cookie-banner" id="cookieBanner">
        <div class="cookie-popup">
            <!-- 
            <img src="/general-remove-preview.png" alt="logo site" /> -->

            <h2 style="color: green;">Le respect de votre vie privée nous tient à cœur</h2>
            <p>EasyTravel et ses partenaires utilisent des cookies (ou des technologies similaires) pour évaluer et
                analyser l'utilisation de la plateforme, et pour vous proposer des publicités ciblées basées sur ce
                qui
                vous intéresse. En cliquant sur « Accepter et poursuivre », vous autorisez ces cookies, mais vous
                pourrez modifier ces préférences à tout moment dans les Paramètres des cookies. Veuillez noter que le
                blocage de certains types de cookies pourrait affecter votre utilisation de l'appli BlaBlaCar et de
                certaines de ses fonctionnalités. Pour plus d'informations, consultez notre <a href="">Charte sur
                    les cookies</a>.</p>
            <button class="accept-btn" onclick="acceptCookies()">Accepter et poursuivre</button>
            <button class="decline-btn" onclick="declineCookies()">Tout refuser</button>
        </div>
    </div>


    <script>
    function acceptCookies() {
        document.cookie = "cookiesAccepted=true; path=/; max-age=" + (60 * 60 * 24 * 365);
        document.getElementById('cookieBanner').style.display = 'none';
    }

    function declineCookies() {
        document.cookie = "cookiesAccepted=false; path=/; max-age=" + (60 * 60 * 24 * 365);
        document.getElementById('cookieBanner').style.display = 'none';
    }

    // Vérifier si le cookie a déjà été accepté
    window.onload = function() {
        if (document.cookie.indexOf('cookiesAccepted=') === -1) {
            document.getElementById('cookieBanner').style.display = 'flex';
        } else {
            document.getElementById('cookieBanner').style.display = 'none';
        }
    }
    </script>

</body>

</html>