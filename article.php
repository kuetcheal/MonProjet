<?php
session_start();
require_once __DIR__ . '/config.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$article = null;

if ($id) {
    try {
        $stmt = $pdo->prepare("
            SELECT id, titre, resume, contenu, image_url, date_publication
            FROM actualite
            WHERE id = :id
            AND statut = 'publie'
            LIMIT 1
        ");

        $stmt->execute([
            ':id' => $id
        ]);

        $article = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $article = null;
    }
}

if (!$article) {
    http_response_code(404);
}

function formatDateFr($date)
{
    if (empty($date)) {
        return '';
    }

    return date('d/m/Y', strtotime($date));
}

$contenuArticle = '';

if ($article) {
    $contenuArticle = !empty($article['contenu'])
        ? $article['contenu']
        : $article['resume'];
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $article ? htmlspecialchars($article['titre']) : 'Article introuvable' ?> - Général Voyage
    </title>

    <!-- Tailwind CDN simple -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background-color: aliceblue;
        }
    </style>
</head>

<body class="text-slate-900">

    <?php include __DIR__ . '/includes/topbar.php'; ?>
    <?php include __DIR__ . '/includes/header.php'; ?>

    <?php if ($article): ?>

        <!-- HERO ARTICLE -->
        <section class="relative min-h-[430px] sm:min-h-[480px] lg:min-h-[520px] flex items-end overflow-hidden">
            <div class="absolute inset-0">
                <img
                    src="<?= htmlspecialchars($article['image_url']) ?>"
                    alt="<?= htmlspecialchars($article['titre']) ?>"
                    class="w-full h-full object-cover">

                <div class="absolute inset-0 bg-black/55"></div>
            </div>

            <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 w-full">
                <div class="flex flex-wrap items-center gap-3 mb-6">
                    <a
                        href="Accueil.php"
                        class="inline-flex items-center gap-2 text-white/90 hover:text-white text-sm font-medium">
                        <i class="fa-solid fa-arrow-left"></i>
                        Retour à l'accueil
                    </a>

                    <span class="inline-block bg-white/15 text-white border border-white/20 px-4 py-2 text-sm font-semibold">
                        Actualité Général Voyage
                    </span>
                </div>

                <h1 class="text-white text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight max-w-4xl">
                    <?= htmlspecialchars($article['titre']) ?>
                </h1>

                <?php if (!empty($article['date_publication'])): ?>
                    <p class="text-white/85 mt-5 text-base">
                        Publié le <?= htmlspecialchars(formatDateFr($article['date_publication'])) ?>
                    </p>
                <?php endif; ?>
            </div>
        </section>

        <!-- CONTENU ARTICLE -->
        <main class="py-14">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                <article class="bg-white shadow-md p-6 sm:p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-newspaper"></i>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Général Voyage</p>
                            <p class="font-semibold text-gray-900">Information officielle</p>
                        </div>
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-6">
                        <?= htmlspecialchars($article['titre']) ?>
                    </h2>

                    <div class="text-gray-700 text-base sm:text-lg leading-8 space-y-5">
                        <?= nl2br(htmlspecialchars($contenuArticle)) ?>
                    </div>

                    <div class="border-t border-gray-100 mt-8 pt-6 flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
                        <a
                            href="Accueil.php"
                            class="inline-flex items-center gap-2 text-green-700 font-semibold hover:underline">
                            <i class="fa-solid fa-arrow-left"></i>
                            Voir les autres actualités
                        </a>

                        <a
                            href="formulaire.php"
                            class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-3 rounded-md transition">
                            Réserver un voyage
                        </a>
                    </div>
                </article>
            </div>
        </main>

    <?php else: ?>

        <!-- ARTICLE INTROUVABLE -->
        <main class="min-h-[60vh] flex items-center justify-center px-4 py-16">
            <div class="bg-white shadow-md p-8 max-w-xl text-center">
                <div class="w-16 h-16 mx-auto rounded-full bg-red-100 text-red-600 flex items-center justify-center text-2xl mb-5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <h1 class="text-2xl font-extrabold text-gray-900 mb-3">
                    Article introuvable
                </h1>

                <p class="text-gray-600 mb-6">
                    Cette actualité n’existe pas ou n’est plus disponible.
                </p>

                <a
                    href="Accueil.php"
                    class="inline-flex items-center justify-center bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-3 rounded-md transition">
                    Retour à l'accueil
                </a>
            </div>
        </main>

    <?php endif; ?>

    <?php include __DIR__ . '/includes/scrollToUp.php'; ?>
    <?php include __DIR__ . '/includes/footer.php'; ?>

</body>
</html>