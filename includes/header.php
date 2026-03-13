<?php
if (isset($_POST['deconnect_account'])) {
    session_unset();
    session_destroy();
    header('Location: connexion.php');
    exit;
}
?>

<header class="gv-header">
    <div class="gv-header__inner">
        <a href="Accueil.php" class="gv-header__brand">
            <img src="pictures/logo-general.jpg" alt="Logo Général Voyage" class="gv-header__logo">
        </a>

        <nav class="gv-header__desktop-nav">
            <a href="Accueil.php" class="gv-header__link gv-header__link--active">Accueil</a>
            <a href="reservations.php" class="gv-header__link">Réservations</a>
            <a href="services.php" class="gv-header__link">Services</a>
            <a href="contact.php" class="gv-header__link">Nos contacts</a>
            <a href="inscription.php" class="gv-header__link">Inscription</a>
            <a href="connexion.php" class="gv-header__link">Connexion</a>
        </nav>

        <div class="gv-header__actions">
            <button type="button" class="gv-header__lang">FR</button>

            <button type="button" class="gv-header__profile-btn" onclick="gvOpenModal('gvAccountModal')">
                <img id="profil" src="pictures/OIP.jpg" alt="Profil" class="gv-header__profile-img">
            </button>

            <button id="gvBurgerButton" type="button" class="gv-header__burger" aria-label="Ouvrir le menu">
                <i class="fa fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<div id="gvMobileBackdrop" class="gv-mobile-backdrop"></div>

<aside id="gvMobileDrawer" class="gv-mobile-drawer" aria-hidden="true">
    <div class="gv-mobile-drawer__header">
        <img src="pictures/logo-general.jpg" alt="Logo Général Voyage" class="gv-mobile-drawer__logo">

        <button id="gvCloseDrawer" type="button" class="gv-mobile-drawer__close" aria-label="Fermer le menu">
            <i class="fa fa-times"></i>
        </button>
    </div>

    <nav class="gv-mobile-drawer__nav">
        <a href="Accueil.php" class="gv-mobile-drawer__link gv-mobile-drawer__link--active">Accueil</a>
        <a href="reservations.php" class="gv-mobile-drawer__link">Réservations</a>
        <a href="services.php" class="gv-mobile-drawer__link">Services</a>
        <a href="contact.php" class="gv-mobile-drawer__link">Nos contacts</a>
        <a href="inscription.php" class="gv-mobile-drawer__link">Inscription</a>
        <a href="connexion.php" class="gv-mobile-drawer__link">Connexion</a>
    </nav>
</aside>

<div id="gvAccountModal" class="gv-account-modal">
    <div class="gv-account-modal__panel">
        <button class="gv-account-modal__close" onclick="gvCloseModal('gvAccountModal')">
            <i class="fa fa-times-circle"></i>
        </button>

        <h2 class="gv-account-modal__title">Mon compte utilisateur</h2>
        <hr class="gv-account-modal__divider">

        <div class="gv-account-modal__profile">
            <img id="profil-pic" src="pictures/OIP.jpg" class="gv-account-modal__profile-img profil-pic-global" alt="Profil utilisateur">
            <input type="file" id="input-file" accept="image/png, image/jpeg, image/jpg" hidden>
            <label for="input-file" class="gv-account-modal__upload-btn">
                Télécharger une image
            </label>
        </div>

        <hr class="gv-account-modal__divider">

        <div class="gv-account-modal__user">
            <p class="gv-account-modal__name">Alex KUETCHE</p>
            <p class="gv-account-modal__email">alexkuetche@gmail.com</p>
        </div>

        <hr class="gv-account-modal__divider">

        <div class="gv-account-modal__actions">
            <button class="gv-account-modal__action gv-account-modal__action--danger" onclick="gvOpenModal('gvDeleteAccountModal')">
                Supprimer
            </button>
            <button class="gv-account-modal__action gv-account-modal__action--neutral" onclick="gvOpenModal('gvLogoutModal')">
                Se déconnecter
            </button>
            <button class="gv-account-modal__action gv-account-modal__action--primary">
                Modifier
            </button>
        </div>
    </div>
</div>

<div id="gvDeleteAccountModal" class="gv-account-modal">
    <div class="gv-account-modal__panel">
        <button class="gv-account-modal__back" onclick="gvCloseModal('gvDeleteAccountModal')">
            <i class="fa fa-arrow-left"></i>
        </button>

        <h2 class="gv-account-modal__title">Suppression du compte</h2>
        <hr class="gv-account-modal__divider">

        <div class="gv-account-modal__profile">
            <img src="pictures/OIP.jpg" class="gv-account-modal__profile-img profil-pic-global" alt="Profil utilisateur">
        </div>

        <hr class="gv-account-modal__divider">

        <div class="gv-account-modal__user">
            <p class="gv-account-modal__name">Alex KUETCHE</p>
            <p class="gv-account-modal__email">alexkuetche@gmail.com</p>
        </div>

        <hr class="gv-account-modal__divider">

        <p class="gv-account-modal__message">
            Attention, cette action effacera définitivement votre compte de l'application.
            Êtes-vous sûr de vouloir supprimer votre compte ?
        </p>

        <div class="gv-account-modal__actions">
            <button class="gv-account-modal__action gv-account-modal__action--danger">
                Confirmer la suppression
            </button>
        </div>
    </div>
