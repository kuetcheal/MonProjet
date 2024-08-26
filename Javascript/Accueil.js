 // Fonction pour ouvrir la modal
 let modalId = document.getElementById("myModal");
 let modalId1 = document.getElementById("myModal1");
 let modalId2 = document.getElementById("myModal2");
 let modalId3 = document.getElementById("myModal3");
 


 function openModal(arg) {
     document.getElementById(arg).style.display = "block";
 }

 // Fonction pour fermer la modal
 function closeModal(arg) {
     document.getElementById(arg).style.display = "none";
 }




 // Vérifier s'il y a une image dans le localStorage
 let profilPic = document.getElementById("profil-pic");
 let profilInput = document.getElementById("input-file");
 const profilPics = document.querySelectorAll(".profil-pic-global");
 const profilOutils = document.getElementById("profil");


 // Vérifier s'il y a une image dans le localStorage
 const savedImage = localStorage.getItem("savedImage");
 if (savedImage) {
     profilPic.src = savedImage; // Affiche l'image dans la popup de settings
     profilPics.forEach(img => img.src = savedImage); // Affiche l'image dans les autres popups
     profilOutils.src = savedImage; // Affiche l'image dans la section "outils"
 }
 // Gérer le changement d'image
 profilInput.onchange = function() {
     const newImageUrl = URL.createObjectURL(profilInput.files[0]);
     profilPic.src = newImageUrl;


     // Mettre à jour toutes les images dans les popups
     profilPics.forEach(img => img.src = newImageUrl);

     // Mettre à jour l'image dans la section "outils"
     profilOutils.src = newImageUrl;

     // Stocker l'image dans le localStorage
     localStorage.setItem("savedImage", newImageUrl);
 }

 //fontion d 'affichage de aller-retour
 $(document).ready(function() {
     $("input[name='inlineRadioOptions']").change(function() {
         if ($(this).val() === 'option2') {
             $("div.form-groupe").show();
         } else {
             $("div.form-groupe").hide();
         }
     });
 });

 //gestion des animations de la page d'acceuil
 const images = [
     "https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg",
     "https://www.sustainable-bus.com/wp-content/uploads/2021/06/CrosswayCNG_Flixbus_inside-1688x1080.jpg",
     "https://content.presspage.com/uploads/2327/1920_flixbus-treyratcliff-paris-02.jpg?10000"
 ];

 let currentIndex = 0;
 const container = document.querySelector('.container');

 function changeBackground() {
     currentIndex = (currentIndex + 1) % images.length;
     container.style.backgroundImage =
         `linear-gradient(rgba(39, 39, 39, 0.6), rgba(0, 0, 0, 0.6)), url(${images[currentIndex]})`;
 }
 setInterval(changeBackground, 4000);

 // gestion du choix du trajet retour ou non
 document.getElementById('inlineRadio1').addEventListener('change', function() {
     document.getElementById('input4').disabled = true;
 });
 document.getElementById('inlineRadio2').addEventListener('change', function() {
     document.getElementById('input4').disabled = false;
 });


 // Code pour ouvrir le modal et charger le contenu
 document.getElementById('openModalButton').addEventListener('click', function() {
     $.ajax({
         url: './formulaire.php',
         success: function(response) {
             document.getElementById('modalContainer').innerHTML = response;
             var modal = document.querySelector('#modalContainer .modal');
             var closeButton = document.querySelector('.close-button');

             modal.style.display = "block";

             closeButton.onclick = function() {
                 modal.style.display = "none";
                 modal.parentNode.removeChild(modal);
             }

             window.onclick = function(event) {
                 if (event.target == modal) {
                     modal.style.display = "none";
                     modal.parentNode.removeChild(modal);
                 }
             }
         }
     });
 });



 // Code pour gérer la soumission du formulaire de réservation
 $(document).on('submit', '#reservationForm', function(e) {
     e.preventDefault();

     $.ajax({
         type: 'POST',
         url: './process_reservation.php', // Mettez à jour l'URL ici
         data: $(this).serialize(),
         dataType: 'json',
         success: function(response) {
             if (response.status === 'success') {
                 window.location.href = response.redirect;
             } else if (response.status === 'error') {
                 console.error(response.message ||
                     'Erreur lors de la vérification de la réservation.');
             }
         },
         error: function(xhr, status, error) {
             console.error("Erreur: ", xhr.responseText);
             alert('Une erreur est survenue.');
         }
     });
 });


 // Code pour ouvrir le modal d'envoi de message
 document.getElementById('openModalMessage').addEventListener('click', function() {
     $.ajax({
         url: './Contact/contact.php',
         success: function(response) {
             document.getElementById('modalMessage').innerHTML = response;
             var modal = document.querySelector('#modalMessage .modalitisation');
             var closeButton = document.querySelector('.close1');

             modal.style.display = "block";

             closeButton.onclick = function() {
                 modal.style.display = "none";
                 modal.parentNode.removeChild(modal);
             }

             window.onclick = function(event) {
                 if (event.target == modal) {
                     modal.style.display = "none";
                     modal.parentNode.removeChild(modal);
                 }
             }
         },
         error: function(xhr, status, error) {
             console.error("Error loading modal: ", status, error);
         }
     });
 });

 // Code pour gérer la soumission du formulaire de message
 $(document).on('submit', '#contactForm', function(e) {
     e.preventDefault();

     $.ajax({
         type: 'POST',
         url: './Contact/contact-ajax.php',
         data: $(this).serialize(),
         dataType: 'json',
         success: function(response) {
             console.log(response);
             if (response.status === 'success') {
                 window.location.href = response.redirect;
             } else if (response.status === 'error') {
                 console.error(response.message ||
                     'Erreur lors de la soumission du formulaire.');
                 alert(response.message || 'Erreur lors de la soumission du formulaire.');
             }
         },
         error: function(xhr, status, error) {
             console.error("Erreur: ", xhr.responseText);
             alert('Une erreur est survenue.');
         }
     });
 });


 // selectionne de la destination
 $(document).ready(function() {
     $('.select2').select2({
         placeholder: 'Sélectionnez une destination',
         allowClear: true
     });
 });