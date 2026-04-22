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
    SELECT 
        u.*,
        cp.statut_validation,
        cp.marque_vehicule,
        cp.modele_vehicule,
        cp.immatriculation,
        cp.nombre_places
    FROM user u
    LEFT JOIN chauffeur_profile cp ON cp.user_id = u.id
    WHERE u.id = :id
    LIMIT 1
");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_unset();
    session_destroy();
    header('Location: /MonProjet/connexion.php');
    exit;
}

$role = $user['role'] ?? 'client';
$statutValidation = $user['statut_validation'] ?? null;

function getRoleLabel(string $role, ?string $statutValidation): string
{
    if ($role === 'admin') {
        return 'Administrateur';
    }

    if ($role === 'client_chauffeur' || $role === 'chauffeur') {
        if ($statutValidation === 'valide') {
            return 'Client / Chauffeur validé';
        }

        if ($statutValidation === 'en_attente') {
            return 'Client / Chauffeur en attente';
        }

        if ($statutValidation === 'refuse') {
            return 'Client / Chauffeur refusé';
        }

        return 'Client / Chauffeur';
    }

    return 'Client';
}

function getDriverStatusLabel(?string $statutValidation): string
{
    if ($statutValidation === 'valide') {
        return 'Profil chauffeur validé';
    }

    if ($statutValidation === 'en_attente') {
        return 'Demande chauffeur en attente';
    }

    if ($statutValidation === 'refuse') {
        return 'Demande chauffeur refusée';
    }

    return 'Aucun profil chauffeur actif';
}

