<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Location de bus - Général Voyage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body style="background-color: aliceblue;">

    <?php include '../includes/topbar.php'; ?>
    <?php include '../includes/header.php'; ?>

    <!-- HERO -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0">
            <img
                src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1600&q=80"
                alt="Bus Général Voyage"
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-black/55"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-24 lg:py-28">
            <div class="max-w-3xl">
                <span class="inline-block bg-white/15 text-white border border-white/20  px-4 py-2 text-sm font-semibold mb-5">
                    Service Premium • Général Voyage
                </span>

                <h1 class="text-white text-3xl sm:text-5xl font-extrabold leading-tight mb-6">
                    Louez un bus confortable et sécurisé pour tous vos déplacements
                </h1>

                <p class="text-white/90 text-base sm:text-lg leading-8 mb-8">
                    Transport de groupe, sorties scolaires, excursions, voyages interurbains, événements privés,
                    missions d’entreprise ou tourisme : Général Voyage met à votre disposition des bus modernes,
                    climatisés et adaptés à vos besoins.
                </p>

                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#reservation"
                       class="inline-flex items-center justify-center bg-[#4CAF50] hover:bg-[#3e8e41] text-white font-semibold px-6 py-3 rounded-md transition">
                        Réserver maintenant
                    </a>

                    <a href="#flotte"
                       class="inline-flex items-center justify-center bg-white/10 hover:bg-white/20 text-white border border-white/20 font-semibold px-6 py-3 rounded-md transition">
                        Voir notre flotte
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- BLOC INFOS RAPIDES -->
    <section class="relative -mt-8 sm:-mt-12 z-10">
        <div class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white  shadow-md p-6 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center text-[#008000] text-2xl">
                        <i class="fa-solid fa-bus"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 mb-2">Flotte moderne</h3>
                    <p class="text-gray-600 text-sm leading-6">
                        Des véhicules confortables, entretenus régulièrement et disponibles pour petits et grands groupes.
                    </p>
                </div>

                <div class="bg-white  shadow-md p-6 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center text-[#008000] text-2xl">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 mb-2">Chauffeurs expérimentés</h3>
                    <p class="text-gray-600 text-sm leading-6">
                        Nos chauffeurs assurent vos trajets avec ponctualité, professionnalisme et sécurité.
                    </p>
                </div>

                <div class="bg-white  shadow-md p-6 text-center">
                    <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-green-100 flex items-center justify-center text-[#008000] text-2xl">
                        <i class="fa-solid fa-route"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-800 mb-2">Trajets sur mesure</h3>
                    <p class="text-gray-600 text-sm leading-6">
                        Une solution flexible pour les mariages, les transferts, les excursions et les voyages professionnels.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- PRESENTATION -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="text-[#008000] font-semibold uppercase tracking-wide text-sm">À propos du service</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-6">
                    Une solution de transport fiable pour vos groupes
                </h2>
                <p class="text-gray-700 leading-8 mb-5">
                    Général Voyage propose un service de location de bus pensé pour les particuliers,
                    les entreprises, les écoles, les associations et les organisateurs d’événements.
                    Notre objectif est simple : vous offrir un transport confortable, sécurisé et ponctuel.
                </p>
                <p class="text-gray-700 leading-8 mb-6">
                    Que vous souhaitiez organiser un transfert vers un aéroport, une sortie touristique,
                    un déplacement professionnel ou un transport pour une cérémonie, nous construisons
                    une offre adaptée à votre itinéraire, à votre nombre de passagers et à votre budget.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-2">Confort à bord</h4>
                        <p class="text-sm text-gray-600">Sièges confortables, climatisation, espace bagages et trajet agréable.</p>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-100">
                        <h4 class="font-bold text-gray-800 mb-2">Organisation simple</h4>
                        <p class="text-sm text-gray-600">Réservation rapide, devis personnalisé et accompagnement avant départ.</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class=" overflow-hidden shadow-lg">
                    <img
                        src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=900&q=80"
                        alt="Intérieur bus"
                        class="w-full h-64 object-cover"
                    >
                </div>
                <div class=" overflow-hidden shadow-lg mt-10">
                    <img
                        src="https://images.unsplash.com/photo-1508057198894-247b23fe5ade?auto=format&fit=crop&w=900&q=80"
                        alt="Voyage groupe"
                        class="w-full h-64 object-cover"
                    >
                </div>
                <div class="col-span-2  overflow-hidden shadow-lg">
                    <img
                        src="https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1200&q=80"
                        alt="Bus sur la route"
                        class="w-full h-72 object-cover"
                    >
                </div>
            </div>
        </div>
    </section>

    <!-- FLOTTE -->
    <section id="flotte" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-[#008000] font-semibold uppercase tracking-wide text-sm">Notre flotte</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">
                    Des véhicules adaptés à chaque besoin
                </h2>
                <p class="text-gray-600 leading-7">
                    Choisissez le type de bus qui correspond le mieux à votre déplacement,
                    à la taille de votre groupe et au niveau de confort souhaité.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 max-w-[1300px]">
                <div class="bg-slate-50  overflow-hidden shadow-md">
                    <img
                        src="https://images.unsplash.com/photo-1517404215738-15263e9f9178?auto=format&fit=crop&w=1200&q=80"
                        alt="Minibus"
                        class="w-full h-56 object-cover"
                    >
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Minibus</h3>
                        <p class="text-sm text-gray-600 mb-4">Idéal pour les petits groupes, excursions privées et transferts rapides.</p>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> 8 à 18 places</li>
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> Climatisation</li>
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> Confort urbain et interurbain</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-slate-50  overflow-hidden shadow-md">
                    <img
                        src="https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?auto=format&fit=crop&w=1200&q=80"
                        alt="Bus standard"
                        class="w-full h-56 object-cover"
                    >
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Bus standard</h3>
                        <p class="text-sm text-gray-600 mb-4">Parfait pour les trajets réguliers, groupes moyens et événements.</p>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> 20 à 40 places</li>
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> Soute à bagages</li>
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> Excellent rapport confort/prix</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-slate-50  overflow-hidden shadow-md">
                    <img
                        src="https://images.unsplash.com/photo-1529074963764-98f45c47344b?auto=format&fit=crop&w=1200&q=80"
                        alt="Autocar premium"
                        class="w-full h-56 object-cover"
                    >
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Autocar premium</h3>
                        <p class="text-sm text-gray-600 mb-4">Conçu pour les longs trajets, le tourisme et les voyages haut de gamme.</p>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> 40 à 60 places</li>
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> Grande capacité bagages</li>
                            <li><i class="fa-solid fa-check text-[#008000] mr-2"></i> Confort supérieur</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- AVANTAGES -->
    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-[#008000] font-semibold uppercase tracking-wide text-sm">Pourquoi nous choisir</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">
                    Un service pensé pour vous faciliter le voyage
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-clock"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-900">Ponctualité</h3>
                    <p class="text-sm text-gray-600 leading-6">Des départs organisés et une gestion rigoureuse de vos horaires.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-shield-heart"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-900">Sécurité</h3>
                    <p class="text-sm text-gray-600 leading-6">Bus entretenus, chauffeurs qualifiés et respect des normes de transport.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-money-bill-wave"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-900">Tarifs adaptés</h3>
                    <p class="text-sm text-gray-600 leading-6">Une offre flexible selon la distance, la durée et le type de véhicule choisi.</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="text-[#008000] text-3xl mb-4"><i class="fa-solid fa-headset"></i></div>
                    <h3 class="font-bold text-lg mb-2 text-gray-900">Assistance</h3>
                    <p class="text-sm text-gray-600 leading-6">Une équipe disponible pour vous guider avant, pendant et après la réservation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- TYPES D'USAGE -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-[#008000] font-semibold uppercase tracking-wide text-sm">Utilisations possibles</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">
                    Pour quels besoins louer un bus ?
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="rounded-2xl bg-green-50 p-6 border border-green-100">
                    <h3 class="font-bold text-lg text-[#008000] mb-3">Sorties scolaires</h3>
                    <p class="text-gray-700 text-sm leading-6">Transport d’élèves, d’enseignants et d’accompagnateurs dans des conditions sûres.</p>
                </div>

                <div class="rounded-2xl bg-green-50 p-6 border border-green-100">
                    <h3 class="font-bold text-lg text-[#008000] mb-3">Mariages et cérémonies</h3>
                    <p class="text-gray-700 text-sm leading-6">Facilitez le transport de vos invités vers le lieu de la réception ou de la cérémonie.</p>
                </div>

                <div class="rounded-2xl bg-green-50 p-6 border border-green-100">
                    <h3 class="font-bold text-lg text-[#008000] mb-3">Tourisme et excursions</h3>
                    <p class="text-gray-700 text-sm leading-6">Organisez des visites touristiques ou des sorties de groupe avec confort et sérénité.</p>
                </div>

                <div class="rounded-2xl bg-green-50 p-6 border border-green-100">
                    <h3 class="font-bold text-lg text-[#008000] mb-3">Entreprises</h3>
                    <p class="text-gray-700 text-sm leading-6">Déplacements professionnels, séminaires, navettes ou transport d’équipes.</p>
                </div>

                <div class="rounded-2xl bg-green-50 p-6 border border-green-100">
                    <h3 class="font-bold text-lg text-[#008000] mb-3">Événements religieux</h3>
                    <p class="text-gray-700 text-sm leading-6">Pèlerinages, rassemblements ou déplacements communautaires organisés facilement.</p>
                </div>

                <div class="rounded-2xl bg-green-50 p-6 border border-green-100">
                    <h3 class="font-bold text-lg text-[#008000] mb-3">Transferts spéciaux</h3>
                    <p class="text-gray-700 text-sm leading-6">Transport vers gares, aéroports, hôtels ou sites événementiels selon vos horaires.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FORMULAIRE -->
    <section id="reservation" class="py-16">
        <div class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#008000]  shadow-xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    <div class="p-8 sm:p-10">
                        <span class="text-green-100 font-semibold uppercase tracking-wide text-sm">Demande de location</span>
                        <h2 class="text-3xl font-extrabold text-white mt-3 mb-4">
                            Demandez votre devis personnalisé
                        </h2>
                        <p class="text-white/85 leading-7 mb-8">
                            Remplissez ce formulaire et notre équipe vous recontactera rapidement avec une proposition adaptée.
                        </p>

                        <form class="space-y-4">
                            <div>
                                <label class="block text-white font-medium mb-2">Nom complet</label>
                                <input type="text" class="w-full rounded-md px-4 py-3 outline-none text-gray-700" placeholder="Votre nom complet">
                            </div>

                            <div>
                                <label class="block text-white font-medium mb-2">Téléphone</label>
                                <input type="text" class="w-full rounded-md px-4 py-3 outline-none text-gray-700" placeholder="Votre numéro">
                            </div>

                            <div>
                                <label class="block text-white font-medium mb-2">Email</label>
                                <input type="email" class="w-full rounded-md px-4 py-3 outline-none text-gray-700" placeholder="Votre email">
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white font-medium mb-2">Lieu de départ</label>
                                    <input type="text" class="w-full rounded-md px-4 py-3 outline-none text-gray-700" placeholder="Ville de départ">
                                </div>

                                <div>
                                    <label class="block text-white font-medium mb-2">Destination</label>
                                    <input type="text" class="w-full rounded-md px-4 py-3 outline-none text-gray-700" placeholder="Ville d'arrivée">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-white font-medium mb-2">Date de départ</label>
                                    <input type="date" class="w-full rounded-md px-4 py-3 outline-none text-gray-700">
                                </div>

                                <div>
                                    <label class="block text-white font-medium mb-2">Nombre de passagers</label>
                                    <input type="number" class="w-full rounded-md px-4 py-3 outline-none text-gray-700" placeholder="Ex : 35">
                                </div>
                            </div>

                            <div>
                                <label class="block text-white font-medium mb-2">Détails du besoin</label>
                                <textarea rows="4" class="w-full rounded-md px-4 py-3 outline-none text-gray-700" placeholder="Décrivez votre besoin..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#4CAF50] hover:bg-[#3e8e41] text-white font-semibold py-3 rounded-md transition">
                                Envoyer ma demande
                            </button>
                        </form>
                    </div>

                    <div class="relative min-h-[320px] lg:min-h-full">
                        <img
                            src="https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80"
                            alt="Voyage groupe"
                            class="absolute inset-0 w-full h-full object-cover"
                        >
                        <div class="absolute inset-0 bg-black/30"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="text-[#008000] font-semibold uppercase tracking-wide text-sm">Questions fréquentes</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-3 mb-4">
                    Ce qu’il faut savoir avant de réserver
                </h2>
            </div>

            <div class="space-y-4">
                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-900 mb-2">Peut-on louer un bus avec chauffeur ?</h3>
                    <p class="text-gray-600 leading-7 text-sm">
                        Oui, Général Voyage propose la location de bus avec chauffeur pour garantir confort, sécurité et tranquillité.
                    </p>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-900 mb-2">Le prix dépend-il de la distance ?</h3>
                    <p class="text-gray-600 leading-7 text-sm">
                        Oui, le tarif varie selon l’itinéraire, la durée de mise à disposition, le type de bus et le nombre de passagers.
                    </p>
                </div>

                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5">
                    <h3 class="font-bold text-gray-900 mb-2">Peut-on réserver pour une journée entière ?</h3>
                    <p class="text-gray-600 leading-7 text-sm">
                        Oui, nous proposons des prestations ponctuelles, journalières ou selon un programme spécifique.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA FINAL -->
    <section class="py-16">
        <div class="max-w-[1300px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class=" bg-gradient-to-r from-[#008000] to-[#0d5d31] p-8 sm:p-10 text-center shadow-xl">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4">
                    Besoin d’un transport fiable pour votre groupe ?
                </h2>
                <p class="text-white/85 max-w-2xl mx-auto leading-8 mb-8">
                    Contactez Général Voyage dès aujourd’hui pour obtenir un devis rapide et organiser votre déplacement
                    dans les meilleures conditions.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="#reservation" class="bg-white text-[#008000] hover:bg-green-50 font-bold px-6 py-3 rounded-md transition">
                        Demander un devis
                    </a>
                    <a href="contact.php" class="bg-[#4CAF50] hover:bg-[#3e8e41] text-white font-bold px-6 py-3 rounded-md transition">
                        Nous contacter
                    </a>
                </div>
            </div>
        </div>
    </section>
    <?php include '../includes/scrollToUp.php' ?>
    <?php include '../includes/footer.php'; ?>

</body>
</html>