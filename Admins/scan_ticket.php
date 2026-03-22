<?php
session_start();

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'bd_stock';

try {
    $bdd = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

$status = 'error';
$message = 'Ticket invalide.';
$reservation = null;

$token = trim($_GET['token'] ?? '');

if ($token !== '') {
    $sql = "
        SELECT 
            r.*,
            v.villeDepart,
            v.villeArrivee,
            v.jourDepart,
            v.heureDepart
        FROM reservation r
        INNER JOIN voyage v ON r.idVoyage = v.idVoyage
        WHERE r.qr_token = :token
        LIMIT 1
    ";

    $stmt = $bdd->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();

    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($reservation) {
        if ($reservation['ticket_status'] === 'cancelled') {
            $status = 'cancelled';
            $message = 'Billet annulé. Accès refusé.';
        } elseif ($reservation['ticket_status'] === 'used') {
            $status = 'used';
            $message = 'Ce billet a déjà été scanné.';
        } else {
            $update = $bdd->prepare("
                UPDATE reservation
                SET ticket_status = 'used', scanned_at = NOW()
                WHERE id_reservation = :id
            ");
            $update->bindValue(':id', $reservation['id_reservation'], PDO::PARAM_INT);
            $update->execute();

            $reservation['ticket_status'] = 'used';
            $reservation['scanned_at'] = date('Y-m-d H:i:s');

            $status = 'success';
            $message = 'Billet valide. Passager autorisé à embarquer.';
        }
    } else {
        $status = 'error';
        $message = 'Billet introuvable.';
    }
}

$bgColor = 'bg-red-600';
if ($status === 'success') $bgColor = 'bg-green-600';
if ($status === 'used') $bgColor = 'bg-yellow-500';
if ($status === 'cancelled') $bgColor = 'bg-gray-700';

ob_start();
?>

<div class="max-w-5xl mx-auto">
    <div class="<?= $bgColor ?> text-white rounded-2xl shadow-xl p-6 mb-6">
        <h1 class="text-3xl font-bold mb-2">Contrôle du billet</h1>
        <p class="text-lg"><?= htmlspecialchars($message) ?></p>
    </div>

    <div class="flex flex-wrap gap-4 mb-6">
        <a href="scanner.php" class="bg-blue-600 text-white px-5 py-3 rounded-lg hover:bg-blue-700">
            Scanner un autre ticket
        </a>

        <?php if ($reservation): ?>
            <a href="voyage_reservations.php?idVoyage=<?= (int)$reservation['idVoyage'] ?>" class="bg-green-600 text-white px-5 py-3 rounded-lg hover:bg-green-700">
                Voir la liste du voyage N° <?= htmlspecialchars($reservation['idVoyage']) ?>
            </a>
        <?php endif; ?>
    </div>

    <?php if ($reservation): ?>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-blue-600">
                <p class="text-sm text-gray-500">N° Voyage</p>
                <p class="text-2xl font-bold"><?= htmlspecialchars($reservation['idVoyage']) ?></p>
            </div>

            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-green-600">
                <p class="text-sm text-gray-500">Siège</p>
                <p class="text-2xl font-bold"><?= htmlspecialchars($reservation['Numero_siege']) ?></p>
            </div>

            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-purple-600">
                <p class="text-sm text-gray-500">N° réservation</p>
                <p class="text-xl font-bold"><?= htmlspecialchars($reservation['Numero_reservation']) ?></p>
            </div>

            <div class="bg-white rounded-xl shadow p-4 border-l-4 border-yellow-500">
                <p class="text-sm text-gray-500">Scanné le</p>
                <p class="text-base font-bold">
                    <?= !empty($reservation['scanned_at']) ? htmlspecialchars($reservation['scanned_at']) : 'Pas encore' ?>
                </p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Informations du passager</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-gray-700">
                <div>
                    <p><span class="font-semibold">Nom :</span> <?= htmlspecialchars($reservation['nom']) ?></p>
                    <p><span class="font-semibold">Prénom :</span> <?= htmlspecialchars($reservation['prenom']) ?></p>
                    <p><span class="font-semibold">Téléphone :</span> <?= htmlspecialchars($reservation['telephone']) ?></p>
                    <p><span class="font-semibold">Email :</span> <?= htmlspecialchars($reservation['email']) ?></p>
                </div>

                <div>
                    <p><span class="font-semibold">Trajet :</span> <?= htmlspecialchars($reservation['villeDepart']) ?> → <?= htmlspecialchars($reservation['villeArrivee']) ?></p>
                    <p><span class="font-semibold">Date :</span> <?= htmlspecialchars($reservation['jourDepart']) ?></p>
                    <p><span class="font-semibold">Heure :</span> <?= htmlspecialchars($reservation['heureDepart']) ?></p>
                    <p><span class="font-semibold">Statut ticket :</span> <?= htmlspecialchars($reservation['ticket_status']) ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$adminContent = ob_get_clean();
$adminTitle = 'Contrôle billet';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Scan et vérification des tickets';
$baseUrl = './';

include __DIR__ . '/../includes/layoutadmin.php';
?>