function getDriverStatusClasses(?string $statutValidation): string
{
    if ($statutValidation === 'valide') {
        return 'bg-green-100 text-green-700 border border-green-200';
    }

    if ($statutValidation === 'en_attente') {
        return 'bg-yellow-100 text-yellow-800 border border-yellow-200';
    }

    if ($statutValidation === 'refuse') {
        return 'bg-red-100 text-red-700 border border-red-200';
    }

    return 'bg-gray-100 text-gray-700 border border-gray-200';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 min-h-screen">
    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="max-w-6xl mx-auto px-4 py-10">
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-green-700">Mon compte</h1>
                    <p class="text-slate-500 mt-2">
                        Bienvenue <?= htmlspecialchars($user['user_firstname'] ?? '') ?>, gérez ici votre profil et vos actions.
                    </p>
                </div>

                <div class="text-sm">
                    <span class="inline-flex items-center px-4 py-2 rounded-full font-semibold <?= getDriverStatusClasses($statutValidation) ?>">
                        <?= htmlspecialchars(getDriverStatusLabel($statutValidation)) ?>
                    </span>
                </div>
            </div>

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

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-1 rounded-xl border border-slate-200 p-5 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Informations personnelles</h2>

                    <div class="space-y-3 text-slate-700">
                        <p><span class="font-semibold">Nom :</span> <?= htmlspecialchars($user['user_name'] ?? '') ?></p>
                        <p><span class="font-semibold">Prénom :</span> <?= htmlspecialchars($user['user_firstname'] ?? '') ?></p>
                        <p><span class="font-semibold">Email :</span> <?= htmlspecialchars($user['user_mail'] ?? '') ?></p>
                        <p><span class="font-semibold">Téléphone :</span> <?= htmlspecialchars((string) ($user['user_phone'] ?? '')) ?></p>
                        <p><span class="font-semibold">Type de compte :</span> <?= htmlspecialchars(getRoleLabel($role, $statutValidation)) ?></p>
                        <p><span class="font-semibold">Statut du compte :</span> <?= htmlspecialchars($user['account_status'] ?? 'active') ?></p>
                    </div>
                </div>

                <div class="xl:col-span-1 rounded-xl border border-slate-200 p-5 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Profil chauffeur</h2>

                    <?php if ($statutValidation !== null): ?>
                        <div class="space-y-3 text-slate-700">
                            <p><span class="font-semibold">Statut :</span> <?= htmlspecialchars(getDriverStatusLabel($statutValidation)) ?></p>
                            <p><span class="font-semibold">Véhicule :</span>
                                <?= htmlspecialchars(trim(($user['marque_vehicule'] ?? '') . ' ' . ($user['modele_vehicule'] ?? ''))) ?: '—' ?>
                            </p>
                            <p><span class="font-semibold">Immatriculation :</span> <?= htmlspecialchars($user['immatriculation'] ?? '—') ?></p>
                            <p><span class="font-semibold">Places déclarées :</span> <?= htmlspecialchars((string) ($user['nombre_places'] ?? '—')) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="rounded-lg bg-gray-100 text-gray-700 border border-gray-200 px-4 py-4">
                            Vous n’avez pas encore créé de profil chauffeur.
                        </div>
                    <?php endif; ?>
                </div>

                <div class="xl:col-span-1 rounded-xl border border-slate-200 p-5 bg-slate-50">
                    <h2 class="text-lg font-semibold text-slate-800 mb-4">Actions disponibles</h2>

                    <div class="flex flex-col gap-3">
                        <a href="/MonProjet/reservations.php" class="bg-green-600 hover:bg-green-700 text-white text-center px-5 py-3 rounded-lg font-medium transition">
                            Mes réservations
                        </a>

                        <?php if ($role === 'client' && $statutValidation === null): ?>
                            <a href="/MonProjet/Authentification/devenir_chauffeur.php" class="bg-blue-600 hover:bg-blue-700 text-white text-center px-5 py-3 rounded-lg font-medium transition">
                                Devenir chauffeur
                            </a>
                        <?php endif; ?>

                        <?php if (($role === 'client_chauffeur' || $role === 'chauffeur') && $statutValidation === 'valide'): ?>
                            <a href="/MonProjet/Authentification/proposer_trajet.php" class="bg-indigo-600 hover:bg-indigo-700 text-white text-center px-5 py-3 rounded-lg font-medium transition">
                                Proposer un trajet
                            </a>

                            <a href="/MonProjet/offres/offres.php" class="bg-slate-800 hover:bg-slate-900 text-white text-center px-5 py-3 rounded-lg font-medium transition">
                                Mes trajets proposés
                            </a>

                            <div class="rounded-lg bg-green-100 text-green-700 border border-green-200 px-4 py-3 text-sm leading-6">
                                Votre profil chauffeur est validé. Vous pouvez désormais proposer des trajets covoiturage.
                            </div>
                        <?php elseif (($role === 'client_chauffeur' || $role === 'chauffeur') && $statutValidation === 'en_attente'): ?>
                            <div class="rounded-lg bg-yellow-100 text-yellow-800 border border-yellow-200 px-4 py-3">
                                Votre demande chauffeur est en attente de validation.
                            </div>
                        <?php elseif (($role === 'client_chauffeur' || $role === 'chauffeur') && $statutValidation === 'refuse'): ?>
                            <div class="rounded-lg bg-red-100 text-red-700 border border-red-200 px-4 py-3">
                                Votre demande chauffeur a été refusée. Vous pouvez soumettre une nouvelle demande.
                            </div>

                            <a href="/MonProjet/Authentification/devenir_chauffeur.php" class="bg-blue-600 hover:bg-blue-700 text-white text-center px-5 py-3 rounded-lg font-medium transition">
                                Refaire une demande chauffeur
                            </a>
                        <?php elseif ($role === 'client' && $statutValidation !== null && $statutValidation !== 'valide'): ?>
                            <a href="/MonProjet/Authentification/devenir_chauffeur.php" class="bg-blue-600 hover:bg-blue-700 text-white text-center px-5 py-3 rounded-lg font-medium transition">
                                Compléter ma demande chauffeur
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mt-8 rounded-xl border border-slate-200 p-5 bg-white">
                <h2 class="text-lg font-semibold text-slate-800 mb-3">Résumé de votre statut</h2>
                <p class="text-slate-600 leading-7">
                    <?php if (($role === 'client_chauffeur' || $role === 'chauffeur') && $statutValidation === 'valide'): ?>
                        Vous êtes actuellement un chauffeur validé sur la plateforme. Vous pouvez proposer des trajets covoiturage, attendre leur validation par l’administrateur puis les gérer depuis votre espace.
                    <?php elseif (($role === 'client_chauffeur' || $role === 'chauffeur') && $statutValidation === 'en_attente'): ?>
                        Votre demande de profil chauffeur a bien été enregistrée. Elle sera examinée par l’administrateur avant activation.
                    <?php elseif (($role === 'client_chauffeur' || $role === 'chauffeur') && $statutValidation === 'refuse'): ?>
                        Votre dossier chauffeur a été refusé. Vous pouvez mettre à jour vos informations et déposer une nouvelle demande.
                    <?php else: ?>
                        Vous utilisez actuellement un compte client classique. Vous pouvez réserver vos trajets et, si vous le souhaitez, demander à devenir chauffeur covoiturage.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>