<?php
session_start();

require_once __DIR__ . '/../config.php';

$userId = (int)($_SESSION['Id_compte'] ?? $_SESSION['user_id'] ?? 0);

if ($userId <= 0) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header('Location: ../connexion.php');
    exit;
}

$idVoyage = isset($_GET['idVoyage']) ? (int)$_GET['idVoyage'] : 0;

if ($idVoyage <= 0) {
    die('Trajet invalide.');
}

try {
    $stmt = $pdo->prepare("
        SELECT *
        FROM voyage
        WHERE idVoyage = :idVoyage
        AND modeTransport = 'covoiturage'
        AND statut_trajet = 'valide'
        LIMIT 1
    ");

    $stmt->execute([
        ':idVoyage' => $idVoyage
    ]);

    $voyage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$voyage) {
        die('Trajet de covoiturage introuvable.');
    }

    if (empty($voyage['chauffeur_id'])) {
        die('Ce trajet n’a pas encore de chauffeur associé.');
    }

    if ((int)$voyage['chauffeur_id'] === $userId) {
        die('Vous ne pouvez pas réserver votre propre trajet.');
    }

    $prix = !empty($voyage['prix_par_place'])
        ? (float)$voyage['prix_par_place']
        : (float)($voyage['prix'] ?? 0);

    if ($prix <= 0) {
        die('Prix du trajet invalide.');
    }

    $placesDisponibles = isset($voyage['nombre_places_disponibles']) && $voyage['nombre_places_disponibles'] !== null
        ? (int)$voyage['nombre_places_disponibles']
        : (int)($voyage['nombrePlaces'] ?? 0);

    /*
        On récupère les informations du compte connecté
        pour préremplir le formulaire.
    */
    $userStmt = $pdo->prepare("
        SELECT 
            user_name,
            user_firstname,
            user_phone,
            user_mail
        FROM user
        WHERE id = :id
        LIMIT 1
    ");

    $userStmt->execute([
        ':id' => $userId
    ]);

    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $prenomClient = $user['user_firstname'] ?? ($_SESSION['user_firstname'] ?? '');
    $nomClient = $user['user_name'] ?? ($_SESSION['user_name'] ?? '');
    $telephoneClient = $user['user_phone'] ?? ($_SESSION['user_phone'] ?? '');
    $emailClient = $user['user_mail'] ?? ($_SESSION['user_mail'] ?? '');

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

ob_start();
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<div class="max-w-4xl mx-auto px-4 py-10">
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">

        <div class="mb-8">
            <p class="inline-flex items-center gap-2 bg-green-100 text-green-700 font-bold px-4 py-2 rounded-full text-sm">
                <i class="bi bi-car-front-fill"></i>
                Covoiturage
            </p>

            <h1 class="text-3xl font-extrabold text-slate-800 mt-4">
                Demande de réservation
            </h1>

            <p class="text-gray-500 mt-2">
                Votre demande sera envoyée au chauffeur. Vous paierez seulement si le chauffeur accepte votre demande.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            <div class="border rounded-2xl p-5 bg-gray-50">
                <h2 class="font-bold text-slate-800 mb-3">Départ</h2>

                <p class="text-lg font-semibold">
                    <?= htmlspecialchars($voyage['villeDepart'] ?? '') ?>
                </p>

                <?php if (!empty($voyage['quartierDepart'])): ?>
                    <p class="text-gray-500">
                        <?= htmlspecialchars($voyage['quartierDepart']) ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="border rounded-2xl p-5 bg-gray-50">
                <h2 class="font-bold text-slate-800 mb-3">Arrivée</h2>

                <p class="text-lg font-semibold">
                    <?= htmlspecialchars($voyage['villeArrivee'] ?? '') ?>
                </p>

                <?php if (!empty($voyage['quartierArrivee'])): ?>
                    <p class="text-gray-500">
                        <?= htmlspecialchars($voyage['quartierArrivee']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
            <div class="border rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Date</p>

                <p class="font-bold text-slate-800">
                    <?= htmlspecialchars($voyage['jourDepart'] ?? '') ?>
                </p>
            </div>

            <div class="border rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Heure</p>

                <p class="font-bold text-slate-800">
                    <?= htmlspecialchars(substr($voyage['heureDepart'] ?? '', 0, 5)) ?>
                </p>
            </div>

            <div class="border rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Prix par place</p>

                <p class="font-bold text-green-600">
                    <?= number_format($prix, 0, ',', ' ') ?> FCFA
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
            <div class="border rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Places disponibles</p>

                <p class="font-bold text-slate-800">
                    <?= (int)$placesDisponibles ?> place(s)
                </p>
            </div>

            <div class="border rounded-2xl p-5">
                <p class="text-gray-500 text-sm">Montant estimé</p>

                <p class="font-extrabold text-green-600 text-xl">
                    <?= number_format($prix, 0, ',', ' ') ?> FCFA
                </p>
            </div>
        </div>

        <?php if (!empty($voyage['commentaire_chauffeur'])): ?>
            <div class="border-l-4 border-green-500 bg-green-50 p-4 rounded-xl mb-8">
                <h3 class="font-bold text-slate-800 mb-1">
                    Message du chauffeur
                </h3>

                <p class="text-gray-700">
                    <?= nl2br(htmlspecialchars($voyage['commentaire_chauffeur'])) ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($placesDisponibles <= 0): ?>

            <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl font-semibold">
                Ce trajet n’a plus de places disponibles.
            </div>

            <div class="mt-6">
                <a href="../listevoyageretour.php?transport=covoiturage"
                   class="inline-block bg-gray-800 hover:bg-gray-900 text-white font-bold px-6 py-3 rounded-xl transition">
                    Retour aux trajets
                </a>
            </div>

        <?php else: ?>

            <form method="post" action="../offres/traiter_reservation.php" class="space-y-5">
                <input type="hidden" name="idVoyage" value="<?= (int)$voyage['idVoyage'] ?>">
                <input type="hidden" name="numeroPlace" value="1">
                <input type="hidden" name="prix_reservation" value="<?= htmlspecialchars((string)$prix) ?>">

                <div class="bg-gray-50 p-5 rounded-2xl">
                    <h3 class="font-bold text-slate-800 mb-4">
                        Vos informations
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-bold text-gray-700 mb-2">
                                Prénom <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="prenom"
                                value="<?= htmlspecialchars($prenomClient) ?>"
                                required
                                readonly
                                class="w-full border rounded-xl p-3 bg-gray-100 text-gray-700 focus:outline-none"
                                placeholder="Votre prénom">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-2">
                                Nom <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="nom"
                                value="<?= htmlspecialchars($nomClient) ?>"
                                required
                                readonly
                                class="w-full border rounded-xl p-3 bg-gray-100 text-gray-700 focus:outline-none"
                                placeholder="Votre nom">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block font-bold text-gray-700 mb-2">
                                Téléphone <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="text"
                                name="telephone"
                                value="<?= htmlspecialchars($telephoneClient) ?>"
                                required
                                readonly
                                class="w-full border rounded-xl p-3 bg-gray-100 text-gray-700 focus:outline-none"
                                placeholder="Ex : 655196254">
                        </div>

                        <div>
                            <label class="block font-bold text-gray-700 mb-2">
                                Email <span class="text-red-500">*</span>
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="<?= htmlspecialchars($emailClient) ?>"
                                required
                                readonly
                                class="w-full border rounded-xl p-3 bg-gray-100 text-gray-700 focus:outline-none"
                                placeholder="exemple@gmail.com">
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-100 text-blue-800 p-4 rounded-xl text-sm">
                    Après l’envoi, le chauffeur pourra accepter ou refuser votre demande.
                    Si elle est acceptée, vous recevrez un email pour payer et confirmer votre place.
                </div>

                <button
                    type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 rounded-xl transition">
                    Envoyer la demande au chauffeur
                </button>
            </form>

        <?php endif; ?>

    </div>
</div>

<?php
$content = ob_get_clean();
$title = "Demande de covoiturage";
include __DIR__ . '/../layouts/default.php';
?>