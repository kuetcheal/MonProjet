<?php
$bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');

// Récupérer les villes de destination
$query = 'SELECT * FROM destination ORDER BY Nom_ville ASC';
$response = $bdd->query($query);

$destinations = [];
while ($donnee = $response->fetch()) {
    $destinations[] = htmlspecialchars($donnee['Nom_ville']);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Recherche de Trajet</title>
</head>

<body class="bg-gray-100 flex justify-center items-center min-h-screen">
    <div class="bg-white shadow-lg rounded-lg p-6 w-full max-w-4xl border border-gray-300">
        <form action="listevoyageretour.php" method="post">
            <div class="flex items-center space-x-4 mb-4">
                <!-- Boutons Aller / Aller-Retour -->
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1" checked
                        class="hidden peer">
                    <span class="w-5 h-5 border-2 border-green-600 rounded-full flex items-center justify-center peer-checked:bg-green-600"></span>
                    <span class="text-gray-700 font-semibold">Aller</span>
                </label>

                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"
                        class="hidden peer">
                    <span class="w-5 h-5 border-2 border-gray-400 rounded-full flex items-center justify-center peer-checked:bg-green-600"></span>
                    <span class="text-gray-700 font-semibold">Aller-Retour</span>
                </label>
            </div>

            <!-- Formulaire principal -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-center">
                <!-- Ville de départ -->
                <div class="relative">
                    <label class="block text-gray-700 font-semibold">
                        <i class="fas fa-map-marker-alt text-green-600 mr-1"></i> DE :
                    </label>
                    <select name="input1" id="input1"
                        class="w-full p-2 border border-green-600 rounded-md focus:ring focus:ring-green-200">
                        <?php foreach ($destinations as $destination) : ?>
                        <option value="<?= $destination; ?>"><?= $destination; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ville d'arrivée -->
                <div class="relative">
                    <label class="block text-gray-700 font-semibold">
                        <i class="fas fa-map-marker-alt text-green-600 mr-1"></i> A :
                    </label>
                    <select name="input2" id="input2"
                        class="w-full p-2 border border-green-600 rounded-md focus:ring focus:ring-green-200">
                        <?php foreach ($destinations as $destination) : ?>
                        <option value="<?= $destination; ?>"><?= $destination; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date de départ -->
                <div>
                    <label class="block text-gray-700 font-semibold">Date départ :</label>
                    <input type="date" name="input3"
                        class="w-full p-2 border border-green-600 rounded-md focus:ring focus:ring-green-200">
                </div>

                <!-- Date de retour -->
                <div>
                    <label class="block text-gray-700 font-semibold">Date retour :</label>
                    <input type="date" name="input4" id="input4"
                        class="w-full p-2 border border-green-600 rounded-md focus:ring focus:ring-green-200 bg-gray-100 cursor-not-allowed"
                        disabled>
                </div>

                <!-- Bouton Valider -->
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded-md hover:bg-green-700 transition duration-200">
                        Valider
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Activer/Désactiver le champ "Date de retour" selon la sélection
        document.querySelectorAll('input[name="inlineRadioOptions"]').forEach(radio => {
            radio.addEventListener('change', function () {
                const dateRetour = document.getElementById("input4");
                if (document.getElementById("inlineRadio2").checked) {
                    dateRetour.removeAttribute("disabled");
                    dateRetour.classList.remove("bg-gray-100", "cursor-not-allowed");
                } else {
                    dateRetour.setAttribute("disabled", "true");
                    dateRetour.classList.add("bg-gray-100", "cursor-not-allowed");
                    dateRetour.value = "";
                }
            });
        });
    </script>
</body>

</html>
