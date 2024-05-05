<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <section class="galerie">
        <h2>Divertissement en vol</h2>
        <div class="conteneur-images">
            <a href="https://www.aeroflot.ru/fr-fr/information/in-flight/entertainment" target="_blank">
                <img src="https://www.pixelstalk.net/wp-content/uploads/2016/08/Fruit-Background-Download-Free.jpg"
                    alt="Divertissement en vol Aeroflot">
            </a>
            <a href="https://www.airfrance.fr/fr/informations-voyage/services-a-bord/divertissement-en-vol"
                target="_blank">
                <img src="https://wallpaperaccess.com/full/1101708.jpg" alt="Divertissement en vol Air France">
            </a>
            <a href="https://www.lufthansa.com/fr/fr/experience/a-bord/divertissement-en-vol" target="_blank"
                data-title="Numéro 3">
                <img src="https://www.pixelstalk.net/wp-content/uploads/2016/08/All-Fruit-Wallpaper-HD-Resolution.jpg"
                    alt="Divertissement en vol Lufthansa">
            </a>
            <a href="https://www.emirates.com/fr/fr/experience/ice-entertainment" target="_blank" data-title="Numéro 4">
                <img src="https://wallpapercave.com/wp/wp3145276.jpg" alt="Divertissement en vol Emirates">
            </a>
            <a href="https://www.qatarairways.com/fr/fr/services/inflight-entertainment.html" target="_blank"
                data-title="Numéro 5">
                <img src=" https://www.hdwallpaper.nu/wp-content/uploads/2016/12/fruit-14.jpg" alt="pictures">
            </a>
            <a href="https://www.qatarairways.com/fr/fr/services/inflight-entertainment.html" target="_blank"
                data-title="Numéro 5">Bonjour la famille
                <img src=" https://www.hdwallpaper.nu/wp-content/uploads/2016/12/fruit-14.jpg" alt="pictures">
            </a>
        </div>


    </section>
    <div class="image-avec-lien">
        <img src="https://www.hdwallpaper.nu/wp-content/uploads/2016/12/fruit-14.jpg" alt="pictures">
        <a href="https://www.qatarairways.com/fr/fr/services/inflight-entertainment.html" target="_blank"
            data-title="Numéro 5" class="lien-superposition">Bonjour la famille</a>
    </div>


    <style>
    .galerie {
        text-align: center;
        margin: 20px auto;
        max-width: 1000px;
    }

    .galerie h2 {
        margin-bottom: 20px;
        font-size: 24px;
    }

    .conteneur-images {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .conteneur-images a {
        display: block;
        margin: 10px;
        text-decoration: none;
    }

    .conteneur-images img {
        width: 300px;
        /* Ajuster la largeur en fonction de vos besoins */
        height: auto;
        /* Conserver le rapport hauteur/largeur */
        border: 1px solid #ccc;
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
    }

    .conteneur-images a::before {
        content: attr(data-title);
        display: block;
        background-color: rgba(255, 255, 255, 0.8);
        padding: 5px 10px;
        border-radius: 5px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1;
        font-weight: bold;
        font-size: 16px;
    }

    .image-avec-lien {
        position: relative;
        /* Permet de positionner les éléments enfants de manière relative */
    }

    .image-avec-lien img {
        max-width: 500px;
        height: auto;
        display: block;
        margin: 0 auto;
    }

    .lien-superposition {
        position: absolute;
        /* Positionnement absolu par rapport au parent */
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        /* Centrer le texte horizontalement et verticalement */
        color: white;
        /* Couleur du texte blanche */
        background-color: rgba(0, 0, 0, 0.5);
        /* Fond semi-transparent noir */
        padding: 10px 20px;
        /* Marge autour du texte */
        text-decoration: none;
        transition: background-color 0.3s ease;
        font-size: 20px;
        /* Taille du texte */
        font-weight: bold;
    }
    </style>

</body>

</html>