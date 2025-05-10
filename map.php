<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations de voyage</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="entete">
    <div class="flex flex-col md:flex-row max-w-7xl mx-auto  p-4 mt-6 space-y-6 md:space-y-0 md:space-x-6 sommet">
        <div class="flex-1">
            <h1 class="text-2xl font-bold mb-4">10 destinations de voyage au Cameroun</h1>
            <ul class="space-y-3">
                <li><strong class="text-lg">Douala</strong><p>Adresse: Bonanjo, Douala</p></li>
                <li><strong class="text-lg">Yaoundé-Mvan</strong><p>Adresse: Mvan, Yaoundé</p></li>
                <li><strong class="text-lg">Édéa</strong><p>Adresse: Centre-ville, Édéa</p></li>
                <li><strong class="text-lg">Bafoussam</strong><p>Adresse: Centre-ville, Bafoussam</p></li>
                <li><strong class="text-lg">Kribi</strong><p>Adresse: Centre-ville, Kribi</p></li>
                <li><strong class="text-lg">Mbouda</strong><p>Adresse: Centre-ville, Mbouda</p></li>
                <li><strong class="text-lg">Garoua</strong><p>Adresse: Centre-ville, Garoua</p></li>
                <li><strong class="text-lg">Limbe</strong><p>Adresse: Centre-ville, Limbe</p></li>
                <li><strong class="text-lg">Maroua</strong><p>Adresse: Centre-ville, Maroua</p></li>
                <li><strong class="text-lg">Bamenda</strong><p>Adresse: Centre-ville, Bamenda</p></li>
                <li><strong class="text-lg">Ebolowa</strong><p>Adresse: Centre-ville, Ebolowa</p></li>
            </ul>
        </div>
        <div id="map" class="relative flex-1 h-screen w-screen rounded-lg">
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        var map = L.map('map').setView([4.0511, 9.7679], 6);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
        }).addTo(map);

        var destinations = [
            { name: 'Douala', lat: 4.0511, lon: 9.7679, address: 'Bonanjo, Douala' },
            { name: 'Yaoundé-Mvan', lat: 3.8480, lon: 11.5021, address: 'Mvan, Yaoundé' },
            { name: 'Édéa', lat: 3.80816, lon: 10.13257, address: 'Centre-ville, Édéa' },
            { name: 'Bafoussam', lat: 5.47775, lon: 10.41759, address: 'Centre-ville, Bafoussam' },
            { name: 'Kribi', lat: 2.93718, lon: 9.90793, address: 'Centre-ville, Kribi' },
            { name: 'Mbouda', lat: 5.62825, lon: 10.25439, address: 'Centre-ville, Mbouda' },
            { name: 'Garoua', lat: 9.3274, lon: 13.3931, address: 'Centre-ville, Garoua' },
            { name: 'Limbe', lat: 4.02429, lon: 9.21492, address: 'Centre-ville, Limbe' },
            { name: 'Maroua', lat: 10.5956, lon: 14.3247, address: 'Centre-ville, Maroua' },
            { name: 'Bamenda', lat: 5.9631, lon: 10.1591, address: 'Centre-ville, Bamenda' },
            { name: 'Ebolowa', lat: 2.9122, lon: 11.1511, address: 'Centre-ville, Ebolowa' },
            { name: 'Ngaoundéré (Adamoua)', lat: 7.32765, lon: 13.58359, address: 'Centre-ville, Ngaoundéré' }
        ];

        destinations.forEach(function(dest) {
            L.marker([dest.lat, dest.lon]).addTo(map)
                .bindPopup('<strong>' + dest.name + '</strong><br>' + dest.address)
                .on('mouseover', function(e) { this.openPopup(); })
                .on('mouseout', function(e) { this.closePopup(); });
        });
    </script>
</body>

<style>
    .sommet{
        
        width: 100%;
    }
    .entete{
        background-color: green !important ; 
    }
</style>

</html>
