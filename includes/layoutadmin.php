<?php
$adminTitle = $adminTitle ?? 'Administration';
$adminContent = $adminContent ?? '';
$baseUrl = $baseUrl ?? '/MonProjet/Admins/';
$adminUserName = $adminUserName ?? 'Alex Stephane';
$adminWelcome = $adminWelcome ?? 'Bienvenue dans votre espace Administrateur !';
$adminNotificationCount = $adminNotificationCount ?? 0;

$currentPage = basename($_SERVER['PHP_SELF']);

function isActiveAdminLink($fileName, $currentPage)
{
    return $fileName === $currentPage
        ? 'bg-green-700 text-white'
        : 'text-gray-200 hover:bg-gray-800 hover:text-white';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($adminTitle) ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="min-h-screen bg-gray-100">

    <div class="flex min-h-screen">
        <aside class="w-72 bg-gray-900 text-white min-h-screen flex flex-col justify-between shadow-2xl">
            <div>
                <div class="flex items-center mb-6 px-5 pt-6">
                    <span class="text-green-400 text-2xl font-bold">⚡ Général Voyage</span>
                </div>

                <nav class="flex-1 px-3">
                    <h2 class="text-gray-400 text-xs uppercase tracking-wider mb-2 px-3">Gestion principale</h2>

                    <ul class="space-y-1 mb-6">
                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>Accueiladmin.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('Accueiladmin.php', $currentPage) ?>">
                                <i class="fas fa-home w-5"></i>
                                <span class="ml-3">Tableau de bord</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>listevoyage.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('listevoyage.php', $currentPage) ?>">
                                <i class="fas fa-bus w-5"></i>
                                <span class="ml-3">Voyages</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>covoiturages.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('covoiturages.php', $currentPage) ?>">
                                <i class="fas fa-car-side w-5"></i>
                                <span class="ml-3">Covoiturages</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>reservations.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('reservations.php', $currentPage) ?>">
                                <i class="fas fa-folder w-5"></i>
                                <span class="ml-3">Réservations</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>utilisateurs.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('utilisateurs.php', $currentPage) ?>">
                                <i class="fas fa-users w-5"></i>
                                <span class="ml-3">Utilisateurs</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>chauffeurs.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('chauffeurs.php', $currentPage) ?>">
                                <i class="fas fa-id-card w-5"></i>
                                <span class="ml-3">Demandes chauffeurs</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>notifications.php"
                               class="flex items-center px-3 py-3 rounded-lg justify-between transition <?= isActiveAdminLink('notifications.php', $currentPage) ?>">
                                <span class="flex items-center">
                                    <i class="fas fa-bell w-5"></i>
                                    <span class="ml-3">Notifications</span>
                                </span>

                                <?php if ($adminNotificationCount > 0): ?>
                                    <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        <?= (int) $adminNotificationCount ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>destinations.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('destinations.php', $currentPage) ?>">
                                <i class="fas fa-map-marker-alt w-5"></i>
                                <span class="ml-3">Destinations</span>
                            </a>
                        </li>
                    </ul>

                    <h2 class="text-gray-400 text-xs uppercase tracking-wider mb-2 px-3">Ressources financières</h2>

                    <ul class="space-y-1">
                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>finances.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('finances.php', $currentPage) ?>">
                                <i class="fas fa-chart-line w-5"></i>
                                <span class="ml-3">Vue d’ensemble</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>transactions.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('transactions.php', $currentPage) ?>">
                                <i class="fas fa-money-check-dollar w-5"></i>
                                <span class="ml-3">Transactions</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>depenses.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('depenses.php', $currentPage) ?>">
                                <i class="fas fa-wallet w-5"></i>
                                <span class="ml-3">Dépenses</span>
                            </a>
                        </li>

                        <li>
                            <a href="<?= htmlspecialchars($baseUrl) ?>rapports_financiers.php"
                               class="flex items-center px-3 py-3 rounded-lg transition <?= isActiveAdminLink('rapports_financiers.php', $currentPage) ?>">
                                <i class="fas fa-file-invoice-dollar w-5"></i>
                                <span class="ml-3">Rapports & exports</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="mt-auto px-3 pb-6 pt-6">
                <h2 class="text-gray-400 text-xs uppercase mb-2">Administration</h2>

                <a href="#" class="flex items-center px-3 py-3 rounded-lg text-gray-200 hover:bg-gray-800 transition">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Paramètres</span>
                </a>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-h-screen">
            <header class="bg-white p-4 flex items-center justify-between shadow">
                <div class="text-xl text-green-700 font-bold">
                    <p><?= htmlspecialchars($adminWelcome) ?></p>
                </div>

                <div class="flex items-center space-x-5">
                    <a href="<?= htmlspecialchars($baseUrl) ?>notifications.php" class="relative text-gray-600 hover:text-gray-900">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($adminNotificationCount > 0): ?>
                            <span class="absolute -top-2 -right-3 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">
                                <?= (int) $adminNotificationCount ?>
                            </span>
                        <?php endif; ?>
                    </a>

                    <div class="flex items-center space-x-2">
                        <img src="/MonProjet/pictures/OIP.jpg" class="rounded-full w-9 h-9 object-cover" alt="User">
                        <span class="text-gray-700 font-bold text-lg"><?= htmlspecialchars($adminUserName) ?></span>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-6">
                <?= $adminContent ?>
            </main>

            <footer class="bg-gray-700 text-white text-center py-4 w-full">
                <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage !</p>
            </footer>
        </div>
    </div>

</body>
</html>