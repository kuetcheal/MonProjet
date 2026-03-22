<?php
session_start();

ob_start();
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Scanner un ticket</h1>
        <p class="text-gray-600 mb-6">
            Scannez le QR code du client ou collez directement le token du billet.
        </p>

        <form method="GET" action="scan_ticket.php" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Token du billet</label>
                <input
                    type="text"
                    name="token"
                    placeholder="Collez ici le token du QR code"
                    class="w-full border rounded-lg px-4 py-3"
                    required
                >
            </div>

            <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                Vérifier le billet
            </button>
        </form>

        <div class="mt-8 p-4 rounded-lg bg-gray-50 border">
            <p class="font-semibold text-gray-800 mb-2">Fonctionnement conseillé :</p>
            <p class="text-gray-600">
                Le plus simple est d’utiliser un smartphone d’agence pour scanner le QR code.
                Le QR code ouvrira automatiquement la page <strong>scan_ticket.php</strong> avec le token.
            </p>
        </div>
    </div>
</div>

<?php
$adminContent = ob_get_clean();
$adminTitle = 'Scanner ticket';
$adminUserName = 'Alex Stephane';
$adminWelcome = 'Contrôle d’accès des passagers';
$baseUrl = './';

include __DIR__ . '/../includes/layoutadmin.php';
?>