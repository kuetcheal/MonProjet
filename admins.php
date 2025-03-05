<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-screen bg-gray-100 flex flex-col">

    <div class="flex flex-1">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-900 text-white h-screen p-5 flex flex-col">
            <!-- Logo -->
            <div class="flex items-center space-x-2 mb-8">
                <span class="text-indigo-400 text-2xl font-bold">⚡ Général Voyage</span>
            </div>

            <!-- Navigation -->
            <nav class="flex-1">
                <ul class="space-y-2">
                    <li class="bg-gray-800 rounded-lg">
                        <a href="#" class="flex items-center px-4 py-2 space-x-3">
                            <i class="fas fa-home"></i>
                            <span>Menus</span>
                        </a>
                    </li>
                    <li><a href="#" class="flex items-center px-4 py-2 space-x-3"><i class="fas fa-users"></i><span>Voyages</span></a></li>
                    <li><a href="#" class="flex items-center px-4 py-2 space-x-3"><i class="fas fa-folder"></i><span>Reservations</span></a></li>
                    <li><a href="#" class="flex items-center px-4 py-2 space-x-3"><i class="fas fa-calendar"></i><span>Calendar</span></a></li>
                    <li><a href="#" class="flex items-center px-4 py-2 space-x-3"><i class="fas fa-file"></i><span>Paiement</span></a></li>
                    <li><a href="Admins/utilisateurs.php" class="flex items-center px-4 py-2 space-x-3"><i class="fas fa-chart-bar"></i><span>Abonnés</span></a></li>
                    <li><a href="#" class="flex items-center px-4 py-2 space-x-3"><i class="fas fa-users"></i><span>Destinations</span></a></li>
                </ul>
            </nav>

            <!-- Teams -->
            <div class="mt-6">
                <h2 class="text-gray-400 text-sm uppercase">Concernant Agence</h2>
                <ul class="mt-2 space-y-2">
                    <li class="flex items-center space-x-3">
                        <span class="bg-gray-700 p-2 rounded-full text-xs">H</span>
                        <span>Nos destinations</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <span class="bg-gray-700 p-2 rounded-full text-xs">T</span>
                        <span>Tailwind Labs</span>
                    </li>
                    <li class="flex items-center space-x-3">
                        <span class="bg-gray-700 p-2 rounded-full text-xs">W</span>
                        <span>Workcation</span>
                    </li>
                </ul>
            </div>

            <!-- Settings -->
            <div class="mt-auto">
                <a href="#" class="flex items-center px-4 py-2 space-x-3">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </div>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white p-4 flex items-center justify-between shadow">
                <!-- Search Bar -->
                <div class="relative w-1/3">
                    <input type="text" class="w-full pl-10 pr-4 py-2 border rounded-lg focus:ring focus:ring-indigo-300" placeholder="Search">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                </div>

                <!-- User Profile -->
                <div class="flex items-center space-x-4">
                    <i class="fas fa-bell text-gray-500"></i>
                    <div class="flex items-center space-x-2">
                        <img src="pictures/OIP.jpg" class="rounded-full w-8 h-8" alt="User">
                        <span class="text-gray-700">Tom Cook</span>
                    </div>
                </div>
            </header>

            <!-- Content Area -->
            <main class="flex-1 p-6">
                <div class="h-full border-2 border-dashed border-gray-300 bg-gray-50 rounded-lg flex items-center justify-center">
                    <span class="text-gray-400">Content Area</span>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-700 text-white text-center py-4 w-full">
        <p>Merci d'avoir choisi notre service. Nous vous souhaitons un excellent voyage!</p>
    </footer>

    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

</body>

<style>
    .rounded-full {
        font-size: 10px;
    }
</style>
</html>
