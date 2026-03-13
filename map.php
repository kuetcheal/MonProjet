<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<div class="gv-map-component">
    <div class="gv-map-layout">
        <div class="gv-map-sidebar">
            <h3 class="gv-map-title">10 destinations de voyage au Cameroun</h3>

            <ul class="gv-map-destination-list">
                <li class="gv-map-destination-item" data-lat="4.0511" data-lng="9.7679" data-name="Douala" data-address="Bonanjo, Douala">
                    <strong>Douala</strong>
                    <span>Adresse : Bonanjo, Douala</span>
                </li>

                <li class="gv-map-destination-item" data-lat="3.8480" data-lng="11.5021" data-name="Yaoundé-Mvan" data-address="Mvan, Yaoundé">
                    <strong>Yaoundé-Mvan</strong>
                    <span>Adresse : Mvan, Yaoundé</span>
                </li>

                <li class="gv-map-destination-item" data-lat="3.80816" data-lng="10.13257" data-name="Édéa" data-address="Centre-ville, Édéa">
                    <strong>Édéa</strong>
                    <span>Adresse : Centre-ville, Édéa</span>
                </li>

                <li class="gv-map-destination-item" data-lat="5.47775" data-lng="10.41759" data-name="Bafoussam" data-address="Centre-ville, Bafoussam">
                    <strong>Bafoussam</strong>
                    <span>Adresse : Centre-ville, Bafoussam</span>
                </li>

                <li class="gv-map-destination-item" data-lat="2.93718" data-lng="9.90793" data-name="Kribi" data-address="Centre-ville, Kribi">
                    <strong>Kribi</strong>
                    <span>Adresse : Centre-ville, Kribi</span>
                </li>

                <li class="gv-map-destination-item" data-lat="5.62825" data-lng="10.25439" data-name="Mbouda" data-address="Centre-ville, Mbouda">
                    <strong>Mbouda</strong>
                    <span>Adresse : Centre-ville, Mbouda</span>
                </li>

                <li class="gv-map-destination-item" data-lat="9.3274" data-lng="13.3931" data-name="Garoua" data-address="Centre-ville, Garoua">
                    <strong>Garoua</strong>
                    <span>Adresse : Centre-ville, Garoua</span>
                </li>

                <li class="gv-map-destination-item" data-lat="4.02429" data-lng="9.21492" data-name="Limbe" data-address="Centre-ville, Limbe">
                    <strong>Limbe</strong>
                    <span>Adresse : Centre-ville, Limbe</span>
                </li>

                <li class="gv-map-destination-item" data-lat="10.5956" data-lng="14.3247" data-name="Maroua" data-address="Centre-ville, Maroua">
                    <strong>Maroua</strong>
                    <span>Adresse : Centre-ville, Maroua</span>
                </li>

                <li class="gv-map-destination-item" data-lat="5.9631" data-lng="10.1591" data-name="Bamenda" data-address="Centre-ville, Bamenda">
                    <strong>Bamenda</strong>
                    <span>Adresse : Centre-ville, Bamenda</span>
                </li>
            </ul>
        </div>

        <div class="gv-map-canvas-wrap">
            <div id="gv-leaflet-map"></div>
        </div>
    </div>
</div>

