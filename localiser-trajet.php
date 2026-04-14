<?php
session_start();

if (
    !isset($_SESSION['tracking_access']) ||
    $_SESSION['tracking_access'] !== true ||
    !isset($_SESSION['tracking_reservation'])
) {
    header('Location: Accueil.php');
    exit;
}

$reservation = $_SESSION['tracking_reservation'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localiser mon trajet</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8f8f8;
        }

        .topbar {
            padding: 18px 24px;
            background: #177043;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar h1 {
            margin: 0;
            font-size: 22px;
        }

        .topbar a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            background: rgba(255,255,255,0.12);
            padding: 10px 14px;
        }

        .page-content {
            max-width: 1300px;
            margin: 0 auto;
            padding: 24px;
        }

        .info-box {
            background: white;
            padding: 18px;
            margin-bottom: 18px;
            box-shadow: 0 8px 22px rgba(0,0,0,0.08);
        }

        .info-box h2 {
            margin-top: 0;
            color: #177043;
        }

        .stats {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .stat-card {
            background: #f3f7f4;
            padding: 14px 18px;
            min-width: 180px;
        }

        .stat-card strong {
            display: block;
            color: #177043;
            margin-bottom: 6px;
        }

        #map {
            width: 100%;
            height: 550px;
            box-shadow: 0 8px 22px rgba(0,0,0,0.08);
            background: #eaeaea;
        }
    </style>
</head>
<body>

<div class="topbar">
    <h1>Localiser mon trajet</h1>
    <a href="Accueil.php">← Retour à l’accueil</a>
</div>

<div class="page-content">
    <div class="info-box">
        <h2>Suivi du véhicule</h2>
        <p>Cette page affiche votre position et celle du véhicule.</p>
        <p>
    Réservation : <strong><?= htmlspecialchars($reservation['Numero_reservation']) ?></strong><br>
    Client : <strong><?= htmlspecialchars($reservation['prenom'] . ' ' . $reservation['nom']) ?></strong>
</p>

        <div class="stats">
            <div class="stat-card">
                <strong>Distance</strong>
                <span id="distanceValue">Calcul en attente...</span>
            </div>

            <div class="stat-card">
                <strong>Temps estimé</strong>
                <span id="durationValue">Calcul en attente...</span>
            </div>

            <div class="stat-card">
                <strong>Statut</strong>
                <span id="statusValue">Initialisation...</span>
            </div>
        </div>
    </div>

    <div id="map"></div>
</div>

<script>
    let map;
    let userMarker = null;
    let vehicleMarker = null;
    let routeLine = null;
    let bounds = null;
    let firstLoad = true;

    const distanceValue = document.getElementById("distanceValue");
    const durationValue = document.getElementById("durationValue");
    const statusValue = document.getElementById("statusValue");

    // Position véhicule simulée
    let vehiclePos = { lat: 43.6047, lng: 3.8796 };

    function formatDuration(durationStr) {
        if (!durationStr) return "Indisponible";

        const totalSeconds = parseInt(durationStr.replace("s", ""), 10);
        if (Number.isNaN(totalSeconds)) return durationStr;

        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.round((totalSeconds % 3600) / 60);

        if (hours > 0) {
            return `${hours} h ${minutes} min`;
        }

        return `${minutes} min`;
    }

    function formatDistance(distanceMeters) {
        if (distanceMeters === null || distanceMeters === undefined) {
            return "Indisponible";
        }

        if (distanceMeters < 1000) {
            return `${distanceMeters} m`;
        }

        return `${(distanceMeters / 1000).toFixed(1)} km`;
    }

    async function drawRealRoute(userPos, currentVehiclePos) {
        try {
            statusValue.textContent = "Calcul de l’itinéraire...";

            const url =
                `geolocalisation/get-route.php?originLat=${encodeURIComponent(userPos.lat)}` +
                `&originLng=${encodeURIComponent(userPos.lng)}` +
                `&destLat=${encodeURIComponent(currentVehiclePos.lat)}` +
                `&destLng=${encodeURIComponent(currentVehiclePos.lng)}`;

            const response = await fetch(url);
            const data = await response.json();

            if (!response.ok || !data.success || !data.encodedPolyline) {
                console.error("Erreur API route :", data);
                distanceValue.textContent = "Erreur";
                durationValue.textContent = "Erreur";
                statusValue.textContent = "Erreur de calcul";
                return;
            }

            const decodedPath = google.maps.geometry.encoding.decodePath(data.encodedPolyline);

            if (routeLine) {
                routeLine.setMap(null);
            }

            routeLine = new google.maps.Polyline({
                path: decodedPath,
                geodesic: true,
                strokeColor: "#d00000",
                strokeOpacity: 0.9,
                strokeWeight: 5,
                map: map
            });

            distanceValue.textContent = formatDistance(data.distanceMeters);
            durationValue.textContent = formatDuration(data.duration);
            statusValue.textContent = "Trajet calculé";

            bounds = new google.maps.LatLngBounds();
            decodedPath.forEach(point => bounds.extend(point));

            // Ajuster la vue uniquement au premier chargement
            if (firstLoad) {
                map.fitBounds(bounds);
                firstLoad = false;
            }

        } catch (error) {
            console.error(error);
            distanceValue.textContent = "Erreur";
            durationValue.textContent = "Erreur";
            statusValue.textContent = "Erreur de calcul";
        }
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 13,
            center: vehiclePos
        });

        vehicleMarker = new google.maps.Marker({
            position: vehiclePos,
            map: map,
            title: "Véhicule",
            icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
        });

        if (!navigator.geolocation) {
            statusValue.textContent = "Géolocalisation non supportée";
            return;
        }

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                const userPos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                userMarker = new google.maps.Marker({
                    position: userPos,
                    map: map,
                    title: "Moi",
                    icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
                });

                await drawRealRoute(userPos, vehiclePos);

                // Simulation légère du mouvement du véhicule
                setInterval(async () => {
                    vehiclePos = {
                        lat: vehiclePos.lat + 0.0010,
                        lng: vehiclePos.lng + 0.0006
                    };

                    vehicleMarker.setPosition(vehiclePos);
                    await drawRealRoute(userPos, vehiclePos);
                }, 6000);
            },
            (error) => {
                console.error(error);
                statusValue.textContent = "Géolocalisation refusée";
                alert("Autorise la géolocalisation pour afficher ta position.");
            }
        );
    }
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDGz1y0BX_Ca_YJitOavFu-8_K188MBYQ&callback=initMap&libraries=geometry">
</script>

</body>
</html>