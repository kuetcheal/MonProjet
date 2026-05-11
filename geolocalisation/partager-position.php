<?php
session_start();

require_once __DIR__ . '/../config.php';

$chauffeurId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($chauffeurId <= 0) {
    header('Location: ../connexion.php');
    exit;
}

$idVoyage = isset($_GET['idVoyage']) ? (int)$_GET['idVoyage'] : 0;

if ($idVoyage <= 0) {
    die('Trajet invalide.');
}

try {
    $stmt = $pdo->prepare("
        SELECT 
            idVoyage,
            villeDepart,
            quartierDepart,
            villeArrivee,
            quartierArrivee,
            jourDepart,
            heureDepart,
            chauffeur_id,
            modeTransport,
            statut_trajet
        FROM voyage
        WHERE idVoyage = :idVoyage
        AND chauffeur_id = :chauffeurId
        LIMIT 1
    ");

    $stmt->execute([
        ':idVoyage' => $idVoyage,
        ':chauffeurId' => $chauffeurId
    ]);

    $voyage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voyage) {
        die('Trajet introuvable ou accès refusé.');
    }

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

ob_start();
?>

<div class="max-w-3xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">

        <h1 class="text-3xl font-extrabold text-slate-800 mb-3">
            Partager ma position
        </h1>

        <p class="text-gray-500 mb-6">
            Gardez cette page ouverte pendant le trajet pour permettre au client de suivre votre position.
        </p>

        <div class="border rounded-2xl p-5 mb-6 bg-gray-50">
            <p class="font-bold text-slate-800">
                <?= htmlspecialchars($voyage['villeDepart']) ?>
                →
                <?= htmlspecialchars($voyage['villeArrivee']) ?>
            </p>

            <p class="text-gray-500 mt-1">
                <?= htmlspecialchars($voyage['jourDepart']) ?>
                à
                <?= htmlspecialchars(substr($voyage['heureDepart'], 0, 5)) ?>
            </p>

            <?php if (!empty($voyage['quartierDepart']) || !empty($voyage['quartierArrivee'])): ?>
                <p class="text-gray-500 mt-1">
                    <?= htmlspecialchars($voyage['quartierDepart'] ?? '') ?>
                    →
                    <?= htmlspecialchars($voyage['quartierArrivee'] ?? '') ?>
                </p>
            <?php endif; ?>
        </div>

        <div id="statusBox" class="bg-yellow-50 border border-yellow-200 text-yellow-700 p-4 rounded-xl mb-6 font-semibold">
            Position non partagée.
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <button
                type="button"
                id="startSharing"
                class="bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition">
                Démarrer le partage GPS
            </button>

            <button
                type="button"
                id="stopSharing"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-4 rounded-xl transition">
                Arrêter le partage
            </button>
        </div>

        <div class="mt-6 text-sm text-gray-500">
            <p>
                Important : autorisez la géolocalisation quand le navigateur vous le demande.
            </p>
            <p>
                Sur téléphone, évitez de fermer cette page pendant le trajet.
            </p>
        </div>

    </div>
</div>

<script>
const idVoyage = <?= (int)$idVoyage ?>;
let watchId = null;
let lastSentAt = 0;

const statusBox = document.getElementById('statusBox');
const startButton = document.getElementById('startSharing');
const stopButton = document.getElementById('stopSharing');

function setStatus(message, type = 'info') {
    statusBox.textContent = message;

    statusBox.className = 'p-4 rounded-xl mb-6 font-semibold border';

    if (type === 'success') {
        statusBox.classList.add('bg-green-50', 'border-green-200', 'text-green-700');
    } else if (type === 'error') {
        statusBox.classList.add('bg-red-50', 'border-red-200', 'text-red-700');
    } else {
        statusBox.classList.add('bg-yellow-50', 'border-yellow-200', 'text-yellow-700');
    }
}

async function sendPosition(position) {
    const now = Date.now();

    if (now - lastSentAt < 5000) {
        return;
    }

    lastSentAt = now;

    const payload = {
        idVoyage: idVoyage,
        latitude: position.coords.latitude,
        longitude: position.coords.longitude,
        accuracy: position.coords.accuracy,
        speed: position.coords.speed,
        heading: position.coords.heading
    };

    try {
        const response = await fetch('../geolocalisation/update-position.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            setStatus(
                'Position partagée avec succès. Dernière mise à jour : ' + new Date().toLocaleTimeString(),
                'success'
            );
        } else {
            setStatus(result.message || 'Impossible d’envoyer la position.', 'error');
        }

    } catch (error) {
        setStatus('Erreur réseau : ' + error.message, 'error');
    }
}

startButton.addEventListener('click', () => {
    if (!navigator.geolocation) {
        setStatus('La géolocalisation n’est pas supportée par ce navigateur.', 'error');
        return;
    }

    if (watchId !== null) {
        setStatus('Le partage GPS est déjà actif.', 'success');
        return;
    }

    setStatus('Demande d’autorisation GPS en cours...', 'info');

    watchId = navigator.geolocation.watchPosition(
        sendPosition,
        (error) => {
            let message = 'Erreur de géolocalisation.';

            if (error.code === error.PERMISSION_DENIED) {
                message = 'Vous avez refusé la géolocalisation.';
            } else if (error.code === error.POSITION_UNAVAILABLE) {
                message = 'Position indisponible.';
            } else if (error.code === error.TIMEOUT) {
                message = 'Délai dépassé pour obtenir la position.';
            }

            setStatus(message, 'error');
        },
        {
            enableHighAccuracy: true,
            maximumAge: 5000,
            timeout: 15000
        }
    );
});

stopButton.addEventListener('click', () => {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        setStatus('Partage GPS arrêté.', 'info');
    } else {
        setStatus('Le partage GPS n’est pas actif.', 'info');
    }
});
</script>

<?php
$content = ob_get_clean();
$title = "Partager ma position";
include __DIR__ . '/../layouts/default.php';
?>