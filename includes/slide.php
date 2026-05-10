<div class="hero-content hero-slide-wrapper" id="homeHeroSlider">
    <div class="hero-slide-text">
        <span class="hero-slide-kicker js-hero-kicker">
            Offre spéciale
        </span>

        <h1 class="hero-title js-hero-title">
            Profite jusqu'à -20% sur les billets de reservations !
        </h1>

        <p class="hero-subtitle js-hero-subtitle">
            <span class="js-hero-text">REMISES EXCLUSIVES POUR LES MEMBRES !</span>
            <a href="connexion.php" class="hero-link js-hero-link">
                CONNECTEZ-VOUS / INSCRIVEZ-VOUS ICI
            </a>
        </p>
    </div>

    <div class="hero-slide-progress" aria-label="Progression des slides">
        <button type="button" class="hero-slide-progress-item is-active" data-slide="0">
            <span></span>
        </button>

        <button type="button" class="hero-slide-progress-item" data-slide="1">
            <span></span>
        </button>

        <button type="button" class="hero-slide-progress-item" data-slide="2">
            <span></span>
        </button>
    </div>
</div>

<script type="module">
    import { animate } from "https://cdn.jsdelivr.net/npm/@motionone/dom/+esm";

    document.addEventListener('DOMContentLoaded', function () {
        const slider = document.getElementById('homeHeroSlider');
        if (!slider) return;

        const textBox = slider.querySelector('.hero-slide-text');
        const kicker = slider.querySelector('.js-hero-kicker');
        const title = slider.querySelector('.js-hero-title');
        const text = slider.querySelector('.js-hero-text');
        const link = slider.querySelector('.js-hero-link');
        const progressItems = slider.querySelectorAll('.hero-slide-progress-item');

        const slideDuration = 6000;

        let currentSlide = 0;
        let timer = null;
        let progressAnimation = null;
        let isAnimating = false;

        const slides = [
            {
                kicker: "Offre spéciale",
                title: "Profite jusqu'à -20% sur les billets de reservations !",
                text: "REMISES EXCLUSIVES POUR LES MEMBRES !",
                linkText: "CONNECTEZ-VOUS / INSCRIVEZ-VOUS ICI",
                href: "connexion.php"
            },
            {
                kicker: "Covoiturage",
                title: "Devenez chauffeur partenaire avec EasyTravel",
                text: "PUBLIEZ VOS TRAJETS ET TRANSPORTEZ DES PASSAGERS !",
                linkText: "INSCRIVEZ-VOUS COMME CHAUFFEUR",
                href: "inscription.php"
            },
            {
                kicker: "Réservation rapide",
                title: "Réservez votre billet de bus en quelques clics !",
                text: "CHOISISSEZ VOTRE TRAJET, VOTRE DATE ET VOYAGEZ SEREINEMENT !",
                linkText: "RÉSERVER MAINTENANT",
                href: "#reservation-voyage"
            }
        ];

        function resetProgress(index) {
            progressItems.forEach((item, itemIndex) => {
                const fill = item.querySelector('span');

                item.classList.remove('is-active', 'is-done');
                fill.style.transform = 'scaleX(0)';

                if (itemIndex < index) {
                    item.classList.add('is-done');
                    fill.style.transform = 'scaleX(1)';
                }
            });
        }

        function startProgress(index) {
            if (progressAnimation) {
                progressAnimation.cancel();
            }

            resetProgress(index);

            const activeItem = progressItems[index];
            const activeFill = activeItem.querySelector('span');

            activeItem.classList.add('is-active');

            progressAnimation = animate(
                activeFill,
                {
                    transform: ['scaleX(0)', 'scaleX(1)']
                },
                {
                    duration: slideDuration / 1000,
                    easing: 'linear'
                }
            );
        }

        async function showSlide(index) {
            if (isAnimating) return;

            isAnimating = true;
            currentSlide = index;

            const slide = slides[index];

            await animate(
                textBox,
                {
                    opacity: [1, 0],
                    y: [0, -14]
                },
                {
                    duration: 0.45,
                    easing: 'ease-in-out'
                }
            ).finished;

            kicker.textContent = slide.kicker;
            title.textContent = slide.title;
            text.textContent = slide.text;
            link.textContent = slide.linkText;
            link.href = slide.href;

            animate(
                textBox,
                {
                    opacity: [0, 1],
                    y: [18, 0]
                },
                {
                    duration: 0.75,
                    easing: 'ease-out'
                }
            );

            startProgress(index);
            restartTimer();

            setTimeout(() => {
                isAnimating = false;
            }, 800);
        }

        function nextSlide() {
            const nextIndex = (currentSlide + 1) % slides.length;
            showSlide(nextIndex);
        }

        function restartTimer() {
            clearTimeout(timer);
            timer = setTimeout(nextSlide, slideDuration);
        }

        progressItems.forEach((item, index) => {
            item.addEventListener('click', function () {
                showSlide(index);
            });
        });

        startProgress(0);
        restartTimer();
    });
</script>