</div>

<div id="gvLogoutModal" class="gv-account-modal">
    <div class="gv-account-modal__panel">
        <button class="gv-account-modal__back" onclick="gvCloseModal('gvLogoutModal')">
            <i class="fa fa-arrow-left"></i>
        </button>

        <h2 class="gv-account-modal__title">Déconnexion</h2>
        <hr class="gv-account-modal__divider">

        <div class="gv-account-modal__profile">
            <img src="pictures/OIP.jpg" class="gv-account-modal__profile-img profil-pic-global" alt="Profil utilisateur">
        </div>

        <hr class="gv-account-modal__divider">

        <div class="gv-account-modal__user">
            <p class="gv-account-modal__name">Alex KUETCHE</p>
            <p class="gv-account-modal__email">alexkuetche@gmail.com</p>
        </div>

        <hr class="gv-account-modal__divider">

        <p class="gv-account-modal__message">
            Attention, cette action vous déconnectera du site.
            Êtes-vous sûr de vouloir vous déconnecter ?
        </p>

        <div class="gv-account-modal__actions">
            <form method="post" class="gv-account-modal__form">
                <button type="submit" name="deconnect_account" class="gv-account-modal__action gv-account-modal__action--neutral">
                    Se déconnecter
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    .gv-header {
        position: sticky;
        top: 0;
        z-index: 1200;
        width: 100%;
        background: linear-gradient(135deg, #018b01 0%, #016f01 100%);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.14);
    }

    .gv-header__inner {
        max-width: 1400px;
        margin: 0 auto;
        min-height: 108px;
        padding: 14px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .gv-header__brand {
        display: flex;
        align-items: center;
        flex-shrink: 0;
        text-decoration: none;
    }

    .gv-header__logo {
        width: 125px;
        height: 90px;
        object-fit: cover;
        border-radius: 10px;
        background: #fff;
        padding: 4px;
    }

    .gv-header__desktop-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex: 1;
        max-width: 760px;
        background: rgba(255, 255, 255, 0.96);
        border-radius: 999px;
        padding: 10px 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .gv-header__link {
        color: #0f6c39;
        text-decoration: none;
        font-size: 1rem;
        font-weight: 700;
        padding: 10px 16px;
        border-radius: 999px;
        transition: all 0.25s ease;
        white-space: nowrap;
    }

    .gv-header__link:hover,
    .gv-header__link--active {
        background: #e8f7eb;
        color: #018b01;
    }

    .gv-header__actions {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
    }

    .gv-header__lang {
        border: none;
        background: transparent;
        color: #fff;
        font-size: 1.15rem;
        font-weight: 700;
        cursor: pointer;
    }

    .gv-header__profile-btn {
        border: none;
        background: transparent;
        padding: 0;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .gv-header__profile-img {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid rgba(255, 255, 255, 0.45);
    }

    .gv-header__burger {
        display: none;
        border: none;
        background: transparent;
        color: #fff;
        font-size: 2rem;
        cursor: pointer;
    }

    .gv-mobile-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(3, 10, 24, 0.55);
        backdrop-filter: blur(7px);
        -webkit-backdrop-filter: blur(7px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: all 0.3s ease;
        z-index: 1300;
    }

    .gv-mobile-backdrop.is-active {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    .gv-mobile-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: min(86vw, 380px);
        height: 100vh;
        background: linear-gradient(180deg, #071120 0%, #0b1425 100%);
        box-shadow: -16px 0 40px rgba(0, 0, 0, 0.28);
        transform: translateX(100%);
        transition: transform 0.35s ease;
        z-index: 1350;
        display: flex;
        flex-direction: column;
        padding: 18px 18px 28px;
    }

    .gv-mobile-drawer.is-open {
        transform: translateX(0);
    }

    .gv-mobile-drawer__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .gv-mobile-drawer__logo {
        width: 88px;
        height: 68px;
        object-fit: cover;
        border-radius: 10px;
        background: #fff;
        padding: 4px;
    }

    .gv-mobile-drawer__close {
        border: none;
        background: transparent;
        color: #fff;
        font-size: 2rem;
        cursor: pointer;
    }

    .gv-mobile-drawer__nav {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 12px;
    }

    .gv-mobile-drawer__link {
        color: #fff;
        text-decoration: none;
        font-size: 1.35rem;
        font-weight: 700;
        padding: 18px 22px;
        border-radius: 18px;
        transition: all 0.25s ease;
    }

    .gv-mobile-drawer__link:hover,
    .gv-mobile-drawer__link--active {
        background: rgba(138, 53, 76, 0.22);
        color: #ff546e;
    }

    .gv-account-modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.45);
        display: none;
        justify-content: flex-end;
        z-index: 1400;
    }

    .gv-account-modal.is-open {
        display: flex;
    }

    .gv-account-modal__panel {
        position: relative;
        width: min(100%, 410px);
        height: 100vh;
        background: #fff;
        padding: 28px 22px;
        overflow-y: auto;
        box-shadow: -8px 0 24px rgba(0, 0, 0, 0.18);
    }

    .gv-account-modal__close,
    .gv-account-modal__back {
        position: absolute;
        top: 18px;
        border: none;
        background: transparent;
        color: #5f6368;
        font-size: 1.4rem;
        cursor: pointer;
    }

    .gv-account-modal__close {
        right: 18px;
    }

    .gv-account-modal__back {
        left: 18px;
    }

    .gv-account-modal__title {
        margin: 10px 0 0;
        text-align: center;
        color: #0f8d43;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .gv-account-modal__divider {
        border: none;
        border-top: 1px solid #c7e8cf;
        margin: 18px 0;
    }

    .gv-account-modal__profile {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 14px;
    }

    .gv-account-modal__profile-img {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #0f8d43;
    }

    .gv-account-modal__upload-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        border-radius: 10px;
        background: #0f8d43;
        color: #fff;
        font-size: 0.95rem;
        font-weight: 700;
        cursor: pointer;
    }

    .gv-account-modal__user {
        text-align: center;
    }

    .gv-account-modal__name {
        margin: 0 0 6px;
        font-size: 1.1rem;
        font-weight: 800;
        color: #1a1a1a;
    }

    .gv-account-modal__email {
        margin: 0;
        color: #0f8d43;
        font-size: 0.96rem;
    }

    .gv-account-modal__message {
        color: #333;
        font-size: 1rem;
        line-height: 1.6;
        font-weight: 600;
        margin: 0 0 16px;
    }

    .gv-account-modal__actions,
    .gv-account-modal__form {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .gv-account-modal__action {
        width: 100%;
        border: none;
        border-radius: 12px;
        padding: 13px 16px;
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
    }

    .gv-account-modal__action--primary {
        background: #ef4444;
    }

    .gv-account-modal__action--danger {
        background: #16a34a;
    }

    .gv-account-modal__action--neutral {
        background: #6b7280;
    }

    @media (max-width: 1100px) {
        .gv-header__desktop-nav {
            display: none;
        }

        .gv-header__burger {
            display: inline-flex;
        }

        .gv-header__inner {
            min-height: 94px;
        }

        .gv-header__logo {
            width: 110px;
            height: 78px;
        }
    }

    @media (max-width: 640px) {
        .gv-header__inner {
            padding: 12px 16px;
            min-height: 88px;
        }

        .gv-header__logo {
            width: 92px;
            height: 70px;
        }

        .gv-header__actions {
            gap: 10px;
        }

        .gv-header__lang {
            font-size: 1rem;
        }

        .gv-header__profile-img {
            width: 42px;
            height: 42px;
        }

        .gv-mobile-drawer {
            width: min(92vw, 360px);
            padding: 16px;
        }

        .gv-mobile-drawer__link {
            font-size: 1.15rem;
            padding: 16px 18px;
        }

        .gv-account-modal__panel {
            width: 100%;
        }
    }
</style>

<script>
    function gvOpenModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.add('is-open');
        }
    }

    function gvCloseModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            modal.classList.remove('is-open');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const burgerButton = document.getElementById('gvBurgerButton');
        const closeDrawerButton = document.getElementById('gvCloseDrawer');
        const mobileDrawer = document.getElementById('gvMobileDrawer');
        const mobileBackdrop = document.getElementById('gvMobileBackdrop');

        function openDrawer() {
            mobileDrawer.classList.add('is-open');
            mobileBackdrop.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            mobileDrawer.classList.remove('is-open');
            mobileBackdrop.classList.remove('is-active');
            document.body.style.overflow = '';
        }

        if (burgerButton) {
            burgerButton.addEventListener('click', openDrawer);
        }

        if (closeDrawerButton) {
            closeDrawerButton.addEventListener('click', closeDrawer);
        }

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', closeDrawer);
        }

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDrawer();
                document.querySelectorAll('.gv-account-modal.is-open').forEach(function (modal) {
                    modal.classList.remove('is-open');
                });
            }
        });

        document.querySelectorAll('.gv-account-modal').forEach(function (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    modal.classList.remove('is-open');
                }
            });
        });

        const profilPic = document.getElementById('profil-pic');
        const profilInput = document.getElementById('input-file');
        const profilOutils = document.getElementById('profil');
        const profilPics = document.querySelectorAll('.profil-pic-global');

        const savedImage = localStorage.getItem('savedImage');
        if (savedImage) {
            if (profilPic) profilPic.src = savedImage;
            if (profilOutils) profilOutils.src = savedImage;
            profilPics.forEach(img => img.src = savedImage);
        }

        if (profilInput) {
            profilInput.addEventListener('change', function () {
                const file = profilInput.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = function (e) {
                    if (profilPic) profilPic.src = e.target.result;
                    if (profilOutils) profilOutils.src = e.target.result;
                    profilPics.forEach(img => img.src = e.target.result);
                    localStorage.setItem('savedImage', e.target.result);
                };
                reader.readAsDataURL(file);
            });
        }
    });
</script>