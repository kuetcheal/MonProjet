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
                                src="https://img.freepik.com/photos-premium/transport-tourisme-voyage-route-concept-personnes-groupe-passagers-touristes-heureux-bus-voyage_380164-170876.jpg"
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