<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('Échec de connexion : ' . $e->getMessage());
}

/* =========================
   RECUPERATION DES DESTINATIONS
========================= */
$stmtDestinations = $bdd->query("SELECT * FROM destination ORDER BY Nom_ville ASC");
$destinations = $stmtDestinations->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   INSERTION D'UN VOYAGE
========================= */
$messageSuccess = '';
$messageError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['depart'], $_POST['arrivee'], $_POST['selectBus'], $_POST['partir'], $_POST['destination'], $_POST['date'], $_POST['prix'])) {
    $depart = trim($_POST['depart']);
    $arrivee = trim($_POST['arrivee']);
    $bus = trim($_POST['selectBus']);
    $heureDepart = trim($_POST['partir']);
    $heureArrivee = trim($_POST['destination']);
    $date = trim($_POST['date']);
    $prix = trim($_POST['prix']);

    if (
        !empty($depart) &&
        !empty($arrivee) &&
        !empty($bus) &&
        !empty($heureDepart) &&
        !empty($heureArrivee) &&
        !empty($date) &&
        !empty($prix)
    ) {
        try {
            $requete = $bdd->prepare("
                INSERT INTO voyage (villeDepart, villeArrivee, typeBus, prix, heureDepart, heureArrivee, jourDepart)
                VALUES (:depart, :arrivee, :bus, :prix, :heureDepart, :heureArrivee, :jourDepart)
            ");

            $requete->execute([
                ':depart' => $depart,
                ':arrivee' => $arrivee,
                ':bus' => $bus,
                ':prix' => $prix,
                ':heureDepart' => $heureDepart,
                ':heureArrivee' => $heureArrivee,
                ':jourDepart' => $date
            ]);

            $messageSuccess = 'Voyage inséré avec succès.';
        } catch (Exception $e) {
            $messageError = "Erreur lors de l'insertion : " . $e->getMessage();
        }
    } else {
        $messageError = 'Veuillez remplir tous les champs.';
    }
}

/* =========================
   CONTENU DE LA PAGE
========================= */
ob_start();
?>

<div class="max-w-5xl mx-auto">
    <?php if (!empty($messageSuccess)): ?>
        <div class="mb-6 bg-green-500 text-white text-center p-3 rounded-lg shadow">
            <?= htmlspecialchars($messageSuccess) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($messageError)): ?>
        <div class="mb-6 bg-red-500 text-white text-center p-3 rounded-lg shadow">
            <?= htmlspecialchars($messageError) ?>
        </div>
    <?php endif; ?>

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Veuillez insérer un trajet de voyage</h2>

        <form action="" method="POST" class="space-y-6">
            <!-- Ligne Départ et Arrivée -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Départ</label>
                    <select name="depart" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">-- Choisir une ville --</option>
                        <?php foreach ($destinations as $ville): ?>
                            <option value="<?= htmlspecialchars($ville['Nom_ville']) ?>" <?= (isset($_POST['depart']) && $_POST['depart'] === $ville['Nom_ville']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ville['Nom_ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Arrivée</label>
                    <select name="arrivee" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">-- Choisir une ville --</option>
                        <?php foreach ($destinations as $ville): ?>
                            <option value="<?= htmlspecialchars($ville['Nom_ville']) ?>" <?= (isset($_POST['arrivee']) && $_POST['arrivee'] === $ville['Nom_ville']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ville['Nom_ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Type de bus -->
            <div>
                <label class="block text-gray-600 font-semibold mb-2">Type de bus</label>
                <select name="selectBus" class="w-full border rounded-lg px-3 py-2" required>
                    <option value="classique" <?= (isset($_POST['selectBus']) && $_POST['selectBus'] === 'classique') ? 'selected' : '' ?>>Bus classique</option>
                    <option value="VIP" <?= (isset($_POST['selectBus']) && $_POST['selectBus'] === 'VIP') ? 'selected' : '' ?>>Bus VIP</option>
                </select>
            </div>

            <!-- Ligne Heure Départ et Heure Arrivée -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Heure de départ</label>
                    <input type="time" name="partir" value="<?= htmlspecialchars($_POST['partir'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Heure d'arrivée</label>
                    <input type="time" name="destination" value="<?= htmlspecialchars($_POST['destination'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>

            <!-- Ligne Date Départ et Prix -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Jour de départ</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Prix</label>
                    <input type="number" name="prix" value="<?= htmlspecialchars($_POST['prix'] ?? '') ?>" placeholder="Ex: 10000" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex justify-center space-x-4">
                <button type="reset" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600">
                    Annuler
                </button>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                    Insérer
                </button>
            </div>
        </form>
    </div>

    <div class="mt-8 flex justify-center">
        <a href="listevoyadmin.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 shadow">
            Consulter la liste des voyages
        </a>
    </div>
</div>

<?php
$adminContent = ob_get_clean();

$adminTitle = 'Insertion voyage';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Bienvenu dans votre espace Administrateur ! ! !';
$baseUrl = '';

include __DIR__ . '/../includes/layoutadmin.php';
?>