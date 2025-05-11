<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$database = "bd_stock";

$conn = mysqli_connect($host, $user, $password, $database);
if (!$conn) {
    die("Connexion échouée : " . mysqli_connect_error());
}

$destinations = mysqli_query($conn, "SELECT Nom_ville FROM destination ORDER BY Nom_ville ASC");

if (isset($_GET['id_reservation'])) {
    $id = intval($_GET['id_reservation']);
    $sql = "SELECT r.*, v.villeDepart, v.villeArrivee, v.jourDepart FROM reservation r JOIN voyage v ON r.idVoyage = v.idVoyage WHERE id_reservation = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $reservation = mysqli_fetch_assoc($result);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la Réservation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded shadow-md w-full max-w-2xl">
    <h1 class="text-2xl font-bold text-center text-gray-700 mb-6">Modifier la Réservation</h1>

    <?php if (isset($reservation)) : ?>
    <form method="POST" action="../listevoyagemodifier.php" class="space-y-4">
        <input type="hidden" name="modification" value="true">
        <input type="hidden" name="id_reservation" value="<?php echo $reservation['id_reservation']; ?>">

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold">Nom</label>
                <input type="text" name="nom" value="<?php echo $reservation['nom']; ?>" required class="w-full px-4 py-2 border rounded">
            </div>
            <div>
                <label class="block font-semibold">Prénom</label>
                <input type="text" name="prenom" value="<?php echo $reservation['prenom']; ?>" required class="w-full px-4 py-2 border rounded">
            </div>
            <div>
                <label class="block font-semibold">Téléphone</label>
                <input type="text" name="telephone" value="<?php echo $reservation['telephone']; ?>" required class="w-full px-4 py-2 border rounded">
            </div>
            <div>
                <label class="block font-semibold">Numéro de siège</label>
                <input type="number" name="numero_siege" value="<?php echo $reservation['Numero_siege']; ?>" min="1" class="w-full px-4 py-2 border rounded">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold">Lieu de départ</label>
                <select name="input1" class="w-full px-4 py-2 border rounded">
                    <?php mysqli_data_seek($destinations, 0); while ($d = mysqli_fetch_assoc($destinations)) : ?>
                        <option value="<?php echo $d['Nom_ville']; ?>" <?php if ($d['Nom_ville'] == $reservation['villeDepart']) echo 'selected'; ?>><?php echo $d['Nom_ville']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label class="block font-semibold">Lieu d'arrivée</label>
                <select name="input2" class="w-full px-4 py-2 border rounded">
                    <?php mysqli_data_seek($destinations, 0); while ($d = mysqli_fetch_assoc($destinations)) : ?>
                        <option value="<?php echo $d['Nom_ville']; ?>" <?php if ($d['Nom_ville'] == $reservation['villeArrivee']) echo 'selected'; ?>><?php echo $d['Nom_ville']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <div>
            <label class="block font-semibold">Jour de départ</label>
            <input type="date" name="input3" value="<?php echo $reservation['jourDepart']; ?>" required class="w-full px-4 py-2 border rounded">
        </div>

        <div class="flex justify-between">
            <a href="../Ma_reservation.php?id_reservation=<?php echo $reservation['id_reservation']; ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">Retour</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded">Valider</button>
        </div>
    </form>
    <?php else : ?>
        <p class="text-red-500 text-center">Réservation non trouvée.</p>
    <?php endif; ?>
</div>
</body>
</html>
