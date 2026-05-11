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

$numeroReservation = $reservation['Numero_reservation'] ?? '';
$prenom = $reservation['prenom'] ?? '';
$nom = $reservation['nom'] ?? '';
$idVoyage = (int)($reservation['idVoyage'] ?? 0);
$typeReservation = $reservation['type_reservation'] ?? 'bus';
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
            gap: 16px;
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
            border-radius: 8px;
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
            border-radius: 12px;
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
            border-radius: 10px;
        }

        .stat-card strong {
            display: block;
            color: #177043;
            margin-bottom: 6px;
        }

        .notice {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #9a3412;
            font-weight: 600;
            display: none;
        }

        #map {
            width: 100%;
            height: 550px;
            box-shadow: 0 8px 22px rgba(0,0,0,0.08);
            background: #eaeaea;
            border-radius: 12px;
        }

        @media (max-width: 700px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            #map {
                height: 480px;
            }
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

        <p>
            Cette page affiche la position réelle du chauffeur lorsque celui-ci partage sa position GPS.
        </p>

        <p>
            Réservation :
            <strong><?= htmlspecialchars($numeroReservation) ?></strong><br>

            Client :
            <strong><?= htmlspecialchars(trim($prenom . ' ' . $nom)) ?></strong><br>

            Type :
            <strong><?= htmlspecialchars($typeReservation === 'covoiturage' ? 'Covoiturage' : 'Bus') ?></strong>
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
                <strong>Dernière position</strong>
                <span id="lastUpdateValue">En attente...</span>
            </div>

            <div class="stat-card">
                <strong>Statut</strong>
                <span id="statusValue">Initialisation...</span>
            </div>
        </div>

        <div id="noticeBox" class="notice"></div>
    </div>

    <div id="map"></div>
</div>