<style>
    .gv-map-component {
        width: 100%;
        height: 100%;
    }

    .gv-map-layout {
        display: grid;
        grid-template-columns: 420px 1fr;
        min-height: 760px;
        width: 100%;
        background: #fff;
    }

    .gv-map-sidebar {
        padding: 34px 28px;
        border-right: 1px solid #ececec;
        overflow-y: auto;
    }

    .gv-map-title {
        margin: 0 0 24px;
        color: #177043;
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.25;
    }

    .gv-map-destination-list {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .gv-map-destination-item {
        padding: 14px 16px;
        border-radius: 14px;
        background: #f8faf8;
        border: 1px solid #e6efe7;
        cursor: pointer;
        transition: all 0.22s ease;
    }

    .gv-map-destination-item:hover,
    .gv-map-destination-item.is-active {
        background: #eaf7ed;
        border-color: #b8dfbf;
        transform: translateY(-1px);
    }

    .gv-map-destination-item strong {
        display: block;
        color: #177043;
        font-size: 1.15rem;
        margin-bottom: 6px;
    }

    .gv-map-destination-item span {
        color: #444;
        font-size: 0.98rem;
        line-height: 1.5;
        display: block;
    }

    .gv-map-canvas-wrap {
        width: 100%;
        height: 100%;
        min-height: 760px;
    }

    #gv-leaflet-map {
        width: 100%;
        height: 100%;
        min-height: 760px;
    }

    @media (max-width: 991px) {
        .gv-map-layout {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .gv-map-sidebar {
            border-right: 0;
            border-bottom: 1px solid #ececec;
            padding: 24px 20px;
        }

        .gv-map-title {
            font-size: 1.6rem;
        }

        .gv-map-canvas-wrap,
        #gv-leaflet-map {
            min-height: 560px;
        }
    }

    @media (max-width: 767px) {
        .gv-map-sidebar {
            padding: 18px 16px;
        }

        .gv-map-title {
            font-size: 1.35rem;
        }

        .gv-map-destination-item strong {
            font-size: 1rem;
        }

        .gv-map-destination-item span {
            font-size: 0.92rem;
        }

        .gv-map-canvas-wrap,
        #gv-leaflet-map {
            min-height: 420px;
        }
    }
</style>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const mapElement = document.getElementById("gv-leaflet-map");
        if (!mapElement || mapElement.dataset.initialized === "true") return;

        mapElement.dataset.initialized = "true";

        const map = L.map("gv-leaflet-map").setView([5.7, 11.5], 6);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const destinations = [
            { name: "Douala", lat: 4.0511, lng: 9.7679, address: "Bonanjo, Douala" },
            { name: "Yaoundé-Mvan", lat: 3.8480, lng: 11.5021, address: "Mvan, Yaoundé" },
            { name: "Édéa", lat: 3.80816, lng: 10.13257, address: "Centre-ville, Édéa" },
            { name: "Bafoussam", lat: 5.47775, lng: 10.41759, address: "Centre-ville, Bafoussam" },
            { name: "Kribi", lat: 2.93718, lng: 9.90793, address: "Centre-ville, Kribi" },
            { name: "Mbouda", lat: 5.62825, lng: 10.25439, address: "Centre-ville, Mbouda" },
            { name: "Garoua", lat: 9.3274, lng: 13.3931, address: "Centre-ville, Garoua" },
            { name: "Limbe", lat: 4.02429, lng: 9.21492, address: "Centre-ville, Limbe" },
            { name: "Maroua", lat: 10.5956, lng: 14.3247, address: "Centre-ville, Maroua" },
            { name: "Bamenda", lat: 5.9631, lng: 10.1591, address: "Centre-ville, Bamenda" }
        ];

        const bounds = [];
        const markers = {};

        destinations.forEach(dest => {
            const marker = L.marker([dest.lat, dest.lng]).addTo(map)
                .bindPopup(`<strong>${dest.name}</strong><br>${dest.address}`);

            markers[dest.name] = marker;
            bounds.push([dest.lat, dest.lng]);
        });

        if (bounds.length) {
            map.fitBounds(bounds, { padding: [40, 40] });
        }

        const items = document.querySelectorAll(".gv-map-destination-item");

        items.forEach(item => {
            item.addEventListener("click", function () {
                items.forEach(el => el.classList.remove("is-active"));
                this.classList.add("is-active");

                const lat = parseFloat(this.dataset.lat);
                const lng = parseFloat(this.dataset.lng);
                const name = this.dataset.name;

                map.flyTo([lat, lng], 9, {
                    animate: true,
                    duration: 1.2
                });

                if (markers[name]) {
                    markers[name].openPopup();
                }
            });
        });

        setTimeout(() => {
            map.invalidateSize();
        }, 250);

        window.addEventListener("resize", function () {
            map.invalidateSize();
        });
    });
</script>