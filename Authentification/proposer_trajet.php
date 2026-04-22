<?php
session_start();

if (empty($_SESSION['Id_compte'])) {
    header('Location: /MonProjet/connexion.php');
    exit;
}

if (!in_array($_SESSION['user_role'] ?? '', ['client_chauffeur', 'chauffeur'], true)) {
    $_SESSION['error'] = "Vous devez être chauffeur pour proposer un trajet.";
    header('Location: /MonProjet/Authentification/mon_compte.php');
    exit;
}

require_once __DIR__ . '/../config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base.');
}

$userId = (int) $_SESSION['Id_compte'];

$check = $bdd->prepare("
    SELECT statut_validation, nombre_places
    FROM chauffeur_profile
    WHERE user_id = :user_id
    LIMIT 1
");
$check->execute([':user_id' => $userId]);
$chauffeur = $check->fetch(PDO::FETCH_ASSOC);

if (!$chauffeur || ($chauffeur['statut_validation'] ?? '') !== 'valide') {
    $_SESSION['error'] = "Votre profil chauffeur doit être validé avant de proposer un trajet.";
    header('Location: /MonProjet/Authentification/mon_compte.php');
    exit;
}

try {
    $destStmt = $bdd->query("SELECT Nom_ville FROM destination ORDER BY Nom_ville ASC");
    $destinations = $destStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $destinations = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proposer un trajet</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="px-4 py-10">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-green-700 text-center mb-6">
                Proposer un trajet covoiturage
            </h1>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="mb-4 rounded-lg bg-green-100 text-green-700 border border-green-200 px-4 py-3">
                    <?php
                    echo htmlspecialchars($_SESSION['success']);
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="mb-4 rounded-lg bg-red-100 text-red-700 border border-red-200 px-4 py-3">
                    <?php
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form action="/MonProjet/Authentification/traitement_proposition_trajet.php" method="POST" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Ville de départ</label>
                        <select name="villeDepart" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($destinations as $destination): ?>
                                <option value="<?= htmlspecialchars($destination['Nom_ville']) ?>">
                                    <?= htmlspecialchars($destination['Nom_ville']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Quartier de départ</label>
                        <input type="text" name="quartierDepart" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Ville d'arrivée</label>
                        <select name="villeArrivee" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                            <option value="">Sélectionner</option>
                            <?php foreach ($destinations as $destination): ?>
                                <option value="<?= htmlspecialchars($destination['Nom_ville']) ?>">
                                    <?= htmlspecialchars($destination['Nom_ville']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Quartier d'arrivée</label>
                        <input type="text" name="quartierArrivee" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Date de départ</label>
                        <input type="date" name="jourDepart" min="<?= date('Y-m-d') ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Heure de départ</label>
                        <input type="time" name="heureDepart" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Heure d'arrivée estimée</label>
                        <input type="time" name="heureArrivee" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Places disponibles</label>
                        <input type="number" name="nombre_places_disponibles" min="1" max="<?= (int) ($chauffeur['nombre_places'] ?? 4) ?>" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-medium mb-2 text-slate-800">Prix par place (FCFA)</label>
                        <input type="number" name="prix" min="500" step="1" class="w-full border border-gray-300 rounded-lg px-4 py-3" required>
                        <p class="text-sm text-gray-500 mt-2">
                            Une commission plateforme de 10 % sera appliquée.
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block font-medium mb-2 text-slate-800">Commentaire chauffeur</label>
                        <textarea name="commentaire_chauffeur" rows="4" class="w-full border border-gray-300 rounded-lg px-4 py-3" placeholder="Ex : départ à l'heure, bagage cabine accepté, point de rendez-vous précis..."></textarea>
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition">
                    Soumettre le trajet
                </button>
            </form>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>