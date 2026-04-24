<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

$departValue = $_POST['input1'] ?? $_SESSION['depart'] ?? '';
$arriveeValue = $_POST['input2'] ?? $_SESSION['arrivee'] ?? '';
$dateValue = $_POST['input3'] ?? $_SESSION['date'] ?? '';
$dateRetourValue = $_POST['input4'] ?? $_SESSION['dateretour'] ?? '';
$tripType = $_POST['inlineRadioOptions'] ?? ($_SESSION['tripType'] ?? 'option1');

$_SESSION['tripType'] = $tripType;

$destinations = [];

try {
    $query = $pdo->query('SELECT Nom_ville FROM destination ORDER BY Nom_ville ASC');
    $destinations = $query->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $destinations = [];
}

function formatDateDisplay($date)
{
    if (!$date) return '';
    $timestamp = strtotime($date);
    return $timestamp ? date('d/m/Y', $timestamp) : '';
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script>

<style>
    .litepicker {
        font-family: 'Inter', Arial, sans-serif;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        min-width: auto !important;
        z-index: 9999 !important;
    }

    .litepicker .container__main {
        background: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
        border-radius: 10px !important;
        padding: 18px 18px 14px !important;
    }

    .litepicker .container__months {
        gap: 18px !important;
    }

    .litepicker .month-item {
        padding: 0 !important;
    }

    .litepicker .month-item-header {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 48px !important;
        margin-bottom: 10px !important;
        color: green !important;
        font-weight: 700 !important;
        font-size: 16px !important;
        border-bottom: 1px solid #f3f4f6;
    }

    .litepicker .month-item-name,
    .litepicker .month-item-year {
        color: green !important;
        font-weight: 700 !important;
        font-size: 16px !important;
    }

    .litepicker .button-previous-month,
    .litepicker .button-next-month {
        width: 36px !important;
        height: 36px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #6b7280 !important;
        background: #f9fafb !important;
        transition: all 0.2s ease !important;
    }

    .litepicker .button-previous-month:hover,
    .litepicker .button-next-month:hover {
        background: #f3f4f6 !important;
        color: green !important;
    }

    .litepicker .month-item-weekdays-row {
        margin-bottom: 8px !important;
    }

    .litepicker .month-item-weekdays-row>div {
        color: #6b7280 !important;
        font-size: 13px !important;
        font-weight: 700 !important;
        text-transform: lowercase;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        height: 34px !important;
    }

    .litepicker .month-item-calendar {
        gap: 4px !important;
    }

    .litepicker .day-item {
        width: 42px !important;
        height: 42px !important;
        max-width: 42px !important;
        line-height: 42px !important;
        border-radius: 999px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 15px !important;
        font-weight: bold !important;
        color: black !important;
        transition: all 0.18s ease !important;
        position: relative;
        z-index: 1;
    }

    .litepicker .day-item:hover {
        background: #f3f4f6 !important;
        color: #111827 !important;
    }

    .litepicker .day-item.is-today {
        border: 1.5px solid #9ca3af !important;
        color: #111827 !important;
        background: #fff !important;
    }

    .litepicker .day-item.is-start-date,
    .litepicker .day-item.is-end-date {
        background: green !important;
        color: #fff !important;
        font-weight: 700 !important;
    }

    .litepicker .day-item.is-in-range {
        background: #f3f4f6 !important;
        color: green !important;
        border-radius: 0 !important;
    }

    .litepicker .day-item.is-start-date.is-in-range {
        border-radius: 999px 0 0 999px !important;
    }

    .litepicker .day-item.is-end-date.is-in-range {
        border-radius: 0 999px 999px 0 !important;
    }

    .litepicker .day-item.is-start-date.is-end-date {
        border-radius: 999px !important;
    }

    .litepicker .day-item.is-locked {
        color: #d1d5db !important;
    }

    .litepicker .container__footer {
        border-top: 1px solid #f3f4f6 !important;
        margin-top: 12px !important;
        padding-top: 12px !important;
    }

    .litepicker .container__footer .button-cancel,
    .litepicker .container__footer .button-apply {
        font-weight: 600 !important;
        padding: 10px 16px !important;
    }

    .litepicker .container__footer .button-apply {
        background: #156f3e !important;
        border-color: #156f3e !important;
    }

    .litepicker .container__footer .button-cancel {
        color: #6b7280 !important;
    }

    .litepicker select {
        appearance: none !important;
        border: 1px solid #e5e7eb !important;
        padding: 6px 28px 6px 10px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #374151 !important;
        background-color: #fff !important;
        background-image: none !important;
        box-shadow: none !important;
        outline: none !important;
    }

    @media (max-width: 768px) {
        .litepicker {
            min-width: auto !important;
            width: calc(100vw - 24px) !important;
        }

        .litepicker .container__main {
            width: calc(100vw - 24px) !important;
            padding: 14px !important;
            border-radius: 20px !important;
        }

        .litepicker .container__months {
            display: block !important;
        }

        .litepicker .month-item+.month-item {
            margin-top: 16px !important;
        }

        .litepicker .day-item {
            width: 38px !important;
            height: 38px !important;
            max-width: 38px !important;
            line-height: 38px !important;
            font-size: 14px !important;
        }
    }
</style>

<div class="secondary-filter-wrapper">
    <div class="secondary-filter-card">
        <form action="listevoyageretour.php" method="post" class="secondary-filter-form" id="secondaryBookingForm">
            <div class="secondary-trip-options">
                <label class="secondary-trip-option">
                    <input
                        type="radio"
                        id="secondaryInlineRadio1"
                        name="inlineRadioOptions"
                        value="option1"
                        <?= ($tripType === 'option1') ? 'checked' : ''; ?>>
                    <span>Aller simple</span>
                </label>

                <label class="secondary-trip-option">
                    <input
                        type="radio"
                        id="secondaryInlineRadio2"
                        name="inlineRadioOptions"
                        value="option2"
                        <?= ($tripType === 'option2') ? 'checked' : ''; ?>>
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
                                value="<?= htmlspecialchars($destination); ?>"
                                <?= ($departValue === $destination) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($destination); ?>
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
                                value="<?= htmlspecialchars($destination); ?>"
                                <?= ($arriveeValue === $destination) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($destination); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="secondary-field-group">
                    <label for="secondary_depart_display">Date départ :</label>
                    <input
                        type="text"
                        id="secondary_depart_display"
                        placeholder="jj/mm/aaaa"
                        value="<?= htmlspecialchars(formatDateDisplay($dateValue)); ?>"
                        readonly
                        class="w-full cursor-pointer">
                    <input
                        type="hidden"
                        id="secondary_depart_date"
                        name="input3"
                        value="<?= htmlspecialchars($dateValue); ?>">
                </div>

                <div class="secondary-field-group">
                    <label for="secondary_return_display">Date retour :</label>
                    <input
                        type="text"
                        id="secondary_return_display"
                        placeholder="jj/mm/aaaa"
                        value="<?= htmlspecialchars(formatDateDisplay($dateRetourValue)); ?>"
                        readonly
                        <?= ($tripType === 'option1') ? 'disabled' : ''; ?>
                        class="w-full cursor-pointer disabled:cursor-not-allowed disabled:bg-gray-100">
                    <input
                        type="hidden"
                        id="secondary_return_date"
                        name="input4"
                        value="<?= htmlspecialchars($dateRetourValue); ?>">
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
    document.addEventListener('DOMContentLoaded', function() {
        function setupSecondaryBookingForm() {
            const form = document.getElementById('secondaryBookingForm');
            if (!form) return;

            const startDisplay = document.getElementById('secondary_depart_display');
            const endDisplay = document.getElementById('secondary_return_display');
            const startHidden = document.getElementById('secondary_depart_date');
            const endHidden = document.getElementById('secondary_return_date');
            const radios = form.querySelectorAll('input[name="inlineRadioOptions"]');

            let picker = null;

            function getTripMode() {
                const checked = form.querySelector('input[name="inlineRadioOptions"]:checked');
                return checked ? checked.value : 'option1';
            }

            function clearDates() {
                startDisplay.value = '';
                endDisplay.value = '';
                startHidden.value = '';
                endHidden.value = '';
            }

            function createPicker(singleMode = true) {
                if (picker) {
                    picker.destroy();
                }

                picker = new Litepicker({
                    element: startDisplay,
                    elementEnd: singleMode ? null : endDisplay,
                    singleMode: singleMode,
                    numberOfMonths: 2,
                    numberOfColumns: 2,
                    autoApply: true,
                    minDate: new Date(),
                    lang: 'fr-FR',
                    format: 'DD/MM/YYYY',
                    dropdowns: {
                        minYear: new Date().getFullYear(),
                        maxYear: new Date().getFullYear() + 2,
                        months: true,
                        years: true
                    },
                    setup: (pickerInstance) => {
                        pickerInstance.on('selected', (date1, date2) => {
                            if (date1) {
                                startDisplay.value = date1.format('DD/MM/YYYY');
                                startHidden.value = date1.format('YYYY-MM-DD');
                            }

                            if (!singleMode && date2) {
                                endDisplay.value = date2.format('DD/MM/YYYY');
                                endHidden.value = date2.format('YYYY-MM-DD');
                            }

                            if (singleMode) {
                                endDisplay.value = '';
                                endHidden.value = '';
                            }
                        });
                    }
                });
            }

            function updateMode(initialLoad = false) {
                const isRoundTrip = getTripMode() === 'option2';

                if (!initialLoad) {
                    clearDates();
                }

                if (isRoundTrip) {
                    endDisplay.disabled = false;
                    createPicker(false);
                } else {
                    endDisplay.disabled = true;
                    endDisplay.value = '';
                    endHidden.value = '';
                    createPicker(true);
                }
            }

            radios.forEach((radio) => {
                radio.addEventListener('change', function() {
                    updateMode(false);
                });
            });

            form.addEventListener('submit', function(e) {
                const isRoundTrip = getTripMode() === 'option2';

                if (!startHidden.value) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une date de départ.');
                    return;
                }

                if (isRoundTrip && !endHidden.value) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une date de retour.');
                }
            });

            updateMode(true);
        }

        setupSecondaryBookingForm();
    });
</script>