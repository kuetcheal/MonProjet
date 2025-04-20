<?php
// location-bus.php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Location de bus | EasyTravel</title>
  <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css" />
  <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white font-sans">

  <!-- Section Héro -->
  <section class="relative text-white text-center bg-cover bg-center h-[500px]" style="background-image: url('https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg');">
    <div class="w-full h-full flex items-center justify-center">
      <div class="bg-white text-gray-800 p-6 rounded shadow max-w-xl w-full">
        <h2 class="text-green-600 font-bold text-lg mb-4">Louer un bus au meilleur prix</h2>
        <form class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" class="border border-gray-300 rounded px-4 py-2 w-full" placeholder="Ville de départ *" />
            <input type="text" class="border border-gray-300 rounded px-4 py-2 w-full" placeholder="Ville d'arrivée *" />
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="number" class="border border-gray-300 rounded px-4 py-2 w-full" placeholder="Nombre de passagers *" />
            <select class="border border-gray-300 rounded px-4 py-2 w-full">
              <option>Aller simple</option>
              <option>Aller-retour</option>
            </select>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="date" class="border border-gray-300 rounded px-4 py-2 w-full" />
            <input type="time" class="border border-gray-300 rounded px-4 py-2 w-full" />
          </div>
          <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded w-full">SUIVANT</button>
        </form>
      </div>
    </div>
  </section>

  <!-- Contenu centralisé -->
  <div class="max-w-[1050px] mx-auto">

    <!-- Pourquoi choisir (carousel Flickity) -->
    <section class="text-white text-center py-12">
      <div class="carousel mx-auto" data-flickity='{ "cellAlign": "left", "contain": true, "wrapAround": false, "pageDots": true }'
      >
        <div class="carousel-cell">
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b" alt="City" />
            <div class="px-6 py-4">
              <div class="font-bold text-xl mb-2">New York City</div>
              <p class="text-gray-700 text-base">The Big Apple, featuring stunning architecture and vibrant culture.</p>
            </div>
          </div>
        </div>
        <div class="carousel-cell">
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad" alt="Tokyo" />
            <div class="px-6 py-4">
              <div class="font-bold text-xl mb-2">Tokyo</div>
              <p class="text-gray-700 text-base">A blend of modern technology and traditional culture.</p>
            </div>
          </div>
        </div>
        <div class="carousel-cell">
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e" alt="Paris" />
            <div class="px-6 py-4">
              <div class="font-bold text-xl mb-2">Paris</div>
              <p class="text-gray-700 text-base">The City of Light, known for its romance and iconic landmarks.</p>
            </div>
          </div>
        </div>
        <div class="carousel-cell">
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b" alt="City" />
            <div class="px-6 py-4">
              <div class="font-bold text-xl mb-2">New York City</div>
              <p class="text-gray-700 text-base">The Big Apple, featuring stunning architecture and vibrant culture.</p>
            </div>
          </div>
        </div>
        <div class="carousel-cell">
          <div class="max-w-sm rounded overflow-hidden shadow-lg bg-white">
            <img class="w-full h-48 object-cover" src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad" alt="Tokyo" />
            <div class="px-6 py-4">
              <div class="font-bold text-xl mb-2">Tokyo</div>
              <p class="text-gray-700 text-base">A blend of modern technology and traditional culture.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Services -->
    <section class="py-12">
      <div class="max-w-7xl mx-auto px-4">
        <h3 class="text-center text-green-600 font-bold text-2xl mb-12">Pourquoi choisir EasyTravel ?</h3>
        <div class="space-y-12">
          <div class="md:flex items-start space-x-6">
            <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg" class="w-40 h-28 object-cover rounded mb-4 md:mb-0" alt="Louer un bus" />
            <div>
              <h4 class="text-xl font-bold mb-2">Louer un bus</h4>
              <p class="text-gray-700">Notre plateforme vous permet de louer un bus en fonction de vos besoins avec un bon rapport qualité/prix.</p>
            </div>
          </div>
          <div class="md:flex items-start flex-row-reverse space-x-reverse space-x-6">
            <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg" class="w-40 h-28 object-cover rounded mb-4 md:mb-0" alt="Location avec chauffeur" />
            <div>
              <h4 class="text-xl font-bold mb-2">Location de bus avec chauffeur</h4>
              <p class="text-gray-700">Nous collaborons avec plus de 1200 compagnies pour garantir confort, sécurité et respect de la réglementation.</p>
            </div>
          </div>
          <div class="md:flex items-start space-x-6">
            <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg" class="w-40 h-28 object-cover rounded mb-4 md:mb-0" alt="Adapté à vos besoins" />
            <div>
              <h4 class="text-xl font-bold mb-2">Un véhicule adapté à vos besoins</h4>
              <p class="text-gray-700">Nous proposons des minibus et autocars de toutes tailles, pour des groupes de 8 à 89 passagers.</p>
            </div>
          </div>
          <div class="md:flex items-start flex-row-reverse space-x-reverse space-x-6">
            <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg" class="w-40 h-28 object-cover rounded mb-4 md:mb-0" alt="Tarifs négociés" />
            <div>
              <h4 class="text-xl font-bold mb-2">Des tarifs négociés</h4>
              <p class="text-gray-700">Nos tarifs sont négociés avec nos partenaires pour offrir le meilleur prix.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Appel à l'action -->
    <section class="bg-green-600 text-white text-center py-12">
      <h3 class="text-xl font-bold">Obtenez votre <span class="bg-white text-green-600 px-2 py-1 rounded">devis de location de bus</span><br>avec chauffeur en ligne en 1 minute !</h3>
      <button class="mt-4 bg-white text-green-600 px-6 py-2 rounded font-semibold">DEVIS EN LIGNE &rarr;</button>
    </section>

    <!-- Témoignages -->
    <section class="py-12 bg-white">
      <div class="max-w-7xl mx-auto px-4 text-center">
        <div class="grid md:grid-cols-3 gap-12">
          <!-- Jean -->
          <div>
            <img src="https://images.unsplash.com/photo-1570129477492-45c003edd2be?auto=format&fit=crop&w=150&q=80" class="mx-auto rounded-full w-32 h-32 object-cover mb-4" alt="Jean">
            <h5 class="font-bold text-lg">Jean</h5>
            <p class="text-orange-600 font-semibold">Responsable CE Paris</p>
            <div class="flex justify-center text-orange-400 my-2">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <p class="text-sm text-gray-700 italic max-w-xs mx-auto">Nous avons organisé un déplacement avec et avions besoin de louer un bus pour une manifestation culturelle pour notre CE. Nous les conseillons vivement pour leur professionnalisme.</p>
          </div>
          <!-- Christine -->
          <div>
            <img src="https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=150&q=80" class="mx-auto rounded-full w-32 h-32 object-cover mb-4" alt="Christine">
            <h5 class="font-bold text-lg">Christine</h5>
            <p class="text-orange-600 font-semibold">Directrice d'école</p>
            <div class="flex justify-center text-orange-400 my-2">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <p class="text-sm text-gray-700 italic max-w-xs mx-auto">Sortie scolaire au musée louerunbus.com parfait pour la qualité de service et le sérieux.</p>
          </div>
          <!-- Lydia -->
          <div>
            <img src="https://images.unsplash.com/photo-1603415526960-f7e0328f1093?auto=format&fit=crop&w=150&q=80" class="mx-auto rounded-full w-32 h-32 object-cover mb-4" alt="Lydia">
            <h5 class="font-bold text-lg">Lydia</h5>
            <p class="text-orange-600 font-semibold">Directrice d'association<br>Marseille</p>
            <div class="flex justify-center text-orange-400 my-2">
              <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
            </div>
            <p class="text-sm text-gray-700 italic max-w-xs mx-auto">Parfait pour les tarifs compétitifs et le sérieux de la prestation. Une équipe sympathique et sérieuse.</p>
          </div>
        </div>
      </div>
    </section>

  </div> <!-- fin du conteneur max-w -->

  <!-- Footer -->
  <footer class="bg-gray-900 text-white text-center py-4">
    <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
  </footer>

  <style>
   .carousel {
  margin-left: auto;
  margin-right: auto;
  padding-left: 0;
}

.carousel-cell {
  width: 300px;
  margin-right: 20px;
}


    .flickity-page-dots {
      bottom: -40px;
      text-align: center;
    }

    .flickity-page-dots .dot {
      background: #333;
      width: 10px;
      height: 10px;
      margin: 0 5px;
      border-radius: 50%;
      opacity: 0.5;
      transition: opacity 0.3s ease;
    }

    .flickity-page-dots .dot.is-selected {
      opacity: 1;
    }
  </style>
</body>
</html>
