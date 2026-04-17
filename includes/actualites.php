<?php
try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock;charset=utf8', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $bdd->prepare("
        SELECT id, titre, resume, image_url, lien
        FROM actualite
        WHERE statut = 'publie'
        ORDER BY date_publication DESC, id DESC
        LIMIT 12
    ");
    $stmt->execute();
    $actualites = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $actualites = [];
}
?>

<section class="bg-[#f8f7f7] py-[55px] pb-[95px]">
    <div class="max-w-[1370px] mx-auto px-3">
        <h2 class="text-xl md:text-3xl text-[#177043] font-bold pb-5 text-left md:px-5 px-3">
            Profitez de toutes les dernières actualités de général voyage
        </h2>

        <?php if (!empty($actualites)): ?>
            <div
                class="carousel home-carousel"
                data-flickity='{ "cellAlign": "left", "contain": true, "wrapAround": false, "pageDots": true }'>

                <?php foreach ($actualites as $actu): ?>
                    <div class="carousel-cell w-[240px] sm:w-[260px] md:w-[320px] mr-4 md:mr-5">
                        <div class="bg-white overflow-hidden shadow-[0_10px_25px_rgba(0,0,0,0.1)] h-full">
                            <img
                                src="<?= htmlspecialchars($actu['image_url']) ?>"
                                alt="<?= htmlspecialchars($actu['titre']) ?>"
                                class="w-full h-[150px] md:h-[220px] object-cover block" />

                            <div class="p-[10px]">
                                <div class="text-[#222] text-[1.1rem] font-extrabold mb-[10px]">
                                    <?= htmlspecialchars($actu['titre']) ?>
                                </div>

                                <p class="text-sm mb-3">
                                    <?= htmlspecialchars($actu['resume']) ?>
                                </p>

                                <a href="<?= htmlspecialchars($actu['lien']) ?>" class="text-blue-600 font-medium hover:underline">
                                    Lire l'article
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        <?php else: ?>
            <div class="bg-white shadow-md p-6 md:p-8 text-center text-gray-600">
                Aucune actualité disponible pour le moment.
            </div>
        <?php endif; ?>
    </div>
</section>