<?php
require_once __DIR__ . '/../config.php';

try {
    $stmt = $pdo->prepare("
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

function limiterTexte($texte, $limite = 56)
{
    $texte = trim($texte);

    if (mb_strlen($texte, 'UTF-8') <= $limite) {
        return $texte;
    }

    return mb_substr($texte, 0, $limite, 'UTF-8') . '...';
}
?>

<section class="bg-[#f8f7f7] py-[55px] pb-[95px]">
    <div class="max-w-[1370px] mx-auto px-3">
        <h2 class="text-xl md:text-3xl font-bold pb-5 text-left md:px-5 px-3">
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
                                <div class="text-[#222] text-[1.1rem] font-extrabold mb-[10px] h-[52px]">
                                    <?= htmlspecialchars($actu['titre']) ?>
                                </div>

                                <p class="text-sm mb-3 h-[40px] overflow-hidden">
                                    <span class="block md:hidden">
                                        <?= htmlspecialchars(limiterTexte($actu['resume'], 50)) ?>
                                    </span>

                                    <span class="hidden md:block">
                                        <?= htmlspecialchars($actu['resume']) ?>
                                    </span>
                                </p>

                                <a 
    href="<?= htmlspecialchars((isset($baseUrl) ? rtrim($baseUrl, '/') . '/' : '') . 'article.php?id=' . (int) $actu['id']) ?>" 
    class="text-[#018b01] font-medium underline"
>
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