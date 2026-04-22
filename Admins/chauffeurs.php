<?php
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Mailjet\Resources;

try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $bdd = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die('Erreur de connexion à la base.');
}

function envoyerMailValidationChauffeur(string $email, string $nomComplet): bool
{
    $mj = new \Mailjet\Client(
        MAILJET_PUBLIC_KEY,
        MAILJET_PRIVATE_KEY,
        true,
        ['version' => 'v3.1']
    );

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => MAIL_FROM_EMAIL,
                    'Name' => MAIL_FROM_NAME,
                ],
                'To' => [
                    [
                        'Email' => $email,
                        'Name' => $nomComplet,
                    ],
                ],
                'Subject' => 'Validation de votre profil chauffeur',
                'TextPart' => "Bonjour {$nomComplet}, votre profil chauffeur a été validé. Vous pouvez désormais proposer des trajets de covoiturage sur EasyTravel.",
                'HTMLPart' => "
                    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #222;'>
                        <h2 style='color: green;'>Bonjour " . htmlspecialchars($nomComplet) . ",</h2>
                        <p>Nous avons le plaisir de vous informer que votre profil <strong>chauffeur covoiturage</strong> a été validé.</p>
                        <p>Vous pouvez désormais proposer vos trajets sur la plateforme EasyTravel.</p>
                        <p>
                            <a href='http://localhost/MonProjet/Authentification/mon_compte.php'
                               style='display:inline-block;padding:12px 20px;background:green;color:#fff;text-decoration:none;border-radius:6px;'>
                               Accéder à mon compte
                            </a>
                        </p>
                        <p>Merci pour votre confiance.</p>
                    </div>
                ",
            ],
        ],
    ];

    try {
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    } catch (Exception $e) {
        return false;
    }
}

