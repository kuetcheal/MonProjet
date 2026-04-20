<?php
session_start();

if (empty($_SESSION['Id_compte'])) {
    header('Location: /MonProjet/connexion.php');
    exit;
}

require_once __DIR__ . '/../config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de données.');
}

$userId = (int) $_SESSION['Id_compte'];

$stmt = $bdd->prepare("
    SELECT u.role, cp.id AS chauffeur_profile_id, cp.statut_validation
    FROM user u
    LEFT JOIN chauffeur_profile cp ON cp.user_id = u.id
    WHERE u.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $userId]);
$account = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$account) {
    header('Location: /MonProjet/connexion.php');
    exit;
}

$alreadyProfile = !empty($account['chauffeur_profile_id']);
$statutValidation = $account['statut_validation'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devenir chauffeur</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="px-4 py-10">
        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg p-6 sm:p-8">
            <h1 class="text-2xl sm:text-3xl font-bold text-center text-green-700 mb-6">
                Devenir chauffeur covoiturage
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

            <?php if ($alreadyProfile && $statutValidation === 'en_attente'): ?>
                <div class="rounded-lg bg-yellow-100 text-yellow-800 border border-yellow-200 px-4 py-4">
                    Vous avez déjà envoyé une demande chauffeur. Elle est actuellement en attente de validation.
                </div>
            <?php elseif ($alreadyProfile && $statutValidation === 'valide'): ?>
                <div class="rounded-lg bg-green-100 text-green-700 border border-green-200 px-4 py-4">
                    Votre profil chauffeur est déjà validé.
                </div>
            <?php else: ?>
                <form action="/MonProjet/Authentification/traitement_chauffeur.php" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block font-medium mb-2 text-slate-800">Numéro de permis</label>
                            <input type="text" name="numero_permis" class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300" required>
                        </div>

                        <div>
                            <label class="block font-medium mb-2 text-slate-800">Numéro CNI</label>
                            <input type="text" name="numero_cni" class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300" required>
                        </div>

                        <div>
                            <label class="block font-medium mb-2 text-slate-800">Marque du véhicule</label>
                            <input type="text" name="marque_vehicule" class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300" required>
                        </div>

                        <div>
                            <label class="block font-medium mb-2 text-slate-800">Modèle du véhicule</label>
                            <input type="text" name="modele_vehicule" class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300" required>
                        </div>

                        <div>
                            <label class="block font-medium mb-2 text-slate-800">Immatriculation</label>
                            <input type="text" name="immatriculation" class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300" required>
                        </div>

                        <div>
                            <label class="block font-medium mb-2 text-slate-800">Couleur du véhicule</label>
                            <input type="text" name="couleur_vehicule" class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300" required>
                        </div>

                        <div>
                            <label class="block font-medium mb-2 text-slate-800">Nombre de places disponibles</label>
                            <input type="number" name="nombre_places" min="1" max="8" class="w-full border border-gray-300 rounded-lg px-4 py-3 outline-none focus:ring-2 focus:ring-green-300" required>
                        </div>
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Photo du permis</label>
                        <input type="file" name="photo_permis" accept="image/*,.pdf" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
                    </div>

                    <div>
                        <label class="block font-medium mb-2 text-slate-800">Photo de la carte grise</label>
                        <input type="file" name="photo_carte_grise" accept="image/*,.pdf" class="w-full border border-gray-300 rounded-lg px-4 py-3 bg-white">
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold transition">
                        Envoyer ma demande
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>