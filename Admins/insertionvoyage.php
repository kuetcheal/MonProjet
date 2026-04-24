<?php
session_start();

require_once __DIR__ . '/../config.php';

$stmtDestinations = $pdo->query("SELECT * FROM destination ORDER BY Nom_ville ASC");
$destinations = $stmtDestinations->fetchAll(PDO::FETCH_ASSOC);

$stmtQuartiers = $pdo->query("
    SELECT q.id_quartier, q.nom_quartier, q.id_destination, d.Nom_ville
    FROM quartier q
    INNER JOIN destination d ON q.id_destination = d.id_destination
    ORDER BY d.Nom_ville ASC, q.nom_quartier ASC
");
$quartiers = $stmtQuartiers->fetchAll(PDO::FETCH_ASSOC);

$messageSuccess = '';
$messageError = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset(
        $_POST['depart'],
        $_POST['quartier_depart'],
        $_POST['arrivee'],
        $_POST['quartier_arrivee'],
        $_POST['selectBus'],
        $_POST['nombrePlaces'],
        $_POST['partir'],
        $_POST['destination'],
        $_POST['date'],
        $_POST['prix']
    )
) {
    $depart = trim($_POST['depart']);
    $quartierDepart = trim($_POST['quartier_depart']);
    $arrivee = trim($_POST['arrivee']);
    $quartierArrivee = trim($_POST['quartier_arrivee']);
    $bus = trim($_POST['selectBus']);
    $nombrePlaces = (int)($_POST['nombrePlaces'] ?? 0);
    $heureDepart = trim($_POST['partir']);
    $heureArrivee = trim($_POST['destination']);
    $date = trim($_POST['date']);
    $prix = trim($_POST['prix']);

    if (
        !empty($depart) &&
        !empty($quartierDepart) &&
        !empty($arrivee) &&
        !empty($quartierArrivee) &&
        !empty($bus) &&
        $nombrePlaces > 0 &&
        !empty($heureDepart) &&
        !empty($heureArrivee) &&
        !empty($date) &&
        !empty($prix)
    ) {
        if ($depart === $arrivee && $quartierDepart === $quartierArrivee) {
            $messageError = 'Le départ et l’arrivée ne peuvent pas être identiques.';
        } else {
            try {
                $requete = $pdo->prepare("
                    INSERT INTO voyage (
                        villeDepart,
                        quartierDepart,
                        villeArrivee,
                        quartierArrivee,
                        typeBus,
                        nombrePlaces,
                        prix,
                        heureDepart,
                        heureArrivee,
                        jourDepart
                    )
                    VALUES (
                        :depart,
                        :quartierDepart,
                        :arrivee,
                        :quartierArrivee,
                        :bus,
                        :nombrePlaces,
                        :prix,
                        :heureDepart,
                        :heureArrivee,
                        :jourDepart
                    )
                ");

                $requete->execute([
                    ':depart' => $depart,
                    ':quartierDepart' => $quartierDepart,
                    ':arrivee' => $arrivee,
                    ':quartierArrivee' => $quartierArrivee,
                    ':bus' => $bus,
                    ':nombrePlaces' => $nombrePlaces,
                    ':prix' => $prix,
                    ':heureDepart' => $heureDepart,
                    ':heureArrivee' => $heureArrivee,
                    ':jourDepart' => $date
                ]);

                $messageSuccess = 'Voyage inséré avec succès.';
                $_POST = [];
            } catch (Exception $e) {
                $messageError = "Erreur lors de l'insertion : " . $e->getMessage();
            }
        }
    } else {
        $messageError = 'Veuillez remplir tous les champs.';
    }
}

$quartiersParVille = [];
foreach ($quartiers as $quartier) {
    $villeId = $quartier['id_destination'];

    if (!isset($quartiersParVille[$villeId])) {
        $quartiersParVille[$villeId] = [];
    }

    $quartiersParVille[$villeId][] = [
        'id_quartier' => $quartier['id_quartier'],
        'nom_quartier' => $quartier['nom_quartier']
    ];
}

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

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Veuillez insérer un trajet de voyage</h2>

        <form action="" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Ville de départ</label>
                    <select id="depart" name="depart" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">-- Choisir une ville --</option>
                        <?php foreach ($destinations as $ville): ?>
                            <option
                                value="<?= htmlspecialchars($ville['Nom_ville']) ?>"
                                data-id="<?= (int)$ville['id_destination'] ?>"
                                <?= (isset($_POST['depart']) && $_POST['depart'] === $ville['Nom_ville']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($ville['Nom_ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Ville d'arrivée</label>
                    <select id="arrivee" name="arrivee" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">-- Choisir une ville --</option>
                        <?php foreach ($destinations as $ville): ?>
                            <option
                                value="<?= htmlspecialchars($ville['Nom_ville']) ?>"
                                data-id="<?= (int)$ville['id_destination'] ?>"
                                <?= (isset($_POST['arrivee']) && $_POST['arrivee'] === $ville['Nom_ville']) ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($ville['Nom_ville']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Quartier de départ</label>
                    <select id="quartier_depart" name="quartier_depart" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">-- Choisir d'abord une ville --</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Quartier d'arrivée</label>
                    <select id="quartier_arrivee" name="quartier_arrivee" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">-- Choisir d'abord une ville --</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Type de bus</label>
                    <select name="selectBus" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="classique" <?= (isset($_POST['selectBus']) && $_POST['selectBus'] === 'classique') ? 'selected' : '' ?>>Bus classique</option>
                        <option value="VIP" <?= (isset($_POST['selectBus']) && $_POST['selectBus'] === 'VIP') ? 'selected' : '' ?>>Bus VIP</option>
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600 font-semibold mb-2">Nombre de places</label>
                    <input type="number" name="nombrePlaces" min="1" value="<?= htmlspecialchars($_POST['nombrePlaces'] ?? '') ?>" placeholder="Ex: 70" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>

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

<script>
    const quartiersParVille = <?= json_encode($quartiersParVille, JSON_UNESCAPED_UNICODE) ?>;
    const selectedQuartierDepart = <?= json_encode($_POST['quartier_depart'] ?? '') ?>;
    const selectedQuartierArrivee = <?= json_encode($_POST['quartier_arrivee'] ?? '') ?>;

    function getSelectedVilleId(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        return selectedOption ? selectedOption.getAttribute('data-id') : null;
    }

    function remplirQuartiers(selectVilleId, selectQuartier, valeurSelectionnee = '') {
        selectQuartier.innerHTML = '';

        if (!selectVilleId || !quartiersParVille[selectVilleId] || quartiersParVille[selectVilleId].length === 0) {
            selectQuartier.innerHTML = '<option value="">-- Aucun quartier disponible --</option>';
            return;
        }

        const optionDefault = document.createElement('option');
        optionDefault.value = '';
        optionDefault.textContent = '-- Choisir un quartier --';
        selectQuartier.appendChild(optionDefault);

        quartiersParVille[selectVilleId].forEach(quartier => {
            const option = document.createElement('option');
            option.value = quartier.nom_quartier;
            option.textContent = quartier.nom_quartier;

            if (valeurSelectionnee && valeurSelectionnee === quartier.nom_quartier) {
                option.selected = true;
            }

            selectQuartier.appendChild(option);
        });
    }

    const departSelect = document.getElementById('depart');
    const arriveeSelect = document.getElementById('arrivee');
    const quartierDepartSelect = document.getElementById('quartier_depart');
    const quartierArriveeSelect = document.getElementById('quartier_arrivee');

    departSelect.addEventListener('change', function () {
        remplirQuartiers(getSelectedVilleId(this), quartierDepartSelect);
    });

    arriveeSelect.addEventListener('change', function () {
        remplirQuartiers(getSelectedVilleId(this), quartierArriveeSelect);
    });

    window.addEventListener('DOMContentLoaded', function () {
        remplirQuartiers(getSelectedVilleId(departSelect), quartierDepartSelect, selectedQuartierDepart);
        remplirQuartiers(getSelectedVilleId(arriveeSelect), quartierArriveeSelect, selectedQuartierArrivee);
    });
</script>

<?php
$adminContent = ob_get_clean();

$adminTitle = 'Insertion voyage';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Bienvenu dans votre espace Administrateur ! ! !';
$baseUrl = '';

include __DIR__ . '/../includes/layoutadmin.php';
?>