<script>
    let map;
    let userMarker = null;
    let vehicleMarker = null;
    let routeLine = null;
    let vehicleInfoWindow = null;

    let userPos = null;
    let currentVehiclePos = null;
    let firstMapFit = true;
    let refreshTimer = null;

    const distanceValue = document.getElementById("distanceValue");
    const durationValue = document.getElementById("durationValue");
    const lastUpdateValue = document.getElementById("lastUpdateValue");
    const statusValue = document.getElementById("statusValue");
    const noticeBox = document.getElementById("noticeBox");

    const defaultCenter = {
        lat: 4.0511,
        lng: 9.7679
    };

    function showNotice(message) {
        noticeBox.textContent = message;
        noticeBox.style.display = "block";
    }

    function hideNotice() {
        noticeBox.textContent = "";
        noticeBox.style.display = "none";
    }

    function formatDuration(durationStr) {
        if (!durationStr) return "Indisponible";

        const totalSeconds = parseInt(String(durationStr).replace("s", ""), 10);
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

        const meters = Number(distanceMeters);

        if (Number.isNaN(meters)) {
            return "Indisponible";
        }

        if (meters < 1000) {
            return `${Math.round(meters)} m`;
        }

        return `${(meters / 1000).toFixed(1)} km`;
    }

    function formatSpeed(speed) {
        if (speed === null || speed === undefined || Number.isNaN(Number(speed))) {
            return null;
        }

        /*
            navigator.geolocation retourne souvent la vitesse en m/s.
            Conversion en km/h.
        */
        return Math.round(Number(speed) * 3.6);
    }

    function formatDateTime(dateString) {
        if (!dateString) return "Indisponible";

        const date = new Date(dateString.replace(" ", "T"));

        if (Number.isNaN(date.getTime())) {
            return dateString;
        }

        return date.toLocaleString("fr-FR", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
            hour: "2-digit",
            minute: "2-digit",
            second: "2-digit"
        });
    }

    function updateVehicleMarker(position, data) {
        if (!vehicleMarker) {
            vehicleMarker = new google.maps.Marker({
                position: position,
                map: map,
                title: "Position du chauffeur",
                icon: "http://maps.google.com/mapfiles/ms/icons/green-dot.png"
            });

            vehicleInfoWindow = new google.maps.InfoWindow();
        } else {
            vehicleMarker.setPosition(position);
        }

        const speedKmH = formatSpeed(data.vitesse);
        const speedText = speedKmH !== null ? `${speedKmH} km/h` : "Indisponible";

        vehicleInfoWindow.setContent(`
            <div style="font-family: Arial, sans-serif; min-width: 180px;">
                <strong>Chauffeur</strong><br>
                Dernière position : ${formatDateTime(data.updated_at)}<br>
                Vitesse : ${speedText}
            </div>
        `);

        vehicleMarker.addListener("click", () => {
            vehicleInfoWindow.open(map, vehicleMarker);
        });
    }

    function updateUserMarker(position) {
        if (!userMarker) {
            userMarker = new google.maps.Marker({
                position: position,
                map: map,
                title: "Ma position",
                icon: "http://maps.google.com/mapfiles/ms/icons/blue-dot.png"
            });
        } else {
            userMarker.setPosition(position);
        }
    }

    function fitMapIfNeeded() {
        if (!currentVehiclePos) {
            return;
        }

        const bounds = new google.maps.LatLngBounds();
        bounds.extend(currentVehiclePos);

        if (userPos) {
            bounds.extend(userPos);
        }

        if (firstMapFit) {
            map.fitBounds(bounds);
            firstMapFit = false;
        }
    }

    async function drawRealRoute() {
        if (!userPos || !currentVehiclePos) {
            distanceValue.textContent = "Position client requise";
            durationValue.textContent = "Position client requise";
            return;
        }

        try {
            statusValue.textContent = "Calcul de l’itinéraire...";

            const url =
                `geolocalisation/get-route.php?originLat=${encodeURIComponent(userPos.lat)}` +
                `&originLng=${encodeURIComponent(userPos.lng)}` +
                `&destLat=${encodeURIComponent(currentVehiclePos.lat)}` +
                `&destLng=${encodeURIComponent(currentVehiclePos.lng)}`;

            const response = await fetch(url, {
                cache: "no-store"
            });

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
            statusValue.textContent = "Suivi actif";

        } catch (error) {
            console.error(error);
            distanceValue.textContent = "Erreur";
            durationValue.textContent = "Erreur";
            statusValue.textContent = "Erreur de calcul";
        }
    }

    async function fetchDriverPosition() {
        try {
            const response = await fetch("geolocalisation/get-driver-position.php", {
                cache: "no-store"
            });

            const data = await response.json();

            if (!data.success) {
                statusValue.textContent = "En attente";
                lastUpdateValue.textContent = "Indisponible";

                showNotice(data.message || "Le chauffeur n’a pas encore partagé sa position.");
                return;
            }

            hideNotice();

            currentVehiclePos = {
                lat: parseFloat(data.latitude),
                lng: parseFloat(data.longitude)
            };

            if (
                Number.isNaN(currentVehiclePos.lat) ||
                Number.isNaN(currentVehiclePos.lng)
            ) {
                statusValue.textContent = "Position invalide";
                showNotice("La position reçue est invalide.");
                return;
            }

            updateVehicleMarker(currentVehiclePos, data);

            lastUpdateValue.textContent = formatDateTime(data.updated_at);

            if (data.is_fresh === false) {
                statusValue.textContent = "Position ancienne";
                showNotice("La dernière position du chauffeur date de plus de 2 minutes.");
            } else {
                statusValue.textContent = "Position reçue";
            }

            fitMapIfNeeded();

            await drawRealRoute();

        } catch (error) {
            console.error(error);
            statusValue.textContent = "Erreur réseau";
            showNotice("Impossible de récupérer la position du chauffeur.");
        }
    }

    function initClientGeolocation() {
        if (!navigator.geolocation) {
            statusValue.textContent = "Géolocalisation client non supportée";
            showNotice("Votre navigateur ne supporte pas la géolocalisation. Vous verrez quand même la position du chauffeur.");
            return;
        }

        navigator.geolocation.getCurrentPosition(
            async (position) => {
                userPos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                updateUserMarker(userPos);

                if (currentVehiclePos) {
                    await drawRealRoute();
                    fitMapIfNeeded();
                }
            },
            (error) => {
                console.error(error);
                distanceValue.textContent = "Position client refusée";
                durationValue.textContent = "Position client refusée";
                showNotice("Vous avez refusé votre géolocalisation. La carte affichera seulement la position du chauffeur.");
            },
            {
                enableHighAccuracy: true,
                timeout: 12000,
                maximumAge: 30000
            }
        );
    }

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            zoom: 13,
            center: defaultCenter
        });

        statusValue.textContent = "Recherche du chauffeur...";

        initClientGeolocation();

        fetchDriverPosition();

        refreshTimer = setInterval(fetchDriverPosition, 5000);
    }

    window.addEventListener("beforeunload", () => {
        if (refreshTimer) {
            clearInterval(refreshTimer);
        }
    });
</script>

<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBDGz1y0BX_Ca_YJitOavFu-8_K188MBYQ&callback=initMap&libraries=geometry">
</script>

</body>
</html>