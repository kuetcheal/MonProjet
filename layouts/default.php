<?php
$title = $title ?? 'MonProjet';
$content = $content ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-[#f4f4f4] flex flex-col">

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="flex-1">
        <?= $content ?>
    </main>

    <?php
    $scrollPath = __DIR__ . '/../includes/scrollToUp.php';
    if (file_exists($scrollPath)) {
        include $scrollPath;
    }
    ?>

    <?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>