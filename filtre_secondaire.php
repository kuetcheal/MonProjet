<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$departValue = $_POST['input1'] ?? $_SESSION['depart'] ?? '';
$arriveeValue = $_POST['input2'] ?? $_SESSION['arrivee'] ?? '';
$dateValue = $_POST['input3'] ?? $_SESSION['date'] ?? '';
$dateRetourValue = $_POST['input4'] ?? $_SESSION['dateretour'] ?? '';
$tripType = $_POST['inlineRadioOptions'] ?? ($_SESSION['tripType'] ?? 'option1');

$_SESSION['tripType'] = $tripType;

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = $bdd->query('SELECT * FROM destination ORDER BY Nom_ville ASC');
    $destinations = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $destinations = [];
}
?>

<div class="secondary-filter-wrapper">
    <div class="secondary-filter-card">
        <form action="listevoyageretour.php" method="post" class="secondary-filter-form">
            <div class="secondary-trip-options">
                <label class="secondary-trip-option">
                    <input
                        type="radio"
                        id="secondaryInlineRadio1"
                        name="inlineRadioOptions"
                        value="option1"
                        <?php echo ($tripType === 'option1') ? 'checked' : ''; ?>
                    >
                    <span>Aller simple</span>
                </label>

                <label class="secondary-trip-option">
                    <input
                        type="radio"
                        id="secondaryInlineRadio2"
                        name="inlineRadioOptions"
                        value="option2"
                        <?php echo ($tripType === 'option2') ? 'checked' : ''; ?>
                    >
                    <span>Aller-Retour</span>
                </label>
            </div>

            <div class="secondary-search-fields">
                <div class="secondary-field-group">
                    <label for="secondaryInput1">
                        <i class="bi bi-geo-alt"></i> DE :
                    </label>
                    <select id="secondaryInput1" name="input1">
                        <?php foreach ($destinations as $destinationItem): ?>
                            <?php $destination = $destinationItem['Nom_ville']; ?>
                            <option
                                value="<?php echo htmlspecialchars($destination); ?>"
                                <?php echo ($departValue === $destination) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($destination); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="secondary-field-group">
                    <label for="secondaryInput2">
                        <i class="bi bi-geo-alt"></i> A :
                    </label>
                    <select id="secondaryInput2" name="input2">
                        <?php foreach ($destinations as $destinationItem): ?>
                            <?php $destination = $destinationItem['Nom_ville']; ?>
                            <option
                                value="<?php echo htmlspecialchars($destination); ?>"
                                <?php echo ($arriveeValue === $destination) ? 'selected' : ''; ?>
                            >
                                <?php echo htmlspecialchars($destination); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="secondary-field-group">
                    <label for="secondaryInput3">Date départ :</label>
                    <input
                        type="date"
                        id="secondaryInput3"
                        name="input3"
                        value="<?php echo htmlspecialchars($dateValue); ?>"
                        required
                    >
                </div>

                <div class="secondary-field-group">
                    <label for="secondaryInput4">Date retour :</label>
                    <input
                        type="date"
                        id="secondaryInput4"
                        name="input4"
                        value="<?php echo htmlspecialchars($dateRetourValue); ?>"
                        <?php echo ($tripType === 'option1') ? 'disabled' : ''; ?>
                    >
                </div>

                <div class="secondary-field-group secondary-submit-group">
                    <label class="secondary-fake-label">Valider</label>
                    <input type="submit" value="Valider" class="secondary-submit-btn">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const radioAller = document.getElementById("secondaryInlineRadio1");
    const radioAllerRetour = document.getElementById("secondaryInlineRadio2");
    const dateRetour = document.getElementById("secondaryInput4");

    function toggleSecondaryDateRetour() {
        if (!radioAller || !radioAllerRetour || !dateRetour) return;

        if (radioAllerRetour.checked) {
            dateRetour.disabled = false;
            dateRetour.required = true;
        } else {
            dateRetour.disabled = true;
            dateRetour.required = false;
            dateRetour.value = "";
        }
    }

    if (radioAller) {
        radioAller.addEventListener("change", toggleSecondaryDateRetour);
    }

    if (radioAllerRetour) {
        radioAllerRetour.addEventListener("change", toggleSecondaryDateRetour);
    }

    toggleSecondaryDateRetour();
});
</script>