<?php
session_start();
require_once __DIR__ . '/../config.php';

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['voyage_id'], $_POST['chauffeur_id'])) {
    $voyageId = (int) $_POST['voyage_id'];
    $chauffeurId = (int) $_POST['chauffeur_id'];
    $action = trim($_POST['action']);

    try {
        $bdd->beginTransaction();

        if ($action === 'valider') {
            $stmt = $bdd->prepare("
                UPDATE voyage
                SET statut_trajet = 'valide'
                WHERE idVoyage = :id
            ");
            $stmt->execute([':id' => $voyageId]);

            $notif = $bdd->prepare("
                INSERT INTO notifications (user_id, type_notification, titre, message, lien)
                VALUES (:user_id, 'trajet_valide', :titre, :message, :lien)
            ");
            $notif->execute([
                ':user_id' => $chauffeurId,
                ':titre' => 'Trajet covoiturage validé',
                ':message' => 'Votre trajet covoiturage a été validé et est maintenant visible sur la plateforme.',
                ':lien' => '/MonProjet/Authentification/mon_compte.php'
            ]);

            $_SESSION['message'] = "Le trajet covoiturage a été validé.";
        }

        if ($action === 'refuser') {
            $stmt = $bdd->prepare("
                UPDATE voyage
                SET statut_trajet = 'refuse'
                WHERE idVoyage = :id
            ");
            $stmt->execute([':id' => $voyageId]);

            $notif = $bdd->prepare("
                INSERT INTO notifications (user_id, type_notification, titre, message, lien)
                VALUES (:user_id, 'trajet_refuse', :titre, :message, :lien)
            ");
            $notif->execute([
                ':user_id' => $chauffeurId,
                ':titre' => 'Trajet covoiturage refusé',
                ':message' => 'Votre trajet covoiturage a été refusé. Veuillez vérifier les informations et proposer un nouveau trajet.',
                ':lien' => '/MonProjet/Authentification/proposer_trajet.php'
            ]);

            $_SESSION['message'] = "Le trajet covoiturage a été refusé.";
        }

        $bdd->commit();
    } catch (PDOException $e) {
        if ($bdd->inTransaction()) {
            $bdd->rollBack();
        }
        $_SESSION['message'] = "Une erreur est survenue lors du traitement.";
    }

    header('Location: covoiturages.php');
    exit;
}

$notifCountStmt = $bdd->query("
    SELECT COUNT(*)
    FROM notifications
    WHERE cible_role = 'admin' AND is_read = 0
");
$adminNotificationCount = (int) $notifCountStmt->fetchColumn();

$stmt = $bdd->query("
    SELECT
        v.*,
        u.user_name,
        u.user_firstname,
        u.user_mail,
        cp.marque_vehicule,
        cp.modele_vehicule,
        cp.immatriculation
    FROM voyage v
    INNER JOIN user u ON u.id = v.chauffeur_id
    LEFT JOIN chauffeur_profile cp ON cp.user_id = u.id
    WHERE v.modeTransport = 'covoiturage'
    ORDER BY v.jourDepart DESC, v.heureDepart DESC
");
$covoiturages = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<div class="max-w-7xl mx-auto">
    <h1 class="text-3xl font-bold text-slate-800 mb-6">Validation des covoiturages</h1>

    <?php if (!empty($_SESSION['message'])): ?>
        <div class="mb-5 rounded-lg bg-green-100 text-green-700 border border-green-200 px-4 py-3">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-slate-800 text-white">
                    <tr>
                        <th class="px-4 py-4">Chauffeur</th>
                        <th class="px-4 py-4">Trajet</th>
                        <th class="px-4 py-4">Date</th>
                        <th class="px-4 py-4">Prix</th>
                        <th class="px-4 py-4">Commission</th>
                        <th class="px-4 py-4">Montant chauffeur</th>
                        <th class="px-4 py-4">Statut</th>
                        <th class="px-4 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($covoiturages as $trajet): ?>
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-4">
                                <div class="font-semibold text-slate-800">
                                    <?= htmlspecialchars(trim(($trajet['user_firstname'] ?? '') . ' ' . ($trajet['user_name'] ?? ''))) ?>
                                </div>
                                <div class="text-gray-500 text-xs">
                                    <?= htmlspecialchars($trajet['user_mail'] ?? '') ?>
                                </div>
                                <div class="text-gray-500 text-xs">
                                    <?= htmlspecialchars(trim(($trajet['marque_vehicule'] ?? '') . ' ' . ($trajet['modele_vehicule'] ?? ''))) ?>
                                    - <?= htmlspecialchars($trajet['immatriculation'] ?? '') ?>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-medium"><?= htmlspecialchars($trajet['villeDepart'] ?? '') ?> → <?= htmlspecialchars($trajet['villeArrivee'] ?? '') ?></div>
                                <div class="text-gray-500 text-xs">
                                    <?= htmlspecialchars($trajet['quartierDepart'] ?? '') ?> → <?= htmlspecialchars($trajet['quartierArrivee'] ?? '') ?>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div><?= htmlspecialchars($trajet['jourDepart'] ?? '') ?></div>
                                <div class="text-gray-500 text-xs">
                                    <?= htmlspecialchars(substr((string) ($trajet['heureDepart'] ?? ''), 0, 5)) ?>
                                    - <?= htmlspecialchars(substr((string) ($trajet['heureArrivee'] ?? ''), 0, 5)) ?>
                                </div>
                            </td>
                            <td class="px-4 py-4"><?= number_format((float) ($trajet['prix'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                            <td class="px-4 py-4"><?= number_format((float) ($trajet['commission_plateforme'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                            <td class="px-4 py-4"><?= number_format((float) ($trajet['montant_chauffeur'] ?? 0), 0, ',', ' ') ?> FCFA</td>
                            <td class="px-4 py-4">
                                <?php
                                $statut = $trajet['statut_trajet'] ?? 'en_attente';
                                $classes = 'bg-yellow-100 text-yellow-800';
                                if ($statut === 'valide') $classes = 'bg-green-100 text-green-700';
                                if ($statut === 'refuse') $classes = 'bg-red-100 text-red-700';
                                if ($statut === 'annule') $classes = 'bg-gray-100 text-gray-600';
                                if ($statut === 'termine') $classes = 'bg-blue-100 text-blue-700';
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $classes ?>">
                                    <?= htmlspecialchars($statut) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <?php if (($trajet['statut_trajet'] ?? '') !== 'valide'): ?>
                                        <form method="post">
                                            <input type="hidden" name="voyage_id" value="<?= (int) $trajet['idVoyage'] ?>">
                                            <input type="hidden" name="chauffeur_id" value="<?= (int) $trajet['chauffeur_id'] ?>">
                                            <button type="submit" name="action" value="valider" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                                Valider
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (($trajet['statut_trajet'] ?? '') !== 'refuse'): ?>
                                        <form method="post">
                                            <input type="hidden" name="voyage_id" value="<?= (int) $trajet['idVoyage'] ?>">
                                            <input type="hidden" name="chauffeur_id" value="<?= (int) $trajet['chauffeur_id'] ?>">
                                            <button type="submit" name="action" value="refuser" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                                Refuser
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($covoiturages)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Aucun covoiturage proposé pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
$adminContent = ob_get_clean();

$adminTitle = 'Validation des covoiturages';
$adminWelcome = 'Gestion des trajets covoiturage';
$adminUserName = 'Alex Stephane';
$baseUrl = '/MonProjet/Admins/';

include __DIR__ . '/../includes/layoutadmin.php';