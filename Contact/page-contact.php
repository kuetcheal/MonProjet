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
</head>

<body class="bg-light">

    <header class="bg-success text-white py-4 text-center">
        <h1 class="mb-0">Contact EasyTravel</h1>
        <p class="mb-0">Douala - Cameroun</p>
    </header>


    <section class="py-5">
  <div class="container">
    <div class="row align-items-stretch">
      <!-- Colonne gauche (infos + image) -->
      <div class="col-md-6 d-flex flex-column justify-content-between p-3">
        <!-- Infos sur une seule ligne -->
        <div class="d-flex justify-content-around text-center flex-wrap mb-3 w-100">
          <div class="mx-2">
            <i class="fas fa-phone-alt text-success"></i>
            <span class="ml-2">+237 6 99 00 00 00</span>
          </div>
          <div class="mx-2">
            <i class="fas fa-envelope text-success"></i>
            <span class="ml-2">contact@easytravel.com</span>
          </div>
        </div>

        <!-- Image plus haute -->
        <div class="flex-grow-1 d-flex align-items-center justify-content-center">
          <img src="https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg"
               alt="Flixbus EasyTravel"
               class="img-fluid w-100 rounded" style="max-height: 450px; object-fit: cover;">
        </div>
      </div>

      <!-- Colonne droite (formulaire) -->
      <div class="col-md-6 d-flex">
        <form id="contactForm" class="bg-white p-4 rounded shadow-sm w-100 d-flex flex-column justify-content-between">
          <div>
            <!-- Nom & Email -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="name">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Nom">
              </div>
              <div class="form-group col-md-6">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="mail" required placeholder="Email">
              </div>
            </div>

            <!-- Adresse & Téléphone -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="adresse">Adresse <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="adresse" name="adresse" required placeholder="Adresse">
              </div>
              <div class="form-group col-md-6">
                <label for="phone">Téléphone <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="phone" name="phone" required placeholder="Téléphone">
              </div>
            </div>

            <div class="form-group">
              <label for="messager">Message <span class="text-danger">*</span></label>
              <textarea class="form-control" id="messager" name="messager" rows="5" required placeholder="Tapez votre message ici..."></textarea>
            </div>
            <div class="form-group">
              <label for="input2">Agence :</label>
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
          </div>
          <button type="submit" class="btn btn-success btn-block mt-3">Envoyer le message</button>
        </form>
      </div>
    </div>
  </div>
</section>





    <section class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 d-flex align-items-center">
                    <i class="fas fa-paper-plane fa-2x mr-3"></i>
                    <span class="h5 font-weight-bold mb-0">Inscription à la Newsletter</span>
                </div>
                <div class="col-md-6">
                    <form class="form-inline justify-content-center">
                        <input type="email" class="form-control mr-2" placeholder="Votre adresse email">
                        <button type="submit" class="btn btn-light">GO</button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <a href="#" class="text-white mx-2"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </section>

    <footer class="bg-secondary text-white text-center py-3">
        <p class="mb-0">&copy; 2024 EasyTravel. Tous droits réservés.</p>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#contactForm').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'page-contact-ajax.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        const jsonResponse = JSON.parse(response);
                        const $modal = $('#responseModal');
                        const $message = $('#modalMessage');

                        if (jsonResponse.status === 'success') {
                            $modal.find('.modal-header').removeClass('bg-danger').addClass('bg-success');
                            $modal.find('.modal-title').text('Succès');
                            $message.html("✅ Message envoyé avec succès !");
                            $('#contactForm')[0].reset();
                        } else {
                            $modal.find('.modal-header').removeClass('bg-success').addClass('bg-danger');
                            $modal.find('.modal-title').text('Erreur');
                            $message.html(`❌ ${jsonResponse.message}`);
                        }

                        $modal.modal('show');

                        // Fermer automatiquement après 3 secondes
                        setTimeout(() => {
                            $modal.modal('hide');
                        }, 3000);
                    },
                    error: function() {
                        const $modal = $('#responseModal');
                        $modal.find('.modal-header').removeClass('bg-success').addClass('bg-danger');
                        $modal.find('.modal-title').text('Erreur');
                        $('#modalMessage').html("❌ Une erreur est survenue. Veuillez réessayer.");
                        $modal.modal('show');

                        setTimeout(() => {
                            $modal.modal('hide');
                        }, 3000);
                    }
                });
            });
        });
    </script>
</body>

</html>