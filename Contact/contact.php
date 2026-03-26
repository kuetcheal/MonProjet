<?php
session_start();

$agences = [];

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $query = 'SELECT Nom_ville FROM destination ORDER BY Nom_ville ASC';
    $stmt = $bdd->query($query);
    $agences = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $agences = [];
}

ob_start();
?>

<section class="relative overflow-hidden bg-gradient-to-br from-green-50 via-white to-emerald-50">
    <div class="absolute inset-0 opacity-30 pointer-events-none">
        <div class="absolute top-0 left-0 w-72 h-72 bg-green-200 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-emerald-200 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div>
                <span class="inline-flex items-center rounded-full bg-green-100 text-green-700 px-4 py-1 text-sm font-semibold">
                    EasyTravel • Contact Agence
                </span>

                <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">
                    Contactez notre agence et
                    <span class="text-green-600">préparez votre voyage</span>
                    en toute sérénité
                </h1>

                <p class="mt-6 text-lg text-slate-600 leading-8 max-w-2xl">
                    Une question sur un trajet, une réservation, un départ ou une destination ?
                    Notre équipe vous répond rapidement et vous accompagne pour trouver la meilleure solution.
                </p>

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white/90 backdrop-blur  shadow-md border border-green-100 p-5">
                        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-700 mb-3">
                            <i class="fa fa-phone text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">Réponse rapide</h3>
                        <p class="text-sm text-slate-600 mt-1">Notre équipe traite vos demandes dans les meilleurs délais.</p>
                    </div>

                    <div class="bg-white/90 backdrop-blur  shadow-md border border-green-100 p-5">
                        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-700 mb-3">
                            <i class="fa fa-map-marker text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">Agences disponibles</h3>
                        <p class="text-sm text-slate-600 mt-1">Choisissez facilement la ville ou l’agence qui vous convient.</p>
                    </div>

                    <div class="bg-white/90 backdrop-blur rounded-2xl shadow-md border border-green-100 p-5">
                        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center text-green-700 mb-3">
                            <i class="fa fa-bus text-lg"></i>
                        </div>
                        <h3 class="font-bold text-slate-800">Assistance voyage</h3>
                        <p class="text-sm text-slate-600 mt-1">Réservations, horaires, trajets et renseignements en un seul endroit.</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <div class="relative rounded-[8px] overflow-hidden shadow-2xl min-h-[380px] lg:min-h-[520px]">
                    <img
                        src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80"
                        alt="Contact EasyTravel"
                        class="absolute inset-0 w-full h-full object-cover"
                    >
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>

                    <div class="absolute bottom-0 left-0 right-0 p-8">
                        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 text-white">
                            <p class="uppercase tracking-[0.2em] text-xs text-green-200 mb-3">Service client</p>
                            <h2 class="text-2xl font-bold mb-2">Voyagez avec confiance</h2>
                            <p class="text-white/85 leading-7">
                                Nous vous accompagnons avant, pendant et après votre réservation pour une expérience fluide et rassurante.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:block absolute -bottom-6 -left-6 bg-white shadow-xl rounded-2xl px-5 py-4 border border-green-100">
                    <p class="text-sm text-slate-500">Agence EasyTravel</p>
                    <p class="text-lg font-bold text-slate-800">Toujours proche de vous</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-16 bg-[#f8fafc]">
    <div class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="overflow-hidden shadow-2xl">
                    <div class="grid grid-cols-1 lg:grid-cols-2">
                        <div class="bg-[#008000] p-8 sm:p-10">
                            <span class="text-green-100 font-semibold uppercase tracking-wide text-sm">
                                Formulaire de contact
                            </span>

                            <h2 class="text-3xl font-extrabold text-white mt-3 mb-4">
                                Parlez-nous de votre besoin
                            </h2>

                            <p class="text-white/85 leading-7 mb-8">
                                Remplissez ce formulaire et notre équipe vous recontactera rapidement avec une réponse adaptée à votre demande.
                            </p>

                            <form action="" method="POST" class="space-y-5">
                                <div>
                                    <label class="block text-white font-medium mb-2">Votre nom</label>
                                    <input
                                        type="text"
                                        name="name"
                                        placeholder="Alex Kuetche"
                                        class="w-full rounded-xl px-4 py-3 outline-none text-slate-700 border border-white/20 focus:ring-2 focus:ring-green-200"
                                    >
                                </div>

                                <div>
                                    <label class="block text-white font-medium mb-2">Numéro téléphone</label>
                                    <input
                                        type="text"
                                        name="telephone"
                                        placeholder="655196254"
                                        class="w-full rounded-xl px-4 py-3 outline-none text-slate-700 border border-white/20 focus:ring-2 focus:ring-green-200"
                                    >
                                </div>

                                <div>
                                    <label class="block text-white font-medium mb-2">Adresse email</label>
                                    <input
                                        type="email"
                                        name="gmail"
                                        placeholder="name@example.com"
                                        class="w-full rounded-xl px-4 py-3 outline-none text-slate-700 border border-white/20 focus:ring-2 focus:ring-green-200"
                                    >
                                </div>

                                <div>
                                    <label class="block text-white font-medium mb-2">Choisir l'agence</label>
                                    <select
                                        name="choix"
                                        class="w-full rounded-xl px-4 py-3 outline-none text-slate-700 border border-white/20 bg-white focus:ring-2 focus:ring-green-200"
                                    >
                                        <option value="">Sélectionnez une agence</option>
                                        <?php foreach ($agences as $agence): ?>
                                            <option value="<?= htmlspecialchars($agence) ?>">
                                                <?= htmlspecialchars($agence) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-white font-medium mb-2">Votre message</label>
                                    <textarea
                                        name="message"
                                        rows="5"
                                        placeholder="Décrivez votre demande, votre trajet ou votre préoccupation..."
                                        class="w-full rounded-xl px-4 py-3 outline-none text-slate-700 border border-white/20 focus:ring-2 focus:ring-green-200 resize-none"
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full bg-white text-green-700 hover:bg-green-50 font-bold py-3.5 rounded-xl transition duration-300 shadow-md"
                                >
                                    Envoyer mon message
                                </button>
                            </form>
                        </div>

                        <div class="relative min-h-[320px] lg:min-h-full">
                            <img
                                src="https://images.unsplash.com/photo-1502920917128-1aa500764cbd?auto=format&fit=crop&w=1200&q=80"
                                alt="Agence voyage"
                                class="absolute inset-0 w-full h-full object-cover"
                            >
                            <div class="absolute inset-0 bg-black/35"></div>

                            <div class="absolute inset-x-0 bottom-0 p-8">
                                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/20 text-white">
                                    <h3 class="text-2xl font-bold mb-3">Une équipe à votre écoute</h3>
                                    <p class="text-white/85 leading-7">
                                        Que ce soit pour une réservation, une information sur un départ ou un besoin spécifique,
                                        nous sommes disponibles pour vous aider.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white  shadow-lg p-7 border border-gray-100">
                    <h3 class="text-2xl font-bold text-slate-800 mb-5">Informations utiles</h3>

                    <div class="space-y-5">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                                <i class="fa fa-map-marker text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">Nos agences</p>
                                <p class="text-slate-600 text-sm leading-6">
                                    Retrouvez EasyTravel dans plusieurs villes pour faciliter vos départs et vos réservations.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                                <i class="fa fa-phone text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">Téléphone</p>
                                <p class="text-slate-600 text-sm">+237 6 55 19 62 54</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                                <i class="fa fa-envelope text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">Email</p>
                                <p class="text-slate-600 text-sm">contact@easytravel.com</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 text-green-700 flex items-center justify-center shrink-0">
                                <i class="fa fa-clock-o text-lg"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-slate-800">Horaires</p>
                                <p class="text-slate-600 text-sm">Lundi - Samedi : 07h00 à 19h00</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-slate-900 to-slate-800  shadow-lg p-7 text-white">
                    <p class="text-green-300 uppercase tracking-widest text-xs font-semibold mb-3">
                        Pourquoi nous écrire ?
                    </p>
                    <h3 class="text-2xl font-bold mb-4">Un accompagnement simple et humain</h3>
                    <ul class="space-y-3 text-white/85 leading-7">
                        <li class="flex gap-3">
                            <span class="text-green-400 mt-1">✓</span>
                            <span>Informations sur les trajets et horaires</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-green-400 mt-1">✓</span>
                            <span>Aide pour les réservations et billets</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-green-400 mt-1">✓</span>
                            <span>Orientation vers l’agence la plus adaptée</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="text-green-400 mt-1">✓</span>
                            <span>Réponses personnalisées à vos préoccupations</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
$title = 'Contact - EasyTravel';
$content = ob_get_clean();
include __DIR__ . '/../layouts/default.php';
?>