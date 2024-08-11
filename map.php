<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations de voyage</title>

    <!-- Inclure le CSS de Leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Inclure le CSS personnalisé -->
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        background-color: #f5f5f5;
    }

    .contient {
        display: flex;
        width: 1300px;
        margin: 0 auto;
        padding: 20px;
        background-color: white;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .desti {
        flex: 1;
        padding-right: 20px;
    }

    .desti h1 {
        font-size: 24px;
        margin-bottom: 20px;
    }

    .desti ul {
        list-style-type: none;
        padding: 0;
    }

    .desti li {
        margin-bottom: 10px;
    }

    .desti li p {
        margin: 0;
        font-size: 16px;
    }

    .desti li strong {
        display: block;
        font-size: 18px;
    }

    #map {
        flex: 1;
        height: 500px;
        width: 600px;
        position: relative;
    }

    #expandBtn {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: white;
        border: 1px solid #ccc;
        padding: 5px 10px;
        cursor: pointer;
        z-index: 1000;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    }

    #expandBtn:hover {
        background-color: #f0f0f0;
    }
    </style>
</head>

<body>
    <div class="contient">
        <div class="desti">
            <h1>10 destinations de voyage au Cameroun</h1>
            <ul>
                <li>
                    <strong>Douala</strong>
                    <p>Adresse: Bonanjo, Douala</p>
                </li>
                <li>
                    <strong>Yaoundé-Mvan</strong>
                    <p>Adresse: Mvan, Yaoundé</p>
                </li>
                <li>
                    <strong>Édéa</strong>
                    <p>Adresse: Centre-ville, Édéa</p>
                </li>
                <li>
                    <strong>Bafoussam</strong>
                    <p>Adresse: Centre-ville, Bafoussam</p>
                </li>
                <li>
                    <strong>Kribi</strong>
                    <p>Adresse: Centre-ville, Kribi</p>
                </li>
                <li>
                    <strong>Mbouda</strong>
                    <p>Adresse: Centre-ville, Mbouda</p>
                </li>
                <li>
                    <strong>Garoua</strong>
                    <p>Adresse: Centre-ville, Garoua</p>
                </li>
                <li>
                    <strong>Limbe</strong>
                    <p>Adresse: Centre-ville, Limbe</p>
                </li>
                <li>
                    <strong>Maroua</strong>
                    <p>Adresse: Centre-ville, Maroua</p>
                </li>
                <li>
                    <strong>Bamenda</strong>
                    <p>Adresse: Centre-ville, Bamenda</p>
                </li>
                <li>
                    <strong>Ebolowa</strong>
                    <p>Adresse: Centre-ville, Ebolowa</p>
                </li>
                <!-- <li>
                    <strong>Ngaoundéré (Adamoua)</strong>
                    <p>Adresse: Centre-ville, Ngaoundéré</p>
                </li> -->
            </ul>
        </div>
        <div id="map">
            <button id="expandBtn">Agrandir</button>
        </div>
    </div>

    <!-- Inclure le JS de Leaflet -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Script JS pour configurer la carte -->
    <script>
    var map = L.map('map').setView([4.0511, 9.7679], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    var destinations = [{
            name: 'Douala',
            lat: 4.0511,
            lon: 9.7679,
            address: 'Bonanjo, Douala'
        },
        {
            name: 'Yaoundé-Mvan',
            lat: 3.8480,
            lon: 11.5021,
            address: 'Mvan, Yaoundé'
        },
        {
            name: 'Édéa',
            lat: 3.80816,
            lon: 10.13257,
            address: 'Centre-ville, Édéa'
        },
        {
            name: 'Bafoussam',
            lat: 5.47775,
            lon: 10.41759,
            address: 'Centre-ville, Bafoussam'
        },
        {
            name: 'Kribi',
            lat: 2.93718,
            lon: 9.90793,
            address: 'Centre-ville, Kribi'
        },
        {
            name: 'Mbouda',
            lat: 5.62825,
            lon: 10.25439,
            address: 'Centre-ville, Mbouda'
        },
        {
            name: 'Garoua',
            lat: 9.3274,
            lon: 13.3931,
            address: 'Centre-ville, Garoua'
        },
        {
            name: 'Limbe',
            lat: 4.02429,
            lon: 9.21492,
            address: 'Centre-ville, Limbe'
        },
        {
            name: 'Maroua',
            lat: 10.5956,
            lon: 14.3247,
            address: 'Centre-ville, Maroua'
        },
        {
            name: 'Bamenda',
            lat: 5.9631,
            lon: 10.1591,
            address: 'Centre-ville, Bamenda'
        },
        {
            name: 'Ebolowa',
            lat: 2.9122,
            lon: 11.1511,
            address: 'Centre-ville, Ebolowa'
        },
        {
            name: 'Ngaoundéré (Adamoua)',
            lat: 7.32765,
            lon: 13.58359,
            address: 'Centre-ville, Ngaoundéré'
        }
    ];

    destinations.forEach(function(dest) {
        L.marker([dest.lat, dest.lon]).addTo(map)
            .bindPopup('<strong>' + dest.name + '</strong><br>' + dest.address)
            .on('mouseover', function(e) {
                this.openPopup();
            })
            .on('mouseout', function(e) {
                this.closePopup();
            });
    });

    // Fonction pour agrandir/réduire la carte
    var isExpanded = false;
    document.getElementById('expandBtn').addEventListener('click', function() {
        if (isExpanded) {
            document.getElementById('map').style.height = '500px';
            document.getElementById('map').style.width = '600px';
            this.innerText = 'Agrandir';
        } else {
            document.getElementById('map').style.height = '100vh';
            document.getElementById('map').style.width = '100%';
            this.innerText = 'Réduire';
        }
        isExpanded = !isExpanded;
        map.invalidateSize(); // Met à jour la carte après changement de taille
    });
    </script>
</body>

</html>