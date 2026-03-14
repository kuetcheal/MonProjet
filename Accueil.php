<?php
session_start();

if (isset($_POST['deconnect_account'])) {
    session_unset();
    session_destroy();
    header('Location: connexion.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css" />
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>

    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="CSS/Accueil.css">

    <title>Accueil</title>
</head>

<body>
    <?php include 'includes/header.php'; ?>
    <?php include_once 'Cookies/cookies.php'; ?>

    <section class="hero-section">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <h1 class="hero-title">
                Profite jusqu'à -20% sur les billets de reservations !
            </h1>

            <p class="hero-subtitle">
                REMISES EXCLUSIVES POUR LES MEMBRES !
                <a href="connexion.php" class="hero-link">
                    CONNECTEZ-VOUS / INSCRIVEZ-VOUS ICI
                </a>
            </p>

  
        </div>

        <div class="search-card-wrapper">
            <h3 class="hero-search-title">Reserver votre voyage</h3>
            <div class="search-card">
                <form action="listevoyageretour.php" method="post" class="search-form">

                    <div class="trip-options">
                        <label class="trip-option">
                            <input type="radio" id="inlineRadio1" name="inlineRadioOptions" value="option1" checked>
                            <span>Aller simple</span>
                        </label>

                        <label class="trip-option">
                            <input type="radio" id="inlineRadio2" name="inlineRadioOptions" value="option2">
                            <span>Aller-Retour</span>
                        </label>
                    </div>

                    <div class="search-fields">
                        <div class="field-group">
                            <label for="input1">
                                <i class="bi bi-geo-alt"></i> DE :
                            </label>
                            <select id="input1" name="input1" class="select2">
                                <?php
                                $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
                                $query = 'SELECT * FROM destination ORDER BY Nom_ville ASC';
                                $response = $bdd->query($query);
                                while ($donnee = $response->fetch()) {
                                    $destination = $donnee['Nom_ville'];
                                    echo '<option value="' . htmlspecialchars($destination) . '">' . htmlspecialchars($destination) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="input2">
                                <i class="bi bi-geo-alt"></i> A :
                            </label>
                            <select id="input2" name="input2" class="select2">
                                <?php
                                $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
                                $query = 'SELECT * FROM destination ORDER BY Nom_ville ASC';
                                $response = $bdd->query($query);
                                while ($donnee = $response->fetch()) {
                                    $destination = $donnee['Nom_ville'];
                                    echo '<option value="' . htmlspecialchars($destination) . '">' . htmlspecialchars($destination) . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="input3">Date départ :</label>
                            <input type="date" id="input3" name="input3">
                        </div>

                        <div class="field-group">
                            <label for="input4">Date retour :</label>
                            <input type="date" id="input4" name="input4" disabled>
                        </div>

                        <div class="field-group submit-group">
                            <label class="fake-label">Valider</label>
                            <input type="submit" value="Valider" class="submit-btn">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <main class="home-main">
        <section class="actions-section">
            <h2 class="actions-title">
                Gérer vos trajets et vos reservations sans soucis grâce à vos identifiants de reservation sur votre billet de voyage.
            </h2>

            <div class="action-buttons">
                <button class="action-btn" id="openModalButton">Gérer ma réservation</button>
                <button class="action-btn">Localiser mon trajet</button>
                <button class="action-btn">Besoin d'aide</button>
            </div>
        </section>

        <div id="modalContainer"></div>

        <section class="map-section">
          
            <div class="map-box">
                <?php include 'map.php'; ?>
            </div>
        </section>

        <section class="presentation-section">
            <div class="presentation-grid">
                <div class="presentation-image-box">
                    <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg"
                        alt="Bus EasyTravel"
                        class="presentation-image">
                </div>

                <div class="presentation-text">
                    <h2>EasyTravel est le plus grand réseau camerounais de transport personnel inter-urbain !</h2>

                    <p>
                        Depuis 2000, <strong>Général Voyage</strong> agrandit continuellement son réseau
                        camerounais et dessert chaque jour plus de 100 destinations dont plus de 30 villes au Cameroun.
                        Notre objectif est de rendre le Cameroun vert !
                        Le réseau de Général Voyage s’étend du Sud à l'Est jusqu’au Grand Nord.
                        Découvrez notre <a href="#">carte interactive</a>
                        ou réservez dès maintenant pour <strong>Yaoundé, Kribi, Bamenda, Edea, Banga</strong> et bien d’autres.
                    </p>

                    <h3>C’est simple et confortable</h3>

                    <p>
                        Voyager n’a jamais été aussi simple avec Général Voyage.
                        Notre personnel serviable et notre site web détaillé vous accompagnent de la réservation jusqu’à l’arrivée.
                        Vous pouvez <a href="#">acheter votre billet en ligne</a>
                        ou même au dernier moment auprès du conducteur.
                    </p>

                    <p>
                        Nos bus garantissent <strong>une place assise avec espace pour vos jambes</strong>,
                        <strong>Wi-fi gratuit</strong>,
                        <strong>prises électriques</strong> et des snacks à petit prix !
                    </p>
                </div>
            </div>
        </section>

        <section class="carousel-section">
            <h2 class="section-title">
                Découvrez quelques destinations populaires
            </h2>

            <div class="carousel home-carousel"
                data-flickity='{ "cellAlign": "left", "contain": true, "wrapAround": false, "pageDots": true }'>

                <div class="carousel-cell">
                    <div class="destination-card">
                        <img src="https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b" alt="City" />
                        <div class="destination-card-body">
                            <div class="destination-card-title">New York City</div>
                            <p>The Big Apple, featuring stunning architecture and vibrant culture.</p>
                        </div>
                    </div>
                </div>

                <div class="carousel-cell">
                    <div class="destination-card">
                        <img src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad" alt="Tokyo" />
                        <div class="destination-card-body">
                            <div class="destination-card-title">Tokyo</div>
                            <p>A blend of modern technology and traditional culture.</p>
                        </div>
                    </div>
                </div>

                <div class="carousel-cell">
                    <div class="destination-card">
                        <img src="https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e" alt="Paris" />
                        <div class="destination-card-body">
                            <div class="destination-card-title">Paris</div>
                            <p>The City of Light, known for its romance and iconic landmarks.</p>
                        </div>
                    </div>
                </div>

                <div class="carousel-cell">
                    <div class="destination-card">
                        <img src="https://images.unsplash.com/photo-1480714378408-67cf0d13bc1b" alt="City" />
                        <div class="destination-card-body">
                            <div class="destination-card-title">New York City</div>
                            <p>The Big Apple, featuring stunning architecture and vibrant culture.</p>
                        </div>
                    </div>
                </div>

                <div class="carousel-cell">
                    <div class="destination-card">
                        <img src="https://images.unsplash.com/photo-1513635269975-59663e0ac1ad" alt="Tokyo" />
                        <div class="destination-card-body">
                            <div class="destination-card-title">Tokyo</div>
                            <p>A blend of modern technology and traditional culture.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="chat-floating">
        <i class="fa fa-comments" id="openChat"></i>
    </div>

    <div id="chatModal" class="chat-modal">
        <div class="chat-modal-header">
            <span>Assistance Mobiliis</span>
            <span id="closeChat" class="chat-close">
                <i class="fas fa-times"></i>
            </span>
        </div>

        <iframe src="Cookies/chat.php" width="100%" height="100%" frameborder="0"></iframe>
    </div>

    <div id="modalMessage"></div>

    <?php include 'includes/footer.php'; ?>

    <script src="Javascript/Accueil.js"></script>
</body>

</html>