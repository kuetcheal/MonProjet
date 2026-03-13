document.addEventListener("DOMContentLoaded", function () {
    // =========================
    // Fonctions globales modales
    // =========================
    window.openModal = function (arg) {
        const el = document.getElementById(arg);
        if (el) {
            el.style.display = "block";
        }
    };

    window.closeModal = function (arg) {
        const el = document.getElementById(arg);
        if (el) {
            el.style.display = "none";
        }
    };

    // =========================
    // Gestion image profil
    // =========================
    const profilPic = document.getElementById("profil-pic");
    const profilInput = document.getElementById("input-file");
    const profilPics = document.querySelectorAll(".profil-pic-global");
    const profilOutils = document.getElementById("profil");

    const savedImage = localStorage.getItem("savedImage");

    if (savedImage) {
        if (profilPic) profilPic.src = savedImage;
        profilPics.forEach((img) => (img.src = savedImage));
        if (profilOutils) profilOutils.src = savedImage;
    }

    if (profilInput) {
        profilInput.onchange = function () {
            if (!profilInput.files || !profilInput.files[0]) return;

            const newImageUrl = URL.createObjectURL(profilInput.files[0]);

            if (profilPic) profilPic.src = newImageUrl;
            profilPics.forEach((img) => (img.src = newImageUrl));
            if (profilOutils) profilOutils.src = newImageUrl;

            localStorage.setItem("savedImage", newImageUrl);
        };
    }

    // =========================
    // Aller / Aller-Retour
    // =========================
    const radioAller = document.getElementById("inlineRadio1");
    const radioAllerRetour = document.getElementById("inlineRadio2");
    const dateRetour = document.getElementById("input4");

    function toggleDateRetour() {
        if (!radioAller || !radioAllerRetour || !dateRetour) return;

        if (radioAllerRetour.checked) {
            dateRetour.disabled = false;
            dateRetour.required = true;
        } else {
            dateRetour.disabled = true;
            dateRetour.required = false;
            dateRetour.value = "";
        }
    }

    if (radioAller) {
        radioAller.addEventListener("change", toggleDateRetour);
    }

    if (radioAllerRetour) {
        radioAllerRetour.addEventListener("change", toggleDateRetour);
    }

    toggleDateRetour();

    // =========================
    // Animation fond hero
    // =========================
    const images = [
        "https://www.autorite-transports.fr/wp-content/uploads/2016/03/autocar-Flixbus.jpg",
        "https://www.sustainable-bus.com/wp-content/uploads/2021/06/CrosswayCNG_Flixbus_inside-1688x1080.jpg",
        "https://content.presspage.com/uploads/2327/1920_flixbus-treyratcliff-paris-02.jpg?10000"
    ];

    let currentIndex = 0;
    const heroSection = document.querySelector(".hero-section");

    function changeBackground() {
        if (!heroSection) return;

        currentIndex = (currentIndex + 1) % images.length;
        heroSection.style.backgroundImage = `url('${images[currentIndex]}')`;
    }

    if (heroSection) {
        setInterval(changeBackground, 4000);
    }

    // =========================
    // Modale réservation
    // =========================
    const openModalButton = document.getElementById("openModalButton");
    const modalContainer = document.getElementById("modalContainer");

    if (openModalButton && modalContainer) {
        openModalButton.addEventListener("click", function () {
            $.ajax({
                url: "./formulaire.php",
                success: function (response) {
                    modalContainer.innerHTML = response;

                    const modal =
                        document.querySelector("#modalContainer .modalisation") ||
                        document.querySelector("#modalContainer .modal");

                    const closeButton =
                        document.querySelector("#modalContainer .close-button") ||
                        document.querySelector("#modalContainer .close");

                    if (modal) {
                        modal.style.display = "flex";
                    }

                    if (closeButton && modal) {
                        closeButton.addEventListener("click", function () {
                            modal.style.display = "none";
                            modalContainer.innerHTML = "";
                        });
                    }

                    if (modal) {
                        window.addEventListener("click", function handleOutsideClick(event) {
                            if (event.target === modal) {
                                modal.style.display = "none";
                                modalContainer.innerHTML = "";
                                window.removeEventListener("click", handleOutsideClick);
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Erreur chargement modale réservation :", status, error);
                }
            });
        });
    }

    // =========================
    // Soumission formulaire réservation
    // =========================
    $(document).on("submit", "#reservationForm", function (e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "./process_reservation.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    window.location.href = response.redirect;
                } else if (response.status === "error") {
                    console.error(
                        response.message || "Erreur lors de la vérification de la réservation."
                    );
                    alert(response.message || "Erreur lors de la vérification de la réservation.");
                }
            },
            error: function (xhr) {
                console.error("Erreur :", xhr.responseText);
                alert("Une erreur est survenue.");
            }
        });
    });

    // =========================
    // Modale message / contact
    // =========================
    const openModalMessage = document.getElementById("openModalMessage");
    const modalMessage = document.getElementById("modalMessage");

    if (openModalMessage && modalMessage) {
        openModalMessage.addEventListener("click", function () {
            $.ajax({
                url: "./Contact/contact.php",
                success: function (response) {
                    modalMessage.innerHTML = response;

                    const modal = document.querySelector("#modalMessage .modalitisation");
                    const closeButton = document.querySelector("#modalMessage .close1");

                    if (modal) {
                        modal.style.display = "block";
                    }

                    if (closeButton && modal) {
                        closeButton.addEventListener("click", function () {
                            modal.style.display = "none";
                            modalMessage.innerHTML = "";
                        });
                    }

                    if (modal) {
                        window.addEventListener("click", function handleOutsideClick(event) {
                            if (event.target === modal) {
                                modal.style.display = "none";
                                modalMessage.innerHTML = "";
                                window.removeEventListener("click", handleOutsideClick);
                            }
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Erreur chargement modal message :", status, error);
                }
            });
        });
    }

    // =========================
    // Soumission formulaire contact
    // =========================
    $(document).on("submit", "#contactForm", function (e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: "./Contact/contact-ajax.php",
            data: $(this).serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    window.location.href = response.redirect;
                } else if (response.status === "error") {
                    console.error(
                        response.message || "Erreur lors de la soumission du formulaire."
                    );
                    alert(response.message || "Erreur lors de la soumission du formulaire.");
                }
            },
            error: function (xhr) {
                console.error("Erreur :", xhr.responseText);
                alert("Une erreur est survenue.");
            }
        });
    });

    // =========================
    // Chat flottant
    // =========================
    const openChat = document.getElementById("openChat");
    const closeChat = document.getElementById("closeChat");
    const chatModal = document.getElementById("chatModal");

    if (openChat && chatModal) {
        openChat.addEventListener("click", function () {
            chatModal.style.display = "block";
        });
    }

    if (closeChat && chatModal) {
        closeChat.addEventListener("click", function () {
            chatModal.style.display = "none";
        });
    }

    // =========================
    // Select2
    // =========================
    if ($(".select2").length) {
        $(".select2").select2({
            placeholder: "Sélectionnez une destination",
            allowClear: true
        });
    }
});