<?php
$nombrePlaces = isset($nombrePlaces) ? (int)$nombrePlaces : 0;
$reservedSeats = $reservedSeats ?? [];
?>

<div class="space-y-4">
    <div class="flex items-center gap-4 text-sm">
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-blue-500 inline-block"></span>
            <span>Libre</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-green-500 inline-block"></span>
            <span>Sélectionnée</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-4 bg-gray-400 inline-block"></span>
            <span>Occupée</span>
        </div>
    </div>

    <div class="grid grid-cols-6 gap-2 bg-gray-100 p-4 rounded-xl">
        <div class="col-span-6 flex justify-center items-center bg-orange-500 text-white font-bold py-3 rounded-lg">
            Conducteur
        </div>

        <?php
        $seatNumber = 1;
        $rows = (int) ceil($nombrePlaces / 5);

        for ($row = 0; $row < $rows; $row++) {
            for ($col = 0; $col < 6; $col++) {
                if ($col === 2) {
                    echo '<div></div>';
                    continue;
                }

                if ($seatNumber > $nombrePlaces) {
                    echo '<div></div>';
                    continue;
                }

                $isReserved = in_array($seatNumber, $reservedSeats, true);

                if ($isReserved) {
                    echo '
                        <button
                            type="button"
                            class="bg-gray-400 text-white p-2 rounded cursor-not-allowed opacity-80"
                            disabled
                        >' . $seatNumber . '</button>
                    ';
                } else {
                    echo '
                        <button
                            type="button"
                            class="seat-btn bg-blue-500 text-white p-2 rounded hover:bg-blue-700 transition"
                            id="seat' . $seatNumber . '"
                            onclick="selectSeat(' . $seatNumber . ')"
                        >' . $seatNumber . '</button>
                    ';
                }

                $seatNumber++;
            }
        }
        ?>
    </div>
</div>