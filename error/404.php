<?php
session_start();
http_response_code(404);

ob_start();
?>

<section class="min-h-[70vh] flex items-center bg-[#f8f9fb] py-16">
    <div class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8 w-full">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            
            <!-- Partie gauche -->
            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-green-700 mb-3">
                    Erreur 404
                </p>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold text-green-700 mb-4">
                    Oups !
                </h1>

                <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-4">
                    Cette page n'existe pas (ou plus)
                </h2>

                <p class="text-slate-600 text-lg mb-2">
                    Le lien que vous avez suivi est peut-être incorrect, ou la page a été déplacée.
                </p>

                <p class="text-slate-500 mb-8">
                    Code d’erreur : 404
                </p>

                <div class="flex flex-wrap gap-4">
                    <a
                        href="/MonProjet/Accueil.php"
                        class="inline-flex items-center justify-center px-6 py-3 bg-green-700 hover:bg-green-800 text-white font-semibold rounded-lg transition duration-300"
                    >
                        Retour à l'accueil
                    </a>

                    <a
                        href="/MonProjet/contact/contact.php"
                        class="inline-flex items-center justify-center px-6 py-3 border border-green-700 text-green-700 hover:bg-green-50 font-semibold rounded-lg transition duration-300"
                    >
                        Nous contacter
                    </a>
                </div>
            </div>

            <!-- Partie droite -->
            <div class="flex justify-center">
                <img
                    src="/MonProjet/pictures/error-pnj.webp"
                    alt="Erreur 404"
                    class="max-w-full h-auto object-contain"
                >
            </div>
        </div>
    </div>
</section>

<?php
$title = '404 - Page introuvable';
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>