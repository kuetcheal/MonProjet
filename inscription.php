<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body style="background-color: aliceblue;">

    <?php include 'includes/header.php'; ?>

    <main class="px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="w-full max-w-xl mx-auto mt-6 sm:mt-10 mb-12 bg-[#008000] rounded-md shadow-lg p-5 sm:p-6 md:p-8">
            <h2 class="text-center text-white text-2xl sm:text-3xl font-bold mb-4">
                Formulaire d'inscription
            </h2>

            <hr class="border-white/30 mb-6">

            <form action="verification.php" method="POST" class="flex flex-col gap-4">
                <div>
                    <label for="nom" class="block text-white font-medium mb-2">
                        Nom :
                    </label>
                    <input
                        type="text"
                        id="nom"
                        name="nom"
                        placeholder="KUETCHE"
                        required
                        class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                    >
                </div>

                <div>
                    <label for="prenom" class="block text-white font-medium mb-2">
                        Prénom :
                    </label>
                    <input
                        type="text"
                        id="prenom"
                        name="prenom"
                        placeholder="ALEX"
                        required
                        class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                    >
                </div>

                <div>
                    <label for="email" class="block text-white font-medium mb-2">
                        Email :
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nom@gmail.com"
                        required
                        class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                    >
                </div>

                <div>
                    <label for="phone" class="block text-white font-medium mb-2">
                        Téléphone :
                    </label>
                    <input
                        type="number"
                        id="phone"
                        name="phone"
                        placeholder="655198412"
                        required
                        class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 placeholder-gray-400 outline-none focus:ring-2 focus:ring-green-300"
                    >
                </div>

                <div>
                    <label for="password" class="block text-white font-medium mb-2">
                        Mot de passe :
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full rounded-md px-4 py-3 border border-gray-300 text-gray-700 outline-none focus:ring-2 focus:ring-green-300"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#4CAF50] hover:bg-[#3e8e41] text-white py-3 rounded-md text-base font-medium transition duration-200 mt-2"
                >
                    S'inscrire
                </button>

                <p class="mt-4 text-white text-base leading-7">
                    Vous avez déjà un compte ?
                    <a href="connexion.php" class="text-white font-bold underline hover:text-green-200 transition">
                        Se connecter
                    </a>
                </p>
            </form>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>

</html>