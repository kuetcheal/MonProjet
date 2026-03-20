<?php
$adminTitle = $adminTitle ?? 'Administration';
$adminContent = $adminContent ?? '';
$baseUrl = $baseUrl ?? '';
$adminUserName = $adminUserName ?? 'Alex Stephane';
$adminWelcome = $adminWelcome ?? 'Bienvenu dans votre espace Administrateur ! ! !';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($adminTitle) ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body class="min-h-screen bg-gray-100">

    <div class="flex min-h-screen">
        <!-- SIDEBAR ADMIN -->
        <aside class="w-60 bg-gray-900 text-white min-h-screen flex flex-col justify-between">
            <div>
                <div class="flex items-center mb-6 px-4 pt-6">
                    <span class="text-indigo-400 text-2xl font-bold">⚡ Général</span>
                </div>

                <nav class="flex-1 px-2">
                    <ul class="space-y-1">
                        <li class="rounded-lg hover:bg-gray-800">
                            <a href="<?= $baseUrl ?>admin.php" class="flex items-center px-3 py-3">
                                <i class="fas fa-home w-5"></i>
                                <span class="ml-3">Trajets</span>
                            </a>
                        </li>

                        <li class="rounded-lg hover:bg-gray-800">
                            <a href="<?= $baseUrl ?>listevoyage.php" class="flex items-center px-3 py-3">
                                <i class="fas fa-bus w-5"></i>
                                <span class="ml-3">Voyages</span>
                            </a>
                        </li>

                        <li class="rounded-lg hover:bg-gray-800">
                            <a href="<?= $baseUrl ?>reservations.php" class="flex items-center px-3 py-3">
                                <i class="fas fa-folder w-5"></i>
                                <span class="ml-3">Reservations</span>
                            </a>
                        </li>

                        <li class="rounded-lg hover:bg-gray-800">
                            <a href="#" class="flex items-center px-3 py-3">
                                <i class="fas fa-calendar w-5"></i>
                                <span class="ml-3">Calendar</span>
                            </a>
                        </li>

                        <li class="rounded-lg hover:bg-gray-800">
                            <a href="#" class="flex items-center px-3 py-3">
                                <i class="fas fa-file-invoice-dollar w-5"></i>
                                <span class="ml-3">Paiement</span>
                            </a>
                        </li>

                        <li class="rounded-lg hover:bg-gray-800">
                            <a href="<?= $baseUrl ?>utilisateurs.php" class="flex items-center px-3 py-3">
                                <i class="fas fa-users w-5"></i>
                                <span class="ml-3">Abonnés</span>
                            </a>
                        </li>

                        <li class="rounded-lg hover:bg-gray-800">
                            <a href="<?= $baseUrl ?>destinations.php" class="flex items-center px-3 py-3">
                                <i class="fas fa-map-marker-alt w-5"></i>
                                <span class="ml-3">Destinations</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="mt-auto px-3 pb-6">
                <h2 class="text-gray-400 text-xs uppercase mb-2">Concernant agence</h2>
                <ul class="space-y-2">
                    <li class="flex items-center py-2">
                        <span class="bg-gray-700 p-2 rounded-full text-xs w-6 h-6 flex items-center justify-center">H</span>
                        <span class="ml-3">destinations</span>
                    </li>
                    <li class="flex items-center py-2">
                        <span class="bg-gray-700 p-2 rounded-full text-xs w-6 h-6 flex items-center justify-center">T</span>
                        <span class="ml-3">Tailwind</span>
                    </li>
                    <li class="flex items-center py-2">
                        <span class="bg-gray-700 p-2 rounded-full text-xs w-6 h-6 flex items-center justify-center">W</span>
                        <span class="ml-3">Workcation</span>
                    </li>
                </ul>

                <a href="#" class="flex items-center px-1 py-3 mt-4">
                    <i class="fas fa-cog w-5"></i>
                    <span class="ml-3">Settings</span>
                </a>
            </div>
        </aside>

        <!-- ZONE DROITE -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- HEADER ADMIN -->
            <header class="bg-white p-4 flex items-center justify-between shadow">
                <div class="text-xl text-green-700 font-bold">
                    <p><?= htmlspecialchars($adminWelcome) ?></p>
                </div>

                <div class="flex items-center space-x-4">
                    <i class="fas fa-bell text-gray-500"></i>
                    <div class="flex items-center space-x-2">
                        <img src="<?= $baseUrl ?>pictures/OIP.jpg" class="rounded-full w-8 h-8 object-cover" alt="User">
                        <span class="text-gray-700 font-bold text-lg"><?= htmlspecialchars($adminUserName) ?></span>
                    </div>
                </div>
            </header>

            <!-- CONTENU PAGE -->
            <main class="flex-1 p-6">
                <?= $adminContent ?>
            </main>

            <footer class="bg-gray-700 text-white text-center py-4 w-full">
                <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!</p>
            </footer>
        </div>
    </div>

</body>
</html>