<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact EasyTravel</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .contact-header {
        background-color: green;
        color: white;
        padding: 40px 0;
        text-align: center;
        height: 120px;
    }

    .contact-header h1 {
        margin: 0;
    }

    .contact-section {
        padding: 60px 0;
    }

    .contact-info {
        text-align: center;
        margin-bottom: 40px;
    }

    .contact-info i {
        font-size: 2em;
        color: #28a745;
        margin-bottom: 10px;
    }

    .contact-info h4 {
        margin-top: 15px;
    }

    .contact-form {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .contact-form .form-group {
        margin-bottom: 20px;
    }

    .contact-form .form-control,
    .contact-form select {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        border-radius: 5px;
        border: 1px solid #ced4da;
    }

    .contact-form .row .form-group {
        margin-bottom: 0;
    }

    .contact-form button {
        background-color: green;
        border: none;
        width: 100%;
        padding: 15px;
        font-size: 18px;
        border-radius: 5px;
        color: white;
        cursor: pointer;
    }

    footer {
        background-color: #6c757d;
        color: white;
        padding: 20px 0;
        text-align: center;
    }

    span {
        color: red;
    }

    /* Newsletter Section */
    .newsletter-section {
        background-color: #8b4a47;
        color: white;
        padding: 20px 0;
    }

    .newsletter-section .newsletter-icon {
        font-size: 2.5em;
        color: #f8f9fa;
        margin-right: 10px;
    }

    .newsletter-section .newsletter-title {
        font-weight: bold;
    }

    .newsletter-section .newsletter-form {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 10px;
    }

    .newsletter-section .newsletter-form input[type="email"] {
        width: 300px;
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        margin-right: 10px;
    }

    .newsletter-section .newsletter-form button {
        background-color: #6c757d;
        color: white;
        border: none;
        padding: 7px 20px;
        border-radius: 3px;
    }

    /* Social Icons */
    .social-icons {
        display: flex;
        justify-content: center;
        margin-top: 10px;
    }

    .social-icons a {
        color: #f8f9fa;
        margin: 0 10px;
        font-size: 1.5em;
    }

    .social-icons a:hover {
        color: #28a745;
    }
    </style>
</head>

<body>

    <div class="contact-header">
        <h1>Contact EasyTravel</h1>
        <p>Douala - Cameroun</p>
    </div>

    <section class="contact-section">
        <div class="container">
            <form id="contactForm">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label for="name">Nom <span>*</span></label>
                        <input type="text" class="form-control" id="name" placeholder="Nom" name="name" required>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="email">Email <span>*</span></label>
                        <input type="email" class="form-control" id="email" placeholder="Email" name="mail" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Adresse <span>*</span></label>
                    <input type="text" class="form-control" id="address" placeholder="Adresse" name="adresse" required>
                </div>
                <div class="form-group">
                    <label for="address">Telephone <span>*</span></label>
                    <input type="text" class="form-control" id="address" placeholder="Adresse" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="message">Message <span>*</span></label>
                    <textarea class="form-control" id="message" rows="5" placeholder="Tapez votre message"
                        name="messager" required></textarea>
                </div>
                <div class="form-group">
                    <label for="input2"><i class="bi bi-geo-alt custom-icon"></i> Agence :</label>
                    <select id="input2" name="ville" class="form-control">
                        <?php
                        $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
                        $query = 'select * from destination order by Nom_ville ASC';
                        $response = $bdd->query($query);
                        while ($donnee = $response->fetch()) {
                            $destination = $donnee['Nom_ville'];
                            echo '<option value="' . htmlspecialchars($destination) . '">' . htmlspecialchars($destination) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Envoyer le message</button>
            </form>
            <div id="formResponse" class="mt-3"></div> <!-- Section pour afficher les réponses -->
        </div>
    </section>

    <!-- Newsletter and Social Icons Section -->
    <section class="newsletter-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 d-flex align-items-center">
                    <i class="fas fa-paper-plane newsletter-icon"></i>
                    <span class="newsletter-title">Inscription à la Newsletter</span>
                </div>
                <div class="col-md-6">
                    <form class="newsletter-form">
                        <input type="email" placeholder="Votre adresse email">
                        <button type="submit">GO</button>
                    </form>
                </div>
            </div>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </section>
    <footer>
        <p>&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.9/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#contactForm').on('submit', function(e) {
            e.preventDefault(); // Empêche le rechargement de la page

            $.ajax({
                type: 'POST',
                url: 'page-contact-ajax.php',
                data: $(this).serialize(),
                success: function(response) {
                    const jsonResponse = JSON.parse(response);

                    if (jsonResponse.status === 'success') {
                        $('#formResponse').html(
                            '<div class="alert alert-success">Message envoyé avec succès ! Nous allons traiter votre requête au plus vite et vous donnez unze reponse </div>'
                        );
                        $('#contactForm')[0].reset(); // Réinitialise le formulaire
                    } else {
                        $('#formResponse').html('<div class="alert alert-danger">' +
                            jsonResponse.message + '</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#formResponse').html(
                        '<div class="alert alert-danger">Une erreur est survenue. Veuillez réessayer.</div>'
                    );
                }
            });
        });
    });
    </script>
</body>

</html>