function envoyerMailRefusChauffeur(string $email, string $nomComplet): bool
{
    $mj = new \Mailjet\Client(
        MAILJET_PUBLIC_KEY,
        MAILJET_PRIVATE_KEY,
        true,
        ['version' => 'v3.1']
    );

    $body = [
        'Messages' => [
            [
                'From' => [
                    'Email' => MAIL_FROM_EMAIL,
                    'Name' => MAIL_FROM_NAME,
                ],
                'To' => [
                    [
                        'Email' => $email,
                        'Name' => $nomComplet,
                    ],
                ],
                'Subject' => 'Mise à jour de votre demande chauffeur',
                'TextPart' => "Bonjour {$nomComplet}, votre demande chauffeur n’a pas été validée pour le moment. Vous pouvez mettre à jour votre dossier et soumettre une nouvelle demande.",
                'HTMLPart' => "
                    <div style='font-family: Arial, sans-serif; line-height: 1.6; color: #222;'>
                        <h2 style='color: #b91c1c;'>Bonjour " . htmlspecialchars($nomComplet) . ",</h2>
                        <p>Votre demande pour devenir chauffeur n’a pas été validée pour le moment.</p>
                        <p>Vous pouvez corriger ou compléter votre dossier puis soumettre une nouvelle demande.</p>
                        <p>
                            <a href='http://localhost/MonProjet/Authentification/devenir_chauffeur.php'
                               style='display:inline-block;padding:12px 20px;background:#dc2626;color:#fff;text-decoration:none;border-radius:6px;'>
                               Refaire ma demande
                            </a>
                        </p>
                        <p>Merci pour votre compréhension.</p>
                    </div>
                ",
            ],
        ],
    ];

    try {
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success();
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['profile_id'], $_POST['user_id'])) {
    $profileId = (int) $_POST['profile_id'];
    $userId = (int) $_POST['user_id'];
    $action = trim($_POST['action']);

    $userStmt = $bdd->prepare("
        SELECT 
            u.id,
            u.user_name,
            u.user_firstname,
            u.user_mail,
            cp.id AS profile_id
        FROM user u
        INNER JOIN chauffeur_profile cp ON cp.user_id = u.id
        WHERE u.id = :user_id AND cp.id = :profile_id
        LIMIT 1
    ");
    $userStmt->execute([
        ':user_id' => $userId,
        ':profile_id' => $profileId
    ]);
    $targetUser = $userStmt->fetch(PDO::FETCH_ASSOC);

    if ($targetUser) {
        $nomComplet = trim(($targetUser['user_firstname'] ?? '') . ' ' . ($targetUser['user_name'] ?? ''));
        $email = trim($targetUser['user_mail'] ?? '');

        try {
            $bdd->beginTransaction();

            if ($action === 'valider') {
                $stmt = $bdd->prepare("
                    UPDATE chauffeur_profile
                    SET statut_validation = 'valide'
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $profileId]);

                $stmt = $bdd->prepare("
                    UPDATE user
                    SET role = 'client_chauffeur'
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $userId]);

                $notif = $bdd->prepare("
                    INSERT INTO notifications (user_id, type_notification, titre, message, lien)
                    VALUES (:user_id, 'validation_chauffeur', :titre, :message, :lien)
                ");
                $notif->execute([
                    ':user_id' => $userId,
                    ':titre' => 'Profil chauffeur validé',
                    ':message' => 'Votre profil chauffeur a été validé. Vous pouvez maintenant proposer des trajets.',
                    ':lien' => '/MonProjet/Authentification/mon_compte.php'
                ]);

                $bdd->commit();

                $mailOk = false;
                if ($email !== '') {
                    $mailOk = envoyerMailValidationChauffeur($email, $nomComplet !== '' ? $nomComplet : 'Utilisateur');
                }

                $_SESSION['message'] = $mailOk
                    ? "Le chauffeur a été validé et l'email de confirmation a bien été envoyé."
                    : "Le chauffeur a été validé, mais l'email de confirmation n'a pas pu être envoyé.";
            }

            if ($action === 'refuser') {
                $stmt = $bdd->prepare("
                    UPDATE chauffeur_profile
                    SET statut_validation = 'refuse'
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $profileId]);

                $stmt = $bdd->prepare("
                    UPDATE user
                    SET role = 'client'
                    WHERE id = :id
                ");
                $stmt->execute([':id' => $userId]);

                $notif = $bdd->prepare("
                    INSERT INTO notifications (user_id, type_notification, titre, message, lien)
                    VALUES (:user_id, 'refus_chauffeur', :titre, :message, :lien)
                ");
                $notif->execute([
                    ':user_id' => $userId,
                    ':titre' => 'Profil chauffeur refusé',
                    ':message' => 'Votre demande chauffeur a été refusée. Vous pouvez corriger votre dossier et refaire une demande.',
                    ':lien' => '/MonProjet/Authentification/devenir_chauffeur.php'
                ]);

                $bdd->commit();

                $mailOk = false;
                if ($email !== '') {
                    $mailOk = envoyerMailRefusChauffeur($email, $nomComplet !== '' ? $nomComplet : 'Utilisateur');
                }

                $_SESSION['message'] = $mailOk
                    ? "La demande a été refusée et l'email a bien été envoyé."
                    : "La demande a été refusée, mais l'email n'a pas pu être envoyé.";
            }
        } catch (PDOException $e) {
            if ($bdd->inTransaction()) {
                $bdd->rollBack();
            }
            $_SESSION['message'] = "Une erreur est survenue lors du traitement de la demande.";
        }
    } else {
        $_SESSION['message'] = "Demande introuvable.";
    }

    header('Location: chauffeurs.php');
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
        cp.*,
        u.user_name,
        u.user_firstname,
        u.user_mail,
        u.user_phone
    FROM chauffeur_profile cp
    INNER JOIN user u ON u.id = cp.user_id
    ORDER BY cp.created_at DESC
");
$chauffeurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<div class="max-w-7xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Demandes chauffeurs</h1>
    </div>

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
                        <th class="px-4 py-4">Nom</th>
                        <th class="px-4 py-4">Email</th>
                        <th class="px-4 py-4">Téléphone</th>
                        <th class="px-4 py-4">Véhicule</th>
                        <th class="px-4 py-4">Places</th>
                        <th class="px-4 py-4">Statut</th>
                        <th class="px-4 py-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chauffeurs as $chauffeur): ?>
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-4 font-medium">
                                <?= htmlspecialchars(trim(($chauffeur['user_firstname'] ?? '') . ' ' . ($chauffeur['user_name'] ?? ''))) ?>
                            </td>
                            <td class="px-4 py-4"><?= htmlspecialchars($chauffeur['user_mail'] ?? '') ?></td>
                            <td class="px-4 py-4"><?= htmlspecialchars((string) ($chauffeur['user_phone'] ?? '')) ?></td>
                            <td class="px-4 py-4">
                                <?= htmlspecialchars(trim(($chauffeur['marque_vehicule'] ?? '') . ' ' . ($chauffeur['modele_vehicule'] ?? ''))) ?><br>
                                <span class="text-gray-500"><?= htmlspecialchars($chauffeur['immatriculation'] ?? '') ?></span>
                            </td>
                            <td class="px-4 py-4"><?= (int) ($chauffeur['nombre_places'] ?? 0) ?></td>
                            <td class="px-4 py-4">
                                <?php
                                $statut = $chauffeur['statut_validation'] ?? 'en_attente';
                                $classes = 'bg-yellow-100 text-yellow-800';
                                if ($statut === 'valide') $classes = 'bg-green-100 text-green-700';
                                if ($statut === 'refuse') $classes = 'bg-red-100 text-red-700';
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold <?= $classes ?>">
                                    <?= htmlspecialchars($statut) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap gap-2">
                                    <?php if (($chauffeur['statut_validation'] ?? '') !== 'valide'): ?>
                                        <form method="post">
                                            <input type="hidden" name="profile_id" value="<?= (int) $chauffeur['id'] ?>">
                                            <input type="hidden" name="user_id" value="<?= (int) $chauffeur['user_id'] ?>">
                                            <button type="submit" name="action" value="valider" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">
                                                Valider
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (($chauffeur['statut_validation'] ?? '') !== 'refuse'): ?>
                                        <form method="post">
                                            <input type="hidden" name="profile_id" value="<?= (int) $chauffeur['id'] ?>">
                                            <input type="hidden" name="user_id" value="<?= (int) $chauffeur['user_id'] ?>">
                                            <button type="submit" name="action" value="refuser" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                                Refuser
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($chauffeurs)): ?>
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Aucune demande chauffeur disponible.
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

$adminTitle = 'Demandes chauffeurs';
$adminWelcome = 'Gestion des demandes chauffeurs';
$adminUserName = 'Alex Stephane';
$baseUrl = '/MonProjet/Admins/';

include __DIR__ . '/../includes/layoutadmin.php';